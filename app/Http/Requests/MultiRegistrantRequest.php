<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

class MultiRegistrantRequest extends FormRequest
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
            'full_name.*' => 'nullable|max:255|string',
            'no_telpon.*' => 'nullable|numeric|digits_between:8,13',
            'first_full_name' => 'required|max:255|string',
            'first_no_telpon' => 'required|numeric|digits_between:8,13',
            'link_path' => 'required|string|max:10',
        ];
    }

    public function attributes()
    {
        return [
            'full_name.*' => 'Nama Lengkap',
            'no_telpon.*' => 'No. Telpon',
            'first_full_name' => 'Nama Lengkap Pertama',
            'first_no_telpon' => 'No. Telpon Pertama',
        ];
    }

    public function validationData()
    {
        $data = $this->all();
        // store first registrant
        $data['first_full_name'] = $data['full_name'][0];
        $data['first_no_telpon'] = $data['no_telpon'][0];

        $data['link_path'] = $this->route('link');

        // remove first registrant
        unset($data['full_name'][0]);
        unset($data['no_telpon'][0]);

        return $data;
    }

    public function validated()
    {
        $validated = parent::validated();

        $parent_member = Session::get('member_parent');
        $validated['sub_members'] = [];
        // add first registrant to sub members
        $validated['sub_members'][] = [
            'full_name' => $validated['first_full_name'],
            'contact_number' => $validated['first_no_telpon'],
            'corporation' => $parent_member->corporation,
        ];

        foreach ($validated['full_name'] as $key => $value) {
            if ($value != null && $validated['no_telpon'][$key] != null) {
                $validated['sub_members'][] = [
                    'full_name' => $value,
                    'contact_number' => $validated['no_telpon'][$key],
                    'corporation' => $parent_member->corporation,
                ];
            }
        }
        // delete sub members from where null
        unset($validated['full_name']);
        unset($validated['no_telpon']);
        
        return $validated;
    }
}
