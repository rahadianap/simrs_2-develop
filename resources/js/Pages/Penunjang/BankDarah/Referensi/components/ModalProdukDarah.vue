<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalProdukDarah" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitProdukDarah" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">JENIS PRODUK DARAH</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalProdukDarah" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0.5em;">                                 
                        
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Produk Darah*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="produkDarah.jenis_produk_darah" type="text" :disabled="isUpdate" required>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Inisial*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="produkDarah.inisial" type="text">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="produkDarah.deskripsi" type="text" required></textarea>
                            </div>
                        </div>      

                        <div class="uk-width-1-1@m">
                            <search-select
                                :config = configSelect
                                searchUrl = "/acts"
                                label = "Biaya Layanan*"
                                placeholder = "biaya pelayanan"
                                captionField = "tindakan_nama"
                                valueField = "tindakan_nama"
                                :itemListData = tindakanDesc
                                :value = "produkDarah.tindakan_nama"
                                v-on:item-select = "tindakanSelected"
                            ></search-select>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="produkDarah.is_aktif" style="margin-left:5px;"> Jenis produk darah aktif</label>
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
            produkDarah : {
                jenis_produk_darah_id : null,
                jenis_produk_darah : null,
                inisial : null,
                tindakan_id : null,
                tindakan_nama : null,
                client_id : null,
                deskripsi : null,
                is_aktif : true,
            },
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : false,
            },
            tindakanDesc : [
                { colName : 'tindakan_nama', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
                { colName : 'tindakan_id', labelData : '', isTitle : false },
            ],
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refProduk',['jenisProdukRefs','isRefProdukExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {        
        ...mapActions('produkDarah',['createProdukDarah','updateProdukDarah','dataProdukDarah']),      
        ...mapMutations(['CLR_ERRORS']),
        
        initialize() {
        },

        closeModalProdukDarah(){
            this.$emit('closedModalProdukDarah',true);
            UIkit.modal('#modalProdukDarah').hide();
        },

        submitProdukDarah(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createProdukDarah(this.produkDarah).then((response) => {
                    if (response.success == true) {
                        alert("Jenis produk darah baru berhasil dibuat.");
                        this.clearProdukDarah();
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateProdukDarah(this.produkDarah).then((response) => {
                    if (response.success == true) {
                        alert("Jenis produk darah berhasil diubah.");
                        this.clearProdukDarah();
                        this.isUpdate = false;
                        this.closeModalProdukDarah();
                    }
                })
            }         
        },

        tindakanSelected(data) {
            if(data) {
                this.produkDarah.tindakan_nama = data.tindakan_nama;
                this.produkDarah.tindakan_id = data.tindakan_id;
            }
        },        
        
        newData(){
            this.clearProdukDarah();
            this.isUpdate = false;
            UIkit.modal('#modalProdukDarah').show();
        },  

        editData(id){
            this.clearProdukDarah();            
            this.dataProdukDarah(id).then((response)=>{
                if (response.success == true) {
                    this.fillProdukDarah(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#modalProdukDarah').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearProdukDarah(){
            this.produkDarah.client_id = null;
            this.produkDarah.jenis_produk_darah_id = null;
            this.produkDarah.jenis_produk_darah = null;
            this.produkDarah.inisial = null;
            this.produkDarah.deskripsi = null;
            this.produkDarah.tindakan_id = null;
            this.produkDarah.tindakan_nama = null;
            this.produkDarah.is_aktif = true;
        },

        fillProdukDarah(data){
            this.produkDarah.client_id = data.client_id;
            this.produkDarah.jenis_produk_darah_id = data.jenis_produk_darah_id;
            this.produkDarah.jenis_produk_darah = data.jenis_produk_darah;
            this.produkDarah.inisial = data.inisial;
            this.produkDarah.deskripsi = data.deskripsi;
            this.produkDarah.tindakan_id = data.tindakan_id;
            this.produkDarah.tindakan_nama = data.tindakan_nama;
            this.produkDarah.is_aktif = data.is_aktif;
        },
    }
}
</script>
