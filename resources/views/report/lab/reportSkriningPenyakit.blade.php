<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Rekap Pasien {{ $datas['skrining'] }}</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
        <style>
            table tr td, table tr th {
                padding:0px 5px !important;
                border:1px solid black;
            }
            table tr th {
                font-size:10px;font-family:cour;
                text-align:center !important;
                font-weight:bold;
            }
        </style>
        <!-- end styling -->
    </head>
    <body>
        {{-- KOP SURAT --}}
        @include('components.kopSurat')
        {{-- JUDUL --}}
        <!-- Judul Laporan -->
        <div class="uk-align-center" style="padding:0px !important;background-color:red;">
            <div style="font-size:14px;font-weight:bold;text-align:center;font-family: verdana-bold;padding:0px !important;">LAPORAN REKAP PASIEN {{ $datas['skrining'] }}</div>
            <div style="font-size:11px;text-align:center;font-family:verdana;padding:0px !important;">Periode : {{ $datas['periode'] }}</div>
        </div>
        <!-- Data Transaksi / Tindakan -->
        <table class="uk-table uk-table-small" style="margin-top:-15px;">
            <thead>
                <th>No</th>
                <th>Tanggal</th>
                <th>No.RM</th>
                <th>Nama Pasien</th>
                <th>No.Lab</th>
                <th>Unit</th>
                <th>Nama Dokter</th>
                <th>Tes</th>
            </thead>

            <tbody style="font-size:10px;font-family: cour;">
                @foreach ($datas['data'] as $index => $data)
                    <tr>
                        <td style="text-align:center !important;">
                            {{ $index+1 }}
                        </td>
                        <td style="text-align:center !important;">
                            {{ $data['tanggal_periksa'] }}
                        </td>
                        <td>
                            {{ $data['no_rm'] }}
                        </td>
                        <td>
                            {{ $data['nama_pasien'] }}
                        </td>
                        <td>
                            {{ $data['trx_id'] }}
                        </td>
                        <td>
                            {{ $data['unit_nama'] }}
                        </td>
                        <td>
                            {{ $data['dokter_nama'] }}
                        </td>
                        <td>
                            {{ $data['hasil_nama'] }}
                        </td>
                    </tr>
                @endforeach

                {{-- Pengulangan data untuk contoh halaman lebih dari satu --}}
                @foreach ($datas['data'] as $index => $data)
                    <tr>
                        <td style="text-align:center !important;">
                            {{ $index+11 }}
                        </td>
                        <td style="text-align:center !important;">
                            {{ $data['tanggal_periksa'] }}
                        </td>
                        <td>
                            {{ $data['no_rm'] }}
                        </td>
                        <td>
                            {{ $data['nama_pasien'] }}
                        </td>
                        <td>
                            {{ $data['trx_id'] }}
                        </td>
                        <td>
                            {{ $data['unit_nama'] }}
                        </td>
                        <td>
                            {{ $data['dokter_nama'] }}
                        </td>
                        <td>
                            {{ $data['hasil_nama'] }}
                        </td>
                    </tr>
                @endforeach
                {{-- Pengulangan data untuk contoh halaman lebih dari satu --}}
                @foreach ($datas['data'] as $index => $data)
                    <tr>
                        <td style="text-align:center !important;">
                            {{ $index+21 }}
                        </td>
                        <td style="text-align:center !important;">
                            {{ $data['tanggal_periksa'] }}
                        </td>
                        <td>
                            {{ $data['no_rm'] }}
                        </td>
                        <td>
                            {{ $data['nama_pasien'] }}
                        </td>
                        <td>
                            {{ $data['trx_id'] }}
                        </td>
                        <td>
                            {{ $data['unit_nama'] }}
                        </td>
                        <td>
                            {{ $data['dokter_nama'] }}
                        </td>
                        <td>
                            {{ $data['hasil_nama'] }}
                        </td>
                    </tr>
                @endforeach
                {{-- Pengulangan data untuk contoh halaman lebih dari satu --}}
                @foreach ($datas['data'] as $index => $data)
                    <tr>
                        <td style="text-align:center !important;">
                            {{ $index+31 }}
                        </td>
                        <td style="text-align:center !important;">
                            {{ $data['tanggal_periksa'] }}
                        </td>
                        <td>
                            {{ $data['no_rm'] }}
                        </td>
                        <td>
                            {{ $data['nama_pasien'] }}
                        </td>
                        <td>
                            {{ $data['trx_id'] }}
                        </td>
                        <td>
                            {{ $data['unit_nama'] }}
                        </td>
                        <td>
                            {{ $data['dokter_nama'] }}
                        </td>
                        <td>
                            {{ $data['hasil_nama'] }}
                        </td>
                    </tr>
                @endforeach
                {{-- Pengulangan data untuk contoh halaman lebih dari satu --}}
                @foreach ($datas['data'] as $index => $data)
                    <tr>
                        <td style="text-align:center !important;">
                            {{ $index+41 }}
                        </td>
                        <td style="text-align:center !important;">
                            {{ $data['tanggal_periksa'] }}
                        </td>
                        <td>
                            {{ $data['no_rm'] }}
                        </td>
                        <td>
                            {{ $data['nama_pasien'] }}
                        </td>
                        <td>
                            {{ $data['trx_id'] }}
                        </td>
                        <td>
                            {{ $data['unit_nama'] }}
                        </td>
                        <td>
                            {{ $data['dokter_nama'] }}
                        </td>
                        <td>
                            {{ $data['hasil_nama'] }}
                        </td>
                    </tr>
                @endforeach
                {{-- Pengulangan data untuk contoh halaman lebih dari satu --}}
                @foreach ($datas['data'] as $index => $data)
                    <tr>
                        <td style="text-align:center !important;">
                            {{ $index+51 }}
                        </td>
                        <td style="text-align:center !important;">
                            {{ $data['tanggal_periksa'] }}
                        </td>
                        <td>
                            {{ $data['no_rm'] }}
                        </td>
                        <td>
                            {{ $data['nama_pasien'] }}
                        </td>
                        <td>
                            {{ $data['trx_id'] }}
                        </td>
                        <td>
                            {{ $data['unit_nama'] }}
                        </td>
                        <td>
                            {{ $data['dokter_nama'] }}
                        </td>
                        <td>
                            {{ $data['hasil_nama'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
