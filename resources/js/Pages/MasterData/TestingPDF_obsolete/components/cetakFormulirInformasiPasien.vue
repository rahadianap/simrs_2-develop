<template>
    <div style="padding: 3em;">
        <div class="uk-container paper-like" style="padding:0 1em 10em;height: 320mm;" >
            <div style="position: sticky;top:10px;z-index:100;">
                <div class="uk-width-1-1 uk-text-right">
                    <input class="submit" type="submit" value="Print" style="margin-right:-120px" @click="printDiv('cetakFormulirInformasiPasien')">
                </div>
            </div>
            <ul style="padding:0;margin:0;" id="cetakFormulirInformasiPasien">
                <li style="padding:0;margin:0;">
                    <div style="padding:1em 0 1em 0;" class="uk-container">
                        <div style="padding:0; margin-top:1em; border-radius:10px;min-height:400px;" id="pdf-body-b">
                            <div class="uk-container uk-flex uk-flex-middle uk-card-body">
                                <div class="uk-width-1-4">
                                    <div class="uk-flex uk-flex-center uk-flex-middle">
                                        <img src="https://aviat.id/static/assets/images/aviat-logo.webp" class="logo-pdf uk-text-center uk-responsive-width">
                                    </div>
                                </div>
                                <div class="uk-width-1-2">
                                    <div class="uk-flex-center uk-text-center">
                                        <p style="font-size: 14px;font-family: verdana-bold;font-weight:bold;letter-spacing:1.1px;">Rumah Sakit Kebagusan Jaksel</p>
                                        <p class="uk-margin-small" style="font-size: 12px;font-family: verdana;letter-spacing:1.3px;">Jl. Kebagusan Raya No.192 Pasar Minggu, Jaksel</p>
                                        <p class="uk-margin-small" style="font-size: 12px;font-family: verdana;letter-spacing:1.3px;">Telp. +62-21-78838951 | Email: support@kp3.co.id</p>
                                    </div>
                                </div>
                            </div>

                            <hr style="border:0.2px solid #cc0202;" />

                            <div class="uk-align-center uk-margin-medium">
                                <div>
                                    <p class="uk-margin-small" style="font-size:20px;font-weight:bold;text-align:center;font-family: verdana-bold;letter-spacing:1.1px;text-decoration: underline">FORMULIR INFORMASI PASIEN</p>
                                </div>
                            </div>
                            <table style="width:100%;font-size:14px;letter-spacing:0.5px;" class="uk-margin-small">
                                <tr>
                                    <td style="font-family:cour;font-weight:bold;">No.RM</td>
                                    <td style="font-family:cour">: {{ no_rm }}</td>
                                    <td style="font-family:cour;font-weight:bold;">Status Rawat</td>
                                    <td style="font-family:cour">: Outpatient / Rawat Jalan</td>
                                </tr>
                                <tr>
                                    <td style="font-family:cour;font-weight:bold;">Nama Pasien</td>
                                    <td style="font-family:cour">: {{ nama_pasien }}</td>
                                    <td style="font-family:cour;font-weight:bold;">Dokter Rawat</td>
                                    <td style="font-family:cour">: {{ dokter_utama }}</td>
                                </tr>
                                <tr>
                                    <td style="font-family:cour;font-weight:bold;">Tanggal Lahir | Usia</td>
                                    <td style="font-family:cour">: {{ tgl_lahir }} | 44 tahun</td>
                                    <td style="font-family:cour;font-weight:bold;">Pejamin</td>
                                    <td style="font-family:cour">: {{ penjamin }}</td>
                                </tr>
                                <tr>
                                    <td style="font-family:cour;font-weight:bold;">Kelas / No.Tempat Tidur</td>
                                    <td style="font-family:cour">: {{ kelas }} / {{ no_tempat_tidur }}</td>
                                    <td style="font-family:cour;font-weight:bold;;">Hak Kelas</td>
                                    <td style="font-family:cour">: {{ hak_kelas }}</td>
                                </tr>
                                <tr>
                                    <td style="font-family:cour;font-weight:bold;">Tanggal Rawat</td>
                                    <td style="font-family:cour">: {{ tgl_masuk }}</td>
                                    <td style="font-family:cour;font-weight:bold;">Tanggal Keluar</td>
                                    <td style="font-family:cour">: {{ tgl_keluar }}</td>
                                </tr>
                                <tr>
                                    <td style="font-family:cour;font-weight:bold;">Alamat</td>
                                    <td style="font-family:cour">: {{ alamat }}</td>
                                </tr>
                                
                            </table>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import stickyHeader from '../components/StickyHeader/index.vue';

