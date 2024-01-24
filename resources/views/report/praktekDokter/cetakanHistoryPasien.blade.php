<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetakan Kunjungan</title>
        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
        <style>
	table tr th {
                font-size:10px;
                text-align:center !important;
                font-weight:bold;
            }
            table.standard-table tr td{
                padding: 3px 5px !important;
            }
            table.standard-table tr th{
                padding: 1px 5px !important;
                border-top: 1px solid black !important;
	            vertical-align: middle;
            }
            table.standard-table thead th {
                font-size: 11px;
                font-weight: bold;         /* Make text bold */
                text-transform: capitalize; /* Capitalize each word */
            }
            table.no-border tr td {
                padding: 0px 5px !important;
                border-collapse: collapse;
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
        <div class="uk-align-center uk-margin-remove">
            <div>
                <p class="uk-margin-remove" style="font-size:14px;font-weight:bold;text-align:center;text-decoration-line: underline;">RIWAYAT PASIEN</p>
            </div>
        </div>
        <br>
        {{-- ISI --}}
        {{-- DATA PASIEN --}}
        <table class="uk-table uk-table-small no-border" style="margin-top:-15px;margin-left:auto;margin-right:auto;width:80%;font-size:12px !important;">
            <tr>
                {{-- KIRI --}}
                <td style="width:15%;">No. RM </td>
                <td style="width:2%;">:</td>
                <td style="width:30%;">{{ $data['praktek']['no_rm'] }} </td>
                {{-- KAANAN --}}
                <td style="width:15%;">No. Registrasi </td>
                <td style="width:2%;">:</td>
                <td style="width:auto;">{{ $data['praktek']['reg_id'] }}</td>
            </tr>
            <tr>
                {{-- KIRI --}}
                <td>Nama Pasien</td>
                <td>:</td>
                <td>{{ $data['praktek']['nama_pasien'] }} </td>
                {{-- KAANAN --}}
                <td>Tgl. Periksa </td>
                <td>:</td>
                <td>{{ $data['praktek']['tgl_periksa'] }}</td>
            </tr>
            <tr>
                {{-- KIRI --}}
                <td>Tgl. Lahir</td>
                <td>:</td>
                <td>{{ $data['praktek']['tgl_lahir'] }} </td>
                {{-- KAANAN --}}
                <td>Tgl. Daftar</td>
                <td>:</td>
                <td>{{ $data['praktek']['tgl_registrasi'] }} </td>
            </tr>
            <tr>
                {{-- KIRI --}}
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>
                    {{ $data['praktek']['jns_kelamin'] == "L" ? 'Laki-laki' : 'Perempuan' }}
                </td>
                {{-- KAANAN --}}
                <td>Dibuat oleh </td>
                <td>:</td>
                <td>{{ $data['praktek']['dokter_nama'] }}</td>
            </tr>
        </table>
        <table class="uk-table uk-table-small standard-table" style="margin-top:-15px;margin-left:auto;margin-right:auto;width:80%;font-size:12px !important;">
            <colgroup>
                <col style="width: 15%;">
                <col style="width: 15%;">
                <col style="width: 2%;">
                <col style="width: auto;"> <!-- This column will have an automatic width -->
            </colgroup>
            {{-- START | TANDA VITAL --}}
            <tr>
                <td colspan="4"><hr>
            </tr>
            <tr>
                <td rowspan="7" style="vertical-align: top;font-weight:bold;">TANDA VITAL </td>
                <td >Tinggi Badan </td>
                <td >:</td>
                <td >{{ $data['soap']['tinggi_badan'] }} cm</td>
            </tr>
            <tr>
                <td>Berat Badan </td>
                <td>:</td>
                <td>{{ $data['soap']['berat_badan'] }} kg</td>
            </tr>
            <tr>
                <td>Diastole</td>
                <td>:</td>
                <td>{{ $data['soap']['diastole'] }} mmHg</td>
            </tr>
            <tr>
                <td>Sistole </td>
                <td>:</td>
                <td>{{ $data['soap']['sistole'] }} mmHg</td>
            </tr>
            <tr>
                <td>Suhu</td>
                <td>:</td>
                <td>{{ $data['soap']['suhu'] }} <span>&#8451;</span></td>
            </tr>
            <tr>
                <td>Nadi</td>
                <td>:</td>
                <td>{{ $data['soap']['denyut_nadi'] }} bpm</td>
            </tr>
            <tr>
                <td>Pernapasan</td>
                <td>:</td>
                <td>{{ $data['soap']['pernapasan'] }} bpm</td>
            </tr>
            {{-- END | TANDA VITAL --}}
            {{-- START | SOAP --}}
            <tr>
                <td colspan="4"><hr>
            </tr>
            <tr>
                <td rowspan="7" style="vertical-align: top;font-weight:bold;">SOAP </td>
                <td >Subject </td>
                <td >:</td>
                <td style="text-align: justify;">{{ $data['soap']['subjective'] }} </td>
            </tr>
            <tr>
                <td>Objective </td>
                <td>:</td>
                <td style="text-align: justify;">{{ $data['soap']['objective'] }} </td>
            </tr>
            <tr>
                <td>Assestment</td>
                <td>:</td>
                <td style="text-align: justify;">{{ $data['soap']['assesment'] }} </td>
            </tr>
            <tr>
                <td>Plan </td>
                <td>:</td>
                <td style="text-align: justify;">{{ $data['soap']['plan'] }} </td>
            </tr>
            <tr>
                <td>Evaluation </td>
                <td>:</td>
                <td style="text-align: justify;">{{ $data['soap']['evaluation'] }} </td>
            </tr>
            <tr>
                <td>Intervention </td>
                <td>:</td>
                <td style="text-align: justify;">{{ $data['soap']['intervention'] }} </td>
            </tr>
            <tr>
                <td>Note </td>
                <td>:</td>
                <td style="text-align: justify;">{{ $data['soap']['note_assesmen'] }} </td>
            </tr>
            {{-- END | SOAP --}}
            {{-- START | DIAGNOSA --}}
            <tr>
                <td colspan="4"><hr></td>
            </tr>
            @foreach ($data['diagnosa'] as $key => $values)
            <tr>
                @if ($key === 0)
                <td rowspan="{{ count($data['diagnosa']) }}" style="vertical-align: top; font-weight:bold;">DIAGNOSA</td>
                @else
                <td rowspan="{{ count($data['diagnosa']) }}" style="vertical-align: top; font-weight:bold;"></td>
                @endif
                <td colspan="3" style="font-style:italic;">{{ $values['kode_icd'] }} - {{ $values['nama_icd'] }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-transform: uppercase;">
                    Kasus: {{ $values['tipe'] }} - {{ $values['kasus_lama'] ? 'Kasus Lama' : 'Kasus Baru' }}
                </td>
            </tr>
            @endforeach
            {{-- END | DIAGNOSA --}}
            {{-- START | TINDAKAN --}}
            <tr>
                <td colspan="4"><hr>
            </tr>
            @foreach ($data['tindakan'] as $key => $values)
            <tr>
                @if ($key === 0)
                <td rowspan="{{ count($data['tindakan']) }}" style="vertical-align: top; font-weight:bold;">TINDAKAN</td>
                @else
                <td rowspan="{{ count($data['tindakan']) }}" style="vertical-align: top; font-weight:bold;"></td>
                @endif
                <td colspan="3">
                    <div style="display: flex;justify-content: space-between;">
                        <span style="text-align:left;">
                            {{ $values['item_nama'] }}
                        </span>
                        <span style="text-align:right;margin-right:40%;">
                            {{ $values['jumlah'] . '' . $values['satuan'] }} &nbsp;&nbsp;&nbsp; {{ $values['subtotal'] }}
                        </span>
                    </div>
                </td>
            </tr>
            @endforeach
            {{-- END | TINDAKAN --}}
            {{-- START | RESEP --}}
            <tr>
                <td colspan="4"><hr></td>
            </tr>
            @foreach ($data['resep'] as $key => $values)
            <tr>
                @if ($key === 0)
                <td rowspan="{{ count($data['resep']) }}" style="vertical-align: top; font-weight:bold;">RESEP</td>
                @else
                <td rowspan="{{ count($data['resep']) }}" style="vertical-align: top; font-weight:bold;"></td>
                @endif
                <td colspan="3">
                    {{ $values['item_nama'] }} &nbsp; ({{ $values['jumlah'] }}) &nbsp; {{ $values['signa_deskripsi'] }}
                </td>
            </tr>
            @endforeach
            {{-- END | RESEP --}}
        </table>
    </body>
</html>
