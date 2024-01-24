<template>
    <div style="padding: 3em;">
        <div style="position: sticky;top:10px;z-index:100;">
            <div class="uk-width-1-1 uk-text-right">
                <input class="submit" type="submit" value="Print" style="margin-right:-120px" @click="printDiv('cetakGelangDewasa')">
            </div>
        </div>
        <div class="uk-container paper-like" style="padding:0 1em 10em;height:10in;width:2in;" id="cetakGelangDewasa">
            <ul style="padding:0;margin:0;">
                <li style="padding:0;margin:0;">
                    <div style="padding:1em 0 1em 0;" class="uk-container">
                        <div style="padding:0; margin-top:1em; border-radius:10px;">
                            <table style="width: 11.8in;height:1in;font-size:14px;letter-spacing:0.5px;rotate:270deg;margin-top: 4.6in;margin-left: -5in;" class="uk-margin-small">
                                <tr>
                                    <td colspan="3" style="font-weight:bold;">
                                        <img style="width: 200px;height: 60px;" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR6G98OoDQL2OBcX3cZeH6YjUsA2ZzRRmclirULowJ7k1BIpMoxtPDJ3uVfjWf4pHxkn7Y&usqp=CAU">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-weight:bold;">HAFIIZH ASROFIL AL BANNA</td>
                                </tr>
                                <tr>
                                    <td style="width:6%;">No RM</td>
                                    <td style="width:2%;">:</td>
                                    <td >134287</td>
                                </tr>
                                <tr>
                                    <td style="width:6%;">Tgl Lahir</td>
                                    <td style="width:2%;">:</td>
                                    <td >09/10/1986</td>
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
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

export default {
    name: 'cetak-gelang-dewasa',
    components : {
        html2canvas, jsPDF
    },
    props : {
    },

    data() {
        return {
         
        }
    },

    computed: {
      
    },
    mounted() {

    },

    methods : {
        printToPDF() {
                const content = this.$refs.cetakGelangDewasa;
                const options = {
                filename: 'gelang-dewasa.pdf',
                jsPDF: { format: 'a4' },
                html2canvas: { 
                    scale: 1,
                }
            };

            html2canvas(content, { useCORS: true })
                .then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jsPDF();

                    const imgProps = pdf.getImageProperties(imgData);
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                    pdf.save('gelang-dewasa.pdf');
                })
                .catch(error => {
                    console.error('Error printing to PDF:', error);
                });
            
        },
        printDiv(pdf_body) {
            let contents = document.getElementById(pdf_body).innerHTML;
            var printWindow = window.open('', '', 'height=400,width=800');
            var style = document.createElement('style');
            var css = '';
            printWindow.document.write('<html lang="en"><head><title>SIMRS PRINT</title>');
            printWindow.document.write('<head><style>');
            printWindow.document.write('body { width:8in; background-color: #ffffff;}');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write(contents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            setTimeout(function () {
                printWindow.print();
                printWindow.close();
            }, 1500);
            return false;
        },
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
