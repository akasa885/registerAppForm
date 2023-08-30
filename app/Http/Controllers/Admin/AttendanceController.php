<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Mail\ConfirmationAttendances;

use App\Models\MemberAttend;
use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\Email;
use App\Models\Link;

use App\Http\Traits\GenerateTokenUniqueColumnTrait;
use App\Http\Traits\FileUploadTrait;

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
    use GenerateTokenUniqueColumnTrait, FileUploadTrait;
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
        $links_active = Link::filterActiveMyLinks();
        $links = auth()->user()->links;
        
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
            $token = $this->getToken($attend->toArray(), 'attendance_path', 6);
            $validated['attendance_path'] = $token;

            // create attendance
            $attendance = Attendance::create($validated);

            return redirect()->route('admin.attendance.view')->with('success', 'Attendance created successfully');


        } catch (\Throwable $th) {
            if (config('app.debug'))
                throw $th;
            \Log::error($th);
            
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
    public function destroy(Attendance $attendance)
    {
        //
    }

    public function page ($link)
    {
        $show = true;
        $date = date("Y-m-d H:i:s");
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
        }
        
        return view('pages.absensi.page', ['link' => $attendance->link ?? null] ,compact('attendance', 'show'));
    }

    public function attending(AttendingRequest $request, Attendance $attendance)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            if (!$validated['member_id']) {
                DB::rollback();
                return back()
                    ->withInput($request->only('email'))
                    ->with('error', __('attend.failed_member_not_found'));
            }
            $member = Member::find($validated['member_id']);
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

            $MemberAttend = MemberAttend::create($validated);
            if ($validated['is_certificate'] && $MemberAttend) {
                $MemberAttend->payment_proof = $this->saveInvoice($validated['bukti'], MemberAttend::CERT_PAYMENT_PROOF);
                $MemberAttend->save();
            }

            $email = false;
            if ($attendance->confirmation_mail) {
                $this->sendConfirmationAttendanceMail($attendance, $validated['member_id']);
                $email = true;
            }
            $emailText = $email ? __('attend.success_with_email', ['email' => $member->email]) : '';

            DB::commit();
            return back()->with('success', __('attend.success').$emailText);
        } catch (\Throwable $th) {
            DB::rollback();
            if (config('app.debug')) throw $th;

            Log::error('Error: AttendanceController - attending() | ');
            Log::error($th->getMessage());

            return back()->with('error', 'Something went wrong, failed attend');
        }
    }

    private function sendConfirmationAttendanceMail(Attendance $attendance, int $member_id)
    {
        try {
            $data = [];
            $member = Member::find($member_id);
            $data['name'] = $member->full_name;
            $data['email'] = $member->email;
            $data['phone'] = $member->contact_number;
            $data['event'] = $attendance->link->title;
            $data['message'] = $attendance->confirmation_mail;
            $data['link_path'] = $attendance->link->link_path;

            $from_mail = Email::EMAIL_FROM;

            Mail::to($data['email'])->send(new ConfirmationAttendances($data, $from_mail));
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
        $data = Attendance::ownAttendance()->with('link')->get();
        
        return DataTables::of($data)
        ->addIndexColumn()
        ->removeColumn('id', 'created_at', 'updated_at', 'created_by', 'link_id')
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
                $html = $attending . ' / ' . $member. ' (' . round($attending / $member * 100, 2) . '%)';
            } else {
                $html = $attending . ' / ' . $member. ' (0%)';
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
            $edit = "<a href=\"".route('admin.attendance.delete', $data)."\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-danger\" style=\"margin-right:0.2rem;\">
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
            ->editColumn('payment_proof', function ($row) {
                return $row->payment_proof ? asset('storage/bukti_image/'.MemberAttend::CERT_PAYMENT_PROOF.$row->payment_proof) : null;
            })
            ->addColumn('options', function ($row) {
                if ($row->payment_proof){
                    return "viewProof('".asset('storage/bukti_image/'.MemberAttend::CERT_PAYMENT_PROOF.$row->payment_proof)."', '".$row->full_name."')";
                };

                return false;
            })
            ->only(['full_name', 'email', 'phone_number', 'instansi', 'attend', 'certificate', 'payment_proof', 'options'])
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
