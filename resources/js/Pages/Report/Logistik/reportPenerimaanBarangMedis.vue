<template>
    <div>
        <vue3-html2pdf :show-layout="false" :float-layout="true" :enable-download="false" :preview-modal="true"
            :paginate-elements-by-height="1400" filename="hee hee" :pdf-quality="2" :manual-pagination="false"
            pdf-format="a4" pdf-orientation="landscape" pdf-content-width="1200px"
            @hasStartedGeneration="hasStartedGeneration()" @hasGenerated="hasGenerated($event)" ref="html2Pdf">

            <template v-slot:pdf-content>
                <div class="uk-grid-small" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-emphasis" style="text-align:center;color:black;margin-left:28px;margin-top:28px;font-size:20px;font-weight: 500;">LAPORAN PENERIMAAN BARANG MEDIS</h3>
                        </div>
                    </div>
                </div>
                <div class="uk-margin-auto uk-margin-auto-vertical" uk-grid>
                    <div>
                        <div>
                            <table align="center" class="css-serial" style="font-size:10px;font-weight:500;border:0.1px solid #000000;font-family: arial,sans-serif;border-collapse:collapse;width:100%;">
                                <tr>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 30px">No.</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 75px">Tanggal</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 100px">Inv</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 150px">Nama Produk</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Jenis Item</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 100px">Supplier</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Jumlah</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Unit</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Harga</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Diskon</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">PPN</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Total</th>
                                </tr>
                                <tr v-for="dt in reportData.detail">
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 30px"></td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 75px">{{dt['tanggal']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 100px">{{dt['inv']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 150px">{{dt['produk']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['jenis']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 100px">{{dt['supplier']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['jumlah']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['unit']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: right;padding: 8px;width: 50px">{{dt['harga']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: right;padding: 8px;width: 50px">{{dt['diskon']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: right;padding: 8px;width: 50px">{{dt['ppn']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: right;padding: 8px;width: 50px">{{dt['total']}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
        </vue3-html2pdf>
    </div>
</template>

<script>
import Vue3Html2pdf from 'vue3-html2pdf';

export default {
    name: 'report-penerimaan-barang-medis',
    components: {
        Vue3Html2pdf
    },
    data() {
        return {
            reportData: {
                item_name: []
            },
        }
    },
    computed: {
        htmlToPdfOptions() {
            return {
                margin: 0,
                filename: "hee hee.pdf",
                image: {
                    type: "jpeg",
                    quality: 2,
                },
                enableLinks: true,
                html2canvas: {
                    scale: 1,
                    useCORS: true,
                },
                jsPDF: {
                    unit: "mm",
                    format: "a4",
                    orientation: 'landscape',
                },
            };
        },
    },
    methods: {
        getCurrency(params) {
            let curr = new Intl.NumberFormat("id-ID", { maximumSignificantDigits: 2 }).format(params);
            return curr;
        },

        getDate(params) {
            const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            let res = new Date(params);
            let year = res.getFullYear();
            let month = months[res.getMonth()];
            if (month < 10) { month = `0${month}` }
            let date = res.getDate();
            if (date < 10) { date = `0${date}` }
            return `${date} ${month} ${year}`;
        },

        generateReport(data) {
            this.reportData.detail = [];
            for(let i=0;i<=data.length-1;i++) {
                this.reportData.detail.push({
                    'tanggal': data[i].tgl_transaksi,
                    'inv': data[i].no_invoice,
                    'produk': data[i].produk_nama,
                    'jenis': data[i].jenis_produk,
                    'supplier': data[i].vendor_nama,
                    'jumlah': data[i].jml_por,
                    'unit': data[i].satuan_beli,
                    'harga': this.getCurrency(data[i].harga),
                    'diskon': this.getCurrency(data[i].nilai_diskon),
                    'ppn': this.getCurrency(data[i].nilai_pajak),
                    'total': this.getCurrency(data[i].subtotal),
                });
            }
            this.$refs.html2Pdf.generatePdf();
        }
    },
}
</script>

<style type="text/css">
/* Penomoran otomatis pada baris */
.css-serial {
  counter-reset: serial-number;  /* Atur penomoran ke 0 */
}
.css-serial td:first-child:before {
  counter-increment: serial-number;  /* Kenaikan penomoran */
  content: counter(serial-number);  /* Tampilan counter */
}
</style>