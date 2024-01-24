<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formAkunProduk" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitAkunProduk" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">AKUN PRODUK</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalAkunProduk" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Produk*</label>
                            <div class="uk-form-controls">
                                <select v-if="isRefProdukExist" v-model="akunProduk.jenis_produk" required class="uk-select uk-form-small" :disabled="isUpdate">
                                    <option v-for="option in jenisProdukRefs.json_data" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                </select>
                            </div>
                        </div>   
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Akun Produk*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="akunProduk.produk_akun" type="text" :disabled="isUpdate" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="akunProduk.deskripsi" type="text" required></textarea>
                            </div>
                        </div>                  
                        <div class="uk-width-1-1@m">
                            <search-select
                                :config = configSelect
                                searchUrl = "/coa/accounts"
                                label = "Akun Revenue"
                                placeholder = "akun pendapatan produk"
                                captionField = "text_coa"
                                valueField = "text_coa"
                                :itemListData = coaDesc
                                :value = "akunProduk.coa_revenue"
                                v-on:item-select = "akunRevenueSelected"
                            ></search-select>
                        </div>
                        <div class="uk-width-1-1@m">
                            <search-select
                                :config = configSelect
                                searchUrl = "/coa/accounts"
                                label = "Akun Inventory"
                                placeholder = "akun persediaan produk"
                                captionField = "text_coa"
                                valueField = "text_coa"
                                :itemListData = coaDesc
                                :value = "akunProduk.coa_inventory"
                                v-on:item-select = "akunInventorySelected"
                            ></search-select>
                        </div>
                        <div class="uk-width-1-1@m">
                            <search-select
                                :config = configSelect
                                searchUrl = "/coa/accounts"
                                label = "Akun COGS"
                                placeholder = "akun biaya pengadaan produk"
                                captionField = "text_coa"
                                valueField = "text_coa"
                                :itemListData = coaDesc
                                :value = "akunProduk.coa_cogs"
                                v-on:item-select = "akunCogsSelected"
                            ></search-select>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="akunProduk.is_aktif" style="margin-left:5px;"> Akun Produk aktif</label>
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
    name : 'form-akun-produk', 
    components : {
        SearchSelect,
        SearchList,
    },
    data() {
        return {
            isUpdate : true,
            akunProduk : {
                client_id : null,
                produk_akun_id : null,
                produk_akun : null,
                jenis_produk : null,
                
                coa_revenue_id : null,
                coa_revenue : null,
                
                coa_inventory_id : null,
                coa_inventory : null,
                
                coa_cogs_id : null,
                coa_cogs : null,
                
                deskripsi : null,
                
                is_aktif : true,
            },
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : false,
            },
            coaDesc : [
                { colName : 'text_coa', labelData : '', isTitle : true },
                { colName : 'nama_coa', labelData : '', isTitle : false },
                { colName : 'kode_coa', labelData : 'ID: ', isTitle : false },
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
        ...mapActions('produkAkun',['createProdukAkun','updateProdukAkun','dataProdukAkun']),        
        ...mapActions('refProduk',['listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),
        
        initialize() {
            this.listRefProduk();
        },

        closeModalAkunProduk(){
            this.$emit('closed',true);
            UIkit.modal('#formAkunProduk').hide();
        },

        submitAkunProduk(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createProdukAkun(this.akunProduk).then((response) => {
                    if (response.success == true) {
                        alert("Account Product baru berhasil dibuat.");
                        this.clearAkunProduk();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateProdukAkun(this.akunProduk).then((response) => {
                    if (response.success == true) {
                        alert("Account Product berhasil diubah.");
                        this.clearAkunProduk();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalAkunProduk();
                    }
                })
            }         
        },  

        akunCogsSelected(data) {
            if(data) {
                this.akunProduk.coa_cogs = data.text_coa;
                this.akunProduk.coa_cogs_id = data.coa_id;
            }
        },

        akunRevenueSelected(data) {
            if(data) {
                this.akunProduk.coa_revenue = data.text_coa;
                this.akunProduk.coa_revenue_id = data.coa_id;
            }
        },
        
        akunInventorySelected(data) {
            if(data) {
                this.akunProduk.coa_inventory_id = data.coa_id;
                this.akunProduk.coa_inventory = data.text_coa;
            }
        },
        
        newAkunProduk(){
            this.clearAkunProduk();
            this.isUpdate = false;
            UIkit.modal('#formAkunProduk').show();
        },  

        editAkunProduk(id){
            this.clearAkunProduk();            
            this.dataProdukAkun(id).then((response)=>{
                if (response.success == true) {
                    this.fillAkunProduk(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formAkunProduk').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearAkunProduk(){
            this.akunProduk.client_id = null;
            this.akunProduk.produk_akun_id = null;
            this.akunProduk.produk_akun = null;
            this.akunProduk.jenis_produk = null;
            
            this.akunProduk.deskripsi = null;
            
            this.akunProduk.coa_revenue_id = null;
            this.akunProduk.coa_inventory_id = null;
            this.akunProduk.coa_cogs_id = null;
            
            this.akunProduk.coa_revenue = null;
            this.akunProduk.coa_inventory = null;
            this.akunProduk.coa_cogs = null;
                
            this.akunProduk.is_aktif = true;
        },

        fillAkunProduk(data){
            this.akunProduk.client_id = null;
            this.akunProduk.produk_akun_id = data.produk_akun_id;
            this.akunProduk.produk_akun = data.produk_akun;
            this.akunProduk.jenis_produk = data.jenis_produk;
            
            this.akunProduk.deskripsi = data.deskripsi;
            
            this.akunProduk.coa_revenue_id = data.coa_revenue_id;
            this.akunProduk.coa_inventory_id = data.coa_inventory_id;
            this.akunProduk.coa_cogs_id = data.coa_cogs_id;
            
            this.akunProduk.coa_revenue = data.coa_revenue;
            this.akunProduk.coa_inventory = data.coa_inventory;
            this.akunProduk.coa_cogs = data.coa_cogs;
            
            
            this.akunProduk.is_aktif = data.is_aktif;
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