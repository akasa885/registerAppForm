<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Attendance;

class AttendRegisteredEvent implements Rule
{
    public $attendance;
    protected $field;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attendance, $field = null)
    {
        $this->attendance = $attendance;
        if ($field == null) {
            $this->field = 'email';
        } else {
            $this->field = $field;
        }
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
        $attend = null;

        $link = $this->attendance->link;
        $nonRegAllowed = $this->attendance->allow_non_register;

        if ($this->field == 'email') {
            $attend = $link->members()->where('email', $value)->first();
        }

        if ($this->field == 'contact_number') {
            $attend = $link->members()->where('contact_number', $value)->first();
        }

        if ($nonRegAllowed || $attend) {
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
        if (config('app.locale') == 'en') {
            return 'You are not registered in this event';
        }
        
        return 'Anda tidak terdaftar dalam event ini';
    }
}
