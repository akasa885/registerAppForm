<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

trait SiteContainTrait {

    public function getInformationFile()
    {
        try {
            $information = Storage::disk('local')->get('key/site.json');
            $information = json_decode($information, true);

            $information = [
                'sitename' => $information['SITE_NAME'],
                'description' => $information['DESCRIPTION'],
                'keywords' => $information['KEYWORDS'],
                'developedby' => $information['DEVELOPED_BY'],
                'license' => $information['LICENSE'],
                'copyright' => $information['COPYRIGHT'],
            ];

            return $information;
        } catch (FileNotFoundException $e) {
            $information = [
                'sitename' => config('app.name'),
                'copyright' => ' PT Utama Padma Qualiti',
                'developedby' => 'IT Department',
                'description' => 'Website E-Form Internal Organisasi',
                'keywords' => 'eform, e-form, internal form, form registrasi, form internal, form',
            ];

            return $information;
        }
    }

    public function getDataGeneralWebsite()
    {
        $sitename = config('app.name');
        $siteCopyRight = ' PT Utama Padma Qualiti';
        $developedBy = 'IT Department';

        return [$sitename, $siteCopyRight, $developedBy];
    }
}