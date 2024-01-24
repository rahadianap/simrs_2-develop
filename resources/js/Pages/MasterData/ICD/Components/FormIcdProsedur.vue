<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formIcdProcedure" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitIcdProcedure" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">KODE ICD9 PROCEDURE</h5>
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kode ICD*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="icd9.kode_icd" type="text" required  :disabled="isUpdate" placeholder="kode icd">
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama ICD</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="icd9.nama_icd" type="text" placeholder="nama icd">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kata Pencarian</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="icd9.kata_kunci" type="text" placeholder="kata pencarian"></textarea>
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="icd9.is_valid_icd" style="margin-left:5px;"> Valid Kode ICD</label>
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
    name : 'form-icd-prosedur', 
    data() {
        return {
            isUpdate : true,
            icd9 : {
                client_id:null,
                kode_icd:null,
                nama_icd:null,
                kata_kunci : '',
                is_valid_icd : null,
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
        ...mapActions('icd9',['createIcdProcedure','updateIcdProcedure','dataIcdProcedure']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formIcdProcedure').hide();
            return false;
        },

        submitIcdProcedure(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createIcdProcedure(this.icd9).then((response) => {
                    if (response.success == true) {
                        alert("ICD9 baru berhasil dibuat.");
                        this.clearIcdProcedure();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }
            else {
                this.updateIcdProcedure(this.icd9).then((response) => {
                    if (response.success == true) {
                        this.fillIcdProcedure(response.data);
                        alert("ICD9 berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }            
        },

        newIcdProcedure(){
            this.clearIcdProcedure();
            this.isUpdate = false;
            UIkit.modal('#formIcdProcedure').show();
        },

        editIcdProcedure(icdId){
            this.clearIcdProcedure();            
            this.dataIcdProcedure(icdId).then((response)=>{
                if (response.success == true) {
                    this.fillIcdProcedure(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formIcdProcedure').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearIcdProcedure(){
            this.icd9.client_id = null;
            this.icd9.kode_icd = null;
            this.icd9.nama_icd = null;
            this.icd9.kata_kunci = '';
            this.icd9.is_valid_icd = true;
            this.icd9.is_aktif = true;
        },

        fillIcdProcedure(data){
            this.icd9.client_id = null;
            this.icd9.kode_icd = data.kode_icd;
            this.icd9.nama_icd = data.nama_icd;
            this.icd9.kata_kunci = data.kata_kunci;
            this.icd9.is_valid_icd = data.is_valid_icd;
            this.icd9.is_aktif = data.is_aktif;
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