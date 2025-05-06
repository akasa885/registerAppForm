<?php

namespace App\Http\Requests;

use App\Helpers\MembershipChecker;
use Illuminate\Foundation\Http\FormRequest;
//rule
use App\Rules\FullnameRule;
use App\Models\Link;
use App\Models\LocationCity;

class StoreFormUserRequest extends FormRequest
{
    private $ver_control = 'v0';
    private $linkModel;
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
        return $this->verRules();
    }

    public function verRules()
    {
        $rules = [
            'v0' => [
                'link' => ['required', 'string', 'max:10'],
                'fullname' => ['required', new FullnameRule()],
                'email' => ['required', 'email:rfc,dns'],
                'no_telpon' => ['numeric', 'digits_between:8,13'],
                'instansi' => ['required']
            ],
            'v1' => [
                'link' => ['required', 'string', 'max:10'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email'],
                'no_telpon' => ['numeric', 'digits_between:8,13'],
                'domisili' => ['required', 'string', 'max:100'],
                'instansi' => ['required'],
            ]
        ];

        $linkModel = Link::where('link_path', $this->link)->first();
        $this->linkModel = $linkModel;

        if (isset($linkModel->is_membership_only) && $linkModel->is_membership_only) {
            $rulesMember = [];
            $rulesMember['membership_status'] = ['required', 'numeric', 'in:0,1'];
            $rulesMember['registration_number'] = ['required', 'string', 'max:20'];
            $rulesMember['email'] = ['required', 'email:rfc,dns'];

            return $rulesMember;
        }

        if ($this->request->has('sel_domisili')) {
            $rules[$this->ver_control]['sel_domisili'] = ['required', 'numeric', 'exists:location_cities,id'];
        } else if ($this->request->has('domisili')) {
            $rules[$this->ver_control]['domisili'] = ['required', 'string', 'max:100'];
        }

        return $rules[$this->ver_control];
    }

    public function verAttributes()
    {
        if ($this->linkModel->is_membership_only) {
            return [
                'membership_status' => __('Membership Status'),
                'nik' => __('NIN'),
                'registration_number' => __('Membership Registration Number'),
                'email' => __('Email Address')
            ];
        }

        $attributes = [
            'v0' => [
                'fullname' => __('Full Name'),
                'email' => __('Email Address'),
                'no_telpon' => __('Phone Number (WhatsApp)'),
                'domisili' => __('Domicile (City)'),
                'instansi' => __('Instance / Company Name')
            ],
            'v1' => [
                'first_name' => 'Nama Depan',
                'last_name' => 'Nama Belakang',
                'email' => 'Email',
                'no_telpon' => 'Nomor Telepon',
                'instansi' => 'Instansi'
            ]
        ];

        return $attributes[$this->ver_control];
    }

    public function attributes()
    {
        return $this->verAttributes();
    }

    public function validated()
    {
        $validated = parent::validated();

        if ($this->linkModel->is_membership_only) {
            $validated = $this->membershipProcess($validated);
        } else {
            if ($this->ver_control == 'v1') {
                $validated['full_name'] = $validated['first_name'] . ' ' . $validated['last_name'];
            } else {
                $validated['full_name'] = $validated['fullname'];
            }

            // 
            $validated['email'] = strtolower($validated['email']);
            // no telpon -> contact number
            $validated['contact_number'] = $validated['no_telpon'];
            unset($validated['no_telpon']);
            // instansi -> coorporation
            $validated['corporation'] = $validated['instansi'];
            unset($validated['instansi']);
    
            if ($this->request->has('sel_domisili')) {
                $city = LocationCity::find($validated['sel_domisili']);
                $validated['domisili'] = ucwords(strtolower($city->name));
                unset($validated['sel_domisili']);
            }
        }

        return $validated;
    }

    private function membershipProcess($validated)
    {
        $member = MembershipChecker::connect()->verified()->member(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Client-Domain' => parse_url(config('app.url'))['host'],
            ], [
                'email' => $validated['email'],
            ]
        );

        $validated['full_name'] = $member['full_name_with_title'];
        $validated['domisili'] = $member['city'];
        $validated['corporation'] = $member['corporation'] ?? '-';
        $validated['link'] = $this->link;
        $validated['contact_number'] = $member['whatsapp_number'];

        return $validated;
    }
}
