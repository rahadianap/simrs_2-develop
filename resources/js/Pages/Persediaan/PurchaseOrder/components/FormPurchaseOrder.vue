<template>
    <div class="uk-container hims-form-container" style="min-height:50vh;">
        <div class="uk-grid-small" uk-grid>
            <div class ="uk-width-expand">
                <h4 style="font-weight:500;margin:0;padding:0;" class="uk-text-uppercase">Form Pembelian</h4>
                <p style="font-weight:300;margin:0;padding:0;font-size:12px;font-style:italic;">form data pembelian persediaan</p>
            </div>
        </div>
        <div class="uk-container" style="background-color:#fff;padding:0.5em 1em 1em 1em;margin-top:1em;">
            <form action="" @submit.prevent="submitPurchaseOrder">
                <div class="uk-grid-small uk-card1 uk-card-default1 hims-form-header" uk-grid style="padding:0;margin:0;">                        
                    <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                        <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                            <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="closeModalPO"><span uk-icon="arrow-left"></span> Kembali</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="refreshPurchaseOrder"><span uk-icon="refresh"></span> Refresh</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="removeOrder" :disabled="!isUpdate"><span uk-icon="trash"></span> Hapus</button>
                            <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="cetakDataPO" :disabled="!isUpdate"><span uk-icon="file-pdf"></span> Print</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="approveOrder" :disabled="!isUpdate"><span uk-icon="file-edit"></span> Setujui</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="releaseOrder" :disabled="!isUpdate"><span uk-icon="lock"></span> Kirim PO</button>
                            <button type="submit" class="hims-table-btn uk-width-auto"><span uk-icon="file-text"></span> Simpan</button>
                        </div>
                    </div>
                </div>

                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>
                <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                    <div uk-spinner="ratio : 2"></div>
                    <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">proses data...</span>
                </div>     

                <div class="uk-grid-small hims-form-subpage" uk-grid>
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA PEMBELIAN</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            data pembelian.
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                        <div class="uk-width-1-2@m">
                            <search-list
                                :config = configSelect
                                :dataLists = userUnitLists
                                label = "Unit Pengadaan*"
                                placeholder = "pilih unit pelayanan"
                                captionField = "unit_nama"
                                valueField = "unit_nama"
                                :detailItems = unitDesc
                                :value = "order.unit_nama"
                                v-on:item-select = "unitSelected">
                            </search-list>
                        </div>
                        <div class="uk-width-1-2@m">
                            <search-select 
                                :config=configSelect 
                                searchUrl="/vendors" 
                                label="Vendor*" 
                                placeholder=""
                                captionField="vendor_nama" 
                                valueField="vendor_nama" 
                                :itemListData=vendorDesc
                                :value="order.vendor_nama" 
                                v-on:item-select="vendorSelected">
                            </search-select>
                        </div>

                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Transaksi*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="order.tgl_transaksi" type="date" placeholder="tanggal transaksi" required disabled>
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Dibutuhkan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="order.tgl_dibutuhkan"
                                    type="date" placeholder="tanggal dibutuhkan"
                                    required :disabled="isUpdate">
                            </div>
                        </div>

                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Termin Bayar(hari)*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="order.termin" type="number" required>
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Bayar*</label>
                            <div class="uk-form-controls">
                                <select v-model = "order.jenis_bayar" class="uk-select uk-form-small">
                                    <option value="KREDIT">KREDIT</option>
                                    <option value="TUNAI">TUNAI</option>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="order.catatan" type="text" required>
                            </div>
                        </div>
                        <div v-if="isUpdate" class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:14px;color:black;">
                                <input class="uk-checkbox" type="checkbox" v-model="order.is_aktif" style="margin-left:5px;"> Data order aktif
                            </label>
                        </div>
                    </div>
                </div>
                <div class="uk-grid-small hims-form-subpage" uk-grid>
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0 0.5em 0 0.5em;">
                        <div class="uk-width-expand@m">
                            <h5 style="color:indigo;padding:0;margin:0;">DETAIL PEMBELIAN
                                <span style="font-size:12px;font-style:italic;padding:0;margin:0;"> data detail pembelian.</span>
                            </h5>
                        </div>
                        <div class="uk-width-auto@m">
                            <button @click.prevent="pilihItemPermintaan">pilih item permintaan</button>
                        </div>
                    </div>
                    <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                        <table class="uk-table hims-table uk-table-striped">
                            <thead>                                   
                                <th style="text-align:center;padding:0.5em;margin:0;border:1px solid red;"></th>

                                <th style="text-align:center;padding:0.5em;margin:0;border:1px solid red;">Jenis</th>
                                <th style="text-align:center;padding:0.5em;margin:0;border:1px solid red;">Item</th>

                                <th style="width:60px;text-align:center;padding:0.5em;margin:0;border:1px solid red;">Jml</th>
                                <th style="text-align:center;padding:0.5em;margin:0;border:1px solid red;">Satuan</th>
                                <th style="width:80px;text-align:center;padding:0.5em;margin:0;border:1px solid red;">Harga</th>
                                <th style="width:60px;text-align:center;padding:0.5em;margin:0;border:1px solid red;">Disc(%)</th>

                                <th style="text-align:center;padding:0.5em;margin:0;border:1px solid red;">Subtotal</th>
                                <th style="width:40px;text-align:center;padding:0.5em;margin:0;border:1px solid red;">PPN</th>
                                <th style="width:40px;text-align:center;padding:0.5em;margin:0;border:1px solid red;">...</th>
                                
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2" style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:80px;">
                                        <div class="uk-form-controls">
                                            <select v-model="detailPO.jenis_produk" class="uk-select uk-form-small" @change="jenisSelected">
                                                <option v-if="isRefProdukExist" v-for="dt in jenisProdukRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:200px;">
                                        <div class="uk-form-controls">
                                            <search-select 
                                                :config=configSelect2 
                                                :searchUrl=urlSearch
                                                placeholder="Paracetamol 500mg, Kertas HVS, etc..."
                                                captionField="produk_nama" 
                                                valueField="produk_id"
                                                :itemListData=produkDesc 
                                                :value=detailPO.produk_id
                                                v-on:item-select="produkSelected">
                                            </search-select>
                                        </div>
                                    </td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:30px;">
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="number" v-model="detailPO.jml_po" style="text-align:center;">
                                        </div>
                                    </td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                        <search-list
                                            :config = configSelect2
                                            :dataLists = satuanProdukRefs.json_data
                                            placeholder = ""
                                            captionField = "value"
                                            valueField = "value"
                                            :detailItems = satuanDesc
                                            :value = "detailPO.satuan_beli"
                                            v-on:item-select = "satuanSelected"
                                        ></search-list>
                                    </td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="number" style="text-align:center;" v-model="detailPO.harga">
                                        </div>
                                    </td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="number" style="text-align:center;" v-model="detailPO.diskon" max="100" min="0">
                                        </div>
                                    </td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:80px;">
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="number" style="text-align:right;" v-model="detailPO.subtotal" readonly>
                                        </div>
                                    </td>
                                    <td style="padding:1em;margin:0;color:black;text-align: center;">
                                        <div class="uk-form-controls">
                                            <input class="uk-checkbox" type="checkbox" v-model="detailPO.is_pajak"> 
                                        </div>
                                    </td>                                        
                                    <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                        <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addDetailPO" style="padding:0;margin:0;color:blue;display:inline-block;" :disabled="isUpdate"></a>
                                    </td>
                                </tr>
                                <row-item-order 
                                    v-if="order.detail_po" 
                                    v-for="dt in order.detail_po" 
                                    :data="dt"
                                    :updateData="updateRowData">
                                </row-item-order>

                                <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">
                                    <td colspan="7" style="font-size:1em;padding:0.2em;margin:0;text-align: right;">Total:</td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:14px;font-weight:500;text-align: right;">{{ formatCurrency(order.subtotal) }}</td>
                                </tr>
                                <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">
                                    <td colspan="7" style="font-size:1em;padding:0.2em;margin:0;text-align: right;">PPN:</td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:14px;font-weight:500;text-align: right;">{{ formatCurrency(order.total_pajak) }}</td>
                                </tr>
                                <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">
                                    <td colspan="7" style="font-size:1em;padding:0.2em;margin:0;text-align: right;">Grand Total:</td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:14px;font-weight:500;text-align: right;">{{ formatCurrency(order.total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
        <modal-item-request ref="modalItemRequest" v-on:itemSelected="addItemToPurchase"></modal-item-request>
        <cetakan-purchase-order ref="cetakanPurchaseOrder"></cetakan-purchase-order>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';
