<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetakan Formulir Informasi Pasien</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
        <style>
            table tr td {
                padding:0px 0px !important;
                vertical-align:top;
            }
        </style>
        <!-- end styling -->
    </head>
    <body>
        {{-- KOP SURAT --}}
        @include('components.kopSurat')
        {{-- JUDUL --}}
        <div class="uk-align-center" style="padding:0px !important;">
            <div style="font-size:18px;font-weight:bold;text-align:center;padding:0px !important;text-decoration:underline;">CETAKAN FORMULIR INFORMASI PASIEN</div>
        </div>
        <hr style="width:90%;margin:auto;">
        {{-- ISI --}}
        <table style="width:90%;font-size:10;margin:auto;font-size:10px;" class="uk-margin-small">
            <tr>
                <td style="width:15%;font-weight:bold;">No.RM</td>
                <td style="width:35%;">: {{ $data['no_rm'] }}</td>
                <td style="width:15%;font-weight:bold;">Status Rawat</td>
                <td style="width:35%;">: {{ $data['status_rawat'] }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Nama Pasien</td>
                <td style="">: {{ $data['nama_pasien'] }}</td>
                <td style="font-weight:bold;">Dokter Rawat</td>
                <td style="">: {{ $data['dokter_utama'] }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Tanggal Lahir</td>
                <td style="">: {{ $data['tgl_lahir'] }} </td>
                <td style="font-weight:bold;">Pejamin</td>
                <td style="">: {{ $data['jns_penjamin'] }} / {{ $data['penjamin_nama'] }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Usia</td>
                <td style="">: {{ $data['usia'] }} tahun </td>
                <td style="font-weight:bold;">Kelas / No.Tempat Tidur</td>
                <td style="">: {{ $data['kelas'] }} / {{ $data['no_tempat_tidur'] }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Tanggal Rawat</td>
                <td style="">: {{ $data['tgl_masuk'] }}</td>
                <td style="font-weight:bold;;">Hak Kelas</td>
                <td style="">: {{ $data['hak_kelas'] }}</td>                
            </tr>
            <tr>
                <td rowspan="2" style="font-weight:bold;">Alamat</td>
                <td rowspan="2" style="">: {{ $data['alamat'] }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Tanggal Keluar</td>
                <td style="">: {{ $data['tgl_keluar'] }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td style="font-weight:bold;">Alasan Pulang</td>
                <td style="">: {{ $data['alasan_pulang'] }}</td>
            </tr>
        </table>
    </body>
</html>
