<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Member;
use App\Jobs\SendEmailJob;
use App\Http\Traits\MailPaymentTrait;

class MemberObserver
{
    use MailPaymentTrait;

    /**
     * Handle the Member "created" event.
     *
     * @param  \App\Models\Member  $member
     * @return void
     */
    public function created(Member $member)
    {
        $link = $member->link;
        $isFree = $link->link_type === 'free';

        if ($isFree) {
            $data = array(
                'name'      =>  $member->full_name,
                'acara'     => $link->title,
                'message'   =>   $link->registration_info ?? $link->description,
            );
            $data['subject'] = "Pendaftaran Berhasil: " . $link->title;

            SendEmailJob::sendMail(dataMail: $data, link: $link, member: $member, type: 'event_info');
        }
        // For paid links, email is handled in controller after invoice creation
    }

    /**
     * Handle the Member "updated" event.
     *
     * @param  \App\Models\Member  $member
     * @return void
     */
    public function updated(Member $member)
    {
        //
    }

    /**
     * Handle the Member "deleted" event.
     *
     * @param  \App\Models\Member  $member
     * @return void
     */
    public function deleted(Member $member)
    {
        //
    }

    /**
     * Handle the Member "restored" event.
     *
     * @param  \App\Models\Member  $member
     * @return void
     */
    public function restored(Member $member)
    {
        //
    }

    /**
     * Handle the Member "force deleted" event.
     *
     * @param  \App\Models\Member  $member
     * @return void
     */
    public function forceDeleted(Member $member)
    {
        //
    }
}
