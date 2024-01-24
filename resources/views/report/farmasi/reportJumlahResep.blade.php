<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Jumlah Resep</title>
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
            <div style="font-size:14px;font-weight:bold;text-align:center;font-family: verdana-bold;padding:0px !important;">LAPORAN JUMLAH RESEP</div>
            <div style="font-size:11px;text-align:center;font-family:verdana;padding:0px !important;">Periode : {{ $data['tgl_awal'] . " s/d " .  $data['tgl_akhir']}}</div>
        </div>
        <!-- Data Transaksi / Tindakan -->
        <table class="uk-table uk-table-small" style="margin-top:-15px;">
            <thead>
                <th>ID</th>
                <th>Tgl. Resep</th>
                <th>Nama Pasien</th>
                <th>Peresep</th>
                <th>Total</th>
            </thead>

            <tbody style="font-size:11px;font-family: cour;">
                @foreach ($data['report'] as $index => $value)
                <tr>
                    <td style="font-weight:bold;">
                        {{ $value->trx_id }}
                    </td>
                    <td>
                        {{ $value->tgl_resep }}
                    </td>
                    <td>
                        {{ $value->nama_pasien }}
                    </td>
                    <td>
                        {{ $value->peresep }}
                    </td>
                    <td>
                        {{ $value->total }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
