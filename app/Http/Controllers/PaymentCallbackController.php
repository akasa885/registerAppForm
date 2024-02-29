<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberAttend;
use App\Models\Invoice;
use App\Models\AttendPaymentStore;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\Midtrans\CallbackService;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Traits\MailPaymentTrait;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Helpers\Midtrans as MidHelper;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    use MailPaymentTrait;
    private function createDuplicateOrder($order)
    {
        $preDupOrder = $order->prepDupOrder();
        $orderDetails = $order->orderDetails;

        $newOrder = Order::create($preDupOrder->toArray());
        $dupOrdet = collect();
        foreach ($orderDetails as $item) {
            $dupOrdet->push($item->prepDupOrderDetail());
        }
        $newOrder->orderDetails()->saveMany($dupOrdet);

        return $newOrder;
    }

    private function availableOrderType()
    {
        return [
            'TCK' => 'ticket',
            'CRT' => 'certificate',
        ];
    }

    private function orderType($order_number)
    {
        // order_number : ex. ORD.2023.1118.TCK.0001.0001

        $orderType = explode('.', $order_number)[3];
        
        return $this->availableOrderType()[$orderType];

    }

    public function receive()
    {
        try {
            $orderType = 'ticket';
            DB::beginTransaction();
            $callback = new CallbackService;

            if ($callback->isSignatureKeyVerified()) {
                $notification = $callback->getNotification();
                $order = $callback->getOrder();
                $invoice = $order->invoice;
                $orderType = $this->orderType($order->order_number);

                if (is_null($invoice) && $orderType != 'certificate') {
                    return response()
                        ->json([
                            'error' => true,
                            'message' => 'Order / Invoice is incorrect',
                        ], 404);
                }

                if ($callback->isSuccess()) {
                    Order::where('id', $order->id)->update([
                        'paid_at' => now(),
                        'status' => 3,
                    ]);

                    if ($orderType != 'certificate') {
                        Invoice::where('id', $invoice->id)->update([
                            'is_automatic' => true,
                            'status' => 2,
                            'payment_method' => $callback->getPaymentType(),
                        ]);

                        $member = $order->member;
                        $linkMember = $member->link;
    
                        $this->sendMailPaymentReceived($linkMember, $member);
                    }

                    if ($orderType == 'certificate') {
                        $attendPaymentStore = AttendPaymentStore::where('order_id', $order->id)->first();
                        $attend = $attendPaymentStore->attend;
                        if ($attend->confirmation_mail) {
                            try {
                                (new AttendanceController)->sendConfirmationAttendanceMail($attend, $attendPaymentStore->member_id, $attendPaymentStore->member);
                            } catch (\Throwable $th) {
                                report($th);
                            }
                        }

                        try {
                            MemberAttend::updateOrCreate(
                                ['member_id' => $attendPaymentStore->member_id, 'attend_id' => $attend->id],
                                ['certificate' => 1]
                            );
                        } catch (\Throwable $th) {
                            report($th);
                        }
                    
                    }
                }

                if ($callback->isPending()) {
                    Order::where('id', $order->id)->update([
                        'status' => 2,
                    ]);
                }

                if ($callback->isExpire()) {
                    Order::where('id', $order->id)->update([
                        'status' => 6,
                    ]);

                    if ($orderType != 'certificate') {
                        $newOrder = $this->createDuplicateOrder($order);

                        $invoice->invoicedOrder->order_id = $newOrder->id;
                        $invoice->invoicedOrder->save();
                    } else {
                        AttendPaymentStore::where('order_id', $order->id)->delete();
                    }
                }

                if ($callback->isCancelled()) {
                    if ($orderType != 'certificate') {
                        $member = $order->member;
                        $member->forceDelete();
                    } else {
                        AttendPaymentStore::where('order_id', $order->id)->delete();
                    }
                }

                DB::commit();
                return response()
                    ->json([
                        'success' => true,
                        'message' => 'Notification successfully processed',
                    ]);
            } else {
                DB::rollBack();
                return response()
                    ->json([
                        'error' => true,
                        'message' => 'Signature key not verified',
                    ], 403);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            report($th);

            if (config('app.debug')) {
                return response()
                    ->json([
                        'error' => true,
                        'message' => $th->getMessage(),
                        'method' => __METHOD__,
                        'line' => $th->getLine(),
                    ], 500);
            }

            return response()
                ->json([
                    'error' => true,
                    'message' => 'Failed to update status of order. please contact our customer service',
                ], 500);
        }
    }

    public function status(Request $request)
    {
        try {
            $orderType = 'ticket';
            // // make validator for request
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,order_number',
                'customer_id' => 'required',
                'transaction_status' => 'nullable|in:success,pending,cancelled,expire,settlement',
            ]);

            // if validator fails
            if ($validator->fails()) {
                if (config('app.debug')) {
                    return response()
                        ->json([
                            'error' => true,
                            'message' => $validator->errors(),
                        ], 422);
                }

                return response()
                    ->json([
                        'error' => true,
                        'message' => 'Status payment '. $request->transaction_status . '!, redirect failed',
                    ], 422);
            }

            // $customer_id = $request->customer_id;
            $customer_id = Crypt::decryptString($request->customer_id);
            $order = Order::where('order_number', $request->order_id)->firstOrFail();
            $customer = Member::where('id', $customer_id)->firstOrFail();

            $orderType = $this->orderType($order->order_number);

            $data = [
                'order_number' => $order->order_number,
                'order_net_total' => $order->net_total,
                'customer' => $customer ?? null,
                'status' => $order->status,
                'type' => $orderType,
            ];

            if ($orderType != 'certificate') {
                $data['form_invoice'] = route('form.link.pay', ['link' => $customer->link->link_path, 'payment' => $customer->invoices->token]);
                $data['form_link'] = route('form.link.view', ['link' => $customer->link->link_path]);
            } else {
                $attendance = $order->orderDetails->first()->orderable;
                $data['form_link'] = route('attend.link', ['link' => $attendance->attendance_path]);
            }

            if ($data['status'] == 'processing' || $data['status'] == 'pending') {
                $data['status'] = 'processing';
                $data['cancel_transaction'] = route('payments.request.cancel');
            }

            if (Schema::hasColumn('orders', 'snap_redirect') && $order->snap_redirect) {
                $data['payment_page'] = $order->snap_redirect;
                $urlArr = parse_url($order->snap_redirect);
                $lastPath = explode('/', $urlArr['path']);
                $lastPath = end($lastPath);
                $transactionId = $lastPath;
                $data['transaction_id'] = $transactionId;
                isset($data['cancel_transaction']) && $data['cancel_transaction'] != null ? $data['cancel_transaction'] .= "?transaction_id={$transactionId}&order_number={$order->order_number}" : null;
            }

            return view('pages.transaction.callback-info', $data);
        } catch (\Throwable $th) {
            if (config('app.debug')) throw $th;
            report($th);

            abort(404, 'Url invalid, page not found');
        }
    }

    public function cancel(Request $request)
    {
        if (!$request->ajax()) {
            abort(404, 'Url invalid, page not found');
        }

        try {
            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required|string',
                'order_number' => 'required|exists:orders,order_number',
            ]);

            if ($validator->fails()) {
                if (config('app.debug')) {
                    return response()
                        ->json([
                            'error' => true,
                            'message' => $validator->errors(),
                        ], 422);
                }

                return response()
                    ->json([
                        'error' => true,
                        'message' => 'Failed to cancel transaction',
                    ], 422);
            }

            DB::beginTransaction();

            // $midApiCancel = Http::withBasicAuth(config('midtrans.server_key'), '')
            //     ->withHeaders([
            //         'Accept' => 'application/json',
            //         'Content-Type' => 'application/json',
            //     ])
            //     ->post(MidHelper::getVersioningApiUrl()."/{$request->order_number}/cancel");

            $client = new Client();
            $midApiCancel = $client->request('POST', MidHelper::getVersioningApiUrl()."/{$request->order_number}/cancel", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'auth' => [config('midtrans.server_key'), ''],
            ]);

            $midApiCancelBody = null;

            if ($midApiCancel->getStatusCode() == 200) $midApiCancelBody = json_decode($midApiCancel->getBody()->getContents());
            
            if ($midApiCancel->getStatusCode() == 200 && $midApiCancelBody->fraud_status == 'accept') {
                $order = Order::where('order_number', $request->order_number)->firstOrFail();
                $order->update([
                    'status' => 5,
                ]);

                // name will be ticket registration & certificate payment
                $orderType = $this->orderType($order->order_number);

                if ($orderType != 'certificate') {
                    $newOrder = $this->createDuplicateOrder($order);
                    $invoice = $order->invoice;

                    $invoice->invoicedOrder->order_id = $newOrder->id;
                    $invoice->invoicedOrder->save();
                }

                $customer = $order->member;

                Log::info("#### Canceling Order by User ID: " .$customer->id ." Category :".$orderType." ##########");
                DB::commit();
                return response()
                    ->json([
                        'status' => 'success',
                        'message' => 'Transaction successfully cancelled',
                        'redirect' => route('form.link.pay', ['link' => $customer->link->link_path, 'payment' => $customer->invoices->token])
                    ]);
            }

            DB::rollBack();
            return response()
                ->json([
                    'status' => 'failed',
                    'message' => 'Failed to cancel transaction',
                ], 500);
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug')) throw $th;
            report($th);

            return response()
                ->json([
                    'status' => 'failed',
                    'message' => 'Failed to cancel transaction',
                ], 500);
        }
    }
}
