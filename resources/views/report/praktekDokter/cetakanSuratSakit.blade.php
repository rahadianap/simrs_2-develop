<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Surat Sehat</title>
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
        {{-- KOP SURAT --}}
        @include('components.kopSurat')
        {{-- JUDUL --}}
        <br>
        <div class="uk-align-center uk-margin-remove">
            <div style="line-height:16.5px;">
                <p class="uk-margin-remove" style="font-size:16px;font-weight:bold;text-align:center;">SURAT KETERANGAN SAKIT</p>
	     <p class="uk-margin-remove" style="font-size:14px;text-align:center;">SICK LEAVE CERTIFICATE</p>
            </div>
        </div>
        {{-- ISI --}}
        <br/>
        <br/>
        <div class="uk-grid-small" uk-grid style="margin-left:5%;margni-right:auto;margin-top:-35px;">
            <table class="uk-table uk-table-small standard-table" style="width:60%;font-size:12px !important;">
                <tr>
                    <td colspan="3">Dengan ini Menerangkan <br> This is to state </td>
                </tr>
                <tr>
                    <td colspan="3"><br></td>
                </tr>
                <tr>
                    <td style="padding-left:15% !important;">Nama <br> Name</td>
                    <td>:</td>
                    <td>{{ $data['SuratSakit']['nama_pasien'] }}</td>
                </tr>
                <tr>
                    <td style="padding-left:15% !important;">Umur <br> Age</td>
                    <td>:</td>
                    <td>{{ $data['SuratSakit']['umur'] }} Tahun</td>
                </tr>
                <tr>
                    <td style="padding-left:15% !important;">Pekerjaan <br> Occupation</td>
                    <td>:</td>
                    <td>{{ $data['SuratSakit']['pekerjaan'] }}</td>
                </tr>
                <tr>
                    <td style="padding-left:15% !important;">Alamat <br> Address</td>
                    <td>:</td>
                    <td>{{ $data['SuratSakit']['alamat'] }}</td>
                </tr>
            </table>
            <table class="uk-table uk-table-small standard-table" style="width:70%;font-size:12px !important;">
                <tr>
                    <td colspan="2" style="width:30%;">
                        Perlu beristirahat
                        <br> 
                        Shall be
                    </td>
                    <td colspan="2" style="width:40%;">
                        @if($data['SuratSakit']['kateg_istirahat'] == "diRumah")
                            <b> di rumah </b>
                            <br> 
                            <b> undergo home resting </b>
                        @elseif($data['SuratSakit']['kateg_istirahat'] == "cutiHamil")
                            <b> cuti hamil </b>
                            <br> 
                            <b> pregnancy-leave </b>
                        @else
                            <b> di rawat di Rumah Sakit {{ $data['SuratSakit']['nama_rs'] }} </b>
                            <br> 
                            <b> hospitalizing in the {{ $data['SuratSakit']['nama_rs'] }} </b>
                        @endif
                    </td>
                </tr>               
	            <tr>
                    <td colspan="2">Selama <br> For</td>
                    <td>{{ $data['SuratSakit']['hari'] }} </td>
                            <td>Hari <br> Day(s)</td>
                </tr>
                <tr>
                    <td>Dari tanggal <br> Counting from </td>
	                <td>{{ $data['SuratSakit']['tgl_awal'] }} </td>
                    <td>Sampai dengan <br> Until </td>
	                <td>{{ $data['SuratSakit']['tgl_akhir'] }} </td>
                </tr>
                <tr>
                    <td colspan="4">Harap yang berkepentingan maklum <br> Please take this into consideration to whorm it may concern </td>
                </tr>
            </table>
            <table class="uk-table uk-table-small standard-table" style="width:70%;font-size:12px !important;">
                <tr>
                    <td style="width: 40%;">Diagnosa <br> Diagnose </td>
                    <td style="width: 60%;">
                        @foreach ($data['SuratSakit']['diagnosa'] as $keys => $values)
                            {{ "- " . $values['tipe'] }} | {{ $values['nama_icd'] }} <br/>
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>
        <div class="uk-grid-small uk-align-right" style="margin-right:5%;">
            <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;width:100% !important;font-size:12px !important;float:right;">
                <tr>
                    <td style="font-weight:bold;text-align:center;">Jakarta, {{ now()->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td style="text-align:center;">Dokter pemeriksa <br> Attending Doctor</td>
                </tr>
                <tr>
                    <td><br><br><br><br></td>
                </tr>
                <tr>
                    <td style="text-decoration-line: underline;text-align:center;">({{ $data['SuratSakit']['dokter_nama'] }})</td>
                </tr>
            </table>
        </div>
        <div class="uk-grid-small">
            <table class="uk-table uk-table-small standard-table" style="margin-left:10%;margin-top:10%;width:60% !important;font-size:12px !important;">
                <tr>
                    <td style="font-weight:bold;">**Catatan <br> Note</td>
                </tr>
                <tr>
                    <td>
                        {{ $data['SuratSakit']['catatan'] }}
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
