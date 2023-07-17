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
            'full_name' => ['bail', 'nullable', 'string', 'max:255'],
            'email' => ['bail', 'required', 'email', 'max:255', new AttendRegisteredEvent($this->attendance)],
            'no_telpon' => ['bail', 'required', 'numeric', 'digits_between:8,13', 'exists:members,contact_number'],
            'is_certificate' => ['required', 'in:yes,no'],
            'bukti' => ['required_if:is_certificate,yes', 'image', 'max:10240'],
            'corporation' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes()
    {
        return [
            'full_name' => __('attend.form.full_name'),
            'email' => __('attend.form.email'),
            'no_telpon' => __('attend.form.phone_number'),
            'is_certificate' => __('attend.form.is_certificate'),
            'bukti' => __('attend.form.upload_pay'),
            'corporation' => __('attend.form.corporation'),
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }

    public function validated()
    {
        $validated = parent::validated();
        $memberS = null;
        if (!$validated['full_name']) {
            unset($validated['full_name']);
        }
        
        $validated['is_certificate'] = $validated['is_certificate'] == 'yes' ? true : false;
        $validated['certificate'] = $validated['is_certificate'];
        $validated['attend_id'] = $this->attendance->id;
        if ($this->attendance->allow_non_register) {
            $link = $this->attendance->link;
            $memberS = $link->members()->where('email', $validated['email'])->first();
            if (!$memberS) {
                $memberS = $link->members()->create([
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'contact_number' => $validated['no_telpon'],
                    'corporation' => $validated['corporation'],
                ]);
            }
            
        } else {
            $memberS = $this->attendance->link->members()->where('email', $validated['email'])->first();
        }
        $validated['member_id'] = $memberS->id;
        unset($validated['email']);
        unset($validated['no_telpon']);

        return $validated;
    }
}
