<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cetakan - Gelang Dewasa</title>
        <style>
            table tr td {
                padding: 0px !important;
            }
        </style>
    </head>
    <body>
        
        <table style="font-size:8px;transform:rotate(270deg);margin-left:-60px;margin-top:9in;width:200px;border:none;">
            <tr>
                <td colspan="3">
                    @php
                        echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG('000001','C39',1,33) . '" alt="barcode"   />';     //OK
                    @endphp
                </td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight:bold;font-size:10px !important;">{{ $datas['nama_pasien'] }}</td>
            </tr>
            <tr>
                <td style="width:20%;">No RM</td>
                <td style="width:2%;">:</td>
                <td>{{ $datas['no_rm'] }}</td>
            </tr>
            <tr>
                <td>Tgl Lahir</td>
                <td>:</td>
                <td>{{ $datas['tgl_lahir'] }}</td>
            </tr>
            <tr>
                <td>Umur</td>
                <td>:</td>
                <td>{{  $datas['umur'] }} Tahun</td>
            </tr>
        </table>  
    </body>
</html>
