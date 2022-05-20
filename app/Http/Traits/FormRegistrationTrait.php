<?php
namespace App\Http\Traits;

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

}