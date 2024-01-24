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
                <p class="uk-margin-remove" style="font-size:16px;font-weight:bold;text-align:center;">SURAT KETERANGAN BERBADAN SEHAT</p>
	     <p class="uk-margin-remove" style="font-size:14px;text-align:center;">CERTIFICATE OF MEDICAL FITNESS</p>
            </div>
        </div>
        {{-- ISI --}}
        <br/>
        <div class="uk-grid-small uk-align-left" uk-grid style="font-size:12px !important;margin-left:3.5%;">
            <p>
                Kepada yang berkepentingan : 
                <br/>
                To whom it may concern
            </p>
            <p>
                Yang bertanda tangan dibawah ini, menyatakan bahwa pasien berikut adalah benar telah menjalani pengobatan di "NMRS" :
                <br/>
                This is to certify that the following patients was treated at "NMRS" :
            </p>
        </div>
        <div class="uk-grid-small uk-align-left" uk-grid style="margin-left:5%;margni-right:auto;margin-top:-35px;">
            <table class="uk-table uk-table-small standard-table" style="width:100%;font-size:12px !important;">
                <tr>
                    <td style="width:30%;">Nama <br> Name</td>
                    <td style="width:2%;">:</td>
                    <td style="width:auto;font-weight:bold;">{{ $data['SuratSehat']['nama_pasien'] }}</td>
                </tr>
                <tr>
                    <td>Jenis kelamin <br> Gender </td>
                    <td>:</td>
                    <td>{{ $data['SuratSehat']['jns_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td>Nomor Rekam Medis <br> Medical Record Number</td>
                    <td>:</td>
                    <td>{{ $data['SuratSehat']['no_rm'] }}</td>
                </tr>
                <tr>
                    <td>Umur / Tanggal Lahir <br> Age / Date of Birth </td>
                    <td>:</td>
                    <?php
                        $tgl_lahir = $data['SuratSehat']['tgl_lahir'];
                        $birthdate = new DateTime($tgl_lahir);
                        $currentDate = new DateTime();
                        $interval = $currentDate->diff($birthdate);
                    ?>
                    <td>{{ $interval->y }} thn {{ $interval->m }} bl {{ $interval->d }} hr / {{ $birthdate->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Alamat <br> Address</td>
                    <td>:</td>
                    <td>{{ $data['SuratSehat']['alamat'] }}</td>
                </tr>
                <tr>
                    <td>Dengan hasil sebagai berikut <br> With results are as follow </td>
                    <td>:</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="color:white;">-</td>
                    <td style="color:white;">:</td>
                    <td style="color:white;">-</td>
                </tr>
                <tr>
                    <td>Tekanan Darah <br> Blood Pressure</td>
                    <td>:</td>
                    <td>{{ $data['SuratSehat']['sistole'] }} / {{ $data['SuratSehat']['diastole'] }} mmHg</td>
                </tr>
                <tr>
                    <td>Temperatur <br> Temperature</td>
                    <td>:</td>
                    <td>{{ $data['SuratSehat']['suhu'] }} Celcius</td>
                </tr>
                <tr>
                    <td>Denyut Jantung <br> Heart Rate</td>
                    <td>:</td>
                    <td>{{ $data['SuratSehat']['denyut_nadi'] }}</td>
                </tr>
                <tr>
                    <td>Laju Pernafasan <br> Respiratory Rate</td>
                    <td>:</td>
                    <td>{{ $data['SuratSehat']['pernapasan'] }} /menit</td>
                </tr>
                <tr>
                    <td>Tinggi Badan <br> Height</td>
                    <td>:</td>
                    <td>{{ $data['SuratSehat']['tinggi_badan'] }} cm</td>
                </tr>
                <tr>
                    <td>Berat Badan <br> Weight</td>
                    <td>:</td>
                    <td>{{ $data['SuratSehat']['berat_badan'] }} kg</td>
                </tr>
            </table>
        </div>
        <div class="uk-grid-small uk-align-right" style="margin-right:10%;">
            <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;width:55% !importnat;font-size:12px !important;float:left;">
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
                    <td style="text-decoration-line: underline;text-align:center;">({{ $data['SuratSehat']['dokter_nama'] }})</td>
                </tr>
            </table>
        </div>
    </body>
</html>
