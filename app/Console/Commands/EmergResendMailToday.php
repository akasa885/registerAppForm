<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Email;
use Illuminate\Support\Facades\Mail;
use App\Models\MemberAttend;
use Carbon\Carbon;

class EmergResendMailToday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emgc:resend-mail-today {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend mail today for emergency case. This command will be run every 5 minutes to check if there is any emergency case and resend mail to the user. If the user has been sent mail today, the command will not send mail to the user.';

    private const mail_type = [
        'confirmation_pay' => 'App\Mail\ConfirmPay',
        'confirmed_pay' => 'App\Mail\ConfirmedPay',
        'attendance_confirmation' => 'App\Mail\ConfirmationAttendances',
        'event_info' => 'App\Mail\EventInfo',
        'reminder_event' => 'App\Mail\ReminderEvent',
    ];

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
        $this->info('Start Resend Email =============================== START');

        $today = Carbon::now()->format('Y-m-d');
        $type = $this->argument('type');
        $mail = self::mail_type[$type];

        $emails = Email::where('created_at', 'like', $today . '%')
            ->where('type_email', $type)
            ->get();
        
        try {
            if ($type == 'attendance_confirmation') {
                $data = $this->typeAttendancesConfirmation($today, $emails);
            } else {
                return $this->info('Resend Email run successfully, but the type is not found. Please check the type again.');
            }
    
            return $this->info('End Of Resend Email =============================== END');
        } catch (\Throwable $th) {
            $this->error('Error: ' . $th->getMessage());
            dd($th);
        }
    }

    public function typeAttendancesConfirmation($date, $emails)
    {
        $dataReturn = [];
        
        $attendances = MemberAttend::where('created_at', 'like', $date . '%')
            ->get();

        // mapping email and attendance, by attendance:member_id and email:user_id
        $emailMap = $emails->map(function ($email) {
            return intval($email->user_id);
        });

        $attendancesMap = $attendances->map(function ($attendance) {
            return intval($attendance->member_id);
        });

        $this->info('Email Map Total: ' . $emailMap->count());
        $this->info('Attendances Map Total: ' . $attendancesMap->count());

        if ($emailMap->count() == 0 || $attendancesMap->count() == 0) {
            $this->info('Resend Email run successfully, but the email or attendances is empty.');

            return [];
        }

        // get same value from emailMap and attendancesMap
        $interSecMemberId = $emailMap->intersect($attendancesMap);

        $this->info('Intersect Member Id Total: ' . $interSecMemberId->count());

        if ($interSecMemberId->count() == 0) {
            $this->info('Resend Email run successfully, but the intersect member id is empty.');

            return [];
        }

        $attends = MemberAttend::whereIn('member_id', $interSecMemberId)->with('member', 'attendance')->get();

        $attends->each(function ($attend) {
            $member = $attend->member;
            $attendance = $attend->attendance;
            $dataReturn = [
                'name' => $member->full_name,
                'email' => $member->email,
                'phone' => $member->contact_number,
                'event' => $attendance->link->title,
                'message' => $attendance->confirmation_mail,
                'link_path' => $attendance->link->link_path,
            ];

        });

        return $this->info('Resend Email run successfully');

        return $dataReturn;
    }
}