<template>
    <div style="padding: 3em;">
        <div class="uk-container paper-like" style="padding:0 1em 10em;height: auto;">
            <div style="position: sticky;top:10px;z-index:100;">
                <div class="uk-width-1-1 uk-text-right">
                    <input class="submit" type="submit" value="Print" style="margin-right:-120px" @click="printDiv('laporanJumlahResep')">
                </div>
            </div>
            <ul style="padding:0;margin:0;"  id="laporanJumlahResep">
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
                                    <p class="uk-margin-small" style="font-size:16px;font-weight:bold;text-align:center;font-family: verdana-bold;letter-spacing:1.1px;text-decoration: underline">LAPORAN JUMLAH RESEP (ITEM OBAT)</p>
                                    <p class="uk-margin-small" style="font-size:12px;font-weight:bold;text-align:center;">PERIODE : {{ this.periode }}</p>
                                </div>
                            </div>
                            <table style="border:0.5px solid black;width:100%;font-size:11px;letter-spacing:0.5px;" class="uk-margin-small">
                                <thead>
                                    <tr>
                                        <th style="border-bottom:0.5px solid black;text-align:center;">Tgl Resep</th>
                                        <th style="border-bottom:0.5px solid black;text-align:center;">Nama Item</th>
                                        <th style="border-bottom:0.5px solid black;text-align:center;">Qty</th>
                                        <th style="border-bottom:0.5px solid black;text-align:center;">Penjamin</th>
                                    </tr>
                                </thead>
                                <tbody v-if="data_rekap" v-for="drekap in data_rekap">
                                    <tr>
                                        <td colspan="4" style="border-top:0.5px solid black;border-bottom:0.5px solid black;">No. Resep : {{ drekap.no_resep }}</td>
                                    </tr>
                                    <tr v-for="ditem in drekap.item">
                                        <td>{{ ditem.tgl_resep }}</td>
                                        <td>{{ ditem.item_nama }}</td>
                                        <td>{{ ditem.jml_resep }}</td>
                                        <td >{{ ditem.penjamin_nama }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-if="data_rekap" v-for="drekap in data_rekap">
                                    <tr>
                                        <td colspan="4" style="border-top:0.5px solid black;border-bottom:0.5px solid black;">No. Resep : {{ drekap.no_resep }}</td>
                                    </tr>
                                    <tr v-for="ditem in drekap.item">
                                        <td>{{ ditem.tgl_resep }}</td>
                                        <td>{{ ditem.item_nama }}</td>
                                        <td>{{ ditem.jml_resep }}</td>
                                        <td >{{ ditem.penjamin_nama }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-if="data_rekap" v-for="drekap in data_rekap">
                                    <tr>
                                        <td colspan="4" style="border-top:0.5px solid black;border-bottom:0.5px solid black;">No. Resep : {{ drekap.no_resep }}</td>
                                    </tr>
                                    <tr v-for="ditem in drekap.item">
                                        <td>{{ ditem.tgl_resep }}</td>
                                        <td>{{ ditem.item_nama }}</td>
                                        <td>{{ ditem.jml_resep }}</td>
                                        <td >{{ ditem.penjamin_nama }}</td>
                                    </tr>
                                </tbody>
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

export default {
    name: 'laporan-jumlah-resep',
    components : {
    },
    props : {
    },

    data() {
        return {
            data_rekap : [],
            periode : '',
        }
    },

    computed: {
      
    },
    mounted() {
        this.callData();       
    },

    methods : {
        ...mapActions('pdf',['dataLapJumlahResep']),

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
            this.dataLapJumlahResep().then((response) => {
                if (response.success == true) {
                    this.data_rekap = response.data.data;
                    this.periode = response.data.periode;
                }
                else { alert('data LAPORAN JUMLAH RESEP (ITEM OBAT) KARYAWAN tidak ditemukan'); this.isLoading = false; }
            })
        }

    }
}
</script>

<style>
#pageFooter
{
    page-break-before: always;
    counter-increment: page;
}
#pageFooter:after
{
    display: block;
    text-align: right;
    content: "Page " counter(page);
}
#pageFooter.first.page
{
    page-break-before: avoid;
}
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
