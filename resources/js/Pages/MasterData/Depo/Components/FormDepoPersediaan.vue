<template>
    <div class="uk-container-large hims-form-container" style="padding:1em;min-height:70vh;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="closeModal" style="background-color:white;padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:2"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0;padding:0;" class="uk-text-uppercase">
                    DEPO / LOKASI PERSEDIAAN
                </h4>
                <p style="font-weight:300;margin:0;padding:0;font-size:12px;font-style:italic;">
                    ubah dan buat master data depo lokasi persediaan
                </p>
            </div>
        </div>
        
        <div class="uk-container-large hims-form-container1" style="margin-top:1em;background-color:#fff;min-height:60vh;">
            <form action="" @submit.prevent="submitDepo"  style="padding:0 1em 1em 1em;">
                <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid style="margin-top:0.5em;">
                    <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                        <h5 class="uk-text-uppercase">FORM DEPO PERSEDIAAN</h5>
                    </div>                                
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="button" @click.prevent="refreshData" class="uk-button-close uk-button uk-button-default uk-button-medium" style="font-size:12px;">Refresh</button>                  
                    </div>
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                        <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                    </div>
                </div>
                <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                    <div uk-spinner="ratio : 2"></div>
                    <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                </div>
                
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>                
                
                <div class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            informasi dan data terkait depo lokasi persediaaan.
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small uk-text-uppercase" v-model="depo.depo_nama" type="text" placeholder="nama depo" required style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lokasi Gudang*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="depo.lokasi" type="text" placeholder="gedung atau lantai lokasi" required  style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi*</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea uk-form-small" v-model="depo.deskripsi" type="text" required placeholder="deskripsi depo persediaan"  style="color:black;"></textarea>
                                </div>
                            </div> 
                        </div>                            
                    </div>
                </div>  
                <div>
                    <ul uk-tab style="padding:0;margin:0;">
                        <li><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">PARAMETER DEPO</h5></a></li>
                        <li v-if="isUpdate"><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">ITEM PERSEDIAAN</h5></a></li>
                    </ul>
                    <ul class="uk-switcher" style="padding:0;margin:0;">
                        <li>
                            <div class="uk-grid-small hims-form-subpage" uk-grid>                                    
                                <div class="uk-width-1-1@s uk-grid-small" uk-grid>
                                    <div class="uk-width-1-3@m" style="margin:0;padding:0.3em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:14px;font-weight:400;">
                                                <input class="uk-checkbox" type="checkbox" value="1" v-model="depo.is_stock_adjustment"> Stock Adjustment
                                            </label>
                                        </div>
                                        <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Depo boleh melakukan penyesuaian stok item persediaan.</p>
                                    </div>

                                    <div class="uk-width-1-3@m" style="margin:0;padding:0.3em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:14px;font-weight:400;">
                                                <input class="uk-checkbox" type="checkbox" value="1" v-model="depo.is_inventory_issue"> Inventory Issue
                                            </label>
                                        </div>
                                        <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Depo boleh melakukan pengeluaran item persediaan.</p>
                                    </div>

                                    <div class="uk-width-1-3@m" style="margin:0;padding:0.3em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:14px;font-weight:400;">
                                                <input class="uk-checkbox" type="checkbox" value="1" v-model="depo.is_distribution"> Distribusi Persediaan
                                            </label>
                                        </div>
                                        <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Depo dapat melakukan proses distribusi persediaan.</p>
                                    </div>

                                    <div class="uk-width-1-3@m" style="margin:0;padding:0.3em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:14px;font-weight:400;">
                                                <input class="uk-checkbox" type="checkbox" value="1" v-model="depo.is_purchase_req"> Purchase Request
                                            </label>
                                        </div>
                                        <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Depo dapat mengajukan pembelian.</p>
                                    </div>
                
                                    <div class="uk-width-1-3@m" style="margin:0;padding:0.3em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:14px;font-weight:400;">
                                                <input class="uk-checkbox" type="checkbox" value="1" v-model="depo.is_purchase_order"> Purchase Order
                                            </label>
                                        </div>
                                        <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Depo dapat mengeluarkan PO (pembelian)</p>
                                    </div>

                                    <div class="uk-width-1-3@m" style="margin:0;padding:0.3em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:14px;font-weight:400;">
                                                <input class="uk-checkbox" type="checkbox" value="1" v-model="depo.is_purchase_receive"> Purchase Receive
                                            </label>
                                        </div>
                                        <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Depo dapat melakukan penerimaan item PO</p>
                                    </div>

                                    <div class="uk-width-1-3@m" style="margin:0;padding:0.3em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:14px;font-weight:400;">
                                                <input class="uk-checkbox" type="checkbox" value="1" v-model="depo.is_purchase_return"> Pengembalian Penerimaan PO
                                            </label>
                                        </div>
                                        <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Depo dapat mengembalikan penerimaan PO</p>
                                    </div>
                                    <div class="uk-width-1-3@m" style="margin:0;padding:0.3em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:14px;font-weight:400;">
                                                <input class="uk-checkbox" type="checkbox" value="1" v-model="depo.is_direct_purchase"> Pembelian langsung
                                            </label>
                                        </div>
                                        <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Depo dapat melakukan pembelian non PO</p>
                                    </div>
                                    <div class="uk-width-1-3@m" style="margin:0;padding:0.3em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:14px;font-weight:400;">
                                                <input class="uk-checkbox" type="checkbox" value="1" v-model="depo.is_production"> Produksi persediaan
                                            </label>
                                        </div>
                                        <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Depo boleh melakukan produksi persediaan</p>
                                    </div>
                                    <div class="uk-width-1-3@m" style="margin:0;padding:0.3em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:14px;font-weight:400;">
                                                <input class="uk-checkbox" type="checkbox" value="1" v-model="depo.is_sell"> Penjualan produk
                                            </label>
                                        </div>
                                        <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Depo boleh melakukan penjualan produk</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li v-if="isUpdate">
                            <item-depo-persediaan ref="itemdepo"></item-depo-persediaan>
                        </li>
                    </ul>
                </div>
                
            </form>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import ItemDepoPersediaan from '@/Pages/MasterData/Depo/Components/ItemDepoPersediaan.vue';

