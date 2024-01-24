<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Laporan Marketing Per Payer</title>

        <!-- styling -->
        <link rel="stylesheet" href="{{url('css/report/style.css')}}">
        <!-- <link rel="stylesheet" href="{{url('css/hims.css')}}"> -->
        <link rel="stylesheet" href="{{url('uikit/css/uikit.min.css')}}">
    </head>
    <body>
    <div style="padding: 2em;">
        <div class="uk-container paper-like" style="padding:auto">
            <ul style="padding:0;margin:0;" id="templateCetak"  ref="templateCetak" class="uk-margin-remove">
                <li style="padding:1em 0.2em;margin: 0;">
                    <div style="padding:1em 0.2em;" class="uk-container">
                        <div style="padding:0;border-radius:10px;min-height:400px;" id="pdf-body-b">
                            <!-- <div>
                                <div class="uk-flex-start" style="font-size:15px;font-weight:bold;font-family: verdana;letter-spacing:1px;text-transform: uppercase">
                                    <p>Rumah Sakit Kebagusan</p>
                                    <p class="uk-margin-small" style="text-transform:uppercase;">LAPORAN marketing per payer</p>
                                </div>
                            </div> -->
                            <!-- kop surat align left -->
                            @include('components.kopSuratv2')

                            <!-- <hr style="border:0.2px solid #cc0202;" /> -->

                        <!-- Data Transaksi / Tindakan -->
                        <table class="uk-table uk-table-small uk-table-hover uk-margin-medium" style="padding:0.2em;width:100%">
                            <thead style="font-size:13px;font-family: cour;text-align:center">
                                <th style="font-weight:bold;">No.</th>
                                <th style="font-weight:bold;width:35%">PENJAMIN</th>
                                <th style="font-weight:bold;">RAWAT JALAN</th>
                                <th style="font-weight:bold;">RAWAT INAP</th>
                                <th style="font-weight:bold;">TOTAL</th>
                                <th style="font-weight:bold;">JUMLAH PASIEN RAJAL</th>
                                <th style="font-weight:bold;">JUMLAH PASIEN RANAP</th>

                            </thead>

                            <tbody style="font-size:13px;font-family: cour;">
                                <!-- PERLU DIINGAT CLASS PADA KOLOM YANG BERKAITAN DENGAN QTY MAUPUN NOMINAL BUKANLAH CLASS BERUPA CSS, NAMUN FUNGSI GETELEMENT SEBAGAI TRIGGER KE FUNGSI SUM KOLOM TOTAL TRANSAKSI -->
                                <tr>
                                    <th colspan="7" style="background-color:#fff;font-weight:bold;border-bottom: 2px solid #80808021;"> ABADI SMILYNKS, PT</th>
                                </tr>
                                <tr>
                                    <td><span>1.</span></td>
                                    <td><span contenteditable>ABADI SMILYNKS, PT - PACIFIC PLACE JAKARTA,PT</span></td>
                                    <td style="text-align:right" class="sum-rajal" oninput="calculateTotal()"><span contenteditable>{{ number_format(('1723608'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-ranap" oninput="calculateTotal()"><span contenteditable>{{ number_format(('1240000'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-total" oninput="calculateTotal()"><span contenteditable>{{ number_format(('0'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-pasienrajal"  oninput="calculateTotal()"><span contenteditable>{{ number_format(('120'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-pasienranap"  oninput="calculateTotal()"><span contenteditable>{{ number_format(('250'),0, ',', '.') }}</span></td>
                                </tr>
                                <tr>
                                    <th colspan="7" style="background-color:#fff;font-weight:bold;border-bottom: 2px solid #80808021;"> ABDA ( ASURANSI BINA DANA ARTA )</th>
                                </tr>
                                <tr>
                                    <td><span>1.</span></td>
                                    <td><span contenteditable>ABDA ( ASURANSI BINA DANA ARTA ) - INDO PREMIER SEKURITAS PT</span></td>

                                    <td style="text-align:right" class="sum-rajal" oninput="calculateTotal()"><span contenteditable>{{ number_format(('1723608'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-ranap" oninput="calculateTotal()"><span contenteditable>{{ number_format(('1240000'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-total" oninput="calculateTotal()"><span contenteditable>{{ number_format(('0'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-pasienrajal"  oninput="calculateTotal()"><span contenteditable>{{ number_format(('840'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-pasienranap"  oninput="calculateTotal()"><span contenteditable>{{ number_format(('320'),0, ',', '.') }}</span></td>
                                
                                </tr>
                                <tr>
                                    <th colspan="7" style="background-color:#fff;font-weight:bold;border-bottom: 2px solid #80808021;"> ADMINISTRASI MEDIKA ( ADMEDIKA ), PT</th>
                                </tr>
                                <tr>
                                    <td><span>1.</span></td>
                                    <td><span contenteditable>ADMINISTRASI MEDIKA ( ADMEDIKA ), PT - PT. TUGU PRATAMA INDONESIA</span></td>
                                    <td style="text-align:right" class="sum-rajal" oninput="calculateTotal()"><span contenteditable>{{ number_format(('2930479'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-ranap" oninput="calculateTotal()"><span contenteditable>{{ number_format(('2500000'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-total" oninput="calculateTotal()"><span contenteditable>{{ number_format(('0'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-pasienrajal"  oninput="calculateTotal()"><span contenteditable>{{ number_format(('500'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-pasienranap"  oninput="calculateTotal()"><span contenteditable>{{ number_format(('150'),0, ',', '.') }}</span></td>
                                </tr>
                                <tr>
                                    <td><span>2.</span></td>
                                    <td><span contenteditable>ADMINISTRASI MEDIKA ( ADMEDIKA ), PT - PT.PLN (Persero) UIP Kalimantan Bagian Tengah</span></td>
                                    <td style="text-align:right" class="sum-rajal" oninput="calculateTotal()"><span contenteditable>{{ number_format(('2930479'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-ranap" oninput="calculateTotal()"><span contenteditable>{{ number_format(('2500000'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-total" oninput="calculateTotal()"><span contenteditable>{{ number_format(('0'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-pasienrajal"  oninput="calculateTotal()"><span contenteditable>{{ number_format(('500'),0, ',', '.') }}</span></td>
                                    <td style="text-align:right" class="sum-pasienranap"  oninput="calculateTotal()"><span contenteditable>{{ number_format(('150'),0, ',', '.') }}</span></td>
                                </tr>
                                <tr style="border-top: 0.1px solid #80808021;">
                                    <th colspan="2" style="text-align: right;background-color:rgb(249 249 249);"> GRAND TOTAL</th>
                                    <td id="totalKolomA" style="text-align: right">0</td> 
                                    <td id="totalKolomB" style="text-align: right">0</td>
                                    <td id="totalKolomC" style="text-align: right">0</td>
                                    <td id="totalKolomD" style="text-align: right">0</td>
                                    <td id="totalKolomE" style="text-align: right">0</td>
                                </tr>
                            </tbody>
                            <tfoot></tfoot>
                        </table>

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
            // DEFINE UNTUK KOLOM TOTAL TRANSAKSI
            var totalKolomA = 0;
            var totalKolomB = 0;
            // var totalKolomC = 0;
            var totalKolomD = 0;
            var totalKolomE = 0;
            var totalKolomF = 0;

            // TRIGGER KOLOM KOLOM YANG BERKAITAN DENGAN QTY MAUPUN NOMINAL 
            var cellsA = document.getElementsByClassName("sum-rajal");
            var cellsB = document.getElementsByClassName("sum-ranap");
            var cellsC = document.getElementsByClassName("sum-total");
            var cellsD = document.getElementsByClassName("sum-pasienrajal");
            var cellsE = document.getElementsByClassName("sum-pasienrajal");
            var cellsF = document.getElementsByClassName("sum-pasienranap");

            // FUNGSI SEPARATOR PADA KOLOM getElementsByClassName LALU SEBAGAI TRIGGER KE TOTAL TRANSAKSI 
            var decimalSeparator = ","; //FUNGSI DESIMAL 2 ANGKA SEPARATOR KOMA. CONTOH: 150.752,00 //
            
            // ADDITIONAL NOTES: JIKA KOLOM YANG BERKAITAN DENGAN QTY MAUPUN NOMINAL HANYA 3 KOLOM, MAKA PENYESUAIANNYA YAITU totalKolomC, cellsC valueC
            for (var i = 0; i < cellsA.length; i++) {
                var valueA = cellsA[i].innerText.replace(/\./g, "").replace(/,/g, ".");
                totalKolomA += parseFloat(valueA);
            }
            
                        
            for (var i = 0; i < cellsB.length; i++) {
                //var valueB = cellsB[i].innerText.replace(decimalSeparator, "."); //FUNGSI DESIMAL 2 ANGKA SEPARATOR KOMA. CONTOH: 150.752,00 //
                var valueB = cellsB[i].innerText.replace(/\./g, "").replace(/,/g, ".");
                totalKolomB += parseFloat(valueB);
            }

            for (var i = 0; i < cellsC.length; i++) {
                var valueC = cellsC[i].innerText.replace(/\./g, "").replace(/,/g, ".");
                totalKolomC += parseFloat(valueC);
            }

            for (var i = 0; i < cellsD.length; i++) {
                var valueD = cellsD[i].innerText.replace(/\./g, "").replace(/,/g, ".");
                totalKolomD += parseFloat(valueD);
            }

            for (var i = 0; i < cellsE.length; i++) {
                var valueE = cellsE[i].innerText.replace(/\./g, "").replace(/,/g, ".");
                totalKolomE += parseFloat(valueE);
            }

            var totalKolomC = 0;

            // FUNGSI SUM PADA KOLOM TOTAL
            for (var i = 0; i < cellsC.length; i++) {
            var valueA = cellsA[i].innerText.replace(/\./g, "").replace(/,/g, ".");
            var valueB = cellsB[i].innerText.replace(/\./g, "").replace(/,/g, ".");
            
            var jumlah = (parseFloat(valueA) + parseFloat(valueB));
            
            cellsC[i].innerText = formatNumber(jumlah.toFixed(2));
            totalKolomC += jumlah;
            }

            //FUNGSI UNTUK GET ELEMENT YANG SUDAH DI DEFINE LALU SUM DENGAN SEPARATOR PADA KOLOM TOTAL TRANSAKSI 
            // document.getElementById("totalKolomA").innerText = "Rp. " + formatNumber(totalKolomA.toFixed(2)); //jika mau pake Rp. , aktifkan fungsi ini
            document.getElementById("totalKolomA").innerText = formatNumber(totalKolomA.toFixed(2)); // tanpa Rp.
            // document.getElementById("totalKolomB").innerText = totalKolomB.toFixed(2).replace(".", decimalSeparator); //GUNAKAN decimalSeparator JIKA INGIN MENGGUNAKAN SEPARATOR DESIMAL 2 ANGKA. CONTOH: 150.752,00 
            document.getElementById("totalKolomB").innerText = formatNumber(totalKolomB.toFixed(2)); // tanpa Rp.
            document.getElementById("totalKolomC").innerText = formatNumber(totalKolomC.toFixed(2));
            document.getElementById("totalKolomD").innerText = formatNumber(totalKolomD.toFixed(2));
            document.getElementById("totalKolomE").innerText = formatNumber(totalKolomE.toFixed(2));
            // document.getElementById("totalKolomF").innerText = formatNumber(totalKolomF.toFixed(2));
            // document.getElementById("totalKolomG").innerText = formatNumber(totalKolomG.toFixed(2));
            // document.getElementById("totalKolomJumlah").innerText = formatNumber(totalKolomJumlah.toFixed(2));
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
