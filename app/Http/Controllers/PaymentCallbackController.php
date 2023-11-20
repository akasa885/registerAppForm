<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\Midtrans\CallbackService;

class PaymentCallbackController extends Controller
{
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

    public function receive()
    {
        try {
            DB::beginTransaction();
            $callback = new CallbackService;

            if ($callback->isSignatureKeyVerified()) {
                $notification = $callback->getNotification();
                $order = $callback->getOrder();
                $invoice = $order->invoice;

                if (is_null($invoice)) {
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

                    Invoice::where('id', $invoice->id)->update([
                        'status' => 2,
                        'payment_method' => $callback->getPaymentType(),
                    ]);
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

                    $newOrder = $this->createDuplicateOrder($order);

                    $invoice->invoicedOrder->order_id = $newOrder->id;
                    $invoice->invoicedOrder->save();

                }

                if ($callback->isCancelled()) {
                    Order::where('id', $order->id)->update([
                        'status' => 5,
                    ]);

                    Invoice::where('id', $invoice->id)->update([
                        'status' => 0,
                    ]);
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
            // // make validator for request
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,order_number',
                'customer_id' => 'required',
                'status' => 'required|in:success,pending,cancelled,expire',
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

                abort(404, 'Url invalid, page not found');
            }

            $customer_id = Crypt::decryptString($request->customer_id);
            $order = Order::where('order_id', $request->order_id)->firstOrFail();
            $customer = Account::where('id', $customer_id)->firstOrFail();
            // $order = Order::first();
            // $customer = Account::first();

            $data = [
                'order_id' => $order->order_id,
                'order_net_total' => $order->net_total,
                'customer' => $customer ?? null,
                'status' => $request->status,
            ];
            return view('page.transaction.callback-info', [
                'title' => [
                    'web' => 'Payment Status',
                    'page' => 'Payment Status',
                ],
            ], $data);
        } catch (\Throwable $th) {
            if (config('app.debug')) throw $th;
            report($th);

            abort(404, 'Url invalid, page not found');
        }
    }
}