export default {
    name: 'cetak-form-inap',
    components : {
        stickyHeader
    },
    props : {
        data_pasien : { type:Object, default:null, required : false },
    },

    data() {
        return {
           no_reg : '',
           no_rm : '',
           nama_pasien : '',
           tgl_lahir : '',
           dokter_utama : '',
           tgl_masuk : '',
           alamat : '',
           alasan_pulang : '',
           penjamain : '',
           harga : '',
           instansi : '',
           tgl_keluar : '',
           hak_kelas : '',
           lama_perawatan : '',
           ruang_perawatan : '',
           kelas : '',
           no_tempat_tidur : '',
           data_ruang : [],
           data_jasa_visit : [],
           obat : [],
        }
    },

    computed: {
      
    },
    mounted() {
        this.callData();       
        this.$nextTick(() => {
            document.querySelector('table.printpdf tbody').appendChild(element);
        });

        function generateTableRow() {
            var emptyColumn = document.createElement('tr');

            emptyColumn.innerHTML = '<td><a class="cut">-</a><span contenteditable></span></td>' +
                '<td><span contenteditable></span></td>' +
                '<td><span data-prefix>$</span><span contenteditable>0.00</span></td>' +
                '<td><span contenteditable>0</span></td>' +
                '<td><span data-prefix>$</span><span>0.00</span></td>';

            return emptyColumn;
        }

        function parseFloatHTML(element) {
            return parseFloat(element.innerHTML.replace(/[^\d\.\-]+/g, '')) || 0;
        }

        function parsePrice(number) {
            return number.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1,');
        }

        /* Update Number
        /* ========================================================================== */

        function updateNumber(e) {
            var
            activeElement = document.activeElement,
            value = parseFloat(activeElement.innerHTML),
            wasPrice = activeElement.innerHTML == parsePrice(parseFloatHTML(activeElement));

            if (!isNaN(value) && (e.keyCode == 38 || e.keyCode == 40 || e.wheelDeltaY)) {
                e.preventDefault();

                value += e.keyCode == 38 ? 1 : e.keyCode == 40 ? -1 : Math.round(e.wheelDelta * 0.025);
                value = Math.max(value, 0);

                activeElement.innerHTML = wasPrice ? parsePrice(value) : value;
            }

            updateInvoice();
        }

        /* Update Invoice
        /* ========================================================================== */

        function updateInvoice() {
            var total = 0;
            var cells, price, total, a, i;

            // update printpdf cells
            // ======================

            for (var a = document.querySelectorAll('table.printpdf tbody tr'), i = 0; a[i]; ++i) {
                // get printpdf row cells
                cells = a[i].querySelectorAll('span:last-child');

                // set price as cell[2] * cell[3]
                price = parseFloatHTML(cells[2]) * parseFloatHTML(cells[3]);

                // add price to total
                total += price;

                // set row total
                cells[4].innerHTML = price;
            }

            // update balance cells
            // ====================

            // get balance instansi cells
            cells = document.querySelectorAll('table.balance td:last-child span:last-child');

            // get balance pasien cells
            cells = document.querySelectorAll('table.balance-pasien td:last-child span:last-child');

            // set total
            cells[0].innerHTML = total;

            // set balance and meta balance
            cells[2].innerHTML = document.querySelector('table.printpdf tr:last-child td:last-child span:last-child').innerHTML = parsePrice(total - parseFloatHTML(cells[1]));

            // update prefix formatting
            // ========================

            var prefix = document.querySelector('#prefix').innerHTML;
            for (a = document.querySelectorAll('[data-prefix]'), i = 0; a[i]; ++i) a[i].innerHTML = prefix;

            // update price formatting
            // =======================

            for (a = document.querySelectorAll('span[data-prefix] + span'), i = 0; a[i]; ++i) if (document.activeElement != a[i]) a[i].innerHTML = parsePrice(parseFloatHTML(a[i]));
        }

        /* On Content Load
        /* ========================================================================== */

        function onContentLoad() {
            updateInvoice();

            var
            input = document.querySelector('input'),
            image = document.querySelector('img');

            function onClick(e) {
                var element = e.target.querySelector('[contenteditable]'), row;

                element && e.target != document.documentElement && e.target != document.body && element.focus();

                if (e.target.matchesSelector('.add')) {
                    document.querySelector('table.printpdf tbody').appendChild(generateTableRow());
                }
                else if (e.target.className == 'cut') {
                    row = e.target.ancestorQuerySelector('tr');

                    row.parentNode.removeChild(row);
                }

                updateInvoice();
            }

            function onEnterCancel(e) {
                e.preventDefault();

                image.classList.add('hover');
            }

            function onLeaveCancel(e) {
                e.preventDefault();

                image.classList.remove('hover');
            }

            function onFileInput(e) {
                image.classList.remove('hover');

                var
                reader = new FileReader(),
                files = e.dataTransfer ? e.dataTransfer.files : e.target.files,
                i = 0;

                reader.onload = onFileLoad;

                while (files[i]) reader.readAsDataURL(files[i++]);
            }

            function onFileLoad(e) {
                var data = e.target.result;

                image.src = data;
            }

            if (window.addEventListener) {
                document.addEventListener('click', onClick);

                document.addEventListener('mousewheel', updateNumber);
                document.addEventListener('keydown', updateNumber);

                document.addEventListener('keydown', updateInvoice);
                document.addEventListener('keyup', updateInvoice);

                input.addEventListener('focus', onEnterCancel);
                input.addEventListener('mouseover', onEnterCancel);
                input.addEventListener('dragover', onEnterCancel);
                input.addEventListener('dragenter', onEnterCancel);

                input.addEventListener('blur', onLeaveCancel);
                input.addEventListener('dragleave', onLeaveCancel);
                input.addEventListener('mouseout', onLeaveCancel);

                input.addEventListener('drop', onFileInput);
                input.addEventListener('change', onFileInput);
            }
        }

    window.addEventListener && document.addEventListener('DOMContentLoaded', onContentLoad);

    },

    methods : {
        ...mapActions('pdf',['dataPatologi','dataRawatInap']),

        tandaTanganPetugas() {
            return {
            marginTop: '50px',
            marginBottom: '50px',
            backgroundImage: "url('../../../Assets/signatures/ttd-petugas.png')",
            };
        },

        tandaTanganPenerima() {
            return {
            marginTop: '50px',
            marginBottom: '50px',
            };
        },

        totalHarga() {
            let total = 0;
            for (let i = 0; i < this.data_ruang.length; i++) {
            const dr = this.data_ruang[i];
            total += dr.harga;
            }
            return total;
        },

        kolomHarga() {
            let jumlah = 0;
            for (let i = 0; i < this.data_ruang.length; i++) {
            const dr = this.data_ruang[i];
            const harga = dr.qty * dr.satuan;
            jumlah += harga;
            }
            return jumlah;
        },

        belumBayar() {
            const totalHarga = this.totalHarga();
            const belumDibayar = parseFloat(this.$refs.belumDibayar.innerText.replace("Rp. ", ""));
            return totalHarga - belumDibayar;
        },

        fillData(res) {
            this.no_reg = res.no_reg;
            this.no_rm = res.no_rm;
            this.nama_pasien = res.nama_pasien;
            this.tgl_lahir = res.tgl_lahir;
            this.dokter_utama = res.dokter_utama;
            this.tgl_masuk = res.tgl_masuk;
            this.alamat = res.alamat;
            this.alasan_pulang = res.alasan_pulang;
            this.penjamin = res.penjamin;
            this.instansi = res.instansi;
            this.harga = res.harga;
            this.tgl_keluar = res.tgl_keluar;
            this.hak_kelas = res.hak_kelas;
            this.lama_perawatan = res.lama_perawatan;
            this.ruang_perawatan = res.ruang_perawatan;
            this.kelas = res.kelas;
            this.no_tempat_tidur = res.no_tmpt_tidur;
        },

       
        printDiv(pdf_body) {
            let contents = document.getElementById(pdf_body).innerHTML;
            var printWindow = window.open('', '', 'height=400,width=800');
            var style = document.createElement('style');
            var css = '';
            printWindow.document.write('<html lang="en"><head><title>SIMRS PRINT</title>');
            printWindow.document.write('<head><style>');
            printWindow.document.write('body { width:8in; background-color: #ffffff;}');
            printWindow.document.write('</style>');
            printWindow.document.write('<link rel="stylesheet" type="text/css" href="http://localhost:8000/uikit/css/uikit.min.css"/>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(contents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            setTimeout(function () {
                printWindow.print();
                printWindow.close();
            }, 1500);
            return false;
        },

        callData(){
            this.dataRawatInap().then((response) => {
                if (response.success == true) {
                    this.fillData(response.data.data_rawat_inap);
                    this.data_ruang = response.data.data_transaksi.ruangan;
                    this.obat = response.data.data_transaksi.obat;
                    this.data_jasa_visit = response.data.data_transaksi.jasa_visit_konsultasi;
                }
                else { alert('data rawat inap tidak ditemukan'); this.isLoading = false; }
            })
        }

    }
}
</script>
<style>
.logo-pdf {
    width: 90px;
    height: auto;
}

