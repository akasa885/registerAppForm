@props([
    'attendance' => null,
    'link' => null,
])
@if ($attendance->is_using_payment_gateway)
<!--begin::Option 1 : Payment Gateway link midtrans-->
<div class="flex flex-wrap hidden" id="uploadPay">
    <!--begin::paragraph payment information-->
    <p class="text-gray-600 text-sm mt-2 mb-2 w-full whitespace-normal bg-gray-300 p-2 italic">
        {{ $attendance->payment_information }}
    </p>
    <!--end::paragraph payment information-->
    <!--begin::price show-->
    <div class="flex flex-wrap w-full mb-3 gap-2">
        <!--begin::label certificate price-->
        <span class="text-sm text-bold font-bold text-gray-700 dark:text-white">
            {{ __('attend.certificate_price').' : ' }}
        </span>
        <!--end::label certificate price-->
        <!--begin::certificate price-->
        <span class="text-sm text-gray-500 dark:text-gray-300">
            {{ $attendance->formatted_price_certificate }}
        </span>
        <!--end::certificate price-->
    </div>
    <!--end::price show-->
    <!--begin::text helper-->
    <span class="text-gray-600 text-sm mt-2 w-full text-red-500">
        {{ __('attend.form.helper_payment_gateway') }}
    </span>
</div>
<!--end::Option 1 : Payment Gateway link midtrans-->
@else
<!--begin::Option 2 : Upload Bukti Pembayaran-->
<div class="flex flex-wrap hidden" id="uploadPay">
    <!--begin::paragraph payment information-->
    <p class="text-gray-600 text-sm mt-2 mb-2 w-full whitespace-normal bg-gray-300 p-2 italic">
        {{ $attendance->payment_information }}
    </p>
    <!--end::paragraph payment information-->
    <!--begin::price show-->
    <div class="flex flex-wrap w-full mb-3 gap-2">
        <!--begin::label certificate price-->
        <span class="text-sm text-bold font-bold text-gray-700 dark:text-white">
            {{ __('attend.certificate_price').' : ' }}
        </span>
        <!--end::label certificate price-->
        <!--begin::certificate price-->
        <span class="text-sm text-gray-500 dark:text-gray-300">
            {{ $attendance->formatted_price_certificate }}
        </span>
        <!--end::certificate price-->
    </div>
    <label for="input-4" class="block w-full text-gray-700 text-sm font-bold mb-2 sm:mb-1 required">
        {{ __('attend.form.upload_pay') }}
    </label>

    <input
        class="form-control block w-full
    px-3 py-1.5 text-base font-normal text-gray-700
    bg-white bg-clip-padding border border-solid border-gray-300 rounded
    transition ease-in-out m-0
    focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
        type="file" name="bukti" accept=".jpeg, .jpg, .png" id="formFile">

    <span
        class="text-gray-600 text-xs mt-2 w-full">{{ __('attend.form.upload_pay_helper') }}</span><br />

    @error('bukti')
        <p class="text-red-500 text-xs italic mt-2">
            {{ $message }}
        </p>
    @enderror
</div>
<!--end::Option 2 : Upload Bukti Pembayaran-->
@endif