@extends('layouts.app')
@if ($status == 'completed')
    @section('title', __('transaction.success-payment-title', ['order_number' => $order_number]))
@elseif ($status == 'processing')
    @section('title', __('transaction.pending-payment-title', ['order_number' => $order_number]))
@endif

@section('content')
    <main class="flex justify-center container px-7 sm:px-8 md:px-2 lg:px-0 py-10 sm:container sm:mx-auto">
        <div class="lg:w-6/12 sm:px-6 sm:w-10/12">
            <section class="flex flex-col break-words bg-white sm:border-1 sm:rounded-md sm:shadow-sm sm:shadow-lg">
                <div class="bg-gray-100">
                    <div class="bg-white p-6  md:mx-auto">
                        @if ($status == 'completed')
                            <svg viewBox="0 0 24 24" class="text-green-600 w-16 h-16 mx-auto my-6">
                                <path fill="currentColor"
                                    d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z">
                                </path>
                            </svg>
                            <div class="text-center">
                                <h3 class="md:text-2xl text-base text-gray-900 font-semibold text-center">Payment Done!</h3>
                                <p class="text-gray-600 my-2">{{ __('transaction.success-payment-auto-confirmed') }}</p>
                                @if ($type != 'certificate')
                                    <p>{{ __('mail.message-check-email-event-info') }}</p>
                                @else
                                    <p>Absensi Berhasil Dilakukan !</p>
                                @endif
                                <div class="py-10 text-center">
                                    <a href="{{ $form_link }}"
                                        class="px-12 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3">
                                        GO BACK
                                    </a>
                                </div>
                            </div>
                        @elseif ($status == 'processing')
                            <svg width="24" height="24" class="text-yellow-600 w-16 h-16 mx-auto my-6"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10"
                                    fill="currentColor" />
                                <rect x="11" y="14" width="7" height="2" rx="1"
                                    transform="rotate(-90 11 14)" fill="currentColor" />
                                <rect x="11" y="17" width="2" height="2" rx="1"
                                    transform="rotate(-90 11 17)" fill="currentColor" />
                            </svg>
                            <div class="text-center">
                                <h3 class="md:text-2xl text-base text-gray-900 font-semibold text-center">Payment On Process!</h3>
                                <p class="text-gray-600 my-2">{{ __('transaction.pending-payment-message') }}</p>
                                <p class="text-gray-600 my-2">{{ __('transaction.pending-payment-title', ['order_number' => $order_number]) }}</p>
                                <div class="py-10 text-center flex flex-col gap-2">
                                    <a href="{{ $payment_page }}" target="_blank"
                                        class="px-12 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3">
                                        Continue Payment
                                    </a>

                                    {{-- <a href="{{ $form_link }}"
                                        class="px-12 bg-green-600 hover:bg-green-500 text-white font-semibold py-3">
                                        Change Payment Method
                                    </a> --}}

                                    <a href="{{ $cancel_transaction }}" onclick="event.preventDefault(); document.getElementById('cancelling_transaction_post').submit();"
                                        class="px-12 bg-red-600 hover:bg-red-500 text-white font-semibold py-3">
                                        Cancel Transaction
                                    </a>
                                    
                                    <form action="{{ $cancel_transaction }}" method="POST" id="cancelling_transaction_post"></form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </main>
@endsection
