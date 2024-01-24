<template>
    <div class="uk-modal" uk-modal="bg-close:false;" :id="modalId">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container hims-form-container1" style="background-color:#fff;padding:0.5em;">
                <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;text-align:center;">
                    <h4 class="uk-text-uppercase">SURAT SAKIT PASIEN</h4>
                </div> 
                <form action="" @submit.prevent="submitSuratPasien" >
                    <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                        <div uk-spinner="ratio : 2"></div>
                        <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                    </div>
                    
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header1" uk-grid style="margin:0;padding:0.5em;">                        
                        <div class="uk-width-1-1 uk-grid-small" uk-grid>
                            <div class="uk-width-1-1@m">
                                <h5 style="font-weight:500;">Mohon melengkapi data berikut ini : </h5>
                            </div>
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select" style="color:black;border-radius:7px;" v-model="srtPasien.dokter_id" required @change="dokterChange">                
                                        <option v-if="doctorLists" v-for="itm in doctorLists.data" :key="itm in doctorLists.data" :value="itm.dokter_id">{{ itm.dokter_nama }}</option>
                                    </select> 
                                </div>
                            </div> 
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien : </label>
                                <p style="margin-top: 0px;font-size: 16px;font-weight: bold;">{{ this.srtPasien.nama_pasien }}</p>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. RM : </label>
                                <p style="margin-top: 0px;font-size: 16px;font-weight: bold;">{{ this.srtPasien.no_rm }}</p>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Perlu beristirahat : </label>
                                <select class="uk-select" v-model="srtPasien.kateg_istirahat" style="border-radius:7px">
                                    <option value="diRumah">Di rumah</option>
                                    <option value="diRumahSakit">Di rumah sakit</option>
                                    <option value="cutiHamil">Cuti hamil</option>
                                </select>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Rumah Sakit : </label>
                                <input type="text" name="nmRumahSakit" v-model="srtPasien.nmRumahSakit" :disabled="srtPasien.kateg_istirahat !== 'diRumahSakit'" class="uk-input" style="color:black;border-radius:7px" placeholder="Nama Rumah Sakit">
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Berapa hari</label>
                                <div class="uk-form-controls">
                                    <input type="number" class="uk-input" style="color:black;border-radius:7px;text-align:right;" v-model="srtPasien.hari">
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;color:white;">Berapa hari</label>
                                <div class="uk-form-controls" style="margin-top:7px;">
                                    hari
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" for="transactionDate">Dari tanggal</label>
                                <div class="uk-form-controls">
                                    <input type="date" class="uk-input" v-model="srtPasien.tgl_awal" style="border-radius:7px" placeholder="Pilih Tanggal">
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" for="transactionDate">Sampai dengan</label>
                                <div class="uk-form-controls">
                                    <input type="date" class="uk-input" v-model="srtPasien.tgl_akhir" style="border-radius:7px" placeholder="Pilih Tanggal">
                                </div>
                            </div>
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" for="transactionDate">Catatan</label>
                                <div class="uk-form-controls">
                                    <textarea rows="4" class="uk-textarea" v-model="srtPasien.catatan" type="text" placeholder="Catatan"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="uk-width-1-1"  style="text-align:right; margin-top:2em;padding:1em;">
                            <button type="button" class="uk-button uk-modal-close" @click.prevent="closeModal" style="margin-right:5px;border:1px solid #999; border-radius:7px; height:40px;">Tutup</button>
                            <button type="submit" class="uk-button uk-button-primary" style="border-radius:7px; height:40px;">Simpan</button>
                            <button type="button" class="uk-button uk-button-primary" style="border-radius:7px; height:40px;" @click.prevent="printSuratSakit()">Print</button>
                        </div>
                    </div>  
                </form>  
            </div>  
        </div>
    </div>    
</template>
<script>

