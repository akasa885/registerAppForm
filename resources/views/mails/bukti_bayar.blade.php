<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
    <p>Kepada : <strong>{{ucwords($data['name'])}}</strong></p>
    <p>Yang mendaftar pada acara <strong>{{$data['acara']}}</strong></p>
    {!!$data['message']!!}
    <h4><strong>Link konfirmasi pembayaran : </strong></h4>
    <h5> <strong>Batas Waktu : {{ date('d-m-Y H:i', strtotime($data['valid_until'])) }}</strong> </h5>
    <a href="{{$data['link_pay']}}" 
    style="font-size: 14px; padding: 10px 15px; background-color:darkcyan; text-align: center;
    text-decoration: none; color: #FFF; width: 100%; border-radius: 10px; ">
        Link Upload Bayar
    </a>
    <br/>
    <p>Atau anda bisa copy link dibawah ini..</p>
    <a href="{{$data['link_pay']}}">{{$data['link_pay']}}</a>
    <br/>
    <p>Terima Kasih..</p>

</body>
</html>