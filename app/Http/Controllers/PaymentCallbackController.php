<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Invoice;
use App\Models\AttendPaymentStore;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\Midtrans\CallbackService;
use App\Http\Traits\MailPaymentTrait;

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
            $order = Order::where('order_number', $request->order_id)->firstOrFail();
            $customer = Member::where('id', $customer_id)->firstOrFail();

            $orderType = $this->orderType($order->order_number);

            $data = [
                'order_number' => $order->order_number,
                'order_net_total' => $order->net_total,
                'customer' => $customer ?? null,
                'status' => $request->status,
                'type' => $orderType,
            ];

            if ($orderType != 'certificate') {
                $data['form_link'] = route('form.link.view', ['link' => $customer->link->link_path]);
            } else {
                $attendance = $order->orderDetails->first()->orderable;
                $data['form_link'] = route('attend.link', ['link' => $attendance->attendance_path]);
            }

            return view('pages.transaction.callback-info', $data);
        } catch (\Throwable $th) {
            if (config('app.debug')) throw $th;
            report($th);

            abort(404, 'Url invalid, page not found');
        }
    }
}
