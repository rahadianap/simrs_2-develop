<template>
    <!-- Popup Modal EDIT data lokasi -->
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="editformlokasi" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="border-radius:10px;">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitlokasi" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" style="border-radius:10px;justify-content:center;text-align:center;" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 1em 0.1em;margin:0;">
                            <h5 class="uk-text-uppercase">EDIT DATA lokasi</h5>
                        </div>                                
                    </div>
                    <div style="margin:0 10px;border-top:2px solid #cc0202;">                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small hims-form-subpage-asset" style="margin:30px 0" uk-grid>
                            <div class="uk-width-1-1@s uk-grid-small" style="margin-left:1px;padding-left:1px;" uk-grid> 
                                                                
                                <!-- Id client -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Client*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="lokasis.client_id" type="text" placeholder="masukkan ID client" required>
                                    </div>
                                </div>

                                <!-- nama lokasi -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama lokasi*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="lokasis.lokasi_nama" type="text" placeholder="masukkan nama lokasi" required>
                                    </div>
                                </div>

                                <!-- nomor seri -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nomor Seri*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="lokasis.serial_no" type="text" placeholder="masukkan nomor seri" required>
                                    </div>
                                </div>

                                <!-- nama brand lokasi -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Brand*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="lokasis.brand_nama" type="text" placeholder="nama brand lokasi" required>
                                    </div>
                                </div>

                                <!-- tgl kadaluarsa lokasi -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Kadaluarsa lokasi*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="lokasis.tgl_kadaluarsa" type="date" placeholder="tgl kadaluarsa lokasi" required>
                                    </div>
                                </div>

                                <!-- status lokasis -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="lokasis.status" type="text" placeholder="status lokasi" required>
                                    </div>
                                </div>    

                                <!-- deskripsi lokasi -->
                                <div class="uk-width-1-1@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi lokasi</label>
                                    <div class="uk-form-controls">
                                        <textarea input class="uk-input uk-textarea" v-model="lokasis.deskripsi" type="text" placeholder="deskripsi lokasi"></textarea>
                                    </div>
                                </div>

                                <!-- is_aktif -->
                                <!-- <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="lokasis.is_aktif" style="margin-left:5px;"> Data lokasi aktif</label>
                                </div> -->
                            </div>
                        </div>

                    </div>
                    <div class="uk-grid-collapse uk-card uk-card-default uk-column-1-2@m hims-form-footer" style="border-radius: 10px;">
                        <div class="uk-width-auto uk-column-1-1@s" style="padding:1em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="lokasis.is_aktif" style="margin-left:5px;"> Data lokasi aktif</label>
                        </div>
                        <div class="uk-grid-match uk-align-center uk-align-right@m uk-column-1-2@s" style="margin-bottom:6px;margin-top:1px">
                            <div class="uk-width-auto" style="padding:0.5em;">
                                <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;border-radius:10px;padding:0 85px;">Tutup</button>                  
                            </div>
                            <div class="uk-width-auto" style="padding:0.5em;">
                                <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;border-radius:10px;padding:0 85px;">Simpan</button>                  
                            </div>
                        </div>
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'edit-form-lokasi', 
    // mixins: [Vue2Filters.mixin],

    data() {
        return {
            isUpdate : true,
            lokasis : {
                lokasi_nama:null,
                serial_no:null,
                brand_nama : null,
                deskripsi : null,
                tgl_kadaluarsa:null,
                status : null,
                is_aktif : true,
                client_id : null,
                updated_by : null,
            },
            unitData : {
                lokasi_nama:null,
                serial_no:null,
                brand_nama : null,
                deskripsi : null,
                tgl_kadaluarsa:null,
                status : null,
                is_aktif : true,
                client_id : null,
                updated_by : null,
            },

            // status_lokasi : [
            //     { id:'Terverifikasi', text:'Terverifikasi' },
            //     { id:'Tidak Terverifikasi', text:'Tidak Terverifikasi' },
            //     { id:'Tidak Tahu', text:'Tidak Tahu' },
            // ]
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
        // this.createlokasi();
        // this['lokasis/form-lokasi']();
        // try {
        // }
    },

    methods : {
        ...mapActions('lokasis',['createlokasi','updatelokasi','datalokasi']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#editformlokasi').hide();
            return false;
        },

        submitlokasi(){
            this.CLR_ERRORS();
            console.log(this.isUpdate)
            if(!this.isUpdate) {
                this.createlokasi(this.lokasis).then((response) => {
                    if (response.success == true) {
                        alert("Data lokasi baru berhasil dibuat.");
                        // this.filllokasi(response.data);
                        this.clearlokasi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        // UIkit.modal('#editFormlokasi').hide();
                    }
                })
            } 
            
            else {
                console.log("Masuk ke update");
                this.updatelokasi(this.lokasis).then((response) => {
                    if (response.success == true) {
                        // this.filllokasi(response.data);
                        alert("Data lokasi berhasil diubah.");
                        // this.$emit('saveSucceed',true);
                        // this.isUpdate = true;
                        this.clearlokasi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }            
        },

        newlokasi(){
            this.clearlokasi();
            this.isUpdate = false;
            UIkit.modal('#editformlokasi').show();
        },

        editlokasi(lokasi_id){
                this.clearlokasi();            
                this.datalokasi(lokasi_id).then((response)=>{
                    if (response.success == true) {
                        this.filllokasi(response.data);
                        this.isUpdate = true;
                        UIkit.modal('#editformlokasi').show();
                    } else {
                        alert(response.message)
                    }
                })
            
        },   

        clearlokasi(){
            this.lokasis.lokasi_id = null;
            this.lokasis.lokasi_nama = null;
            this.lokasis.serial_no = null;
            this.lokasis.brand_nama = null;
            this.lokasis.deskripsi = null;
            this.lokasis.tgl_kadaluarsa = null;
            this.lokasis.status = null;
            this.lokasis.is_aktif = null;
            this.lokasis.client_id = null;
        },

        filllokasi(data){
            this.lokasis.lokasi_id = data.lokasi_id;
            this.lokasis.lokasi_nama = data.lokasi_nama;
            this.lokasis.serial_no = data.serial_no;
            this.lokasis.brand_nama = data.brand_nama;
            this.lokasis.deskripsi = data.deskripsi;
            this.lokasis.tgl_kadaluarsa = data.tgl_kadaluarsa;
            this.lokasis.status = data.status;
            this.lokasis.is_aktif = data.is_aktif;
            this.lokasis.client_id = data.client_id;
        },
    }
}
</script>


<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
}

.hims-form-container label {
    color:#333;
    font-size:12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border:2px solid #cc0202;
}

.uk-button {
    border:2px solid #aaa;
    font-weight:400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    border-radius:5px;
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
}

.hims-form-header {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    top:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-header h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}


.hims-form-subpage-asset {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
} */

</style>