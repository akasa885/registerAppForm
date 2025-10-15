<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventInfo extends Mailable
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
        $this->subject = __('mail.subject.event_info');
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
                ->addTextHeader('X-Category', 'EventInfo');
        })
        ->view('mails.event_info')
        ->with('data', $this->data);
    }
}
