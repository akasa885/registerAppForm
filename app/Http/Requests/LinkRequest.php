<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkRequest extends FormRequest
{
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
        return [
            'title' => ['required'],
            'desc' => ['required'],
            'email_confirmation' => ['required', 'max:500'],
            'email_confirmed' => ['required', 'max:500'],
            'open_date' => ['required'],
            'close_date' => ['required']
        ];
    }
}
