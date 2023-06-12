<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// request
use App\Http\Requests\StoreFormUserRequest;

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

class FormController extends Controller
{
    use FileUploadTrait;
    use FormRegistrationTrait;

    public function storeIdentity(StoreFormUserRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $link_coll = Link::where('link_path', $validated['link'])->first();
            $current_member = $link_coll->members;
            if(!$check_avail = $this->AvailableMemberOnEvent($current_member, $validated['email'])){
                return back()
                ->withInput($request->all())
                ->withErrors(['email' => 'Email yang anda masukkan sudah terdaftar dalam event ini!']);
            }
            if($link_coll->has_member_limit){
                if($link_coll->link_type == 'free'){
                    if(!$check_quota = $this->isRegistrationMemberQuota($current_member, $link_coll->member_limit)){
                        quotaFullMessage:
                        return back()
                        ->withErrors(['message' => 'Maaf, Quota pendaftaran sudah penuh!']);
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

                return back()->with('success', 'Pendaftaran berhasil dilakukan. Terima kasih telah mendaftar');
            }
            if($link_coll->link_type == 'pay'){
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
            if (config('app.debug')) throw $th;
            DB::rollback();
            Log::error('Failed, run form storeIdentity');
            Log::error("error : ". $th->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan, silahkan coba beberapa saat lagi']);
        }
    }

    public function paymentUp($link, $payment)
    {
        $pay_detail = Invoice::where('token', $payment)->first();
        $expired = false;
        $used = false;
        if($pay_detail != null){
            $member = $pay_detail->member;
            $link_detail = Link::find($member->link_id);
            $date = date("Y-m-d");
            if($pay_detail->status != 0){
                $used = true;
                return view('pages.pendaftaran.upPay', 
                ['pay_code' => $payment, 
                'member' => $member,
                'link' => $link_detail,
                'expire' => $expired,
                'used' => $used]);
            }
            if($date <= date("Y-m-d", strtotime($pay_detail->valid_until)) ){
                if($link == $link_detail->link_path){
                    return view('pages.pendaftaran.upPay', 
                    ['pay_code' => $payment,
                    'member' => $member,
                    'link' => $link_detail,
                    'expire' => $expired,
                    'used' => $used]);
                }else{
                    abort(404);
                }
            }else{
                $expired = true;
                return view('pages.pendaftaran.upPay', 
                ['pay_code' => $payment,
                'member' => $member,
                'link' => $link_detail,
                'expire' => $expired,
                'used' => $used]);
            }
        }else{
            abort(404, 'Confirmation Code Not Found!');
        }
    }

    public function payStore(Request $request, $payment)
    {
        $this->validate($request, [
            'bukti' => ['required', 'image', 'max:2048']
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

                    return back()->with('success', 'Bukti berhasil di upload, silahkan tunggu untuk verifikasinya. Terima Kasih..!!!');
                }
            }else{
                abort(404);
            }
        } catch (\Throwable $th) {
            if (config('app.debug')) throw $th;
            DB::rollback();
            Log::error('Failed, run payStore');
            Log::error("error : ". $th->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan, silahkan coba beberapa saat lagi']);
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

    public function sendMailPayment($link, $member, $invoice){
        foreach($link->mails as $item){
            if($item->type == 'confirmation'){
                $information = $item->information;
            }
        }
        $data = array(
            'name'      =>  $member->full_name,
            'acara'     => $link->title,
            'message'   =>   $information,
            'valid_until' => $invoice->valid_until,
            'link_pay'  => route('form.link.pay', ['link' => $link->link_path, 'payment' => $invoice->token])
        );
        $from_mail = Email::EMAIL_FROM;

        try {
            Mail::to($member->email)->send(new ConfirmPay($data, $from_mail));
            $mail_db = new Email;
            $mail_db->send_from = $from_mail;
            $mail_db->send_to = $member->email;
            $mail_db->message = $information;
            $mail_db->user_id = $member->id;
            $mail_db->type_email = Email::TYPE_EMAIL[0];
            $mail_db->sent_count = 1;
            $mail_db->save();
        } catch (\Throwable $th) {
            abort(500);
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
