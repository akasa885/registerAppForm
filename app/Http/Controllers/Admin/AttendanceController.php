<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\MemberAttend;
use App\Models\Attendance;
use App\Models\Link;

use App\Http\Traits\GenerateTokenUniqueColumnTrait;
use App\Http\Traits\FileUploadTrait;

use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\AttendingRequest;

use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
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
            $attend = Attendance::select('attendance_path')->get();
            $token = $this->getToken($attend->toArray(), 'attendance_path', 6);
            $validated['attendance_path'] = $token;

            // create attendance
            $attendance = Attendance::create($validated);

            return redirect()->route('admin.attendance.view')->with('success', 'Attendance created successfully');


        } catch (\Throwable $th) {
            if (config('app.debug'))
                throw $th;
            
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
            $validated = $request->validated();
            $member_attend = MemberAttend::where('member_id', $validated['member_id'])->where('attend_id', $attendance->id)->first();
            if ($member_attend) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('info', __('attend.already_attend'));
            }
            DB::beginTransaction();
            $MemberAttend = MemberAttend::create($validated);
            if ($validated['is_certificate'] && $MemberAttend) {
                $MemberAttend->payment_proof = $this->saveInvoice($validated['bukti'], MemberAttend::CERT_PAYMENT_PROOF);
                $MemberAttend->save();
            }
            DB::commit();
            return back()->with('success', __('attend.success'));
        } catch (\Throwable $th) {
            DB::rollback();
            if (config('app.debug')) throw $th;

            Log::error('Error: AttendanceController - attending() | ');
            Log::error($th->getMessage());

            return back()->with('error', 'Something went wrong, failed attend');
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
        $member = $member_attend->pluck('member')->flatten();
        // add member attend to member
        foreach ($member_attend as $key => $value) {
            $member[$key]->attend = $value->created_at;
        }

        if ($request->attendings == 'false') {
            $member = $data->link->members->diff($member);
        }

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
                return '';
            })
            ->only(['full_name', 'email', 'phone_number', 'instansi', 'attend'])
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
