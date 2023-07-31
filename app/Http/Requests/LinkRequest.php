<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                    'is_multiple_registrant_allowed' => ['sometimes'],
                    'sub_member_limit' => ['required_if:is_multiple_registrant_allowed,1', 'numeric', 'min:2'],
                    'email_confirmation' => ['required', 'max:2000'],
                    'email_confirmed' => ['required', 'max:2000'],
                ];
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
            'is_multiple_registrant_allowed' => 'Pendaftaran Boleh Lebih dari 1 Orang',
            'sub_member_limit' => 'Batas Multi Peserta',
        ];

        switch ($this->event_type) {
            case "pay":
                $attributes['email_confirmation'] = 'Email Konfirmasi Pembayaran';
                $attributes['email_confirmed'] = 'Email Konfirmasi Pembayaran';
                break;
        }

        return $attributes;
    }
}
