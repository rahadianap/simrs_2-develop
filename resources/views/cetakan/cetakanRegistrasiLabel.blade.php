<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cetakan - Registrasi Label</title>
        <style>
            table tr td {
                padding: 0px !important;
            }
        </style>
    </head>
    <body>
        <table style="font-size:8px;transform:rotate(270deg);margin-left:-60px;margin-top:1.5in;width:200px;border:none;font-weight:bold;">
            <tr>
                <td colspan="2">
                    {{ $data['nama_pasien'] }}
                </td>
            </tr>
            <tr>
                <td rowspan="4">
                    @php
                        echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG('000001','C39',1,28) . '" alt="barcode"   />';
                    @endphp
                </td>
                <td>
                    <tr>
                        <td style="font-size:6px;">
                            RM-1112233 (L)
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:6px;">
                            (TL-2/6/1978)
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:6px;">
                            44thn 8bl 6hr
                        </td>
                    </tr>
                </td>
            </tr>
        </table>  
    </body>
</html>
