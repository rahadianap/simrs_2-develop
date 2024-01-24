<template>
    <div>
    
        <vue3-html2pdf :show-layout="false" :float-layout="true" :enable-download="false" :preview-modal="true"
            :paginate-elements-by-height="1400" filename="hee hee" :pdf-quality="2" :manual-pagination="false"
            pdf-orientation="portrait" pdf-content-width="auto" :html-to-pdf-options="htmlToPdfOptions"

            @hasStartedGeneration="hasStartedGeneration()" @hasGenerated="hasGenerated($event)" ref="html2Pdf"
            >

            <template v-slot:pdf-content>
                <!-- header -->
                <div class="uk-width-1-1 uk-margin-small-top">
                    <h3 class="uk-text-bolder uk-text-right uk-text-large" style="font-size:24px;">{{ reportData.no_antrian }}</h3>
                </div>
                <div class="uk-width-1-1">
                    <h3 class="uk-text-bolder uk-text-center uk-text-large" style="font-size:20px;margin-top:-15px;"><u>TRACER</u></h3>
                </div>
                <div class="uk-child-width-expand@s uk-text-center" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top">
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;">Tgl. Cetak</h3>
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;">No. Reg</h3>
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;">No. RM</h3>
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;">Pasien</h3>
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;">Penjamin</h3>
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;">Tgl. Registrasi</h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top">
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;margin-left: -100px;">{{ ': ' + reportData.tgl_cetak }}</h3>
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;margin-left: -100px;">{{ ': ' + reportData.reg_id }}</h3>
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;margin-left: -100px;">{{ ': ' + reportData.no_rm }}</h3>
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;margin-left: -100px;">{{ ': ' + reportData.nama_pasien }}</h3>
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;margin-left: -100px;">{{ ': ' + reportData.penjamin }}</h3>
                            <h3 class="uk-text-normal uk-text-left uk-text-default" style="font-size:14px;margin-top:-15px;margin-left: -100px;">{{ ': ' + reportData.tgl_registrasi }}</h3>
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-1 uk-margin-small-top">
                    <table align="left" class="css-serial uk-table uk-table-small uk-table-divider" style="font-size:10px;margin-top:-30px;border:0.1px solid #000000;border-collapse:collapse;">
                        <tr>
                            <th style="color:black;border: 0.1px solid #000000;font-size:10px;border-collapse:collapse;">No.</th>
                            <th style="color:black;border: 0.1px solid #000000;font-size:10px;border-collapse:collapse;">Tujuan / Dokter</th>
                        </tr>
                        <tr v-for="dt in reportData.detail">
                            <td></td>
                            <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;">{{dt['unit'] + ' / ' + dt['dokter']}}</td>
                        </tr>
                    </table>
                </div>
            </template>
        </vue3-html2pdf>
    </div>
</template>

<script>
import Vue3Html2pdf from 'vue3-html2pdf';

export default {
    name: 'cetakan-tracer',
    components: {
        Vue3Html2pdf
    },
    data() {
        return {
            reportData: {
                nama_pasien : '',
                reg_id : '',
                no_rm: '',
                unit: '',
                dokter: '',
                no_antrian: '',
                reg_id: '',
                penjamin: '',
                tgl_cetak: '',
                tgl_registrasi: '',
                detail: []
            },
        }
    },
    computed: {
        htmlToPdfOptions() {
            return {
                margin: 5,
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
                    format: [100,100],
                    orientation: 'portrait',
                },
            };
        },
    },
    methods: {
        getDate() {
            let res = new Date();
            let year = res.getFullYear();
            let month = res.getMonth();
            if (month < 10) { month = `0${month}` }
            let date = res.getDate();
            if (date < 10) { date = `0${date}` }
            let h = res.getHours();
            if (h < 10) { h = `0${h}` }
            let m = res.getMinutes();
            if (m < 10) { m = `0${m}` }
            let time = h + ":" + m;
            return `${date}-${month}-${year}  ${time}`;
        },

        generateReport(data) {
            this.reportData.nama_pasien = data[0].nama_pasien;
            this.reportData.reg_id = data[0].reg_id;
            this.reportData.no_rm = data[0].no_rm;
            this.reportData.no_antrian = data[0].no_antrian;
            this.reportData.reg_id = data[0].reg_id;
            this.reportData.penjamin = data[0].penjamin_nama;
            this.reportData.tgl_cetak = this.getDate();
            this.reportData.tgl_registrasi = data[0].tgl_registrasi;
            for(let i=0;i<=data.length-1;i++) {
                this.reportData.detail.push({
                    'unit': data[i].unit_nama,
                    'dokter': data[i].dokter_nama,
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