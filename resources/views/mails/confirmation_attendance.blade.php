@extends('layouts.mail')
@section('mail_content')
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="border:none;">
        <tr>
            <td width="100%" style="background-color: #f25454; color: #FFFFFF;">
                <p class="heading"
                    style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; line-height: 1.5em; text-align: center;">
                    {{__('mail.title.attendance_confirmation')}}
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
                            <p>Informasi Anda :</strong> </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;">
                            <table class="wrapper" style="100%" style="border:none;">
                                <tr>
                                    <td><strong>Nama</strong></td>
                                    <td>:</td>
                                    <td>{{ $data['name'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>:</td>
                                    <td>{{ $data['email'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>No Telepon</strong></td>
                                    <td>:</td>
                                    <td>{{ $data['phone'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Acara</strong></td>
                                    <td>:</td>
                                    <td>{{ $data['event'] }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none;">
                            <p>{!! $data['message'] !!}</p>
                        </td>
                    </tr>
                    @if ($data['link_path'] == "4CyTZ")
                    <tr>
                        <td>
                            <table class="wrapper" width="100%" style="border:none;">
                                <tr>
                                    <td style="text-align:center;">
                                        <a href="{{ $urlComfirmation }}" target="_blank"
                                                    style="border:none; border-radius: 3px; box-sizing: border-box; color: #ffffff; cursor: pointer; display: inline-block; font-size: 13px; font-weight: bold; margin: 0; padding: 12px 25px; text-decoration: none; text-transform: capitalize; background-color: #f25454; border-color: #f25454; ">
                                                {{__('mail.button.attendance_confirmation')}}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif
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
