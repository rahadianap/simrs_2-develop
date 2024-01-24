<template>
    <div ref="modalFormRegistrasi"
        class="uk-offcanvas-overlay uk-open"
        :style="formStyles"
    >
        <div  
            :id="modalId"
            class="uk-modal1 uk-width-1-3@m" 
            :style="modalStyles"
            uk-modal1="bg-close:false;" 
        >
            <div class="uk-modal-dialog1">
                <div style="border-bottom: 1px solid rgb(212, 212, 212); width: auto;">
                    <h2 class="uk-modal-title1" style="padding:0;margin:0;font-weight:700; color:black;">Pendaftaran Antrian</h2>
                </div>
                <div style="padding:0em 0em 0em 1em">
                    <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:1em 1em 1em 0.5em; margin-top:1em;border:1px solid #ccc; background-color: #f1f1f1; border-radius:7px;">                                
                        <div class="uk-width-1-1">
                            <h4 style="font-weight: 500;padding:0;margin:0;">{{regData.nama_pasien}}</h4>
                        </div>         
                        <div class="uk-width-1-1"></div>
                        <div class="uk-width-1-2@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>No. RM </dt>
                                <dd>{{regData.no_rm}}</dd>
                            </dl>
                        </div> 
                        <div class="uk-width-1-2@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>ID Pasien </dt>
                                <dd>{{regData.pasien_id}}</dd>
                            </dl>
                        </div> 

                        <div class="uk-width-1-2@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Tempat | Tgl Lahir </dt>
                                <dd>{{regData.tempat_lahir}} , {{regData.tgl_lahir}}</dd>
                            </dl>
                        </div>
                        <div class="uk-width-1-2@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Jenis Kelamin</dt>
                                <dd v-if="regData.jns_kelamin == 'L'">Laki-laki</dd>
                                <dd v-else-if="regData.jns_kelamin == 'P'">Perempuan</dd>
                                <dd v-else>(Tidak tahu)</dd>                                
                            </dl>
                        </div>                            
                    </div>
                </div>
                <div style="padding:0em 0em 0em 1em">
                    <form action="" @submit.prevent="daftarAntrian">
                        <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:1em 1em 1em 0.5em; margin-top:1em;border-radius:7px; border:1px solid #ccc;">   
                            <div class="uk-width-1-1@m">
                                <h5>Form Registrasi</h5>                         
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Periksa</label>
                                <div class="uk-form-controls">
                                    <input type="datetime-local" class="uk-input" style="color:black;border-radius:7px;" v-model="regData.tgl_periksa" required :disabled="isUpdate">
                                </div>
                            </div>

                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Penjamin</label>
                                <div class="uk-form-controls">
                                    <!-- <input type="text" class="uk-input" style="color:black;border-radius:7px;" v-model="regData.jns_penjamin"> -->
                                    <select class="uk-select" style="color:black;border-radius:7px;" v-model="regData.jns_penjamin" required>                
                                        <option v-if="jnsPenjaminLists" v-for="itm in jnsPenjaminLists" :key="itm in jnsPenjaminLists" :value="itm.jenis">{{ itm.text }}</option>
                                    </select>            
                                </div>
                            </div>
                            
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter</label>
                                <div class="uk-form-controls">
                                    <!-- <input type="text" class="uk-input" style="color:black;border-radius:7px;" v-model="regData.dokter_id"> -->
                                    <select class="uk-select" style="color:black;border-radius:7px;" v-model="regData.dokter_id" required @change="dokterChange" :disabled="isUpdate">                
                                        <option v-if="doctorLists" v-for="itm in doctorLists.data" :key="itm in doctorLists.data" :value="itm.dokter_id">{{ itm.dokter_nama }}</option>
                                    </select> 
                                    <p style="padding:0;margin:0;text-align:center; "><a href="#" @click.prevent="addEditDokter" style="font-size:14px;font-weight:medium;font-style:italic;">tambah dokter</a></p>
                                </div>
                            </div>                            
                            
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                <div class="uk-form-controls">
                                    <textarea type="text" rows="5" class="uk-textarea" style="color:black;border-radius:7px;" v-model="regData.catatan"></textarea>
                                </div>
                            </div>
                        </div>        
                        <div style="padding:1em; text-align: right;">
                            <button class="uk-button uk-button-default uk-modal-close" type="button" @click.prevent="closeModalReg" style="margin-right:10px; border-radius: 7px; font-size: 14px; text-transform: none;">Tutup</button>
                            <button class="uk-button uk-button-primary" type="submit" style="border-radius: 7px; font-size: 14px; text-transform: none;">Simpan Data</button>                                        
                        </div>                    
                    </form>
                </div>
            </div>
            <modal-dokter ref="modalMasterPraktek"></modal-dokter>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import ModalDokter from '@/Pages/PraktekDokter/LayananDokter/tabs/MasterPasien/components/ModalDokter.vue';

