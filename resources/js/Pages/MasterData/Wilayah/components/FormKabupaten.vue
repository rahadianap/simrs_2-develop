<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formKabupaten" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitKabupaten" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">KABUPATEN / KOTA</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalKabupaten" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                                <input class="uk-input uk-form-small" v-model="kabupaten.propinsi" type="text" disabled>
                            </div>
                        </div>   
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Kabupaten/Kota*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kabupaten.kota" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis</label>
                            <div class="uk-form-controls">
                                <select v-model="kabupaten.jenis" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                    <option value=""></option>
                                    <option value="Kabupaten">Kabupaten</option>
                                    <option value="Kota">Kota</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kabupaten.is_prioritas" style="margin-left:5px;"> Kabupaten ini prioritas pilihan</label>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kabupaten.is_aktif" style="margin-left:5px;"> Kabupaten aktif</label>
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
    name : 'form-kabupaten', 
    data() {
        return {
            isUpdate : true,
            kabupaten : {
                client_id:null,
                propinsi_id:null,
                propinsi : null,
                kota_id : null,
                kota : null,
                no_urut : null,
                jenis : null,
                is_prioritas : false,
                is_aktif : true,
            },
            propinsi : {
                propinsi_id : null,
                propinsi : null,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('kabupaten',['createKabupaten','dataKabupaten','updateKabupaten']),
        ...mapMutations(['CLR_ERRORS']),

        closeModalKabupaten(){
            this.$emit('closed',true);
            UIkit.modal('#formKabupaten').hide();
        },

        submitKabupaten(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createKabupaten(this.kabupaten).then((response) => {
                    if (response.success == true) {
                        alert("data kabupaten baru berhasil dibuat.");
                        this.clearKabupaten();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateKabupaten(this.kabupaten).then((response) => {
                    if (response.success == true) {
                        alert("data kabupaten berhasil diubah.");
                        this.clearKabupaten();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalKabupaten();
                    }
                })
            }         
        },

        newKabupaten(dataPropinsi){
            this.propinsi = dataPropinsi;
            this.clearKabupaten();
            this.isUpdate = false;
            UIkit.modal('#formKabupaten').show();
        },  

        editKabupaten(propinsiData, id){
            this.propinsi = propinsiData;
            this.clearKabupaten();            
            this.dataKabupaten(id).then((response)=>{
                if (response.success == true) {
                    this.fillKabupaten(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formKabupaten').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearKabupaten(){
            this.kabupaten.client_id = null;
            this.kabupaten.propinsi_id = this.propinsi.propinsi_id;
            this.kabupaten.propinsi = this.propinsi.propinsi;
            this.kabupaten.kota_id = null;
            this.kabupaten.kota = null;
            this.kabupaten.jenis = null; 
            this.kabupaten.is_prioritas = false;
            this.kabupaten.is_aktif = true; 
            this.kabupaten.no_urut = null;
        },

        fillKabupaten(data){
            this.kabupaten.client_id = null;
            this.kabupaten.kota_id = data.kota_id;
            this.kabupaten.kota = data.kota;
            this.kabupaten.propinsi_id = data.propinsi_id;
            this.kabupaten.propinsi = data.propinsi;
            this.kabupaten.no_urut = data.no_urut;
            this.kabupaten.jenis = data.jenis; 
            this.kabupaten.is_aktif = data.is_aktif;
            this.kabupaten.is_prioritas = data.is_prioritas;
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