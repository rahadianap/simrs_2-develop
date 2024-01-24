<template>
    <div style="padding: 2em;">
        <div class="uk-container paper-like" style="padding:auto;width:950px;height:800px;">
            <div style="position: sticky;top:10px;z-index:100;">
                <div class="uk-width-1-1 uk-text-right">
                    <input class="submit" type="submit" value="Print" style="margin-right:-120px" @click="printDiv('cetakanTracer')">
                </div>
            </div>

            <!-- <div v-html="htmlContent" id="htmlContent"></div> -->

            <ul style="padding:0;margin:0;" ref="cetakanTracer" id="cetakanTracer">
                <li style="padding:0;margin:0;">
                    <div style="padding:1em 2em;" class="uk-container">
                        <div style="padding:0; margin-top:1em; border-radius:10px;min-height:400px;" id="pdf-body-b">
                            <div class="uk-container uk-flex uk-flex-middle uk-card-body">
                                <div class="uk-width-1-4">
                                    <div class="uk-flex uk-flex-center uk-flex-middle">
                                        <img src="../../../../Assets/aviat-logo.webp" class="logo-pdf uk-text-center uk-responsive-width">
                                    </div>
                                </div>
                                <div class="uk-width-1-2">
                                    <div class="uk-flex-center uk-text-center">
                                        <p style="font-size: 20px;font-family: verdana-bold;font-weight:bold;letter-spacing:1.1px;">Rumah Sakit Kebagusan Jaksel</p>
                                        <p class="uk-margin-small" style="font-size: 16px;font-family: verdana;letter-spacing:1px;">Jl. Kebagusan Raya No.192 Pasar Minggu, Jaksel</p>
                                        <p class="uk-margin-small" style="font-size: 16px;font-family: verdana;letter-spacing:1px;">Telp. +62-21-78838951 | Email: support@kp3.co.id</p>
                                    </div>
                                </div>
                            </div>

                            <hr style="border:0.2px solid #cc0202;" />

                            <!-- Judul Laporan -->
                            <div class="uk-align-center uk-margin-medium">
                                <div>
                                    <p class="uk-margin-small" style="font-size:20px;font-weight:bold;text-align:center;font-family: verdana-bold;letter-spacing:1.1px;">TRACER</p>
                                </div>
                            </div>

                            <!-- Data TRACER     -->
                            <table style="width:100%;font-size:13px;letter-spacing:0.5px;" class="uk-margin-small">
                                <tr>
                                    <!-- Kiri -->
                                    <td style="font-family:cour;font-weight:bold;">No.Reg / No.RM</td>
                                    <td style="font-family:cour">: {{ no_reg }} | {{ no_rm }}</td>
                                    <!-- Kanan -->
                                    <td style="font-family:cour;font-weight:bold;">Tanggal Cetak</td>
                                    <td style="font-family:cour">: 01 Juli 2023</td>
                                </tr>
                                <tr>
                                    <!-- Kiri -->
                                    <td style="font-family:cour;font-weight:bold;">Nama Pasien</td>
                                    <td style="font-family:cour">: {{ nama_pasien }}</td>
                                    <!-- Kanan -->
                                    <td style="font-family:cour;font-weight:bold;">Penjamin</td>
                                    <td style="font-family:cour">: {{ penjamin }}</td>
                                </tr>
                                <tr>
                                    <!-- Kiri -->
                                    <td style="font-family:cour;font-weight:bold;">Lokasi BRM</td>
                                    <td style="font-family:cour">: <span contenteditable>Contoh Nama Lokasi</span></td>
                                    <!-- kanan -->
                                    <td style="font-family:cour;font-weight:bold;">Tanggal Registrasi</td>
                                    <td style="font-family:cour">: <span contenteditable>25 Juni 2023 13:08</span></td>                                    </tr>

                            </table>                                
                            <!-- Data Registrasi -->
                            <table style="width:100%;" class="uk-table uk-table-small uk-table-hover uk-margin-medium printpdf" >
                                <thead style="font-size:13px;font-family: cour;font-weight:bold;">
                                    <tr>
                                        <th><span>No.</span></th>
                                        <th><span contenteditable>Tujuan Berobat</span></th>
                                        <th ><span contenteditable>Dokter</span></th>
                                        <th><span contenteditable>Catatan Dokter</span></th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:13px;font-family: cour;">
                                    <tr>
                                        <td><span contenteditable>1. </span></td>
                                        <td><span contenteditable>POLI MATA</span></td>
                                        <td><span contenteditable>Drs. Tri Wahyuni Sp.M.</span></td>
                                        <td><span contenteditable>
                                            <ul>
                                                <li>
                                                    Catatan poin pertama
                                                </li>
                                                <li>
                                                    Catatan poin kedua
                                                </li>
                                            </ul>
                                        </span></td>
                                    </tr>
                                </tbody>
                            </table>
                                
                        </div>
                        <!-- <a class="add" id="generateTableRow">+</a> -->
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
// import stickyHeader from '../components/StickyHeader/index.vue';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

