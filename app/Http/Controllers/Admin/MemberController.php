<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

//mail
use App\Mail\ConfirmedPay;

use App\Models\Member;
use App\Models\Invoice;
use App\Models\Email;

use Carbon\Carbon;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Member $member)
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

    public function viewSheet($id)
    {
        $data = Member::findorfail($id);
        if ($data->bukti_bayar == null) {
            return response()->json(['success' => false, 'message' => 'Bukti tidak ada!'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Bukti ditemukan', 'bukti' => asset('storage/bukti_image').'/'.$data->bukti_bayar, 'memberId' => $data->id], 200);
    }

    public function updateBukti(Request $request, Member $member)
    {
        try {
            $data = $member->findorfail($request->id);
            $invoice = Invoice::where('member_id', $data->id)->first();
            $invoice->status = 2;

            $this->sendMailPaymentReceived($data->link, $data);
            $invoice->save();

            return response()->json(['success' => true, 'message' => 'Pembayaran Diterima']);
        } catch (\Throwable $th) {
            // throw $th;
            // return $data->id;
            // return $member->link;
            return response()->json(['success' => false, 'message' => 'Request Error'], 500);
        }
    }

    public function sendMailPaymentReceived($link, $member){
        foreach($link->mails as $item){
            if($item->type == 'confirmed'){
                $information = $item->information;
            }
        }
        $data = array(
            'name'      =>  $member->full_name,
            'acara'     => $link->title,
            'message'   =>   $information,
        );
        $from_mail = Email::EMAIL_FROM;

        try {
            Mail::to($member->email)->send(new ConfirmedPay($data, $from_mail));
            $mail_db = new Email;
            $mail_db->send_from = $from_mail;
            $mail_db->send_to = $member->email;
            $mail_db->message = $information;
            $mail_db->user_id = $member->id;
            $mail_db->type_email = Email::TYPE_EMAIL[2];
            $mail_db->sent_count = 1;
            $mail_db->save();
        } catch (\Throwable $th) {
            abort(500);
        }
    }
}
