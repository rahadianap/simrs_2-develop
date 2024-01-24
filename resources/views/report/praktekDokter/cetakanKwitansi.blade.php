<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetakan Kwitansi</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
        <style>
            table tr td {
                padding:0px 0px !important;
            }
        </style>
        <!-- end styling -->
    </head>
    <body>
        {{-- KOP SURAT --}}
        <div class="uk-container uk-flex uk-flex-middle" style="margin-top:10px;" ref="kop-surat">
            <div class="uk-width-1-4" style="float:right;">
                <img src="{{ $data['central']['url_logo'] }}" class="logo-pdf uk-text-center uk-responsive-width" style="width:160px!important;height:80px !important;margin-right:-20% !important;">
            </div>
            <div class="uk-width-3-4" style="margin-left:-15%;">
                <div class="uk-flex-center uk-text-center">
                    <p style="font-size:22px;font-weight:bold;margin-bottom:2px;">{{ $data['central']['client_nama'] }}</p>
                    <p style="font-size:14px;" class="uk-margin-remove" >{{ $data['central']['alamat'] }}</p>
                    <p style="font-size:14px;" class="uk-margin-remove" >Telp. {{ $data['central']['no_telepon'] }} | Email: {{ $data['central']['email'] }}</p>
                </div>
            </div>
        </div>
        <br/>
        {{-- JUDUL --}}
        <div class="uk-align-center uk-margin-remove">
            <div>
                <p class="uk-margin-remove" style="font-size:14px;font-weight:bold;text-align:center;text-decoration-line: underline;">BUKTI PEMBAYARAN</p>
                <p class="uk-margin-remove" style="font-size:9px;text-align:center;">Tgl Bayar : {{ $data['detail'][0]['tgl_bayar'] }} </p>
            </div>
        </div>
        {{-- ISI --}}
        <table style="width:60%;font-size:12px;margin:auto;" class="uk-margin-small">
            <tr>
                <td style="width:40%;">Sudah terima dari </td>
                <td style="width:3%;">: </td>
                <td>{{ $data['nama_pasien'] }}</td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>: </td>
                <td>{{ $data['nama_pasien'] }}</td>
            </tr>
            <tr>
                <td>No.Registrasi</td>
                <td>: </td>
                <td>{{ $data['reg_id'] }}</td>
            </tr>
            <tr>
                <td>Banyaknya uang</td>
                <td>: </td>
                <td>Rp. {{ number_format($data['jml_bayar'],2) }}</td>
            </tr>
            <tr>
                <td>Terbilang</td>
                <td>: </td>
                <td>{{ $data['terbilang'] }}</td>
            </tr>
        </table>
        <table style="width:60%;font-size:12px;margin:auto;padding-right:15%;" class="uk-margin-small">
            <tr>
                <td colspan="3">Transaksi</td>
            </tr>
            @foreach($data['detail'] as $trx)
            <tr>
                <td style="width:55.5%;">{{ $trx['jns_payment'] }}</td>
                <td style="width:4%;">: </td>
                <td style="width:3%;">Rp. </td>
                <td style="width:auto;text-align:right;">{{ number_format($trx['jml_bayar'],2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4"><hr style="border:1px solid #cc0202;width:100%;margin:auto;" /></td>
            </tr>
            <tr>
                <td style="width:55.5%;">Total</td>
                <td style="width:4%;">: </td>
                <td style="width:3%;">Rp. </td>
                <td style="width:auto;text-align:right;">{{ number_format($data['jml_bayar'],2) }}</td>
            </tr>
        </table>
        <br>
        <table style="width:auto;margin:auto;float:right;padding-right:20%;" class="uk-margin-small">
            <tr><td style="text-align:center;font-size:9px;">{{ date('d-m-Y', strtotime($data['detail'][0]['tgl_bayar'])) }}</td></tr>
            <tr><td style="text-align:center;font-size:12px;">Kasir, </td></tr>
            <tr><td style="height:60px;"></td></tr>
            <tr><td style="text-align:right;"></td></tr>
        </table>
    </body>
</html>
