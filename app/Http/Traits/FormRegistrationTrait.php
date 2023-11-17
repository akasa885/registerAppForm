<?php

namespace App\Http\Traits;

use App\Helpers\GenerateStringUnique;;
use App\Models\Link;
use App\Models\Member;
use App\Models\Invoice;
use Carbon\Carbon;

use App\Http\Traits\GenerateTokenUniqueColumnTrait;

trait FormRegistrationTrait
{
    use GenerateTokenUniqueColumnTrait;

    public function AvailableMemberOnEvent($member, $email)
    {
        $matched = 0;
        foreach ($member as $item) {
            if ($item->email == $email) {
                $matched += 1;
            }
        }

        if ($matched == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isRegistrationMemberQuota($member, $quota)
    {
        if (count($member) < $quota) {
            return true;
        } else {
            return false;
        }
    }

    public function isRegistrationPaidMemberQuota($member, $quota)
    {
        $paid = 0;
        $now = Carbon::now();
        foreach ($member as $item) {
            // check if invoice is paid (2) or not expired
            if ($item->invoices->status == 2 || $item->invoices->status == 1 || $item->invoices->valid_until > $now) {
                $paid += 1;
            }
        }

        if ($paid < $quota) {
            return true;
        } else {
            return false;
        }
    }

    public function countTotalRegisteredMemberOnEvent($member)
    {
        return count($member);
    }

    public function createInvoice(Member $member, Link $link)
    {
        $invoice = new Invoice;
        $invoice->member_id = $member->id;
        $invoice->token = GenerateStringUnique::make(Invoice::all()->toArray(), 'token')->getToken(Member::PAYMENT_TOKEN_LENGTH);
        $currentDateTime = Carbon::now();
        $newDateTime = Carbon::now()->addHours(24);
        $invoice->valid_until = $newDateTime;
        $invoice->status = 0;
        $invoice->save();

        return $invoice;
    }
}
