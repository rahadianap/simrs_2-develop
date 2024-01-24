<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Cetakan Riwayat Pasien</title>

        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <!-- <link rel="stylesheet" href="{{url('css/hims.css')}}"> -->
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">

        <style>
            table.standard-table tr th, 
            table.standard-table tr td {
                padding: 1px 5px !important;
                border: 1px solid black;
                vertical-align:middle;
            }
            table.no-border-table tr td,
            table.no-border-table tr th {
                padding: 0px 5px !important;
                border: none;
            }
            table.standard-table thead th {
                font-size: 10px;
                font-weight: bold;         /* Make text bold */
                text-transform: capitalize; /* Capitalize each word */
            }
            table tr th {
                font-size:10px;
                text-align:center !important;
                font-weight:bold;
            }
        </style>
    </head>
    <body>
        <!-- kop surat -->
        @include('components.kopSurat')

        <!-- Judul Laporan -->
        <div class="uk-align-center uk-margin-remove">
            <div>
                <p class="uk-margin-remove" style="font-size:20px;font-weight:bold;text-align:center;letter-spacing:1.1px;">RINGKASAN RIWAYAT</p>
            </div>
            <div>
                <p class="uk-margin-remove" style="font-size:16px;text-align:center;letter-spacing:1.3px;">Tanggal Cetak : {{ $data['tgl_cetak'] }}</p>
            </div>
        </div>
        <br>
        <!-- Data Pasien -->
        <table class="uk-table uk-table-small no-border-table" style="margin-top:-15px;margin:auto;width:90%;font-size:10px !important;">
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;">No.RM</td>
                <td style="">: {{ $data['pasien']['no_rm'] }}</td>
                <!-- Kanan -->
                <td style="font-weight:bold;">No. Telpn</td>
                <td style="">: {{ $data['pasien']['no_telepon'] }}</td>
            </tr>
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;">Nama Pasien</td>
                <td style="">: {{ $data['pasien']['nama_pasien'] }}</td>
                <!-- Kanan -->
                <td style="font-weight:bold;">NIK</td>
                <td style="">: {{ $data['pasien']['nik'] }}</td>
            </tr>
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;">Jenis Kelamin</td>
                <td style="">: {{ $data['pasien']['jns_kelamin'] }}</td>
                <!-- Kanan -->
                <td style="font-weight:bold;;">Penjamin</td>
                <td style="">: {{ $data['pasien']['jns_penjamin'] }} / {{ $data['pasien']['penjamin_nama'] }}</td>
            </tr>
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;">Tempat / Tgl. Lahir</td>
                <td style="">: {{ $data['pasien']['tempat_lahir'] }} / {{ $data['pasien']['tgl_lahir'] }} </td>
                <!-- Kanan -->
                <td style="font-weight:bold;;">Usia</td>
                <td style="">: {{ $data['pasien']['usia'] }}</td>
            </tr>
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;">Alamat</td>
                <td colspan="3" style="">: {{ $data['pasien']['alamat_lengkap'] }}</td>
            </tr>
        </table>
        <br>
        <!-- Data Rriwayat Pasien -->
        <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin:auto;width:100%;font-size:9px !important;">
            <thead>
                <th style="font-weight:bold;">Tgl. Transaksi</th>
                <th style="font-weight:bold;">Jns. Transaksi</th>
                <th style="font-weight:bold;">Poliklinik</th>
                <th style="font-weight:bold;">Dokter</th>
                <th style="font-weight:bold;">Diagnosa</th>
                <th style="font-weight:bold;">Kode</th>
                <th style="font-weight:bold;">Keterangan</th>
            </thead>

            <tbody style="font-size:13px;font-family: cour;">
                @foreach ($data['riwayat'] as $riwayat)
                <tr>
                    <td>
                        {{ $riwayat['jns_transaksi'] }}
                    </td>
                    <td>
                        {{ $riwayat['unit_nama'] }}
                    </td>
                    <td>
                        {{ $riwayat['dokter_nama'] }}
                    </td>
                    <td>
                        {{-- {{ $riwayat['diagnosa'] }} --}}
                    </td>
                    <td>
                        {{-- {{ $riwayat['kode'] }} --}}
                    </td>
                    <td>
                        {{-- {{ $riwayat['keterangan'] }} --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>