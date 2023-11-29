<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use App\Http\Controllers\Controller;
use App\Helpers\Midtrans;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    private function generateMenu()
    {
        return [
            [
                'label' => 'Website',
                'route' => 'admin.setting.view',
                'link' => route('admin.setting.view')
            ],
            [
                'label' => 'Midtrans',
                'route' => 'admin.setting.midtrans',
                'link' => route('admin.setting.midtrans')
            ]
        ];
    }

    public function site()
    {
        $menu = $this->generateMenu();

        $content = view('admin.pages.setting.site')->render();

        return view('admin.pages.setting.view', [
            'title' => 'Pengaturan Website',
            'header' => 'Pengaturan Website',
            'subheader' => 'This is a page where you can manage your regular website settings',
        ], compact('menu', 'content'));
    }

    public function midtrans()
    {
        $menu = $this->generateMenu();
        $midtransInfo = Midtrans::midtransInfo();

        $content = view('admin.pages.setting.midtrans', compact('midtransInfo'))->render();

        return view('admin.pages.setting.view', [
            'title' => 'Pengaturan Midtrans',
            'header' => 'Pengaturan Midtrans',
            'subheader' => 'This is a page where you can manage your midtrans settings',
        ], compact('menu', 'content'));
    }

    public function midtransUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'midtrans_client_key' => 'required',
            'midtrans_server_key' => 'required',
            'midtrans_merchant_id' => 'required',
            'midtrans_environment' => 'required|in:production,sandbox',
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

        $validated = $validator->validated();

        if ($validated['midtrans_environment'] == 'production') {
            $validated['midtrans_is_production'] = true;
        } else {
            $validated['midtrans_is_production'] = false;
        }

        $env = [
            'MIDTRANS_CLIENT_KEY' => $validated['midtrans_client_key'],
            'MIDTRANS_SERVER_KEY' => $validated['midtrans_server_key'],
            'MIDTRANS_MERCHANT_ID' => Crypt::encryptString($validated['midtrans_merchant_id']),
            'MIDTRANS_IS_PRODUCTION' => $validated['midtrans_is_production'],
        ];

        $jsonMidtrans = json_encode($env);

        Storage::disk('local')->put('key/midtrans.json', $jsonMidtrans);

        return redirect()->back()->with('success', 'Midtrans settings updated successfully');
    }
}
