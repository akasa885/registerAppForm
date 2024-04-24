<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Midtrans {

    public $midtransInfo;
    public $midApiProduction = 'https://api.midtrans.com';
    public $midApiSandbox = 'https://api.sandbox.midtrans.com';

    protected $versionApi = 'v2';

    public function __construct()
    {
        try {
            $midtransInfo = Storage::disk('local')->get('key/midtrans.json');
            $midtransInfo = json_decode($midtransInfo, true);
            $midtransInfo['MIDTRANS_MERCHANT_ID'] = Crypt::decryptString($midtransInfo['MIDTRANS_MERCHANT_ID']);
        } catch (FileNotFoundException $e) {
            $midtransInfo = [
                'MIDTRANS_CLIENT_KEY' => '',
                'MIDTRANS_SERVER_KEY' => '',
                'MIDTRANS_MERCHANT_ID' => '',
                'MIDTRANS_IS_PRODUCTION' => false,
            ];
        }

        $this->midtransInfo = $midtransInfo;
    }

    public function isMidtransConfigured()
    {
        return $this->midtransInfo['MIDTRANS_CLIENT_KEY'] && $this->midtransInfo['MIDTRANS_SERVER_KEY'] && $this->midtransInfo['MIDTRANS_MERCHANT_ID'];
    }

    public static function midtransInfo()
    {
        $midtrans = new Midtrans();
        return $midtrans->midtransInfo;
    }

    public static function getMerchantId()
    {
        $midtrans = new Midtrans();
        return $midtrans->midtransInfo['MIDTRANS_MERCHANT_ID'];
    }

    public static function getClientKey()
    {
        $midtrans = new Midtrans();
        return $midtrans->midtransInfo['MIDTRANS_CLIENT_KEY'];
    }

    public static function getServerKey()
    {
        $midtrans = new Midtrans();
        return $midtrans->midtransInfo['MIDTRANS_SERVER_KEY'];
    }

    public static function isProduction()
    {
        $midtrans = new Midtrans();
        return $midtrans->midtransInfo['MIDTRANS_IS_PRODUCTION'];
    }

    public static function getApiUrl()
    {
        $midtrans = new Midtrans();

        return $midtrans->midtransInfo['MIDTRANS_IS_PRODUCTION'] ? $midtrans->midApiProduction : $midtrans->midApiSandbox;
    }

    public static function getVersioningApiUrl()
    {
        $midtrans = new Midtrans();

        return $midtrans->midtransInfo['MIDTRANS_IS_PRODUCTION'] ? $midtrans->midApiProduction.'/'.$midtrans->versionApi : $midtrans->midApiSandbox.'/'.$midtrans->versionApi;
    }

    public static function createConfig()
    {
        $midtrans = new Midtrans();
        return [
            'mercant_id' => $midtrans->midtransInfo['MIDTRANS_MERCHANT_ID'],
            'client_key' => $midtrans->midtransInfo['MIDTRANS_CLIENT_KEY'],
            'server_key' => $midtrans->midtransInfo['MIDTRANS_SERVER_KEY'],
            'is_production' => $midtrans->midtransInfo['MIDTRANS_IS_PRODUCTION'],
            'is_sanitized' => false,
            'is_3ds' => true,
        ];
    }
}