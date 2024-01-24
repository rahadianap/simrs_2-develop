<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LAPORAN PENDAPATAN RAWAT JALAN</title>
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
            <div style="font-size:14px;font-weight:bold;text-align:center;padding:0px !important;">LAPORAN PENDAPATAN RAWAT JALAN</div>
        </div>
        <table class="uk-table uk-table-small no-border-table" style="margin-top:-15px;margin:auto;width:90%;font-size:10px !important;">
            <tr>
                <td style="font-weight:bold;width:15%;">Tgl Transaksi</td>
                <td style="width: 1%;">: </td>
                <td style="text-align: left;">{{ $data['tgl_awal'] }} s/d {{ $data['tgl_akhir'] }}</td>
            </tr>
        </table>
        <br>
        @php
            $data = json_decode($data['report']);
            $penjaminData = [];
        
            // Aggregate data by penjamin_nama
            foreach ($data as $transaction) {
                $penjaminData[$transaction->penjamin_nama][$transaction->jns_transaksi] = $transaction->subtotal;
            }
        @endphp
        
        <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin:auto;width:100%;font-size:9px !important;">
            <thead>
                <tr>
                    <th>No</th>
                    <th style="width: 35%">Nama Penjamin</th>
                    <th>Rawat Jalan</th>
                    <th>BHP</th>
                    <th>Laboratorium</th>
                    <th>Radiologi</th>
                    <th>Total</th>
                </tr>
            </thead>
            @php $rowNumber = 0; @endphp
            @foreach($penjaminData as $namaPenjamin => $transactions)
                @php
                    $rowNumber++;
                    $subtotalBHP = $transactions['BHP'] ?? 0;
                    $subtotalLAB = $transactions['LAB'] ?? 0;
                    $subtotalRadio = $transactions['RADIOLOGI'] ?? 0;
                    $subtotalTindakan = $transactions['TINDAKAN'] ?? 0;
                    $total = $subtotalBHP + $subtotalLAB + $subtotalRadio + $subtotalTindakan;
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $rowNumber }}</td>
                    <td>{{ $namaPenjamin }}</td>
                    <td style="text-align:right !important;">{{ $subtotalTindakan }}</td>
                    <td style="text-align:right !important;">{{ $subtotalBHP }}</td>
                    <td style="text-align:right !important;">{{ $subtotalLAB }}</td>
                    <td style="text-align:right !important;">{{ $subtotalRadio }}</td>
                    <td style="text-align:right !important;">{{ $total }}</td>
                </tr>
            @endforeach
        </table>        
    </body>
</html>
