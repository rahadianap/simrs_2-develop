<template>
    <!-- Popup Modal tambah data Lokasi -->
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formLokasi" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitLokasi" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" style="border-radius:10px;justify-content:center;text-align:center;" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 1em 0.1em;margin:0;">
                            <h5 class="uk-text-uppercase">TAMBAH DATA LOKASI</h5>
                        </div>                                
                        <!-- <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div> -->
                    </div>
                    <div style="margin:0 10px;border-top:2px solid #cc0202;">                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small hims-form-subpage-asset" style="margin:30px 0" uk-grid >
                            <div class="uk-width-1-1@s uk-grid-small" style="margin-left:1px;padding-left:1px;" uk-grid>
                                <!-- nama lokasi -->
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Lokasi*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="locations.lokasi_nama" type="text" placeholder="masukkan nama lokasi" required>
                                    </div>
                                </div>

                                <!-- deskripsi lokasi -->
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi Lokasi*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="locations.deskripsi" type="text" placeholder="masukkan deskripsi lokasi" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-collapse uk-card uk-card-default hims-form-footer uk-column-1-2@m" style="border-radius: 10px;">
                        <div class="uk-width-auto uk-column-1-1@s" style="padding:1em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="locations.is_aktif" style="margin-left:5px;"> Data Lokasi aktif</label>
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
    name : 'form-lokasi', 
    // mixins: [Vue2Filters.mixin],

    data() {
        return {
            isUpdate : true,
            locations : {
                lokasi_aset_id:null,
                lokasi_nama:null,
                deskripsi:null,
                client_id : null,
                created_by : null,
                updated_by : null,
                is_aktif : true,
            },
            unitData : {
                lokasi_aset_id:null,
                lokasi_nama:null,
                deskripsi:null,
                client_id : null,
                created_by : null,
                updated_by : null,
                is_aktif : true,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    

    methods : {
        ...mapActions('lokasi',['createLokasi','updateLokasi','dataLokasi']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formLokasi').hide();
            return false;
        },

        submitLokasi(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createLokasi(this.locations).then((response) => {
                    if (response.success == true) {
                        alert("Data Lokasi baru berhasil dibuat.");
                        // this.fillAsset(response.data);
                        this.clearLokasi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        UIkit.modal('#formLokasi').hide();
                    }
                })
            }
            else {
                this.updateLokasi(this.locations).then((response) => {
                    if (response.success == true) {
                        // this.fillAsset(response.data);
                        alert("Data Lokasi berhasil diubah.");
                        // this.$emit('saveSucceed',true);
                        // this.isUpdate = true;
                        this.clearLokasi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        UIkit.modal('#editFormLokasi').hide();
                        this.closeModal();
                    }
                })
            }            
        },

        newLokasi(){
            this.clearLokasi();
            this.isUpdate = false;
            UIkit.modal('#formLokasi').show();
        },

        editFormLokasi(lokasi_aset_id){
            console.log(444);
            this.clearLokasi();            
            this.dataLokasi(lokasi_aset_id).then((response)=>{
                if (response.success == true) {
                    this.fillLokasi(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#editFormLokasi').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearLokasi(){
            this.locations.lokasi_aset_id = null;
            this.locations.lokasi_nama = null;
            this.locations.deskripsi = null;
            this.locations.is_aktif = true;
            this.locations.client_id = null;
            this.locations.created_by = true;
            this.locations.updated_by = null;
        },

        fillLokasi(data){
            // this.locations.asset_id = data.asset_id;
            this.locations.lokasi_aset_id = data.lokasi_aset_id;
            this.locations.lokasi_nama = data.lokasi_nama;
            this.locations.deskripsi = data.deskripsi;
            this.locations.is_aktif = data.is_aktif;
            this.locations.client_id = data.client_id;
            this.locations.created_by = data.created_by;
            this.locations.updated_by = data.updated_by;
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


.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */

</style>