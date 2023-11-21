@extends('layouts.mail')
@section('mail_content')
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="border:none;">
        <tr>
            <td width="100%" style="background-color: #f25454; color: #FFFFFF;">
                <p class="heading"
                    style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; line-height: 1.5em; text-align: center;">
                    Bukti Bayar Diterima
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
                            <p>Kepada : <strong>{{ __(ucwords($data['name'])) }}</strong> </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;">
                            <p>Yang mendaftar pada acara <strong>{{ $data['acara'] }}</strong></p>
                        </td>
                    </tr>
                    @if (isset($data['token']))
                    <tr>
                        <td style="border:none;">
                            <p>Kode Invoice: <strong>{{ $data['token'] }}</strong></p>
                            <p>Order Number:  <strong>{{ $data['order'] }}</strong></p>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="border:none;">
                            <p>{!! $data['message'] !!}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;">
                            <table class="wrapper" width="100%" style="border:none;">
                                <tr>
                                    <td>
                                        <p><strong>Terima Kasih Telah Menggunakan Layanan Kami !</strong></p>
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
