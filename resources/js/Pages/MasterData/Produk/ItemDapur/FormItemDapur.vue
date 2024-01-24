<template>
    <div class="uk-container">
        <div class="uk-grid uk-grid-small" style="padding:0.5em 1em 0.5em 1em;">
            <div class="uk-width-auto" style="padding:0;">
                <a href="#" @click.prevent="closeModalItem" uk-icon="icon:arrow-left;ratio:1.5" style="display:block;width:30px;"></a>
            </div>
            <div class="uk-width-expand"  style="padding:0;margin:0;">
                <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                    <li>
                        <a href="#" @click.prevent="closeModalItem" class="uk-text-uppercase">
                            <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">MASTER PERSEDIAAN</h4>
                        </a>                    
                    </li>
                    <li v-if="produk.produk_nama !== null">
                        <span style="font-weight:400;color:#333;font-size:14px;"  class="uk-text-uppercase">{{produk.produk_nama}}</span>
                    </li>           
                </ul>
            </div>
        </div>
        
        <div class="uk-container" style="margin:1em 0 0 0;padding:0 1em 1em 1em;background-color:#fff;">
                <form action="" @submit.prevent="submitItemDapur" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header uk-box-shadow-large" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA ITEM DAPUR</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="checkProdukId" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">REFRESH</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
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
                                    data item persediaan umum.
                                </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid>                                 
                                <div class="uk-width-2-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Item*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" :disabled="isUpdate" v-model="produk.produk_nama" type="text" placeholder="nama item barang" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kode Barcode</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="produk.barcode" type="text" placeholder="kode ext (barcode)">
                                    </div>
                                </div>
                                
                                
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Harga Beli Satuan</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" v-model="produk.harga_beli" type="number"  required>
                                    </div>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Harga Jual Dasar</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" v-model="produk.hna" type="number" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Harga Jual Tertinggi</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" v-model="produk.het" type="number">
                                    </div>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <search-list
                                        :config = configSelect
                                        :dataLists = groupBillingLists.data
                                        label = "Kelompok Tagihan"
                                        placeholder = ""
                                        captionField = "kelompok_tagihan"
                                        valueField = "kelompok_tagihan"
                                        :detailItems = billingDesc
                                        :value = "produk.kelompok_tagihan"
                                        v-on:item-select = "billingSelected"
                                    ></search-list>
                                </div>

                                <div class="uk-width-1-3@m">                                    
                                    <search-list
                                        :config = configSelect
                                        :dataLists = groupVclaimLists.data
                                        label = "Kelompok Eklaim"
                                        placeholder = ""
                                        captionField = "kelompok_vclaim"
                                        valueField = "kelompok_vclaim"
                                        :detailItems = vclaimDesc
                                        :value = "produk.kelompok_vclaim"
                                        v-on:item-select = "vklaimSelected"
                                    ></search-list>
                                </div>

                                <div class="uk-width-1-3@m">
                                    <search-list
                                        :config = configSelect
                                        :dataLists = produkAccount
                                        label = "Akun Produk"
                                        placeholder = ""
                                        captionField = "produk_akun"
                                        valueField = "produk_akun"
                                        :detailItems = productAccountDesc
                                        :value = "produk.produk_akun"
                                        v-on:item-select = "akunSelected"
                                    ></search-list>
                                </div>
                                <div class="uk-width-1-1@m" style="margin:0;padding:1em 0.2em 0.2em 1em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="produk.is_aktif"> Item / Produk aktif
                                        </label>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                        <div class="uk-grid-small hims-form-subpage" uk-grid >
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">PARAMETER PRODUK</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    pengaturan parameter produk.
                                </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="produk.is_jual"> Item Dijual
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">item persediaan dapat dijual.</p>
                                </div>
                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="produk.is_pengadaan"> Item Pengadaan
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">item dapat diperoleh dari pengadaan.</p>
                                </div>
                                
                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="produk.is_kontrol_kadaluarsa"> Kontrol Kadaluarsa
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">item perlu kontrol masa kadaluarsa.</p>
                                </div>

                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="produk.is_produksi"> Item Produksi
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">item persediaan adalah hasil produksi.</p>
                                </div>
                            </div>
                        </div>
                        <div class="uk-grid-small hims-form-subpage" uk-grid >
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">ATRIBUT PRODUK</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    pemasok, pabrikan, kemasan dan cara pakai.
                                </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                                <div class="uk-width-1-1@m">
                                    <search-list
                                        :config = configSelect
                                        :dataLists = vendorLists.data
                                        label = "Pemasok"
                                        placeholder = ""
                                        captionField = "vendor_nama"
                                        valueField = "vendor_nama"
                                        :detailItems = vendorDesc
                                        :value = "produk.vendor"
                                        v-on:item-select = "pbfSelected"
                                    ></search-list>
                                </div>

                                <div class="uk-width-1-3@m">
                                    <search-list
                                        :config = configSelect
                                        :dataLists = satuanProdukRefs.json_data
                                        label = "Satuan Persediaan"
                                        placeholder = ""
                                        captionField = "value"
                                        valueField = "value"
                                        :detailItems = satuanDesc
                                        :value = "produk.satuan"
                                        v-on:item-select = "satuanSelected"
                                    ></search-list>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <search-list
                                        :config = configSelect
                                        :dataLists = satuanProdukRefs.json_data
                                        label = "Satuan Beli"
                                        placeholder = ""
                                        captionField = "value"
                                        valueField = "value"
                                        :detailItems = satuanDesc
                                        :value = "produk.satuan_beli"
                                        v-on:item-select = "satuanBeliSelected"
                                    ></search-list>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Konversi</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" v-model="produk.konversi" type="number">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="uk-grid-small hims-form-subpage" uk-grid >
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">GOLONGAN DAN KLASIFIKASI</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    golongan dan klasifikasi produk.
                                </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                                <div class="uk-width-1-1@m" style="margin:0;padding:1em 0.2em 0 0.2em;">                                    
                                    <p class="uk-text-uppercase" style="font-size:14px;font-weight:500;color:black;padding:0.2em 0.2em 0.2em 1em;margin:0;">Golongan Produk</p>
                                </div>
                                <template v-if="isRefProdukExist" v-for="gol in golonganProdukRefs.json_data">
                                    <div  v-if="gol.jenis_produk=='DAPUR' && gol.aktif" class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:12px;">
                                                <input class="uk-radio" type="radio" :value="gol" v-model="golongan"> 
                                                {{gol.value}}<p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">{{gol.desc}}</p> 
                                            </label>
                                        </div>
                                    </div>
                                </template>

                                <div class="uk-width-1-1@m" style="margin:0;padding:1em 0.2em 0 0.2em;">                                    
                                    <p class="uk-text-uppercase" style="font-size:14px;font-weight:500;color:black;padding:0.2em 0.2em 0.2em 1em;margin:0;">Klasifikasi Produk</p>
                                </div>
                                <template v-if="isRefProdukExist" v-for="klas in klasifikasiProdukRefs.json_data">
                                    <div  v-if="klas.jenis_produk=='DAPUR' && klas.aktif" class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                        <div class="uk-form-controls">
                                            <label style="color:black; font-size:12px;">
                                                <input class="uk-checkbox" type="checkbox" :value="klas" v-model="klasifikasi"> 
                                                    {{klas.value}}<p style="font-size:11px; font-style:italic; color:black;padding:0.2em 0.2em 0.2em 2em;margin:0;">{{klas.desc}}</p>
                                            </label>
                                        </div>                                        
                                    </div>
                                </template>
                            </div>                            
                        </div>                        
                    </div>
                </form>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-item-dapur',
    props : {
        produkId : { type:String, required:false, default:null }
    },
    components : {
        SearchSelect,
        SearchList,
    },
    data (){
        return {
            urlBack : '/master/produk/dapur',
            isUpdate : false,
            isLoading : false,
            produk : {
                client_id : null,
                produk_id : null,
                barcode : null,
                produk_nama : null,
                jenis_produk : 'DAPUR',
                satuan : null, //satuan stock persediaan, saat stock sudah dibuat, tidak dapat dihapus.
                satuan_beli : null, //satuan yang digunakan untuk pembelian, hanya sebagai acuan.
                konversi : null,
                harga_beli : null, 
                hna : null, //harga pokok sebelum margin dan ppn
                het : null, //harga eceran tertinggi
                is_hasil_produksi : false, //item adalah hasil produksi
                is_jual : true , //item boleh dijual
                is_pengadaan : true, //item dapat dibeli
                is_sterilisasi : false, // item butuh sterilisasi
                is_kontrol_kadaluarsa : true, //item harus dikontrol kadaluarsa
                is_kontrol_stok : true, //item perlu di kontrol jumlah persediaan
                is_laundry_item : false, //item ini digunakan dan diperlukan untuk di laundry
                cara_pakai : null, // cara penggunaan sebelum dikonsumsi
                signa : null,
                aturan_pakai : null, // signa atau cara pakai
                label_obat : null, //label obat : Obat dalam, obat luar, dsbnya
                klasifikasi : null, //contoh : Narkotik, obat herbal, Obat keras,dsb
                sediaan : null, // contoh : puyer , obat tetes, dsbnya
                golongan : null, //fornas,formularium,generic,non generic
                komposisi : null,
                kontra_indikasi : null,
                vendor_id : null,
                vendor : null,
                pabrikan_id : null,
                pabrikan : null,
                meta_data : null,
                produk_akun : null,
                produk_akun_id : null,
                kelompok_vclaim_id : null,
                kelompok_vclaim : null,
                kelompok_tagihan_id : null,
                kelompok_tagihan : null,
                jenis_etiket : null,
                is_aktif : true,
            },
            klasifikasi : [],
            golongan : [],

            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : true,
            },
            vclaimDesc : [
                { colName : 'kelompok_vclaim', labelData : '', isTitle : true },
                { colName : 'kelompok_vclaim_id', labelData : 'ID :', isTitle : false },
            ],
            billingDesc : [
                { colName : 'kelompok_tagihan', labelData : '', isTitle : true },
                { colName : 'kelompok_tagihan_id', labelData : 'ID :', isTitle : false },
            ],
            vendorDesc : [
                { colName : 'vendor_nama', labelData : '', isTitle : true },
                { colName : 'vendor_id', labelData : 'ID :', isTitle : false },
            ],
            satuanDesc : [
                { colName : 'value', labelData : '', isTitle : true },
                { colName : 'text', labelData : '', isTitle : false },
            ],
            productAccountDesc: [
                { colName : 'produk_akun', labelData : '', isTitle : true },
                { colName : 'produk_akun_id', labelData : 'ID: ', isTitle : false },
            ],

            satuan : [],
            produkAccount : [],
        }
    },
    watch :{
        'productAccounts' : function(newVal, oldVal) {
            if(newVal !== null && newVal !== '') {
                this.produkAccount = newVal.data.filter(item => {return item['jenis_produk'].toUpperCase()== 'DAPUR'});
            }
            else {
                this.produkAccount = [];
            }
        },
        'produkId' : function(newVal, oldVal) {
            //if(newVal) { this.checkProdukId(); }
        },
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelompokVclaim',['groupVclaimLists']),
        ...mapGetters('kelompokTagihan',['groupBillingLists']),
        ...mapGetters('vendor',['vendorLists','isVendorExist']),
        ...mapGetters('produkAkun',['productAccounts','isProductAccountExist']),
        ...mapGetters('refProduk',['isRefProdukExist','klasifikasiProdukRefs','golonganProdukRefs','satuanProdukRefs']),
    },

    mounted() {        
        this.initialize();
    },

    methods : {

        ...mapActions('produk',['createProduk','updateProduk','dataProduk']),
        ...mapActions('produkAkun',['listProdukAkun']),
        ...mapActions('kelompokVclaim',['listKelompokVclaim']),
        ...mapActions('kelompokTagihan',['listKelompokTagihan']),
        ...mapActions('vendor',['listVendor']),
        ...mapActions('refProduk',['listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            this.CLR_ERRORS(); 
            this.clearProduk();
            this.listRefProduk({per_page:'ALL'});
            this.listProdukAkun({per_page:'ALL'});
            this.listVendor({per_page:'ALL'}); 
            this.listKelompokTagihan({per_page:'ALL'});
            this.listKelompokVclaim({per_page:'ALL'});
        },

        checkProdukId(){
            this.CLR_ERRORS();
            this.listKelompokTagihan({per_page:'ALL'});            
            this.listKelompokVclaim({per_page:'ALL'});
            this.listVendor({per_page:'ALL'});
            this.listProdukAkun({per_page:'ALL'});
            this.produkAccount = this.productAccounts.filter(item => {return item['jenis_produk'].toUpperCase()== 'DAPUR'});
            this.listRefProduk({per_page:'ALL'});
            
            if(this.produkId.toLowerCase() == 'baru') { this.newProduk(); }
            else { this.editProduk(this.produkId); }
        },

        vklaimSelected(data){
            if(data) {
                this.produk.kelompok_vclaim_id = data.kelompok_vclaim_id;
                this.produk.kelompok_vclaim = data.kelompok_vclaim;
            }
        },

        billingSelected(data){
            if(data) {
                this.produk.kelompok_tagihan_id = data.kelompok_tagihan_id;
                this.produk.kelompok_tagihan = data.kelompok_tagihan;
            }
        },

        pbfSelected(data){
            if(data) {
                this.produk.vendor_id = data.vendor_id;
                this.produk.vendor = data.vendor_nama;
            }
        },
        
        satuanSelected(data) {
            if(data) {
                this.produk.satuan = data.value;
            }
        },

        satuanBeliSelected(data) {
            if(data) {
                this.produk.satuan_beli = data.value;
            }
        },        

        akunSelected(data) {
            if(data) {
                this.produk.produk_akun = data.produk_akun;
                this.produk.produk_akun_id = data.produk_akun_id;
            }
        },

        closeModalItem(){
            this.CLR_ERRORS();
            this.clearProduk();
            this.$emit('close',true);
        },

        submitItemDapur(){
            this.CLR_ERRORS();
            this.produk.produk_nama = this.produk.produk_nama.toUpperCase();
            this.produk.klasifikasi = null; 
            this.produk.golongan = this.golongan; 
            let dt = [];
            this.klasifikasi.forEach(klas => {
                let res = this.klasifikasiProdukRefs.json_data.filter(item => {return item['value'].toUpperCase()== klas.value.toUpperCase()});
                if(res) {
                    dt.push({ value : res[0].value, desc : res[0].desc, jenis_produk : res[0].jenis_produk, aktif : res[0].aktif});
                    this.produk.klasifikasi = dt;
                }
            });

            if(!this.isUpdate) {
                this.createProduk(this.produk).then((response) => {
                    if (response.success == true) {
                        alert("Data produk baru berhasil dibuat.");
                        this.fillProduk(response.data);
                        this.$emit('succeed',true);
                        this.isUpdate = true;
                    }
                })
            }
            else {
                this.updateProduk(this.produk).then((response) => {
                    if (response.success == true) {
                        this.fillProduk(response.data);
                        alert("Data produk berhasil diubah.");
                        this.$emit('succeed',true);
                        this.isUpdate = true;
                    }
                })
            }            
        },

        newProduk(){
            this.CLR_ERRORS();
            if(!this.isRefProdukExist) {
                this.listRefProduk({per_page:'ALL'});
            }
            this.clearProduk();
            this.isUpdate = false;
            this.isLoading = false;
        },

        editProduk(produkId){
            this.CLR_ERRORS();
            
            if(!this.isRefProdukExist) {
                this.listRefProduk({per_page:'ALL'});
            }
            this.clearProduk();
            this.isLoading = true;
            this.dataProduk(produkId).then((response)=>{
                if (response.success == true) {
                    this.fillProduk(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                    this.$emit('close',true);
                }
                this.isLoading = false;
            })
        },   

        clearProduk(){
            this.produk.client_id = null;
            this.produk.produk_id = null;
            this.produk.barcode = null;
            this.produk.produk_nama = null;
            this.produk.jenis_produk = 'DAPUR';
            this.produk.satuan = null;
            this.produk.satuan_beli = null;
            this.produk.konversi = null;
            this.produk.harga_beli = null; 
            this.produk.hna = null;
            this.produk.het = null;
            this.produk.is_hasil_produksi = false;
            this.produk.is_jual = true ;
            this.produk.is_pengadaan = true;
            this.produk.is_sterilisasi = false;
            this.produk.is_kontrol_kadaluarsa = true;
            this.produk.is_kontrol_stok = true;
            this.produk.is_laundry_item = false;
            this.produk.cara_pakai = null;
            this.produk.signa = null;
            this.produk.aturan_pakai = null;
            this.produk.label_obat = null;
            this.produk.klasifikasi = null;
            this.produk.sediaan = null;
            this.produk.golongan = null;
            this.produk.komposisi = null;
            this.produk.kontra_indikasi = null;
            this.produk.vendor_id = null;
            this.produk.vendor = null;
            this.produk.pabrikan_id = null;
            this.produk.pabrikan = null;
            this.produk.meta_data = null;
            this.produk.produk_akun = null;
            this.produk.kelompok_vclaim_id = null;
            this.produk.kelompok_vclaim = null;
            this.produk.kelompok_tagihan_id = null;
            this.produk.kelompok_tagihan = null;
            this.produk.jenis_etiket = null;
            this.produk.is_aktif = true;
        },

        fillProduk(data) {
            this.produk.client_id = null;
            this.produk.produk_id = data.produk_id;
            this.produk.barcode = data.barcode;
            this.produk.produk_nama = data.produk_nama;
            this.produk.jenis_produk = data.jenis_produk;
            this.produk.satuan = data.satuan;
            this.produk.satuan_beli = data.satuan_beli;
            this.produk.konversi = data.konversi;
            this.produk.harga_beli = data.harga_beli; 
            this.produk.hna = data.hna;
            this.produk.het = data.het;
            this.produk.is_hasil_produksi = data.is_hasil_produksi;
            this.produk.is_jual = data.is_jual;
            this.produk.is_pengadaan = data.is_pengadaan;
            this.produk.is_sterilisasi = data.is_sterilisasi;
            this.produk.is_kontrol_kadaluarsa = data.is_kontrol_kadaluarsa;
            this.produk.is_kontrol_stok = data.is_kontrol_stok;
            this.produk.is_laundry_item = data.is_laundry_item;
            this.produk.cara_pakai = data.cara_pakai;
            this.produk.signa = data.signa;
            this.produk.aturan_pakai = data.aturan_pakai;
            this.produk.label_obat = data.label_obat;
            this.produk.klasifikasi = data.klasifikasi;
            this.produk.sediaan = data.sediaan;
            this.produk.golongan = data.golongan;
            this.produk.komposisi = data.komposisi;
            this.produk.kontra_indikasi = data.kontra_indikasi;
            this.produk.vendor_id = data.vendor_id;
            this.produk.vendor = data.vendor;
            this.produk.pabrikan_id = data.pabrikan_id;
            this.produk.pabrikan = data.pabrikan;
            this.produk.meta_data = data.meta_data;
            this.produk.produk_akun = data.produk_akun;
            this.produk.produk_akun_id = data.produk_akun_id;
            this.produk.kelompok_vclaim_id = data.kelompok_vclaim_id;
            this.produk.kelompok_vclaim = data.kelompok_vclaim;
            this.produk.kelompok_tagihan_id = data.kelompok_tagihan_id;
            this.produk.kelompok_tagihan = data.kelompok_tagihan;
            this.produk.jenis_etiket = data.jenis_etiket;
            this.produk.is_aktif = true;
            
            if(data.klasifikasi) {
                this.klasifikasi = JSON.parse(JSON.stringify(data.klasifikasi));
            }
            if(data.golongan) {
                this.golongan = JSON.parse(JSON.stringify(data.golongan));
            }
        },
    }
}
</script>
<style>
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

tr.inaktif td {
    text-decoration: line-through;
    font-style: italic;
}
</style>
