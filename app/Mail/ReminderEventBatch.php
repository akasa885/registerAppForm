<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderEventBatch extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public array $emails,
        public $data,
        public $from_mail,
        public $subject = null
    ) {
        $this->subject ? $this->subject = $subject : $this->setSubject();
    }

    public function setSubject() {
        $this->subject = __('mail.subject.reminder');
    }

    public function build()
    {
        return $this
        ->from($this->from_mail, "Event Organizer Upquality")
        ->subject($this->subject)
        ->view('mails.reminder_event_batch')
        ->with('data', $this->data)
        ->bcc($this->emails);
    }
}
