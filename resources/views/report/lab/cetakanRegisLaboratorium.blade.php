<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FORM PERMINTAAN PEMERIKSAAN LABORATORIUM</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
        <style>
            table.standard-table tr td, 
            table.standard-table tr th{
                border:1px solid black;
                padding:2px 5px !important;
            }
            table.no-border tr td{
                padding:0.5px !important;
            }
        </style>
        <!-- end styling -->
    </head>
    <body>
        {{-- KOP SURAT --}}
        @include('components.kopSurat')
        {{-- JUDUL --}}
        <br>
        <div class="uk-align-center uk-margin-remove">
            <div>
                <p class="uk-margin-remove" style="font-size:20px;font-weight:bold;text-align:center;text-decoration-line:underline;">FORM PERMINTAAN PEMERIKSAAN LABORATORIUM</p>
            </div>
        </div>
        {{-- ISI --}}
        <div class="uk-grid-small uk-align-center" uk-grid>
            <table class="uk-table uk-table-small no-border" style="margin-top:-15px;margin-left:auto;margin-right:auto;width:65%;font-size:12px !important;">
                <tr>
                    {{-- KIRI --}}
                    <td style="width:15%;">No.RM </td>
                    <td style="width:2%;">:</td>
                    <td style="width:30%;">{{ $data['lab'][0]['no_rm'] }} </td>
                    {{-- KAANAN --}}
                    <td style="width:15%;">Unit </td>
                    <td style="width:2%;">:</td>
                    <td style="width:auto;">{{ $data['lab'][0]['unit_nama'] }}</td>
                </tr>
                <tr>
                    {{-- KIRI --}}
                    <td>Nama Pasien</td>
                    <td>:</td>
                    <td>{{ $data['lab'][0]['nama_pasien'] }} </td>
                    {{-- KAANAN --}}
                    <td>Dokter </td>
                    <td>:</td>
                    <td>{{ $data['lab'][0]['dokter_nama'] }}</td>
                </tr>
                <tr>
                    @php
                        $date = new DateTime($data['lab'][0]['tgl_lahir']);
                        $now = new DateTime();
                        $interval = $now->diff($date);
                    @endphp
                    {{-- KIRI --}}
                    <td>Usia</td>
                    <td>:</td>
                    <td>{{ $interval->y }} Tahun </td>
                    {{-- KAANAN --}}
                    <td>Tanggal </td>
                    <td>:</td>
                    @php
                        $date = date_create($data['lab'][0]['tgl_periksa']);
                        $rDate = date_format($date,"d M Y");
                    @endphp
                    <td>{{ $rDate }}</td>
                </tr>
            </table>
            <br>
            <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin-left:auto;margin-right:auto;width:65%;font-size:12px !important;">
                <tr>
                    <th style="font-size:12px !important;font-weight:bold;text-align:center;">No. </th>
                    <th style="font-size:12px !important;font-weight:bold;text-align:center;">Jenis Pemeriksaan</th>
                    <th style="font-size:12px !important;font-weight:bold;text-align:center;">Jumlah</th>
                    <th style="font-size:12px !important;font-weight:bold;text-align:center;">Harga</th>
                    <th style="font-size:12px !important;font-weight:bold;text-align:center;">Subtotal</th>
                </tr>
                @foreach ($data['lab'] as $index => $detail)
                @php
                    $groupAmount = 0;
                    $groupDicount = 0;
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $index+1 }} </td>
                    <td>{{ $detail['item_nama'] }} </td>
                    <td style="text-align:right;">{{ $detail['jumlah'] }} </td>
                    <td style="text-align:right;">Rp {{ number_format(floatval($detail['nilai']),2) }} </td>
                    <td style="text-align:right;">Rp {{ number_format(floatval($detail['subtotal']),2) }} </td>
                </tr>
                @php
                    $groupAmount += floatval($detail['subtotal']);
                    $groupDicount += floatval($detail['diskon']);
                @endphp
                @endforeach
                <tr>
                    <td colspan="4" style="font-weight:bold;text-align:right;">Total</td>
                    <td style="text-align:right;">Rp {{ number_format(floatval($groupAmount),2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="font-weight:bold;text-align:right;">Diskon</td>
                    <td style="text-align:right;">Rp {{ number_format(floatval($groupDicount),2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="font-weight:bold;text-align:right;">Grand Total</td>
                    <td style="text-align:right;">Rp {{ number_format(floatval($groupAmount - $groupDicount), 2) }} </td>
                </tr>
               
            </table>
            <br>
            <table class="uk-table uk-table-small no-border" style="margin-top:-15px;margin-left:auto;margin-right:auto;width:45%;font-size:12px !important;float: right;">
                <tr>
                    <td style="font-weight:bold;text-align:center;">Jakarta, 17 Agustus 2023</td>
                </tr>
                <tr>
                    <td style="text-align:center;">Dibuat oleh</td>
                </tr>
                <tr>
                    <td><br><br><br><br></td>
                </tr>
                <tr>
                    <td style="text-decoration-line: underline;text-align:center;">(<b>_________________</b>)</td>
                </tr>
            </table>
        </div>
    </body>
</html>
