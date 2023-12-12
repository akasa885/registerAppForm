<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SettingSiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('isAdmin') || Gate::allows('isSuperAdmin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sitename' => ['required', 'string', 'max:100'],
            'sitedescription' => ['required', 'string', 'max:255'],
            'copyright' => ['required', 'string', 'max:100'],
            'keywords' => ['nullable', 'array'],
            'keywords.*' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes()
    {
        return [
            'sitename' => 'Nama Website',
            'sitedescription' => 'Deskripsi Website',
            'copyright' => 'copyright',
            'keywords' => 'Kata Kunci',
        ];
    }

    public function validated()
    {
        $validated = parent::validated();
        $validated['keywords'] = implode(',', $validated['keywords']);
        $validated['description'] = $validated['sitedescription'];
        $validated['license'] = 'MIT';
        $validated['developedby'] = '';

        unset($validated['sitedescription']);

        return $validated;
    }
}
