<template>
    <!-- Popup Modal tambah sparepart -->
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formSparepart" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="border-radius:10px;">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitSparepart" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" style="border-radius:10px;justify-content:center;text-align:center;" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 1em 0.1em;margin:0;">
                            <h5 class="uk-text-uppercase">TAMBAH DATA Sparepart</h5>
                        </div>                                
                    </div>
                    <div style="margin:0 10px;border-top:2px solid #cc0202;">                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small hims-form-subpage-asset" style="margin:30px 0" uk-grid >
                            <div class="uk-width-1-1@s uk-grid-small" style="margin-left:1px;padding-left:1px;" uk-grid> 

                                <!-- nama sparepart -->
                                <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Sparepart*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="spareparts.sparepart_nama" type="text" placeholder="masukkan nama sparepart" required>
                                    </div>
                                </div>

                                <!-- nomor seri -->
                                <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nomor Seri*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="spareparts.serial_no" type="text" placeholder="masukkan nomor seri" required>
                                    </div>
                                </div>

                                <!-- nama brand sparepart -->
                                <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Brand*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="spareparts.brand_nama" type="text" placeholder="nama brand sparepart" required>
                                    </div>
                                </div>

                                <!-- tgl kadaluarsa sparepart -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Kadaluarsa Sparepart*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="spareparts.tgl_kadaluarsa" type="date" placeholder="tgl kadaluarsa sparepart" required>
                                    </div>
                                </div>

                                <!-- status spareparts -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="spareparts.status" type="text" placeholder="status sparepart" required>
                                    </div>
                                </div>    

                                <!-- deskripsi Sparepart -->
                                <div class="uk-width-1-1@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi Sparepart</label>
                                    <div class="uk-form-controls">
                                        <textarea input class="uk-input uk-textarea" v-model="spareparts.deskripsi" type="text" placeholder="deskripsi Sparepart"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-collapse uk-card uk-card-default uk-column-1-2@m hims-form-footer" style="border-radius: 10px;">
                        <div class="uk-width-auto uk-column-1-1@s" style="padding:1em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="spareparts.is_aktif" style="margin-left:5px;"> Data Sparepart aktif</label>
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
    name : 'form-sparepart', 
    // mixins: [Vue2Filters.mixin],

    data() {
        return {
            isUpdate : true,
            spareparts : {
                sparepart_id:null,
                sparepart_nama : null,
                serial_no:null,
                brand_nama : null,
                deskripsi:null,
                tgl_kadaluarsa : null,
                status:null,
                is_aktif : true,
                client_id : null,
                created_by : null,
            },
            unitData : {
                sparepart_id:null,
                sparepart_nama : null,
                serial_no:null,
                brand_nama : null,
                deskripsi:null,
                tgl_kadaluarsa : null,
                status:null,
                is_aktif : true,
                client_id : null,
                created_by : null,
            },

            // status_asset : [
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
        // this.createAsset();
        // this['assets/form-asset']();
    },
    

    methods : {
        ...mapActions('spareparts',['createSparepart','updateSparepart','dataSparepart']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formSparepart').hide();
            return false;
        },

        submitSparepart(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createSparepart(this.spareparts).then((response) => {
                    if (response.success == true) {
                        alert("Data Sparepart baru berhasil dibuat.");
                        // this.fillAsset(response.data);
                        this.clearSparepart();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        UIkit.modal('#formSparepart').hide();
                    }
                })
            }
            else {
                this.updateSparepart(this.spareparts).then((response) => {
                    if (response.success == true) {
                        // this.fillAsset(response.data);
                        alert("Data Sparepart berhasil diubah.");
                        // this.$emit('saveSucceed',true);
                        // this.isUpdate = true;
                        this.clearSparepart();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        UIkit.modal('#editformSparepart').hide();
                        this.closeModal();
                    }
                })
            }            
        },

        newSparepart(){
            this.clearSparepart();
            this.isUpdate = false;
            UIkit.modal('#formSparepart').show();
        },

        editSparepart(sparepart_id){
            console.log(444);
            this.clearSparepart();            
            this.dataSparepart(sparepart_id).then((response)=>{
                if (response.success == true) {
                    this.fillSparepart(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#editformSparepart').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearSparepart(){
            this.spareparts.sparepart_nama = null;
            this.spareparts.serial_no = null;
            this.spareparts.brand_nama = null;
            this.spareparts.deskripsi = null;
            this.spareparts.tgl_kadaluarsa = null;
            this.spareparts.status = null;
            this.spareparts.is_aktif = true;
            this.spareparts.client_id = null;
        },

        fillSparepart(data){
            this.spareparts.sparepart_nama = data.sparepart_nama;
            this.spareparts.serial_no = data.serial_no;
            this.spareparts.brand_nama = data.brand_nama;
            this.spareparts.deskripsi = data.deskripsi;
            this.spareparts.tgl_kadaluarsa = data.tgl_kadaluarsa;
            this.spareparts.status = data.status;
            this.spareparts.is_aktif = data.is_aktif;
            this.spareparts.client_id = data.client_id;

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