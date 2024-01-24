<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetakan Pendaftaran</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
        <style>
            table tr td {
                padding:0px 0px !important;
                text-align: center;
            }
        </style>
        <!-- end styling -->
    </head>
    <body>
        {{-- KOP SURAT --}}
        @include('components.kopSuratv2')
        {{-- JUDUL --}}
        <div class="uk-align-center uk-margin-remove">
            <div>
                <p class="uk-margin-remove" style="font-size:14px;font-weight:bold;text-align:center;text-decoration-line: underline;">BUKTI PENDAFTARAN</p>
            </div>
        </div>
        {{-- ISI --}}
        <div class="uk-grid-small uk-align-center" uk-grid>
            <table style="margin-left:auto;margin-right:auto;width:65%;">
                <tr>
                    <td style="font-style:italic;">{{ $data['pendaftaran']['dokter_nama'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;font-size:26px;">{{ $data['pendaftaran']['no_antrian'] }}</td>
                </tr>
                <tr>
                    <td>{{ $data['pendaftaran']['reg_id'] }}</td>
                </tr>
                <tr>
                    <td>{{ $data['pendaftaran']['no_rm'] }}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;">{{ $data['pendaftaran']['nama_pasien'] }}</td>
                </tr>
                <tr>
                    <td>{{ $data['pendaftaran']['jns_penjamin'] }}</td>
                </tr>
                <tr>
                    <td>
                        @php 
                            echo date("d/m/Y h:m:s");
                        @endphp
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
