<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmedPay extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $from_mail;
    public $subject;

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
    }

    public function setSubject()
    {
        if (config('app.locale') == 'id') {
            $this->subject = 'Terima Kasih Atas Pembayaran Anda';
        }

        if (config('app.locale') == 'en') {
            $this->subject = 'Thank You For Your Payment';
        }
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
        ->view('mails.penerimaan_bayar')
        ->with('data', $this->data);
    }
}