import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    name : 'modal-surat-sakit',
    components : {

    },

    data() {
        return {
            modalId : 'formSuratSakit',
            isLoading : false,
            isUpdate : true,
            srtPasien : {
                reg_id : null,
                nama_pasien : null,
                no_rm : null,
                tgl_awal : this.getTodayDate(),
                tgl_akhir : this.getTodayDate(),
                hari : 0,
                kateg_istirahat : null,
                nmRumahSakit : null,
                dokter_id : null,
                dokter_nama : null,
                catatan : null,
            },
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['doctorLists']),
    },

    methods : {
        ...mapActions('praktekDokter',['dataSuratPasien','createSuratPasien','updateSuratPasien','cetakSuratPasien','listDokter']),
        ...mapMutations(['CLR_ERRORS']),

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        initialize() {
            this.modalId = `formPembayaran-${this.getTodayDate()}`;
        },

        getTodayDate(){
            let dt = new Date();
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate();
            if(date < 10) {date = `0${date}`}
            let str_dt = `${year}-${month}-${date}`
            return str_dt;
        },

        closeModal(){
            UIkit.modal(`#${this.modalId}`).hide();
            this.clrSuratPasien();
        },

        createSurat(data) {
            this.CLR_ERRORS();
            this.isUpdate = false;
            this.srtPasien.reg_id = data.reg_id;
            this.srtPasien.nama_pasien = data.nama_pasien;
            this.srtPasien.no_rm = data.no_rm;
            this.retrieveSuratPasien(data.reg_id);
            UIkit.modal(`#${this.modalId}`).show();
        },

        submitSuratPasien(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.isLoading = true;
                this.srtPasien.jnsSurat = "SuratSakit";
                this.createSuratPasien(this.srtPasien).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        this.isUpdate = true;
                        alert(response.message);
                        // UIkit.modal(`#${this.modalId}`).hide();
                    }
                })  
            }
            else {
                this.isLoading = true;
                this.srtPasien.jnsSurat = "SuratSakit";
                this.updateSuratPasien(this.srtPasien).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        this.$emit('saveSucceed',response.data);
                        this.isUpdate = true;
                        alert(response.message);
                        // UIkit.modal(`#${this.modalId}`).hide();
                    }
                })
            }
        },

        retrieveSuratPasien(regId) {
            this.CLR_ERRORS();
            this.isLoading = true;
            let params = {
                jns_surat : 'SuratSakit',
                reg_id : this.srtPasien.reg_id,
            };
            this.dataSuratPasien(params).then((response) => {
                this.isLoading = false;
                if (response.success == true) {
                    if(response.data != null){
                        this.isUpdate = true;
                        console.log(response.data);
                        this.fillSrtPasien(response.data);
                    }
                }
                else {
                    alert(response.message);
                }
            })
        },
        
        clrSuratPasien(){
            this.srtPasien.tgl_awal = this.getTodayDate();
            this.srtPasien.tgl_akhir = this.getTodayDate();
            this.srtPasien.hari = 0;
            this.srtPasien.nama_pasien = null;
            this.srtPasien.no_rm = null;
            this.srtPasien.kateg_istirahat = null;
            this.srtPasien.nmRumahSakit = null;
            this.srtPasien.dokter_id = null;
            this.srtPasien.dokter_nama = null;
            this.srtPasien.catatan = null;
        },


        fillSrtPasien(data) {
            if(data){
                this.srtPasien.tgl_awal = data.tgl_awal;
                this.srtPasien.tgl_akhir = data.tgl_akhir;
                this.srtPasien.hari = data.hari;
                this.srtPasien.nama_pasien = data.nama_pasien;
                this.srtPasien.no_rm = data.no_rm;
                this.srtPasien.kateg_istirahat = data.kateg_istirahat;
                console.log(this.srtPasien.kateg_istirahat);
                console.log(data.kateg_istirahat);
                this.srtPasien.nmRumahSakit = data.nmRumahSakit;
                this.srtPasien.dokter_id = data.dokter_id;
                this.srtPasien.dokter_nama = data.dokter_nama;
                this.srtPasien.catatan = data.catatan;
            }
        },

        printSuratSakit(){
            console.log("print surat sakit");
            this.params = {
                    reg_id: this.srtPasien.reg_id,
                    jns_surat: "SuratSakit",
            };
            this.cetakSuratPasien(this.params).then((response) => {
                var newNode = document.createElement('p');
                newNode.appendChild(document.createTextNode('html string'));
                this.printDiv(response,"6.5in 8.2in");
            });
        },

        getDateFormated(curDate){
              const today = new Date(curDate);
              const yyyy = today.getFullYear();
              let mm = today.getMonth() + 1; // Months start at 0!
              let dd = today.getDate();

              if (dd < 10) dd = '0' + dd;
              if (mm < 10) mm = '0' + mm;

              const formattedToday = dd + '/' + mm + '/' + yyyy;
              return formattedToday;
        },

        printDiv(pdf_body, paperSize) {
            const customPaperSize = paperSize;
            let frame1 = document.createElement('iframe');
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            let frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();

            // Set custom paper size using CSS @page rule
            const customStyles = `
                <style>
                    @page {
                        size: ${customPaperSize};
                        margin: 0;
                    }
                </style>
            `;

            frameDoc.document.write(customStyles);

            // Use the entire content as one page
            const fullHtml = pdf_body;

            frameDoc.document.write(fullHtml);
            frameDoc.document.close();

            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 1000);

            return false;
        },

        retrieveDokter(){
            let params = {
                is_aktif : true,
                per_page : 'ALL',
            };
            this.listDokter(params).then((response) => {
                if (response.success == true) {
                    this.dokterList = this.doctorLists.data;
                }
                this.isLoading = false;
            })
        },

        dokterChange(){
            this.srtPasien.dokter_nama = null;
            let dok = this.doctorLists.data.find(item => item.dokter_id === this.srtPasien.dokter_id);
            if(dok) {
                this.srtPasien.dokter_nama = dok.dokter_nama;
            }
        },

    }
}
</script>