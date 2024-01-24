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
            .nominal {
                text-align: right;
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
            <div style="font-size:11px;text-align:center;font-family:verdana;padding:0px !important;">Periode : {{ $datas['periode'] }}</div>
        </div>
        <!-- Data Transaksi / Tindakan -->
        <table class="uk-table uk-table-small" style="margin-top:-15px;">
            <thead>
                <tr>
                    <th rowspan="2" style="font-size:10px;vertical-align:middle;">Tanggal</th>
                    <th style="font-size:10px;font-family: cour;" colspan="2">IGD</th>
                    <th style="font-size:10px;font-family: cour;" colspan="2">RWI</th>
                    <th style="font-size:10px;font-family: cour;" colspan="2">RWJ</th>
                    <th style="font-size:10px;font-family: cour;" colspan="2">Total</th>
                </tr>
                <tr>
                    <th style="font-size:10px;font-family: cour;">Harga</th>
                    <th style="font-size:10px;font-family: cour;">Diskon</th>
                    <th style="font-size:10px;font-family: cour;">Harga</th>
                    <th style="font-size:10px;font-family: cour;">Diskon</th>
                    <th style="font-size:10px;font-family: cour;">Harga</th>
                    <th style="font-size:10px;font-family: cour;">Diskon</th>
                    <th style="font-size:10px;font-family: cour;">Harga</th>
                    <th style="font-size:10px;font-family: cour;">Diskon</th>
                </tr>
            </thead>
            <tbody style="font-size:10px;font-family: cour;">
                @foreach (json_decode($datas['data']) as $data)
                    <tr>
                        <td style="text-align:center;">
                            {{ $data->tanggal }}
                        </td>
                        <td class="nominal">
                            {{ $data->igd_total_harga }}
                        </td>
                        <td class="nominal">
                            {{ $data->igd_diskon }}
                        </td>
                        <td class="nominal">
                            {{ $data->ri_total_harga }}
                        </td>
                        <td class="nominal">
                            {{ $data->ri_diskon }}
                        </td>
                        <td class="nominal">
                            {{ $data->rj_total_harga }}
                        </td>
                        <td class="nominal">
                            {{ $data->rj_diskon }}
                        </td>
                        <td class="nominal" style="font-weight:bold;">
                            {{ $data->total_harga }}
                        </td>
                        <td class="nominal" style="font-weight:bold;">
                            {{ $data->total_diskon }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td class="nominal" style="font-weight:bold;">Grand Total</td>
                    <td class="nominal" style="font-weight:bold;">{{ $datas['grand_total']['igd_total_harga'] }}</td>
                    <td class="nominal" style="font-weight:bold;">{{ $datas['grand_total']['igd_diskon'] }}</td>
                    <td class="nominal" style="font-weight:bold;">{{ $datas['grand_total']['ri_total_harga'] }}</td>
                    <td class="nominal" style="font-weight:bold;">{{ $datas['grand_total']['ri_diskon'] }}</td>
                    <td class="nominal" style="font-weight:bold;">{{ $datas['grand_total']['rj_total_harga'] }}</td>
                    <td class="nominal" style="font-weight:bold;">{{ $datas['grand_total']['rj_diskon'] }}</td>
                    <td class="nominal" style="font-weight:bold;">{{ $datas['grand_total']['total_harga'] }}</td>
                    <td class="nominal" style="font-weight:bold;">{{ $datas['grand_total']['total_diskon'] }}</td>
                </tr>
                {{-- Pengulangan data untuk contoh halaman lebih dari satu --}}
            </tbody>
            <tfoot>

            </tfoot>
        </table>
    </body>
</html>
