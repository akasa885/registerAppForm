<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AttendingUpPayment implements Rule
{
    public $attendance;
    public $withCertificate;
    protected $fileUploadBukti;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attendance, $buktiUpload = null)
    {
        $this->attendance = $attendance;
        $this->fileUploadBukti = $buktiUpload;
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
        if ($value == 'yes') {
            $this->withCertificate = true;
        }

        if (!$this->attendance->is_using_payment_gateway) {
            // if without certificate then no need to check payment
            if (!$this->withCertificate) {
                return true;
            }
            // if with certificate then check payment, is value is null then return false
            if ($this->withCertificate && $this->fileUploadBukti) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (config('app.locale' == 'en')) {
            return 'Your payment proof must be uploaded';
        }

        return 'Bukti pembayaran anda wajib di upload';
    }
}
