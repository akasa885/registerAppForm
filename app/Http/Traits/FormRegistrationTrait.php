<?php
namespace App\Http\Traits;
use Carbon\Carbon;

trait FormRegistrationTrait {

    public function AvailableMemberOnEvent($member, $email)
    {
        $matched = 0;
        foreach ($member as $item) {
            if($item->email == $email){
                $matched += 1;
            }
        }

        if($matched == 0){
            return true;
        }else{
            return false;
        }
    }

    public function isRegistrationMemberQuota($member, $quota)
    {
        if(count($member) < $quota){
            return true;
        }else{
            return false;
        }
    }

    public function isRegistrationPaidMemberQuota($member, $quota)
    {
        $paid = 0;
        $now = Carbon::now();
        foreach ($member as $item) {
            // check if invoice is paid (2) or not expired
            if($item->invoices->status == 2 || $item->invoices->status == 1 || $item->invoices->valid_until > $now ){
                $paid += 1;
            }
        }

        if($paid < $quota){
            return true;
        }else{
            return false;
        }
    }

    public function countTotalRegisteredMemberOnEvent($member)
    {
        return count($member);
    }

}