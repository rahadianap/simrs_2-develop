<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formKecamatan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitKecamatan" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">KECAMATAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalKecamatan" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-grid-small" uk-grid style="padding:0;">   
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Propinsi</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kecamatan.propinsi" type="text" disabled>
                            </div>
                        </div>   
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kabupaten/Kota</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kecamatan.kota" type="text" disabled>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kecamatan</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kecamatan.kecamatan" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kecamatan.is_prioritas" style="margin-left:5px;"> Kecamatan ini prioritas pilihan</label>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kecamatan.is_aktif" style="margin-left:5px;"> Kecamatan aktif</label>
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
    name : 'form-kecamatan', 
    data() {
        return {
            isUpdate : true,
            kecamatan : {
                client_id:null,
                propinsi_id:null,
                propinsi : null,
                kota_id : null,
                kota : null,
                kecamatan_id : null,
                kecamatan: null,
                is_prioritas : false,
                is_aktif : true,
            },

            kabupaten : {
                propinsi_id : null,
                propinsi : null,
                kabupaten_id : null,
                kabupaten : null,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('kecamatan',['createKecamatan','dataKecamatan','updateKecamatan']),
        ...mapMutations(['CLR_ERRORS']),

        closeModalKecamatan(){
            this.$emit('closed',true);
            UIkit.modal('#formKecamatan').hide();
        },

        submitKecamatan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createKecamatan(this.kecamatan).then((response) => {
                    if (response.success == true) {
                        alert("data kecamatan baru berhasil dibuat.");
                        this.clearKecamatan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateKecamatan(this.kecamatan).then((response) => {
                    if (response.success == true) {
                        alert("data kecamatan berhasil diubah.");
                        this.clearKecamatan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalKecamatan();
                    }
                })
            }         
        },

        newKecamatan(dataKabupaten){
            this.kabupaten = dataKabupaten;
            this.clearKecamatan();
            this.isUpdate = false;
            UIkit.modal('#formKecamatan').show();
        },  

        editKecamatan(kabupatenData, id){
            this.kabupaten = kabupatenData;
            this.clearKecamatan();            
            this.dataKecamatan(id).then((response)=>{
                if (response.success == true) {
                    this.fillKecamatan(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formKecamatan').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearKecamatan(){
            this.kecamatan.client_id = null;
            this.kecamatan.propinsi_id = this.kabupaten.propinsi_id;
            this.kecamatan.propinsi = this.kabupaten.propinsi;
            this.kecamatan.kota_id = this.kabupaten.kota_id;
            this.kecamatan.kota = this.kabupaten.kota;
            this.kecamatan.kecamatan_id = null;
            this.kecamatan.kecamatan = null;
            this.kecamatan.is_prioritas = false;
            this.kecamatan.is_aktif = true; 
        },

        fillKecamatan(data){
            this.kecamatan.client_id = null;
            this.kecamatan.kota_id = data.kota_id;
            this.kecamatan.kota = data.kota;
            this.kecamatan.propinsi_id = data.propinsi_id;
            this.kecamatan.propinsi = data.propinsi;
            this.kecamatan.kecamatan_id = data.kecamatan_id;
            this.kecamatan.kecamatan = data.kecamatan;
            this.kecamatan.is_aktif = data.is_aktif;
            this.kecamatan.is_prioritas = data.is_prioritas;
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