<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FORM PENEMPATAN KELAS RAWAT INAP</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
        <style>
            table.standard-table tr td, 
            table.standard-table tr th{
                border:1px solid black;
                padding:2px 5px !important;
            }
            table.no-border tr td{
                padding:0.5px !important;
            }
        </style>
        <!-- end styling -->
    </head>
    <body>
        {{-- KOP SURAT --}}
        @include('components.kopSurat')
        {{-- JUDUL --}}
        <br>
        <div class="uk-align-center uk-margin-remove">
            <div>
                <p class="uk-margin-remove" style="font-size:20px;font-weight:bold;text-align:center;text-decoration-line:underline;">FORM PENEMPATAN KELAS RAWAT INAP</p>
            </div>
        </div>
        {{-- ISI --}}
        <div class="uk-grid-small uk-align-center" uk-grid>
            <table class="uk-table uk-table-small no-border" style="margin-top:-15px;margin-left:auto;margin-right:auto;width:85%;font-size:12px !important;">
                <tr>
                    {{-- KIRI --}}
                    <td style="width:30%;">No.Registrasi </td>
                    <td style="width:2%;">:</td>
                    <td style="width:30%;">{{ $data['inap']['reg_id'] }} </td>
                    {{-- KANAN --}}
                    <td style="width:10%;">&nbsp;&nbsp;&nbsp; No.RM </td>
                    <td style="width:2%;">:</td>
                    <td style="width:30%;">{{ $data['inap']['no_rm'] }} </td>
                </tr>
                <tr>
                    {{-- KIRI --}}
                    <td>Nama Pasien</td>
                    <td>:</td>
                    <td colspan="4">{{ $data['inap']['nama_pasien'] }} </td>
                </tr>
                <tr>
                    {{-- KIRI --}}
                    <td>Penjamin</td>
                    <td>:</td>
                    <td colspan="4">{{ $data['inap']['penjamin_nama'] }} </td>
                </tr>
                <tr>
                    {{-- KIRI --}}
                    <td>Tanggal | Jam Masuk</td>
                    <td>:</td>
                    <td colspan="4">{{ $data['inap']['tgl_masuk'] . " | " . $data['inap']['jam_masuk'] }} </td>
                </tr>
                <tr>
                    {{-- KIRI --}}
                    <td style="width:15%;">Hak Kelas Perawatan </td>
                    <td style="width:2%;">:</td>
                    <td style="width:30%;">{{ $data['inap']['no_rm'] }} </td>
                    {{-- KANAN --}}
                    <td style="width:15%;"> &nbsp;&nbsp;&nbsp; No.Bed </td>
                    <td style="width:2%;">:</td>
                    <td style="width:30%;">{{ $data['inap']['no_bed'] }} </td>
                </tr>
                <tr>
                    {{-- KIRI --}}
                    <td>Kelas | Ruang Rawat</td>
                    <td>:</td>
                    <td colspan="4">{{ $data['inap']['kelas_harga'] . " | " . $data['inap']['ruang_nama'] }} </td>
                </tr>
                <tr>
                    {{-- KIRI --}}
                    <td>Alasan Mutasi</td>
                    <td>:</td>
                    <td colspan="4"> </td>
                </tr>
            </table>
            <br><br>
            <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin-left:auto;margin-right:auto;width:85%;font-size:12px !important;">
                <tr>
                    <td style="border: 1px solid black;width:4%;"></td>
                    <td style="border: none"> &nbsp;&nbsp;&nbsp; Kehendak sendiri dan seluruh biaya ditanggung pribadi</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: none"> &nbsp;&nbsp;&nbsp; Kehendak sendiri dan selisih biaya kamar ditanggung pribadi (diluar kamar ditanggung Penjamin)</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: none"> &nbsp;&nbsp;&nbsp; Seluruh selisih biaya ditanggung perusahaan penjamin</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: none"> &nbsp;&nbsp;&nbsp; Hak kelas penuh maka setelah 2 x 24 jam seluruh biaya ditanggung pribadi</td>
                </tr>
            </table>
            <br><br><br>
            <table class="uk-table uk-table-small no-border" style="margin-top:-15px;margin-left:auto;margin-right:auto;width:85%;font-size:12px !important;">
                <tr>
                    {{-- KIRI --}}
                    <td style="text-align:left;">
                        Tanda tangan pasien / Penanggung Jawab 
                        <br>
                        Jakarta, {{ date('d F Y') }}
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        (<b>{{ $data['inap']['nama_penanggung'] }}</b>)
                    </td>
                    {{-- KANAN --}}
                    <td style="text-align:center;">
                        Dibuat oleh,
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        ({{ $data['user'] }}) {{-- Nama sementara dan nanti akan diganti dengan nama username --}}
                        <br>
                        {{ date('d-m-Y H:i:s') }}
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
