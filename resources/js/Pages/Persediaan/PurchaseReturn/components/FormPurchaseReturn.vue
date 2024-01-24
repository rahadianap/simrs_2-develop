
<template>
    
    <div class="uk-container hims-form-container" style="min-height:50vh;">
        <div class="uk-grid-small" uk-grid>
            <div class ="uk-width-expand">
                <h4 style="font-weight:500;margin:0;padding:0;" class="uk-text-uppercase">Form Purchase Return</h4>
                <p style="font-weight:300;margin:0;padding:0;font-size:12px;font-style:italic;">form data pengembalian penerimaan</p>
            </div>
        </div>
        <div class="uk-container" style="background-color:#fff;padding:0.5em 1em 1em 1em;margin-top:1em;">
            <div class="uk-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitPORR" style="padding:0;">                    
                    <div class="uk-grid-small uk-card1 uk-card-default1 hims-form-header" uk-grid style="padding:0;margin:0;">                        
                        <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                            <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                                <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="closeModal"><span uk-icon="arrow-left"></span> Kembali</button>
                                <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="refreshPurchaseReturn"><span uk-icon="refresh"></span> Refresh</button>
                                <button type="button" class="hims-table-btn uk-width-auto" @click="removePurchaseReturn" :disabled="!isUpdate"><span uk-icon="trash"></span> Hapus</button>
                                <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                                <button type="button" class="hims-table-btn uk-width-auto" @click="approvePurchaseReturn" :disabled="!isUpdate"><span uk-icon="file-edit"></span> Selesai</button>
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
                                data pembelian persediaan.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                            <div  v-if="!isUpdate" class="uk-width-1-1@m">
                                <button type="button" @click.prevent="showModalPO">Pilih PO</button>
                            </div>
                            <template v-if="receive.pengadaan_id == null">
                                <div>
                                    <p style="font-size:11px; font-style: italic;">pilih salah satu pembelian</p>
                                </div>
                            </template>
                            <template v-else>
                                <div class="uk-width-1-2@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>PO </dt>
                                        <dd>{{receive.order.trx_id}}</dd>
                                        <dt>Unit Pengadaan</dt>
                                        <dd>{{receive.order.unit_nama}}</dd>
                                        <dt>Vendor </dt>
                                        <dd>{{receive.order.vendor_nama}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Pembayaran</dt>
                                        <dd>{{receive.order.jenis_bayar}} / {{receive.order.termin}} Hari</dd>
                                        <dt>Tgl. PO / Dibutuhkan</dt>
                                        <dd>{{receive.order.tgl_transaksi}} / {{receive.order.tgl_dibutuhkan}}</dd>
                                        <dt>Catatan</dt>
                                        <dd>{{receive.order.catatan}}</dd>
                                    </dl>
                                </div>
                            </template>
                        </div>
                    </div>                            

                    <div class="uk-grid-small hims-form-subpage" uk-grid>
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PENGEMBALIAN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                data pengembalian penerimaan.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                            <div class="uk-width-1-2@m">
                                <search-list
                                    :config = configSelect2
                                    :dataLists = userUnitLists
                                    label = "Unit Retur*"
                                    placeholder = "pilih unit retur"
                                    captionField = "unit_nama"
                                    valueField = "unit_nama"
                                    :detailItems = unitDesc
                                    :value = "receive.unit_nama"
                                    v-on:item-select = "unitSelected">
                                </search-list>
                            </div>
                            <div class="uk-width-1-2@m">
                                <search-list
                                    :config = configSelect2
                                    :dataLists = userDepoLists
                                    label = "Depo Retur*"
                                    placeholder = "pilih depo retur"
                                    captionField = "depo_nama"
                                    valueField = "depo_nama"
                                    :detailItems = depoDesc
                                    :value = "receive.depo_nama"
                                    v-on:item-select = "depoSelected">
                                </search-list>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Retur*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="receive.tgl_transaksi" type="date" placeholder="tanggal retur" required disabled  style="color:black;">
                                </div>
                            </div>                            
                            <div class="uk-width-3-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="receive.catatan" type="text" required style="color:black;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid>
                        <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DETAIL PENGEMBALIAN</h5>
                        </div>
                        <template v-if="!isUpdate">
                            <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                                <table class="uk-table hims-table uk-table-striped">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="border:1px solid red;padding:0.2em;">Produk</th>
                                            <th rowspan="2" style="text-align: center;border:1px solid red;padding:0.2em;">Beli</th>
                                            <th rowspan="2" style="text-align: center;border:1px solid red;padding:0.2em;">Terima</th>
                                            <th rowspan="2" style="text-align: center;border:1px solid red;padding:0.2em;">Retur</th>
                                            <th colspan="4" style="text-align: center;border:1px solid red;padding:0.2em;">Penerimaan</th>
                                            <th colspan="4" style="text-align: center;border:1px solid red;padding:0.2em;">Harga</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Jml</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Unit</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Konv</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Sediaan</th>

                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Harga</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Disc(%)</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Subtotal</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">PPN</th>                                        
                                        </tr>                                    
                                    </thead>
                                    <tbody>
                                        <tr v-for="dt in receive.detail_receive" v-bind:class="dt.is_aktif != true ? 'inaktif':'' ">                                        
                                            <td style="padding:0.3em;margin:0;color:black;font-size:12px;">
                                                <p style="margin:0;padding:0;font-weight: 500;">{{dt.produk_nama}}</p>
                                                <p style="margin:0;padding:0;font-weight: 300;font-size: 10px; font-style: italic;">{{dt.produk_id}}</p>
                                            </td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center;">
                                                {{dt.jml_po}} {{dt.satuan_beli}}
                                            </td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center;">
                                                {{dt.jml_por}} {{dt.satuan_beli}}
                                            </td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center;">
                                                {{dt.jml_porr}} {{dt.satuan_beli}}
                                            </td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;width:80px;">
                                                <div class="uk-form-controls">
                                                    <input class="uk-input uk-form-small" type="number" v-model="dt.jml_kembali" @change="calculateReceive" style="text-align:center;">
                                                </div>
                                            </td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center;">{{dt.satuan_beli}}</td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;text-align: center; width:80px;">
                                                <div class="uk-form-controls">
                                                    <input class="uk-input uk-form-small" type="number" v-model="dt.konversi" @change="calculateReceive" style="text-align:center;">
                                                </div>
                                            </td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center;">{{dt.total_kembali}} {{dt.satuan}}</td>

                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: right;">{{formatCurrency(dt.harga)}}</td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center;">{{dt.diskon}}</td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: right;">{{formatCurrency(dt.subtotal)}}</td>
                                            <td style="width:50px;text-align: center;">
                                                <input class="uk-checkbox" type="checkbox" v-model="dt.is_pajak" disabled style="margin-left:5px;border:1px solid black;">
                                            </td>
                                        </tr>

                                        <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">
                                            <td colspan="10" style="font-size:1em;padding:0.2em;margin:0;text-align: right;">Total:</td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:14px;font-weight:500;text-align: right;">{{ formatCurrency(receive.subtotal) }}</td>
                                            <td style="font-size:1em;padding:0.2em;margin:0;text-align: right;"></td>
                                        </tr>
                                        <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">
                                            <td colspan="10" style="font-size:1em;padding:0.2em;margin:0;text-align: right;">PPN:</td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:14px;font-weight:500;text-align: right;">{{ formatCurrency(receive.total_pajak) }}</td>
                                            <td style="font-size:1em;padding:0.2em;margin:0;text-align: right;"></td>
                                        </tr>
                                        <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">
                                            <td colspan="10" style="font-size:1em;padding:0.2em;margin:0;text-align: right;">Grand Total:</td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:14px;font-weight:500;text-align: right;">{{ formatCurrency(receive.total) }}</td>
                                            <td style="font-size:1em;padding:0.2em;margin:0;text-align: right;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                        <template v-else>
                            <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                                <table class="uk-table hims-table uk-table-striped">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="border:1px solid red;padding:0.2em;">Produk</th>
                                            <th colspan="4" style="text-align: center;border:1px solid red;padding:0.2em;">Penerimaan</th>
                                            <th colspan="4" style="text-align: center;border:1px solid red;padding:0.2em;">Harga</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Jml</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Unit</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Konv</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Sediaan</th>

                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Harga</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Disc(%)</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">Subtotal</th>
                                            <th style="text-align: center;border:1px solid red;padding:0.2em;">PPN</th>                                        
                                        </tr>                                    
                                    </thead>
                                    <tbody>
                                        <tr v-for="dt in receive.detail_receive" v-bind:class="dt.is_aktif != true ? 'inaktif':'' ">                                        
                                            <td style="padding:0.3em;margin:0;color:black;font-size:12px;">
                                                <p style="margin:0;padding:0;font-weight: 500;">{{dt.produk_nama}}</p>
                                                <p style="margin:0;padding:0;font-weight: 300;font-size: 10px; font-style: italic;">{{dt.produk_id}}</p>
                                            </td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center;">
                                                {{dt.jml_porr}}
                                            </td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center;">{{dt.satuan_beli}}</td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center; width:80px;">
                                                {{dt.konversi}}
                                            </td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center;">{{dt.jml_total_porr}} {{dt.satuan}}</td>

                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: right;">{{formatCurrency(dt.harga)}}</td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: center;">{{dt.diskon}}</td>
                                            <td style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;text-align: right;">{{formatCurrency(dt.subtotal)}}</td>
                                            <td style="width:50px;text-align: center;">
                                                <input class="uk-checkbox" type="checkbox" v-model="dt.is_pajak" disabled style="margin-left:5px;border:1px solid black;">
                                            </td>
                                        </tr>

                                        <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">
                                            <td colspan="7" style="font-size:1em;padding:0.2em;margin:0;text-align: right;">Total:</td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:14px;font-weight:500;text-align: right;">{{ formatCurrency(receive.subtotal) }}</td>
                                            <td style="font-size:1em;padding:0.2em;margin:0;text-align: right;"></td>
                                        </tr>
                                        <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">
                                            <td colspan="7" style="font-size:1em;padding:0.2em;margin:0;text-align: right;">PPN:</td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:14px;font-weight:500;text-align: right;">{{ formatCurrency(receive.total_pajak) }}</td>
                                            <td style="font-size:1em;padding:0.2em;margin:0;text-align: right;"></td>
                                        </tr>
                                        <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">
                                            <td colspan="7" style="font-size:1em;padding:0.2em;margin:0;text-align: right;">Grand Total:</td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:14px;font-weight:500;text-align: right;">{{ formatCurrency(receive.total) }}</td>
                                            <td style="font-size:1em;padding:0.2em;margin:0;text-align: right;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </div>
                </form>
            </div>
        </div>
        <modal-list-order ref="modalListOrder" v-on:poSelected="poSelected"></modal-list-order>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';
import ModalListOrder from '@/Pages/Persediaan/PurchaseReceive/components/ModalListOrder.vue';

export default {
    name: 'form-purchase-return',
    components: { 
        SearchSelect,
        SearchList,
        ModalListOrder, 
    },
    data() {
        return {
            isUpdate: false,
            isLoading : false,

            configSelect2: {
                required: true,
                disabled: false,
                compClass: "uk-width-1-1@m",
                compStyle: "padding:0;margin:0;",
            },
            unitDesc: [
                { colName: 'unit_nama', labelData: '', isTitle: true },
                { colName: 'unit_id', labelData: '', isTitle: false },
            ],
            vendorDesc: [
                { colName: 'vendor_nama', labelData: '', isTitle: true },
                { colName: 'vendor_id', labelData: '', isTitle: false },
            ],
            depoDesc: [
                { colName: 'depo_nama', labelData: '', isTitle: true },
                { colName: 'depo_id', labelData: '', isTitle: false },
            ],
            
            receive: {
                client_id: null,
                pengadaan_id: null,
                vendor_id: null,
                vendor_nama: null,
                
                pr_id: null,
                status: 'DRAFT',
                tgl_transaksi: this.getTodayDate(),
                tgl_dibutuhkan: null,
                tgl_invoice : this.getTodayDate(),
                tgl_rencana_bayar : this.getTodayDate(),
                no_invoice : null,
                unit_id: null,
                unit_nama: null,
                depo_id: null,
                depo_nama: null,
                catatan: null,
                total_diskon : 0,
                total_pajak : 0,
                subtotal: 0,
                total : 0,
                is_aktif: true,
                detail_receive: [],
                order : {
                    trx_id : null,
                    pengadaan_id : null,
                    vendor_id : null,
                    vendor_nama : null,
                    unit_id : null,
                    unit_nama : null,
                    termin : null,
                    jenis_bayar : null,
                    tgl_transaksi : null,
                    catatan : null,
                    tgl_dibutuhkan : null,
                },
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
        ...mapGetters('refProduk', ['jenisProdukRefs', 'isRefProdukExist']),
    },

    mounted() {
        this.init();
    },

    methods: {
        ...mapActions('purchaseReturn', ['updatePORR', 'createPORR', 'dataPORR','deletePORR', 'approvePORR']),
        ...mapActions('users',['listUserUnit']),        
        ...mapActions('purchaseOrder', ['dataPO']),
        //...mapActions('unitPelayanan', ['listUnitPelayanan']),
        ...mapActions('refProduk', ['updateRefProduk', 'listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

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
                    console.log(this.userUnitLists);
                }
            })
        },

        showModalPO(){
            this.$refs.modalListOrder.showModalItemRequest();
        },

        newReceive() {
            this.CLR_ERRORS();
            this.isUpdate = false;
            this.clearReceive();
        },
        
        viewReceive(data) {
            this.CLR_ERRORS();
            this.clearReceive();            
            this.isUpdate = true;
            this.isLoading = true;
            this.dataPORR(data.trx_id).then((response) => {
                if (response.success == true) {
                    this.fillReceive(response.data);
                    this.isLoading = false;            
                    this.isUpdate = true;
                } 
                else {
                    alert(response.message);
                    this.isLoading = false;            
                    this.isUpdate = true;
                    this.closeModal();
                }
            })
        },
        

        closeModal() {
            this.$emit('closed', true);
            this.clearReceive();
            return false;
        },

        unitSelected(data) {
            if (data) {
                this.receive.unit_id = data.unit_id;
                this.receive.unit_nama = data.unit_nama;
                this.userDepoLists = data.depos;
            }
        },

        depoSelected(data) {
            if (data) {
                this.receive.depo_id = data.depo_id;
                this.receive.depo_nama = data.depo_nama;
            }
        },

        vendorSelected(data) {
            if (data) {
                this.receive.vendor_id = data.vendor_id;
                this.receive.vendor_nama = data.vendor_nama;
            }
        },

        getDate(params){
            var date = new Date(params);
            var outDate = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();
            return outDate;
        },

        submitPORR() {
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.countTotal();
                this.createPORR(this.receive).then((response) => {
                    if (response.success == true) {
                        alert(" data berhasil disimpan.");
                        this.isUpdate = true;
                        this.viewReceive(response.data);
                    }
                })
            }
            else {
                this.updatePORR(this.receive).then((response) => {
                    if (response.success == true) {
                        alert(" data berhasil disimpan.");
                        this.isUpdate = true;
                        this.viewReceive(response.data);
                    }
                })
            }
        },

        poSelected(data){
            if(data) {
                this.CLR_ERRORS();
                this.clearReceive();
                this.isLoading = true;
                this.dataPO(data.trx_id).then((response) => {
                    if (response.success == true) {
                        this.isUpdate = false;
                        this.fillReceive(response.data);
                        this.isLoading = false;
                    } 
                    else {
                        alert(response.message);
                        this.isLoading = false;
                    }
                })
            }
        },

        clearReceive() {
            this.receive.client_id= null;
            this.receive.pengadaan_id= null;
            this.receive.status= 'DRAFT';
            this.receive.tgl_transaksi= this.getTodayDate();
            this.receive.tgl_dibutuhkan= null;
            this.receive.tgl_invoice = this.getTodayDate();
            this.receive.tgl_rencana_bayar = this.getTodayDate();
            this.receive.no_invoice = null;
            this.receive.unit_id= null;
            this.receive.unit_nama= null;
            this.receive.depo_id= null;
            this.receive.depo_nama= null;
            this.receive.vendor_id= null;
            this.receive.vendor_nama= null;
            this.receive.catatan= null;
            this.receive.total_diskon = 0;
            this.receive.total_pajak = 0;
            this.receive.subtotal= 0;
            this.receive.total = 0;
            this.receive.is_aktif= true;
            this.receive.detail_receive= [];
            this.receive.order = {
                    trx_id : null,
                    pengadaan_id : null,
                    vendor_id : null,
                    vendor_nama : null,
                    unit_id : null,
                    unit_nama : null,
                    termin : null,
                    jenis_bayar : null,
                    tgl_transaksi : null,
                    catatan : null,
                    tgl_dibutuhkan : null,
                };
        },

        fillReceive(data) {      
            console.log(data);      
            this.receive.pengadaan_id = data.pengadaan_id;
            this.receive.vendor_id = data.vendor_id;
            this.receive.vendor_nama = data.vendor_nama;
            
            if(this.isUpdate) { 
                this.receive.order = data.order;                
                let unitSelected = this.userUnitLists.find(item => item.unit_id === data.unit_id );
                console.log(unitSelected);
                if(unitSelected) {
                    console.log(unitSelected.depos);
                    this.userDepoLists = unitSelected.depos;
                }
                else {
                    alert('Unit / Depo Persediaan tidak dalam otorisasi-mu');
                    this.closeModal();
                    return false;
                }
                this.receive.unit_id= data.unit_id;
                this.receive.unit_nama= data.unit_nama;
                this.receive.depo_id= data.depo_id;
                this.receive.depo_nama= data.depo_nama;
                this.configSelect2.disabled = true;

                this.receive.reff_trx_id = data.reff_trx_id;
                this.receive.trx_id = data.trx_id;
                
                this.receive.status = data.status;
                this.receive.tgl_transaksi= data.tgl_transaksi;
                this.receive.tgl_dibutuhkan= data.tgl_dibutuhkan;
                this.receive.tgl_invoice = data.tgl_invoice;
                this.receive.tgl_rencana_bayar = data.tgl_rencana_bayar;
                this.receive.no_invoice = data.no_invoice;
            
                this.receive.catatan= data.catatan;
                this.receive.total_diskon = data.total_diskon;
                this.receive.total_pajak = data.total_pajak;
                this.receive.subtotal= data.subtotal;
                this.receive.total = data.total;
                this.receive.is_aktif= data.is_aktif;
                this.receive.detail_receive = data.detail; 
            }
            else { 
                this.configSelect2.disabled = false;
                this.receive.reff_trx_id = data.trx_id;            
                this.receive.detail_receive = data.detail_po.filter(item => { return item.jml_kembali > 0 }); 
                this.receive.order = JSON.parse(JSON.stringify(data));
                this.countTotal();
            }
        },

        calculateReceive() {
            this.receive.detail_receive.forEach(function(item) {
                let selisih = item.jml_por - item.jml_porr;
                if(item.jml_kembali > selisih) { item.jml_kembali = selisih; }
                let jmlTotalKembali = (item.jml_kembali * item.konversi) * 1;
                item.total_kembali = jmlTotalKembali;
            });
            this.countTotal();
        },

        countTotal() {            
            //calculate subtotal
            this.receive.subtotal = 0;
            this.receive.total_diskon = 0;
            this.receive.total_pajak = 0;
            this.receive.total = 0;
            let me = this;
            this.receive.detail_receive.forEach(function(data) {
                if(data.is_aktif) {
                    //hitung diskon per data
                    let discVal = (data.jml_kembali * data.harga * data.diskon)/100; 
                    data.nilai_diskon = discVal;                
                    me.calculateTotal(discVal, 0, 0);
                    //hitung subtotal
                    let subtotalVal = (data.jml_kembali * data.harga) - discVal;
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
            this.receive.total_diskon = this.receive.total_diskon + diskon;
            this.receive.subtotal = this.receive.subtotal + subtotal;
            this.receive.total_pajak = this.receive.total_pajak + pajak;
            this.receive.total = this.receive.total + pajak + subtotal; 
        },

        removePurchaseReturn(){
            this.CLR_ERRORS();
            if(this.receive.status !== 'DRAFT') {
                alert('Status sudah tidak di DRAFT. data tidak dapat dihapus.'); return false;
            }

            if(confirm(`Proses ini akan menghapus penerimaan. Lanjutkan ?`)){
                this.deletePORR(this.receive.trx_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.closeModal();
                    }
                    else { alert (response.message) }
                });
            };
        },

        refreshPurchaseReturn() {
            if(this.isUpdate){ this.viewReceive(this.receive); }
            else {
                this.isUpdate = false; this.clearReceive(); this.CLR_ERRORS();
            }
        },

        approvePurchaseReturn(){
            if(this.receive.status !== 'DRAFT') {
                alert('Status sudah tidak di DRAFT. data tidak dapat dihapus.'); return false;
            }

            if(confirm(`Proses ini akan mengubah jumlah persediaan. Proses tidak dapat dibatalkan. Lanjutkan ?`)){
                this.approvePORR(this.receive.trx_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.closeModal();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>


<!-- <template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formPurchaseReturn" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitPORR" style="padding:0;">
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">PENGEMBALIAN PEMBELIAN PERSEDIAAN</h5>
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal"
                                class="uk-button uk-button-default uk-button-medium"
                                style="font-size:12px;">Tutup</button>
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit"
                                class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large"
                                style="font-size:12px;">Simpan</button>
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>

                    <div class="uk-grid-small hims-form-subpage" uk-grid>
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PENGEMBALIAN PEMBELIAN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                data pembelian.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                            <div class="uk-width-1-2@m">
                                <search-select :config=configSelect2 searchUrl="/vendors" label="Vendor*" placeholder=""
                                    captionField="vendor_nama" valueField="vendor_id" :itemListData=vendorDesc
                                    :value="pengembalian.vendor_nama" v-on:item-select="vendorSelected"></search-select>
                            </div>
                            
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal
                                    Dibutuhkan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pengembalian.tgl_dibutuhkan"
                                        type="date" placeholder="tanggal dibutuhkan"
                                        required disabled>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <search-select :config=configSelect searchUrl="/units" label="Unit*"
                                    placeholder="Poli Gigi, Poli Mata, etc.." captionField="unit_nama"
                                    valueField="unit_id" :itemListData=unitDesc :value="pengembalian.unit_id"
                                    v-on:item-select="unitSelected"></search-select>
                            </div>
                            <div class="uk-width-1-2@m">
                                <search-select :config=configSelect searchUrl="/depos" label="Depo*"
                                    placeholder="Depo Poli Gigi, Depo Poli Mata, etc.." captionField="depo_nama"
                                    valueField="depo_id" :itemListData=depoDesc :value="pengembalian.depo_id"
                                    v-on:item-select="depoSelected"></search-select>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Rencana
                                    Bayar*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pengembalian.tgl_rencana_bayar"
                                        type="date" placeholder="tanggal rencana bayar"
                                        required disabled>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea" v-model="pengembalian.catatan" type="text"
                                        required disabled>{{pengembalian.catatan}}</textarea>
                                </div>
                            </div>
                            <div v-if="isUpdate" class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                <label style="padding:0;margin:0;font-size:14px;color:black;"><input class="uk-checkbox"
                                        type="checkbox" v-model="pengembalian.is_aktif" style="margin-left:5px;"> Data pengembalian
                                    aktif</label>
                            </div>
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alasan</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea" v-model="pengembalian.alasan" type="text"
                                        required :disabled="isUpdate">{{pengembalian.catatan}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid>
                        <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DETAIL PEMBELIAN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                data detail pembelian.
                            </p>
                        </div>
                        <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                            <table class="uk-table uk-table-striped">
                                <thead>
                                    <th
                                        style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">
                                        Jenis Produk</th>
                                    <th
                                        style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">
                                        Nama Produk</th>
                                    <th
                                        style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">
                                        Jumlah Diterima</th>
                                    <th
                                        style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">
                                        Jumlah Pengembalian</th>
                                    <th
                                        style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">
                                        Satuan</th>
                                    <th
                                        style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">
                                        Harga</th>
                                    <th
                                        style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">
                                        Subtotal</th>
                                </thead>
                                <tbody>
                                    <tr v-for="dt in pengembalian.detail_retur"
                                        v-bind:class="dt.is_aktif != true ? 'inaktif':'' ">
                                        <td
                                            style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:80px;font-weight:500;">
                                            {{dt.jenis_produk}}</td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.produk_id}}
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.jumlah_diterima}}
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:50px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small"
                                                    type="number" v-model="dt.jumlah_return" :disabled="isUpdate">
                                            </div>
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.satuan}}
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.harga}}</td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.subtotal}}
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

