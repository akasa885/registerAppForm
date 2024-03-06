<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */

    public $tries = 3;

    public $failOnTimeout = true;

    protected $registerMail = [
        'attendance_confirmation' => 'App\Mail\ConfirmationAttendances',
        'event_info' => 'App\Mail\EventInfo',
        'confirmation_pay' => 'App\Mail\ConfirmPay',
    ];

    protected $link;

    protected $attendance;

    protected $member;

    protected $datamail;

    protected $type;

    public function __construct($link, $member, $data, $type, $attendance = null)
    {
        $this->attendance = $attendance;
        $this->datamail = $data;
        $this->link = $link;
        $this->member = $member;
        // type must be one of the keys of $registerMail
        if (array_key_exists($type, $this->registerMail)) {
            $this->type = $type;
        } else {
            throw new \Exception('Invalid type');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = $this->registerMail[$this->type];
        $from = Email::EMAIL_FROM;

        Mail::to($this->member->email)->send(new $mail($this->datamail, $from, $this->datamail['subject'] ?? null));

        try {
            $emailDb = new Email();
            $emailDb->send_from = $from;
            $emailDb->send_to = $this->member->email;
            $emailDb->message = $this->datamail['message'];
            $emailDb->user_id = $this->member->id;
            $emailDb->type_email = $this->type;
            $emailDb->sent_count = 1;
            $emailDb->save();
        } catch (\Throwable $th) {
            Log::channel('job')->error('Save email failed', [
                'message' => $th->getMessage(),
                'type' => $this->type,
                'email' => $this->member->email,
            ]);
        }

    }

    public function retryUntil()
    {
        return now()->addSeconds(5);
    }

    public function failed(\Exception $exception)
    {
        Log::channel('job')->error('Send email failed', [
            'message' => $exception->getMessage(),
            'type' => $this->type,
            'email' => $this->member->email,
        ]);
    }

    public static function sendMail($dataMail, $link, $member, $type, $attendance = null)
    {
        $mail = new SendEmailJob($link, $member, $dataMail, $type, $attendance);
        $mail->onQueue('emails');
        dispatch($mail);
    }
}
