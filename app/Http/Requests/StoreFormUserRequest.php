<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//rule
use App\Rules\FullnameRule;

class StoreFormUserRequest extends FormRequest
{
    private $ver_control = 'v0';
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
                'email' => ['required', 'email'],
                'no_telpon' => ['numeric', 'digits_between:8,13'],
                'instansi' => ['required'],
                'pertanyaan1' => ['required'],
                'pertanyaan2' => ['sometimes'],
                'pertanyaan3' => ['sometimes'],
            ],
            'v1' => [
                'link' => ['required', 'string', 'max:10'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email'],
                'no_telpon' => ['numeric', 'digits_between:8,13'],
                'instansi' => ['required'],
            ]
        ];

        return $rules[$this->ver_control];
    }

    public function verAttributes()
    {
        $attributes = [
            'v0' => [
                'fullname' => 'Nama Lengkap',
                'email' => 'Email',
                'no_telpon' => 'Nomor Telepon',
                'instansi' => 'Instansi',
                'pertanyaan1' => 'Pertanyaan 1',
                'pertanyaan2' => 'Pertanyaan 2',
                'pertanyaan3' => 'Pertanyaan 3',
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

        if ($this->ver_control == 'v1') {
            $validated['full_name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        } else {
            $validated['full_name'] = $validated['fullname'];
        }
        // no telpon -> contact number
        // $validated['contact_number'] = $validated['no_telpon'];
        // unset($validated['no_telpon']);
        // instansi -> coorporation
        // pertanyaan -> question
        $validated['question1'] = $validated['pertanyaan1'];
        unset($validated['pertanyaan1']);
        $validated['question2'] = $validated['pertanyaan2'];
        unset($validated['pertanyaan2']);
        $validated['question3'] = $validated['pertanyaan3'];
        unset($validated['pertanyaan3']);

        $validated['corporation'] = $validated['instansi'];
        unset($validated['instansi']);

        return $validated;
    }
}
