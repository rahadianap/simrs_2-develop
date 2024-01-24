<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Pasien Rujukan Dari Luar Detail</title>
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
            <div style="font-size:14px;font-weight:bold;text-align:center;font-family: verdana-bold;padding:0px !important;">LAPORAN PASIEN RUJUKAN DARI LUAR DETAIL</div>
            <div style="font-size:11px;text-align:center;font-family:verdana;padding:0px !important;">Nama Pengirim : {{ $data['nama_pengirim'] }}</div>
            <div style="font-size:11px;text-align:center;font-family:verdana;padding:0px !important;">Periode : {{ $data['periode'] }}</div>
        </div>
        <!-- Data Transaksi / Tindakan -->
        <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin:auto;width:100%;font-size:9px !important;">
            <thead>
                <th>No</th>
                <th>No. Reg</th>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Nama Unit</th>
                <th>Nama Dokter</th>
            </thead>

            <tbody>
                @foreach ($data['report'] as $index => $data)
                    <tr>
                        <td style="text-align:center !important;">
                            {{ $index+1 }}
                        </td>
                        <td>
                            {{ $data->reg_id }}
                        </td>
                        <td>
                            {{ $data->no_rm }}
                        </td>
                        <td>
                            {{ $data->nama_pasien }}
                        </td>
                        <td>
                            {{ $data->unit_asal }}
                        </td>
                        <td>
                            {{ $data->dokter_nama }}
                        </td>
                    </tr>
                @endforeach

                {{-- Pengulangan data untuk contoh halaman lebih dari satu --}}
                
            </tbody>
        </table>
    </body>
</html>
