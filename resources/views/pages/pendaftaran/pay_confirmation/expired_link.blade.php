@if (config('app.locale') == 'id')
    <h3 class="font-semibold px-10 py-5 sm:mx-auto">Link Konfirmasi Pembayaran Telah Kadaluarsa</h3>
@else
    <h3 class="font-semibold px-10 py-5 sm:mx-auto">Payment Confirmation Link Has Expired</h3>
@endif
    <p class="px-10 py-5 pb-10 sm:mx-auto text-center">{{ $message }}</p>
    <div class="flex justify-center py-5 align-center">
        <a href="{{ $route_form }}"
            class="bg-transparent flex-fill hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
            @if (config('app.locale') == 'id')
                Kembali mengisi form
            @else
                Back to fill out the form
            @endif
        </a>
    </div>
