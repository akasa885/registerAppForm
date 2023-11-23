@extends('layouts.mail')
@section('mail_content')
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="border:none;">
        <tr>
            <td width="100%" style="background-color: #f25454; color: #FFFFFF;">
                <p class="heading"
                    style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; line-height: 1.5em; text-align: center;">
                    {{ __('mail.payment.title-confirmation') }}
                </p>
            </td>
        </tr>
        <tr>
            <td class="body" width="100%" cellpadding="0" cellspacing="0"
                style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; width: 100%; ">
                <table class="wrapper" cellpadding="0" cellspacing="0" width="100%"
                    style="border: none; background-color: #ffffff; border-bottom: 1px solid #e8e5ef; border-top: 1px solid #e8e5ef; padding: 1.5em;">
                    <tr>
                        <td style="border:none;">
                            <p>{{ __('mail.template.to') }} : <strong>{{ __(ucwords($data['name'])) }}</strong> </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;">
                            <p>{{ __('mail.template.registered-on') }} <strong>{{ $data['acara'] }}</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;">
                            <p>{!! $data['message'] !!}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;">
                            <h4><strong>Link konfirmasi pembayaran : </strong></h4>
                            <h5> <strong>Batas Waktu : {{ date('d-m-Y H:i', strtotime($data['valid_until'])) }}</strong> </h5>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;text-align:center;">
                            <a href="{{ $data['link_pay'] }}" target="_blank"
                                style="font-size: 14px; padding: 10px 15px; background-color:darkcyan; text-align: center;
                                text-decoration: none; color: #FFF; width: 100%; border-radius: 10px; ">
                                {{ __('mail.template.btn-text-payment') }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;">
                            <p>{{ __('mail.template.text-link-to-copy') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;">
                            <a href="{{ $data['link_pay'] }}">{{ $data['link_pay'] }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;">
                            <table class="wrapper" width="100%" style="border:none;">
                                <tr>
                                    <td>
                                        <p><strong>{{ __('mail.template.footer-thanks') }}</strong></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <table width="100%">
                <tr>
                    <td>
                        <p>@include('includes.mail_footer')</p>
                    </td>
                </tr>
            </table>
        </tr>
    </table>
@endsection
