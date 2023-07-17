<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Link;

class AttendanceRequest extends FormRequest
{
    private $ver_control = 'v0';
    public $type_control;
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
        return $this->customRoles($this->attendance_type ?? 'day');
    }

    public function customRoles($type)
    {
        $this->type_control = $type;

        $roles = [];
        $roles = [
            'attendance_type' => ['required', 'string', 'in:day,hourly'],
            'selected_event' => ['required', 'string', 'exists:links,link_path'],
            'cert_confirm' => ['sometimes'],
            'mail_confirm' => ['sometimes'],
            'confirmation_mail' => ['nullable', 'max:2000'],
            'allow_non_register' => ['sometimes'],
        ];

        switch ($type){
            case 'day' : 
                $roles = array_merge($roles, [
                    'date' => ['required', 'date'],
                ]);
                break;
            case 'hourly' :
                $roles = array_merge($roles, [
                    'datetime_start' => ['required', 'date'],
                    'datetime_end' => ['required', 'date', 'after:datetime_start'],
                ]);
                break;
        }

        return $roles;
    }

    public function attributes()
    {
        $attributes = [];
        $attributes = [
            'selected_event' => 'Event',
        ];

        switch($this->type_control){
            case 'day' :
                $attributes = array_merge($attributes, [
                    'date' => 'Hari',
                ]);
                break;
            case 'hourly' :
                $attributes = array_merge($attributes, [
                    'datetime_start' => 'Waktu Buka',
                    'datetime_end' => 'Waktu Tutup',
                ]);
                break;
        }

        return $attributes;
    }

    public function validated()
    {
        $validated = parent::validated();
        if ($this->type == 'day')
        {
            $validated['active_from'] = date('Y-m-d H:i:s', strtotime($validated['date'] . ' 00:00:00'));
            $validated['active_until'] = date('Y-m-d H:i:s', strtotime($validated['date'] . ' 23:59:59'));
            unset($validated['date']);
        } else if ($this->type == 'hourly')
        {
            $validated['active_from'] = date('Y-m-d H:i:s', strtotime($validated['datetime_start']));
            $validated['active_until'] = date('Y-m-d H:i:s', strtotime($validated['datetime_end']));
            unset($validated['date']);
            unset($validated['datetime_start']);
            unset($validated['datetime_end']);
        }
        $validated['link_id'] = Link::where('link_path', $validated['selected_event'])->first()->id;
        $validated['created_by'] = auth()->user()->id;

        if (isset($validated['cert_confirm'])) {
            $validated['with_verification_certificate'] = true;
        }

        if (isset($validated['allow_non_register'])) {
            $validated['allow_non_register'] = true;
        }

        if (!isset($validated['mail_confirm'])) {
            unset($validated['confirmation_mail']);
        }
        
        unset($validated['selected_event']);

        return $validated;
    }
}
