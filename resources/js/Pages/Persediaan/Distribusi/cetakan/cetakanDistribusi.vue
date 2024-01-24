<template>
    <div>
        <vue3-html2pdf :show-layout="false" :float-layout="true" :enable-download="false" :preview-modal="true"
            :paginate-elements-by-height="1400" filename="hee hee" :pdf-quality="2" :manual-pagination="false"
            pdf-format="a4" pdf-orientation="portrait" pdf-content-width="800px"
            @hasStartedGeneration="hasStartedGeneration()" @hasGenerated="hasGenerated($event)" ref="html2Pdf">

            <template v-slot:pdf-content>
                <div class="uk-grid-small" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-emphasis" style="color:black;margin-left:28px;margin-top:28px;font-size:20px;font-weight: 500;">FORM DISTRIBUSI INTERNAL BARANG</h3>
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
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-28px;font-size:14px;font-weight: 500;">{{':  ' +
                            reportData.distribusi_id}}</h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-10px;font-size:14px;font-weight: 500;">{{':  ' +
                            reportData.tgl_dibuat}}</h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-28px;font-size:14px;">Depo Pengirim
                            </h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-10px;font-size:14px;">Depo Penerima
                            </h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-28px;font-size:14px;font-weight: 500;">{{':  ' +
                            reportData.depo_pengirim}}</h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-10px;font-size:14px;font-weight: 500;">{{':  ' +
                            reportData.depo_penerima}}</h3>
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
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 300px">Nama Produk</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Jumlah Diminta</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Jumlah Diterima</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 70px">Satuan</th>
                                </tr>
                                <tr v-for="dt in reportData.detail">
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 30px"></td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 100px">{{dt['jenis_produk']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 300px">{{dt['produk_nama']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['jumlah_diminta']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['jumlah_dikirim']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 70px">{{dt['satuan']}}</td>
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
    name: 'cetakan-distribusi',
    components: {
        Vue3Html2pdf
    },
    data() {
        return {
            reportData: {
                distribusi_id: '',
                depo_pengirim: '',
                depo_penerima: '',
                tgl_dibuat: '',
                tgl_diterima: '',
                status: '',
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
            this.reportData.distribusi_id = data[0].distribusi_id;
            this.reportData.jumlah_diminta = data[0].jumlah_diminta;
            this.reportData.jumlah_diterima = data[0].jumlah_dikirim;
            this.reportData.tgl_dibuat = this.getDate(data[0].tgl_dibuat);
            this.reportData.depo_pengirim = data[0].depo_pengirim;
            this.reportData.depo_penerima = data[0].depo_penerima;
            this.reportData.catatan = data[0].catatan;
            this.reportData.created = data[0].created_by;
            for(let i=0;i<=data.length-1;i++) {
                console.log(data);
                this.reportData.detail.push({
                    'jenis_produk': data[i].jenis_produk,
                    'produk_nama': data[i].produk_nama,
                    'jumlah_diminta': data[i].jml_diminta,
                    'jumlah_dikirim': data[i].jml_dikirim,
                    'satuan': data[i].satuan,
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