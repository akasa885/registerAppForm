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
        $rules = [];
        $this->event_type = $type;

        switch($type){
            case "pay" :
                $rules = [
                    'event_type' => ['required'],
                    'title' => ['required'],
                    'desc' => ['required'],
                    'email_confirmation' => ['required', 'max:500'],
                    'email_confirmed' => ['required', 'max:500'],
                    'open_date' => ['required'],
                    'close_date' => ['required']
                ];
                break;
            case "free" :
                $rules = [
                    'event_type' => ['required'],
                    'title' => ['required'],
                    'desc' => ['required'],
                    'open_date' => ['required'],
                    'close_date' => ['required']
                ];
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
            'open_date' => 'Tanggal Buka Event',
            'close_date' => 'Tanggal Tutup Event'
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
