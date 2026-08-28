<?php

namespace App\Jobs;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBatchEmailJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const BATCH_SIZE = 100;

    public $tries = 3;

    public $failOnTimeout = true;

    public $backoff = 300;

    public $timeout = 60;

    public $maxExceptions = 3;

    public $uniqueFor = 900;

    protected $registerMail = [
        'reminder_event_batch' => [
            'mailable' => 'App\\Mail\\ReminderEventBatch',
            'email_type' => 'reminder_event',
        ],
    ];

    protected $linkId;

    protected $members;

    protected $datamail;

    protected $type;

    protected $recipient;

    public function __construct($linkId, array $members, array $data, string $type)
    {
        if (!array_key_exists($type, $this->registerMail)) {
            throw new \Exception('Invalid type');
        }

        $this->linkId = $linkId;
        $this->members = $members;
        $this->datamail = $data;
        $this->type = $type;
        $this->recipient = $type . '_' . $linkId . '_' . md5(json_encode($members));
    }

    public function uniqueId()
    {
        return $this->recipient;
    }

    public function handle()
    {
        if (count($this->members) === 0) {
            Log::channel('job')->warning('Send batch email skipped because recipient list is empty', [
                'type' => $this->type,
                'link_id' => $this->linkId,
            ]);

            return;
        }

        $mailConfig = $this->registerMail[$this->type];
        $mail = $mailConfig['mailable'];
        $emailType = $mailConfig['email_type'];
        $from = Email::EMAIL_FROM;
        $emails = array_values(array_filter(array_map(function ($member) {
            return $member['email'] ?? null;
        }, $this->members)));

        if (count($emails) === 0) {
            Log::channel('job')->warning('Send batch email skipped because no valid emails were found', [
                'type' => $this->type,
                'link_id' => $this->linkId,
            ]);

            return;
        }

        try {
            DB::beginTransaction();

            Mail::send(new $mail(
                emails: $emails,
                data: $this->datamail,
                from_mail: $from,
                subject: $this->datamail['subject'] ?? null
            ));

            foreach ($this->members as $member) {
                if (empty($member['id']) || empty($member['email'])) {
                    continue;
                }

                $emailDb = new Email();
                $emailDb->send_from = $from;
                $emailDb->send_to = $member['email'];
                $emailDb->message = $this->datamail['message'] ?? null;
                $emailDb->user_id = $member['id'];
                $emailDb->type_email = $emailType;
                $emailDb->sent_count = 1;
                $emailDb->save();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::channel('job')->error('Save batch email failed', [
                'message' => $th->getMessage(),
                'type' => $this->type,
                'link_id' => $this->linkId,
                'recipient_count' => count($this->members),
            ]);
            Log::channel('job')->error($th);
            $this->fail($th);
        }
    }

    public function retryUntil()
    {
        return now()->addSeconds(($this->tries * $this->backoff) + $this->timeout);
    }

    public function failed(\Throwable $exception)
    {
        Log::channel('job')->error('Send batch email failed', [
            'message' => $exception->getMessage(),
            'type' => $this->type,
            'link_id' => $this->linkId,
            'recipient_count' => count($this->members),
        ]);
    }

    public function tags()
    {
        return ['email', 'batch-email', $this->type];
    }

    public static function sendMail($dataMail, $link, array $members, $type)
    {
        foreach (array_chunk($members, self::BATCH_SIZE) as $memberChunk) {
            $mail = new SendBatchEmailJob($link->id, $memberChunk, $dataMail, $type);
            $mail->onQueue('emails');
            dispatch($mail);
        }
    }

    public function displayName()
    {
        return 'Send Batch Email : [' . $this->type . '] for link ' . $this->linkId . ' to ' . count($this->members) . ' recipients';
    }
}
