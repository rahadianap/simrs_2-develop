<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formSatuan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitSatuan" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA SEDIAAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalSatuan" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Satuan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="satuan.satuan_id" type="text" placeholder="satuan" required :disabled="isUpdate">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Satuan</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="satuan.satuan_nama" type="text" placeholder="satuan" required>
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
    name : 'form-satuan', 
    data() {
        return {
            isUpdate : true,
            satuan : {
                client_id:null,
                satuan_id:null,
                satuan_nama : null,
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
        ...mapActions('satuan',['createSatuan','dataSatuan','updateSatuan']),
        ...mapMutations(['CLR_ERRORS']),

        closeModalSatuan(){
            this.$emit('closed',true);
            UIkit.modal('#formSatuan').hide();
        },

        editSatuan(id){
            this.clearSatuan();            
            this.dataSatuan(id).then((response)=>{
                if (response.success == true) {
                    this.fillSatuan(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formSatuan').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        submitSatuan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createSatuan(this.satuan).then((response) => {
                    if (response.success == true) {
                        alert("Satuan baru berhasil dibuat.");
                        this.clearSatuan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateSatuan(this.satuan).then((response) => {
                    if (response.success == true) {
                        alert("Satuan berhasil diubah.");
                        this.clearSatuan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalSatuan();
                    }
                })
            }         
        },

        newSatuan(){
            this.clearSatuan();
            this.isUpdate = false;
            UIkit.modal('#formSatuan').show();
        },  

        clearSatuan(){
            this.satuan.client_id = null;
            this.satuan.satuan_id = null;
            this.satuan.satuan_nama = null;
            this.satuan.is_aktif = true;
        },

        fillSatuan(data){
            this.satuan.client_id = null;
            this.satuan.satuan_id = data.satuan_id;
            this.satuan.satuan_nama = data.satuan_nama;
            this.satuan.is_aktif = data.is_aktif;
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