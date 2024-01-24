<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formReqDetailToOrder" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitPO2" style="padding:0;">
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">PEMBELIAN PERSEDIAAN</h5>
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
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PEMBELIAN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                data pembelian.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                            <div class="uk-width-1-2@m">
                                <search-select :config=configSelect2 searchUrl="/vendors" label="Vendor*" placeholder=""
                                    captionField="vendor_nama" valueField="vendor_id" :itemListData=vendorDesc
                                    :value="order.vendor_nama" v-on:item-select="vendorSelected"></search-select>
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
                            <div class="uk-width-1-2@m">
                                <search-select 
                                    :config=configSelect 
                                    searchUrl="/units" 
                                    label="Unit*"
                                    placeholder="Poli Gigi, Poli Mata, etc.." 
                                    captionField="unit_nama"
                                    valueField="unit_id" 
                                    :itemListData=unitDesc 
                                    :value="order.unit_id"
                                    v-on:item-select="unitSelected">
                                </search-select>
                            </div>
                            <div class="uk-width-1-2@m">
                                <search-select 
                                    :config=configSelect 
                                    searchUrl="/depos" 
                                    label="Depo*"
                                    placeholder="Depo Poli Gigi, Depo Poli Mata, etc.." 
                                    captionField="depo_nama"
                                    valueField="depo_id" 
                                    :itemListData=depoDesc 
                                    :value="order.depo_id"
                                    v-on:item-select="depoSelected">
                                </search-select>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Rencana Bayar</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="order.tgl_rencana_bayar" type="date" placeholder="tanggal rencana bayar">
                                </div>
                            </div>
                            <div class="uk-width-3-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="order.catatan" type="text" required>
                                </div>
                            </div>
                            <div v-if="isUpdate" class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                <label style="padding:0;margin:0;font-size:14px;color:black;"><input class="uk-checkbox"
                                        type="checkbox" v-model="order.is_aktif" style="margin-left:5px;"> Data order
                                    aktif</label>
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
                            <table class="uk-table hims-table uk-table-striped">
                                <thead>
                                    <th>Jenis Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                    <th>...</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:100px;">
                                            <div class="uk-form-controls">
                                                <select v-model="detailPO.jenis_produk"
                                                    class="uk-select uk-form-small" @change="jenisSelected($event)">
                                                    <option v-if="isRefProdukExist" v-for="dt in jenisProdukRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:200px;">
                                            <div class="uk-form-controls">
                                                <search-select 
                                                    :config=configSelect 
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
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                            <div class="uk-form-controls">
                                                <input v-on:input="countSubtotal()" class="uk-input uk-form-small" type="number" v-model="detailPO.jumlah" :disabled="isUpdate">
                                            </div>
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" v-model="detailPO.satuan" readonly :disabled="isUpdate">
                                            </div>
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="number" v-model="detailPO.harga" readonly :disabled="isUpdate">
                                            </div>
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="number" v-model="detailPO.subtotal" readonly :disabled="isUpdate">
                                            </div>
                                        </td>
                                        <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                            <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addDetailPO" style="padding:0;margin:0;color:blue;display:inline-block;" :disabled="isUpdate"></a>
                                        </td>
                                    </tr>

                                    <tr v-for="dt in order.detail_po"
                                        v-bind:class="dt.is_aktif != true ? 'inaktif':'' ">
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:80px;font-weight:500;">
                                            {{dt.jenis_produk}}
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                                            {{dt.produk_nama}}
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                                            {{dt.jumlah}}
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                                            {{dt.satuan}}
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                                            {{dt.harga}}
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                                            {{dt.subtotal}}
                                        </td>
                                        <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                            <a href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="removeDetailPO(dt['index'])" style="padding:0;margin:0;color:red;display:inline-block;" :disabled="isUpdate"></a>
                                        </td>
                                    </tr>
                                    <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">
                                        <td colspan="4" style="font-size:1.2em;padding:0.2em;margin:0;">Total:</td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:14px;font-weight:500;">{{detailPO.total}}</td>
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
    name: 'form-req-detail-to-order',
    components: { SearchSelect },
    data() {
        return {
            configSelect: {
                required: false,
                compClass: "uk-width-1-1@m",
                compStyle: "padding:0;margin:0;",
            },
            configSelect2: {
                required: true,
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
            order: {
                client_id: null,
                pengadaan_id: null,
                pr_id: null,
                status: 'DRAFT',
                tgl_transaksi: new Date().toISOString().slice(0,10),
                tgl_dibutuhkan: null,
                unit_id: null,
                unit_nama: null,
                depo_id: null,
                depo_nama: null,
                vendor_id: null,
                vendor_nama: null,
                catatan: null,
                is_aktif: true,
                detail_po: []
            },
            detailPO: {
                jenis_produk: '',
                produk_id: '',
                produk_nama: '',
                jumlah: '',
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
        ...mapActions('purchaseOrder', ['createPO', 'dataPO', 'updatePO']),
        ...mapActions('purchaseRequest', ['dataPR']),
        ...mapActions('unitPelayanan', ['listUnitPelayanan']),
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



        init() {
            if (!this.isRefProdukExist) {
                this.listRefProduk();
            };
        },

        countSubtotal() {
            this.detailPO.subtotal = this.detailPO.jumlah * this.detailPO.harga;
        },

        countTotal() {
            let subTotalRsp = 0;
            for (var a = 0; a < this.order.detail_po.length; a++) {
                if(this.order.detail_po[a]["subtotal"]){
                    subTotalRsp += Number(this.order.detail_po[a]["subtotal"]);
                }
            }
            this.detailPO.total = subTotalRsp.toString();
            return this.detailPO.total;
        },

        closeModal() {
            this.$emit('closed', true);
            this.clearProduk();
            UIkit.modal('#formPurchaseOrder').hide();
            return false;
        },

        unitSelected(data) {
            if (data) {
                this.order.unit_id = data.unit_id;
                this.order.unit_nama = data.unit_nama;
            }
        },

        depoSelected(data) {
            if (data) {
                this.order.depo_id = data.depo_id;
                this.order.depo_nama = data.depo_nama;
            }
        },

        vendorSelected(data) {
            if (data) {
                this.order.vendor_id = data.vendor_id;
                this.order.vendor_nama = data.vendor_nama;
            }
        },

        jenisSelected(event) {
            var value = event.target.options[event.target.options.selectedIndex].value;
            this.detailPO.produk_id = value;
            this.getProdukData();
        },

        getProdukData() {
            let produk_id = ''
            if (this.detailPO.produk_id == '') {
                produk_id = document.getElementById("jenis_produk").value;
            } else {
                produk_id = this.detailPO.produk_id;
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
                this.detailPO.produk_id = data.produk_id;
                this.detailPO.produk_nama = data.produk_nama;
                this.detailPO.satuan = data.satuan_beli;
                this.detailPO.harga = data.harga_beli;
            }
        },

        getDate(params){
            var date = new Date(params);
            var outDate = date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();
            return outDate;
        },

        viewPO(pengadaan_id) {
            this.clearProduk();
            this.dataPO(pengadaan_id).then((response) => {
                if (response.success == true) {
                    this.order.client_id = null;
                    if(response.data[0].reff_trx_id != null){
                        this.order.pr_id = response.data[0].reff_trx_id;
                    }else{
                        this.order.pr_id = null;
                    }
                    this.order.pengadaan_id = response.data[0].pengadaan_id;
                    this.order.tgl_transaksi = this.getDate(response.data[0].tgl_transaksi);
                    this.order.tgl_dibutuhkan = this.getDate(response.data[0].tgl_dibutuhkan);
                    this.order.tgl_rencana_bayar = this.getDate(response.data[0].tgl_rencana_bayar);
                    this.order.unit_id = response.data[0].unit_id;
                    this.order.unit_nama = response.data[0].unit_nama;
                    this.order.depo_id = response.data[0].depo_id;
                    this.order.depo_nama = response.data[0].depo_nama;
                    this.order.vendor_id = response.data[0].vendor_id;
                    this.order.vendor_nama = response.data[0].vendor_nama;
                    this.order.status = response.data[0].status;
                    this.order.catatan = response.data[0].catatan;
                    this.order.is_aktif = true;
                    this.configSelect.disabled = true;
                    this.order.detail_po = [];
                    if (response.data.length > 0) {
                        for (let i = 0; i <= response.data.length - 1; i++) {
                            this.order.detail_po.push({
                                "jumlah": response.data[i]['jml_po'],
                                "satuan": response.data[i]['satuan_beli_id'],
                                "produk_id": response.data[i]['produk_nama'],
                                "jenis_produk": response.data[i]['jenis_produk'],
                                "harga": response.data[i]['harga_beli'],
                                "subtotal": response.data[i]['jml_po'] * response.data[i]['harga_beli'],
                            });
                        }
                    }
                    // this.isUpdate = true;
                    UIkit.modal('#formPurchaseOrder').show();
                } else {
                    alert(response.message);
                }
            })
        },

        submitPO2() {
            this.CLR_ERRORS();
            if (!this.isUpdate) {
                this.createPO(this.order).then((response) => {
                    if (response.success == true) {
                        alert(" data baru berhasil dibuat.");
                        this.clearProduk();
                        this.$emit('saveSucceed', true);
                        this.isUpdate = false;
                        UIkit.modal('#formPurchaseOrder').hide();
                    }
                })
            }
            else {
                this.updatePO(this.order).then((response) => {
                    if (response.success == true) {
                        alert(" data berhasil diubah.");
                        this.clearProduk();
                        this.$emit('saveSucceed', true);
                        this.isUpdate = false;
                        this.closeModal();
                        UIkit.modal('#formPurchaseOrder').hide();
                    }
                })
            }
        },

        newPurchaseOrder() {
            this.CLR_ERRORS();
            this.clearProduk();
            this.isUpdate = false;
            UIkit.modal('#formReqDetailToOrder').show();
        },

        removeDetailPO(val){
            for (var i = 0; i < this.req.detail_po.length; i++) {
                if(this.req.detail_po[i]["index"] === val) {
                    this.req.detail_po.splice(i, 1);
                }
            }
            this.detailReq.jenis_produk = null;
            this.detailReq.produk_id = null;
            this.detailReq.produk_nama = null;
            this.detailReq.jumlah = null;
            this.detailReq.satuan = null;
            this.detailReq.harga = null;
            this.detailReq.subtotal = null;
        },

        clearProduk() {
            this.order.client_id = null;
            this.order.pengadaan_id = null;
            this.order.pr_id = null;
            this.order.tgl_transaksi = this.getTodayDate();
            this.order.tgl_dibutuhkan = null;
            this.order.unit_id = null;
            this.order.unit_nama = null;
            this.order.depo_id = null;
            this.order.depo_nama = null;
            this.order.status = null;
            this.order.vendor_id = null;
            this.order.vendor_nama = null;
            this.order.catatan = null;
            this.order.is_aktif = true;
            this.order.detail_po = null;
            this.detailPO.jenis_produk = null;
            this.detailPO.produk_id = null;
            this.detailPO.jumlah = null;
            this.detailPO.harga = null;
        },

        addDetailPO() {
            if (this.detailPO.produk_id == null || this.detailPO.produk_id == '' || this.detailPO.jumlah == null || this.detailPO.jumlah == '' || this.detailPO.jenis_produk == null || this.detailPO.jenis_produk == '') {
                alert('Data tidak lengkap.');
                return false;
            }
            else {
                this.order.detail_po.push(JSON.parse(JSON.stringify(this.detailPO)));
                this.countTotal();
                this.detailPO.jenis_produk = null;
                this.detailPO.produk_id = null;
                this.detailPO.produk_nama = null;
                this.detailPO.jumlah = null;
                this.detailPO.satuan = null;
                this.detailPO.harga = null;
                this.detailPO.subtotal = null;
            }
        },
    }
}
</script>

<style>

</style>