export default {
    name : 'modal-registrasi',
    components:{
        ModalDokter,
    },

    data() {
        return {
            modalId : 'modalRegistrasi',
            pasien : null,
            isUpdate : true,
            regData : {
                reg_id : null,
                tgl_registrasi : null,
                tgl_periksa : null,
                pasien_id : null,
                nama_pasien : null,
                no_rm : null,
                jns_kelamin : null,
                tempat_lahir : null,
                tgl_lahir : null,
                alamat : null,
                is_pasien_baru : null,                
                dokter_id : null,
                dokter_nama : null,                
                jns_penjamin : null,
                penjamin_id : null,
                penjamin_nama : null,                
                catatan : null,
                is_periksa : false,
                is_aktif : true,
            },

            jnsPenjaminLists : [
                { jenis : 'PRIBADI', text: 'PRIBADI' },
                { jenis : 'BPJS', text: 'BPJS' },
                { jenis : 'ASURANSI', text: 'ASURANSI' },                
            ],
            dokterList : [],
            isLoading:true,
            isModalOpen: false,
            // modalId: "uniqueModalId",
            formStyles: {
                visibility: 'visible',
                // transition: 'opacity 600ms, visibility 600ms',
                opacity: '1',
                transition: '', 

            },
            modalStyles: {
                // transform: this.isModalOpen ? 'translateX(100%)' : 'translateX(0)',
                height: '100vh',
                top: '0px',
                right: '-550px',
                zIndex: '999',
                position: 'fixed',
                backgroundColor: 'white',
                paddingTop: '1em',
                paddingLeft: '2em',
                animation: 'slide 0.5s forwards', 
                animationDelay: '0.3s',
                display: 'block',
            },
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['doctorLists']),
        ...mapGetters(['errors']),
        ...mapGetters('referensi',[
            'agamaRefs',
            'pekerjaanRefs',
            'pendidikanRefs',
            'jenisPenjaminRefs',
            'hubKeluargaRefs',
            'statusKawinRefs',
            'sukuBangsaRefs',
            'kebangsaanRefs',
            'caraPulangRefs',
            'statusPulangRefs',
            'asalPasienRefs',
            'jenisAlergiRefs',
            'statusBedInapRefs',
            'kasusIgdRefs',
            'jenisPelayananRefs',
            'caraRegistrasiRefs',
        ]),
    },

    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('praktekDokter',['createAntrian','updateAntrian','dataAntrian','listDokter','incrementTotalPasienBaru']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        openModal() {
            this.isModalOpen = true;
        },

        closeModalReg() {
            // this.isModalOpen = false;
            this.$emit('modalClosed',true);
            this.isLoading = true;

            this.formStyles.visibility = 'hidden';
            this.formStyles.opacity = '0';
            this.formStyles.transition = 'opacity 0.3s, visibility 0.5s';
            this.modalStyles.right = '0px';
            this.modalStyles.animation = 'slideOut 0.5s forwards';
            this.modalStyles.animationDelay = '0.1s';
        },

        initialize(){
            this.$refs.modalFormRegistrasi.style.display = this.isModalOpen ? 'block' : 'none';
            this.isLoading = true;            

            // this.openModal();
            
            if(!this.isRefExist) { 
                this.listReferensi(); 
            };
        },

        showModalReg() {
            //UIkit.modal(`#${this.modalId}`).show();
            this.$refs.modalFormRegistrasi.style.display = "block";  
            // this.$refs.modalFormRegistrasi.style.display = this.isModalOpen ? 'block' : 'none';          
        },

        newRegistrasi(pasien) {
            this.retrieveDokter();
            this.clrData();
            this.fillPasien(pasien);
            // UIkit.modal(`#${this.modalId}`).show();
            this.$refs.modalFormRegistrasi.style.display = "block";
            // this.openModal();
            this.isUpdate = false;
        },

        editRegistrasi(data) {
            this.retrieveDokter();
            this.clrData();
            this.openModal();
            if(data) {
                this.retrieveDataAntrian(data.reg_id);
            }
        },

        retrieveDataAntrian(data) {
            this.dataAntrian(data).then((response) => {
                if (response.success == true) {
                    this.fillData(response.data);
                    this.isUpdate = true;
                    this.showModalReg();
                }
                else {
                    alert(response.message);
                }
            })
        },

        fillPasien(data) {
            this.regData.pasien_id = data.pasien_id;
            this.regData.nama_pasien = data.nama_pasien;
            this.regData.no_rm = data.no_rm;
            this.regData.jns_kelamin = data.jns_kelamin;
            this.regData.tempat_lahir = data.tempat_lahir;
            this.regData.tgl_lahir = data.tgl_lahir;
            this.regData.alamat = null;
            this.regData.is_pasien_baru = data.is_pasien_baru;           
            this.regData.tgl_periksa = this.getTodayDate();     
        },

        /**
         * get date today for default value input date 
         */
         getTodayDate(){
            let dt = new Date();
            // //set year
            // let year = dt.getFullYear();
            // //set month
            // let month = dt.getMonth() + 1;
            // if(month < 10) {month = `0${month}`}
            // //set date now
            // let date = dt.getDate();
            // if(date < 10) {date = `0${date}`}
            // let str_dt = `${year}-${month}-${date}`;
            // return str_dt;
        
            dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
            return dt.toISOString().slice(0, 16);            
        },

//         function datetimeLocal(datetime) {
//     const dt = new Date(datetime);
//     dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
//     return dt.toISOString().slice(0, 16);
// }

        // closeModalReg(){
        //     //UIkit.modal(`#${this.modalId}`).hide();

        //     this.$refs.modalFormRegistrasi.style.display = "none";
            
        // },

        dokterChange(){
            this.regData.dokter_nama = null;
            let dok = this.doctorLists.data.find(item => item.dokter_id === this.regData.dokter_id);
            if(dok) {
                this.regData.dokter_nama = dok.dokter_nama;
            }
        },

        daftarAntrian() {
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.isLoading = true;
                this.createAntrian(this.regData).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        alert("Data antrian baru berhasil dibuat.");
                        this.fillData(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeModalReg();
                    }
                })
            }
            else {
                this.isLoading = true;
                this.updateAntrian(this.regData).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        this.fillData(response.data);
                        alert("Data antrian berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.isLoading = true;
                        this.closeModalReg();
                    }
                })
            }            
        },

        fillData(data) {
            this.regData.reg_id = data.reg_id;
            this.regData.tgl_registrasi = data.tgl_registrasi;
            this.regData.tgl_periksa = data.tgl_periksa;
            this.regData.pasien_id = data.pasien_id;
            this.regData.nama_pasien = data.nama_pasien;
            this.regData.no_rm = data.no_rm;
            this.regData.jns_kelamin = data.jns_kelamin;
            this.regData.tempat_lahir = data.tempat_lahir;
            this.regData.tgl_lahir = data.tgl_lahir;
            this.regData.alamat = data.alamat;
            this.regData.is_pasien_baru = data.is_pasien_baru;
            this.regData.dokter_id = data.dokter_id;
            this.regData.dokter_nama = data.dokter_nama;        
            this.regData.jns_penjamin = data.jns_penjamin;
            this.regData.penjamin_id = data.penjamin_id;
            this.regData.penjamin_nama = data.penjamin_nama;      
            this.regData.catatan = data.catatan;
            this.regData.is_periksa = data.is_periksa;
            this.regData.is_aktif = data.is_aktif;
        },

        // datetimeLocal(data) {
        //     console.log(data);
        //     let dt = new Date(data);
        //     let year = dt.getFullYear();
        //     let month = dt.getMonth() + 1;
        //     if(month < 10) {month = `0${month}`}
        //     let date = dt.getDate();
        //     if(date < 10) {date = `0${date}`}

        //     let hour = dt.getHours();
        //     if(hour < 10) {hour = `0${hour}`}

        //     let minute = dt.getMinutes();
        //     if(minute < 10) {minute = `0${minute}`}

        //     let str_dt = `${year}/${month}/${date} ${hour}:${minute}:00`;
        //     return str_dt;
        // },

        clrData() {
            this.regData.reg_id = null;
            this.regData.tgl_registrasi = null;
            this.regData.tgl_periksa = null;
            this.regData.pasien_id = null;
            this.regData.nama_pasien = null;
            this.regData.no_rm = null;
            this.regData.jns_kelamin = null;
            this.regData.tempat_lahir = null;
            this.regData.tgl_lahir = null;
            this.regData.alamat = null;
            this.regData.is_pasien_baru = false;                
            this.regData.dokter_id = null;
            this.regData.dokter_nama = null;        
            this.regData.jns_penjamin = null;
            this.regData.penjamin_id = null;
            this.regData.penjamin_nama = null;      
            this.regData.catatan = null;
            this.regData.is_periksa = false;
            this.regData.is_aktif = true;
        },

        addEditDokter(){
            this.$refs.modalMasterPraktek.showModal();
        },

        retrieveDokter(){
            // alert('retrieve dokter');
            let params = {
                is_aktif : true,
                per_page : 'ALL',
            };
            this.listDokter(params).then((response) => {
                if (response.success == true) {
                    // console.log(this.doctorLists.data);
                    this.dokterList = this.doctorLists.data;
                }
                this.isLoading = false;
            })
        },
    }
}
</script>
<style>
.uk-offcanvas-overlay {
  position: fixed;
  z-index:998;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  -webkit-backdrop-filter: blur(5px);
  backdrop-filter: blur(5px);
}
.uk-offcanvas-overlay::before {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity .15s linear;
}


/* #slide {
    position: absolute;
    right: -530px;
    height: 100px;
    background: white;
    -webkit-animation: slide 0.5s forwards;
    -webkit-animation-delay: 2s;
    animation: slide 0.5s forwards;
    animation-delay: 2s;
} */

/* @-webkit-keyframes slide {
    from {
        transform: translateX(100%);
    }
    to {
        transform: translateX(0);
    }
} */

/* @keyframes slide {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  } 
}*/
@-webkit-keyframes slide {
    100% { right: 0; }
}
@-webkit-keyframes slideOut {
    100% {
        right: -550px;
    }
}
@keyframes slide {
    100% { right: 0; }
}
@keyframes slideOut {
    100% {
        right: -550px;
    }
}
</style>