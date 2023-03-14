<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\AttendRegisteredEvent;

class AttendingRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255', 'exists:members,email', new AttendRegisteredEvent($this->attendance)],
            'no_telpon' => ['required', 'numeric', 'digits_between:8,13', 'exists:members,contact_number'],
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'email',
            'no_telpon' => 'nomor telepon',
        ];
    }

    public function validated()
    {
        $validated = parent::validated();
        $validated['attend_id'] = $this->attendance->id;
        $validated['member_id'] = $this->attendance->link->members()->where('email', $validated['email'])->first()->id;
        unset($validated['email']);
        unset($validated['no_telpon']);

        return $validated;
    }
}
