<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetakan Tracer</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
        <style>
            table tr td {
                padding:0px 0px !important;
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
                <p class="uk-margin-remove" style="font-size:14px;font-weight:bold;text-align:center;text-decoration-line: underline;">TRACER</p>
                <p class="uk-margin-remove" style="font-size:26px;font-weight:bold;text-align:center;">{{ $data['pendaftaran']['no_antrian'] }}</p>
            </div>
        </div>
        {{-- ISI --}}
        <div class="uk-grid-small uk-align-center" uk-grid>
            <table style="margin-left:auto;margin-right:auto;width:90%;">
                <tr>
                    <td>Tgl. Cetak</td>
                    <td>:</td>
                    <td>
                        @php 
                            echo date("d/m/Y");
                        @endphp
                    </td>
                </tr>
                <tr>
                    <td>No.Reg</td>
                    <td>:</td>
                    <td>{{ $data['pendaftaran']['reg_id'] }}</td>
                </tr>
                <tr>
                    <td>No.RM</td>
                    <td>:</td>
                    <td>{{ $data['pendaftaran']['no_rm'] }}</td>
                </tr>
                <tr>
                    <td>Pasien</td>
                    <td>:</td>
                    <td>{{ $data['pendaftaran']['nama_pasien'] }}</td>
                </tr>
                <tr>
                    <td>Penjamin</td>
                    <td>:</td>
                    <td>{{ $data['pendaftaran']['penjamin_nama'] }}</td>
                </tr>
                <tr>
                    <td>Tgl. Registrasi</td>
                    <td>:</td>
                    <td>{{ $data['pendaftaran']['tgl_registrasi'] }}</td>
                </tr>
                <tr>
                    <td style="vertical-align:top;">Unit/Dokter</td>
                    <td style="vertical-align:top;">:</td>
                    <td>{{ $data['pendaftaran']['unit_nama'] }} / {{ $data['pendaftaran']['dokter_nama'] }}</td>
                </tr>
            </table>
        </div>
    </body>
</html>