@font-face {
  font-family: 'cour';
  src: url('../../../../Assets/fonts/cour.ttf') format('truetype');
}

@font-face {
  font-family: 'cour-bold';
  src: url('../../../../Assets/fonts/cour-bold.ttf') format('truetype');
}

@font-face {
  font-family: 'verdana';
  src: url('../../../../Assets/fonts/verdana.ttf') format('truetype');
}

@font-face {
  font-family: 'verdana-light';
  src: url('../../../../Assets/fonts/verdanaPro-light.ttf') format('truetype');
}

@font-face {
  font-family: 'verdana-bold';
  src: url('../../../../Assets/fonts/verdana-bold.ttf') format('truetype');
}
@font-face {
  font-family: 'verdana-bold-italic';
  src: url('../../../../Assets/fonts/verdana-bold-italic.ttf') format('truetype');
}
.paper-like {
    background: #FFF; 
    border-radius: 3px; 
    box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    width: 297mm;
    /* height: 420mm; */
    height: auto;
    border: 0.3px solid black;
}

/*  TABLE CONFIG */
/* content editable */

*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

*[contenteditable] { cursor: pointer; }

*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 10em 0.5em #DEF; }

span[contenteditable] { display: inline-block; }

/* heading */

.vertical-header {
    vertical-align: middle;
    text-align: center;
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    width: 10px;
}

