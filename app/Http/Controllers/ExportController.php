<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Link;

use Illuminate\Support\Facades\Log;

use App\Exports\memberOfEvent;
use App\Exports\attendanceOfEvent;
use App\Exports\AllOfTheMember;
use App\Models\Attendance;

class ExportController extends Controller
{
    public function memberExport(Link $link) 
    {
        try {
            $member_export = new memberOfEvent($link);
            $member_export->exportProcess();

            $filename = $member_export->getFilename();
            return Excel::download($member_export, $filename);
        } catch (\Throwable $th) {
            if(config('app.debug')) throw $th;
            Log::error($th);
            return abort(404, 'Exportable model not found');
        }
    }

    public function attendanceExport(Attendance $attendance)
    {
        try {
            $attendance_export = new attendanceOfEvent($attendance);
            $attendance_export->exportProcess();

            $filename = $attendance_export->getFilename();
            return Excel::download($attendance_export, $filename);
        } catch (\Throwable $th) {
            if (config('app.debug')) throw $th;
            Log::error($th);
            return abort(404, 'Exportable model not found');
        }
    }

    public function memberExportAll() {
        try {
            $member = new AllOfTheMember();
            $member->exportProcess();

            $filename = 'Export All Member - ' . date('Y-m-d H:i:s') . '.xlsx';


            return Excel::download($member, $filename);
        } catch (\Throwable $th) {
            if(config('app.debug')) throw $th;
            Log::error($th);
            return abort(404, 'Exportable model not found');
        }
    }
}
