<template>
    <div class="uk-container hims-form-container" style="min-height:50vh;">
        <div class="uk-grid uk-grid-small" style="padding:0 1em 0.5em 1em;">
            <div class="uk-width-auto" style="padding:0;">
                <a href="#" @click.prevent="closeModal" uk-icon="icon:arrow-left;ratio:1.5" style="display:block;width:30px;"></a>
            </div>
            <div class="uk-width-expand"  style="padding:0;margin:0;">
                <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                    <li>
                        <a href="#" @click.prevent="closeModal" class="uk-text-uppercase">
                            <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">STOCK ADJUSTMENT</h4>
                        </a> 
                    </li>
                </ul>
            </div>
        </div>        
        <div class="uk-container" style="background-color:#fff;padding:0.5em 1em 1em 1em;margin-top:1em;">
            <form action="" @submit.prevent="submitAdjustment">
                <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                    <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                        <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="closeModal"><span uk-icon="arrow-left"></span> Kembali</button>
                        <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="refreshAdjustment"><span uk-icon="refresh"></span> Refresh</button>
                        <button type="submit" class="hims-table-btn uk-width-auto"><span uk-icon="file-text"></span> Simpan</button>
                        <button type="button" class="hims-table-btn uk-width-auto" @click="removeAdjustment" :disabled="!isUpdate"><span uk-icon="trash"></span> Hapus</button>
                        <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                        <button type="button" class="hims-table-btn uk-width-auto" @click="approvalAdjustment" :disabled="!isUpdate"><span uk-icon="lock"></span> Selesai</button>
                    </div>
                </div>
                <div> 
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
                                informasi utama penyesuaian persediaan.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid>
                                <template v-if="isUpdate">
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Depo Persediaan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="penyesuaian.depo_nama" type="text" disabled style="color:black;">
                                        </div>
                                    </div>
                                </template>

                                <template v-else>   
                                    <div class="uk-width-1-2@m">
                                        <search-select
                                            :config = configSelect
                                            :searchUrl = depoUrl
                                            label = "Depo Persediaan*"
                                            placeholder = "pilih depo persediaan"
                                            captionField = "depo_nama"
                                            valueField = "depo_nama"
                                            :itemListData = depoDesc
                                            :value = "penyesuaian.depo_nama"
                                            v-on:item-select = "depoSelected"
                                        ></search-select>
                                    </div>
                                </template>                                
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="penyesuaian.catatan" type="text" required>{{penyesuaian.catatan}}</textarea>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </form>
            <div class="uk-grid-small hims-form-subpage" uk-grid>
                <div class="uk-width-1-1@m" style="padding:0 0.5em 0 0.5em;">
                    <h5 style="color:indigo;padding:0;margin:0;">STOCK ADJUSTMENT ITEMS
                        <!-- <span style="font-size:12px;font-style:italic;padding:0;margin:0;">item persediaan yang diubah jumlah.</span> -->
                    </h5>
                    <p style="font-size:11px; font-style: italic; padding:0.2em;margin:0;">
                        Data jumlah awal dan akhir penyesuaian bisa jadi berubah dan tidak sesuai dengan riil, seiring pergerakan stok di depo terkait. Data utama hanya jumlah penyesuaian.
                    </p>                    
                </div>                    
                <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">     
                    <div class="uk-width-1-1">
                        <table class="uk-table hims-table uk-table-responsive uk-box-shadow-small">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ITEM</th>
                                    <th style="width:60px;text-align:center;">JML.STOK</th>
                                    <th style="width:60px;text-align:center;">SATUAN</th>
                                    <th style="width:60px;text-align:center;">PENYESUAIAN</th>
                                    <th style="width:60px;text-align:center;">JML.AKHIR</th>
                                    <th style="width:25px;text-align:center;">...</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="itm in penyesuaian.items" style="border-top:1px solid #eee;" :class=" !itm.is_aktif ? 'data-inactive' : ''">
                                    <td style="max-width:140px;font-weight:500;">
                                        {{itm.produk_id}}
                                    </td>
                                    <td style="font-weight:500;">
                                        {{itm.produk_nama}}
                                    </td>
                                    <td style="width:60px;text-align:center;">{{itm.total_awal}}</td>
                                    <td style="width:60px;text-align:center;">{{itm.satuan}}</td>
                                    <td style="width:60px;text-align:center;padding:0.5em 0.2em 0.5em 0.2em;">
                                        <input class="uk-input uk-form-small" v-model="itm.jml_penyesuaian" type="number" style="text-align:center;" @change="calculateRowJmlAkhir(itm)">
                                    </td>
                                    <td style="width:60px;text-align:center;">{{itm.total_akhir}}</td>
                                    <td style="width:25px;text-align:center;padding:1.1em 0.2em 0.5em 0.2em;"><input class="uk-checkbox" v-model="itm.is_aktif" type="checkbox" @change="addedItemAktifasi(itm)"></td>
                                </tr>
                                <tr style="padding:0;margin:0;">
                                    <td colspan="2" style="padding:0.5em 0.2em 0.5em 0.2em;">
                                        <search-select
                                            :config = configSelect
                                            :searchUrl = produkUrl
                                            label = ""
                                            placeholder = "item persediaan"
                                            captionField = "produk_nama"
                                            valueField = "produk_nama"
                                            :itemListData = produkDesc
                                            :value = "addedItem.produk_nama"
                                            v-on:item-select = "produkItemSelected"
                                        ></search-select>
                                    </td>
                                    <td style="width:60px;padding:0.5em 0.2em 0.5em 0.2em;">
                                        <input class="uk-input uk-form-small" v-model="addedItem.total_awal" type="string" style="text-align:center;" disabled>
                                    </td>
                                    <td style="width:60px;padding:0.5em 0.2em 0.5em 0.2em;">
                                        <input class="uk-input uk-form-small" v-model="addedItem.satuan" type="string" style="text-align:center;" disabled>
                                    </td>
                                    <td style="width:60px;padding:0.5em 0.2em 0.5em 0.2em;">
                                        <input class="uk-input uk-form-small" v-model="addedItem.jml_penyesuaian" type="number" style="text-align:center;" @change="calculateJmlAkhir">
                                    </td>       
                                    <td style="width:60px;padding:0.5em 0.2em 0.5em 0.2em;">
                                        <input class="uk-input uk-form-small" v-model="addedItem.total_akhir" type="string" style="text-align:center;" disabled>
                                    </td>                             
                                    <td style="width:25px;text-align:center;padding:0.2em 0 0 0;">
                                        <button type="button" @click.prevent="addItemAdjustment" class="uk-button-small" style="background-color:#eee; border:none; border-radius:5px; padding:0.2em 0.5em 0.2em 0.5em;"><span uk-icon="plus-circle"></span></button>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-adjustment', 
    components : {
        SearchSelect, SearchList
    },
    data() {
        return {
            isLoading : true,
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            depoDesc : [
                { colName : 'depo_nama', labelData : '', isTitle : true },
                { colName : 'depo_id', labelData : '', isTitle : false },
            ],
            produkDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],
            isUpdate : true,
            produkUrl : '',
            depoUrl : '/users/depos',

            penyesuaian : {
                client_id : null,
                sa_id : null,
                jenis_penyesuaian : null,
                depo_id : null,
                depo_nama : null,
                catatan : null,
                tgl_sa : null,
                tgl_selesai : null,
                unit_id : null,
                status : null,
                is_aktif : true,
                items : [],
            },

            addedItem : {
                sa__detail_id : null,
                sa_id : null,
                depo_id : null,
                produk_id : null,
                produk_nama : null,
                satuan : null,
                total_awal : null,
                jml_penyesuaian : null,
                total_akhir : null,
                status : null,
                is_aktif : true,
                client_id : null,
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
    },
    mounted() {},    
    methods : {
        ...mapActions('adjustment',['createAdjustment','updateAdjustment','dataAdjustment','approveAdjustment','deleteAdjustment']),
        ...mapActions('refProduk',['listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {

        },

        closeModal(){
            this.clearAdjustment();
            this.$emit('closed',true);
        },

        depoSelected(data) {
            if(data) {
                this.penyesuaian.depo_id = data.depo_id;
                this.penyesuaian.depo_nama = data.depo_nama;
                this.produkUrl = `/depos/${data.depo_id}/products`;
                this.penyesuaian.items = [];
            }
        },

        produkItemSelected(data) {
            this.addedItem.sa__detail_id = null;
            this.addedItem.sa_id = null;
            this.addedItem.produk_id = null;
            this.addedItem.produk_nama = null;
            this.addedItem.depo_id = null;
            this.addedItem.depo_nama = null;
            this.addedItem.satuan = null;
            this.addedItem.total_awal = null;
            this.addedItem.jml_penyesuaian = null;
            this.addedItem.total_akhir = null;
            this.addedItem.is_aktif = true;

            if(data) {
                if(!this.penyesuaian.items.find(item => item.produk_id === data.produk_id)) {
                    this.addedItem.sa__detail_id = null;
                    this.addedItem.produk_id = data.produk_id;
                    this.addedItem.produk_nama = data.produk_nama;
                    this.addedItem.depo_id = this.penyesuaian.depo_id;
                    this.addedItem.depo_nama = this.penyesuaian.depo_nama;
                    
                    this.addedItem.satuan = data.satuan;
                    this.addedItem.total_awal = data.jml_total;
                    this.addedItem.jml_penyesuaian = 0;
                    this.addedItem.total_akhir = 0;                    
                    this.addedItem.is_aktif = true;
                }
                else {
                    alert('item sudah ada dalam daftar');
                    return false;
                }   
            }
        },

        submitAdjustment(){ 
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createAdjustment(this.penyesuaian).then((response) => {
                    if (response.success == true) {
                        alert("Data penyesuaian baru berhasil dibuat.");
                        this.fillAdjustment(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }
            else {
                this.updateAdjustment(this.penyesuaian).then((response) => {
                    if (response.success == true) {
                        this.fillAdjustment(response.data);
                        alert("Data penyesuaian berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }       
        },

        approvalAdjustment() {
            this.CLR_ERRORS();
            if(this.penyesuaian.status !== 'RENCANA') {
                alert('hanya data dengan status RENCANA yang bisa diproses persetujuan.'); return false;
            } 

            if(confirm(`Proses ini akan menyesuaikan jumlah persediaan di depo ${this.penyesuaian.depo_nama}. Lanjutkan ?`)){
                this.updateAdjustment(this.penyesuaian).then((response) => {
                    if (response.success == true) {
                        this.fillAdjustment(response.data);
                        this.saveApproveAdjustment(response.data.sa_id);
                    }
                })
            }
        },

        saveApproveAdjustment(id){
            this.CLR_ERRORS();
            this.approveAdjustment(id).then((response) => {
                if (response.success == true) {
                    alert("persetujuan penyesuaian berhasil dibuat.");
                    this.closeModal();
                }
            })
        },

        newAdjustment(){
            this.clearAdjustment();
            this.isUpdate = false;
            this.isLoading = false;
        },

        editAdjustment(data){
            this.clearAdjustment();
            this.isLoading = true;
            this.dataAdjustment(data.sa_id).then((response)=>{
                if (response.success == true) {
                    this.fillAdjustment(response.data);
                    this.isUpdate = true;
                } 
                else { alert(response.message); }
                this.isLoading = false;
            })
        },   

        refreshAdjustment(){
            this.CLR_ERRORS();
            if(this.isUpdate) {
                this.isLoading = true;
                this.dataAdjustment(this.penyesuaian.sa_id).then((response)=>{
                    if (response.success == true) {
                        this.fillAdjustment(response.data);
                        this.isUpdate = true;
                    } 
                    else { alert(response.message); }
                    this.isLoading = false;
                })
            }
            else {
                this.newAdjustment();
            }
        },

        clearAdjustment(){
            this.penyesuaian.client_id = null;
            this.penyesuaian.sa_id = null;
            this.penyesuaian.depo_id = null;
            this.penyesuaian.depo_nama = null;
                
            this.penyesuaian.tgl_sa = null;
            this.penyesuaian.tgl_selesai = null;                
            this.penyesuaian.status = null;
            this.penyesuaian.catatan = null;
            this.penyesuaian.is_aktif = true;
            this.penyesuaian.items = [];
        },

        fillAdjustment(data){
            this.penyesuaian.client_id = null;
            this.penyesuaian.sa_id = data.sa_id;
            this.penyesuaian.depo_id = data.depo_id;
            this.penyesuaian.depo_nama = data.depo_nama;
                
            this.penyesuaian.tgl_sa = data.tgl_sa;
            this.penyesuaian.tgl_selesai = data.tgl_selesai;                
            this.penyesuaian.status = data.status;
            this.penyesuaian.catatan = data.catatan;
            this.penyesuaian.is_aktif = true;
            this.penyesuaian.items = data.items;

            this.produkUrl = `/depos/${data.depo_id}/products`;
        },

        calculateJmlAkhir(){
            this.addedItem.total_akhir = this.addedItem.total_awal + this.addedItem.jml_penyesuaian;
        },

        calculateRowJmlAkhir(data) {
            data.total_akhir = data.total_awal + data.jml_penyesuaian;
        },

        addItemAdjustment() {
            if(this.addedItem.produk_nama == "" || this.addedItem.produk_nama == null || this.addedItem.jml_penyesuaian == null || this.addedItem.jml_penyesuaian == 0) {
                alert("Data tidak dapat ditambahkan. ada data yang masih kurang.");
                return false;
            }
            this.addedItem.total_akhir = this.addedItem.total_awal + this.addedItem.jml_penyesuaian;
            let addedVal = JSON.parse(JSON.stringify(this.addedItem));
            this.penyesuaian.items.push(addedVal);
            this.clearAddedItem();
        },

        clearAddedItem(){
            this.addedItem.sa__detail_id = null;
            this.addedItem.sa_id = null;
            this.addedItem.depo_id = null;
            this.addedItem.depo_nama = null;
            this.addedItem.produk_id = null;
            this.addedItem.produk_nama = null;
            this.addedItem.satuan = null;
            
            this.addedItem.total_awal = 0;
            this.addedItem.jml_penyesuaian = 0;
            this.addedItem.total_akhir = 0;
            
            this.addedItem.status = 'RENCANA';
            this.addedItem.is_aktif = true;
        },

        addedItemAktifasi(data){
            this.penyesuaian.items = this.penyesuaian.items.filter(item => { return item['is_aktif'] == true || item['sa__detail_id'] !== null });
        },

        removeAdjustment() {
            if(this.penyesuaian.status !== 'RENCANA') {
                alert('penyesuaian yang sudah/sedang berjalan tidak dapat dibatalkan.');
                return false;
            }
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus rencana / data sementara penyesuaian ini. Lanjutkan ?`)){
                this.deleteAdjustment(this.penyesuaian.sa_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.closeModal();
                    }
                    else { alert (response.message) }
                });
            };
        },
    }
}
</script>

<style>
/* tr.data-inactive td {
    text-decoration: line-through;
    font-style: italic;
} */
/* .uk-input, .uk-textarea, .uk-checkbox, .uk-select {
    border: 1px solid #999;
    color: black;
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

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
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