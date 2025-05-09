<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\ReminderEvent;
use App\Models\Link;
use App\Models\Member;
use App\Models\Email;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $links = $this->checkListEventBeforeOneDay();
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

            $this->sentMailReminder($member, $link, 'pay');
        }
    }

    private function getMemberOfFreeEvent(Link $link)
    {
        $this->sendedCount["link_id_".$link->id]['free'] = 0;
        $members = $link->members()->get();
        $doesThisEventHide = $link->hide_events;

        if ($doesThisEventHide) {
            $this->info('Event ID: '.$link->id.' is hidden, so the reminder will not be sent');
            return;
        }

        foreach ($members as $member) {
            if ($this->doesMemberRegisteredSameDay($link, $member, 'free')) {
                continue;
            }
            if ($this->checkDoesMemberAlreadySentReminder($member, $link)) {
                continue;
            }
            if ($this->pendingJobAvailable($member, 'reminder_event')) {
                continue;
            }

            $this->sentMailReminder($member, $link, 'free');
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
        $memberEmailCheck = "%reminder_event_{$member->email}%";
        $job = DB::table('jobs')
            ->where('payload', 'like', "%reminder_event%")
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

        if ($type == 'pay') {
            $data['message'] = $link->mails()->where('type', 'confirmed')->first()->information;
        } else {
            $data['message'] = $link->registration_info ?? $link->description;
        }

        $from_mail = Email::EMAIL_FROM;

        SendEmailJob::sendMail(dataMail: $data, link: $link, member: $member, type: 'reminder_event');

        $this->sendedCount["link_id_".$link->id][$type] += 1;
    }
}
