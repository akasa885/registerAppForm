<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Validator;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function site()
    {
        //
    }

    public function midtrans()
    {
        //
    }

    public function midtransUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'midtrans_client_key' => 'required',
            'midtrans_server_key' => 'required',
            'midtrans_merchant_id' => 'required',
            'midtrans_environment' => 'required, in:production,sandbox',
        ], [
            'midtrans_client_key.required' => 'Client Key is required',
            'midtrans_server_key.required' => 'Server Key is required',
            'midtrans_merchant_id.required' => 'Merchant ID is required',
            'midtrans_environment.required' => 'Environment is required',
            'midtrans_environment.in' => 'Environment must be production or sandbox',
        ], [
            'midtrans_client_key' => 'Client Key',
            'midtrans_server_key' => 'Server Key',
            'midtrans_merchant_id' => 'Merchant ID',
            'midtrans_environment' => 'Environment',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }
}
