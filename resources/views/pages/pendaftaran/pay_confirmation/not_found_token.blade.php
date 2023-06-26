<div class="h-auto py-5">
    @if (config('app.locale') == 'id')
        <h3 class="text-center font-semibold p-10 sm:mx-auto">Link Konfirmasi Pembayaran Tak Ditemukan !!!</h3>
    @else
        <h3 class="text-center font-semibold p-10 sm:mx-auto">Payment Confirmation Link Not Found !!!</h3>
    @endif
    <!--begin:: back button otuline info-->
    <div class="text-center">
        <a href="{{ $route_form }}"
            class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">Kembali Ke Form</a>
    </div>
</div>
