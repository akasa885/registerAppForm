Kepada: {{ __(ucwords($data['name'])) }}

Terima kasih telah mendaftar pada acara {{ $data['acara'] }}

{!! $data['message'] !!}

Terima Kasih Telah Menggunakan Layanan Kami!

@include('includes.mail_footer_plain')