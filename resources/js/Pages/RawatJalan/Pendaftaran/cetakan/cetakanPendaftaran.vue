<template>
    <div>
    
        <vue3-html2pdf :show-layout="false" :float-layout="true" :enable-download="false" :preview-modal="true"
            :paginate-elements-by-height="1400" filename="hee hee" :pdf-quality="2" :manual-pagination="false"
            pdf-orientation="portrait" pdf-content-width="auto" :html-to-pdf-options="htmlToPdfOptions"

            @hasStartedGeneration="hasStartedGeneration()" @hasGenerated="hasGenerated($event)" ref="html2Pdf"
            >

            <template v-slot:pdf-content>
                <!-- header -->
                <div class="uk-grid-small uk-align-center" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top">
                            <h3 class="uk-text-bolder uk-text-center uk-text-large" style="font-size:24px;">{{ reportData.nama_rs }}</h3>
                        </div>
                        <div class="uk-width-1-1">
                            <h3 class="uk-text-normal uk-text-center uk-text-default" style="font-size:15px;margin-top:-15px;">{{reportData.alamat_rs}}</h3>
                        </div>
                        <div class="uk-width-1-1">
                            <h3 class="uk-text-bolder uk-text-center uk-text-large" style="font-size:20px;margin-top:-15px;">BUKTI PENDAFTARAN RAWAT JALAN</h3>
                        </div>
                        <div class="uk-width-1-1 uk-grid-small uk-visible" uk-grid style="border-top:2px solid black;margin-top:-15px;"></div>
                        <div class="uk-width-1-1">
                            <h3 class="uk-text-normal uk-text-center uk-text-default" style="font-size:15px;margin-top:3px;">{{reportData.unit}}</h3>
                        </div>
                        <div class="uk-width-1-1">
                            <h3 class="uk-text-normal uk-text-center uk-text-default" style="font-size:15px;margin-top:-10px;">{{reportData.dokter}}</h3>
                        </div>
                        <div class="uk-width-1-1">
                            <h1 class="uk-text-bolder uk-text-center uk-text-large" style="font-size:40px;margin-top:-20px;">{{reportData.no_antrian}}</h1>
                        </div>
                        <div class="uk-width-1-1">
                            <h3 class="uk-text-normal uk-text-center uk-text-default" style="font-size:15px;margin-top:-20px;">{{reportData.reg_id}}</h3>
                        </div>
                        <div class="uk-width-1-1">
                            <h3 class="uk-text-normal uk-text-center uk-text-default" style="font-size:15px;margin-top:-20px;">{{reportData.no_rm}}</h3>
                        </div>
                        <div class="uk-width-1-1">
                            <h3 class="uk-text-normal uk-text-center uk-text-default" style="font-size:15px;margin-top:-20px;">{{reportData.nama_pasien}}</h3>
                        </div>
                        <div class="uk-width-1-1">
                            <h3 class="uk-text-normal uk-text-center uk-text-default" style="font-size:15px;margin-top:-20px;">{{reportData.penjamin}}</h3>
                        </div>
                        <div class="uk-width-1-1">
                            <h3 class="uk-text-normal uk-text-center uk-text-default" style="font-size:15px;margin-top:-15px;">{{reportData.tgl_cetak}}</h3>
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
    name: 'cetakan-pendaftaran',
    components: {
        Vue3Html2pdf
    },
    data() {
        return {
            reportData: {
                nama_pasien : '',
                reg_id : '',
                alamat_rs: '',
                nama_rs: '',
                no_rm: '',
                unit: '',
                dokter: '',
                no_antrian: '',
                reg_id: '',
                penjamin: '',
                tgl_cetak: ''
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
                    format: [125,100],
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
            this.reportData.nama_pasien = data[1].nama_pasien;
            this.reportData.reg_id = data[1].reg_id;
            this.reportData.nama_rs = data[0].client_nama; 
            this.reportData.alamat_rs = data[0].alamat;
            this.reportData.no_rm = data[1].no_rm;
            this.reportData.unit = data[1].unit_nama;
            this.reportData.dokter = data[1].dokter_nama;
            this.reportData.no_antrian = data[1].no_antrian;
            this.reportData.reg_id = data[1].reg_id;
            this.reportData.penjamin = data[1].penjamin_nama;
            this.reportData.tgl_cetak = this.getDate();
            this.$refs.html2Pdf.generatePdf();
        }
    },
}
</script>