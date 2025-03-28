<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\GenerateStringUnique;

use App\Http\Traits\MailPaymentTrait;
use App\Http\Traits\FormRegistrationTrait;

use App\Models\Link;
use App\Models\Member;
use App\Models\MailPayment;
use App\Models\Invoice;
use App\Models\User;
use App\Models\LocationCity;
use Illuminate\Support\Facades\Cache;

use App\Http\Requests\LinkRequest;

use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class LinkController extends Controller
{
    use MailPaymentTrait, FormRegistrationTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.links.view', ['date' => date("m-d-Y")]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createPay()
    {
        $methodManual = Invoice::PAYMENT_TYPE != 'multipayment';
        $methodPay = Link::METHOD;
        return view('admin.pages.links.add_pay', compact('methodManual', 'methodPay'));
    }

    public function createFree()
    {
        return view('admin.pages.links.add_free');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LinkRequest $request)
    {
        try {
            $validated = $request->validated();
            $link = new Link;
            $link->link_path = GenerateStringUnique::make('Link', 'link_path')->getToken(Link::TOKEN_LENGTH);
            $link->title = ucwords($request->title);
            if ($request->filepath != null) {
                $link->banner = $request->filepath;
            }
            $link->description = $request->desc;
            $link->registration_info = $request->registration_info;
            if ($request->member_limit > 0) {
                $link->has_member_limit = true;
                $link->member_limit = $request->member_limit;
            }
            $link->active_from = date("Y-m-d", strtotime($request->open_date));
            $link->active_until = date("Y-m-d", strtotime($request->close_date));
            if (isset($validated['event_date'])) {
                $link->event_date = date("Y-m-d", strtotime($validated['event_date']));
            }
            $link->created_by = auth()->id();
            if ($request->event_type == 'pay') {
                $link->price = $validated['price'];
                $link->link_type = Link::LINK_TYPE[0];
                if (isset($validated['bank'])) {
                    $link->bank_information = [
                        'name' => $validated['bank']['name'],
                        'account_number' => $validated['bank']['account_number'],
                        'account_name' => $validated['bank']['account_name'],
                    ];
                }

                $link->method_pay = $request->is_multipayment == null ? 'bank_transfer' : 'multipayment';
                
                $link->is_multiple_registrant_allowed = isset($validated['is_multiple_registrant_allowed']) ? true : false;
                if (isset($validated['is_multiple_registrant_allowed'])) {
                    $link->sub_member_limit = $validated['sub_member_limit'];
                }
            }
            if ($request->event_type == 'free') {
                $link->link_type = Link::LINK_TYPE[1];
            }
            $link->viewed_count = 0;
            $link->save();

            if ($request->event_type == 'pay') {
                $this->saveEmailTemplate($link, $request->email_confirmation, 'confirmation');
                $this->saveEmailTemplate($link, $request->email_confirmed, 'confirmed');
            }

            return redirect()->route('admin.link.view')->with('success', 'Berhasil ditambah');
        } catch (\Throwable $th) {
            if (config('app.debug')) throw $th;
            Log::error('Error in ' . __FILE__ . ' Line: ' . __LINE__ . ' Message ' . $th->getMessage());
            Log::error($th);

            return back()->withInput($request->all())->with('error', 'Server Error on Creating Link');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $link = Link::findorfail($id);
        $menu = [
            [
                'label' => 'Active',
                'route' => 'admin.link.detail',
                'link' => route('admin.link.detail', $id)
            ],
            [
                'label' => 'Trash',
                'route' => 'admin.link.detail.trash',
                'link' => route('admin.link.detail.trash', $link)
            ]
        ];

        return view('admin.pages.links.member_info', ['id' => $id, 'title' => $link->title, 'link' => $link, 'menu' => $menu]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showTrash(Link $link)
    {
        $menu = [
            [
                'label' => 'Active',
                'route' => 'admin.link.detail',
                'link' => route('admin.link.detail', $link->id)
            ],
            [
                'label' => 'Trash',
                'route' => 'admin.link.detail.trash',
                'link' => route('admin.link.detail.trash', $link)
            ]
        ];

        return view('admin.pages.links.member_info_trash', ['id' => $link->id, 'title' => $link->title, 'link' => $link, 'menu' => $menu]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit_link = Link::findorfail($id);
        $methodManual = Invoice::PAYMENT_TYPE != 'multipayment';
        return view('admin.pages.links.edit', ['link_detail' => $edit_link, 'type_reg' => 'pay', 'methodManual' => $methodManual]);
    }

    public function editFree($id)
    {
        $edit_link = Link::findorfail($id);
        return view('admin.pages.links.edit', ['link_detail' => $edit_link, 'type_reg' => 'free']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LinkRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $link = Link::findorfail($id); // will return 404 if not found
            $link->title = $validated['title'];
            if ($request->filepath != null) {
                // compare if the file is different
                if ($link->banner != $request->filepath) {
                    // delete the old file
                    $link->banner = $request->filepath;
                }
            }
            $link->description = $validated['desc'];
            $link->registration_info = $validated['registration_info'];
            if ($validated['member_limit'] > 0) {
                $link->has_member_limit = true;
                $link->member_limit = $validated['member_limit'];
            } else {
                $link->has_member_limit = false;
                $link->member_limit = null;
            }
            $link->active_from = date("Y-m-d", strtotime($validated['open_date']));
            $link->active_until = date("Y-m-d", strtotime($validated['close_date']));
            if (isset($validated['event_date'])) {
                $link->event_date = date("Y-m-d", strtotime($validated['event_date']));
            }

            if ($link->link_type == 'pay') {
                $link->price = $validated['price'];
                if (isset($validated['bank'])) {
                    $link->bank_information = [
                        'name' => $validated['bank']['name'],
                        'account_number' => $validated['bank']['account_number'],
                        'account_name' => $validated['bank']['account_name'],
                    ];
                }
                $link->method_pay = $request->is_multipayment == null ? 'bank_transfer' : 'multipayment';
                $link->is_multiple_registrant_allowed = isset($validated['is_multiple_registrant_allowed']) ? true : false;
                if (isset($validated['is_multiple_registrant_allowed'])) {
                    $link->sub_member_limit = $validated['sub_member_limit'];
                }
            }

            $link->save();

            if ($request->event_type == 'pay') {
                $this->updateEmailTemplate($link, $request->email_confirmation, 'confirmation');
                $this->updateEmailTemplate($link, $request->email_confirmed, 'confirmed');
            }

            return redirect()->route('admin.link.view')->with([
                'stored' => true,
                'success' => 'Berhasil diubah'
            ]);
        } catch (\Throwable $th) {
            if (config('app.debug'))
                return $th;
            return abort(500, 'Request failed');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Link $link)
    {
        try {
            if ($request->ajax()) {
                $link->delete();
                return response()->json(['success' => 'Berhasil dihapus'], 200);
            } else {
                return response()->json(['error' => 'Request failed'], 400);
            }
        } catch (\Throwable $th) {
            if (config('app.debug'))
                return $th;
            return abort(500, 'Request failed');
        }
    }

    public function page(Request $request, $link)
    {
        $data = Cache::remember("link_{$link}", 60*3, function () use ($link) {
            return Link::where('link_path', $link)->first();
        });

        if ($data == null) {
            Cache::forget("link_{$link}");
            abort(404);
        }

        $currentLinkMembers = $data->members;
        $isLinkFull = false;
        $expired = true;
        $notYet = false;

        try {
            $cities = LocationCity::all();
            $selectCities = $cities->pluck('name', 'id');
            $selectCities = $selectCities->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
        } catch (\Throwable $th) {
            $cities = [];
            $selectCities = [];
        }

        if ($data->has_member_limit) {
            if ($data->link_type === 'free')
                $isLinkFull = !$this->isRegistrationMemberQuota($currentLinkMembers, $data->member_limit);
            else
                $isLinkFull = !$this->isRegistrationPaidMemberQuota($currentLinkMembers, $data->member_limit);
        }

        $date = date("Y-m-d");
        if ($data != null) {
            if ($date >= date("Y-m-d", strtotime($data->active_from)) && $date <= date("Y-m-d", strtotime($data->active_until))) {
                $expired = false;
                $data->viewed_count += 1;
                $data->save();
            } else if ($date < date("Y-m-d", strtotime($data->active_from))) {
                $notYet = true;
                return view('pages.pendaftaran.view', ['link' => $data, 'title' => 'Form Register Not Found', 'show' => $expired, 'notFound' => true, 'notYet' => $notYet]);
            }
        } else {
            return view('pages.pendaftaran.view', ['link' => $data, 'title' => 'Form Register Not Found', 'show' => $expired, 'notFound' => true]);
        }

        return view('pages.pendaftaran.view', ['link' => $data, 'show' => $expired, 'selectCities' => $selectCities, 'isLinkFull' => $isLinkFull]);
    }

    public function changeVisibility($id)
    {
        $link = Link::where('id', $id)
            ->when(!Gate::allows('isSuperAdmin'), function ($query) {
                return $query->where('created_by', auth()->id());
            })->first();
        if ($link) {
            $link->hide_events = !$link->hide_events;
            $link->save();

            return response()->json(['message' => 'Berhasil mengubah status', 'success' => true], 200);
        } else {
            return response()->json(['message' => 'Request failed', 'success' => false], 400);
        }
    }


    public function dtb_memberLink($id)
    {
        $link = Link::find($id);
        $data = Member::query()->where('link_id', $id)->with('invoices');
        $edit = '';
        if ($link->link_type == 'pay') {
            return $this->payMemberList($data, $link);
        } else {
            return $this->freeMemberList($data, $link);
        }
    }

    public function dtb_memberLinkTrash(Link $link)
    {
        $memberTrash = $link->membersTrash;
        $memberTrash = $memberTrash->sortBy('id');

        return DataTables::of($memberTrash)
            ->addIndexColumn()
            ->removeColumn('created_at', 'updated_at')
            ->addColumn('registered', function ($data) {
                return date("d/M/Y, H:i", strtotime($data->created_at)) . ' WIB';
            })
            ->addColumn("status", function ($data) {
                $date = date("Y-m-d");
                return '<div class="mb-2 mr-2 badge badge-danger">Exp / Deleted</div>';
            })
            ->addColumn("options", function ($data) use ($link) {
                $edit = "<a href=\"javascript:void(0);\" onClick=\"pageTrashMemberLink.info(" . $data->id . ");\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-info\" style=\"margin-right:0.2rem;\">
                <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                    <i class=\"pe-7s-rocket fa-w-20\"></i>
                </span>
                Show Info
                </a>";
                return $edit;
            })
            ->rawColumns(['status', 'options'])
            ->make(true);
    }

    public function dtb_link(User $user)
    {
        $id = auth()->id();
        $user = auth()->user();
        if ($user->role == 'super admin') {
            $data = Link::latestFirst()->withCount('members')->get();
        } else {
            $data = $this->IncludeLink($user, $id);
        }
        // sort data by id desc
        $data = $data->sortByDesc('id');
        // $data = Link::withCount('members')->get();
        $edit = '';
        return DataTables::of($data)
            ->addIndexColumn()
            ->removeColumn('created_at', 'updated_at', 'description')
            ->addColumn('date_status', function ($data) {
                $date = date("Y-m-d");
                if ($date >= date("Y-m-d", strtotime($data->active_from)) && $date <= date("Y-m-d", strtotime($data->active_until))) {
                    return '<div class="mb-2 mr-2 badge badge-info">Tutup Pada: ' . $data->active_until . '</div>';
                } else if ($date <= date("Y-m-d", strtotime($data->active_from))) {
                    return '<div class="mb-2 mr-2 badge badge-warning">Buka Pada: ' . $data->active_from . '</div>';
                } else if ($date >= date("Y-m-d", strtotime($data->active_until))) {
                    return '<div class="mb-2 mr-2 badge badge-danger">Selesai</div>';
                }
            })
            ->editColumn("method_pay", function ($data) {
                return $data->method_pay == 'bank_transfer' ? 'bank' : 'multi';
            })
            ->editColumn("link_path", function ($data) {
                return route('form.link.view', ['link' => $data->link_path]);
            })
            ->editColumn("members_count", function ($data) {
                return $data->members_count . ' Orang';
            })
            ->addColumn('hide_button', function ($data) {
                if ($data->hide_events) {
                    return '<button onclick="showHideEvent(' . $data->id . ')" id="show-hide-' . $data->id . '" class="mb-2 mr-2 badge border-0 badge-pill badge-danger" style="margin-right:0.2rem;" title="Event Hide">
                    <span class="btn-icon-wrapper pr-2 opacity-7">
                        <i class="pe-7s-close-circle fa-w-20"></i>
                    </span>
                    Hide
                    </button>';
                } else {
                    return '<button onclick="showHideEvent(' . $data->id . ')" id="show-hide-' . $data->id . '" class="mb-2 mr-2 badge border-0 badge-pill badge-success" style="margin-right:0.2rem;" title="Event Showing">
                    <span class="btn-icon-wrapper pr-2 opacity-7">
                        <i class="pe-7s-check fa-w-20"></i>
                    </span>
                    Showed
                    </button>';
                }
            })
            ->addColumn("status", function ($data) {
                $date = date("Y-m-d");
                if ($date >= date("Y-m-d", strtotime($data->active_from)) && $date <= date("Y-m-d", strtotime($data->active_until))) {
                    return '<div class="mb-2 mr-2 badge badge-success">Buka</div>';
                } else {
                    return '<div class="mb-2 mr-2 badge badge-danger">Tutup</div>';
                }
            })
            ->addColumn("options", function ($data) {
                if ($data->link_type == "pay") {
                    $edit = "<a href=\"" . route('admin.link.edit', ['id' => $data->id]) . "\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-warning\" style=\"margin-right:0.2rem;\">
                    <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                        <i class=\"pe-7s-rocket fa-w-20\"></i>
                    </span>
                    Edit
                    </a>";
                } else {
                    $edit = "<a href=\"" . route('admin.link.edit.free', ['id' => $data->id]) . "\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-warning\" style=\"margin-right:0.2rem;\">
                    <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                        <i class=\"pe-7s-rocket fa-w-20\"></i>
                    </span>
                    Edit
                    </a>";
                }
                $edit .= "<a href=\"" . route('admin.link.detail', ['id' => $data->id]) . "\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-info\" style=\"margin-right:0.2rem;\">
                    <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                        <i class=\"pe-7s-rocket fa-w-20\"></i>
                    </span>
                    List
                </a>";
                $edit .= "<a href='javascript:void(0)' onclick=\"deleteScriptJs('";
                $edit .= route('admin.link.delete', $data);
                $edit .= "')\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-danger\" style=\"margin-right:0.2rem;\">
                    <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                        <i class=\"pe-7s-trash fa-w-20\"></i>
                    </span>
                    Hapus
                    </a>";
                return $edit;
            })
            ->rawColumns(['date_status', 'link_path', 'members_count', 'status', 'options', 'hide_button'])
            ->make(true);
    }

    public function generate_token($length = 5)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    private function IncludeLink(User $user, $id)
    {
        $sel = $user->find($id);
        $links = $sel->links()->latestfirst()->withCount('members')->get();

        return $links;
    }

    public function payMemberList($data, $link)
    {
        if (request()->has('order')) $colOrder = request()->order[0]['column'];
        // sort desc id
        $data = $data
        ->join('invoices', 'members.id', '=', 'invoices.member_id')
        ->select('members.*', 'invoices.status as invoice_status')
        ->when($colOrder === '0', function ($query) {
            $query->orderBy('id', 'desc'); // Default order if no sorting is applied
        });

        return DataTables::of($data)
            ->addIndexColumn()
            ->removeColumn('created_at', 'updated_at', 'invoices')
            ->addColumn("status", function ($data) {
                $date = date("Y-m-d");
                if ($data->invoices->status == 0) {
                    return '<div class="mb-2 mr-2 badge badge-danger">' . Invoice::INVO_STATUS[0] . '</div>';
                }
                if ($data->invoices->status == 1) {
                    return '<div class="mb-2 mr-2 badge badge-warning">' . Invoice::INVO_STATUS[1] . '</div>';
                }
                if ($data->invoices->status == 2) {
                    return '<div class="mb-2 mr-2 badge badge-success">' . Invoice::INVO_STATUS[2] . '</div>';
                }
            })
            ->addColumn('registered', function ($data) {
                return date("d/M/Y, H:i", strtotime($data->created_at)) . ' WIB';
            })
            ->addColumn("options", function ($data) use ($link) {
                $link = $data->link;
                if ($data->invoices->status == 1) {
                    $edit = "<a href=\"javascript:void(0);\" onClick=\"viewPayment(" . $data->id . ");\" aria-expanded=\"false\" data-toggle=\"modal\" data-target=\"#ModalViewPict\" class=\"mb-2 mr-2 badge badge-pill badge-info\" style=\"margin-right:0.2rem;\">
                    <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                        <i class=\"pe-7s-rocket fa-w-20\"></i>
                    </span>
                    Cek Bukti Bayar
                </a>";
                } else if ($data->invoices->status == 2 && (!$data->invoices->is_automatic)) {
                    $edit = "<a href=\"javascript:void(0);\" onClick=\"viewProof('" . asset('storage/bukti_image/' . $data->bukti_bayar) . "');\" aria-expanded=\"false\" data-toggle=\"modal\" data-target=\"#ModalViewPict\" class=\"mb-2 mr-2 badge badge-pill badge-info\" style=\"margin-right:0.2rem;\">
                    <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                        <i class=\"pe-7s-rocket fa-w-20\"></i>
                    </span>
                    Lihat Bukti Bayar
                    </a>";
                } else if ($data->invoices->status == 2 && ($data->invoices->is_automatic)) {
                    $edit = "<span class=\"mb-2 mr-2 badge badge-pill badge-info\" style=\"margin-right:0.2rem;\">Method: " . $data->invoices->payment_method . "</span>";
                } else {
                    $edit = '';
                }

                if ($link->is_multiple_registrant_allowed) {
                    $edit .= "<a href=\"javascript:void(0);\" onClick=\"detailPeserta(" . $data->id . ");\" aria-expanded=\"false\" data-toggle=\"modal\" data-target=\"#ModalDetailPeserta\" class=\"mb-2 mr-2 badge badge-pill badge-secondary\" style=\"margin-right:0.2rem;\">
                    <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                        <i class=\"pe-7s-rocket fa-w-20\"></i>
                    </span>
                    Detail Peserta
                </a>";
                }

                if (auth()->user()->email == 'akasa2444@gmail.com') {
                    $edit .= "<a href=\"javascript:void(0);\" onClick=\"deleteScriptJs('";
                    $edit .= route('admin.member.delete.registrant', [$link, $data]);
                    $edit .= "')\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-danger\" style=\"margin-right:0.2rem;\">
                    <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                        <i class=\"pe-7s-trash fa-w-20\"></i>
                    </span>
                    Hapus
                    </a>";
                }

                return $edit;
            })
            ->filterColumn('status', function ($query, $keyword) {
                //
            })
            ->filterColumn('full_name', function ($query, $keyword) {
                $query->where('full_name', 'like', '%' . $keyword . '%');
            })
            ->filterColumn('email', function ($query, $keyword) {
                $query->where('email', 'like', '%' . $keyword . '%');
            })
            ->filterColumn('corporation', function ($query, $keyword) {
                $query->where('corporation', 'like', '%' . $keyword . '%');
            })
            ->orderColumn('full_name', function ($query, $direction) {
                $query->orderBy('full_name', $direction);
            })
            ->orderColumn('email', function ($query, $direction) {
                $query->orderBy('email', $direction);
            })
            ->orderColumn('corporation', function ($query, $direction) {
                $query->orderBy('corporation', $direction);
            })
            ->orderColumn('status', function ($query, $direction) {
                $query->orderBy('invoice_status', $direction)->orderBy('id', $direction);
            })
            ->rawColumns(['status', 'options'])
            ->make(true);
    }

    public function freeMemberList($data, $link)
    {
        if (request()->has('order')) $colOrder = request()->order[0]['column'];
        $data = $data->when($colOrder === '0', function ($query) {
            $query->orderBy('id', 'desc'); // Default order if no sorting is applied
        });

        return DataTables::of($data)
            ->addIndexColumn()
            ->removeColumn('created_at', 'updated_at')
            ->orderColumn('DT_RowIndex', function ($query, $keyword) {
                // $query->where('email', 'like', '%' . $keyword . '%');
            })
            ->addColumn('registered', function ($data) {
                return date("d/M/Y, H:i", strtotime($data->created_at)) . ' WIB';
            })
            ->addColumn("status", function ($data) {
                $date = date("Y-m-d");
                return '<div class="mb-2 mr-2 badge badge-success">Terdaftar</div>';
            })
            ->addColumn("options", function ($data) use ($link) {
                $edit = '';
                if (auth()->user()->email == 'akasa2444@gmail.com') {
                    $edit .= "<a href=\"javascript:void(0);\" onClick=\"deleteScriptJs('";
                    $edit .= route('admin.member.delete.registrant', [$link, $data]);
                    $edit .= "')\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-danger\" style=\"margin-right:0.2rem;\">
                    <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                        <i class=\"pe-7s-trash fa-w-20\"></i>
                    </span>
                    Hapus
                    </a>";
                }
                return $edit;
            })
            ->filterColumn('status', function ($query, $keyword) {
                //
            })
            ->filterColumn('full_name', function ($query, $keyword) {
                $query->where('full_name', 'like', '%' . $keyword . '%');
            })
            ->filterColumn('email', function ($query, $keyword) {
                $query->where('email', 'like', '%' . $keyword . '%');
            })
            ->filterColumn('corporation', function ($query, $keyword) {
                $query->where('corporation', 'like', '%' . $keyword . '%');
            })
            ->orderColumn('full_name', function ($query, $direction) {
                $query->orderBy('full_name', $direction);
            })
            ->orderColumn('email', function ($query, $direction) {
                $query->orderBy('email', $direction);
            })
            ->orderColumn('corporation', function ($query, $direction) {
                $query->orderBy('corporation', $direction);
            })
            ->orderColumn('status', function ($query, $direction) {
                //
            })
            ->rawColumns(['status', 'options'])
            ->make(true);
    }
}
