<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Invoice;

class LinkRequest extends FormRequest
{
    private $event_type;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->customRules($this->input('event_type'));
    }

    public function customRules($type)
    {
        $rules = [
            'event_type' => ['required'],
            'title' => ['required'],
            'desc' => ['required'],
            'registration_info' => ['required', 'max:2000'],
            'member_limit' => ['required', 'numeric', 'min:0'],
            'open_date' => ['required'],
            'close_date' => ['required', 'after:open_date'],
            'event_date' => ['required'],
        ];
        $this->event_type = $type;

        switch($type){
            case "pay" :
                // append rules
                $rules += [
                    'price' => ['required', 'numeric', 'min:0'],
                    'is_multiple_registrant_allowed' => ['sometimes'],
                    'sub_member_limit' => ['required_if:is_multiple_registrant_allowed,1', 'numeric', 'min:2'],
                    'email_confirmation' => ['required', 'max:2000'],
                    'email_confirmed' => ['required', 'max:2000'],
                ];

                if (Invoice::PAYMENT_TYPE != 'multipayment' || $this->is_multipayment == null) {
                    $rules += [
                        'bank' => ['required'],
                        'bank.name' => ['required'],
                        'bank.account_number' => ['required', 'numeric'],
                        'bank.account_name' => ['required'],
                    ];
                }

                break;
            case "free" :
                // nothing to do
                break;
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];
        $attributes = [
            'event_type' => 'Tipe Event',
            'title' => 'Judul Event',
            'desc' => 'Deskripsi Event',
            'registration_info' => 'Informasi Pendaftaran',
            'member_limit' => 'Batas Peserta',
            'open_date' => 'Tanggal Buka Form Event',
            'close_date' => 'Tanggal Tutup Form Event',
            'event_date' => 'Tanggal Event',
        ];

        switch ($this->event_type) {
            case "pay":
                $attributes['email_confirmation'] = 'Email Konfirmasi Pembayaran';
                $attributes['email_confirmed'] = 'Email Konfirmasi Pembayaran';
                $attributes['is_multiple_registrant_allowed'] = 'Pendaftaran Banyak Peserta';
                $attributes['sub_member_limit'] = 'Batas Multi Peserta';

                if (Invoice::PAYMENT_TYPE != 'multipayment' || $this->is_multipayment == null) {
                    $attributes['bank'] = 'Informasi Bank';
                    $attributes['bank.name'] = 'Nama Bank';
                    $attributes['bank.account_number'] = 'Nomor Rekening';
                    $attributes['bank.account_name'] = 'Nama Pemilik Rekening';
                }
                break;
        }

        return $attributes;
    }
}
