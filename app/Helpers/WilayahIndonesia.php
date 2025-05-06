<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class WilayahIndonesia {
    
    protected $api_key = null;
    protected $url = 'https://api.binderbyte.com/wilayah';

    public function __construct() {
        $this->api_key = env('ID_WILAYAH_API_KEY', null);
    }

    private function checkApiKey()
    {
        if (is_null($this->api_key)) {
            throw new \Exception('API Key is required');
        }
    }

    public function getProvinsi()
    {
        $this->checkApiKey();
        $endpoint = '/provinsi';
        $method = 'GET';

        $url = $this->url . $endpoint . '?api_key=' . $this->api_key;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->$method($url);

        return $response->json();
    }

    public function getCity($state_id)
    {
        $this->checkApiKey();
        $endpoint = '/kabupaten';
        $method = 'GET';

        $url = $this->url . $endpoint . '?api_key=' . $this->api_key . '&id_provinsi=' . $state_id;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->$method($url);

        return $response->json();
    }


    public static function connect()
    {
        $self = new self();

        return $self;
    }
}