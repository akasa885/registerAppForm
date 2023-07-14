<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmationAttendances extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $from_mail;
    public $subject;
    public $urlComfirmation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $from, $subject = null)
    {
        $this->data = $data;
        $this->from_mail = $from;
        $subject ? $this->subject = $subject : $this->setSubject();
        $this->urlComfirmation = $this->setUrlConfirmation($data);
    }

    public function setSubject()
    {
        $this->subject = __('mail.subject.attendance_confirmation');
    }

    public function setUrlConfirmation($data)
    {
        $url = [];
        $url['main'] = "https://docs.google.com/forms/d/e/1FAIpQLScgiwaeUjxmZRwTyIBqzaKwdqmcRlWhYHXZxtuVBKh0sqhzzg/formResponse?";
        $url['param_1'] = "usp=pp_url";
        $url['param_2'] = "entry.131830679=".$data['email'];
        $url['param_3'] = "entry.1581148922=".$data['name'];
        $url['param_4'] = "entry.1336003890=Sudah+mendaftarkan+diri+pada+eform+yang+terdapat+di+https://s.id/upquality-RME";
        $url['param_5'] = "entry.573094141=Sudah+melakukan+presensi+pada+formulir+yang+terdapat+di+https://eform.upquality.net/form/2OWya";
        $url['param_6'] = "entry.306093679=Sungguh-sungguh+mengikuti+Webinar+Rekam+Medis+Elektronik+dengan+topik+++Implementasi+Rekam+Medis+Elektronik:+Terpaksa+atau+Menggapai+Peluang?+pada+tanggal+15+Juli+2023";
        $url['param_7'] = "submit=Submit";

        $urlFinal = $url['main'].$url['param_1']."&".$url['param_2']."&".$url['param_3']."&".$url['param_4']."&".$url['param_5']."&".$url['param_6']."&".$url['param_7'];

        return $urlFinal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->from($this->from_mail, "Event Organizer Upquality")
        ->subject($this->subject)
        ->view('mails.confirmation_attendance')
        ->with('data', $this->data);;
    }
}
