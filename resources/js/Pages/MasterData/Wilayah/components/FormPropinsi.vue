<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formPropinsi" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitPropinsi" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">PROPINSI</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalPropinsi" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-grid-small" uk-grid style="padding:0;">      
                        <div class="uk-width-3-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Propinsi*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="propinsi.propinsi" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Urut*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="propinsi.no_urut" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="propinsi.is_prioritas" style="margin-left:5px;"> Propinsi ini prioritas pilihan</label>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="propinsi.is_aktif" style="margin-left:5px;"> Propinsi aktif</label>
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
    name : 'form-propinsi', 
    data() {
        return {
            isUpdate : true,
            propinsi : {
                client_id:null,
                propinsi_id:null,
                propinsi : null,
                no_urut : null,
                is_prioritas : false,
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
        ...mapActions('propinsi',['createPropinsi','dataPropinsi','updatePropinsi']),
        ...mapMutations(['CLR_ERRORS']),

        closeModalPropinsi(){
            this.$emit('closed',true);
            UIkit.modal('#formPropinsi').hide();
        },

        submitPropinsi(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createPropinsi(this.propinsi).then((response) => {
                    if (response.success == true) {
                        alert("data propinsi baru berhasil dibuat.");
                        this.clearPropinsi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updatePropinsi(this.propinsi).then((response) => {
                    if (response.success == true) {
                        alert("data propinsi berhasil diubah.");
                        this.clearPropinsi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalPropinsi();
                    }
                })
            }         
        },

        newPropinsi(){
            this.clearPropinsi();
            this.isUpdate = false;
            UIkit.modal('#formPropinsi').show();
        },  

        editPropinsi(id){
            this.clearPropinsi();            
            this.dataPropinsi(id).then((response)=>{
                if (response.success == true) {
                    this.fillPropinsi(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formPropinsi').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearPropinsi(){
            this.propinsi.client_id = null;
            this.propinsi.propinsi_id = null;
            this.propinsi.propinsi = null;
            this.propinsi.is_aktif = true; 
            this.propinsi.is_prioritas = false; 
            this.propinsi.no_urut = null;
        },

        fillPropinsi(data){
            this.propinsi.client_id = null;
            this.propinsi.propinsi_id = data.propinsi_id;
            this.propinsi.propinsi = data.propinsi;
            this.propinsi.no_urut = data.no_urut;
            this.propinsi.is_prioritas = data.is_prioritas; 
            this.propinsi.is_aktif = data.is_aktif;
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