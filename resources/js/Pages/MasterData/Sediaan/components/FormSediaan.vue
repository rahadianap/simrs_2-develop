<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formSediaan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitSediaan" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA SEDIAAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalSediaan" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0.5em;">                                 
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Sediaan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="sediaan.sediaan" type="text" placeholder="nama sediaan" required :disabled="isUpdate">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="sediaan.deskripsi" type="text" placeholder="deskripsi">{{sediaan.deskripsi}}</textarea>
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
    name : 'form-sediaan', 
    data() {
        return {
            isUpdate : true,
            sediaan : {
                client_id:null,
                sediaan_id:null,
                sediaan : null,
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
        ...mapActions('sediaan',['createSediaan','dataSediaan','updateSediaan']),
        ...mapMutations(['CLR_ERRORS']),

        closeModalSediaan(){
            UIkit.modal('#formSediaan').hide();
            this.$emit('closed',true);
        },

        editSediaan(id){
            this.clearSediaan();            
            this.dataSediaan(id).then((response)=>{
                if (response.success == true) {
                    this.fillSediaan(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formSediaan').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        submitSediaan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createSediaan(this.sediaan).then((response) => {
                    if (response.success == true) {
                        alert("Sediaan baru berhasil dibuat.");
                        this.clearSediaan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateSediaan(this.sediaan).then((response) => {
                    if (response.success == true) {
                        alert("Sediaan berhasil diubah.");
                        this.clearSediaan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalSediaan();
                    }
                })
            }         
        },

        newSediaan(){
            this.clearSediaan();
            this.isUpdate = false;
            UIkit.modal('#formSediaan').show();
        },  

        clearSediaan(){
            this.sediaan.client_id = null;
            this.sediaan.sediaan_id = null;
            this.sediaan.sediaan = null;
            this.sediaan.deskripsi = null;
            this.sediaan.is_aktif = true;
        },

        fillSediaan(data){
            this.sediaan.client_id = null;
            this.sediaan.sediaan_id = data.sediaan_id;
            this.sediaan.sediaan = data.sediaan;
            this.sediaan.deskripsi = data.deskripsi;
            this.sediaan.is_aktif = data.is_aktif;
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