export default {
    name : 'form-depo-persediaan',
    components : {
        headerPage,
        ItemDepoPersediaan,
    },
    data() {
        return {
            isUpdate : true,
            isLoading : false,
            searchItemDepoUrl : null,
            depo : {
                client_id:null,
                depo_id:null,
                depo_nama : null,
                deskripsi : null,
                lokasi : null,
                is_gudang : false,
                is_virtual : false,
                is_stock_adjustment : false,
                is_inventory_issue : false,
                is_distribution : false,
                is_purchase_req : false,
                is_purchase_order : false,
                is_purchase_receive : false,
                is_purchase_return : false,
                is_direct_purchase : false,
                is_production : false,
                is_sell : false,
                is_aktif : true,                
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    methods : {
        ...mapActions('depo',['createDepo','updateDepo','dataDepo']),
        ...mapMutations(['CLR_ERRORS']),

        refreshData() {
            let id = this.depo.depo_id;
            this.isLoading = true;
            this.clearDepo();
            this.dataDepo(id).then((response)=> {
                if (response.success == true) {
                    this.fillDepo(response.data);
                    this.isLoading = false;
                    this.$refs.itemdepo.refreshData(response.data);
                } else {
                    alert(response.message);
                    this.isLoading = false;
                }
            })
        },

        closeModal(){
            this.CLR_ERRORS();
            this.clearDepo();
            this.$emit('closedFormDepo',true);
            return false;
        },

        submitDepo(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createDepo(this.depo).then((response) => {
                    if (response.success == true) {
                        this.isUpdate = true;
                        alert("Depo persediaan baru berhasil dibuat.");
                        this.fillDepo(response.data);
                        this.$emit('saveSucceed',true);
                        this.editDepo(response.data);
                    }
                })
            }
            else {
                this.updateDepo(this.depo).then((response) => {
                    if (response.success == true) {
                        this.isUpdate = true;
                        this.fillDepo(response.data);
                        alert("Depo persediaan berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        //this.refreshData();
                        this.$refs.itemdepo.refreshData(response.data);
                    }
                })
            }            
        },

        newDepo(data){
            this.clearDepo();
            this.isLoading = false;
            this.isUpdate = false;
        },

        editDepo(data){
            this.isLoading = true;
            this.isUpdate = true;
            this.clearDepo();                 
            this.dataDepo(data.depo_id).then((response)=> {
                if (response.success == true) {
                    this.fillDepo(response.data);
                    this.isLoading = false;
                    this.$refs.itemdepo.refreshData(data);
                } else {
                    alert(response.message);
                    this.isLoading = false;
                }
            })
        },   

        clearDepo(){
            this.depo.client_id = null;
            this.depo.depo_id = null;
            this.depo.lokasi = null;
            this.depo.depo_nama = null;
            this.depo.deskripsi = '';
            this.depo.is_gudang = false;
            this.depo.is_virtual = false;
            this.depo.is_stock_adjustment = false;
            this.depo.is_inventory_issue = false;
            this.depo.is_distribution = false;
            this.depo.is_purchase_req = false;
            this.depo.is_purchase_order = false;
            this.depo.is_purchase_receive = false;
            this.depo.is_purchase_return = false;
            this.depo.is_direct_purchase = false;
            this.depo.is_production = false;
            this.depo.is_sell = false;
            this.depo.is_aktif = true;
        },

        fillDepo(data){
            this.depo.client_id = null;
            this.depo.depo_id = data.depo_id;
            this.depo.depo_nama = data.depo_nama;
            this.depo.lokasi = data.lokasi;
            this.depo.is_stock_adjustment = data.is_stock_adjustment;
            this.depo.is_inventory_issue = data.is_inventory_issue;
            this.depo.is_distribution = data.is_distribution;
            this.depo.is_purchase_req = data.is_purchase_req;
            this.depo.is_purchase_order = data.is_purchase_order;
            this.depo.is_purchase_receive = data.is_purchase_receive;
            this.depo.is_purchase_return = data.is_purchase_return;
            this.depo.is_direct_purchase = data.is_direct_purchase;
            this.depo.is_production = data.is_production;
            this.depo.is_sell = data.is_sell;
            this.depo.deskripsi = data.deskripsi;
            this.depo.is_gudang = data.is_gudang;
            this.depo.is_virtual = data.is_virtual;
            this.depo.is_aktif = data.is_aktif;
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