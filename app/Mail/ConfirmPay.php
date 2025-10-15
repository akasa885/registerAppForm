<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmPay extends Mailable
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
        $subject ? $this->subject = $subject : $this->setSubject($data['is_auto']);
    }

    public function setSubject($auto = false) {
        if ($auto) {
            $this->subject = __('mail.subject.confirm_pay_gateway');
            return;
        }

        $this->subject = __('mail.subject.confirm_pay');
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
        ->withSwiftMessage(function ($message) {
            $message->getHeaders()
                ->addTextHeader('X-Category', 'PayConfirmation');
        })
        ->view('mails.bukti_bayar')
        ->with('data', $this->data);
    }
}
