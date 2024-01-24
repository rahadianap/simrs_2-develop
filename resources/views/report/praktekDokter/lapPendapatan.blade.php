<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetakan Pendapatan</title>
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
                <p class="uk-margin-remove" style="font-size:14px;font-weight:bold;text-align:center;text-decoration-line: underline;">LAPORAN PENDAPATAN</p>
            </div>
        </div>
        <br>
        {{-- ISI --}}
        <table class="uk-table uk-table-small no-border-table" style="margin-top:-15px;margin:auto;width:90%;font-size:10px !important;">
            <tr>
                <!-- Kiri -->
                <td style="font-weight:bold;width:10%;">Periode</td>
                <td style="width:40%;">: {{ $data['periode'] }}</td>
                <!-- Kanan -->
                <td style="font-weight:bold;width:10%;">Total Pendapatan</td>
                <td style="width:40%;">: {{ number_format(floatval($data['sumTotal']),2) }}</td>
            </tr>
        </table>
        <br>
        <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin:auto;width:90%;font-size:10px !important;border:1px solid black;">
            <thead>
                <th>Tanggal</th>
                <th>No.Reg</th>
                <th>No.RM</th>
                <th>Nama Pasien</th>
                <th>Dokter</th>
                <th>Jumlah Bayar</th>
            </thead>
            <tbody>
                @foreach ($data['report'] as $keys => $values)
                    @php
                        $totalBayar = 0;
                    @endphp
                    <tr>
                        <td style="text-align:center !important;border-top:1px solid black;">
                            {{ \Carbon\Carbon::parse($values['tgl_bayar'])->format('d-m-Y') }}
                        </td>
                        <td style="text-align:center !important;border-top:1px solid black;">
                            {{ $values['reg_id'] }}
                        </td>
                        <td style="text-align:left !important;border-top:1px solid black;">
                            {{ $values['no_rm'] }}
                        </td>
                        <td style="text-align:left !important;border-top:1px solid black;">
                            {{ $values['nama_pasien'] }}
                        </td>
                        <td style="text-align:left !important;border-top:1px solid black;">
                            {{ $values['dokter_nama'] }}
                        </td>
                        <td style="text-align:right !important;border-top:1px solid black;">
                            {{ number_format(floatval($values['total_akhir']),2) }}
                        </td>
                    </tr>
                    {{-- <tr>
                        <td style="margin:5px 0px 5px 5px;font-weight:bold;" colspan="5"> DETAIL PEMBAYARAN </td>
                    </tr>
                    @foreach ($values['detail'] as $key => $value)
                        @php
                            $totalBayar += floatval($value['jml_bayar']);
                        @endphp
                        <tr>
                            <td style="text-align:center !important;" colspan="2">
                            </td>
                            <td style="text-align:left !important;">
                                {{ $value['payment_id'] }}
                            </td>
                            <td style="text-align:left !important;">
                                {{ $value['jns_payment'] }}
                            </td>
                            <td style="text-align:right !important;">
                                {{ number_format(floatval($value['jml_bayar']),2) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4" style="text-align:right !important;"> Total Bayar : </td>
                        <td style="text-align:right !important;"> {{ number_format(floatval($totalBayar),2) }} </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right !important;"> Kembalian : </td>
                        <td style="text-align:right !important;"> {{ number_format(floatval($values['kembalian']),2) }}</td>
                    </tr>
                     <tr>
                        <td colspan="4" style="text-align:right !important;"> Total Terima : </td>
                        <td style="text-align:right !important;"> {{ number_format(floatval($values['total_akhir']),2) }} </td>
                    </tr> --}}
                @endforeach
            </tbody>
        </table>
    </body>
</html>
