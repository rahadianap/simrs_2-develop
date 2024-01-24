<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetakan Uang Muka Pasien</title>
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
        @include('components.kopSurat')
        {{-- JUDUL --}}
        <!-- Judul Laporan -->
        <div class="uk-align-center uk-margin-remove">
            <div>
                <p class="uk-margin-remove" style="font-size:14px;font-weight:bold;text-align:center;font-family: verdana-bold;letter-spacing:1.1px;">CETAKAN FORMULIR INFORMASI PASIEN</p>
            </div>
        </div>
        <hr  style="border-top:white;">
        {{-- ISI --}}
        <table style="width:100%;font-size:10px;letter-spacing:0.5px;" class="uk-margin-small">
            <tr>
                <td style="font-family:cour;font-weight:bold;">No. Registrasi</td>
                <td style="font-family:cour">: {{ $datas['no_reg'] }}</td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">Nama Pasien</td>
                <td style="font-family:cour">: {{ $datas['nama_pasien'] }}</td>
                <td style="font-family:cour;font-weight:bold;">No. RM</td>
                <td style="font-family:cour">: {{ $datas['no_rm'] }}</td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">Kelas </td>
                <td style="font-family:cour">: {{ $datas['kelas'] }} </td>
                <td style="font-family:cour;font-weight:bold;">No.Tempat Tidur</td>
                <td style="font-family:cour">: {{ $datas['no_tempat_tidur'] }}</td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">Ruang Rawat</td>
                <td style="font-family:cour">: {{ $datas['ruang_perawatan'] }}</td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">No. Identitas</td>
                <td style="font-family:cour">: {{ $datas['no_identitas'] }}</td>
            </tr>
            <tr>
                <td colspan="4"><hr  style="border-top:white;"></td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">Nama Penanggung</td>
                <td style="font-family:cour">: {{ $datas['nama_penanggung'] }}</td>
                <td style="font-family:cour;font-weight:bold;">Hubungan</td>
                <td style="font-family:cour">: {{ $datas['hub_penanggung'] }}</td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">Alamat</td>
                <td style="font-family:cour">: {{ $datas['alamat_penanggung'] }}</td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">No. Telpn</td>
                <td style="font-family:cour">: {{ $datas['telp_penanggung'] }} </td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">No. Identitas</td>
                <td style="font-family:cour">: {{ $datas['no_identitas_penanggung'] }}</td>
            </tr>
            <tr>
                <td colspan="4"><hr  style="border-top:white;"></td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">- Total Uang Muka</td>
                <td style="font-family:cour">: Rp {{ $datas['total_uang_muka'] }}</td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">- Pembayaran Sekarang</td>
                <td style="font-family:cour">: Rp {{ $datas['pembayaran'] }}</td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">- Sisa Uang Muka Yang Ditunda</td>
                <td style="font-family:cour">: Rp {{ $datas['sisa_uang_muka'] }}</td>
            </tr>
            <tr>
                <td colspan="4"><hr  style="border-top:white;"></td>
            </tr>
            <tr>
                <td colspan="4" style="font-family:cour;font-weight:bold;">Perjanjian pembayaran kekurangan uang muka tidak melebihi 24 jam setelah pasien dalam perawatan</td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;">Hari / Tanggal</td>
                <td style="font-family:cour;font-weight:bold;">: {{ $datas['hari_dp'] }} / {{ $datas['tgl_dp'] }}</td>
            </tr>
        </table>
        <table style="width:100%;font-size:10px;letter-spacing:0.5px;padding-top:40px;" class="uk-margin-small">
            <tr>
                <td style="font-family:cour;font-weight:bold;width:33%;text-align:center;">Petugas Administrasi</td>
                <td style="font-family:cour;font-weight:bold;width:33%;text-align:center;">Pemohon</td>
                <td style="font-family:cour;font-weight:bold;width:33%;text-align:center;">Disetujui</td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;height:50px;"></td>
                <td style="font-family:cour;font-weight:bold;height:50px;"></td>
                <td style="font-family:cour;font-weight:bold;height:50px;"></td>
            </tr>
            <tr>
                <td style="font-family:cour;font-weight:bold;text-align:center;">...</td>
                <td style="font-family:cour;font-weight:bold;text-align:center;">...</td>
                <td style="font-family:cour;font-weight:bold;text-align:center;">...</td>
            </tr>
        </table>
    </body>
</html>