export default {
    name: 'cetak-tracer',
    components : {
        
    },
    props : {
        data_pasien : { type:Object, default:null, required : false },
    },

    data() {
        return {
           htmlContent: '',
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
        this.bladeContent();  
        this.$nextTick(() => {
            document.querySelector('table.printpdf tbody').appendChild(element);
        });


/* Helper Functions
/* ========================================================================== */

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
        ...mapActions('pdf',['dataPatologi','dataRender','dataRawatInap']),

        bladeContent() {
            this.dataRender().then((response) => {
                var tag_id = document.getElementById('htmlContent');
                var newNode = document.createElement('p');
                newNode.appendChild(document.createTextNode('html string'));
                this.htmlContent = response;
            })
        },

        printDiv(pdf_body) {
            let contents = document.getElementById(pdf_body).innerHTML;
            let frame1 = document.createElement('iframe');
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            let frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();
            frameDoc.document.write('<html lang="en"><head><title>Fiche Client</title>');
            frameDoc.document.write('<link rel="stylesheet" type="text/css" href="http://localhost:8000/uikit/css/uikit.min.css"/>');
            frameDoc.document.write('</head><body>');
            frameDoc.document.write('<style>@page { zoom: 90%; } hr { margin:0px !important; } </style>');
            frameDoc.document.write('<div class="print-content" style="font-family:cour">'+contents+'</div>');
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 1000);
           return false;
        },

        // printToPDF() {
        //     const content = this.$refs.cetakanTracer;
        //     const options = {
        //         filename: 'dokumen.pdf',
        //         jsPDF: { format: 'a4' },
        //         html2canvas: { scale: 0 }, // scale adalah untuk scala zooming canvas nya
        //     };

        //     html2canvas(content, { useCORS: true }) //useCross true berfungsi untuk mengaktifkan crossOrigin jika style css maupun image dari eksternal agar bisa di render ke cetakan pdf
        //     .then(canvas => {
        //     const imgData = canvas.toDataURL('image/png');
        //     const pdf = new jsPDF('', '','a4',true); //jsPDF(orientation, unit, format, compressPdf) | p adalah potrait/l adalah landscape, cm adalah unit centimeter (kalo pake point/pixel ganti jadi pt), a4 formatnya, true utk compress size file pdf
        //     const date = new Date().toLocaleString('id-ID', {
        //         weekday: 'long',
        //         day: 'numeric',
        //         month: 'long', 
        //         year: 'numeric', 
        //         hour: 'numeric', 
        //         minute: 'numeric', 
        //         hour12: true 
        //     }); //timestamp cetakan berdasarkan wilayah. Format lengkap dengan nama hari ( hari, dd mm yyy pukul HH.MM am/\pm )
        //     const timestamp = "Dicetak pada " + date;

        //     const pages = pdf.internal.getNumberOfPages();
        //     const imgProps = pdf.getImageProperties(imgData);
        //     const pdfWidth = pdf.internal.pageSize.getWidth(); //mengatur lebar font & imgae ke hasil cetakan
        //     const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width; //mengatur tinggi & imgae ke font hasil cetakan

        //     let totalPages = Math.ceil(pdfHeight / pdf.internal.pageSize.getHeight());

        //     const pageWidth = pdf.internal.pageSize.width; 
        //     const pageHeight = pdf.internal.pageSize.height; 

        //     pdf.setProperties({
        //         title: 'Dokumen Cetakan', //penamaan judul file pdf
        //         author: 'Aviat.id',
        //         creator: 'HIMS powered by PT. Prima Karya Putera Perkasa'
        //     });
        //     pdf.addImage(imgData, 'PNG', 0, 8, pdfWidth, pdfHeight, undefined, 'FAST'); //mencetak canvas include dgn image. 8 merupakan margin-top untuk canvas dari cetakan html nya. nilai 0 adalah margin-left nya

        //     //  config halaman awal
        //     for (let firstPage = 1; firstPage <= pages; firstPage++) {
        //         let horizontalPos = pageWidth / 2; 
        //         let verticalPos = pageHeight - 6;  //margin bottom pdf.text
        //         pdf.setFont("verdana", "bold");
        //         pdf.setFontSize(9);
        //         pdf.setTextColor(102,102,102);
        //         pdf.setPage(firstPage);
        //         pdf.text(`${firstPage} of ${pages}`, horizontalPos + 80, verticalPos - 3, { textAlign: 'right' }); //page numbering di pojok kanan bawah
        //         pdf.text(timestamp, horizontalPos - 90, verticalPos - 3, { textAlign: 'left' }); //copyright maupun timestamp di pojok kiri bawah
        //     }

        //     // config untuk page selanjutnya/autopaging
        //     for (let nextPage = 2; nextPage <= totalPages; nextPage++) {
        //         pdf.addPage();
        //         pdf.addImage(imgData, 'PNG', 0, -pdf.internal.pageSize.getHeight() * (nextPage - 1), pdfWidth, pdfHeight, undefined, 'FAST');
                
        //         let horizontalPos = pageWidth / 2;  
        //         let verticalPos = pageHeight - 6;

        //         pdf.setFont("verdana", "bold");
        //         pdf.setFontSize(9);
        //         pdf.setTextColor(102,102,102);
        //         pdf.setPage(nextPage);
        //         pdf.text(`${nextPage} of ${totalPages}`, horizontalPos + 80, verticalPos - 3, { textAlign: 'right' }); //page numbering di pojok kanan bawah
        //         pdf.text(timestamp, horizontalPos - 90, verticalPos - 3, { textAlign: 'left' }); //copyright maupun timestamp di pojok kiri bawah
        //     }

        //         // pdf.save('dokumen.pdf'); // lgsg download
        //         pdf.output('dataurlnewwindow'); // menampilkan output di tab baru
        //     })
        //     .catch(error => {
        //         console.error('Error printing to PDF:', error);
        //     });
        // },

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
            let frame1 = document.createElement('iframe');
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            let frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();
            frameDoc.document.write('<html lang="en"><head><title>Fiche Client</title>');
            frameDoc.document.write('<link rel="stylesheet" type="text/css" href="http://localhost:8000/uikit/css/uikit.min.css"/>');
            frameDoc.document.write('<link rel="stylesheet" type="text/css" href="http://localhost:8000/css/hims.css"/>');
            frameDoc.document.write('</head><body>');
            frameDoc.document.write('<style>@page { zoom: 90%; } hr { margin:0px !important; } </style>');
            frameDoc.document.write('<div class="print-content">'+contents+'</div>');
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 1000);
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

</style>
