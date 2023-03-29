<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Link;

use App\Exports\memberOfEvent;

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
            return abort(404, 'Exportable model not found');
        }
    }
}
