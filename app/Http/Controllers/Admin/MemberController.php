<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;

//mail
use App\Mail\ConfirmedPay;

use App\Models\Member;
use App\Models\Invoice;
use App\Models\Email;

use App\Http\Traits\MailPaymentTrait;
use App\Http\Traits\OrderCustomTrait;
use App\Models\Link;
use App\Models\MemberTrash;
use Carbon\Carbon;

class MemberController extends Controller
{
    use MailPaymentTrait, OrderCustomTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.members.list', [
            'title' => 'Kelola | Daftar Member',
            'subtitle' => 'This is page where all member registered in the link will be listed & managed',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Member $member)
    {
        //
    }

    public function dtListAll(Request $request)
    {
        
        $data = Member::query()
            ->select(
                'email', 
                DB::raw('count(*) as registered_count'),
                DB::raw('max(members.created_at) as last_registered')
            )
            ->join('links', 'members.link_id', '=', 'links.id')
            ->leftJoin('invoices', function ($join) {
                $join->on('members.id', '=', 'invoices.member_id')->where('invoices.status', 2);
            })
            ->where(function ($type) {
                $type->where('links.link_type', 'free')
                    ->orWhere(function ($query) {
                        $query->where('links.link_type', 'pay')->whereNotNull('invoices.id');
                    });
            });
        
        if (!Gate::allows('isSuperAdmin')) {
            $data->where('links.created_by', auth()->user()->id);
        }

        $data->groupBy('email')
            ->orderBy('registered_count', 'desc');

        // add the information of full_name, phone_number, and domisili of the member

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('full_name', function ($row) {
                $member = Member::select('full_name')->where('email', $row->email)->latest()->first();
                return $member->full_name;
            })
            ->addColumn('contact_number', function ($row) {
                $member = Member::select('contact_number')->where('email', $row->email)->latest()->first();
                return $member->contact_number;
            })
            ->addColumn('domisili', function ($row) {
                $member = Member::select('domisili')->where('email', $row->email)->latest()->first();
                return $member->domisili;
            })
            ->addColumn('options', function ($row) {
                $btn = '<a href="#" class="btn btn-primary btn-sm">View</a>';
                return $btn;
            })
            ->orderColumn('DT_RowIndex', function ($query, $keyword) {
                // $query->where('email', 'like', '%' . $keyword . '%');
            })
            ->filterColumn('full_name', function ($query, $keyword) {
                $query->where('full_name', 'like', '%' . $keyword . '%');
            })
            ->filterColumn('contact_number', function ($query, $keyword) {
                $query->where('contact_number', 'like', '%' . $keyword . '%');
            })
            ->filterColumn('domisili', function ($query, $keyword) {
                $query->where('domisili', 'like', '%' . $keyword . '%');
            })
            ->filterColumn('registered_count', function ($query, $keyword) {
                $query->having('registered_count', 'like', '%' . $keyword . '%');
            })
            ->filterColumn('options', function ($query, $keyword) {
                //
            })
            ->rawColumns(['options'])
            ->make(true);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('options', function ($row) {
                $btn = '<a href="#" class="btn btn-primary btn-sm">View</a>';
                return $btn;
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function deleteRegistrant(Request $request, Link $link, Member $member)
    {
        try {
            DB::beginTransaction();
            $isPay = $link->link_type == 'pay' ? true : false;

            // check member is registered in the link
            $data = Member::where('link_id', $link->id)->first();
            if (!$data || $data->id != $member->id) {
                return response()->json(['success' => false, 'message' => 'Registrant not found'], 404);
            }

            // make member to trash
            $trash = $member->toArray();
            $trash['deleted_time'] = Carbon::now();
            $trash['link_id'] = $link->id;
            $trash = MemberTrash::create($trash);

            $member->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Registrant Deleted']);
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug')) throw $th;
            Log::error('Error delete registrant');
            Log::error($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Request Error'], 500);
        }
    }

    public function viewSheet($id)
    {
        $data = Member::findorfail($id);
        if ($data->bukti_bayar == null) {
            return response()->json(['success' => false, 'message' => 'Bukti tidak ada!'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Bukti ditemukan', 'bukti' => asset('storage/bukti_image') . '/' . $data->bukti_bayar, 'memberId' => $data->id], 200);
    }

    public function updateBukti(Request $request, Member $member)
    {
        $request->validate([
            'id' => 'required|exists:members,id',
            'received' => 'required|in:true,false',
        ]);
        $status = null;
        $message = '';
        if ($request->received == 'true') {
            $status = true;
        } else {
            $status = false;
        }
        try {
            if ($status) {
                DB::beginTransaction();
                $data = $member->findorfail($request->id);
                $invoice = Invoice::where('member_id', $data->id)->first();
                $invoice->status = 2;
                $order = $invoice->order;
                $order->status = 'Completed';

                $this->sendMailPaymentReceived($data->link, $data);
                $invoice->save();
                $order->save();

                DB::commit();
                $message = 'Pembayaran Diterima';
            } else {
                DB::beginTransaction();
                $data = $member->findorfail($request->id);
                $invoice = Invoice::where('member_id', $data->id)->first();
                $order = $invoice->order;
                $currentDateTime = Carbon::now();
                $newDateTime = Carbon::now()->addHours(24);
                $invoice->token = $this->getToken(Member::PAYMENT_TOKEN_LENGTH);
                $invoice->valid_until = $newDateTime;
                $invoice->status = 0;
                $invoice->save();

                $order->status = 'decline';
                $order->save();

                $newOrder = $this->createDuplicateOrder($order);

                $invoice->invoicedOrder->order_id = $newOrder->id;
                $invoice->invoicedOrder->save();

                $message_html = "<p> Bukti pembayaran anda ditolak, silahkan upload bukti anda kembali </p>";

                $this->sendMailPayment($data->link, $data, $invoice, true, $message_html);

                DB::commit();
                $message = 'Pembayaran Ditolak';
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug')) throw $th;
            Log::error('Error update bukti bayar');
            Log::error($th->getMessage());
            // return $data->id;
            // return $member->link;
            return response()->json(['success' => false, 'message' => 'Request Error'], 500);
        }
    }

    private function getToken($lenght_token = 10)
    {
        $fix_token = '';
        $lock = 0;
        $data_token = Invoice::select('token')->get();
        if (count($data_token) > 0) {
            $loop = count($data_token);
            for ($i = 0; $i < $loop;) {
                foreach ($data_token as $tok) {
                    $temp = $this->generate_token($lenght_token);
                    if ($tok->token != $temp) {
                        $lock++;
                    } else {
                        $lock = 0;
                    }
                }
                if ($loop == $lock) {
                    $fix_token = $temp;
                    $i = $loop;
                } else {
                    $i++;
                }
            }
            return $fix_token;
        } else {
            return $this->generate_token($lenght_token);
        }
    }

    public function generate_token($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
