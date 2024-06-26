<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\AttendRegisteredEvent;
use App\Rules\AttendingUpPayment;

class AttendingRequest extends FormRequest
{
    protected $attendanceData;

    protected $stopOnFirstFailure = true;
    
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
        $rules = [
            'email' => ['bail', 'required', 'email:rfc,dns', 'max:255', new AttendRegisteredEvent($this->attendance)],
            'no_telpon' => ['bail', 'required', 'numeric', 'digits_between:8,13', new AttendRegisteredEvent($this->attendance, 'contact_number')],
            'is_certificate' => ['required', 'in:yes,no', new AttendingUpPayment($this->attendance, $this->bukti)],
            'bukti' => ['nullable', 'image', 'max:10240'],
        ];

        if ($this->attendanceData->allow_non_register) {
            $rules['full_name'] = ['required', 'string', 'max:255'];
            $rules['corporation'] = ['required', 'string', 'max:255'];
        } else {
            $rules['full_name'] = ['nullable', 'string', 'max:255'];
            $rules['corporation'] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
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
        $this->attendanceData = $this->route()->parameters()['attendance'];

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

                $validated['new_member_created'] = true;
                $validated['created_member'] = $memberS;
            }
            
        } else {
            $memberS = $this->attendance->link->members()->where('email', $validated['email'])->first();
            // email check
            if (!$memberS) {
                $this->validator->errors()->add('email', __('attend.failed_member_not_found'));
            }
            // contact number check
            if ($memberS->contact_number != $validated['no_telpon']) {
                $this->validator->errors()->add('no_telpon', __('attend.failed_member_not_found'));
            }
        }
        $validated['member_id'] = $memberS ? $memberS->id : null;
        unset($validated['email']);
        unset($validated['no_telpon']);

        return $validated;
    }
}
