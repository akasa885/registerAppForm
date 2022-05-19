<?php
namespace App\Http\Traits;

use App\Models\MailPayment;

trait MailPaymentTrait {

    public function saveEmailTemplate($link, $email, $type)
    {
        $mail_tmp = new MailPayment;
        $mail_tmp->link_id = $link->id;
        $mail_tmp->information = $email;
        $mail_tmp->type = $type;
        $mail_tmp->save();
    }

    public function updateEmailTemplate($link, $email, $type)
    {
        $mail_tmp = MailPayment::where('link_id', $link->id)->where('type', $type)->first();
        $mail_tmp->information = $email;
        $mail_tmp->save();
    }
}