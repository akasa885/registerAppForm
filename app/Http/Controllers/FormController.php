<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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

    public function storeIdentity(StoreFormUserRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $link_coll = Link::where('link_path', $validated['link'])->first();
            $current_member = $link_coll->members;
            if(!$check_avail = $this->AvailableMemberOnEvent($current_member, $validated['email'])){
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
                    return back()->with('success', 'Pendaftaran berhasil dilakukan. Silahkan Cek Email Anda untuk informasi event, terima kasih !');
                else
                    return back()->with('success', 'Registration has been successfully done. Please check your email for event information, thank you!');
            }
            if($link_coll->link_type == 'pay'){
                if ($link_coll->is_multiple_registrant_allowed) {
                    Session::put('member_parent', $member);
                    return $this->pageMultiRegistrant($member, $link_coll);
                }
                // $dt_carbon = Carbon::now()->addDays(3);
                $invoice = new Invoice;
                $invoice->member_id = $member->id;
                $invoice->token = $this->getToken(Member::PAYMENT_TOKEN_LENGTH);
                $currentDateTime = Carbon::now();
                $newDateTime = Carbon::now()->addHours(24);
                $invoice->valid_until = $newDateTime;
                $invoice->status = 0;
                $invoice->save();

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

            $invoice = new Invoice;
            $invoice->member_id = $parent_member->id;
            $invoice->token = $this->getToken(Member::PAYMENT_TOKEN_LENGTH);
            $currentDateTime = Carbon::now();
            $newDateTime = Carbon::now()->addHours(24);
            $invoice->valid_until = $newDateTime;
            $invoice->status = 0;
            $invoice->save();

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

        $dataReturn = [
            'expired' => $expired,
            'used' => $used,
            'not_found' => $not_found];

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

                return view('pages.pendaftaran.upPay', $dataReturn);
            }
            if($date <= date("Y-m-d H:i:s", strtotime($pay_detail->valid_until)) ){
                if($link == $link_detail->link_path){
                    $dataReturn['link'] = $link_detail;
                    $dataReturn['member'] = $member;
                    $dataReturn['pay_code'] = $payment;
                    $dataReturn['used'] = $used;
                    $dataReturn['expired'] = $expired;

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

                return view('pages.pendaftaran.upPay', $dataReturn);
            }
        }else{
            $route_form = route('form.link.view', ['link' => $link]);
            $not_found = true;
            $dataReturn['route_form'] = $route_form;
            $dataReturn['not_found'] = $not_found;

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
                        return back()->with('success', 'Bukti berhasil di upload, silahkan tunggu untuk verifikasinya. Terima Kasih..!!!');
                    else
                        return back()->with('success', 'Proof of payment has been uploaded, please wait for verification. Thank you..!!!');
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

    private function getToken($lenght_token = 10)
    {
        $fix_token = '';
        $lock = 0;
        $data_token = Invoice::select('token')->get();
        if(count($data_token) > 0){
            $loop = count($data_token);
            for ($i=0; $i < $loop;) {
                foreach ($data_token as $tok) {
                    $temp = $this->generate_token($lenght_token);
                    if ($tok->token != $temp) {
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
            return $this->generate_token($lenght_token);
        }
    }

    public function generate_token($length = 10)
    {
      $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
      }

      return $randomString;
    }

    public function sendMailEventDeskripsi($link, $member){
        $information = $link->registration_info ?? $link->description;
        $data = array(
            'name'      =>  $member->full_name,
            'acara'     => $link->title,
            'message'   =>   $link->registration_info ?? $link->description,
        );
        $subject = 'Registrasi '.$link->title;
        $from_mail = Email::EMAIL_FROM;

        try {
            Mail::to($member->email)->send(new EventInfo($data, $from_mail, $subject));
            $mail_db = new Email;
            $mail_db->send_from = $from_mail;
            $mail_db->send_to = $member->email;
            $mail_db->message = $information;
            $mail_db->user_id = $member->id;
            $mail_db->type_email = Email::TYPE_EMAIL[3];
            $mail_db->sent_count = 1;
            $mail_db->save();
        } catch (\Throwable $th) {
            throw $th;
            // abort(500);
        }
    }
}
