<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Mail\ConfirmationAttendances;

use App\Models\AttendPaymentStore;
use App\Models\MemberAttend;
use App\Models\Attendance;
use App\Helpers\Midtrans;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Email;
use App\Models\Link;

use App\Helpers\GenerateStringUnique;
use App\Http\Traits\SnapTokenCreate;
use App\Http\Traits\FileUploadTrait;
use App\Http\Traits\AttendingTrait;

use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\AttendingRequest;
use App\Models\Member;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AttendanceController extends Controller
{
    use FileUploadTrait, AttendingTrait, SnapTokenCreate;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.attendance.view');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type)
    {
        $user = auth()->user();
        if ($user->role == 'super admin') {
            $links = Link::all();
        } else {
            $links = Link::myLinksEventRange();
            // $links = auth()->user()->links;
        }
        
        return view('admin.pages.attendance.create', compact('type', 'links'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttendanceRequest $request, $type)
    {
        try {
            $validated = $request->validated();
            if (isset($validated['mail_confirm']) && (!$validated['confirmation_mail'])) {
                return back()->with('error', 'Kolom email konfirmasi tidak boleh kosong, (Jika email konfirmasi: Ya)');
            }
            if ($validated['link_id']->link_type != 'free' && isset($validated['allow_non_register'])) {
                return back()->with('error', 'Kolom izinkan non register tidak boleh di centang, (Jika tipe link: Berbayar)');
            }
            $validated['link_id'] = $validated['link_id']->id;
            $attend = Attendance::select('attendance_path')->get();
            $token = GenerateStringUnique::make('Attendance', 'attendance_path')->getToken(6);
            $validated['attendance_path'] = $token;

            // create attendance
            $attendance = Attendance::create($validated);

            return redirect()->route('admin.attendance.view')->with('success', 'Attendance created successfully');


        } catch (\Throwable $th) {
            if (config('app.debug'))
                throw $th;
            Log::error($th);
            
            return back()->with('error', 'Something went wrong, failed create attendance');
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        $event = $attendance->link;

        return view('admin.pages.attendance.detail', compact('attendance', 'event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Attendance $attendance)
    {
        if ($request->ajax()) {
            try {
                DB::beginTransaction();
                $attendance->delete();
                DB::commit();

                return response()->json([
                    'message' => 'Attendance deleted successfully'
                ], 200);
            } catch (\Throwable $th) {
                DB::rollback();
                if (config('app.debug'))
                    throw $th;
                report($th);
                
                return response()->json([
                    'message' => 'Something went wrong, failed delete attendance'
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Method not allowed'
            ], 405);
        }
    }

    public function page ($link)
    {
        $show = true;
        $date = date("Y-m-d H:i:s");
        $showFormAsRegister = false;
        $attendance = Attendance::where('attendance_path', $link)->first();
        if (!$attendance) {
            $show = false;
        }

        if ($attendance) {
            if($date >= date("Y-m-d H:i:s", strtotime($attendance->active_from)) && $date <= date("Y-m-d H:i:s", strtotime($attendance->active_until)) ){
                $show = true;
            }else{
                $show = false;
            }
            $link = $attendance->link;

            if ($attendance->link->hide_events && $attendance->allow_non_register) {
                $showFormAsRegister = true;
            }
        }

        if ($showFormAsRegister) {
            return view('pages.absensi.temp_page', ['link' => $attendance->link ?? null] ,compact('attendance', 'show'));
        }

        if (config('app.locale') == 'id') {
            $webTitle = 'Absensi: ';
        } else {
            $webTitle = 'Attendance: ';
        }

        return view('pages.absensi.page', ['link' => $attendance->link ?? null] ,compact('attendance', 'show', 'webTitle'));
    }

    public function attending(AttendingRequest $request, Attendance $attendance)
    {
        try {
            $attendance->load('link');
            DB::beginTransaction();
            $validated = $request->validated();
            if (!$validated['member_id']) {
                DB::rollback();
                return back()
                    ->withInput($request->only('email'))
                    ->with('error', __('attend.failed_member_not_found'));
            }
            if (isset($validated['new_member_created'])) {
                $member = $validated['created_member'];
            } else {
                $member = Member::find($validated['member_id']);
            }

            $member_attend = MemberAttend::where('member_id', $validated['member_id'])->where('attend_id', $attendance->id)->first();
            if ($member_attend) {
                DB::rollback();
                return back()
                    ->withInput($request->only('email'))
                    ->with('info', __('attend.already_attend'));
            }

            // check does link is pay
            $link = $member->link;
            if ($link->link_type == 'pay') {
                $member_invoice = $member->invoices;

                if (!$member_invoice->isInvoiceLunas()) {
                    DB::rollback();
                    return back()
                        ->withInput($request->only('email'))
                        ->with('error', __('attend.failed_payment_status'));
                }
            }

            if (isset($validated['full_name'])) {
                $member->full_name = $validated['full_name'];
                $member->save();
            }

            $midtrandsConfig = new Midtrans();

            if ($attendance->is_using_payment_gateway && $midtrandsConfig->isMidtransConfigured()) {
                $payStore = AttendPaymentStore::where('attend_id', $attendance->id)->where('member_id', $member->id)->first();
                if ($payStore != null) {
                    $order = $payStore->order;
                    return redirect()->route('attend.waiting-payment', ['attendance' => $attendance->attendance_path, 'orderNumber' => $order->order_number])
                        ->with('info', 'You have an unpaid order');
                }
            }

            if (($attendance->is_using_payment_gateway && $midtrandsConfig->isMidtransConfigured()) && $validated['is_certificate']) {
                $payStore = AttendPaymentStore::where('attend_id', $attendance->id)->where('member_id', $member->id)->first();
                if (!$payStore) {
                    $order = $this->createOrderCertificate($attendance, $member, (isset($validated['full_name'])) ? $validated['full_name'] : null);
                    DB::commit();

                    return redirect()->route('attend.waiting-payment', ['attendance' => $attendance->attendance_path, 'orderNumber' => $order->order_number]);
                }
            }

            $MemberAttend = MemberAttend::create($validated);
            if ($validated['is_certificate'] && $MemberAttend) {
                if (!$attendance->is_using_payment_gateway) {
                    Log::info('######## create attending member with certificate without payment gateway ########');
                    $MemberAttend->payment_proof = $this->saveInvoice($validated['bukti'], MemberAttend::CERT_PAYMENT_PROOF);
                    $MemberAttend->save();
                }
            }

            $email = false;
            if ($attendance->confirmation_mail) {
                $asReg = $attendance->link->hide_events && $attendance->allow_non_register;
                $this->sendConfirmationAttendanceMail($attendance, $validated['member_id'], $member, $asReg);
                $email = true;
            }
            $asReg = $attendance->link->hide_events && $attendance->allow_non_register;
            $emailText = $email ? __('attend.success_with_email', ['email' => $member->email]) : '';

            DB::commit();

            if ($asReg) {
                return back()->with('success', __('attend.success_as_reg').$emailText);;
            }

            return back()->with('success', __('attend.success').$emailText);
        } catch (\Throwable $th) {
            DB::rollback();
            if (config('app.debug')) throw $th;

            Log::error('Error: AttendanceController - attending() | ');
            Log::error($th);

            return back()->with('error', 'Something went wrong');
        }
    }

    public function waitingPayment($attendance, $orderNumber)
    {
        $checkPaymentStatusUrl = null;
        $attendance = Attendance::where('attendance_path', $attendance)->first();
        $order = Order::where('order_number', $orderNumber)->first();
        if (!$attendance || !$order) {
            abort(404);
        }

        $attendStore = AttendPaymentStore::where('attend_id', $attendance->id)->where('order_id', $order->id)->first();
        if (!$attendStore) {
            abort(404);
        }

        // check is attendStore->due_date is expired
        if ($attendStore->due_date < Carbon::now()) {
            $attendStore->delete();
            $order->status = 'void';
            $order->save();
            
            abort(404);
        }

        if (!$order->paid_at) {
            $checkPaymentStatusUrl = route('api.certificate.payment.check', ['order_number' => $order->order_number, 'temp_store_id' => $attendStore->id]);
        }

        $this->createTransaction($order);

        return view('pages.absensi.waiting_payment', [
            'title' => 'Pembayaran Sertifikat',
            'finished' => $order->paid_at != null,
        ], compact('attendance', 'order', 'checkPaymentStatusUrl'));
    }

    public function sendConfirmationAttendanceMail(Attendance $attendance, int $member_id, $member = null, $asRegister = false)
    {
        try {
            $customSubject = null;
            if ($asRegister) {
                $customSubject = 'Thank you for attending our event';
            }

            $data = [];
            if ($member == null) {
                $member = Member::find($member_id);
            }

            $data['name'] = $member->full_name;
            $data['email'] = $member->email;
            $data['phone'] = $member->contact_number;
            $data['event'] = $attendance->link->title;
            $data['message'] = $attendance->confirmation_mail;
            $data['link_path'] = $attendance->link->link_path;

            $from_mail = Email::EMAIL_FROM;

            Mail::to($data['email'])->send(new ConfirmationAttendances($data, $from_mail, $customSubject));
            $mail_db = new Email;
            $mail_db->send_from = $from_mail;
            $mail_db->send_to = $data['email'];
            $mail_db->message = $attendance->confirmation_mail;
            $mail_db->user_id = $member->id;
            $mail_db->type_email = Email::TYPE_EMAIL[4];
            $mail_db->sent_count = 1;
            $mail_db->save();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function dtb_attendance()
    {
        $data = Attendance::ownAttendance()->with('link:id,link_path,title,link_type')->get();
        
        return DataTables::of($data)
        ->addIndexColumn()
        ->removeColumn('id', 'created_at', 'updated_at', 'created_by', 'link_id', "link.description", 'link.registration_info', 'confirmation_mail')
        ->editColumn('active_from', function($data) {
            return date("d-m-Y", strtotime($data->active_from));
        })
        ->editColumn('active_until', function($data) {
            return date("d-m-Y", strtotime($data->active_until));
        })
        ->addColumn('attend_path', function($data){
            return route('attend.link', $data->attendance_path);
        })
        ->addColumn('members_count', function($data) {
            return count($data->link->members);
        })
        ->addColumn('attend_count', function($data) {
            $member = count($data->link->members);
            $attending = count($data->member_attend);
            if ($member != 0) {
                $html = $attending . ' / ' . $member;
            } else {
                $html = $attending . ' / ' . $member;
            }
            
            return $html;
        })
        ->addColumn('attend_percentage', function ($data) {
            $member = count($data->link->members);
            $attending = count($data->member_attend);

            if ($member != 0) {
                $html = round($attending / $member * 100, 2) . '%';
            } else {
                $html = '0%';
            }

            return $html;
        })
        ->addColumn("status", function($data) {
            $date = date("Y-m-d H:i:s");
            if($date >= date("Y-m-d H:i:s", strtotime($data->active_from)) && $date <= date("Y-m-d H:i:s", strtotime($data->active_until)) ){
                return '<div class="mb-2 mr-2 badge badge-success">Buka</div>';
            }else{
                return '<div class="mb-2 mr-2 badge badge-danger">Tutup</div>';
            }
            
        })
        ->addColumn("options", function($data) {
            $edit = "<a href='javascript:void(0)' onclick=\"deleteScriptJs('";
            $edit .= route('admin.attendance.delete', $data);
            $edit .= "')\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-danger\" style=\"margin-right:0.2rem;\">
                  <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                      <i class=\"pe-7s-trash fa-w-20\"></i>
                  </span>
                  Hapus
                </a>";
            $edit .= "<a href=\"".route('admin.attendance.detail', $data)."\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-info\" style=\"margin-right:0.2rem;\">
                <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                    <i class=\"pe-7s-rocket fa-w-20\"></i>
                </span>
                List
              </a>";
            return $edit;
        })
        ->rawColumns(['status', 'options'])
        ->make(true);
    }

    public function dtb_member(Request $request)
    {
        $request->validate([
            'attendings' => 'required|in:true,false',
            'attend_id' => 'required|exists:attendances,id'
        ]);

        $data = Attendance::find($request->attend_id);
        $member_attend = $data->member_attend;
        $member_attend->load('member');
        $member_attend->map(function ($item, $key){
            $item->member->certificate = $item->certificate;
            $item->member->payment_proof = $item->payment_proof;
            $item->member->attend = $item->created_at;
        });
        // pluck member from member attend
        $member = $member_attend->pluck('member')->flatten();
        // add member attend to member
        foreach ($member_attend as $key => $value) {
            $member[$key]->attend = $value->created_at;
        }

        if ($request->attendings == 'false') {
            $member = $data->link->members->diff($member);
        }

        $member = $member->sortByDesc('attend');
        $member = $member->values();
        $member = $member->map(function ($item, $key) {
            $item->load('certbuy.order');

            return $item;
        });

        return DataTables::of($member)
            ->addIndexColumn()
            ->addColumn('full_name', function ($row) {
                return $row->full_name;
            })
            ->addColumn('email', function ($row) {
                return $row->email;
            })
            ->addColumn('phone_number', function ($row) {
                return $row->contact_number;
            })
            ->addColumn('instansi', function ($row) {
                return $row->corporation;
            })
            ->addColumn('attend', function ($row) {
                //diffForHumans
                return $row->attend ? $row->attend->diffForHumans() : null;
            })
            ->addColumn('transaction_cert', function ($row) {
                return $row->certbuy ? $row->certbuy->order->status : null;
            })
            ->editColumn('payment_proof', function ($row) {
                return $row->payment_proof ? asset('storage/bukti_image/'.MemberAttend::CERT_PAYMENT_PROOF.$row->payment_proof) : null;
            })
            ->addColumn('options', function ($row) {
                if ($row->payment_proof){
                    return "viewProof('".asset('storage/bukti_image/'.MemberAttend::CERT_PAYMENT_PROOF.$row->payment_proof)."', '".$row->full_name."')";
                };

                return false;
            })
            ->only(['full_name', 'email', 'phone_number', 'instansi', 'attend', 'certificate', 'transaction_cert', 'payment_proof', 'options'])
            ->rawColumns(['payment_proof'])
            ->make(true);


        if ($request->ajax())
        {
            
        } else {
            return response()->json([
                'message' => 'Method not allowed'
            ], 405);
        }
    }
}
