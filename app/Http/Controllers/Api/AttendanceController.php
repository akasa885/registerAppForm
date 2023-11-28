<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\AttendPaymentStore;
use App\Models\Attendance;
use App\Models\MemberAttend;
use App\Models\Member;
use App\Models\Order;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function paymentCertificate($temp_store_id, $order_number)
    {
        $tempStore = AttendPaymentStore::where('id', $temp_store_id)->firstOrFail();
        $order = Order::where('order_number', $order_number)->firstOrFail();
        $attendance = Attendance::where('id', $tempStore->attend_id)->firstOrFail();

        if ($order->status == 'completed') {

            $memberAttend = MemberAttend::create([
                'attend_id' => $tempStore->attend_id,
                'member_id' => $tempStore->member_id,
                'certificate' => 1
            ]);

            if ($tempStore->changed_full_name) {
                $member = $order->member;

                $member->full_name = $tempStore->changed_full_name;
                $member->save();
            }

            $tempStore->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment success',
                'order_status' => $order->status,
                'redirect' => route('attend.link', ['link' => $attendance->attendance_path])
            ]);
        }

        if ($order->status == 'pending' || $order->status == 'processing') {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment pending',
                'order_status' => $order->status,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Payment failed',
            'order_status' => $order->status,
        ]);
    }
}
