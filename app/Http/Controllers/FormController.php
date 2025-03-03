<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Jobs\SendEmailJob;

use App\Services\Midtrans\CreateSnapTokenService;
// request
use App\Http\Requests\StoreFormUserRequest;
use App\Http\Requests\MultiRegistrantRequest;

//mail
use App\Mail\ConfirmPay;
use App\Mail\EventInfo;

use App\Models\Member;
use App\Models\Invoice;
use App\Models\Link;
use App\Models\MailPayment;
use App\Models\Email;

use App\Http\Traits\FileUploadTrait;
use App\Http\Traits\FormRegistrationTrait;
use App\Http\Traits\MailPaymentTrait;

class FormController extends Controller
{
    use FileUploadTrait, FormRegistrationTrait, MailPaymentTrait;

    private function getTimeLimit($limittime)
    {
        $currentDateTime = Carbon::now();
        // get the time left, current time to limitTime. format 00:00:00
        $timeLeft = $currentDateTime->diff($limittime)->format('%H:%I:%S');

        return $timeLeft;
    }

    public function storeIdentity(StoreFormUserRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $link_coll = Link::where('link_path', $validated['link'])->first();
            $current_member = $link_coll->members;
            if($check_avail = $this->AvailableMemberOnEvent($current_member, $validated['email'], $link_coll)){
                if (config('app.locale') == 'id') {
                    return back()
                    ->withInput($request->all())
                    ->withErrors(['email' => 'Email yang anda masukkan sudah terdaftar dalam event ini!']);
                } else {
                    return back()
                    ->withInput($request->all())
                    ->withErrors(['email' => 'The email you entered is already registered for this event!']);
                }
            }

            if ($link_coll->link_type == 'pay' && $member = Member::where('email', $validated['email'])->where('link_id', $link_coll->id)->first()) {
                if ($member->invoices->status == 0 && $member->invoices->valid_until > Carbon::now()) {
                    return redirect()->route('form.link.pay', 
                    ['link' => $link_coll->link_path, 'payment' => $member->invoices->token]
                    )->with('info', 'Anda sudah melakukan pendaftaran sebelumnya, silahkan lanjutkan pembayaran');
                }
            }

            if($link_coll->has_member_limit){
                if($link_coll->link_type == 'free'){
                    if(!$check_quota = $this->isRegistrationMemberQuota($current_member, $link_coll->member_limit)){
                        quotaFullMessage:
                        if (config('app.locale') == 'id') {
                            return back()
                            ->withErrors(['message' => 'Maaf, Quota pendaftaran sudah penuh!']);
                        } else {
                            return back()
                            ->withErrors(['message' => 'Sorry, the registration quota is full!']);
                        }
                    }
                } else {
                    if(!$check_quota = $this->isRegistrationPaidMemberQuota($current_member, $link_coll->member_limit)){
                        goto quotaFullMessage;
                    }
                }
            }
            $validated['link_id'] = $link_coll->id;
            $member = Member::create($validated);

            if ($link_coll->link_type == 'free') {
                $this->sendMailEventDeskripsi($link_coll, $member);

                DB::commit();

                if (config('app.locale') == 'id')
                    return back()->with('success', 'Pendaftaran berhasil dilakukan. Silahkan Cek Email Anda (inbox/spam) untuk informasi event, terima kasih !');
                else
                    return back()->with('success', 'Registration has been successfully done. Please check your email (inbox/spam) for event information, thank you !');
            }
            if($link_coll->link_type == 'pay'){
                if ($link_coll->is_multiple_registrant_allowed) {
                    Session::put('member_parent', $member);
                    return $this->pageMultiRegistrant($member, $link_coll);
                }
                // $dt_carbon = Carbon::now()->addDays(3);
                $invoice = $this->createInvoice($member, $link_coll);

                $this->sendMailPayment($link_coll, $member, $invoice);

                DB::commit();

                return redirect()->route('form.link.pay', ['link' => $link_coll->link_path, 'payment' => $invoice->token]);
            }

        } catch (\Throwable $th) {
            DB::rollback();
            if (config('app.debug')) throw $th;
            Log::error('Failed, run form storeIdentity');
            Log::error("error : ". $th->getMessage());
            if (config('app.locale') == 'id')
                return back()->withErrors(['message' => 'Terjadi kesalahan, silahkan coba beberapa saat lagi']);
            else
                return back()->withErrors(['message' => 'An error occurred, please try again later']);
        }
    }

    public function pageMultiRegistrant(Member $member, Link $link) 
    {
        $data = [
            'member' => $member,
            'link' => $link
        ];

        return view('pages.pendaftaran.view-multi-regist', $data);
    }

    public function storeMultiRegistrant(MultiRegistrantRequest $request)
    {
        try {
            $validated = $request->validated();
            DB::beginTransaction();
            $parent_member = Session::get('member_parent');
            $link_coll = Link::where('link_path', $validated['link_path'])->first();
            // because parent_member is not stored in database yet, so we need to store it first
            $parent_member = Member::create($parent_member->toArray());
            $parent_member->subMembers()->createMany($validated['sub_members']);

            $invoice = $this->createInvoice($parent_member, $link_coll);

            $this->sendMailPayment($link_coll, $parent_member, $invoice);

            Session::forget('member_parent');
            DB::commit();

            return redirect()->route('form.link.pay', ['link' => $link_coll->link_path, 'payment' => $invoice->token]);
        } catch (\Throwable $th) {
            DB::rollback();
            if (config('app.debug')) throw $th;
            Log::error('Failed, run form storeMultiRegistrant');
            Log::error("error : ". $th->getMessage());
            if (config('app.locale') == 'id')
                return redirect()->route('form.link.view', ['link' => $validated['link_path']])->withErrors(['message' => 'Terjadi kesalahan, silahkan coba beberapa saat lagi']);
            else
                return redirect()->route('form.link.view', ['link' => $validated['link_path']])->withErrors(['message' => 'An error occurred, please try again later']);

        }
    }

    public function paymentUp($link, $payment)
    {
        $pay_detail = Invoice::where('token', $payment)->first();
        $expired = false;
        $used = false;
        $not_found = false;
        $timeLeft = null;

        if ($pay_detail) {
            $timeLeft = $this->getTimeLimit($pay_detail->valid_until);
        }

        $dataReturn = [
            'expired' => $expired,
            'used' => $used,
            'not_found' => $not_found,
            'timeLeft' => $timeLeft
        ];

        if($pay_detail != null){
            $member = $pay_detail->member;
            $link_detail = Link::find($member->link_id);
            $date = date("Y-m-d H:i:s");
            if($pay_detail->status != 0){
                $used = true;
                $dataReturn['link'] = $link_detail;
                $dataReturn['member'] = $member;
                $dataReturn['pay_code'] = $payment;
                $dataReturn['used'] = $used;
                $dataReturn['expired'] = $expired;

                // will show if payment is used
                return view('pages.pendaftaran.upPay', $dataReturn);
            }
            if($date <= date("Y-m-d H:i:s", strtotime($pay_detail->valid_until)) ){
                if($link == $link_detail->link_path){
                    $dataReturn['link'] = $link_detail;
                    $dataReturn['member'] = $member;
                    $dataReturn['pay_code'] = $payment;
                    $dataReturn['used'] = $used;
                    $dataReturn['expired'] = $expired;

                    if (config('app.version') && config('app.version') == '1.1.0' && Invoice::PAYMENT_TYPE == 'multipayment' && $link_detail->method_pay == 'multipayment') {
                        $this->checkSnapToken($pay_detail);
                        if ($pay_detail->order->snap_token_midtrans) {
                            $dataReturn['snap_token'] = $pay_detail->order->snap_token_midtrans;
                            $dataReturn['snap_redirect'] = $pay_detail->order->snap_redirect;
                        }
                    }
                    // will show if payment is not used, and not expired
                    return view('pages.pendaftaran.upPay', $dataReturn);
                }else{
                    abort(404);
                }
            }else{
                $expired = true;
                $member->delete();
                $dataReturn['link'] = $link_detail;
                $dataReturn['member'] = $member;
                $dataReturn['pay_code'] = $payment;
                $dataReturn['used'] = $used;
                $route_form = route('form.link.view', ['link' => $link]);
                $dataReturn['route_form'] = $route_form;
                $dataReturn['expired'] = $expired;
                if (config('app.locale') == 'id') {
                    $dataReturn['message'] = 'Maaf, waktu upload pembayaran sudah habis. Silahkan daftar ulang. (Pastikan Informasi Yang Anda Masukkan Benar)';
                } else {
                    $dataReturn['message'] = 'Sorry, the payment upload time has expired. Please register again. (Make sure the information you entered is correct)';
                }

                // will show if payment is not used, and expired
                return view('pages.pendaftaran.upPay', $dataReturn);
            }
        }else{
            $route_form = route('form.link.view', ['link' => $link]);
            $not_found = true;
            $dataReturn['route_form'] = $route_form;
            $dataReturn['not_found'] = $not_found;

            // will show if payment is not found
            return view ('pages.pendaftaran.upPay', $dataReturn);
        }
    }

    public function payStore(Request $request, $payment)
    {
        $this->validate($request, [
            'bukti' => ['required', 'image', 'max:10240']
        ]);
        try {
            $invo = Invoice::where('token', $payment)->first();
            if($invo != null){
                DB::beginTransaction();
                // request save file to server
                $filesimpan = $this->saveInvoice($request->file('bukti'));
                
                if($filesimpan){
                    // request save file to db
                    $invo->status = 1;
                    $invo->save();

                    if($invo->status == 1){
                        $member_pay = Member::findorfail($invo->member_id);
                        $member_pay->bukti_bayar = $filesimpan;
                        $member_pay->save();
                    }

                    DB::commit();

                    if (config('app.locale') == 'id')
                        return back()->with('success', 'Bukti berhasil di upload, silahkan tunggu untuk verifikasinya dan balasan email. Terima kasih..!!!');
                    else
                        return back()->with('success', 'Proof of payment has been uploaded, please wait for verification and email reply. Thank you..!!!');
                }
            }else{
                abort(404);
            }
        } catch (\Throwable $th) {
            if (config('app.debug')) throw $th;
            DB::rollback();
            Log::error('Failed, run payStore');
            Log::error("error : ". $th->getMessage());
            if (config('app.locale') == 'id')
                return back()->withErrors(['message' => 'Terjadi kesalahan, silahkan coba beberapa saat lagi']);
            else
                return back()->withErrors(['message' => 'An error occurred, please try again later']);
        }
    }

    public function sendMailEventDeskripsi($link, $member){
        $information = $link->registration_info ?? $link->description;
        $data = array(
            'name'      =>  $member->full_name,
            'acara'     => $link->title,
            'message'   =>   $link->registration_info ?? $link->description,
        );
        $subject = 'Registrasi '.$link->title;
        $data['subject'] = $subject;

        $from_mail = Email::EMAIL_FROM;

        try {
            // Mail::to($member->email)->send(new EventInfo($data, $from_mail, $subject));
            // $mail_db = new Email;
            // $mail_db->send_from = $from_mail;
            // $mail_db->send_to = $member->email;
            // $mail_db->message = $information;
            // $mail_db->user_id = $member->id;
            // $mail_db->type_email = Email::TYPE_EMAIL[3];
            // $mail_db->sent_count = 1;
            // $mail_db->save();
            
            SendEmailJob::sendMail(dataMail: $data, link: $link, member: $member, type: 'event_info');
        } catch (\Throwable $th) {
            throw $th;
            // abort(500);
        }
    }

    private function checkSnapToken($invoice)
    {
        try {
            $invoicedOrder = $invoice->order;
            $snapToken = $invoicedOrder->snap_token_midtrans;

            if (is_null($snapToken) && $invoice->status == 0 ) {
                $midtrans = new CreateSnapTokenService($invoicedOrder);
                $snapToken = $midtrans->getSnapTokenWithGopay();
                $snapUrl = $midtrans->getSnapUrl();

                $invoicedOrder->snap_token_midtrans = $snapToken;
                $invoicedOrder->snap_redirect = $snapUrl;
                $invoicedOrder->save();
            }
        }catch (\Throwable $th) {
            //nothing to do
            Log::error('Error on request show bill');
            report($th);
        }
    }
}
