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
    <p>---------- Terima kasih telah menggunakan layanan kami. -----------</p>
    <p><a href="https://upquality.net">Website Kami.</a></p>
</body>
</html>