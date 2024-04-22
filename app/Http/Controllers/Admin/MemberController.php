<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

//mail
use App\Mail\ConfirmedPay;

use App\Models\Member;
use App\Models\Invoice;
use App\Models\Email;

use App\Http\Traits\MailPaymentTrait;
use App\Http\Traits\OrderCustomTrait;


use Carbon\Carbon;

class MemberController extends Controller
{
    use MailPaymentTrait, OrderCustomTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Member $member)
    {
        //
    }

    public function viewSheet($id)
    {
        $data = Member::findorfail($id);
        if ($data->bukti_bayar == null) {
            return response()->json(['success' => false, 'message' => 'Bukti tidak ada!'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Bukti ditemukan', 'bukti' => asset('storage/bukti_image').'/'.$data->bukti_bayar, 'memberId' => $data->id], 200);
    }

    public function updateBukti(Request $request, Member $member)
    {
        $request->validate([
            'id' => 'required|exists:members,id',
            'received' => 'required|in:true,false',
        ]);
        $status = null;
        $message = '';
        if ($request->received == 'true') {
            $status = true;
        } else {
            $status = false;
        }
        try {
            if ($status){
                DB::beginTransaction();
                $data = $member->findorfail($request->id);
                $invoice = Invoice::where('member_id', $data->id)->first();
                $invoice->status = 2;
                $order = $invoice->order;
                $order->status = 'Completed';

                $this->sendMailPaymentReceived($data->link, $data);
                $invoice->save();
                $order->save();

                DB::commit();
                $message = 'Pembayaran Diterima';
            } else {
                DB::beginTransaction();
                $data = $member->findorfail($request->id);
                $invoice = Invoice::where('member_id', $data->id)->first();
                $order = $invoice->order;
                $currentDateTime = Carbon::now();
                $newDateTime = Carbon::now()->addHours(24);
                $invoice->token = $this->getToken(Member::PAYMENT_TOKEN_LENGTH);
                $invoice->valid_until = $newDateTime;
                $invoice->status = 0;
                $invoice->save();

                $order->status = 'decline';
                $order->save();

                $newOrder = $this->createDuplicateOrder($order);
                
                $invoice->invoicedOrder->order_id = $newOrder->id;
                $invoice->invoicedOrder->save();
                
                $message_html = "<p> Bukti pembayaran anda ditolak, silahkan upload bukti anda kembali </p>";

                $this->sendMailPayment($data->link, $data, $invoice, true, $message_html);

                DB::commit();
                $message = 'Pembayaran Ditolak';
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug')) throw $th;
            Log::error('Error update bukti bayar');
            Log::error($th->getMessage());
            // return $data->id;
            // return $member->link;
            return response()->json(['success' => false, 'message' => 'Request Error'], 500);
        }
    }

    private function getToken($lenght_token = 10)
    {
        $fix_token = '';
        $lock = 0;
        $data_token = Invoice::select('token')->get();
        if(count($data_token) > 0){
            $loop = count($data_token);
            for ($i=0; $i < $loop;) {
                foreach ($data_token as $tok) {
                    $temp = $this->generate_token($lenght_token);
                    if ($tok->token != $temp) {
                    $lock ++;
                    }else{
                    $lock = 0;
                    }
                }
                if ($loop == $lock) {
                    $fix_token = $temp;
                    $i = $loop;
                }else {
                    $i++;
                }
            }
            return $fix_token;
        }else{
            return $this->generate_token($lenght_token);
        }
    }

    public function generate_token($length = 10)
    {
      $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
      }

      return $randomString;
    }
}
