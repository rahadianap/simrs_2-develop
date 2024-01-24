<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Surat Sakit</title>
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
            <div>
                <p class="uk-margin-remove" style="font-size:20px;font-weight:bold;text-align:center;text-decoration-line:underline;">SURAT KETERANGAN SEHAT</p>
                <p class="uk-margin-remove" style="font-size:16px;font-weight:bold;text-align:center;">Nomor : <nomor_surat></p>
            </div>
        </div>
        {{-- ISI --}}
        <div class="uk-grid-small uk-align-center" uk-grid>
            <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin-left:auto;margni-right:auto;width:75%;font-size:12px !important;">
                <tr>
                    <td colspan="3">Yang bertanda tangan di bawah ini : </td>
                </tr>
                <tr>
                    <td style="width:10%;">Nama</td>
                    <td style="width:2%;">:</td>
                    <td style="width:auto;font-weight:bold;">Dr. Wijaya Agung</td>
                </tr>
                <tr>
                    <td>Dokter di </td>
                    <td>:</td>
                    <td style="font-weight:bold;">Rumah Sakit Persada</td>
                </tr>
                <tr>
                    <td colspan="3">Menerangkan bahwa : </td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>Hafiizh Asrofil Al Banna</td>
                </tr>
                <tr>
                    <td>Umur</td>
                    <td>:</td>
                    <td>27 thn</td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>:</td>
                    <td>Laki-laki</td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>IT Programmer</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>Jl. Kebagusan Raya No.192, Pasar Minggu, Jakarta Selatan</td>
                </tr>
                <tr>
                    <td colspan="3"><br></td>
                </tr>
                <tr>
                    <td colspan="3">Berdasarkan pemeriksaan kami, yang bersangkutan dinyatakan <u><b>SEHAT</b></u></td>
                </tr>
                <tr>
                    <td colspan="3">Surat Keterangan ini dibuat untuk keperluan Melamar Pekerjaan</td>
                </tr>
                <tr>
                    <td colspan="3">Demikian agar maklum untuk dapat dapat dipergunakan seperlunya</td>
                </tr>
            </table>
            <br>
            <div style="margin-left:20%;">
                <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;width:25%;margin-right:10%;font-size:12px !important;float: left;">
                    <tr>
                        <td style="width:10%;">Tinggi badan</td>
                        <td style="width:2%;">:</td>
                        <td style="width:30%;">159</td>
                        <td style="">Cm</td>
                    </tr>
                    <tr>
                        <td>Berat badan</td>
                        <td>:</td>
                        <td>66</td>
                        <td>Kg</td>
                    </tr>
                    <tr>
                        <td>Tekanan Darah</td>
                        <td>:</td>
                        <td>110/60</td>
                        <td>mmHg</td>
                    </tr>
                    <tr>
                        <td>Nadi</td>
                        <td>:</td>
                        <td>80</td>
                        <td>kali/menit</td>
                    </tr>
                    <tr>
                        <td>Pengliahatan</td>
                        <td>:</td>
                        <td>Normal</td>
                    </tr>
                    <tr>
                        <td>Pendengaran</td>
                        <td>:</td>
                        <td colspan="2">Normal</td>
                    </tr>
                    <tr>
                        <td>Buta Warna</td>
                        <td>:</td>
                        <td colspan="2">Normal</td>
                    </tr>
                    
                </table>
                <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;width:25%;font-size:12px !important;float:left;">
                    <tr>
                        <td style="font-weight:bold;text-align:center;">Jakarta, 17 Agustus 2023</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">Dokter pemeriksa</td>
                    </tr>
                    <tr>
                        <td><br><br><br><br></td>
                    </tr>
                    <tr>
                        <td style="text-decoration-line: underline;text-align:center;">Dr. Wijaya Agung</td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
