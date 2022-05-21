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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $from)
    {
        $this->data = $data;
        $this->from_mail = $from;
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
        ->subject('Informasi Acara')
        ->view('mails.event_info')
        ->with('data', $this->data);
    }
}
