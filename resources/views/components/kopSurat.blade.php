<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- styling -->
    <link rel="stylesheet" href="{{url('css/report/style.css')}}">
    <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
</head>
<body>
    <div class="uk-container uk-flex uk-flex-middle" style="margin-top:10px;" ref="kop-surat">
        <div class="uk-width-1-4" style="float:left;">
            <img src="{{ $data['central']['url_logo'] }}" class="logo-pdf uk-text-center uk-responsive-width" style="width:160px!important;height:80px !important;margin-left:-27px;">
        </div>
        <div class="uk-width-3-4" style="margin-left:-12%;">
            <div class="uk-flex-center uk-text-center">
                <p style="font-size:22px;font-weight:bold;margin-bottom:2px;"> {{ $data['central']['client_nama'] }}</p>
                <p style="font-size:14px;" class="uk-margin-remove" >{{ $data['central']['alamat'] }}</p>
                <p style="font-size:14px;" class="uk-margin-remove" >Telp. {{ $data['central']['no_telepon'] }} | Email: {{ $data['central']['email'] }}</p>
            </div>
        </div>
    </div>
    <hr style="border:0.1px solid #000000;margin:auto;margin-top:20px;width:95%;">                                                           
</body>
</html>