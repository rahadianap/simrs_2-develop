<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- styling -->
    <link rel="stylesheet" href="{{url('css/report/style.css')}}">
    <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">

    <title>component kopsurat</title>
</head>
<body>
    <div class="uk-container uk-flex uk-flex-middle" style="margin-top:10px;" ref="kop-surat">
        <div class="uk-width-1-1">
            <div class="uk-flex-center uk-text-center">
                <p style="font-size:20px;font-weight:bold;margin-bottom:2px;">{{ $data['central']['client_nama'] }}</p>
                <p style="font-size:10px;" class="uk-margin-remove" >{{ $data['central']['alamat'] }}</p>
                <p style="font-size:10px;" class="uk-margin-remove" >Telp. {{ $data['central']['no_telepon'] }} | Email: {{ $data['central']['email'] }}</p>
            </div>
        </div>
    </div>
</body>
</html>