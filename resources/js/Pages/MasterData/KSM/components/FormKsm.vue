<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formKsm" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitKsm" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">KELOMPOK STAFF MEDIS</h5>
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
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama SMF*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="ksm.smf_nama" type="text" required placeholder="nama SMF">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Assesmen</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="ksm.assesmen_id" type="text" placeholder="assesmen">
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
    name : 'form-ksm', 
    data() {
        return {
            isUpdate : true,
            ksm : {
                client_id:null,
                smf_id:null,
                smf_nama : null,
                assesmen_id : null,
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
        ...mapActions('ksm',['createKsm','updateKsm','dataKsm']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formKsm').hide();
            return false;
        },

        submitKsm(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createKsm(this.ksm).then((response) => {
                    if (response.success == true) {
                        alert("Kelompok staff medis baru berhasil dibuat.");
                        this.clearKsm();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;                        
                        UIkit.modal('#formKsm').hide();
                    }
                })
            }
            else {
                this.updateKsm(this.ksm).then((response) => {
                    if (response.success == true) {
                        this.fillKsm(response.data);
                        alert("Kelompok staff medis berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        UIkit.modal('#formKsm').hide();
                    }
                })
            }            
        },

        newKsm(){
            this.clearKsm();
            this.isUpdate = false;
            UIkit.modal('#formKsm').show();
        },

        editKsm(ksmId){
            this.clearKsm();            
            this.dataKsm(ksmId).then((response)=>{
                if (response.success == true) {
                    this.fillKsm(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formKsm').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearKsm(){
            this.ksm.client_id = null;
            this.ksm.smf_id = null;
            this.ksm.smf_nama = null;
            this.ksm.assesmen_id = null;
            this.ksm.is_aktif = true;
        },

        fillKsm(data){
            this.ksm.client_id = null;
            this.ksm.smf_id = data.smf_id;
            this.ksm.smf_nama = data.smf_nama;
            this.ksm.assesmen_id = data.assesmen_id;
            this.ksm.is_aktif = data.is_aktif;
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