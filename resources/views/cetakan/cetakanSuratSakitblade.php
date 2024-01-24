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
                <p class="uk-margin-remove" style="font-size:20px;font-weight:bold;text-align:center;text-decoration-line:underline;">SURAT KETERANGAN SAKIT</p>
            </div>
        </div>
        {{-- ISI --}}
        <div class="uk-grid-small uk-align-center" uk-grid>
            <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin-left:auto;margni-right:auto;width:75%;font-size:12px !important;">
                <tr>
                    <td colspan="3">Yang bertanda tangan dibawah ini</td>
                </tr>
                <tr>
                    <td style="width:10%;">Dokter</td>
                    <td style="width:2%;">:</td>
                    <td style="width:auto;font-weight:bold;">Dr. Wijaya Agung</td>
                </tr>
                <tr>
                    <td colspan="3"><br></td>
                </tr>
                <tr>
                    <td colspan="3">Menerangkan bahwa</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td style="font-weight:bold;">Hafiizh Asrofil Al Banna</td>
                </tr>
                <tr>
                    <td>Umur</td>
                    <td>:</td>
                    <td>27</td>
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
                    <td colspan="3">Perlu beristirahat karena sakit selama <b>2 (dua) hari</b></td>
                </tr>
                <tr>
                    <td colspan="3">Terhitung tanggal <b>19 Agustus 2023</b> sampai <b>20 Agustus 2023</b></td>
                </tr>
                <tr>
                    <td colspan="3">Harap yang berkepentingan maklum</td>
                </tr>
            </table>
            <br><br>
            <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin-left:auto;margin-right:auto;width:45%;font-size:12px !important;float: right;">
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
    </body>
</html>
