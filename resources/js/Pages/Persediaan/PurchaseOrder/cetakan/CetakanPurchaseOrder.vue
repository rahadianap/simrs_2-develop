<template>
    <div>
        <vue3-html2pdf :show-layout="false" :float-layout="true" :enable-download="false" :preview-modal="true"
            :paginate-elements-by-height="1400" filename="hee hee" :pdf-quality="2" :manual-pagination="false"
            pdf-format="a4" pdf-orientation="landscape" pdf-content-width="800px"
            @hasStartedGeneration="hasStartedGeneration()" @hasGenerated="hasGenerated($event)" ref="html2Pdf">

            <template v-slot:pdf-content>
                <div class="uk-grid-small" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-emphasis" style="color:black;margin-left:28px;margin-top:28px;font-size:20px;font-weight: 500;">FORM PEMBELIAN BARANG</h3>
                        </div>
                    </div>
                </div>
                <div class="uk-grid-small" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-28px;font-size:14px;font-weight: 500;">Nomor Dokumen
                            </h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-10px;font-size:14px;font-weight: 500;">Tanggal Dokumen
                            </h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-10px;font-size:14px;font-weight: 500;">Vendor
                            </h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-28px;font-size:14px;font-weight: 500;">{{':  ' +
                            reportData.no_transaksi}}</h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-10px;font-size:14px;font-weight: 500;">{{':  ' +
                            reportData.tgl_transaksi}}</h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-10px;font-size:14px;font-weight: 500;">{{':  ' +
                            reportData.vendor}}</h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-28px;font-size:14px;">Unit
                            </h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-10px;font-size:14px;">Pembayaran
                            </h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-28px;font-size:14px;font-weight: 500;">{{':  ' +
                            reportData.unit}}</h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-10px;font-size:14px;font-weight: 500;">{{':  ' +
                            reportData.termin}}</h3>
                        </div>
                    </div>
                </div>
                <div class="uk-margin-auto uk-margin-auto-vertical" uk-grid>
                    <div>
                        <div>
                            <table align="center" class="css-serial" style="font-size:14px;font-weight:500;border:0.1px solid #000000;font-family: arial,sans-serif;border-collapse:collapse;width:100%;">
                                <tr>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 30px">No.</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 100px">Jenis Produk</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 400px">Nama Produk</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Jumlah</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Satuan</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 100px">Harga</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 100px">Subtotal</th>
                                </tr>
                                <tr v-for="dt in reportData.detail">
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 30px"></td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 100px">{{dt['jenis_produk']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 400px">{{dt['produk_nama']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['jumlah']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['satuan']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: right;padding: 8px;width: 100px">{{dt['harga']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: right;padding: 8px;width: 100px">{{dt['subtotal']}}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" style="color:black;border: 0.1px solid #000000;background-color:#ffffff;border-collapse:collapse;text-align: right;padding: 8px;width: 30px">Total</th>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: right;padding: 8px;width: 30px">{{reportData.subtotal}}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" style="color:black;border: 0.1px solid #000000;background-color:#ffffff;border-collapse:collapse;text-align: right;padding: 8px;width: 30px">Diskon</th>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: right;padding: 8px;width: 30px">{{reportData.diskon}}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" style="color:black;border: 0.1px solid #000000;background-color:#ffffff;border-collapse:collapse;text-align: right;padding: 8px;width: 30px">PPN</th>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: right;padding: 8px;width: 30px">{{reportData.ppn}}</td>
                                </tr>
                                <tr>
                                    <th colspan="6" style="color:black;border: 0.1px solid #000000;background-color:#ffffff;border-collapse:collapse;text-align: right;padding: 8px;width: 30px">Grand Total</th>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: right;padding: 8px;width: 30px">{{reportData.grandtotal}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="uk-grid-small" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right">
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-10px;font-size:14px;font-weight: 500;">Catatan:
                            </h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-20px;font-size:14px;font-weight: 500;">{{reportData.catatan}}</h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:650px;margin-top:50px;font-size:14px;font-weight: 500;font-style:italic;">Dibuat Oleh</h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:650px;margin-top:75px;font-size:14px;font-weight: 500;">{{'( ' + reportData.created + ' )'}}</h3>
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
    name: 'cetakan-purchase-order',
    components: {
        Vue3Html2pdf
    },
    data() {
        return {
            reportData: {
                no_transaksi: '',
                tgl_transaksi: '',
                unit: '',
                termin: '',
                vendor: '',
                catatan: '',
                created: '',
                subtotal: '',
                diskon: '',
                ppn: '',
                grandtotal: '',
                detail: []
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
                    orientation: 'portrait',
                },
            };
        },
    },
    methods: {
        getCurrency(params) {
            let curr = new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(params);
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
            this.reportData.no_transaksi = data[0].no_transaksi;
            this.reportData.tgl_transaksi = this.getDate(data[0].tgl_transaksi);
            this.reportData.unit = data[0].unit_nama;
            this.reportData.termin = data[0].jenis_bayar + ' / ' + data[0].termin + ' hari';
            this.reportData.vendor = data[0].vendor_nama;
            this.reportData.catatan = data[0].catatan;
            this.reportData.created = data[0].created_by;
            this.reportData.subtotal = this.getCurrency(data[0].subtotal);
            this.reportData.diskon = this.getCurrency(data[0].total_diskon);
            this.reportData.ppn = this.getCurrency(data[0].total_pajak);
            this.reportData.grandtotal = this.getCurrency(data[0].total);
            for(let i=0;i<=data.length-1;i++) {
                this.reportData.detail.push({
                    'jenis_produk': data[i].jenis_produk,
                    'produk_nama': data[i].produk_nama,
                    'jumlah': data[i].jml_po,
                    'satuan': data[i].satuan,
                    'harga': this.getCurrency(data[i].harga),
                    'subtotal': this.getCurrency(data[i].subtotal),
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