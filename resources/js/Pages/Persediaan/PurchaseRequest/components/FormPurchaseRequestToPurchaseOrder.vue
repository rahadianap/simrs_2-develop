<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formPurchaseRequestToPurchaseOrder" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitPO" style="padding:0;">
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
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Purchase Request</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="order.pr_id" type="text" placeholder="Purchase Request ID" disabled style="color:black;">
                                </div>
                            </div>
                            <!-- <div class="uk-width-1-1@m">
                                <search-select
                                    :config = configSelect
                                    searchUrl = "/purchases/requests"
                                    label = "Purchase Request"
                                    placeholder = ""
                                    captionField = "pengadaan_id"
                                    valueField = "pengadaan_id"
                                    :itemListData = prDesc
                                    :value = "order.pengadaan_id"
                                    v-on:item-select = "prSelected"
                                ></search-select>
                            </div> -->
                            
                            <!-- <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal
                                    Transaksi*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="order.tgl_transaksi"
                                        type="date" placeholder="tanggal transaksi"
                                        required readonly :disabled="isUpdate">
                                </div>
                            </div> -->
                            <div class="uk-width-1-2@m">
                                <search-select :config=configSelect searchUrl="/units" label="Unit*"
                                    placeholder="Poli Gigi, Poli Mata, etc.." captionField="unit_nama"
                                    valueField="unit_id" :itemListData=unitDesc :value="order.unit_id"
                                    ></search-select>
                            </div>
                            <div class="uk-width-1-2@m">
                                <search-select :config=configSelect searchUrl="/depos" label="Depo*"
                                    placeholder="Depo Poli Gigi, Depo Poli Mata, etc.." captionField="depo_nama"
                                    valueField="depo_id" :itemListData=depoDesc :value="order.depo_id"
                                    ></search-select>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Dibutuhkan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="order.tgl_dibutuhkan"
                                        type="date" placeholder="tanggal dibutuhkan"
                                        required readonly :disabled="isUpdate">
                                </div>
                            </div>
                            <!-- <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Rencana Bayar*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="order.tgl_rencana_bayar"
                                        type="date" placeholder="tanggal rencana bayar"
                                        required>
                                </div>
                            </div> -->
                            
                            <div class="uk-width-3-4@m">
                                <search-select :config=configSelect2 searchUrl="/vendors" label="Vendor*" placeholder=""
                                    captionField="vendor_nama" valueField="vendor_id" :itemListData=vendorDesc
                                    :value="order.vendor_nama" v-on:item-select="vendorSelected"></search-select>
                            </div>

                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
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
                                                    class="uk-select uk-form-small" @change="jenisSelected($event)" disabled>
                                                    <option v-if="isRefProdukExist"
                                                        v-for="dt in jenisProdukRefs.json_data" :value="dt.value">
                                                        {{dt.value}}</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:200px;">
                                            <div class="uk-form-controls">
                                                <search-select :config=configSelect :searchUrl=urlSearch
                                                    placeholder="Paracetamol 500mg, Kertas HVS, etc..."
                                                    captionField="produk_nama" valueField="produk_id"
                                                    :itemListData=produkDesc :value=detailPO.produk_id
                                                    ></search-select>
                                            </div>
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                            <div class="uk-form-controls">
                                                <input v-on:input="countSubtotal()" class="uk-input uk-form-small"
                                                    type="number" v-model="detailPO.jumlah" disabled>
                                            </div>
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text"
                                                    v-model="detailPO.satuan" readonly disabled>
                                            </div>
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="number"
                                                    v-model="detailPO.harga" readonly disabled>
                                            </div>
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:50px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="number"
                                                    v-model="detailPO.subtotal" readonly disabled>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>Select All
                                        <td style="padding:0.5em;margin-top:0.5em;color:black;font-size:12px;text-align:left;font-weight:500;">
                                            <input class="uk-checkbox" @change="selectAll()" type="checkbox"
                                                v-model="detailPO.is_selected_all" :value="detailPO.is_selected_all">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;">
                                            <input class="uk-checkbox" @change="detailSelected()" type="checkbox" v-model="dt.is_selected" :value="dt.is_selected">
                                        </td>
                                    </tr>
                                    <tr style="padding:0.5em;margin:0;color:black;font-weight:500;">Total:
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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
    name: 'form-purchase-request-to-purchase-order',
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
                tgl_transaksi: null,
                tgl_dibutuhkan: null,
                tgl_rencana_bayar: null,
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
                is_selected: false,
                is_selected_all: false,
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
        ...mapActions('purchaseOrder', ['createPOFromPR', 'dataPO', 'updatePO']),
        ...mapActions('purchaseRequest', ['dataPR']),
        ...mapActions('unitPelayanan', ['listUnitPelayanan']),
        ...mapActions('refProduk', ['updateRefProduk', 'listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        init() {
            if (!this.isRefProdukExist) {
                this.listRefProduk();
            };
        },

        selectAll() {
            for(var i in this.order.detail_po) {
                this.order.detail_po[i].is_selected = true;
                this.order.detail_po[i].is_selected_all = true;
            }
            this.selectedDetail.push({
                "pr_id": this.order.pr_id,
                "vendor_id": this.order.vendor_id,
                "vendor_nama": this.order.vendor_nama,
                "tgl_transaksi": this.order.tgl_transaksi,
                "tgl_dibutuhkan": this.order.tgl_dibutuhkan,
                "unit_id": this.order.unit_id,
                "unit_nama": this.order.unit_nama,
                "depo_id": this.order.depo_id,
                "depo_nama": this.order.depo_nama,
                "tgl_rencana_bayar": this.order.tgl_rencana_bayar,
                "catatan": this.order.catatan,
                "status": this.order.status,
                "is_aktif": this.order.is_aktif,
                // "detail_po": JSON.parse(JSON.stringify(this.order.detail_po.filter(x => x.is_selected))),
                "detail_po": this.order.detail_po.filter(x => x.is_selected),
            });
        },

        detailSelected() {
            this.selectedDetail.push({
                "pr_id": this.order.pr_id,
                "vendor_id": this.order.vendor_id,
                "vendor_nama": this.order.vendor_nama,
                "tgl_transaksi": this.order.tgl_transaksi,
                "tgl_dibutuhkan": this.order.tgl_dibutuhkan,
                "unit_id": this.order.unit_id,
                "unit_nama": this.order.unit_nama,
                "depo_id": this.order.depo_id,
                "depo_nama": this.order.depo_nama,
                "tgl_rencana_bayar": this.order.tgl_rencana_bayar,
                "catatan": this.order.catatan,
                "status": this.order.status,
                "is_aktif": this.order.is_aktif,
                // "detail_po": JSON.parse(JSON.stringify(this.order.detail_po.filter(x => x.is_selected))),
                "detail_po": this.order.detail_po.filter(x => x.is_selected),
            });
        },

        vendorSelected(data) {
            if (data) {
                this.order.vendor_id = data.vendor_id;
                this.order.vendor_nama = data.vendor_nama;
            }
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
            // this.detailPO.total = new Intl.NumberFormat('id-id', { style: 'currency', currency: 'IDR' }).format(subTotalRsp);
            this.detailPO.total = subTotalRsp.toString();
        },

        closeModal() {
            this.$emit('closed', true);
            this.clearProduk();
            UIkit.modal('#formPurchaseRequestToPurchaseOrder').hide();
            return false;
        },

        submitPO() {
            this.CLR_ERRORS();
            if (!this.isUpdate) {
                this.create(this.selectedDetail).then((response) => {
                    if (response.success == true) {
                        alert(" data baru berhasil dibuat.");
                        this.clearProduk();
                        this.$emit('saveSucceed', true);
                        this.isUpdate = false;
                        UIkit.modal('#formPurchaseRequestToPurchaseOrder').hide();
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
                        UIkit.modal('#formPurchaseRequestToPurchaseOrder').hide();
                    }
                })
            }
        },

        newPurchaseOrderPR(pr_id) {
            alert(pr_id);
            this.clearProduk();
            this.dataPR(pr_id).then((response) => {
                if (response.success == true) {
                    console.log(response.data);
                    this.order.client_id = null;
                    this.order.pr_id = response.data.pr_id;
                    this.order.tgl_transaksi = response.data.tgl_pr;
                    this.order.tgl_dibutuhkan = response.data.tgl_kebutuhan;
                    this.order.unit_id = response.data.unit_id;
                    this.order.unit_nama = response.data.unit_nama;
                    this.order.depo_id = response.data.depo_id;
                    this.order.depo_nama = response.data.depo_nama;
                    this.order.status = response.data.status;
                    this.order.is_aktif = true;
                    this.order.detail_po = [];
                    this.selectedDetail = [];
                    if (response.data.length > 0) {
                        for (let i = 0; i <= response.data.length - 1; i++) {
                            this.order.detail_po.push({
                                "is_selected_all": false,
                                "is_selected": false,
                                "jumlah": response.data[i]['jml_pr'],
                                "satuan": response.data[i]['pr_satuan_id'],
                                "produk_id": response.data[i]['produk_id'],
                                "produk_nama": response.data[i]['produk_nama'],
                                "jenis_produk": response.data[i]['jenis_produk'],
                                "harga": response.data[i]['harga_beli'],
                                "subtotal": response.data[i]['jml_pr'] * response.data[i]['harga_beli'],
                            });
                            this.countTotal();
                            this.order.detail_po[0].total = this.detailPO.total;
                        }
                    }
                    // this.isUpdate = true;
                    UIkit.modal('#formPurchaseRequestToPurchaseOrder').show();
                } else {
                    alert(response.message);
                }
            })
        },

        clearProduk() {
            this.order.client_id = null;
            this.order.pengadaan_id = null;
            this.order.pr_id = null;
            this.order.tgl_transaksi = null;
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
    }
}
</script>
