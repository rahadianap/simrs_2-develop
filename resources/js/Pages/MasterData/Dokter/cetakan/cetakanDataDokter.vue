<template>
    <div>
    
        <vue3-html2pdf :show-layout="false" :float-layout="true" :enable-download="false" :preview-modal="true"
            :paginate-elements-by-height="1400" filename="hee hee" :pdf-quality="2" :manual-pagination="false"
            pdf-format="a4" pdf-orientation="portrait" pdf-content-width="800px"

            @hasStartedGeneration="hasStartedGeneration()" @hasGenerated="hasGenerated($event)" ref="html2Pdf"
            >

            <template v-slot:pdf-content>
                <div class="uk-grid-small" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="margin-left:28px;margin-top:28px;font-size:18px;">DATA DOKTER</h3>
                        </div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <hr width="725px;" size="3" color="#000" style="margin-top:-10px;margin-left:28px;margin-right:28px;text-align:center;">
                        </div>
                    </div>
                </div>
                <div class="uk-grid-small" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="margin-left:28px;margin-top:-28px;font-size:14px;">Nama
                            </h3>
                            <h3 class="uk-text-bold" style="margin-left:28px;margin-top:-18px;font-size:14px;">Tempat
                                Tanggal Lahir</h3>
                            <h3 class="uk-text-bold" style="margin-left:28px;margin-top:-18px;font-size:14px;">Alamat
                            </h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 style="margin-left:5px;margin-top:-28px;font-size:14px;">{{':  ' +
                            reportData.nama_dokter}}</h3>
                            <h3 style="margin-left:5px;margin-top:-18px;font-size:14px;">{{':  ' +
                            reportData.tempat_lahir + ', ' + reportData.tgl_lahir}}</h3>
                            <h3 style="margin-left:5px;margin-top:-18px;font-size:14px;">{{':  ' +
                            reportData.alamat}}</h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 class="uk-text-bold" style="margin-left:28px;margin-top:-28px;font-size:14px;">No. STR
                            </h3>
                            <h3 class="uk-text-bold" style="margin-left:28px;margin-top:-18px;font-size:14px;"> Masa Berlaku
                            </h3>
                            <h3 class="uk-text-bold" style="margin-left:28px;margin-top:-18px;font-size:14px;">Status
                            </h3>
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top uk-margin-auto-right uk-text-left">
                            <h3 style="margin-left:5px;margin-top:-28px;font-size:14px;">{{':  ' +
                            reportData.no_str}}</h3>
                            <h3 style="margin-left:5px;margin-top:-18px;font-size:14px;">{{':  ' +
                            reportData.tgl_akhir_sip}}</h3>
                            <h3 style="margin-left:5px;margin-top:-18px;font-size:14px;">{{':  ' +
                            reportData.status}}</h3>
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
    name: 'cetakan-data-dokter',
    components: {
        Vue3Html2pdf
    },
    data() {
        return {
            reportData: {
                nama_dokter: '',
                tempat_lahir: '',
                tgl_lahir: '',
                alamat: '',
                no_str: '',
                status: '',
                tgl_akhir_sip: ''
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
            // console.log(data);
            this.reportData.nama_dokter = data.dokter_nama;
            this.reportData.tempat_lahir = data.tempat_lahir;
            this.reportData.tgl_lahir = this.getDate(data.tgl_lahir);
            this.reportData.alamat = data.alamat;
            this.reportData.no_str = data.no_sip;
            this.reportData.tgl_akhir_sip = this.getDate(data.tgl_akhir_sip);
            this.reportData.status = data.status_kerjasama;
            this.$refs.html2Pdf.generatePdf();
        }
    },
}
</script>