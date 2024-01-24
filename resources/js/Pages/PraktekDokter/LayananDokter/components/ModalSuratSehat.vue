<template>
    <div class="uk-modal" uk-modal="bg-close:false;" :id="modalId">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container hims-form-container1" style="background-color:#fff;padding:0.5em;">
                <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;text-align:center;">
                    <h4 class="uk-text-uppercase">SURAT SEHAT PASIEN</h4>
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
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien : </label>
                                <p style="margin-top: 0px;font-size: 16px;font-weight: bold;">{{ this.srtPasien.nama_pasien }}</p>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. RM : </label>
                                <p style="margin-top: 0px;font-size: 16px;font-weight: bold;">{{ this.srtPasien.no_rm }}</p>
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
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tekanan Darah : </label>
                                <input type="text" class="uk-input" style="color:black;border-radius:7px;text-align:right;" v-model="srtPasien.tekanan_darah">
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Temperatur : </label>
                                <input type="number" class="uk-input" style="color:black;border-radius:7px;text-align:right;" v-model="srtPasien.suhu">
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Denyut Jantung : </label>
                                <div class="uk-form-controls">
                                    <input type="number" class="uk-input" style="color:black;border-radius:7px;text-align:right;" v-model="srtPasien.denyut_nadi">
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Laju Pernapasan : </label>
                                <input type="number" class="uk-input" style="color:black;border-radius:7px;text-align:right;" v-model="srtPasien.pernapasan">
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Berat Badan : </label>
                                <input type="number" class="uk-input" style="color:black;border-radius:7px;text-align:right;" v-model="srtPasien.berat_badan">
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tinggi Badan : </label>
                                <input type="number" class="uk-input" style="color:black;border-radius:7px;text-align:right;" v-model="srtPasien.tinggi_badan">
                            </div>
                            
                        </div>
                        
                        <div class="uk-width-1-1"  style="text-align:right; margin-top:2em;padding:1em;">
                            <button type="button" class="uk-button uk-modal-close" @click.prevent="closeModal" style="border:1px solid #999; border-radius:7px; height:40px;margin-right:5px;">Tutup</button>
                            <button type="submit" class="uk-button uk-button-primary" style="border-radius:7px; height:40px;margin-right:3px;">Simpan</button>
                            <button type="button" class="uk-button uk-button-primary" style="border-radius:7px; height:40px;" @click.prevent="printSuratSehat()">Print</button>
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
    name : 'modal-surat-sehat',
    components : {

    },

    data() {
        return {
            modalId : 'formSuratSehat',
            isLoading : false,
            isUpdate : true,
            dokterList : [],
            srtPasien : {
                nama_pasien : null,
                no_rm : null,
                reg_id : null,
                sistole : 0,
                diastole : 0,
                denyut_nadi : 0,
                suhu : 0,
                pernapasan : 0,
                berat_badan : 0,
                tinggi_badan : 0,
                dokter_id : null,
                dokter_nama : null,
                tekanan_darah : null,
            },
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['doctorLists']),
    },

    methods : {
        ...mapActions('praktekDokter',['dataSuratPasien','createSuratPasien','updateSuratPasien','cetakSuratPasien','dataMedrec','listDokter']),
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
            this.retrieveData(data.reg_id);
            this.retrieveSuratPasien(data.reg_id);
            this.retrieveDokter();
            UIkit.modal(`#${this.modalId}`).show();
        },

        submitSuratPasien(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.isLoading = true;
                this.srtPasien.jnsSurat = "SuratSehat";
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
                this.srtPasien.jnsSurat = "SuratSehat";
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
                jns_surat : 'SuratSehat',
                reg_id : this.srtPasien.reg_id,
            };
            this.dataSuratPasien(params).then((response) => {
                this.isLoading = false;
                if (response.success == true) {
                    if(response.data != null){
                        this.isUpdate = true;
                        this.fillSrtPasien(response.data);
                    }
                }
                else {
                    alert(response.message);
                }
            })
        },
        
        retrieveData() {
            this.dataMedrec(this.srtPasien.reg_id).then((response) => {
                this.isLoading = false;
                if (response.success == true) {
                    this.fillSrtPasien(response.data.soap);
                }
                else {
                    alert(response.message);
                }
            })
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
        
        clrSuratPasien(){
            this.srtPasien.sistole = 0;
            this.srtPasien.diastole = 0;
            this.srtPasien.denyut_nadi = 0;
            this.srtPasien.suhu = 0;
            this.srtPasien.pernapasan = 0;
            this.srtPasien.berat_badan = 0;
            this.srtPasien.tinggi_badan = 0;
            this.srtPasien.dokter_id = null;
            this.srtPasien.dokter_nama = null;
            this.srtPasien.tekanan_darah = null;
        },

        fillSrtPasien(data) {
            if(data){
                this.srtPasien.tekanan_darah = data.sistole + "/" + data.diastole;
                this.srtPasien.sistole = data.sistole;
                this.srtPasien.diastole = data.diastole;
                this.srtPasien.denyut_nadi = data.denyut_nadi;
                this.srtPasien.suhu = data.suhu;
                this.srtPasien.pernapasan = data.pernapasan;
                this.srtPasien.berat_badan = data.berat_badan;
                this.srtPasien.tinggi_badan = data.tinggi_badan;
                this.srtPasien.dokter_id = data.dokter_id;
                this.srtPasien.dokter_nama = data.dokter_nama;
            }
        },

        dokterChange(){
            this.srtPasien.dokter_nama = null;
            let dok = this.doctorLists.data.find(item => item.dokter_id === this.srtPasien.dokter_id);
            if(dok) {
                this.srtPasien.dokter_nama = dok.dokter_nama;
            }
        },

        printSuratSehat(){
            console.log("print surat sehat");
            this.params = {
                    reg_id: this.srtPasien.reg_id,
                    jns_surat: "SuratSehat",
            };
            this.cetakSuratPasien(this.params).then((response) => {
                var newNode = document.createElement('p');
                newNode.appendChild(document.createTextNode('html string'));
                this.printDiv(response,"a4");
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
        

    }
}
</script>