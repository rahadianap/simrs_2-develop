<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formSpesialis" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitSpesialisasi" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA SPESIALISASI</h5>
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Inisial*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="spesialisasi.spesialisasi_id" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Spesialisasi*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="spesialisasi.spesialisasi" type="text" placeholder="nama spesialisasi" required>
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
    name : 'form-spesialisasi', 
    data() {
        return {
            isUpdate : true,
            spesialisasi : {
                client_id:null,
                spesialisasi_id:null,
                spesialisasi : null,
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
        ...mapActions('spesialisasi',['createSpesialisasi','updateSpesialisasi','dataSpesialisasi']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formSpesialis').hide();
            return false;
        },

        submitSpesialisasi(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createSpesialisasi(this.spesialisasi).then((response) => {
                    if (response.success == true) {
                        alert("Spesialisasi baru berhasil dibuat.");
                        this.clearSpesialisasi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        UIkit.modal('#formSpesialis').hide();
                    }
                })
            }
            else {
                this.updateSpesialisasi(this.spesialisasi).then((response) => {
                    if (response.success == true) {
                        this.fillSpesialisasi(response.data);
                        alert("Spesialisasi berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        UIkit.modal('#formSpesialis').hide();
                    }
                })
            }            
        },

        newSpesialisasi(){
            this.clearSpesialisasi();
            this.isUpdate = false;
            UIkit.modal('#formSpesialis').show();
        },

        editSpesialisasi(spesialisasiId){
            this.clearSpesialisasi();            
            this.dataSpesialisasi(spesialisasiId).then((response)=>{
                if (response.success == true) {
                    this.fillSpesialisasi(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formSpesialis').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearSpesialisasi(){
            this.spesialisasi.client_id = null;
            this.spesialisasi.spesialisasi_id = null;
            this.spesialisasi.spesialisasi = null;
            this.spesialisasi.is_aktif = true;
        },

        fillSpesialisasi(data){
            this.spesialisasi.client_id = null;
            this.spesialisasi.spesialisasi_id = data.spesialisasi_id;
            this.spesialisasi.spesialisasi = data.spesialisasi;
            this.spesialisasi.is_aktif = data.is_aktif;
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