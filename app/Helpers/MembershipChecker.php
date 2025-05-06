<?php

namespace App\Helpers;

use App\Models\Link;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class MembershipChecker {

    protected $endsite = null;
    protected $endpoint = null;

    public function __construct() 
    {
        $this->endsite = Link::ENDPOINTMEMBERSHIP;
    }

    private function endPointCheck()
    {
        if (is_null($this->endpoint)) {
            throw new \Exception('Endpoint is required');
        }
    }

    public function verified() 
    {
        $this->endpoint = '/api/v1/member/verified/info';

        return $this;
    }

    public function member($headers, $params)
    {
        $response = Http::withHeaders($headers)
                    ->get($this->endsite . $this->endpoint, $params);
        
        if ($response->status() != 200) {
            throw new \Exception('Failed to connect to the membership API');
        }

        return $response->json()['data']['member'];
    }

    public static function connect($endpoint = null)
    {
        $self = new self();

        // check the response if not 200 throw exception
        $response = Http::get($self->endsite);
        if ($response->status() != 200) {
            throw new \Exception('Failed to connect to the membership API');
        }

        if ($endpoint) {
            $self->endpoint = $endpoint;
        }

        return $self;
    }
}