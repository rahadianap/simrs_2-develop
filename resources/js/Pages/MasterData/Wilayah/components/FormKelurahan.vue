<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formKelurahan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitKelurahan" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">Kelurahan</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalKelurahan" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                                <input class="uk-input uk-form-small" v-model="kelurahan.propinsi" type="text" disabled>
                            </div>
                        </div>   
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kabupaten/Kota</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kelurahan.kota" type="text" disabled>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kecamatan</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kelurahan.kecamatan" type="text" disabled>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelurahan</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kelurahan.kelurahan" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kodepos</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kelurahan.kodepos" type="text">
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kelurahan.is_prioritas" style="margin-left:5px;"> Kelurahan ini prioritas pilihan</label>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kelurahan.is_aktif" style="margin-left:5px;"> Kelurahan aktif</label>
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
    name : 'form-kelurahan', 
    data() {
        return {
            isUpdate : true,
            kelurahan : {
                client_id:null,
                propinsi_id:null,
                propinsi : null,
                kota_id : null,
                kota : null,
                kecamatan_id : null,
                kecamatan: null,
                kelurahan_id : null,
                kelurahan: null,
                kodepos : null,
                is_prioritas : false,
                is_aktif : true,
            },

            kecamatan : {
                propinsi_id : null,
                propinsi : null,
                kota_id : null,
                kota : null,
                kecamatan_id : null,
                kecamatan : null,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('kelurahan',['createKelurahan','dataKelurahan','updateKelurahan']),
        ...mapMutations(['CLR_ERRORS']),

        closeModalKelurahan(){
            this.$emit('closed',true);
            UIkit.modal('#formKelurahan').hide();
        },

        submitKelurahan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createKelurahan(this.kelurahan).then((response) => {
                    if (response.success == true) {
                        alert("data kelurahan baru berhasil dibuat.");
                        this.clearKelurahan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateKelurahan(this.kelurahan).then((response) => {
                    if (response.success == true) {
                        alert("data kelurahan berhasil diubah.");
                        this.clearKelurahan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalKelurahan();
                    }
                })
            }         
        },

        newKelurahan(dataKecamatan){
            this.kecamatan = dataKecamatan;
            this.clearKelurahan();
            this.isUpdate = false;
            UIkit.modal('#formKelurahan').show();
        },  

        editKelurahan(kecamatanData, id){
            this.kecamatan = kecamatanData;
            this.clearKelurahan();            
            this.dataKelurahan(id).then((response)=>{
                if (response.success == true) {
                    this.fillKelurahan(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formKelurahan').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearKelurahan(){
            this.kelurahan.client_id = null;
            this.kelurahan.propinsi_id = this.kecamatan.propinsi_id;
            this.kelurahan.propinsi = this.kecamatan.propinsi;
            this.kelurahan.kota_id = this.kecamatan.kota_id;
            this.kelurahan.kota = this.kecamatan.kota;            
            this.kelurahan.kecamatan_id = this.kecamatan.kecamatan_id;
            this.kelurahan.kecamatan = this.kecamatan.kecamatan;
            this.kelurahan.kelurahan_id = null;
            this.kelurahan.kelurahan = null;
            this.kelurahan.kodepos = null;
            this.kelurahan.is_prioritas = false;
            this.kelurahan.is_aktif = true; 
        },

        fillKelurahan(data){
            this.kelurahan.client_id = null;
            this.kelurahan.kota_id = data.kota_id;
            this.kelurahan.kota = data.kota;
            this.kelurahan.propinsi_id = data.propinsi_id;
            this.kelurahan.propinsi = data.propinsi;
            this.kelurahan.kecamatan_id = data.kecamatan_id;
            this.kelurahan.kecamatan = data.kecamatan;
            this.kelurahan.kelurahan_id = data.kelurahan_id;
            this.kelurahan.kelurahan = data.kelurahan;
            this.kelurahan.kodepos = data.kodepos;
            this.kelurahan.is_prioritas = data.is_prioritas;
            this.kelurahan.is_aktif = data.is_aktif;
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