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
                            <h3 class="uk-text-emphasis" style="color:black;margin-left:28px;margin-top:28px;font-size:20px;font-weight: 500;">KARTU STOCK</h3>
                        </div>
                    </div>
                </div>
                <div class="uk-grid-small" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-28px;font-size:14px;font-weight: 500;">Nama Barang
                            </h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-28px;font-size:14px;font-weight: 500;">{{(':  ' +
                            reportData.produk_nama)}}</h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-28px;font-size:14px;">Tanggal
                            </h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="color:black;margin-left:5px;margin-top:-28px;font-size:14px;font-weight: 500;">{{':  ' +
                            reportData.tanggal}}</h3>
                        </div>
                    </div>
                </div>
                <div class="uk-margin-auto uk-margin-auto-vertical" uk-grid>
                    <div>
                        <div>
                            <table align="center" class="css-serial" style="font-size:14px;font-weight:500;border:0.1px solid #000000;font-family: arial,sans-serif;border-collapse:collapse;width:100%;">
                                <tr>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 30px">No.</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 100px">Tanggal</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 200px">Keterangan</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Awal</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Adjust</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Keluar</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Masuk</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">Sisa</th>
                                </tr>
                                <tr v-for="dt in reportData.detail">
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 30px"></td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 100px">{{dt['tgl_transaksi']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 8px;width: 200px">{{dt['jns_transaksi']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['jml_awal']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['jml_penyesuaian']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['jml_keluar']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['jml_masuk']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 8px;width: 50px">{{dt['jml_akhir']}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- <div class="uk-grid-small" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right">
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-10px;font-size:14px;font-weight: 500;">Catatan:
                            </h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:28px;margin-top:-20px;font-size:14px;font-weight: 500;">{{reportData.catatan}}</h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:650px;margin-top:50px;font-size:14px;font-weight: 500;font-style:italic;">Dibuat Oleh</h3>
                            <h3 class="uk-text-bold" style="color:black;margin-left:650px;margin-top:75px;font-size:14px;font-weight: 500;">{{'( ' + reportData.created + ' )'}}</h3>
                        </div>
                    </div>
                </div> -->
            </template>
        </vue3-html2pdf>
    </div>
</template>

<script>
import Vue3Html2pdf from 'vue3-html2pdf';

export default {
    name: 'cetakan-kartu-stock',
    components: {
        Vue3Html2pdf
    },
    data() {
        return {
            reportData: {
                tanggal: '',
                produk_nama: '',
                detail: []
                // tempat_lahir: '',
                // tgl_lahir: '',
                // alamat: '',
                // no_str: '',
                // status: '',
                // tgl_akhir_sip: ''
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
            if(data == []){
                alert('Produk belum mempunyai history stock'); 
                return false;
            }
            const today = new Date();
            this.reportData.produk_nama = data[0].produk_nama;
            this.reportData.tanggal = this.getDate(today.getFullYear()+' '+(today.getMonth()+1)+' '+today.getDate())+' '+today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
            for(let i=0;i<=data.length-1;i++) {
                this.reportData.detail.push({
                    'tgl_transaksi': data[i].tgl_transaksi,
                    'jns_transaksi': data[i].jns_transaksi,
                    'jml_awal': data[i].jml_awal,
                    'jml_penyesuaian': data[i].jml_penyesuaian,
                    'jml_keluar': data[i].jml_keluar,
                    'jml_masuk': data[i].jml_masuk,
                    'jml_akhir': data[i].jml_akhir,
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