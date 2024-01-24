<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LAPORAN HARIAN KASIR RAWAT INAP</title>
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
        <div class="uk-align-center" style="padding:0px !important;background-color:red;">
            <div style="font-size:14px;font-weight:bold;text-align:center;padding:0px !important;">LAPORAN HARIAN KASIR RAWAT INAP</div>
        </div>
        <!-- Data Kasir -->
        <table class="uk-table uk-table-small no-border-table" style="margin-top:-15px;margin:auto;width:90%;font-size:10px !important;">
            <tr>
                <td style="font-weight:bold;width:10%;">Tgl Transaksi</td>
                <td style="width: 1%;">: </td>
                <td style="text-align: left;">{{ $data['tgl_awal'] }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Nama Kasir</td>
                <td>: </td>
                <td>{{ $data['kasir'] }}</td>
            </tr>
        </table>
        <br>
        <!-- Data Transaksi yang sudah dibayar -->
        <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin:auto;width:100%;font-size:9px !important;">
            <thead>
                <tr>
                    <th>No.Invoice</th>
                    <th rowspan="2" style="width: 15%">Nama Pasien</th>
                    <th rowspan="2" style="width: 10%">Total Biaya</th>
                    <th rowspan="2" style="width: 10%">Diskon</th>
                    <th rowspan="2" style="width: 10%">Kartu</th>
                    <th rowspan="2" style="width: 10%">Jaminan</th>
                    <th rowspan="2" style="width: 10%">Perorangan</th>
                    <th rowspan="2" style="width: 10%">Jumlah</th>
                </tr>
                <tr>
                    <th>No.Registrasi</th>
                </tr>
                
            </thead>
            <tbody>
                @php
                    $data               = json_decode($data['report']);
                    $totalBiaya         = 0;
                    $totalDiskon        = 0;
                    $totalKartu         = 0;
                    $totalJaminan       = 0;
                    $totalPerorangan    = 0;
                    $totalBayar         = 0;
                @endphp
                @foreach ($data as $detail)
                    <tr>
                        <td style="">
                            {{ $detail->trx_id }} 
                        </td>
                        <td rowspan="2" style="">
                            {{ $detail->nama_pasien }}
                        </td>
                        <td rowspan="2" style="text-align:right !important;">
                            {{ number_format(floatval($detail->jumlah*$detail->nilai),2) }}
                        </td>
                        <td rowspan="2" style="text-align:right !important;">
                            {{ number_format(floatval($detail->diskon),2) }}
                        </td>
                        <td rowspan="2" style="text-align:right !important;">
                            {{ number_format(floatval(0),2) }}
                        </td>
                        <td rowspan="2" style="text-align:right !important;">
                            {{ number_format(floatval(0),2) }}
                        </td>
                        <td rowspan="2" style="text-align:right !important;">
                            {{ number_format(floatval(0),2) }}
                        </td>
                        <td rowspan="2" style="text-align:right !important;">
                            {{ number_format(floatval($detail->subtotal),2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{ $detail->reg_id }}
                        </td>
                    </tr>
                    @php
                        $totalBiaya         = $totalBiaya + ($detail->jumlah*$detail->nilai);
                        $totalDiskon        = $totalDiskon + $detail->diskon;
                        $totalKartu         = 0;
                        $totalJaminan       = 0;
                        $totalPerorangan    = 0;
                        $totalBayar         = $totalBayar + $detail->subtotal;
                    @endphp
                
                @endforeach
                <tr>
                    <td colspan="2" style="text-align:right !important;">TOTAL</td>
                    <td style="text-align:right !important;">
                        {{ number_format(floatval($totalBiaya),2) }}
                    </td>
                    <td style="text-align:right !important;">
                        {{ number_format(floatval($totalDiskon),2) }}
                    </td>
                    <td style="text-align:right !important;">
                        {{ number_format(floatval($totalKartu),2) }}
                    </td>
                    <td style="text-align:right !important;">
                        {{ number_format(floatval($totalJaminan),2) }}
                    </td>
                    <td style="text-align:right !important;">
                        {{ number_format(floatval($totalPerorangan),2) }}
                    </td>
                    <td style="text-align:right !important;">
                        {{ number_format(floatval($totalBayar),2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
