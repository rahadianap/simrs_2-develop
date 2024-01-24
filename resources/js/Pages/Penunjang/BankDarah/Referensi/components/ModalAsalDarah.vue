<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalAsalDarah" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitAsalDarah" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">ASAL DARAH</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalAsalDarah" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Asal Darah*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="asalDarah.asal_darah" type="text" :disabled="isUpdate" required>
                            </div>
                        </div>                        
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="asalDarah.deskripsi" type="text" required></textarea>
                            </div>
                        </div> 
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="asalDarah.is_aktif" style="margin-left:5px;"> Asal darah aktif</label>
                        </div>
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'modal-produk-darah', 
    components : {
        SearchSelect,
        SearchList,
    },
    data() {
        return {
            isUpdate : true,
            asalDarah : {
                asal_darah_id : null,
                asal_darah : null,
                client_id : null,
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
        ...mapActions('asalDarah',['createAsalDarah','updateAsalDarah','dataAsalDarah']),      
        ...mapMutations(['CLR_ERRORS']),

        closeModalAsalDarah(){
            this.$emit('closedModalAsalDarah',true);
            UIkit.modal('#modalAsalDarah').hide();
        },

        submitAsalDarah(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createAsalDarah(this.asalDarah).then((response) => {
                    if (response.success == true) {
                        alert("asal darah baru berhasil dibuat.");
                        this.clearAsalDarah();
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateAsalDarah(this.asalDarah).then((response) => {
                    if (response.success == true) {
                        alert("Asal darah berhasil diubah.");
                        this.clearAsalDarah();
                        this.isUpdate = false;
                        this.closeModalAsalDarah();
                    }
                })
            }         
        },        
        
        newData(){
            this.clearAsalDarah();
            this.isUpdate = false;
            UIkit.modal('#modalAsalDarah').show();
        },  

        editData(id){
            this.clearAsalDarah();            
            this.dataAsalDarah(id).then((response)=>{
                if (response.success == true) {
                    this.fillAsalDarah(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#modalAsalDarah').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearAsalDarah(){
            this.asalDarah.client_id = null;
            this.asalDarah.asal_darah_id = null;
            this.asalDarah.asal_darah = null;
            this.asalDarah.deskripsi = null;
            this.asalDarah.is_aktif = true;
        },

        fillAsalDarah(data){
            this.asalDarah.client_id = data.client_id;
            this.asalDarah.asal_darah_id = data.asal_darah_id;
            this.asalDarah.asal_darah = data.asal_darah;
            this.asalDarah.deskripsi = data.deskripsi;
            this.asalDarah.is_aktif = data.is_aktif;
        },
    }
}
</script>
