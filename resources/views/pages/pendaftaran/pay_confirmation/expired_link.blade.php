<h3 class="font-semibold p-10 sm:mx-auto">Link Konfirmasi Pembayaran Telah Kadaluarsa</h3>
<form action="{{ route('form.pay.renew') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_code" value="{{ $pay_code }}">
</form>
