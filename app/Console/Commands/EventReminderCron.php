<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Link;
use App\Models\Member;
use App\Models\Email;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendBatchEmailJob;
use App\Jobs\SendEmailJob;

class EventReminderCron extends Command
{
    protected $sendedCount = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to send reminder to members for upcoming events 1 days before the event date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public $mailSleep = 0.5; // sleep time in seconds between sending emails to avoid spamming

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $links = $this->checkListEventBeforeOneDay();
            $this->info('Event listed successfully, total event: ' . count($links));
            foreach ($links as $link) {
                if ($link->link_type == 'pay') {
                    $this->getMemberOfPaidEvent($link);
                    Log::info('Sended Event Pay ID: '.$link->id.' Reminder Count: ' . $this->sendedCount["link_id_".$link->id]['pay']);
                } else {
                    $this->getMemberOfFreeEvent($link);
                    Log::info('Sended Event Free ID: '.$link->id.' Reminder Count: ' . $this->sendedCount["link_id_".$link->id]['free']);
                }
            }
            Log::info('Event reminder has been sent successfully');
            $this->info('Event reminder has been sent successfully');
        } catch (\Throwable $th) {
            $this->error('Event reminder failed to send');
            $this->error($th->getMessage());
            Log::error('Event reminder failed to send');
            Log::error($th->getMessage());
        }
    }

    private function checkListEventBeforeOneDay()
    {
        $today = Carbon::now();
        $links = Link::where('event_date', $today->addDay()->toDateString())->get();

        return $links;
    }

    private function getMemberOfPaidEvent(Link $link)
    {
        $this->sendedCount["link_id_".$link->id]['pay'] = 0;
        $members = $link->members()->get();
        $members->load(['invoices' => function ($query) {
            $query->lunas();
        }]);

        $batchMembers = [];

        foreach ($members as $member) {
            if ($this->doesMemberRegisteredSameDay($link, $member)) {
                continue;
            }
            if ($this->checkDoesMemberAlreadySentReminder($member, $link)) {
                continue;
            }
            if ($this->pendingJobAvailable($member, 'reminder_event')) {
                continue;
            }

            // $this->sentMailReminder($member, $link, 'pay');
            $batchMembers[] = $this->mapBatchRecipient($member);
        }

        if (count($batchMembers) > 0) {
            $this->sentBatchReminder($batchMembers, $link, 'pay');
        }
    }

    private function getMemberOfFreeEvent(Link $link)
    {
        $this->sendedCount["link_id_".$link->id]['free'] = 0;
        $members = $link->members()->get();
        $doesThisEventHide = $link->hide_events;

        $batchMembers = [];

        if ($doesThisEventHide) {
            $this->info('Event ID: '.$link->id.' is hidden, so the reminder will not be sent');
            return;
        }

        foreach ($members as $member) {
            if ($this->doesMemberRegisteredSameDay($link, $member, 'free')) {
                // continue;
            }
            if ($this->checkDoesMemberAlreadySentReminder($member, $link)) {
                continue;
            }
            if ($this->pendingJobAvailable($member, 'reminder_event')) {
                continue;
            }

            // $this->sentMailReminder($member, $link, 'free');
            $batchMembers[] = $this->mapBatchRecipient($member);
        }

        if (count($batchMembers) > 0) {
            $this->sentBatchReminder($batchMembers, $link, 'free');
        }
    }

    private function doesMemberRegisteredSameDay(Link $link, Member $member, $type = 'pay')
    {
        $date_reg = null;
        $today = Carbon::now(); // today date
        try {
            if ($type == 'pay') {
                // get updated_at from invoices
                if ($member->invoices) {
                    $date_reg = $member->invoices->updated_at;
                }
            } else {
                // get created_at from members
                $date_reg = $member->created_at;
            }

            if (!$date_reg) return true;

            if ($date_reg->toDateString() == $today->toDateString()) {
                return true;
            }

            return false;
        } catch (\Throwable $th) {
            Log::error('Error: doesMemberRegisteredSameDay');
            Log::error('Member id : error' . $member->id);
            throw $th;
        }
    }

    private function checkDoesMemberAlreadySentReminder(Member $member, Link $link)
    {
        $mails = Email::where('user_id', $member->id)->get();

        foreach ($mails as $mail) {
            if ($mail->type_email == 'reminder_event') {
                return true;
            }
        }

        return false;
    }

    private function pendingJobAvailable($member, $type)
    {
        $fullNameCheck = "%{$member->full_name}%";
        $memberEmailCheck = "%{$member->email}%";
        $job = DB::table('jobs')
            ->where('payload', 'like', "%{$type}%")
            ->where('payload', 'like', $fullNameCheck)
            ->where('payload', 'like', $memberEmailCheck);

        return $job->exists();
    }

    private function sentMailReminder(Member $member, Link $link, $type = 'pay')
    {
        $data = [
            'name' => $member->full_name,
            'acara' => $link->title,
            'event_date' => date('d-m-Y', strtotime($link->event_date)),
        ];

        $confirmedMail = $link->mails()->where('type', 'confirmed')->first();

        if ($type == 'pay') {
            $data['message'] = $confirmedMail?->information ?? $link->description;
        } else {
            $data['message'] = $link->registration_info ?? $link->description;
        }

        $from_mail = Email::EMAIL_FROM;

        // sleep to avoid spamming
        usleep($this->mailSleep * 1000000); // convert seconds to microseconds
        // total seconds = $this->mailSleep * 1000000 so, if $this->mailSleep = 0.5, then usleep(500000); and wait for 0.5 seconds

        SendEmailJob::sendMail(dataMail: $data, link: $link, member: $member, type: 'reminder_event');

        $this->sendedCount["link_id_".$link->id][$type] += 1;
    }

    private function sentBatchReminder(array $members, Link $link, $type = 'pay')
    {
        $data = [
            'acara' => $link->title,
            'event_date' => date('d-m-Y', strtotime($link->event_date)),
        ];

        $confirmedMail = $link->mails()->where('type', 'confirmed')->first();

        if ($type == 'pay') {
            $data['message'] = $confirmedMail?->information ?? $link->description;
        } else {
            $data['message'] = $link->registration_info ?? $link->description;
        }

        SendBatchEmailJob::sendMail(
            dataMail: $data,
            link: $link,
            members: $members,
            type: 'reminder_event_batch'
        );

        $this->sendedCount["link_id_".$link->id][$type] += count($members);
    }

    private function mapBatchRecipient(Member $member)
    {
        return [
            'id' => $member->id,
            'email' => $member->email,
            'full_name' => $member->full_name,
        ];
    }
}
