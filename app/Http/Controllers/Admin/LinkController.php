<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Traits\MailPaymentTrait;

use App\Models\Link;
use App\Models\Member;
use App\Models\MailPayment;
use App\Models\Invoice;
use App\Models\User;

use App\Http\Requests\LinkRequest;

use DataTables;
use Carbon;

class LinkController extends Controller
{
    use MailPaymentTrait;
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
    public function create()
    {
        return view('admin.pages.links.add');
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
            $link = new Link;
            $link->link_path = $this->getToken(Link::TOKEN_LENGTH);
            $link->title = ucwords($request->title);
            if($request->filepath != null){
                $link->banner = $request->filepath;
            }
            $link->description = $request->desc;
            $link->active_from = date("Y-m-d", strtotime($request->open_date));
            $link->active_until = date("Y-m-d", strtotime($request->close_date));
            $link->created_by = auth()->id();
            $link->save();

            $this->saveEmailTemplate($link, $request->email_confirmation, 'confirmation');
            $this->saveEmailTemplate($link, $request->email_confirmed, 'confirmed');

            return redirect()->route('admin.link.view')->with([
                'stored' => true
            ]);

        } catch (\Throwable $th) {
            // return $th;
            abort(500);
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
        return view('admin.pages.links.member_info', ['id' => $id, 'title' => $link->title]);
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
        return view('admin.pages.links.edit', ['link_detail' => $edit_link]);
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
            $link = Link::findorfail($id);
            $link->title = $request->title;
            if($request->filepath != null){
                $link->banner = $request->filepath;
            }
            $link->description = $request->desc;
            $link->active_from = date("Y-m-d", strtotime($request->open_date));
            $link->active_until = date("Y-m-d", strtotime($request->close_date));
            $link->save();
            
            $this->updateEmailTemplate($link, $request->email_confirmation, 'confirmation');
            $this->updateEmailTemplate($link, $request->email_confirmed, 'confirmed');

            return redirect()->route('admin.link.view')->with([
                'stored' => true
            ]);

        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function page(Request $request, $link)
    {
        $data = Link::where('link_path', $link)->first();
        $expired = true;
        $date = date("Y-m-d");
        if($data != null){
            if($date >= date("Y-m-d", strtotime($data->active_from)) && $date <= date("Y-m-d", strtotime($data->active_until)) ){
                $expired = false;
            }
        }
        return view('pages.pendaftaran.view', ['link' => $data, 'show'=> $expired]);
    }


    public function dtb_memberLink($id)
    {
        $link = Link::find($id);
        $data = $link->members;
        $edit ='';
        return DataTables::of($data)
        ->addColumn("status", function($data) {
            $date = date("Y-m-d");
            if($data->invoices->status == 0 ){
                return '<div class="mb-2 mr-2 badge badge-danger">'.Invoice::INVO_STATUS[0].'</div>';
            }
            if($data->invoices->status == 1){
                return '<div class="mb-2 mr-2 badge badge-warning">'.Invoice::INVO_STATUS[1].'</div>';
            }
            if($data->invoices->status == 2){
                return '<div class="mb-2 mr-2 badge badge-success">'.Invoice::INVO_STATUS[2].'</div>';
            }
            
        })
        ->addColumn("options", function($data) {
            $edit = "<a href=\"".route('admin.link.edit', ['id' => $data->id])."\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-info\" style=\"margin-right:0.2rem;\">
                  <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                      <i class=\"pe-7s-rocket fa-w-20\"></i>
                  </span>
                  Detail
                </a>";
            return $edit;
        })
        ->rawColumns(['status', 'options'])
        ->make(true);
    }

    public function dtb_link(User $user)
    {
        $id = auth()->id();
        $data = $this->IncludeLink($user, $id);
        // $data = Link::withCount('members')->get();
        $edit ='';
        return DataTables::of($data)
        ->editColumn("link_path", function($data){
            return route('form.link.view', ['link' => $data->link_path]);
        })
        ->editColumn("members_count", function($data) {
            return $data->members_count.' Orang';
        })
        ->addColumn("status", function($data) {
            $date = date("Y-m-d");
            if($date >= date("Y-m-d", strtotime($data->active_from)) && $date <= date("Y-m-d", strtotime($data->active_until)) ){
                return '<div class="mb-2 mr-2 badge badge-success">Buka</div>';
            }else{
                return '<div class="mb-2 mr-2 badge badge-danger">Tutup</div>';
            }
            
        })
        ->addColumn("options", function($data) {
            $edit = "<a href=\"".route('admin.link.edit', ['id' => $data->id])."\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-warning\" style=\"margin-right:0.2rem;\">
                  <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                      <i class=\"pe-7s-rocket fa-w-20\"></i>
                  </span>
                  Edit
                </a>";
            $edit .= "<a href=\"".route('admin.link.detail', ['id' => $data->id])."\" aria-expanded=\"false\" class=\"mb-2 mr-2 badge badge-pill badge-info\" style=\"margin-right:0.2rem;\">
                <span class=\"btn-icon-wrapper pr-2 opacity-7\">
                    <i class=\"pe-7s-rocket fa-w-20\"></i>
                </span>
                List
              </a>";
            return $edit;
        })
        ->rawColumns(['link_path','members_count','status', 'options'])
        ->make(true);
    }

    private function getToken($length_token = 5)
    {
        $fix_token = '';
        $lock = 0;
        $data_token = Link::select('link_path')->get();
        if(count($data_token) > 0){
            $loop = count($data_token);
            for ($i=0; $i < $loop;) {
                foreach ($data_token as $tok) {
                    $temp = $this->generate_token($length_token);
                    if ($tok->link_path != $temp) {
                    $lock ++;
                    }else{
                    $lock = 0;
                    }
                }
                if ($loop == $lock) {
                    $fix_token = $temp;
                    $i = $loop;
                }else {
                    $i++;
                }
            }
            return $fix_token;
        }else{
            return $this->generate_token($length_token);
        }
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
}
