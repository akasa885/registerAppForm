<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Attendance;
use App\Models\Link;

use App\Http\Traits\GenerateTokenUniqueColumnTrait;

use App\Http\Requests\AttendanceRequest;

class AttendanceController extends Controller
{
    use GenerateTokenUniqueColumnTrait;
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
    public function show($id)
    {
        //
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
}
