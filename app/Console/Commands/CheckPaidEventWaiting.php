<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Link;
use App\Models\Member;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\MemberTrash;

class CheckPaidEventWaiting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:waiting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check paid event waiting for 1 day and change status to expired';

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
            Log::info('Event waiting start to check');
            $this->info('Event waiting start to check');

            $links = $this->currentlyOpenPaidEvent();
            $membersOfLinks = $links->map(function ($link) {
                return $link->members;
            });
            $membersOfLinks = $membersOfLinks->flatten();
            $membersOfLinksUnpaid = $membersOfLinks->filter(function ($member) {
                return $member->invoices->status == 0;
            });

            $membersUnpaidExpired = $membersOfLinksUnpaid->filter(function ($member) {
                return $member->invoices->valid_until < Carbon::now();
            });

            DB::beginTransaction();
            $this->deleteMemberUnpaidExpired($membersUnpaidExpired);
            DB::commit();

            $this->info('Event waiting has been checked successfully');
            Log::info('Event waiting has been checked successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Event waiting has been checked failed');
            Log::error($th->getMessage());
            $this->error('Event waiting has been checked failed');
            throw $th;
        }
    }

    private function currentlyOpenPaidEvent()
    {
        $today = Carbon::now();
        $todayDate = $today->toDateString();
        // where link_type = pay and today is between start_date and end_date
        $links = Link::where('link_type', 'pay')
            ->whereDate('active_from', '<=', $todayDate)
            ->whereDate('active_until', '>=', $todayDate)
            ->get();

        return $links;
    }



    private function deleteMemberUnpaidExpired($members)
    {
        foreach ($members as $member) {
            $trash = $member->toArray();
            $trash['deleted_time'] = Carbon::now();
            $trash['link_id'] = $member->link_id;
            $trash = MemberTrash::create($trash);

            $member->delete();
        }
    }
}
