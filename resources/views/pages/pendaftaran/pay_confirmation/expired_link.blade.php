@if (config('app.locale') == 'id')
    <h3 class="font-semibold p-10 sm:mx-auto">Link Konfirmasi Pembayaran Telah Kadaluarsa</h3>
@else
    <h3 class="font-semibold p-10 sm:mx-auto">Payment Confirmation Link Has Expired</h3>
@endif
<form action="{{ route('form.pay.renew') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_code" value="{{ $pay_code }}">
</form>
