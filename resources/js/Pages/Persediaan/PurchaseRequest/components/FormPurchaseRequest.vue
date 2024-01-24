<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formPurchaseRequest" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitPR" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">PERMINTAAN PEMBELIAN PERSEDIAAN</h5>
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
                        
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PERMINTAAN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                data permintaan pembelian.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>              
                            
                            <div class="uk-width-1-2@m">
                                <search-list
                                    :config = configSelect
                                    :dataLists = userUnitLists
                                    label = "Unit*"
                                    placeholder = "pilih unit pelayanan"
                                    captionField = "unit_nama"
                                    valueField = "unit_nama"
                                    :detailItems = unitDesc
                                    :value = "req.unit_nama"
                                    v-on:item-select = "unitSelected">
                                </search-list>
                            </div>
                            <div class="uk-width-1-2@m">
                                <search-list
                                    :config = configSelect
                                    :dataLists = userDepoLists
                                    label = "Depo Persediaan*"
                                    placeholder = "pilih depo persediaan"
                                    captionField = "depo_nama"
                                    valueField = "depo_nama"
                                    :detailItems = depoDesc
                                    :value = "req.depo_nama"
                                    v-on:item-select = "depoSelected" >
                                </search-list>
                            </div>
                            <div class="uk-width-1-4@m uk-hidden">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Permintaan</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="req.tgl_pr" type="date" placeholder="tanggal permintaan">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Dibutuhkan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="req.tgl_kebutuhan" type="date" placeholder="tanggal dibutuhkan" required>
                                </div>
                            </div>
                            <div class="uk-width-3-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small uk-text-uppercase" v-model="req.catatan" type="text" required>
                                </div>
                            </div>
                            <div v-if="isUpdate" class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                <label style="padding:0;margin:0;font-size:14px;color:black;"><input class="uk-checkbox" type="checkbox" v-model="req.is_aktif" style="margin-left:5px;" :disabled="isUpdate"> Data req aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DETAIL PERMINTAAN
                                <span style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    data detail permintaan pembelian dalam satuan kemasan beli
                                </span>
                            </h5>
                            
                        </div>
                        <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                            <table class="uk-table hims-table uk-table-striped">
                                <thead>
                                    <th>Jenis Produk</th>
                                    <th>Nama Produk</th>
                                    <th style="text-align: center;">Satuan</th>
                                    <th style="text-align: center;">Jumlah</th>  
                                    <th style="text-align: center;width:30px;">...</th>
                                </thead>
                                <tbody>    
                                    <tr>
                                        
                                        <td colspan="2" style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:200px;">
                                            <div class="uk-form-controls">
                                                <search-select
                                                    :config = configSelect
                                                    :searchUrl = urlSearch
                                                    placeholder = "Paracetamol 500mg, Kertas HVS, etc..."
                                                    captionField = "produk_nama"
                                                    valueField = "produk_id"
                                                    :itemListData = produkDesc
                                                    :value = detailReq.produk_id
                                                    v-on:item-select = "produkSelected"
                                                ></search-select>
                                            </div>                        
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;width:80px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" v-model="detailReq.satuan" disabled style="text-align: center;color:black;">
                                            </div>                        
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;width:80px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="number" v-model="detailReq.jml_satuan" style="text-align: center;">
                                            </div>                        
                                        </td>
                                        
                                        <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                            <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addDetailPR" style="padding:0;margin:0;color:blue;display:inline-block;" :disabled="isUpdate"></a>
                                        </td>
                                    </tr>

                                    <tr v-for="dt in req.detail_req" v-bind:class="dt.is_aktif != true ? 'inaktif':'' ">
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;width:80px;font-weight:500;">{{dt.jenis_produk}}</td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.produk_nama}}</td>
                                        <td style="width:80px;padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;">{{dt.satuan}}</td>
                                        <td style="width:80px;padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;">
                                            <input class="uk-input uk-form-small" type="number" v-model="dt.jml_satuan" style="text-align: center;">
                                        </td>
                                        <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;width:30px;">
                                            <input class="uk-checkbox" v-model="dt.is_aktif" type="checkbox" @change="addedItemAktifasi(dt)">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
    name : 'form-purchase-request', 
    components : { SearchSelect,SearchList }, 
    data() {
        return {
            configSelect : {
                disabled : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],
            depoDesc : [
                { colName : 'depo_nama', labelData : '', isTitle : true },
                { colName : 'depo_id', labelData : '', isTitle : false },
            ],
            produkDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],

            isUpdate : false,
            req : {
                client_id:null,
                pr_id:null,
                status : 'DRAFT',
                tgl_pr: new Date().toISOString().slice(0,10),
                tgl_kebutuhan: null,
                unit_id: null,
                unit_nama: null,
                depo_id: null,
                depo_nama: null,
                catatan:null,
                is_aktif : true,
                detail_req : []
            },
            detailReq : {
                pr_detail_id: null,
                pr_id : null,
                jenis_produk : null,
                produk_id : null,
                produk_nama : null,
                jml_satuan : null,
                satuan: null,
                hna : 0,
                subtotal : 0,
                is_aktif : true,
            },
            urlSearch : '',
            arrItemDel_data: [],
            userUnitLists : null,
            userDepoLists : null,            
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('unitPelayanan',['unitLists']),
        ...mapGetters('refProduk',['jenisProdukRefs','isRefProdukExist']),
    },

    mounted() {
        this.init();
    },
    
    methods : {
        ...mapActions('purchaseRequest',['createPR','dataPR','updatePR']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('refProduk',['updateRefProduk','listRefProduk']),
        
        ...mapActions('users',['listUserUnit']),
        
        ...mapMutations(['CLR_ERRORS']),

        init() {
            this.CLR_ERRORS();
            if(!this.isRefProdukExist) { 
                this.listRefProduk(); 
            };

            this.listUserUnit({'per_page':'ALL'}).then((response) => {
                if (response.success == true) {
                    this.userUnitLists = response.data.data;
                }
            })
        },

        closeModal(){
            this.$emit('closed',true);
            this.clearProduk();
            UIkit.modal('#formPurchaseRequest').hide();
            return false;
        },

        unitSelected(data){
            if(data) {
                
                this.req.unit_id = data.unit_id;   
                this.req.unit_nama = data.unit_nama;
                
                this.req.depo_id = null;
                this.req.depo_nama = null;  
                this.userDepoLists = [];
                
                if(data.depos) {
                    this.userDepoLists = data.depos;
                }
            }
        },

        depoSelected(data){
            if(data) {
                this.req.depo_id = data.depo_id;
                this.req.depo_nama = data.depo_nama;     
                this.urlSearch = `/depos/${data.depo_id}/products`;           
            }
        },

        produkSelected(data){
            if(data) {
                this.detailReq.produk_id = data.produk_id;
                this.detailReq.produk_nama = data.produk_nama;
                this.detailReq.satuan = data.satuan;      
                this.detailReq.jenis_produk = data.jenis_produk;
                this.detailReq.is_aktif = true;
                   
            }
        },

        viewPR(pr_id){
            this.CLR_ERRORS();
            this.clearProduk();            
            this.dataPR(pr_id).then((response)=>{
                if (response.success == true) {
                    this.req.client_id = null;
                    this.req.pr_id = response.data[0].pr_id;
                    this.req.tgl_pr = response.data[0].tgl_pr;
                    this.req.tgl_kebutuhan = response.data[0].tgl_kebutuhan;
                    this.req.unit_id = response.data[0].unit_id;
                    this.req.unit_nama = response.data[0].unit_nama;
                    this.req.depo_id = response.data[0].depo_id;
                    this.req.depo_nama = response.data[0].depo_nama;
                    this.req.status = response.data[0].status;
                    this.req.catatan = response.data[0].catatan;
                    this.req.is_aktif = true;
                    this.configSelect.disabled = true;
                    this.req.detail_req = [];
                    if(response.data.length > 0){
                        for(let i=0;i<=response.data.length-1;i++){
                            this.req.detail_req.push({
                                "jml_satuan" : response.data[i]['jml_satuan'],
                                "satuan" : response.data[i]['pr_satuan_id'],
                                "produk_id" : response.data[i]['produk_id'],
                                "produk_nama" : response.data[i]['produk_nama'],
                                "jenis_produk" : response.data[i]['jenis_produk'],
                            });
                        }
                    }
                    this.isUpdate = true;
                    UIkit.modal('#formPurchaseRequest').show();
                } else {
                    alert(response.message);
                }
            })
        },

        submitPR(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createPR(this.req).then((response) => {
                    if (response.success == true) {
                        alert(" data permintaan baru berhasil dibuat.");
                        this.clearProduk();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        UIkit.modal('#formPurchaseRequest').hide();
                    }
                })
            }   
            else {
                this.updatePR(this.req).then((response) => {
                    if (response.success == true) {
                        alert(" data permintaan berhasil diubah.");
                        this.clearProduk();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeModal();
                        UIkit.modal('#formPurchaseRequest').hide();
                    }
                })
            }         
        },

        newPurchaseRequest(){
            this.configSelect.disabled = false;
            this.clearProduk();
            this.isUpdate = false;
            this.userDepoLists = [];
            UIkit.modal('#formPurchaseRequest').show();
        },  

        editPurchaseRequest(id){
            this.CLR_ERRORS();
            this.clearProduk();
            this.dataPR(id).then((response) => {
                if (response.success == true) {
                    this.configSelect.disabled = true;
                    this.isUpdate = true;
                    this.fillPR(response.data);
                    UIkit.modal('#formPurchaseRequest').show();
                }
                else {
                    alert('ada kesalahan dalam pengambilan data.');
                }
            })
        },

        fillPR(data){
            // console.log(data);
            this.req.client_id = data.client_id;
            this.req.pr_id = data.pr_id;
            this.req.tgl_pr = data.tgl_pr;
            this.req.tgl_kebutuhan = data.tgl_kebutuhan;
            
            this.req.unit_id = data.unit_id;
            this.req.depo_id = data.unit_id;
            this.req.unit_nama = data.unit_nama;
            this.req.depo_nama = data.depo_nama;
            
            this.req.status = data.status;
            this.req.catatan = data.catatan;
            this.req.is_aktif = data.is_aktif;
            this.req.detail_req = data.detail_req;            
        },

        clearProduk(){
            this.req.client_id = null;
            this.req.pr_id = null;
            this.req.tgl_pr = null;
            this.req.tgl_kebutuhan = null;

            this.req.unit_id = null;
            this.req.unit_nama = null;
            this.req.depo_id = null;
            this.req.depo_nama = null;
            
            this.req.status = null;
            this.req.catatan = null;
            this.req.is_aktif = true;
            this.req.detail_req = [];

            this.clearDetailItem();
        },

        addDetailPR(){
            let exist = this.req.detail_req.find(item => item.produk_id === this.detailReq.produk_id);
            if(exist) { 
                alert('Data sudah ada dalam daftar.'); 
                return false; 
            }

            if(this.detailReq.produk_id == null || this.detailReq.produk_id == '' || this.detailReq.jml_satuan == null || this.detailReq.jml_satuan == '' || this.detailReq.jenis_produk == null || this.detailReq.jenis_produk == '' ){
                alert('Data tidak lengkap.');
                return false;
            }
            else {

                this.req.detail_req.push(JSON.parse(JSON.stringify(this.detailReq)));
                this.clearDetailItem();
            }
        },

        clearDetailItem(){
            this.detailReq.pr_detail_id = null;
            this.detailReq.pr_id = null;
            this.detailReq.jenis_produk = null;
            this.detailReq.produk_id = null;
            this.detailReq.produk_nama = null;
            this.detailReq.jml_satuan = null;
            this.detailReq.satuan = null;
            this.detailReq.harga = null;
            this.detailReq.subtotal = null;
            this.detailReq.is_aktif = true;
        },
        
        addedItemAktifasi(data){
            this.req.detail_req = this.req.detail_req.filter(item => { return item['is_aktif'] == true || item['pr_detail_id'] !== null });
        },
    }
}
</script>
