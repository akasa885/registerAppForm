<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

//rule
use App\Rules\FullnameRule;

//mail
use App\Mail\ConfirmPay;

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

    public function storeIdentity(Request $request)
    {
        $this->validate($request, [
            'fullname' => ['required', new FullnameRule()],
            'email' => ['required', 'email'],
            'no_telpon' => ['numeric', 'digits_between:8,13'],
            'instansi' => ['required']
        ]);

        try {
            $link_coll = Link::where('link_path', $request->link)->first();
            $current_member = $link_coll->members;
            if(!$check_avail = $this->AvailableMemberOnEvent($current_member, $request->email)){
                return back()
                ->withInput($request->all())
                ->withErrors(['email' => 'Email yang anda masukkan sudah terdaftar dalam event ini!']);
            }
            $member = new Member;
            $member->link_id = $link_coll->id;
            $member->full_name = $request->fullname;
            $member->email = $request->email;
            $member->contact_number = $request->no_telpon;
            $member->corporation = $request->instansi;
            $member->save();

            // $dt_carbon = Carbon::now()->addDays(3);
            $invoice = new Invoice;
            $invoice->member_id = $member->id;
            $invoice->token = $this->getToken();
            // $invoice->valid_until = date("Y-m-d", strtotime($dt_carbon->toDateString()));
            $invoice->valid_until = date("Y-m-d", strtotime($link_coll->active_until));
            $invoice->status = 0;
            $invoice->save();

            $this->sendMailPayment($link_coll, $member, $invoice);

            return redirect()->route('form.link.pay', ['link' => $link_coll->link_path, 'payment' => $invoice->token]);

        } catch (\Throwable $th) {
            // throw $th;
            abort(500);
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
            abort(404);
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
                    return back()->with('success', 'Bukti berhasil di upload, silahkan tunggu untuk verifikasinya. Terima Kasih..!!!');
                }
            }else{
                abort(404);
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }

    private function getToken()
    {
        $fix_token = '';
        $lock = 0;
        $data_token = Invoice::select('token')->get();
        if(count($data_token) > 0){
            $loop = count($data_token);
            for ($i=0; $i < $loop;) {
                foreach ($data_token as $tok) {
                    $temp = $this->generate_token();
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
            return $this->generate_token();
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
}