h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }

/* table */

table { font-size: 75%; width: 100%; }
/* table { border-collapse: separate; border-spacing: 2px; } */
th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
/* th, td { border-top: solid; } */
th { background: #EEE; border-color: #BBB; }
/* th { background:#f9f9f9; border-color:#BBB;} */
td { border-color: #DDD; }

/* header */

header { margin: 0 0 3em; }
header:after { clear: both; content: ""; display: table; }

header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
header address p { margin: 0 0 0.25em; }
header span, header img { display: block; float: right; }
header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
header img { max-height: 100%; max-width: 100%; }
header input { cursor: pointer; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

/* article */

article, article address, table.meta, table.printpdf { margin: 0 0 3em; }
article:after { clear: both; content: ""; display: table; }
article h1 { clip: rect(0 0 0 0); position: absolute; }

article address { float: left; font-size: 125%; font-weight: bold; }

/* table meta & balance instansi */

table.meta, table.balance { float: right; width: 65%; }
table.meta:after, table.balance:after { clear: both; content: ""; display: table; }

/* table meta & balance pasien */

table.meta, table.balance-pasien { float: left; width: 65%; }
table.meta:after, table.balance-pasien:after { clear: both; content: ""; display: table; }


/* table meta */

table.meta th { width: 40%;vertical-align: middle; }
table.meta td { width: 60%; }

/* table items */

/* table.printpdf { clear: both; width: 100%; } */
/* table.printpdf th { font-weight: bold; text-align: center; } */
/* 
table.printpdf td:nth-child(1) { width: 26%; }
table.printpdf td:nth-child(2) { width: 38%; }
table.printpdf td:nth-child(3) { text-align: right; width: 12%; }
table.printpdf td:nth-child(4) { text-align: right; width: 12%; }
table.printpdf td:nth-child(5) { text-align: right; width: 12%; } */

/* table balance instansi */

table.balance th, table.balance td { width: 50%; }
table.balance td { text-align: left; }

/* table balance pasien */
table.balance-pasien th, table.balance-pasien td { width: 50%; }
table.balance-pasien td { text-align: left; }
/* aside */

aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
aside h1 { border-color: #999; border-bottom-style: solid; }

/* javascript */

.add, .cut
{
	border-width: 1px;
	display: block;
	font-size: .8rem;
	padding: 0.25em 0.5em;	
	float: left;
	text-align: center;
	width: 0.6em;
}

.add, .cut
{
	background: #9AF;
	box-shadow: 0 1px 2px rgba(0,0,0,0.2);
	background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
	background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
	border-radius: 0.5em;
	border-color: #0076A3;
	color: #FFF;
	cursor: pointer;
	font-weight: bold;
	text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
}

.add { margin: -2.5em 0 0; }

.add:hover { background: #00ADEE; }

.cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }
.cut { -webkit-transition: opacity 100ms ease-in; }

tr:hover .cut { opacity: 1; }

@media print {
	* { -webkit-print-color-adjust: exact; }
	html { background: none; padding: 0; }
	body { box-shadow: none; margin: 0; }
	span:empty { display: none; }
	.add, .cut { display: none; }
}

/* custom checkbox */
.uk-checkbox:checked, .uk-checkbox:indeterminate, .uk-radio:checked {
    /* background-color: #cc0202; */
    background-color: black;
    border-color: white;
    /* border-radius: 3px; */
}
.uk-checkbox:checked {
    background-image: url(@/Assets/checklist-white-14x9px.svg);
    background-size: 16px;

}

.uk-checkbox, .uk-radio {
    display: inline-block;
    height: 16px;
    width: 16px;
    overflow: hidden;
    margin-top: -4px;
    vertical-align: middle;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: transparent;
    background-repeat: no-repeat;
    background-position: 50% 50%;
    border: 1px solid #ccc;
    border-radius: 3px;
    transition: .2s ease-in-out;
    transition-property: background-color,border;
}
</style>
