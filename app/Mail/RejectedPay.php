<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RejectedPay extends Mailable
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
            $this->subject = 'Bukti Pembayaran Anda Ditolak';
        }

        if (config('app.locale') == 'en') {
            $this->subject = 'Your Payment Proof is Rejected';
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
        ->subject('Bukti Pembayaran Anda Ditolak')
        ->view('mails.bukti_bayar')
        ->with('data', $this->data);
    }
}
