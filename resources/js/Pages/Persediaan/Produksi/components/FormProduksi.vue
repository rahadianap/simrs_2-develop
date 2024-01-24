<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalProduksiItem" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div style="margin:0;padding:0;">    
                <form action="" @submit.prevent="submitProduksi">
                    <div class="uk-card-default uk-grid-small" uk-grid style="position:sticky; top:-50px; padding:0;margin:0; border-top:0px solid #cc0202; height: 70px;">
                        <div class="uk-width-expand" style="padding:1em;">
                            <h3 style="color:black; font-weight: 400;">DATA PRODUKSI</h3>
                        </div>
                        <div class="uk-width-auto" style="padding:0.8em;">
                            <button class="uk-button" @click.prevent="closeModal" type="button" style="color:#333; font-weight: 500;">TUTUP</button>
                            <button class="uk-button" type="submit" style="color:#fff; font-weight: 500;background-color: #cc0202;margin-left:5px;">SIMPAN</button>
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
                                    informasi utama produksi persediaan.
                                </p>
                            </div>
                            <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                                <div class="uk-width-1-1@m uk-grid-small" uk-grid>
                                    <template v-if="isUpdate">
                                        <div class="uk-width-1-1@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Depo Persediaan**</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="produksi.depo_nama" type="text" disabled style="color:black;">
                                            </div>
                                        </div>
                                        <div v-if="isUpdate" class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Produk Hasil*</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="produksi.produk_hasil" type="text" disabled style="color:black;">
                                            </div>
                                        </div>
                                    </template>

                                    <template v-else>
                                        <div class="uk-width-1-1@m">
                                            <search-select
                                                :config = configSelect
                                                :searchUrl = depoUrl
                                                label = "Depo Persediaan*"
                                                placeholder = "depo persediaan"
                                                captionField = "depo_nama"
                                                valueField = "depo_nama"
                                                :itemListData = depoDesc
                                                :value = "produksi.depo_nama"
                                                v-on:item-select = "depoSelected"
                                            ></search-select>
                                        </div>                                
                                        
                                        <div class="uk-width-1-2@m">
                                            <search-select
                                                :config = configSelect
                                                :searchUrl = produkUrl
                                                label = "Produk Hasil*"
                                                placeholder = "item hasil produksi"
                                                captionField = "produk_nama"
                                                valueField = "produk_nama"
                                                :itemListData = produkDesc
                                                :value = "produksi.produk_nama"
                                                v-on:item-select = "produkSelected"
                                            ></search-select>
                                        </div>
                                    </template>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jml.Hasil</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" style="color:black;text-align: center;" v-model="produksi.jml_hasil" type="number">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Satuan</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" style="color:black;text-align: center;" v-model="produksi.satuan" type="text" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-1@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="produksi.catatan" type="text" required>{{produksi.catatan}}</textarea>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </form>
                <div class="uk-grid-small hims-form-subpage" uk-grid>
                    <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">ITEM BAHAN PRODUKSI
                            <span style="font-size:12px;font-style:italic;padding:0;margin:0 0 0 0.3em;">item (bahan) produksi persediaan</span>
                        </h5>
                    </div>                    
                    <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">     
                        <div class="uk-width-1-1">
                            <table class="uk-table hims-table uk-table-responsive uk-box-shadow-small">
                                <thead>
                                    <tr>
                                        <th style="width:180px;">ID</th>
                                        <th>ITEM BAHAN</th>
                                        <th style="text-align:center;">SATUAN</th>
                                        <th style="width:80px;text-align:center;">JML</th>
                                        <th style="width:50px;text-align:center;">...</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="padding:0;margin:0;">
                                        <td colspan="2" style="padding:0.5em 0.2em 0.5em 0.2em;">
                                            <search-select
                                                :config = configSelect
                                                :searchUrl = produkUrl
                                                label = ""
                                                placeholder = "item hasil produksi"
                                                captionField = "produk_nama"
                                                valueField = "produk_nama"
                                                :itemListData = produkDesc
                                                :value = "addedItem.produk_nama"
                                                v-on:item-select = "produkItemSelected"
                                            ></search-select>
                                        </td>
                                        <!-- <td style="width:180px;padding:0.5em 0.2em 0.5em 0.2em;">
                                            <search-list
                                                :config = configSelect
                                                :dataLists = satuanProdukRefs.json_data
                                                label = ""
                                                placeholder = ""
                                                captionField = "value"
                                                valueField = "value"
                                                :detailItems = refDesc
                                                :value = "addedItem.satuan"
                                                v-on:item-select = "satuanItemSelected"
                                            ></search-list>
                                        </td> -->
                                        <td style="width:100px;padding:0.5em 0.2em 0.5em 0.2em;">
                                            <input class="uk-input uk-form-small" v-model="addedItem.satuan" type="text" disabled style="text-align:center;color:black;">
                                        </td>
                                        <td style="width:80px;padding:0.5em 0.2em 0.5em 0.2em;">
                                            <input class="uk-input uk-form-small" v-model="addedItem.jml_bahan" type="number" style="text-align:center;">
                                        </td>
                                        <td style="width:25px;text-align:center;padding:0.2em 0 0 0;">
                                            <button type="button" @click.prevent="addItemProduksi" class="uk-button-small" style="background-color:#eee; border:none; border-radius:5px; padding:0.2em 0.5em 0.2em 0.5em;"><span uk-icon="plus-circle"></span></button>
                                        </td>
                                    </tr>
                                    <tr v-for="itm in produksi.items" style="border-top:1px solid #eee;" :class=" !itm.is_aktif ? 'data-inactive' : ''">
                                        <td style="font-weight: 500;">{{itm.produk_id}}</td>
                                        <td>{{itm.produk_nama}}</td>
                                        <td style="width:180px;text-align:center;">{{itm.satuan}}</td>
                                        <td style="width:80px;text-align:center;padding:0.5em 0.2em 0.5em 0.2em;">
                                            <input class="uk-input uk-form-small" v-model="itm.jml_bahan" type="number" style="text-align:center;">
                                        </td>
                                        <td style="width:25px;text-align:center;padding:1.3em 0.2em 0.5em 0.2em;"><input class="uk-checkbox" v-model="itm.is_aktif" type="checkbox" @change="addedItemAktifasi(itm)"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
    name : 'form-produksi', 
    components : { SearchSelect, SearchList },
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
            refDesc : [
                { colName : 'value', labelData : '', isTitle : true },
                { colName : 'text', labelData : '', isTitle : false },
            ], 
            isUpdate : true,
            isEdit : false,
            produkUrl : '',
            produkHasilUrl : '',
            depoUrl : '/users/depos',

            produksi : {
                client_id : null,
                produksi_id : null,
                depo_id : null,
                depo_nama : null,
                produk_hasil_id : null,
                produk_hasil : null,
                jml_hasil : null,
                satuan : null,
                tgl_produksi : null,
                tgl_selesai : null,
                status : null,
                catatan : null,
                is_aktif : true,
                items : [],
            },
            addedItem : {
                produksi_detail_id : null,
                produk_id : null,
                produk_nama : null,
                depo_id : null,
                satuan : null,
                jml_hasil : 0,
                jml_bahan : 1,
                is_aktif : true,                
                is_hasil : false,
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
    
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('produksi',['createProduksi','updateProduksi','dataProduksi','approveProduksi']),
        ...mapActions('refProduk',['listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let param = {per_page:'ALL'};
            this.listRefProduk({per_page:'ALL'});
        },

        closeModal(){
            this.clearProduksi();
            this.$emit('formProduksiClosed',true);
            UIkit.modal('#modalProduksiItem').hide();
        },

        depoSelected(data) {
            if(data) {
                this.produksi.depo_id = data.depo_id;
                this.produksi.depo_nama = data.depo_nama;
                this.produkUrl = `/depos/${data.depo_id}/products`;
                this.produkHasilUrl = `/items/productions/results/${data.depo_id}`;
            }
        },

        produkSelected(data) {
            this.produksi.produk_hasil_id = null;
            this.produksi.produk_hasil = null;
            this.produksi.satuan = null;
            this.produksi.jml_hasil = 1;
            if(data) {
                this.produksi.produk_hasil_id = data.produk_id;
                this.produksi.produk_hasil = data.produk_nama;
                this.produksi.satuan = data.satuan;
            }
        },

        produkItemSelected(data) {
            this.addedItem.produksi_detail_id = null;
            this.addedItem.produk_id = null;
            this.addedItem.produk_nama = null;
            this.addedItem.depo_id = null;
            this.addedItem.satuan = null;
            this.addedItem.is_hasil = false;
            this.addedItem.jml_hasil = 0;
            this.addedItem.jml_bahan = null;
            this.addedItem.is_aktif = true;
            if(data) {
                if(data.produk_id == this.produksi.produk_hasil_id) {
                    alert('Item bahan tidak boleh sama dengan item hasil');
                    return false;
                }
                else {
                    if(!this.produksi.items.find(item => item.produk_id === data.produk_id)) {
                        this.addedItem.produksi_detail_id = null;
                        this.addedItem.produk_id = data.produk_id;
                        this.addedItem.produk_nama = data.produk_nama;
                        this.addedItem.depo_id = this.produksi.depo_id;
                        this.addedItem.satuan = data.satuan;
                        this.addedItem.jml_hasil = 0;
                        this.addedItem.jml_bahan = 1;
                        this.addedItem.is_aktif = true;
                    }
                    else {
                        alert('item sudah ada dalam daftar');
                        return false;
                    }   
                }
            }
        },

        satuanSelected(data) {
            if(data) { this.produksi.satuan = data.value; }
        },

        satuanItemSelected(data) {
            if(data) { this.addedItem.satuan = data.value; }
        },

        submitProduksi(){            
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createProduksi(this.produksi).then((response) => {
                    if (response.success == true) {
                        alert("Data produksi baru berhasil dibuat.");
                        this.fillProduksi(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeModal();
                    }
                })
            }
            else {
                this.updateProduksi(this.produksi).then((response) => {
                    if (response.success == true) {
                        this.fillProduksi(response.data);
                        alert("Data produksi berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeModal();
                    }
                })
            }            
        },

        newProduksi(){
            this.listRefProduk({per_page:'ALL'});
            this.clearProduksi();
            this.isUpdate = false;
            this.isLoading = false;
            UIkit.modal('#modalProduksiItem').show();    
        },

        editProduksi(data){
            this.clearProduksi();
            this.listRefProduk({per_page:'ALL'});
            this.produkUrl = `/depos/${data.depo_id}/products`;
            this.isLoading = true;
            this.dataProduksi(data.produksi_id).then((response)=>{
                if (response.success == true) {
                    this.fillProduksi(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#modalProduksiItem').show();
                } 
                else { alert(response.message); }
                this.isLoading = false;
            })
        },   

        clearProduksi(){
            this.produksi.client_id = null;
            this.produksi.produksi_id = null;
            this.produksi.produk_hasil_id = null;
            this.produksi.produk_hasil = null;
            this.produksi.satuan = null;
            this.produksi.jml_hasil = null;            
            this.produksi.depo_id = null;
            this.produksi.depo_nama = null;
            this.produksi.tgl_produksi = null;
            this.produksi.tgl_selesai = null;
            this.produksi.status = null;
            this.produksi.catatan = null;
            this.produksi.is_aktif = true;
            this.produksi.items = [];
        },

        fillProduksi(data){
            this.produksi.client_id = null;
            this.produksi.produksi_id = data.produksi_id;
            this.produksi.produk_hasil_id = data.produk_hasil_id;
            this.produksi.produk_hasil = data.produk_hasil;
            this.produksi.satuan = data.satuan;
            this.produksi.jml_hasil = data.jml_hasil;            
            this.produksi.depo_id = data.depo_id;
            this.produksi.depo_nama = data.depo_nama;
            this.produksi.tgl_produksi = data.tgl_produksi;
            this.produksi.tgl_selesai = data.tgl_selesai;
            this.produksi.status = data.status;
            this.produksi.catatan = data.catatan;
            this.produksi.is_aktif = data.is_aktif;

            this.produkUrl = `/depos/${data.depo_id}/products`;
            this.produksi.items = data.items;
        },

        addItemProduksi() {
            if(this.addedItem.produk_nama == "" || this.addedItem.produk_nama == null || this.addedItem.jml_bahan == null || this.addedItem.jml_bahan == 0) {
                alert("Data tidak dapat ditambahkan. ada data yang masih kurang.");
                return false;
            }
            let addedVal = JSON.parse(JSON.stringify(this.addedItem));
            this.produksi.items.push(addedVal);
            this.clearAddedItem();
        },

        clearAddedItem(){
            this.addedItem.produksi_detail_id = null;
            this.addedItem.produk_id = null;
            this.addedItem.produk_nama = null;
            this.addedItem.depo_id = null;
            this.addedItem.satuan = null;
            this.addedItem.jml_hasil = 0;
            this.addedItem.jml_bahan = null;
            this.addedItem.is_aktif = true;
            this.addedItem.is_hasil = false;
        },

        addedItemAktifasi(data){
            this.produksi.items = this.produksi.items.filter(item => { return item['is_aktif'] == true || item['produksi_detail_id'] !== null });
        },

        validasiData(){
            if(confirm(`Proses ini akan mengakhiri proses produksi. Data sudah tidak dapat diubah. Lanjutkan ?`)){
                if(this.isUpdate) {
                    this.updateProduksi(this.produksi).then((response) => {
                        if (response.success == true) {
                            this.approvalProduksi();
                        }
                    })
                }                             
            };
        },

        approvalProduksi(){
            this.approveProduksi(this.produksi.produksi_id).then((response) => {
                if (response.success == true) {
                    alert(response.message);
                    this.closeModal();
                }
                else { alert (response.message) }
            });
        },
    }
}
</script>

<style>
/* tr.data-inactive td {
    text-decoration: line-through;
    font-style: italic;
}
.uk-input, .uk-textarea, .uk-checkbox, .uk-select {
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