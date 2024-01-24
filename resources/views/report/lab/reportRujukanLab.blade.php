<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Rujukan Laboratorium</title>
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
            <div style="font-size:14px;font-weight:bold;text-align:center;font-family: verdana-bold;padding:0px !important;">LAPORAN RUJUKAN LABORATORIUM</div>
            <div style="font-size:11px;text-align:center;font-family:verdana;padding:0px !important;">Periode : {{ $data['tgl_awal'] . ' s/d ' . $data['tgl_akhir'] }}</div>
        </div>
        <!-- Data Transaksi / Tindakan -->
        @php
            $groupedData = collect($data['report'])->groupBy('dokter_nama')->map(function ($group) {
                return [
                    'dokter_nama' => $group->first()['dokter_nama'],
                    'poli' => $group->sum('poli'),
                    'ranap' => $group->sum('ranap'),
                    'igd' => $group->sum('igd'),
                    'walk_in' => $group->sum('walk_in'),
                ];
            })->values();
        @endphp
        <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin:auto;width:100%;font-size:9px !important;">
            <thead>
                <th>No</th>
                <th>Nama Dokter</th>
                <th>Poli</th>
                <th>Ranap</th>
                <th>IGD</th>
                <th>Walk In</th>
                <th>Total</th>
            </thead>
            <tbody>
                @foreach ($groupedData as $index => $data)
                    @php
                        $total = $data['poli'] + $data['ranap'] + $data['igd'] + $data['walk_in'];
                        $data['total'] = $total;
                    @endphp
                    <tr>
                        <td style="text-align:center !important;">
                            {{ $index+1 }}
                        </td>
                        <td>
                            {{ $data['dokter_nama'] }}
                        </td>
                        <td>
                            {{ $data['poli'] }}
                        </td>
                        <td>
                            {{ $data['ranap'] }}
                        </td>
                        <td>
                            {{ $data['igd'] }}
                        </td>
                        <td>
                            {{ $data['walk_in'] }}
                        </td>
                        <td>
                            {{ $data['total'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
