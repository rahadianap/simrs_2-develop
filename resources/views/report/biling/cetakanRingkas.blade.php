<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetakan Ringkas Tagihan</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
        <style>
            table.standard-table tr td{
                padding: 1.2px 5px !important;
            }
            table.standard-table tr th, 
            table tr td.bordered {
                padding: 1px 5px !important;
                border: 1px solid black;
            }
            table.no-border-table tr td,
            table.no-border-table tr th {
                padding: 0px 5px !important;
                border: none;
            }
            table.standard-table thead th {
                font-size: 11px;
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
        <hr style="border:0.1px solid #000000;margin:auto;margin-top:20px;width:95%;"> 
        
        <div class="uk-align-center" style="padding:0px !important;background-color:red;">
            <div style="font-size:14px;font-weight:bold;text-align:center;padding:0px !important;">REKAP RINCIAN TAGIHAN PASIEN</div>
            <div style="font-size:11px;text-align:center;padding:0px !important;">Tgl Transaksi : {{$data['pendaftaran']['tgl_registrasi']}} </div>
        </div>
        <table class="uk-table uk-table-small no-border-table" style="margin-top:-15px;margin:auto;width:90%;font-size:10px !important;">
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;">No.Reg / No.RM</td>
                <td>: {{ $data['pendaftaran']['reg_id'] }} | {{ $data['pendaftaran']['no_rm'] }}</td>
                <!-- Kanan -->
                <td style="font-weight:bold;">Penjamain Bayar</td>
                <td>: {{ $data['pendaftaran']['jns_penjamin'] }}</td>
            </tr>
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;">Nama Pasien</td>
                <td>: {{ $data['pendaftaran']['nama_pasien'] }}</td>
                <!-- Kanan -->
                <td style="font-weight:bold;">Instansi</td>
                <td>: {{ $data['pendaftaran']['penjamin_nama'] }}</td>
            </tr>
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;">Tanggal Lahir</td>
                <td>: {{ $data['pendaftaran']['tempat_lahir'] }} , {{ $data['pendaftaran']['tgl_lahir'] }}</td>
                <!-- Kanan -->
                <td style="font-weight:bold;">Tanggal Keluar</td>
                <td>: {{ $data['pendaftaran']['tgl_pulang'] }}</td>
            </tr>
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;">Dokter Utama</td>
                <td>: {{ $data['pendaftaran']['dokter_nama'] }}</td>
                <!-- Kanan -->
                <td style="font-weight:bold;;">Unit</td>
                <td>: {{ $data['pendaftaran']['unit_nama'] }}</td>
            </tr>
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;">Tanggal Periksa</td>
                <td>: {{ $data['pendaftaran']['tgl_periksa'] }}</td>
                <!-- Kanan -->
                <td style="font-weight:bold;">Lama Perawatan</td>
                <td>: -</td>
            </tr>
            <tr>
                <!-- Kiri -->
                <td rowspan="2" style="vertical-align:top;font-weight:bold;">Alamat</td>
                <td rowspan="2" style="vertical-align:top;">: -</td>
                <!-- Kanan -->
                <td style="font-weight:bold;">Alasan Pulang</td>
                <td>: {{ $data['pendaftaran']['cara_pulang'] }}</td>
            </tr>
            <tr>
                
                <!-- Kanan -->
                <td style="font-weight:bold;">Ruang / No.Tempat Tidur</td>
                <td>: {{ $data['pendaftaran']['bed_id'] }} / {{ $data['pendaftaran']['no_bed'] }}</td>
            </tr>
           
            
        </table>
        <br>
        <!-- Data Transaksi / Tindakan -->
        <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin:auto;width:90%;font-size:10px !important;border:1px solid black;">
            <thead>
                <th>Uraian / Transaksi</th>
                <th>Tanggal</th>
                <th>Satuan</th>
                <th>Qty</th>
                <th>Kelas</th>
                <th>Diskon</th>
                <th>Harga</th>
            </thead>
            <tbody>
                @php
                    $maxIndex = count($data['detail']) - 1;
                @endphp
                @foreach ($data['detail'] as $index => $detail)
                    @if ($index == 0 || $detail['kelompok_tagihan'] != $data['detail'][$index - 1]['kelompok_tagihan'])
                        <tr>
                            <td colspan="7" class="bordered" style="padding-left:5px;font-style:italic;">{{ $detail['kelompok_tagihan'] }}</td>
                        </tr>
                        @php
                            $groupAmount = 0;
                            $groupDicount = 0;
                        @endphp
                    @endif
                    @php
                        $groupAmount += floatval($detail['subtotal']);
                        $groupDicount += floatval($detail['diskon']);
                    @endphp
                    
                    @if ($index == $maxIndex || $detail['kelompok_tagihan'] != $data['detail'][$index + 1]['kelompok_tagihan'])
                        <tr>
                            <td colspan="5" style="padding-left:5px;font-style:italic;text-align:right;">Total {{ $detail['kelompok_tagihan'] }}</td>
                            <td style="text-align:right !important;">
                                {{ number_format($groupDicount, 2) }}
                            </td>
                            <td style="text-align:right !important;">
                                {{ number_format($groupAmount, 2) }}
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="5" style="text-align:right !important;">TOTAL</td>
                    <td style="text-align:right !important;">
                        {{ number_format($data['total_diskon'], 2) }}
                    </td>
                    <td style="text-align:right !important;">
                        {{ number_format($data['grand_total'], 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
    </body>
</html>
