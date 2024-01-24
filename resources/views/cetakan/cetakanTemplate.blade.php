<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Cetakan Template</title>

        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <!-- <link rel="stylesheet" href="{{url('css/hims.css')}}"> -->
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">

        <style>
            @font-face {
                font-family: 'cour';
                src: url('/fonts/cour.ttf') format('truetype');
            }

            @font-face {
                font-family: 'cour-bold';
                src: url('/fonts/cour-bold.ttf') format('truetype');
            }

            @font-face {
                font-family: 'verdana';
                src: url('/fonts/verdana.ttf') format('truetype');
            }

            @font-face {
                font-family: 'verdana-light';
                src: url('/fonts/verdanaPro-light.ttf') format('truetype');
            }

            @font-face {
                font-family: 'verdana-bold';
                src: url('/fonts/verdana-bold.ttf') format('truetype');
            }
            @font-face {
                font-family: 'verdana-bold-italic';
                src: url('/fonts/verdana-bold-italic.ttf') format('truetype');
            }
        </style>
    </head>
    <body>
    <div style="padding: 2em;">
        <div class="uk-container paper-like" style="padding:auto">
            <ul style="padding:0;margin:0;" id="templateCetak"  ref="templateCetak" class="uk-margin-remove">
                <li style="padding:1em 0.2em;margin: 0;">
                    <div style="padding:1em 0.2em;" class="uk-container">
                        <div style="padding:0;border-radius:10px;min-height:400px;" id="pdf-body-b">
                            <!-- kop surat -->
                            @include('components.kopSurat')

                            <!-- Judul Laporan -->
                            <div class="uk-align-center uk-margin-remove">
                                <div>
                                    <p class="uk-margin-remove" style="font-size:20px;font-weight:bold;text-align:center;font-family: verdana-bold;letter-spacing:1.1px;">RINCIAN BIAYA PERAWATAN</p>
                                </div>
                                <div>
                                    <p class="uk-margin-remove" style="font-size:16px;text-align:center;font-family:verdana;letter-spacing:1.3px;">Periode Perawatan : 26-Dec-2022 s/d 29-Dec-2022</p>
                                </div>
                            </div>
                            <!-- Data Pasien -->
                            <table style="width:100%;font-size:13px;font-family:cour;letter-spacing:0.5px;padding:0.2em" class="uk-margin-small">
                            <tr>
                                <!-- Kiri -->
                                <td style="font-family:cour;font-weight:bold;">No.Reg / No.RM</td>
                                <td style="font-family:cour">: no_reg  | no_rm</td>
                                <!-- Kanan -->
                                <td style="font-family:cour;font-weight:bold;">Penjamain Bayar</td>
                                <td style="font-family:cour">: penjamin   </td>
                            </tr>
                            <tr>
                                <!-- Kiri -->
                                <td style="font-family:cour;font-weight:bold;">Nama Pasien</td>
                                <td style="font-family:cour">: nama_pasien   </td>
                                <!-- Kanan -->
                                <td style="font-family:cour;font-weight:bold;">Instansi</td>
                                <td style="font-family:cour">: instansi   </td>
                            </tr>
                            <tr>
                                <!-- Kiri -->
                                <td style="font-family:cour;font-weight:bold;">Tanggal Lahir</td>
                                <td style="font-family:cour">: tgl_lahir   </td>
                                <!-- Kanan -->
                                <td style="font-family:cour;font-weight:bold;">Tanggal Keluar</td>
                                <td style="font-family:cour">: tgl_keluar   </td>
                            </tr>
                            <tr>
                                <!-- Kiri -->
                                <td style="font-family:cour;font-weight:bold;">Dokter Utama</td>
                                <td style="font-family:cour">: dokter_utama   </td>
                                <!-- Kanan -->
                                <td style="font-family:cour;font-weight:bold;;">Hak Kelas</td>
                                <td style="font-family:cour">: hak_kelas   </td>
                            </tr>
                            <tr>
                                <!-- Kiri -->
                                <td style="font-family:cour;font-weight:bold;">Tanggal Masuk</td>
                                <td style="font-family:cour">: tgl_masuk   </td>
                                <!-- Kanan -->
                                <td style="font-family:cour;font-weight:bold;">Lama Perawatan</td>
                                <td style="font-family:cour">: lama_perawatan   </td>
                            </tr>
                            <tr>
                                <!-- Kiri -->
                                <td style="vertical-align:top;font-family:cour;font-weight:bold;">Alamat</td>
                                <td style="vertical-align:top;font-family:cour;">: alamat   </td>
                                <!-- Kanan -->
                                <td style="font-family:cour;font-weight:bold;">Ruang Perawatan</td>
                                <td style="font-family:cour">: ruang_perawatan   </td>
                            </tr>
                            <tr>
                                <!-- Kanan -->
                                <td style="font-family:cour;font-weight:bold;">Kelas / No.Tempat Tidur</td>
                                <td style="font-family:cour">: kelas / no_tempat_tidur   </td>
                                <!-- Kiri -->
                                <td style="font-family:cour;font-weight:bold;">Alasan Pulang</td>
                                <td style="font-family:cour">: alasan_pulang   </td>
                            </tr>
                            
                        </table>
                    
                        <!-- Data Transaksi / Tindakan -->
                        <table class="uk-table uk-table-small uk-table-hover uk-margin-medium" style="padding:0.2em">
                            <thead style="font-size:13px;font-family: cour;text-align:center">
                                <th style="font-weight:bold;">Uraian/Transaksi</th>
                                <th style="font-weight:bold;">Tanggal</th>
                                <th style="font-weight:bold;">Kelas</th>
                                <th style="font-weight:bold;">Pasien</th>
                                <th style="font-weight:bold;">Diskon</th>
                                <th style="font-weight:bold;">Satuan</th>
                                <th style="font-weight:bold;">Qty</th>
                                <th style="font-weight:bold;">Harga</th>
                            </thead>


                            <!-- <tbody style="font-size:13px;font-family: cour;">
                                <tr>
                                    <th colspan="9" style="background-color:#fff;font-weight:bold;border-bottom: 0.1px solid #80808021;"> Data Ruangan</th>
                                </tr>
                                @foreach ($datas['data_transaksi']['ruangan'] as $data)
                                <tr>
                                    <td contenteditable>
                                        {{ $data['uraian'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['tanggal'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['kelas'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['pasien'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['diskon'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['satuan'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['qty'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['instansi'] }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr style="border-top: 0.1px solid #80808021;">
                                    <th colspan="5" style="text-align: right;background-color:rgb(249 249 249);"> Total Transaksi Ruangan</th>
                                    <td>Rp. 123</td>
                                    <td>456</td>
                                    <td>Rp. 456</td>
                                </tr>
                            </tbody> -->

                            <tbody style="font-size:13px;font-family: cour;">
                                <tr>
                                    <th colspan="9" style="background-color:#fff;font-weight:bold;border-bottom: 2px solid #80808021;"> Data Ruangan</th>
                                </tr>
                                @foreach ($datas['data_transaksi']['ruangan'] as $data)
                                <tr>
                                    <td contenteditable>
                                        {{ $data['uraian'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['tanggal'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['kelas'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['pasien'] }}
                                    </td>
                                    <td contenteditable>
                                        {{ $data['diskon'] }}
                                    </td>
                                    <td contenteditable class="satuan" style="text-align: right" oninput="calculateTotal()" data-value="{{ $data['satuan'] }}">  <!-- fungsi separator titik untuk nominal pada kolom satuan -->
                                     {{ number_format($data['satuan'], 0, ',', '.') }}
                                    </td>
                                    <td contenteditable class="qty" style="text-align: right" oninput="calculateTotal()"  data-decimal-separator=","> <!-- fungsi separator hanya koma untuk desimal pada kolom QTY -->
                                        {{ number_format($data['qty'], 2, ',', '.') }}
                                    </td>
                                    <td contenteditable class="instansi" style="text-align: right" oninput="calculateTotal()"  data-value="{{ $data['instansi'] }}">  <!-- fungsi separator titik untuk nominal pada kolom instansi -->
                                    {{ number_format($data['instansi'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr style="border-top: 0.1px solid #80808021;">
                                    <th colspan="5" style="text-align: right;background-color:rgb(249 249 249);"> Total Transaksi Ruangan</th>
                                    <td id="totalSatuan" style="text-align: right">0</td> 
                                    <td id="totalQty" style="text-align: right">0</td>
                                    <td id="totalInstansi" style="text-align: right">0</td>
                            </tbody>
                            <tfoot></tfoot>
                        </table>

                        <!-- table total transaksi -->
                        <div class="uk-container uk-flex uk-flex-middle uk-margin-medium">
                            <!-- balance instansi -->
                            <div class="uk-width-1-2" style="margin-right: 62.5px;">
                                <table class="balance">
                                    <p style="margin-left:185px;font-family: cour;font-weight: bold;color: #22527a;text-transform:uppercase;margin-bottom:5.5px;">
                                        Instansi
                                    </p>
                                    <tr>
                                        <th style="font-family: cour;font-weight: bold;background: #f9f9f9;"><span contenteditable>Jumlah Tagihan </span></th>
                                        <td style="font-family: cour;"><span data-prefix>Rp. </span><span>3388</span></td>
                                    </tr>
                                    <tr>
                                        <th style="font-family: cour;font-weight: bold;background: #f9f9f9"><span contenteditable>Adm & Pelayanan</span></th>
                                        <td style="font-family: cour;"><span data-prefix>Rp. </span><span contenteditable>0.00</span></td>
                                    </tr>
                                    <tr>
                                        <th style="font-family: cour;font-weight: bold;background: #f9f9f9"><span contenteditable>Uang Angsuran</span></th>
                                        <td style="font-family: cour;"><span data-prefix>Rp. </span><span>888333</span></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- balance pasien -->
                            <div class="uk-width-1-2">
                                <table class="balance-pasien">
                                <p style="font-family: cour;font-weight: bold;color: #22527a;text-transform:uppercase;margin-bottom: 5.5px;">
                                    Pasien
                                </p>
                                <tr>
                                    <th style="font-family: cour;font-weight: bold;background: #f9f9f9"><span contenteditable>Jumlah Tagihan </span></th>
                                    <td style="font-family: cour;"><span data-prefix>Rp. </span><span>125555</span></td>
                                </tr>
                                <tr>
                                    <th style="font-family: cour;font-weight: bold;background: #f9f9f9"><span contenteditable>Adm & Pelayanan</span></th>
                                    <td style="font-family: cour;"><span data-prefix>Rp. </span><span contenteditable>0.00</span></td>
                                </tr>
                                <tr>
                                    <th style="font-family: cour;font-weight: bold;background: #f9f9f9"><span contenteditable>Uang Angsuran</span></th>
                                    <td style="font-family: cour;"><span data-prefix>Rp. </span><span>12333</span></td>
                                </tr>
                                </table>
                            </div>
                        </div>

                        <!-- footer -->
                        <div class="uk-container uk-flex uk-flex-middle uk-card-body" style="font-family: verdana-bold;">
                                    <div class="uk-width-1-2 uk-text-center">
                                        <div>
                                            <p>Petugas</p>
                                        </div>
                                        <div style="padding: 10px 0 10px">
                                            <img src="{{url('/images//ttd-petugas.png') }}" style="width:100px;">
                                        </div>
                                        <div>
                                            <p contenteditable> Aviat </p>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-2 uk-text-center">
                                        <div>
                                            <p>Penerima</p>
                                        </div>
                                        <div style="padding: 10px 0 10px">
                                            <img src="{{url('/images/ttd-penerima.png') }}" style="width:100px;">
                                        </div>
                                        <div>
                                            <p>( <span contenteditable style="text-decoration: underline;">Penerima</span> )</p>
                                        </div>
                                    </div>

                        </div>

                    </div>
                </li>
            </ul>
        </div>
    </div>
    </body>

    <script type="text/javascript">

        // FUNGSI SUM PADA KOLOM 'TOTAL TRANSAKSI RUANGAN' . FUNGSI INI SESUAI DENGAN PENULISAN RUPIAH DENGAN SEPARATOR TITIK SERTA DESIMAL 2 ANGKA DENGAN SEPARATOR KOMA
        document.addEventListener("DOMContentLoaded", function() {
            calculateTotal();
        });

        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        function calculateTotal() {
            var totalSatuan = 0;
            var totalQty = 0;
            var totalInstansi = 0;

            var satuanCells = document.getElementsByClassName("satuan");
            var qtyCells = document.getElementsByClassName("qty");
            var instansiCells = document.getElementsByClassName("instansi");

            var decimalSeparator = ",";
            for (var i = 0; i < qtyCells.length; i++) {
                var qtyValue = qtyCells[i].innerText.replace(decimalSeparator, ".");
                totalQty += parseFloat(qtyValue);
            }

            // for (var i = 0; i < satuanCells.length; i++) {
            //     totalSatuan += parseFloat(satuanCells[i].innerText.replace(/\./g, '').replace(/,/, '.'));
            //     totalQty += parseFloat(qtyCells[i].innerText.replace(/\./g, '').replace(/,/, '.'));
            //     totalInstansi += parseFloat(instansiCells[i].innerText.replace(/\./g, '').replace(/,/, '.'));
            // }

            for (var i = 0; i < satuanCells.length; i++) {
            var satuanValue = satuanCells[i].innerText.replace(/\./g, "").replace(/,/g, ".");
            totalSatuan += parseFloat(satuanValue);
            }

            for (var i = 0; i < instansiCells.length; i++) {
                var instansiValue = instansiCells[i].innerText.replace(/\./g, "").replace(/,/g, ".");
                totalInstansi += parseFloat(instansiValue);
            }


            // document.getElementById("totalSatuan").innerText = "Rp. " + formatNumber(totalSatuan.toFixed(2)); //jika mau pake Rp. , aktifkan fungsi ini
            document.getElementById("totalSatuan").innerText = formatNumber(totalSatuan.toFixed(2)); // tanpa Rp.
            document.getElementById("totalQty").innerText = totalQty.toFixed(2).replace(".", decimalSeparator);
            document.getElementById("totalInstansi").innerText = formatNumber(totalInstansi.toFixed(2));
        }
        // END FUNCTION

    </script>
    <style>
        /* custom uk-table-small untuk gap atas bawah pada kolom kolom/isi tablenya */
        .uk-table-small td, .uk-table-small th {
            padding: 4px 12px;
        }

        /* custom th dan td untuk gap atas bawah */
        th, td {
            border-width: 1px;
            padding: 0 !important;
            position: relative;
            text-align: left;
        }
            </style>
</html>
