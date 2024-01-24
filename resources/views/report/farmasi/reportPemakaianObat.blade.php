<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LAPORAN PEMAKAIAN OBAT</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
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
        <!-- end styling -->
    </head>
    <body>
        {{-- KOP SURAT --}}
        @include('components.kopSurat')
        {{-- JUDUL --}}
        <!-- Judul Laporan -->
        <div class="uk-align-center" style="padding:0px !important;background-color:red;">
            <div style="font-size:14px;font-weight:bold;text-align:center;font-family: verdana-bold;padding:0px !important;">LAPORAN PEMAKAIAN OBAT {{ $datas['nama_obat'] }}</div>
            <div style="font-size:11px;text-align:center;font-family:verdana;padding:0px !important;">Penjamin : {{ $datas['penjamin'] }}</div>
            <div style="font-size:11px;text-align:center;font-family:verdana;padding:0px !important;">Periode : {{ $datas['periode'] }}</div>
        </div>
        <!-- Data Transaksi / Tindakan -->
        <table class="uk-table uk-table-small no-border-table" style="margin-top:-15px;margin:auto;width:90%;font-size:10px !important;">
            <thead>
                <th>No</th>
                <th>Tgl. Transaksi</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Usia</th>
                <th>Dokter</th>
            </thead>

            <tbody style="font-size:11px;font-family: cour;">
                @foreach ($datas['data'] as $index => $data)
                    <tr>
                        <td style="text-align:center !important;">
                            {{ $index+1 }}
                        </td>
                        <td style="text-align:center !important;">
                            {{ $data['tgl_transaksi'] }}
                        </td>
                        <td style="text-align:center !important;">
                            {{ $data['jumlah'] }}
                        </td>
                        <td>
                            {{ $data['satuan'] }}
                        </td>
                        <td>
                            {{ $data['no_rm'] }}
                        </td>
                        <td>
                            {{ $data['nama_pasien'] }}
                        </td>
                        <td>
                            {{ $data['usia'] }}
                        </td>
                        <td>
                            {{ $data['nama_dokter'] }}
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
                            {{ $data['tgl_transaksi'] }}
                        </td>
                        <td style="text-align:center !important;">
                            {{ $data['jumlah'] }}
                        </td>
                        <td>
                            {{ $data['satuan'] }}
                        </td>
                        <td>
                            {{ $data['no_rm'] }}
                        </td>
                        <td>
                            {{ $data['nama_pasien'] }}
                        </td>
                        <td>
                            {{ $data['usia'] }}
                        </td>
                        <td>
                            {{ $data['nama_dokter'] }}
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
                            {{ $data['tgl_transaksi'] }}
                        </td>
                        <td style="text-align:center !important;">
                            {{ $data['jumlah'] }}
                        </td>
                        <td>
                            {{ $data['satuan'] }}
                        </td>
                        <td>
                            {{ $data['no_rm'] }}
                        </td>
                        <td>
                            {{ $data['nama_pasien'] }}
                        </td>
                        <td>
                            {{ $data['usia'] }}
                        </td>
                        <td>
                            {{ $data['nama_dokter'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