export default {
    name: 'form-purchase-return',
    components: { SearchSelect },
    data() {
        return {
            configSelect: {
                required: false,
                disabled: true,
                compClass: "uk-width-1-1@m",
                compStyle: "padding:0;margin:0;",
            },
            configSelect2: {
                required: true,
                disabled: true,
                compClass: "uk-width-1-1@m",
                compStyle: "padding:0;margin:0;",
            },
            unitDesc: [
                { colName: 'unit_nama', labelData: '', isTitle: true },
                { colName: 'unit_id', labelData: '', isTitle: false },
            ],
            prDesc: [
                { colName: 'pengadaan_id', labelData: '', isTitle: true },
                { colName: 'pengadaan_id', labelData: '', isTitle: false },
            ],
            vendorDesc: [
                { colName: 'vendor_nama', labelData: '', isTitle: true },
                { colName: 'vendor_id', labelData: '', isTitle: false },
            ],
            depoDesc: [
                { colName: 'depo_nama', labelData: '', isTitle: true },
                { colName: 'depo_id', labelData: '', isTitle: false },
            ],
            produkDesc: [
                { colName: 'produk_nama', labelData: '', isTitle: true },
                { colName: 'produk_id', labelData: '', isTitle: false },
            ],

            isUpdate: false,
            pengembalian: {
                client_id: null,
                pengadaan_id: null,
                pr_id: null,
                status: 'DRAFT',
                tgl_transaksi: null,
                tgl_dibutuhkan: null,
                unit_id: null,
                unit_nama: null,
                depo_id: null,
                depo_nama: null,
                vendor_id: null,
                vendor_nama: null,
                catatan: null,
                alasan: null,
                is_aktif: true,
                detail_retur: []
            },
            detailReturn: {
                jenis_produk: '',
                produk_id: '',
                produk_nama: '',
                jumlah_diterima: '',
                jumlah_return: '',
                satuan: '',
                harga: '',
                subtotal: '',
                total: ''
            },
            selectedDetail: [],
            urlSearch: ''
        }
    },
    computed: {
        ...mapGetters(['errors']),
        ...mapGetters('unitPelayanan', ['unitLists']),
        ...mapGetters('refProduk', ['jenisProdukRefs', 'isRefProdukExist']),
    },

    mounted() {
        this.init();
    },

    methods: {
        ...mapActions('purchaseReturn', ['updatePORR', 'createPORR', 'dataPORR']),
        ...mapActions('purchaseRequest', ['dataPR']),
        ...mapActions('unitPelayanan', ['listUnitPelayanan']),
        ...mapActions('refProduk', ['updateRefProduk', 'listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        init() {
            if (!this.isRefProdukExist) {
                this.listRefProduk();
            };
        },

        closeModal() {
            this.$emit('closed', true);
            this.clearProduk();
            UIkit.modal('#formPurchaseReturn').hide();
            return false;
        },

        unitSelected(data) {
            if (data) {
                this.pengembalian.unit_id = data.unit_id;
                this.pengembalian.unit_nama = data.unit_nama;
            }
        },

        depoSelected(data) {
            if (data) {
                this.pengembalian.depo_id = data.depo_id;
                this.pengembalian.depo_nama = data.depo_nama;
            }
        },

        vendorSelected(data) {
            if (data) {
                this.pengembalian.vendor_id = data.vendor_id;
                this.pengembalian.vendor_nama = data.vendor_nama;
            }
        },

        jenisSelected(event) {
            var value = event.target.options[event.target.options.selectedIndex].value;
            this.detailReturn.produk_id = value;
            this.getProdukData();
        },

        getProdukData() {
            let produk_id = ''
            if (this.detailReturn.produk_id == '') {
                produk_id = document.getElementById("jenis_produk").value;
            } else {
                produk_id = this.detailReturn.produk_id;
            }

            if (produk_id == 'MEDIS') {
                produk_id = 'medical';
            } else if (produk_id == 'UMUM') {
                produk_id = 'general';
            } else if (produk_id == 'JASA') {
                produk_id = 'service';
            } else {
                produk_id = 'kitchen';
            }

            this.urlSearch = `products/${produk_id}/items`
        },

        produkSelected(data) {
            if (data) {
                this.detailReturn.produk_id = data.produk_id;
                this.detailReturn.produk_nama = data.produk_nama;
                this.detailReturn.satuan = data.satuan_beli;
                this.detailReturn.harga = data.harga_beli;
            }
        },

        getDate(params){
            var date = new Date(params);
            var outDate = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();
            return outDate;
        },

        viewPO(pengadaan_id) {
            this.clearProduk();
            this.dataPORR(pengadaan_id).then((response) => {
                if (response.success == true) {
                    this.pengembalian.client_id = null;
                    this.pengembalian.pengadaan_id = response.data[0].pengadaan_id;
                    this.pengembalian.tgl_transaksi = response.data[0].tgl_transaksi;
                    this.pengembalian.tgl_dibutuhkan = response.data[0].tgl_dibutuhkan;
                    this.pengembalian.tgl_rencana_bayar = response.data[0].tgl_rencana_bayar;
                    this.pengembalian.unit_id = response.data[0].unit_id;
                    this.pengembalian.unit_nama = response.data[0].unit_nama;
                    this.pengembalian.depo_id = response.data[0].depo_id;
                    this.pengembalian.depo_nama = response.data[0].depo_nama;
                    this.pengembalian.vendor_id = response.data[0].vendor_id;
                    this.pengembalian.vendor_nama = response.data[0].vendor_nama;
                    this.pengembalian.status = response.data[0].status;
                    this.pengembalian.catatan = response.data[0].catatan;
                    this.pengembalian.is_aktif = true;
                    this.configSelect.disabled = true;
                    this.pengembalian.detail_retur = [];
                    if (response.data.length > 0) {
                        for (let i = 0; i <= response.data.length - 1; i++) {
                            this.pengembalian.detail_retur.push({
                                "is_selected": false,
                                "jumlah_diterima": response.data[i]['jml_por'],
                                "satuan": response.data[i]['satuan_beli_id'],
                                "produk_id": response.data[i]['produk_id'],
                                "produk_nama": response.data[i]['produk_nama'],
                                "jenis_produk": response.data[i]['jenis_produk'],
                                "harga": response.data[i]['harga'],
                                "subtotal": response.data[i]['jml_por'] * response.data[i]['harga'],
                            });
                        }
                    }
                    // this.isUpdate = true;
                    UIkit.modal('#formPurchaseReturn').show();
                } else {
                    alert(response.message);
                }
            })
        },

        submitPORR() {
            this.CLR_ERRORS();
            this.updatePORR(this.pengembalian).then((response) => {
                if (response.success == true) {
                    alert(" data berhasil diubah.");
                    this.clearProduk();
                    this.$emit('saveSucceed', true);
                    this.isUpdate = false;
                    this.closeModal();
                    UIkit.modal('#formPurchaseReturn').hide();
                }
            })
        },

        clearProduk() {
            this.pengembalian.client_id = null;
            this.pengembalian.pengadaan_id = null;
            this.pengembalian.pr_id = null;
            this.pengembalian.tgl_transaksi = null;
            this.pengembalian.tgl_dibutuhkan = null;
            this.pengembalian.unit_id = null;
            this.pengembalian.unit_nama = null;
            this.pengembalian.depo_id = null;
            this.pengembalian.depo_nama = null;
            this.pengembalian.status = null;
            this.pengembalian.vendor_id = null;
            this.pengembalian.vendor_nama = null;
            this.pengembalian.catatan = null;
            this.pengembalian.is_aktif = true;
            this.pengembalian.detail_retur = null;
            this.detailReturn.jenis_produk = null;
            this.detailReturn.produk_id = null;
            this.detailReturn.jumlah_diterima = null;
            this.detailReturn.harga = null;
        },
    }
}
</script>

<style>
/* .uk-input,
.uk-textarea,
.uk-checkbox {
    breceive: 1px solid #999;
}

.hims-form-container label {
    color: #333;
    font-size: 12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    breceive: 2px solid #cc0202;
}

.uk-button {
    breceive-radius: 50px;
    breceive: 2px solid #aaa;
    font-weight: 400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    breceive: 2px solid #cc0202;
    font-weight: bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    breceive: 2px solid #cc0202;
    font-weight: bold;
}

.hims-accordion-title {
    breceive-bottom: 1px solid #cc0202;
    font-size: 14px;
    font-weight: 500;
    background-color: #cc0202;
    color: white;
    padding: 0.5em 1em 0.5em 1em;
    breceive-radius: 5px;
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
    breceive-top: 1px solid #cc0202;
} */
</style> -->