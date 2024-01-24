<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <div class="uk-container uk-flex uk-flex-middle" style="margin-top:10px;" ref="kop-surat">
            <div class="uk-width-1-4" style="float:left;">
                <img src="{{ $data['central']['url_logo'] }}" class="logo-pdf uk-text-center uk-responsive-width" style="width:160px!important;height:80px !important;margin-left:-27px;">
            </div>
            <div class="uk-width-3-4" style="margin-left:-12%;">
                <div class="uk-flex-center uk-text-center">
                    <p style="font-size:22px;font-weight:bold;margin-bottom:2px;">{{ $data['central']['client_nama'] }}</p>
                    <p style="font-size:14px;" class="uk-margin-remove" >{{ $data['central']['alamat'] }}</p>
                    <p style="font-size:14px;" class="uk-margin-remove" >Telp. {{ $data['central']['no_telepon'] }} | Email: {{ $data['central']['email'] }}</p>
                </div>
            </div>
        </div>
        <hr style="border:0.1px solid #000000;margin:auto;margin-top:20px;margin-bottom:10px;width:95%;"> 
        {{-- JUDUL --}}
        <div class="uk-align-center uk-margin-remove">
            <div>
                <p class="uk-margin-remove" style="font-size:14px;font-weight:bold;text-align:center;text-decoration-line: underline;">LAPORAN KUNJUNGAN PASIEN LABORATORIUM</p>
                <div style="font-size:11px;text-align:center;font-family:verdana;padding:0px !important;">Periode : {{ $data['tgl_awal'] . ' s/d ' .  $data['tgl_akhir'] }}</div>
            </div>
        </div>
        <br>
        <hr style="border:0.1px solid #000000;margin:auto;margin-top:20px;width:95%;">   
        <br/>
        <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin:auto;width:100%;font-size:9px !important;">
            <thead>
                <th>Tanggal</th>
                {{-- <th>Transaksi ID</th> --}}
                <th>No.RM</th>
                <th>Pasien</th>
                <th>Dokter</th>
                <th>Unit</th>
                <th>Penjamin</th>
                <th>Status</th>
            </thead>
            <tbody>
            @foreach ($data['detail'] as $index => $detail)
                <tr>
                    {{-- <td style="padding-left:5px;text-align:center;">{{ date('d-m-Y', strtotime($detail['tgl_periksa'])) }} {{ $detail['jam_periksa'] }}</td> --}}
                    <td style="padding-left:5px;text-align:center;">{{ date('d-m-Y', strtotime($detail['tgl_periksa'])) }}</td>
                    {{-- <td style="padding-left:5px;">{{ $detail['trx_id'] }}</td> --}}
                    <td style="padding-left:5px;text-align:center;">{{ $detail['no_rm'] }}</td>
                    <td style="padding-left:5px;">{{ $detail['nama_pasien'] }}</td>
                    <td style="padding-left:5px;">{{ $detail['dokter_nama'] }}</td>
                    <td style="padding-left:5px;">{{ $detail['unit_nama'] }}</td>
                    <td style="padding-left:5px;">{{ $detail['penjamin_nama'] }}</td>
                    <td style="padding-left:5px;text-align:center;">{{ $detail['status'] }}</td>
                    
                </tr>
            @endforeach
            </tbody>
        </table>
    </body>
</html>
