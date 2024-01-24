<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formSigna" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitSigna" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA SIGNA</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0.5em;">     
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Signa*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="signa.signa" type="text" placeholder="signa" required :disabled="isUpdate">
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Cara Konsumsi</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="signa.deskripsi" type="text" placeholder="cara konsumsi" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="signa.is_aktif" style="margin-left:5px;"> Data signa aktif</label>
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
    name : 'form-signa', 
    data() {
        return {
            isUpdate : true,
            signa : {
                client_id:null,
                signa_id:null,
                signa : null,
                deskripsi : null,
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
        ...mapActions('signa',['createSigna','dataSigna','updateSigna']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formSigna').hide();
            return false;
        },
        
        editSigna(signaId){
            this.clearSigna();            
            this.dataSigna(signaId).then((response)=>{
                if (response.success == true) {
                    this.fillSigna(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formSigna').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        submitSigna(){
            this.CLR_ERRORS();
            this.signa.deskripsi = this.signa.deskripsi.toUpperCase();
            if(!this.isUpdate) {
                this.createSigna(this.signa).then((response) => {
                    if (response.success == true) {
                        alert("Signa baru berhasil dibuat.");
                        this.clearSigna();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }  
            else {
                this.updateSigna(this.signa).then((response) => {
                    if (response.success == true) {
                        alert("Signa berhasil diubah.");
                        this.clearSigna();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeModal();
                    }
                })
            }       
        },

        newSigna(){
            this.clearSigna();
            this.isUpdate = false;
            UIkit.modal('#formSigna').show();
        },  

        clearSigna(){
            this.signa.client_id = null;
            this.signa.signa_id = null;
            this.signa.signa = null;
            this.signa.deskripsi = null;
            this.signa.is_aktif = true;
        },

        fillSigna(data){
            this.signa.client_id = null;
            this.signa.signa_id = data.signa_id;
            this.signa.signa = data.signa;
            this.signa.deskripsi = data.deskripsi;
            this.signa.is_aktif = data.is_aktif;
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