import ModalItemRequest from '@/Pages/Persediaan/PurchaseOrder/components/ModalItemRequest.vue';
import RowItemOrder from '@/Pages/Persediaan/PurchaseOrder/components/RowItemOrder.vue';
import CetakanPurchaseOrder from '@/Pages/Persediaan/PurchaseOrder/cetakan/CetakanPurchaseOrder.vue';

export default {
    name: 'form-purchase-order',
    components: { SearchSelect,SearchList,ModalItemRequest,RowItemOrder,CetakanPurchaseOrder },
    data() {
        return {
            //isExpanded : false,
            isLoading : false,
            configSelect: {
                required: true,
                compClass: "uk-width-1-1@m",
                compStyle: "padding:0;margin:0;",
            },
            configSelect2: {
                required: false,
                compClass: "uk-width-1-1@m",
                compStyle: "padding:0;margin:0;",
            },
            unitDesc: [
                { colName: 'unit_nama', labelData: '', isTitle: true },
                { colName: 'unit_id', labelData: '', isTitle: false },
            ],
            // prDesc: [
            //     { colName: 'pengadaan_id', labelData: '', isTitle: true },
            //     { colName: 'pengadaan_id', labelData: '', isTitle: false },
            // ],
            vendorDesc: [
                { colName: 'vendor_nama', labelData: '', isTitle: true },
                { colName: 'vendor_id', labelData: '', isTitle: false },
            ],
            // depoDesc: [
            //     { colName: 'depo_nama', labelData: '', isTitle: true },
            //     { colName: 'depo_id', labelData: '', isTitle: false },
            // ],
            produkDesc: [
                { colName: 'produk_nama', labelData: '', isTitle: true },
                { colName: 'produk_id', labelData: '', isTitle: false },
            ],
            satuanDesc : [
                { colName : 'value', labelData : '', isTitle : true },
                { colName : 'text', labelData : '', isTitle : false },
            ],

            isUpdate: false,
            order: {
                pengadaan_id: null,
                trx_id: null,
                reff_trx_id: null,
                tgl_transaksi: null,
                jenis_transaksi : null,
                no_transaksi : null,
                tgl_dibutuhkan: null,                
                unit_id: null,
                unit_nama: null,
                vendor_id: null,
                vendor_nama: null,                
                termin : 30,
                jenis_bayar : null,
                catatan: null,                
                total_diskon : 0,
                total_pajak : 0,
                subtotal: 0,
                total : 0,
                is_aktif: true,
                status : null,
                client_id: null,
                detail_po: [],
            },

            detailPO: {
                detail_id : null,
                pengadaan_id : null,
                jenis_produk: null,
                produk_id: null,
                produk_nama: null,
                jml_po: 1,
                satuan_beli : null,
                harga: 0,
                diskon : 0,
                nilai_diskon : 0,
                subtotal: 0,
                is_pajak : false,                
                persen_pajak : 11,
                nilai_pajak : 0,
                pr_detail_id : null,
                pr_id : null,
                is_aktif : true,
                items : [],
            },

            selectedDetail: [],
            urlSearch: '',
            userUnitLists : null,
            userDepoLists : null, 
        }
    },

    computed: {
        ...mapGetters(['errors']),
        ...mapGetters('unitPelayanan', ['unitLists']),
        ...mapGetters('refProduk',['jenisProdukRefs','isRefProdukExist','klasifikasiProdukRefs','golonganProdukRefs','satuanProdukRefs']),
    },

    mounted() {
        this.init();
    },

    methods: {
        ...mapActions('purchaseOrder', ['createPO', 'dataPO', 'updatePO','updateApprovePO','deletePO','updateProsesPO']),
        ...mapActions('users',['listUserUnit']),
        ...mapActions('purchaseRequest', ['dataPR']),
        ...mapActions('unitPelayanan', ['listUnitPelayanan']),
        ...mapActions('refProduk', ['updateRefProduk', 'listRefProduk']),
        ...mapActions('cetakan', ['dataCetakanPO']),
        ...mapMutations(['CLR_ERRORS']),
        
        // changeRowExpand(){
        //     this.isExpanded = !this.isExpanded;
        //     return false;
        // },

        closeModalPO(){
            this.CLR_ERRORS();
            this.clearOrder();
            this.$emit('modalPoClosed',true);
        },

        /**
         * get date today for default value input date 
         */
         getTodayDate(){
            let dt = new Date();
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate();
            if(date < 10) {date = `0${date}`}
            let str_dt = `${year}-${month}-${date}`
            return str_dt;
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        init() {
            if (!this.isRefProdukExist) {
                this.listRefProduk();
            };
            this.listUserUnit({'per_page':'ALL'}).then((response) => {
                if (response.success == true) {
                    this.userUnitLists = response.data.data;
                    // console.log(this.userUnitLists);
                }
            })
        },

        // countSubtotal() {
        //     this.detailPO.subtotal = this.detailPO.jml_po * this.detailPO.harga;
        // },

        countTotal() {            
            //calculate subtotal
            this.order.subtotal = 0;
            this.order.total_diskon = 0;
            this.order.total_pajak = 0;
            this.order.total = 0;
            let me = this;
            this.order.detail_po.forEach(function(data) {
                if(data.is_aktif) {
                    //hitung diskon per data
                    let discVal = (data.jml_po * data.harga * data.diskon)/100; 
                    data.nilai_diskon = discVal;                
                    me.calculateTotal(discVal, 0, 0);
                    //hitung subtotal
                    let subtotalVal = (data.jml_po * data.harga) - discVal;
                    data.subtotal = subtotalVal;
                    me.calculateTotal(0, subtotalVal, 0);
                    //hitung pajak
                    if(data.is_pajak) {
                        let ppnVal = (data.subtotal * data.persen_pajak * 1) / 100;
                        data.nilai_pajak = ppnVal;
                        me.calculateTotal(0, 0, ppnVal);
                    }
                } 
            });
        },

        calculateTotal(diskon, subtotal, pajak ){
            this.order.total_diskon = this.order.total_diskon + diskon;
            this.order.subtotal = this.order.subtotal + subtotal;
            this.order.total_pajak = this.order.total_pajak + pajak;
            this.order.total = this.order.total + pajak + subtotal; 
        },

        closeModal() {
            this.$emit('closed', true);
            this.clearOrder();
            UIkit.modal('#formPurchaseOrder').hide();
            return false;
        },

        unitSelected(data) {
            if (data) {
                this.order.unit_id = data.unit_id;
                this.order.unit_nama = data.unit_nama;
                this.userDepoLists = data.depos;
            }
        },

        // depoSelected(data) {
        //     if (data) {
        //         this.order.depo_id = data.depo_id;
        //         this.order.depo_nama = data.depo_nama;
        //         this.urlSearch = `/depos/${data.depo_id}/products`;
        //     }
        // },

        vendorSelected(data) {
            if (data) {
                this.order.vendor_id = data.vendor_id;
                this.order.vendor_nama = data.vendor_nama;
            }
        },

        satuanSelected(data){
            if(data){ this.detailPO.satuan_beli = data.value; }
        },

        jenisSelected() {
            this.searchUrl = null;
            this.detailPO.produk_id = null;
            this.detailPO.produk_nama = null;
            this.detailPO.jml_po = 1;
            this.detailPO.satuan_beli = null;
            this.detailPO.harga = 0;
            this.detailPO.subtotal = 0;

            if (this.detailPO.jenis_produk == 'MEDIS') {  this.urlSearch = `products/medical/items`; } 
            else if (this.detailPO.jenis_produk == 'UMUM') { this.urlSearch = `products/general/items`; } 
            else if (this.detailPO.jenis_produk == 'JASA') {  this.urlSearch = `products/service/items`; } 
            else { this.urlSearch = `products/kitchen/items`; }            
        },

        // getProdukData() {
        //     let produk_id = ''
        //     if (this.detailPO.produk_id == '') { produk_id = document.getElementById("jenis_produk").value; } 
        //     else { produk_id = this.detailPO.produk_id; }

        //     if (produk_id == 'MEDIS') { produk_id = 'medical'; } 
        //     else if (produk_id == 'UMUM') { produk_id = 'general'; } 
        //     else if (produk_id == 'JASA') { produk_id = 'service'; } 
        //     else { produk_id = 'kitchen'; }

        //     this.urlSearch = `products/${produk_id}/items`;
        // },

        produkSelected(data) {
            this.clearDetailPO();
            if (data) {
                this.detailPO.jenis_produk = data.jenis_produk;
                this.detailPO.produk_id = data.produk_id;
                this.detailPO.produk_nama = data.produk_nama;
                this.detailPO.satuan_beli = data.satuan_beli;
                this.detailPO.harga = data.harga_beli;
                this.detailPO.diskon = 0;
                this.detailPO.subtotal = data.harga_beli;
                
            }
        },

        getDate(params){
            var date = new Date(params);
            var outDate = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();
            return outDate;
        },

        // viewPO(pengadaan_id) {
        //     this.clearOrder();
        //     this.dataPO(pengadaan_id).then((response) => {
        //         if (response.success == true) {
        //             this.order.client_id = null;
        //             if(response.data[0].reff_trx_id != null){
        //                 this.order.pr_id = response.data[0].reff_trx_id;
        //             }else{
        //                 this.order.pr_id = null;
        //             }
        //             this.order.pengadaan_id = response.data[0].pengadaan_id;
        //             this.order.tgl_transaksi = this.getDate(response.data[0].tgl_transaksi);
        //             this.order.tgl_dibutuhkan = this.getDate(response.data[0].tgl_dibutuhkan);
        //             this.order.tgl_rencana_bayar = this.getDate(response.data[0].tgl_rencana_bayar);
        //             this.order.unit_id = response.data[0].unit_id;
        //             this.order.unit_nama = response.data[0].unit_nama;
        //             this.order.depo_id = response.data[0].depo_id;
        //             this.order.depo_nama = response.data[0].depo_nama;
        //             this.order.vendor_id = response.data[0].vendor_id;
        //             this.order.vendor_nama = response.data[0].vendor_nama;
        //             this.order.status = response.data[0].status;
        //             this.order.catatan = response.data[0].catatan;
        //             this.order.is_aktif = true;
        //             this.configSelect.disabled = true;
        //             this.order.detail_po = [];
        //             if (response.data.length > 0) {
        //                 for (let i = 0; i <= response.data.length - 1; i++) {
        //                     this.order.detail_po.push({
        //                         "jumlah": response.data[i]['jml_po'],
        //                         "satuan": response.data[i]['satuan_beli_id'],
        //                         "produk_id": response.data[i]['produk_nama'],
        //                         "jenis_produk": response.data[i]['jenis_produk'],
        //                         "harga": response.data[i]['harga_beli'],
        //                         "subtotal": response.data[i]['jml_po'] * response.data[i]['harga_beli'],
        //                     });
        //                 }
        //             }
        //             // this.isUpdate = true;
        //             UIkit.modal('#formPurchaseOrder').show();
        //         } else {
        //             alert(response.message);
        //         }
        //     })
        // },

        submitPurchaseOrder() {
            this.isLoading = true; 
            this.CLR_ERRORS();
            this.countTotal();
            if (!this.isUpdate) {
                this.createPO(this.order).then((response) => {
                    if (response.success == true) {
                        alert(" data baru berhasil dibuat.");
                        this.clearOrder();
                        this.isLoading = false; 
                        this.isUpdate = false;
                    }
                    else { 
                        alert(response.message);
                        this.isLoading = false; 
                    }
                })
            }
            else {
                this.updatePO(this.order).then((response) => {
                    if (response.success == true) {
                        alert(" data berhasil diubah.");
                        this.clearOrder();
                        this.isUpdate = false;
                        this.isLoading = false; 
                        this.closeModal();
                    }
                    else { 
                        alert(response.message);
                        this.isLoading = false; 
                    }
                })
            }
        },

        refreshPurchaseOrder(){
            if(this.isUpdate) { this.editPurchaseOrder(this.order.pengadaan_id); }
            else { this.newPurchaseOrder(); }
        },

        newPurchaseOrder() {
            this.isLoading = true;
            this.CLR_ERRORS();
            this.clearOrder();
            this.isUpdate = false;
            this.isLoading = false;
            //UIkit.modal('#formPurchaseOrder').show();
        },

        editPurchaseOrder(pengadaan_id) {
            this.isLoading = true;            
            this.CLR_ERRORS();
            this.clearOrder();
            this.dataPO(pengadaan_id).then((response) => {
                if (response.success == true) {
                    this.fillOrder(response.data);
                    this.isUpdate = true;
                    this.isLoading = false;
                } 
                else {
                    alert(response.message);
                    this.isLoading = false;
                }
            })
        },

        // removeDetailPO(val){
        //     for (var i = 0; i < this.req.detail_po.length; i++) {
        //         if(this.req.detail_po[i]["index"] === val) {
        //             this.req.detail_po.splice(i, 1);
        //         }
        //     }
        //     this.detailReq.jenis_produk = null;
        //     this.detailReq.produk_id = null;
        //     this.detailReq.produk_nama = null;
        //     this.detailReq.jumlah = null;
        //     this.detailReq.satuan = null;
        //     this.detailReq.harga = null;
        //     this.detailReq.subtotal = null;
        // },

        clearOrder() {
            this.order.pengadaan_id = null;
            this.order.trx_id = null;
            this.order.reff_trx_id = null;
            this.order.tgl_transaksi = null;
            this.order.jenis_transaksi = null;
            this.order.no_transaksi = null;
            this.order.tgl_dibutuhkan = null;                
            this.order.unit_id = null;
            this.order.unit_nama = null;
            this.order.vendor_id = null;
            this.order.vendor_nama = null;                
            this.order.termin = 30;
            this.order.jenis_bayar = null;
            this.order.catatan = null;                
            this.order.total_diskon = 0;
            this.order.total_pajak = 0;
            this.order.subtotal = 0;
            this.order.total = 0;
            this.order.is_aktif = true;
            this.order.status = null;
            this.order.client_id = null;
            this.order.detail_po = [];
            this.clearDetailPO();
        },

        clearDetailPO(){
            this.detailPO.pengadaan_id = null;
            this.detailPO.jenis_produk= null;
            this.detailPO.produk_id= null;
            this.detailPO.produk_nama= null;
            this.detailPO.jml_po = 1;
            this.detailPO.satuan_beli= null;
            this.detailPO.harga= 0;
            this.detailPO.diskon = 0;
            this.detailPO.nilai_diskon = 0;
            this.detailPO.subtotal= 0;
            this.detailPO.is_pajak = false;
            this.detailPO.nilai_pajak = 0;
            this.detailPO.is_aktif = true;
            this.detailPO.items = [];
        },

        fillOrder(data) {
            this.order.pengadaan_id = data.pengadaan_id;
            this.order.trx_id = data.trx_id;
            this.order.reff_trx_id = data.reff_trx_id;
            this.order.tgl_transaksi = data.tgl_transaksi;
            this.order.jenis_transaksi = data.jenis_transaksi;
            this.order.no_transaksi = data.no_transaksi;
            this.order.tgl_dibutuhkan = data.tgl_dibutuhkan;
            this.order.unit_id = data.unit_id;
            this.order.unit_nama = data.unit_nama;
            this.order.vendor_id = data.vendor_id;
            this.order.vendor_nama = data.vendor_nama;
            this.order.termin = data.termin;
            this.order.jenis_bayar = data.jenis_bayar;
            this.order.catatan = data.catatan; 
            this.order.total_diskon = data.total_diskon;
            this.order.total_pajak = data.total_pajak;
            this.order.subtotal = data.subtotal;
            this.order.total = data.total;
            this.order.is_aktif = data.is_aktif;
            this.order.status = data.status;
            this.order.client_id = data.client_id;
            this.order.detail_po = data.detail_po;
        },

        addDetailPO() {
            if (this.detailPO.produk_id == null || this.detailPO.produk_id == '' || this.detailPO.jml_po == null || this.detailPO.jml_po == '' || this.detailPO.jenis_produk == null || this.detailPO.jenis_produk == '') {
                alert('Data tidak lengkap.');
                return false;
            }
            else {
                let exist = this.order.detail_po.find(item => item.produk_id == this.detailPO.produk_id);
                if(exist) { 
                    alert('item data sudah ada dalam daftar.'); 
                    this.clearDetailPO();
                    return false; 
                }

                this.order.detail_po.push(JSON.parse(JSON.stringify(this.detailPO)));
                this.countTotal();
                this.clearDetailPO();
            }
        },

        pilihItemPermintaan(){
            this.$refs.modalItemRequest.showModalItemRequest();
        },

        addItemToPurchase(data){
            data.forEach(dt => {
                if(dt.is_pilih == true) {
                    let val = JSON.parse(JSON.stringify(dt));
                    let itemExist = this.order.detail_po.find(dpo => dpo.produk_id == dt.produk_id);
                    if(!itemExist) {
                        this.order.detail_po.push({
                            'pengadaan_id' : null,
                            'produk_id' : dt.produk_id,
                            'jenis_produk' : dt.jenis_produk,
                            'produk_nama' : dt.produk_nama,
                            'jml_po' : dt.jml_pr,
                            'satuan_beli' : dt.satuan,
                            'harga'  : dt.harga_beli,
                            'subtotal' : dt.jml_pr*dt.harga_beli,
                            'diskon'  : 0,
                            'is_pajak' : false,
                            'persen_pajak' : 11,
                            'nilai_pajak' : 0,
                            'is_aktif' : true,
                            'items' : [ val ],
                        });
                    }
                    else { this.addDetailItemToItemPurchase(dt); }
                    this.countTotal();
                }
            });            
        },

        addDetailItemToItemPurchase(data){
            this.order.detail_po.forEach(function(detail) {
                if(data.produk_id == detail.produk_id) {
                    let findItem = detail.items.find(item => item.pr_detail_id == data.pr_detail_id);
                    if(!findItem) {
                        detail.items.push(JSON.parse(JSON.stringify(data)));
                        let jml = detail.jml_po + data.jml_pr;
                        let subtotal = jml * detail.harga;
                        detail.jml_po = jml;
                        detail.subtotal = subtotal;
                    }
                }
            });
        },

        // checkUpdate(){
        //     alert('cek update');
        //     console.log(this.order);
        // },

        updateRowData(){
            this.order.detail_po = this.order.detail_po.filter(item => { return item.is_aktif == true || item.pengadaan_id !== null;  });
            this.countTotal();
        },
        /**
         * tahapan pengajuan PO untuk disetujui. data sudah tidak dapat diubah
         */
        approveOrder(){
            if (this.order.status == 'PEMBELIAN' || this.order.status == 'DISETUJUI') {
                alert('Data tidak bisa diubah karena telah diproses.');
                return false;
            }
            
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengubah status PO dan tidak dapat dikembalikan. Setujui data pembelian ?`)){
                this.isLoading = true; 
                this.updateApprovePO(this.order.trx_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.isLoading = false; 
                        this.closeModalPO();
                    }
                    else { alert (response.message); this.isLoading = false;  }
                });
            };
        },

        removeOrder(){
            if (this.order.status !== 'DRAFT' && this.order.status !== 'DISETUJUI' && this.order.status !== 'PEMBELIAN') {
                alert('Data tidak bisa diubah karena telah diproses.');
                return false;
            }
            this.CLR_ERRORS();
            if(confirm(`Hapus data PO?`)){
                this.isLoading = true; 
                this.deletePO(this.order.trx_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.isLoading = false; 
                        this.closeModalPO();
                    }
                    else { alert (response.message); this.isLoading = false;  }
                });
            };
        },

        /**
         * tahapan final dari proses PO. data sudah tidak dapat diubah
         */
        releaseOrder() {
            if (this.order.status == 'PEMBELIAN') {
                alert('Data tidak bisa diubah karena telah diproses.');
                return false;
            }

            else if(this.order.status == 'DRAFT'){
                alert('Dokumen masih dalam status draft, silahkan approve terlebih dahulu untuk memproses PO.');
                return false;
            }
            
            if(confirm(`Lanjutkan PO ke vendor?`)){
                this.isLoading = true; 
                this.updateProsesPO(this.order.trx_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.isLoading = false; 
                        this.closeModalPO();
                    }
                    else { alert (response.message) }
                });
            };
        },

        cetakDataPO(){
            this.dataCetakanPO(this.order.trx_id).then((response) => {
                if (response.success == true) {
                    this.$refs.cetakanPurchaseOrder.generateReport(response.data);
                }
                else { alert (response.message) }
            });
        }
    }
}
</script>

<style>
/* .uk-input,
.uk-textarea,
.uk-checkbox {
    border: 1px solid #999;
}

.hims-form-container label {
    color: #333;
    font-size: 12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border: 2px solid #cc0202;
}

.uk-button {
    border-radius: 50px;
    border: 2px solid #aaa;
    font-weight: 400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border: 2px solid #cc0202;
    font-weight: bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border: 2px solid #cc0202;
    font-weight: bold;
}

.hims-accordion-title {
    border-bottom: 1px solid #cc0202;
    font-size: 14px;
    font-weight: 500;
    background-color: #cc0202;
    color: white;
    padding: 0.5em 1em 0.5em 1em;
    border-radius: 5px;
}

.hims-accordion-title:hover {
    color: white;
}

.hims-accordion-title::before {
    color: white;
}

.hims-form-header {
    padding: 0.5em 0 0 0;
    margin: 1em 0 0 0;
    position: sticky;
    top: 0;
    background-color: white;
    z-index: 99;
    color: #cc0202;
}

.hims-form-header h5 {
    color: #333;
    font-weight: 500;
    font-size: 18px;
}


.hims-form-subpage {
    padding: 1em;
    margin: 0 0.5em 0.5em 0.5em;
    border-top: 1px solid #cc0202;
} */
</style>