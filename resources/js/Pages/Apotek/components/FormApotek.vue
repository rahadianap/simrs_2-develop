<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:true;" :id="modalId" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">FORM APOTEK</h2>
            <div style="padding:0;margin:0;">
                <form action="" @submit.prevent="submitResep">
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0 0.5em 0 0.5em;">
                            <div class="uk-width-expand@m">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA RESEP
                                    <span style="font-size:12px;font-style:italic;padding:0;margin:0;"> data resep dan obat.</span>
                                </h5>
                            </div>
                        </div>
                        <div v-if="resepLangsung.is_racikan == false" class="uk-width-1-1@s uk-grid-small" uk-grid> 
                            <div class="uk-width-1-1@m">
                                <div style="padding:0 0 0.2em 0;">
                                    <table class="uk-table uk-table-striped" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-reg" style="text-align:center;color:white;background-color: #cc0202;">Racik</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Item Resep</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Cara Pakai</th>
                                            <th class="tb-header-reg" style="width:40px;text-align:center;color:white;background-color: #cc0202;">Jumlah</th>
                                            <th class="tb-header-reg" style="text-align:center;color:white;background-color: #cc0202;">Satuan</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Harga</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Subtotal</th>
                                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">...</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding:1em; font-size: 12px; text-align:center; color:black;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="resepLangsung.is_racikan" style="text-align: center;" @change="racikanChange">
                                                </td>
                                                <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
                                                    <search-select  ref="searchObat"
                                                        :config = configSelect
                                                        searchUrl = '/products/medical/items'
                                                        placeholder = "pilih item obat"
                                                        captionField = "produk_nama"
                                                        valueField = "produk_nama"
                                                        :itemListData = produkDesc
                                                        :value = "resepLangsung.produk_nama"
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
                                                        :value = "resepLangsung.signa"
                                                        v-on:item-select = "signaSelected"
                                                    ></search-select>
                                                </td>

                                                <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <input id="amount" v-model="resepLangsung.jumlah" class="uk-input uk-form-small" type="number" style="text-align: center;" @change="onChangeJumlah">
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <input v-model="resepLangsung.satuan" class="uk-input uk-form-small" type="text" style="text-align: center;" readonly>
                                                </td>
                                                <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(resepLangsung.harga)}}</td>
                                                <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(resepLangsung.subtotal)}}</td>
                                                
                                                <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                    <button type="button" @click.prevent="insertResep">Tambah</button>
                                                </td>
                                            </tr>
                                            <template v-for="dt in prescriptionItems">
                                                <tr v-if="dt.is_aktif">
                                                    <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_racikan" style="text-align: center;" disabled>
                                                    </td>
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;" readonly>{{dt.produk_nama}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black;">
                                                        <p style="margin:0;padding:0;font-weight: 400;">
                                                            <strong>{{ dt.signa }}</strong> {{ dt.signa_deskripsi }}
                                                        </p>
                                                    </td>        
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;" readonly>{{dt.jumlah}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black; text-align:center;">
                                                        <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;" readonly>{{dt.satuan}}</p>
                                                    </td>
                                                    <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;" readonly>{{dt.harga}}</td>
                                                    <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;" readonly>{{dt.subtotal}}</td>
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
                                    <table class="uk-table uk-table-striped" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-reg" style="text-align:center;color:white;background-color: #cc0202;">Racik</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Group</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Komposisi</th>
                                            <th class="tb-header-reg" style="width:40px;text-align:center;color:white;background-color: #cc0202;">Jumlah Komposisi</th>
                                            <th class="tb-header-reg" style="text-align:center;color:white;background-color: #cc0202;">Satuan Komposisi</th>
                                            <th class="tb-header-reg" style="width:40px;text-align:center;color:white;background-color: #cc0202;">Jumlah Hasil</th>
                                            <th class="tb-header-reg" style="text-align:center;color:white;background-color: #cc0202;">Satuan Hasil</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Cara Pakai</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Harga</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Subtotal</th>
                                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">...</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding:1em; font-size: 12px; text-align:center; color:black;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="resepLangsung.is_racikan" style="text-align: center;" @change="racikanChange">
                                                </td>
                                                <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
                                                    <select v-model="resepRacikan.no_racikan" class="uk-select uk-form-small" id="form-horizontal-select">
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
                                                        :value = "resepRacikan.produk_nama"
                                                        v-on:item-select = "itemRacikanSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <input id="jumlah" v-model="resepRacikan.jumlah_komposisi" class="uk-input uk-form-small" type="number" style="text-align: center;">
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <select v-model="resepRacikan.satuan_komposisi" class="uk-select uk-form-small">
                                                        <option v-if="isRefProdukExist" v-for="dt in satuanProdukRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                                    </select>
                                                </td>
                                                <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <input id="jumlah_hasil" v-model="resepRacikan.jumlah_hasil" class="uk-input uk-form-small" type="number" style="text-align: center;" @change="onChangeJumlahRacikan">
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <select v-model="resepRacikan.satuan_hasil" class="uk-select uk-form-small">
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
                                                        :value = "resepRacikan.signa"
                                                        v-on:item-select = "signaRacikanSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(resepRacikan.harga)}}</td>
                                                <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(resepRacikan.subtotal)}}</td>
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
export default {
    name : 'form-apotek',
    props : { 
        modalId : { type:String, required:false, default:'formApotek' }, 
    },
    components : {
        SearchSelect
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
                { colName : 'satuan_nama', labelData : '', isTitle : false },
            ],
            trxDesc : [
                { colName : 'trx_id', labelData : '', isTitle : true },
                { colName : 'nama_pasien', labelData : '', isTitle : false },
            ],
            refDesc : [
                { colName : 'value', labelData : '', isTitle : true },
                { colName : 'text', labelData : '', isTitle : false },
            ],
            pasien : {
                trx_id : null,
                nama_pasien : null,
                no_rm : null,
                nama_dokter : null
            },
            resepLangsung : {
                detail_id : null,
                produk_id : null,
                produk_nama : null,
                signa : null,
                signa_deskripsi : null,
                satuan : null,
                jenis_produk : null,
                jumlah : 1,
                harga : 0,
                subtotal : 0,
                is_aktif : true,
                is_racikan : false,
                client_id : null,
                realization : []
            },
            resepRacikan : {
                detail_id : null,
                no_racikan : null,
                produk_id : null,
                produk_nama : null,
                signa : null,
                signa_deskripsi : null,
                satuan : null,
                jenis_produk : null,
                jumlah : 1,
                harga : 0,
                subtotal : 0,
                is_aktif : true,
                is_racikan : true,
                client_id : null,
                realization : []
            },
        }
    },

    mounted() {
        this.initialize();
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliItemPrescripts','poliResepLists']),
        ...mapGetters('resep',['prescriptionItems','prescription']),
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',['CLR_POLI_TRANSACTION','SET_POLI_TRANSACTION','SET_ITEM_PRESCRIPTS']),
        ...mapMutations('resep',['CLR_PRESCRIPTION','NEW_PRESCRIPTION','SET_PRESCRIPTION','SET_PRESCRIPTION_ITEMS']),
        ...mapActions('apotek',['createApotek','updateApotek','dataApotek']),
        ...mapActions('rawatJalan',['dataTransaksiPoli']),
        ...mapActions('signa',['createSigna','dataSigna','updateSigna']),
        ...mapActions('refProduk',['listRefProduk']),

        initialize() {
            this.listRefProduk();
            console.log(this.listRefProduk());
        },

        approveResep(resep_id){
            // this.clearSigna();            
            this.dataPrescription(resep_id).then((response)=>{
                if (response.success == true) {
                    this.fillSigna(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formRealisasiResep').show();
                } else {
                    alert(response.message)
                }
            })
        },

        itemRacikanSelected(data) {
            if(data) {
                // this.itemResep.detail_id = data.detail_id;
                this.resepRacikan.produk_nama = data.produk_nama;
                this.resepRacikan.produk_id = data.produk_id;
                this.resepRacikan.harga = data.hna;
                this.resepRacikan.subtotal = data.hna;
            }
        },

        signaRacikanSelected(data) {
            if(data) {
                this.resepRacikan.signa = data.signa;
                this.resepRacikan.signa_deskripsi = data.deskripsi;
            }
        },

        fillSigna(data){
            // console.log(data);
            this.resepLangsung.client_id = data.client_id;
            data.items.forEach(data => {
                this.resepLangsung.produk_nama = data.item_nama;
            });
            // this.signa.signa_id = data.signa_id;
            // this.signa.signa = data.signa;
            // this.signa.deskripsi = data.deskripsi;
            // this.signa.is_aktif = data.is_aktif;
        },

        itemSelected(data) {
            if(data) {
                // console.log(data);
                // this.resepLangsung.detail_id = data.detail_id;
                this.resepLangsung.produk_nama = data.produk_nama;
                this.resepLangsung.produk_id = data.produk_id;
                this.resepLangsung.signa = data.signa;
                this.resepLangsung.satuan = data.satuan;
                this.resepLangsung.harga = data.hna;
                this.resepLangsung.subtotal = data.hna;
            }
        }, 
        signaSelected(data) {
            if(data) {
                this.resepLangsung.signa = data.signa;
                this.resepLangsung.signa_deskripsi = data.deskripsi;
            }
        },

        trxSelected(data) {
            if(data) {
                this.pasien.nama_pasien = data.nama_pasien;
                this.pasien.no_rm = data.no_rm;
                this.pasien.nama_dokter = data.dokter_nama;
            }
        },

        satuanSelected(data) {
            if(data) { this.resepLangsung.satuan = data.satuan_id; }
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
            if(this.isUpdate) {
                if(confirm(`Proses ini akan menambahkan data resep. Lanjutkan ?`)){
                    this.updateApotek(this.prescription).then((response) => {
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
                    this.createApotek(this.prescription).then((response) => {
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
            let jml = document.getElementById("amount").value;
            console.log(jml);
            this.resepLangsung.subtotal = jml * this.resepLangsung.harga;
        },

        insertResep(){
            let val = JSON.parse(JSON.stringify(this.resepLangsung));
            this.prescriptionItems.push(val);
            this.calculateTotal();
            this.resepLangsung.produk_id = null;
            this.resepLangsung.produk_nama = null;
            this.resepLangsung.signa = null;
            this.resepLangsung.signa_deskripsi = null;
            this.resepLangsung.satuan = null;
            this.resepLangsung.jenis_produk = null;
            this.resepLangsung.jumlah = 1;
            this.resepLangsung.harga = null;
            this.resepLangsung.subtotal = null;
            this.resepLangsung.is_racikan = false;
            this.resepLangsung.is_aktif = true;
        },

        insertResepRacikan(){
            // alert('abc');
            // return false;
            let val = JSON.parse(JSON.stringify(this.resepRacikan));
            this.prescriptionItems.push(val);
            this.calculateTotal();
            this.resepRacikan.produk_id = null;
            this.resepRacikan.produk_nama = null;
            this.resepRacikan.jumlah_komposisi = null;
            this.resepRacikan.satuan_komposisi = null;
            // this.resepRacikan.jumlah_hasil = null;
            // this.resepRacikan.satuan_hasil = null;
            this.resepRacikan.is_racikan = true;
            this.resepRacikan.is_aktif = true;
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
                this.NEW_PRESCRIPTION(val);
                this.isUpdate = false;
                this.showModal();
            }
        },

        pilihItemPermintaan() {
            this.prescriptionItems.forEach(data => {
                this.resepLangsung.realization.push(data);
            })
            // let val = JSON.parse(JSON.stringify(this.prescriptionItems[0]));
            // this.resepLangsung.realization.push(val);
            // console.log(this.resepLangsung.realization);
        },

        activationChange() {
            this.resepLangsung.realization = [];
        }
    }
}
</script>