<?php
namespace App\Http\Traits;
use App\Models\User;
use Yajra\DataTables\Facades\Datatables;

trait DataTablesTrait {

    public function AllUserListDt()
    {
        $data = User::where('role','!=','super admin')->orderBy('created_at', 'DESC')->get();
        $edit="";
            return DataTables::of($data)
        ->editColumn("status", function ($data) {
            if($data->status){
                return "<div class=\"mb-2 mr-2 badge badge-pill badge-success\">Aktif</div>";
            }else{
            return "<div class=\"mb-2 mr-2 badge badge-pill badge-danger\">Non Aktif</div>";
            }         
        })       
        ->editColumn('role', function ($data){
            return ucwords($data->role);
        })
        ->addColumn('Options', function ($data){
            $edit = "<a href=".route('admin.users.edit',['id' => $data->id])." aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-info\" style=\"margin-right:0.2rem;\">
                                                <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                                                    <i class=\"pe-7s-magic-wand fa-w-20\"></i>
                                                </span>
                                                Edit
                                            </a>";
            if($data->status){
                $edit .= "<a href=\"javascript:deactivateAction(".$data->id.");\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-danger\" style=\"margin-right:0.2rem;\">
                                                <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                                                    <i class=\"fa fa-trash fa-w-20\"></i>
                                                </span>
                                                Non Aktifkan
                                            </a>";
            }else{
                $edit .= "<a href=\"javascript:activateAction(".$data->id.");\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-success\" style=\"margin-right:0.2rem;\">
                                                <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                                                    <i class=\"pe-7s-magic-wand fa-w-20\"></i>
                                                </span>
                                                Aktifkan
                                            </a>";
            }
            return $edit;
        })
        ->rawColumns(['role','Options', 'status'])
        ->make(true);
    }
}