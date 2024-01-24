<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:true;" :id="modalId" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">RESEP</h2>
            <div style="padding:0;margin:0;">
                <form action="" @submit.prevent="submitResep">
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 0 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">RESEP DAN OBAT</h5>
                            <!-- <p style="font-size:12px;font-style:italic;padding:0;margin:0;">catatan ICD Diagnosa.</p> -->
                        </div>
                        <div v-if="itemResep.is_racikan == false" class="uk-width-1-1@s uk-grid-small" uk-grid> 
                            <div class="uk-width-1-1@m">
                                <div style="padding:0 0 0.2em 0;">
                                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-reg" style="text-align:center;color:white;">Racik</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">Item Resep</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">Cara Pakai</th>
                                            <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Jumlah</th>
                                            <th class="tb-header-reg" style="text-align:center;color:white;">Satuan</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Harga</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>
                                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding:1em; font-size: 12px; text-align:center; color:black;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="itemResep.is_racikan" style="text-align: center;" @change="racikanChange">
                                                </td>
                                                <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
                                                    <search-select  ref="searchObat"
                                                        :config = configSelect
                                                        searchUrl = '/products/medical/items'
                                                        placeholder = "pilih item obat"
                                                        captionField = "produk_nama"
                                                        valueField = "produk_nama"
                                                        :itemListData = produkDesc
                                                        :value = "itemResep.produk_nama"
                                                        v-on:item-select = "itemSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; color:black;">
                                                    <search-select  ref="searchSigna"
                                                        :config = configSelect
                                                        searchUrl = '/signas'
                                                        placeholder = "pilih signa"
                                                        captionField = "signa"
                                                        valueField = "signa"
                                                        :itemListData = signaDesc
                                                        :value = "itemResep.signa"
                                                        v-on:item-select = "signaSelected"
                                                    ></search-select>
                                                </td>

                                                <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <input id="jumlah" v-model="itemResep.jumlah" class="uk-input uk-form-small" type="number" style="text-align: center;" @change="onChangeJumlah">
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <search-select  ref="searchSatuan"
                                                        :config = configSelect
                                                        searchUrl = '/uoms'
                                                        placeholder = "pilih satuan"
                                                        captionField = "satuan_id"
                                                        valueField = "satuan_id"
                                                        :itemListData = satuanDesc
                                                        :value = "itemResep.satuan"
                                                        v-on:item-select = "satuanSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemResep.harga)}}</td>
                                                <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemResep.subtotal)}}</td>
                                                
                                                <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                    <button type="button" @click.prevent="insertResep">Tambah</button>
                                                </td>
                                            </tr>
                                            <template v-for="dt in prescriptionItems">
                                                <tr v-if="dt.is_aktif">
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">

                                                    </td>
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dt.produk_nama}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black;">
                                                        <p style="margin:0;padding:0;font-weight: 400;">
                                                            <strong>{{ dt.signa }}</strong> {{ dt.signa_deskripsi }}
                                                        </p>
                                                    </td>        
                                                    <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                        <input class="uk-input uk-form-small" type="number" v-model="dt.jumlah" style="text-align: center;">
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black; text-align:center;">
                                                        <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;">{{dt.satuan}}</p>
                                                    </td>
                                                    <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.harga)}}</td>
                                                    <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.subtotal)}}</td>

                                                    <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" style="text-align: center;" @change="activationChange">
                                                    </td>
                                                </tr>
                                            </template>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div v-else class="uk-width-1-1@s uk-grid-small" uk-grid> 
                            <div class="uk-width-1-1@m">
                                <div style="padding:0 0 0.2em 0;">
                                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-reg" style="text-align:center;color:white;">Racik2</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Group</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">Komposisi</th>
                                            <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Jumlah Komposisi</th>
                                            <th class="tb-header-reg" style="text-align:center;color:white;">Satuan Komposisi</th>
                                            <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Jumlah Hasil</th>
                                            <th class="tb-header-reg" style="text-align:center;color:white;">Satuan Hasil</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">Cara Pakai</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Harga</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>
                                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding:1em; font-size: 12px; text-align:center; color:black;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="itemResep.is_racikan" style="text-align: center;" @change="racikanChange">
                                                </td>
                                                <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
                                                    <select v-model="itemRacikan.no_racikan" class="uk-select uk-form-small" id="form-horizontal-select">
                                                        <option value="racikan_01">Racikan 01</option>
                                                        <option value="racikan_02">Racikan 02</option>
                                                        <option value="racikan_03">Racikan 03</option>
                                                        <option value="racikan_04">Racikan 04</option>
                                                        <option value="racikan_05">Racikan 05</option>
                                                        <option value="racikan_06">Racikan 06</option>
                                                        <option value="racikan_07">Racikan 07</option>
                                                        <option value="racikan_08">Racikan 08</option>
                                                        <option value="racikan_09">Racikan 09</option>
                                                        <option value="racikan_10">Racikan 10</option>
                                                    </select>
                                                </td>
                                                <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
                                                    <search-select  ref="searchObat"
                                                        :config = configSelect
                                                        searchUrl = '/products/medical/items'
                                                        placeholder = "pilih item obat"
                                                        captionField = "produk_nama"
                                                        valueField = "produk_nama"
                                                        :itemListData = produkDesc
                                                        :value = "itemRacikan.produk_nama"
                                                        v-on:item-select = "itemRacikanSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <input id="jumlah" v-model="itemRacikan.jumlah_komposisi" class="uk-input uk-form-small" type="number" style="text-align: center;">
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <select v-model="itemRacikan.satuan_komposisi" class="uk-select uk-form-small">
                                                        <option v-if="isRefProdukExist" v-for="dt in satuanProdukRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                                    </select>
                                                </td>
                                                <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <input id="jumlah_hasil" v-model="itemRacikan.jumlah_hasil" class="uk-input uk-form-small" type="number" style="text-align: center;" @change="onChangeJumlahRacikan">
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <select v-model="itemRacikan.satuan_hasil" class="uk-select uk-form-small">
                                                        <option v-if="isRefProdukExist" v-for="dt in satuanProdukRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                                    </select>
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; color:black;">
                                                    <search-select  ref="searchSigna"
                                                        :config = configSelect
                                                        searchUrl = '/signas'
                                                        placeholder = "pilih signa"
                                                        captionField = "signa"
                                                        valueField = "signa"
                                                        :itemListData = signaDesc
                                                        :value = "itemRacikan.signa"
                                                        v-on:item-select = "signaRacikanSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemRacikan.harga)}}</td>
                                                <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemRacikan.subtotal)}}</td>
                                                <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                    <button type="button" @click.prevent="insertResepRacikan">Tambah</button>
                                                </td>
                                            </tr>
                                            <template v-for="dt in prescriptionItems">
                                                <tr v-if="dt.is_aktif">
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">

                                                    </td>
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dt.no_racikan}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dt.produk_nama}}</p>
                                                    </td>       
                                                    <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                        <input class="uk-input uk-form-small" type="number" v-model="dt.jumlah_komposisi" style="text-align: center;">
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black; text-align:center;">
                                                        <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;">{{dt.satuan_komposisi}}</p>
                                                    </td>
                                                    <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                        <input class="uk-input uk-form-small" type="number" v-model="dt.jumlah_hasil" style="text-align: center;">
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black; text-align:center;">
                                                        <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;">{{dt.satuan_hasil}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black;">
                                                        <p style="margin:0;padding:0;font-weight: 400;">
                                                            <strong>{{ dt.signa }}</strong> {{ dt.signa_deskripsi }}
                                                        </p>
                                                    </td>
                                                    <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.harga)}}</td>
                                                    <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.subtotal)}}</td>
                                                    <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" style="text-align: center;" @change="activationChange">
                                                    </td>
                                                </tr>
                                            </template>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid>
                        <div class="uk-width-1-1" style="text-align:right;padding:0 1em 0.5em 1em;">
                            <button type="button" class="uk-button uk-modal-close" style="border:none; background-color: whitesmoke; color:#000;">TUTUP</button>
                            <button type="submit" class="uk-button" style="border:none; background-color: #cc0202; color:#fff;margin-left:0.5em;">SIMPAN</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'modal-resep',
    props : { 
        modalId : { type:String, required:false, default:'modalAsesmen' }, 
    },
    components : {
        SearchSelect,
        SearchList
    },
    data() {
        return {
            isUpdate : false,

            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            produkDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],
            signaDesc : [
                { colName : 'signa', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
            ],
            satuanDesc : [
                { colName : 'satuan_id', labelData : '', isTitle : true },
                { colName : 'satuan', labelData : '', isTitle : false },
            ],
            refDesc : [
                { colName : 'value', labelData : '', isTitle : true },
                { colName : 'text', labelData : '', isTitle : false },
            ], 
            itemResep : {
                detail_id : null,
                produk_id : null,
                produk_nama : null,
                signa : null,
                signa_deskripsi : null,
                satuan : null,
                jenis_produk : null,
                jumlah : 0,
                harga : 0,
                subtotal : 0,
                is_aktif : true,
                is_racikan : false,
                jumlah_komposisi : 0,
                satuan_komposisi : null,
                jumlah_hasil : 0,
                satuan_hasil : null
            },
            itemRacikan : {
                detail_id : null,
                no_racikan : null,
                produk_id : null,
                produk_nama : null,
                signa : null,
                signa_deskripsi : null,
                satuan : null,
                jenis_produk : null,
                jumlah : 0,
                harga : 0,
                subtotal : 0,
                is_aktif : true,
                is_racikan : true,
                jumlah_komposisi : 0,
                satuan_komposisi : null,
                jumlah_hasil : 0,
                satuan_hasil : null
            },
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliItemPrescripts','poliResepLists']),
        ...mapGetters('resep',['prescriptionItems','prescription']),
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
    },

    mounted() {
        this.initialize();
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',['CLR_POLI_TRANSACTION','SET_POLI_TRANSACTION','SET_ITEM_PRESCRIPTS']),
        ...mapMutations('resep',['CLR_PRESCRIPTION','NEW_PRESCRIPTION','SET_PRESCRIPTION','SET_PRESCRIPTION_ITEMS']),
        ...mapActions('resep',['createPrescription','updatePrescription','dataPrescription']),
        ...mapActions('rawatJalan',['dataTransaksiPoli']),
        ...mapActions('refProduk',['listRefProduk']),

        initialize() {
            this.listRefProduk();
            console.log(this.listRefProduk());
        },

        itemSelected(data) {
            if(data) {
                // this.itemResep.detail_id = data.detail_id;
                this.itemResep.produk_nama = data.produk_nama;
                this.itemResep.produk_id = data.produk_id;
                this.itemResep.signa = data.signa;
                this.itemResep.satuan = data.satuan;
                this.itemResep.harga = data.hna;
                this.itemResep.subtotal = data.hna;
            }
        },
        
        itemRacikanSelected(data) {
            if(data) {
                // this.itemResep.detail_id = data.detail_id;
                this.itemRacikan.produk_nama = data.produk_nama;
                this.itemRacikan.produk_id = data.produk_id;
                this.itemRacikan.harga = data.hna;
                this.itemRacikan.subtotal = data.hna;
            }
        },

        signaRacikanSelected(data) {
            if(data) {
                this.itemRacikan.signa = data.signa;
                this.itemRacikan.signa_deskripsi = data.deskripsi;
            }
        },

        signaSelected(data) {
            if(data) {
                this.itemResep.signa = data.signa;
                this.itemResep.signa_deskripsi = data.deskripsi;
            }
        },

        satuanSelected(data) {
            if(data) {
                this.itemResep.satuan = data.satuan;
                this.itemResep.satuan_komposisi = data.value;
            }
        },

        showModal(){
            UIkit.modal(`#${this.modalId}`).show();
        },

        closeModal(){
            UIkit.modal(`#${this.modalId}`).hide();
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        calculateTotal(){
            let total = 0;
            this.prescriptionItems.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.prescription.total = total; 
            })
        },

        changeRowRegExpand() {
            this.isExpandedReg = !this.isExpandedReg;
            return false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        },
        
        submitResep(){
            this.CLR_ERRORS();
            if(this.isUpdate) {
                if(confirm(`Proses ini akan menambahkan data resep. Lanjutkan ?`)){
                    this.updatePrescription(this.prescription).then((response) => {
                        if (response.success == true) {
                            this.isUpdate = true;
                            alert(response.message);
                            this.RefreshPemeriksaan();
                            this.closeModal();
                        }
                        else { 
                            alert (response.message); 
                        }
                    });
                };
            }
            else {
                if(confirm(`Proses ini akan membuat data baru resep. Lanjutkan ?`)){
                    this.createPrescription(this.prescription).then((response) => {
                        if (response.success == true) {
                            this.isUpdate = true;
                            alert(response.message);
                            this.RefreshPemeriksaan();
                            this.closeModal();
                        }
                        else { 
                            alert (response.message); 
                        }
                    });
                };
            }
        },

        RefreshPemeriksaan(){
            let trxId = this.poliTransaction.trx_id;
            this.CLR_ERRORS();
            this.isLoading = true;
            if(trxId) {
                this.dataTransaksiPoli(trxId).then((response) => {
                    if (response.success == true) {
                        this.SET_POLI_TRANSACTION(response.data);
                        this.isLoading = false;
                    }
                    else { 
                        this.CLR_POLI_TRANSACTION();
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
        },

        onChangeJumlah(){
            let jumlah = document.getElementById("jumlah").value;
            this.itemResep.subtotal = jumlah * this.itemResep.harga;
        },

        onChangeJumlahRacikan(){
            let jumlah = document.getElementById("jumlah_hasil").value;
            this.itemRacikan.subtotal = jumlah * this.itemRacikan.harga;
        },

        insertResep(){
            let val = JSON.parse(JSON.stringify(this.itemResep));
            // console.log(val);
            this.prescriptionItems.push(val);
            this.calculateTotal();
            this.itemResep.produk_id = null;
            this.itemResep.produk_nama = null;
            this.itemResep.signa = null;
            this.itemResep.signa_deskripsi = null;
            this.itemResep.satuan = null;
            this.itemResep.jenis_produk = null;
            this.itemResep.jumlah = null;
            this.itemResep.harga = null;
            this.itemResep.subtotal = null;
            this.itemResep.is_racikan = false;
            this.itemResep.is_aktif = true;
        },

        insertResepRacikan(){
            // alert('abc');
            // return false;
            let val = JSON.parse(JSON.stringify(this.itemRacikan));
            this.prescriptionItems.push(val);
            this.calculateTotal();
            this.itemRacikan.produk_id = null;
            this.itemRacikan.produk_nama = null;
            this.itemRacikan.jumlah_komposisi = null;
            this.itemRacikan.satuan_komposisi = null;
            // this.itemRacikan.jumlah_hasil = null;
            // this.itemRacikan.satuan_hasil = null;
            this.itemRacikan.is_racikan = true;
            this.itemRacikan.is_aktif = true;
        },

        resepBaru(data) {
            if(data) {
                // console.log(data);
                let val = {
                    reg_id : data.reg_id,
                    trx_id : data.trx_id,
                    reff_trx_id : data.reff_trx_id,
                    dokter_id : data.dokter_id,
                    dokter_nama : data.dokter_nama,
                    unit_id : data.unit_id,
                    unit_nama : data.unit_nama,
                    pasien_id : data.pasien_id,
                };
                // console.log(val);
                this.NEW_PRESCRIPTION(val);
                this.isUpdate = false;
                this.showModal();
            }
        },
    }
}
</script>