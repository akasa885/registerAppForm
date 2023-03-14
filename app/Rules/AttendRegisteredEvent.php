<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Attendance;

class AttendRegisteredEvent implements Rule
{
    public $attendance;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $link = $this->attendance->link;
        
        $attend = $link->members()->where('email', $value)->first();
        if ($attend) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Anda tidak terdaftar dalam event ini';
    }
}
