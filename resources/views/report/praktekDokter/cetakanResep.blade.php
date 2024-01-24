<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetakan Resep</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
        <style>
            table tr td {
                padding:0px 0px !important;
	     line-height:0.95;
            }
        </style>
        <!-- end styling -->
    </head>
    <body>
        {{-- KOP SURAT --}}
        <div class="" style="margin-top:-3px;mergin-top:-20%;line-height:1;" ref="kop-surat">
            <div class="uk-width-1-1" style="margin:0;">
                <div class="uk-flex-center uk-text-center">
                    <p style="font-size:14px;font-weight:bold;margin-bottom:2px;">{{ $data['central']['client_nama'] }}</p>
                    <p style="font-size:11px;" class="uk-margin-remove" >{{ $data['central']['alamat'] }}</p>
                    <p style="font-size:11px;" class="uk-margin-remove" >Telp. {{ $data['central']['no_telepon'] }} | Email: {{ $data['central']['email'] }}</p>
                </div>
            </div>
        </div>
        <hr style="border:0.1px solid #000000;margin:auto;margin-top:20px;margin-bottom:10px;width:95%;"> 
        {{-- JUDUL --}}
        {{-- ISI --}}
        <table style="width:100%;font-size:10px;margin-top:0px !important;margin:auto;" class="uk-margin-small">
            <tr>
                <td style="width:40%;">Tanggal </td>
                <td style="width:3%;">: </td>
                <td>{{ $data['pasien']['tgl_transaksi'] }}</td>
            </tr>
            <tr>
                <td>Dokter</td>
                <td>: </td>
                <td>{{ $data['pasien']['dokter_nama'] }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>: </td>
                <td>{{ $data['pasien']['nama_pasien'] }}</td>
            </tr>
            <tr>
                <td>No. RM</td>
                <td>: </td>
                <td>{{ $data['pasien']['no_rm'] }}</td>
            </tr>
            <tr>
                <td>Tgl. Lahir / Umur</td>
                <td>: </td>
                <td>{{ $data['pasien']['tgl_lahir'] . ' / ' . $data['pasien']['umur'] }} thn</td>
            </tr>
            <tr>
                <td>Penjamin</td>
                <td>: </td>
                <td>{{ $data['pasien']['penjamin_nama'] }}</td>
            </tr>
            <tr>
                <td>Notes</td>
                <td>: </td>
                <td></td>
            </tr>
        </table>
        <table style="width:100%;font-size:10px;margin:auto;padding-right:15%;" class="uk-margin-small">
            @foreach($data['detail'] as $trx)
            <tr>
                <td colspan="2" style="width:90%;border-bottom:0.5px dashed black;">{{ $trx['item_nama'] }}</td>
            </tr>
            <tr>
                <td style="font-size:10px;">{{ $trx['signa_deskripsi'] }}</td>
                <td style="font-size:10px;">Jml : {{ $trx['jumlah'] }}</td>
            </tr>
            @endforeach
        </table>
    </body>
</html>
