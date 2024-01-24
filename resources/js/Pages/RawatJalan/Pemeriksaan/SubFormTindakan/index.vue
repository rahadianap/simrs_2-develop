<template>
    <div style="padding:0 0 1em 0;margin:0;">   
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="formClosed" style="padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:1"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">PEMERIKSAAN DAN BHP</h4>
            </div>
        </div>
        <div style="background-color:white;margin-top:2em;">
            <div class="uk-grid-small hims-form-subpage" uk-grid style="border:none;padding:1em 0.5em 0.5em 0.5em;background-color:#fff;">
                <div class="uk-width-1-1@m">
                    <p style="font-size:12px;padding:0;margin:0;">ID.{{ examination.pasien_id }}</p>
                    <h5 style="color:black;padding:0;margin:0;font-weight: 500;">{{ examination.nama_pasien }} ({{ examination.no_rm }})</h5>                                        
                </div>
                <div class="uk-width-1-3@m">
                    <dl class="uk-description-list hims-description-list">
                        <dt>Dokter</dt>
                        <dd>{{examination.dokter_nama}}</dd>
                    </dl>                                        
                </div>
                <div class="uk-width-1-3@m">                                
                    <dl class="uk-description-list hims-description-list">
                        <dt>Unit</dt>
                        <dd>{{examination.unit_nama}}</dd>
                    </dl>
                </div>
                <div class="uk-width-1-3@m">                                        
                    <dl class="uk-description-list hims-description-list">
                        <dt>No.Registrasi</dt>
                        <dd>{{examination.trx_id}} <strong>{{ examination.status }}</strong></dd>
                    </dl>
                </div>                
            </div> 
            <form action="" @submit.prevent="simpanPemeriksaan">
                <div class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-1">
                        <div style="padding:0.5em 0.5em 0.5em 1em;position: sticky; position: -webkit-sticky;top: 0; z-index:99;">
                            <div class="uk-grid-small uk-card1 uk-card-default1 hims-form-header" uk-grid style="padding:0;margin:0;">                        
                                <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                                    <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                                        <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="formClosed" style="padding:0.5em;"><span uk-icon="arrow-left"></span> Kembali</button>
                                        <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="initialize" style="padding:0.5em;"><span uk-icon="refresh"></span> Refresh</button>
                                        <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                                        <template v-if="isDraft">
                                            <button :disabled="isLoading" type="submit" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-text"></span> Simpan</button>
                                            <button :disabled="isLoading" type="button"  @click.prevent="konfirmasiData" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-text"></span> Selesai</button>
                                        </template>
                                        <template v-else>
                                            <button :disabled="isLoading" type="button" @click.prevent="batalKonfirmasiData" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-edit"></span> Ubah Pemeriksaan</button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                        <div uk-spinner="ratio : 2"></div>
                        <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                    </div>                       
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid> 
                        <div class="uk-width-expand@m">
                            <h5 style="color:indigo;padding:0;margin:0;">TINDAKAN PEMERIKSAAN</h5>
                        </div>                            
                        <div class="uk-width-1-1@m">
                            <div style="padding:0 0 0.2em 0;">                                
                                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                    <thead>
                                        <th class="tb-header-reg" style="text-align:center;color:white;width:10px;"></th>
                                        <th class="tb-header-reg" style="text-align:center;color:white;">Tindakan</th>
                                        <th class="tb-header-reg" style="text-align:left;color:white;">Dokter</th>
                                        <th class="tb-header-reg" style="text-align:left;color:white;">Jumlah</th>
                                        <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Harga</th>
                                        <th class="tb-header-reg" style="text-align:center;color:white;">Diskon</th>
                                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>
                                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">
                                            <span uk-icon="icon:ban;"></span>
                                        </th>
                                    </thead>
                                    <tbody>
                                        <row-input-tindakan ref="rowInputTindakan"
                                            :rowData = tindakan
                                            :unitId = trxUnitId
                                            :addFunction = insertTindakan
                                        ></row-input-tindakan>
                                        
                                        <row-form-tindakan 
                                            v-if="tindakanLists" 
                                            v-for="dt in tindakanLists" 
                                            :rowData = dt
                                            :dataChange = calculateTotal
                                            :activationChange = changeActivationAct
                                            ref="rowFormTindakan">
                                        </row-form-tindakan>                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div style="padding:0 0.5em 0.2em 0;"></div>
                        <div class="uk-width-1-1@m">
                            <h5 style="color:indigo;padding:0;margin:0;">BAHAN HABIS PAKAI (BHP)</h5>
                        </div>
                        <div class="uk-width-1-1@m">
                            <div style="padding:0 0 0.2em 0;">
                                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                    <thead>
                                        <th class="tb-header-reg" style="text-align:left;color:white;">Depo</th>
                                        <th class="tb-header-reg" style="text-align:left;color:white;">Item BHP</th>
                                        <th class="tb-header-reg" style="text-align:left;color:white;">Tindakan</th>
                                        <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Jumlah</th>
                                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">Satuan</th>
                                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Harga</th>
                                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>  
                                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Bhp Tind.</th>  
                                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!examination.is_realisasi">
                                            <td style="padding:0.4em;">
                                                <select v-model="bhp.depo_id" class="uk-select uk-form-small" @change="depoSelected">
                                                    <option v-for="dt in depoLists" :value="dt.depo_id">{{ dt.depo_nama }}</option>
                                                </select>
                                            </td>
                                            <td style="padding:0.4em;">
                                                <search-select
                                                    :config = configSelect
                                                    :searchUrl = urlItemDepoPicker
                                                    placeholder = "pilih item bhp"
                                                    captionField = "produk_nama"
                                                    valueField = "produk_nama"
                                                    :itemListData = produkDesc
                                                    :value = "bhp.item_nama"
                                                    v-on:item-select = "bhpSelected"
                                                ></search-select>
                                            </td>
                                            <td  style="padding:0.4em;">
                                                <select v-model="bhp.tindakan_id" class="uk-select uk-form-small" @change="tindakanBhpSelected">
                                                    <option></option>
                                                    <option v-for="act in tindakanLists" :value="act.item_id">{{ act.item_nama }}</option>
                                                </select>
                                            </td>
                                            <td style="padding:0.4em;">
                                                <input v-model="bhp.jumlah" type="number" class="uk-input uk-form-small" style="text-align:center;" @change="jmlBhpChange">
                                            </td>
                                            <td style="padding:1em;text-align:center;font-size:12px;color:black;">{{ bhp.satuan }}</td>
                                            <td style="padding:1em;text-align:right;font-size:12px;color:black;">{{formatCurrency(bhp.nilai)}}</td>
                                            <td style="padding:1em;text-align:right;font-size:12px;color:black;">{{formatCurrency(bhp.subtotal)}}</td>
                                            <td style="padding:1em;text-align:center;font-size:12px;color:black;">
                                                <input class="uk-checkbox" type="checkbox" v-model="bhp.bhp_tindakan" style="text-align: center;">
                                            </td>
                                            <td style="padding:1em;text-align:center;font-size:12px;color:black;">
                                                <button @click.prevent="addBhpManual">Tambah</button>
                                            </td>
                                        </tr>
                                        <template v-for="dt in bhpLists">
                                            <tr v-if="dt.is_aktif">
                                                <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                    <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dt.depo_nama}}</p>
                                                    <p class="uk-text-uppercase1" style="font-size:10px;margin:0;padding:0;font-weight: 400;">{{dt.depo_id}}</p>
                                                </td>
                                                <td style="padding:1em; font-size: 12px; color:black;">
                                                    <p style="margin:0;padding:0;font-weight: 400;">
                                                        {{ dt.item_nama }}
                                                    </p>
                                                </td>
                                                <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                    <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dt.tindakan_nama}}</p>
                                                    <p class="uk-text-uppercase" style="font-size:10px;margin:0;padding:0;font-weight: 400;">{{dt.tindakan_id}}</p>
                                                </td>        
                                                <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.jumlah)}}</td>
                                                <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                    <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{dt.satuan}}</p>
                                                </td>
                                                <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.nilai)}}</td>
                                                <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.subtotal)}}</td>
                                                <td style="padding:1em;text-align:center;font-size:12px;color:black;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="dt.bhp_tindakan" style="text-align: center;">
                                                </td>
                                                <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" style="text-align: center;" @change="changeActivationBhp(dt)">
                                                </td>
                                            </tr>
                                        </template>
                                        <tr>
                                            <td colspan="6" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL PEMERIKSAAN DAN BHP</td>
                                            <td style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(examination.total)}}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> 
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowFormTindakan from '@/Pages/RawatJalan/Pemeriksaan/SubFormTindakan/RowFormTindakan.vue';
import RowInputTindakan from '@/Pages/RawatJalan/Pemeriksaan/SubFormTindakan/RowInputTindakan.vue';
import RowFormBhp from '@/Pages/RawatJalan/Pemeriksaan/SubFormBhp/RowFormBhp.vue';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'sub-form-tindakan',
    props : { 
        trxId : { type:String, required:true },
        actId : { type:String, required:true },
     },    
    components : {
        RowFormTindakan,
        RowInputTindakan,
        RowFormBhp,
        SearchSelect,
    },
    data() {
        return {
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m uk-text-uppercase",
                compStyle : "padding:0;margin:0;",
                retrieveAll : false,
            },
            produkDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],
            tindakanDesc : [
                { colName : 'tindakan_nama', labelData : '', isTitle : true },
                { colName : 'tindakan_id', labelData : '', isTitle : false },
            ],
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],

            urlItemDepoPicker : '',
            urlTindakanPicker : '',
            urlDokterPicker : '',
            urlSearch : null,
            trxUnitId : null,
            isDraft : false,
            // tindakan :[],
            depoLists : [],
            defaultDepo : null,
            depo: { depo_id: null,  depo_nama: null, },
            bhp : { 
                detail_id : null,
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                item_id : null,
                item_nama : null,
                jenis : 'BHP',
                jumlah : 1,
                satuan : null,
                klasifikasi :  null,
                unit_id : null,
                unit_nama : null,
                depo_id : null,
                depo_nama : null,
                tindakan_id : null,
                tindakan_nama : null,
                bhp_tindakan : false,
                kelas_harga_id : '-',
                buku_harga_id : '-',
                nilai : 0,
                diskon : 0,
                diskon_persen : 0,
                harga_diskon : 0,
                subtotal : 0,
                dokter_nama : null,
                dokter_id : null,
                dokter_pengirim_id : null,
                is_racikan : false,
                is_bhp : true,
                is_aktif : true,
                signa : null,
                signa_deskripsi : null,
                komponen : [],
                jml_formula : 0,
            },
            
            tindakan : { 
                detail_id : null,
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                item_id : null,
                item_nama : null,
                jenis : null,
                jumlah : 1,
                satuan : null,
                klasifikasi :  null,
                unit_id : null,
                unit_nama : null,
                depo_id : null,
                depo_nama : null,
                tindakan_id : null,
                tindakan_nama : null,
                bhp_tindakan : false,
                kelas_harga_id : '-',
                buku_harga_id : '-',
                nilai : 0,
                diskon : 0,
                diskon_persen : 0,
                harga_diskon : 0,
                subtotal : 0,
                dokter_nama : null,
                dokter_id : null,
                dokter_pengirim_id : null,
                is_racikan : false,
                is_aktif : true,
                signa : null,
                signa_deskripsi : null,
                komponen : [],
                bhp : [],
            },

            dokter : {
                dokter_id : null, 
                dokter_nama : null, 
            },
            
            bhpItems : [],
            isExpandedReg : false,
            isLoading : false,
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliActs','poliItemUsages']),
        ...mapGetters('pemeriksaan',['examinationItems','tindakanLists','bhpLists','examination']),
    },
    watch:{
        'poliTransaction' : function(oldVal,newVal) {
            this.dokter.dokter_id = newVal.dokter_id;
            this.dokter.dokter_nama = newVal.dokter_nama;            
        }
    },
    mounted(){
        this.initialize();
    },
    methods : {          
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',['CLR_POLI_TRANSACTION','SET_POLI_TRANSACTION','SET_ACTS_POLI','SET_ITEM_USAGE']),   
        ...mapActions('unitPelayanan',['dataUnitPelayanan']),
        ...mapMutations('pemeriksaan',['CLR_EXAMINATION','NEW_EXAMINATION','SET_EXAMINATION','SET_EXAMINATION_ITEMS']),
        ...mapActions('rawatJalan',['dataTransaksiPoli','updateTransaksiPoli','createTransaksiPoli']),
        ...mapActions('pemeriksaan',['createPemeriksaan','updatePemeriksaan','dataPemeriksaan','confirmPemeriksaan','unconfirmPemeriksaan']),
        
        formClosed() {
            this.clearTindakan();
            this.$router.push({ name : 'dataPasienPoli',params: { trxId: this.trxId } });
        },

        clearInputTindakan(){
            this.$refs.rowInputTindakan.clearInputData();
        },

        changeRowRegExpand() {
            this.isExpandedReg = !this.isExpandedReg;
            return false;
        },

        initialize(){
            this.clearTindakan();
            this.retrieveData();
        },

        retrieveData() {            
            this.CLR_ERRORS();
            if(this.trxId) {
                this.isLoading = true;
                this.dataTransaksiPoli(this.trxId).then((response) => {
                    if (response.success == true) {  
                        this.isLoading = false;   
                        this.NEW_EXAMINATION(response.data);
                        this.initData();
                    }
                    else { 
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
        },

        initData(){
            this.trxUnitId = this.poliTransaction.unit_id;
            this.dataUnitPelayanan(this.poliTransaction.unit_id).then((response) => {
                if (response.success == true) { 
                    this.isLoading = false;

                    this.depoLists = JSON.parse(JSON.stringify(response.data.depo)); 
                    let depo = this.depoLists.find(data => data.is_depo_utama == true);
                    if(depo) { this.defaultDepo = JSON.parse(JSON.stringify(depo)); }
                    else { this.defaultDepo = JSON.parse(JSON.stringify(this.depoLists[0])); }
                    this.bhp.depo_id = this.defaultDepo.depo_id;
                    this.bhp.depo_nama = this.defaultDepo.depo_nama;    
                    this.urlItemDepoPicker = `/depos/${this.defaultDepo.depo_id}/products`;
                    
                    if(this.actId.toUpperCase() == 'BARU') {
                        this.isUpdate = false;                        
                        this.isDraft = true;
                    }
                    else { 
                        this. retrieveDataPemeriksaan();
                    }
                }
                else {
                    this.isLoading = false;
                    alert(response.message);
                }
            });            
        },

        retrieveDataPemeriksaan(){
            this.isLoading = true;
            this.dataPemeriksaan(this.actId).then((response) => {
                if (response.success == true) {  
                    this.isLoading = false;
                    this.isUpdate = true;
                    if(response.data.status.toUpperCase() == 'DRAFT') { this.isDraft = true; }
                    else { this.isDraft = false; }
                }
                else { 
                    alert(response.message); 
                    this.isLoading = false; 
                }
            })
        },        

        batalKonfirmasiData(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan Mengubah status data pemeriksaan ${this.actId} - ${this.examination.nama_pasien} kembali ke DRAFT. Lanjutkan ?`)){
                this.isLoading = true;
                this.unconfirmPemeriksaan(this.actId).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('konfirmasi pemeriksaan dibatalkan. Anda dapat mengubah kembali data pemeriksaan ini.');
                        this.$emit('confirmLabCancel',response.data);
                    }
                    else { 
                        this.isLoading = false;
                        alert('konfirmasi registrasi lab tidak berhasil dibatalkan.'); 
                    }
                })
            }
        },
        
        searchTindakan(){
            if(this.poliTransaction.unit_id == null || 
                this.poliTransaction.unit_id == '' || 
                this.poliTransaction.kelas_harga_id == null ||
                this.poliTransaction.kelas_harga_id == '') 
            {
                alert('Pilih unit pelayanan dan kelas terlebih dahulu.');
            }

            else {
                this.urlPicker = `units/${this.poliTransaction.unit_id}/acts`;
                this.$refs.actModalPicker.showModal(this.urlPicker);
            }
        },

        tindakanSelected(dataSelected){        
            if(dataSelected) {
                let data = JSON.parse(JSON.stringify(dataSelected));
                let trxData = this.poliTransaction;
                let hargaRes = data.harga.find(item => item.buku_harga_id == trxData.buku_harga_id && item.kelas_id == trxData.kelas_harga_id);

                if(!hargaRes) {
                    alert('harga pemeriksaan tidak ditemukan. Item tidak dapat ditambahkan.'); 
                    this.clearTindakan(); 
                    return false;
                }
                else {
                    if(!hargaRes.komponen) {
                        alert('komponen harga pemeriksaan tidak ditemukan. Item tidak dapat ditambahkan.'); 
                        this.clearTindakan();
                        return false;
                    }
                }

                let exist = false;
                if(this.examinationItems) {
                    exist = this.examinationItems.find(item => item.item_id == data.tindakan_id);
                    if(exist) { 
                        return false; 
                    }
                    else {                    
                        let komp = [];
                        hargaRes.komponen.forEach(dt => {
                            komp.push({
                                client_id : null,
                                komp_detail_id : null,
                                detail_id : null,
                                reg_id : null,
                                trx_id : null,
                                sub_trx_id : null,
                                jumlah : 1,
                                harga_id : dt.harga_id,
                                komp_harga_id : dt.komp_harga_id,
                                komp_harga : dt.komp_harga,
                                nilai : dt.nilai,
                                diskon : 0,
                                nilai_diskon : 0,
                                subtotal : dt.nilai,
                                is_diskon : dt.is_diskon,
                                is_ubah_manual : dt.is_ubah_manual,
                                is_realisasi : false,
                                is_bayar : false,
                                is_aktif : dt.is_aktif,
                            });
                        });

                        this.tindakan.detail_id = null;
                        this.tindakan.reg_id = null;
                        this.tindakan.trx_id = null;
                        this.tindakan.sub_trx_id = null;
                        this.tindakan.jenis = 'TINDAKAN';
                        this.tindakan.item_id = data.tindakan_id;
                        this.tindakan.item_nama = data.tindakan_nama;
                        this.tindakan.jumlah = 1;
                        this.tindakan.satuan = 'X';
                        this.tindakan.kelas_harga_id = trxData.kelas_harga_id;
                        this.tindakan.buku_harga_id = trxData.buku_harga_id;
                        this.tindakan.nilai = hargaRes.nilai;
                        this.tindakan.diskon = 0;
                        this.tindakan.diskon_persen = 0;
                        this.tindakan.harga_diskon = 0;
                        this.tindakan.subtotal = hargaRes.nilai;
                        this.tindakan.dokter_pengirim_id = trxData.dokter_pengirim_id;
                        this.tindakan.is_aktif = true;
                        this.tindakan.komponen = komp;
                        this.tindakan.bhp = data.bhp;
                    }
                }
            }           
        },

        dokterSelected(data) {
            if(data) {
                this.tindakan.dokter_id = data.dokter_id;
                this.tindakan.dokter_nama = data.dokter_nama;
            }
            else {
                this.tindakan.dokter_id = null;
                this.tindakan.dokter_nama = null;
            }
        },

        clearTindakan(){
            this.tindakan.tindakan_id = null;
            this.tindakan.tindakan_nama = null;
            this.tindakan.detail_id = null;
            this.tindakan.reg_id = null;
            this.tindakan.trx_id = null;
            this.tindakan.sub_trx_id = null;
            this.tindakan.item_id = null;
            this.tindakan.item_nama = null;
            this.tindakan.jenis = null;
            this.tindakan.jumlah = 1;
            this.tindakan.satuan = null;
            this.tindakan.klasifikasi =  null;
            this.tindakan.unit_id = this.poliTransaction.unit_id;
            this.tindakan.unit_nama = this.poliTransaction.unit_nama;
            this.tindakan.depo_id = null;
            this.tindakan.depo_nama = null;
            this.tindakan.bhp_tindakan = false;
            this.tindakan.kelas_harga_id = this.poliTransaction.kelas_harga_id;
            this.tindakan.buku_harga_id = this.poliTransaction.buku_harga_id;
            this.tindakan.nilai = 0;
            this.tindakan.diskon = 0;
            this.tindakan.diskon_persen = 0;
            this.tindakan.harga_diskon = 0;
            this.tindakan.subtotal = 0;            
            this.tindakan.dokter_pengirim_id = this.poliTransaction.dokter_pengirim_id;
            this.tindakan.is_racikan = false;
            this.tindakan.is_aktif = true;
            this.tindakan.signa = null;
            this.tindakan.signa_deskripsi = null;
            this.tindakan.komponen = [];
            this.tindakan.bhp = [];
        },

        depoSelected(){
            this.urlItemDepoPicker = `/depos/${this.bhp.depo_id}/products`;
        },

        tindakanBhpSelected(){
            let val = this.tindakanLists.find(data => data.item_id == this.bhp.tindakan_id);
            if(val) { this.bhp.tindakan_nama = val.item_nama; }
        },

        searchBhp(){
            this.urlPicker = `/depos/${this.bhp.depo_id}/products`;
            this.$refs.bhpModalPicker.showModal(this.urlPicker);
        },

        addBhp(dataVal){
            this.CLR_ERRORS();
            if(dataVal) {
                let trxData = this.poliTransaction;
                let data = JSON.parse(JSON.stringify(dataVal));
                this.examinationItems.push({
                    detail_id : null,
                    reg_id : null,
                    trx_id : null,
                    sub_trx_id : null,
                    item_id : data.produk_id,
                    item_nama : data.produk_nama,
                    jenis : 'BHP',
                    jumlah : data.jumlah,
                    jml_formula : data.jml_formula,
                    satuan : data.satuan,
                    klasifikasi :  data.jenis_produk,
                    unit_id : trxData.unit_id,
                    unit_nama : trxData.unit_nama,
                    depo_id : this.defaultDepo.depo_id,//data.depo_id,
                    depo_nama : this.defaultDepo.depo_nama,//data.depo_nama,
                    tindakan_id : data.tindakan_id,
                    tindakan_nama : data.tindakan_nama,
                    bhp_tindakan : data.bhp_tindakan,
                    kelas_harga_id : '-',
                    buku_harga_id : '-',
                    nilai : data.hna,
                    diskon : 0,
                    diskon_persen : 0,
                    harga_diskon : 0,
                    subtotal : data.hna,
                    dokter_nama : trxData.dokter_nama,
                    dokter_id : trxData.dokter_id,
                    dokter_pengirim_id : null,
                    is_bhp : true,
                    is_racikan : false,
                    is_aktif : true,
                    signa : null,
                    signa_deskripsi : null,
                    komponen : [],
                });

                this.calculateTotalBhp();
            }
        },

        calculateTotalBhp(){
            let total = 0;          
            this.bhpLists.forEach(data => {
                total = (total*1) + (data.subtotal*1);
            })
        },

        jmlBhpChange(){
            this.bhp.subtotal = this.bhp.jumlah * this.bhp.nilai;
        },

        calculateTotal(){
            let total = 0;          
            this.examination.total = total; 
            
            this.examinationItems.forEach(data => {
                if(data.is_aktif) {
                    total = (total*1) + (data.subtotal*1);
                    this.examination.total = total; 
                }
            })
        },

        calculateTotalBhp(){
            // let total = 0;          
            // this.poliTransaction.bhp.detail.forEach(data => {
            //     total = (total*1) + (data.subtotal*1);
            //     this.poliTransaction.bhp.total = total; 
            // })
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        bhpSelected(data){
            if(data) {
                this.bhp.item_id = data.produk_id;
                this.bhp.item_nama = data.produk_nama;
                this.bhp.satuan = data.satuan;
                this.bhp.jumlah = 1;
                this.bhp.nilai = data.hna;
                this.bhp.diskon = 0;
                this.bhp.diskon_persen = 0;
                this.bhp.harga_diskon = 0;
                this.bhp.subtotal = data.hna;
                this.bhp.dokter_nama = this.poliTransaction.dokter_nama;
                this.bhp.dokter_id = this.poliTransaction.dokter_id;
             }
        },

        addBhpManual(){
            this.bhp.is_bhp = true;
                
            if(this.bhp.depo_id == null || this.bhp.depo_id == '') {
                alert('depo tidak boleh kosong'); return false;
            }

            if(this.bhp.item_id == null || this.bhp.item_id == '') {
                alert('item bhp tidak boleh kosong'); return false;
            }

            if(this.bhp.tindakan_id !== null && this.bhp.tindakan_id !== '') {
                this.bhp.bhp_tindakan = false;
            }
            let val = this.tindakanLists.find(data => data.item_id == this.bhp.tindakan_id);
            if(val) { 
                this.bhp.tindakan_nama = val.item_nama; 
                this.bhp_tindakan = true;
            }
            this.examinationItems.push(JSON.parse(JSON.stringify(this.bhp)));
            this.clearBhpData();
            this.calculateTotal();
        },

        clearBhpData(){
            this.bhp.detail_id = null;
            this.bhp.reg_id = null;
            this.bhp.trx_id = null;
            this.bhp.sub_trx_id = null;
            this.bhp.item_id = null;
            this.bhp.item_nama = null;
            this.bhp.jumlah = 1;
            this.bhp.jenis = 'BHP';
            this.bhp.satuan = null;
            this.bhp.klasifikasi =  null;
            this.bhp.unit_id = null;
            this.bhp.unit_nama = null;
            this.bhp.depo_id = this.defaultDepo.depo_id;
            this.bhp.depo_nama = this.defaultDepo.depo_nama;
            this.bhp.tindakan_id = null;
            this.bhp.tindakan_nama = null;
            this.bhp.bhp_tindakan = false;
            this.bhp.kelas_harga_id = '-';
            this.bhp.buku_harga_id = '-';
            this.bhp.nilai = 0;
            this.bhp.diskon = 0;
            this.bhp.diskon_persen = 0;
            this.bhp.harga_diskon = 0;
            this.bhp.subtotal = 0;
            this.bhp.dokter_nama = null;
            this.bhp.dokter_id = null;
            this.bhp.dokter_pengirim_id = null;
            this.bhp.is_racikan = false;
            this.bhp.is_aktif = true;
            this.bhp.signa = null;            
            this.bhp.jml_formula = 0;
            this.bhp.signa_deskripsi = null;
            this.bhp.komponen = [];
        },

        onChangeJumlah(){
            // let jumlah = document.getElementById("jumlah").value;
            // this.itemResep.subtotal = jumlah * this.itemResep.harga;
        },

        insertTindakan(){
            this.CLR_ERRORS();
            let data = JSON.parse(JSON.stringify(this.tindakan));
            let trxData = this.poliTransaction;
            let exist = false;
            this.clearTindakan();
                        
            /**kroscek data pemeriksaan */
            if(data.item_id == null ||  data.item_id == '' ||  data.item_nama == null ||  data.item_nama == '') { 
                alert('tindakan tidak boleh kosong.'); 
                return false; 
            }
            if(data.dokter_id == null ||  data.dokter_id == '' ||  data.dokter_nama == null ||  data.dokter_nama == '') { 
                alert('dokter tidak boleh kosong.'); 
                return false; 
            }
            
            if(this.examinationItems){
                exist = this.examinationItems.find(item => item.item_id == data.item_id);
            }
            
            if(!exist) {
                this.examinationItems.push({
                    detail_id : null,
                    reg_id : null,
                    trx_id : null,
                    sub_trx_id : null,
                    jenis : 'TINDAKAN',
                    item_id : data.item_id,
                    item_nama : data.item_nama,
                    jml_formula : 1,
                    jumlah : data.jumlah,
                    satuan : data.satuan,
                    depo_id : null,
                    depo_nama : null,
                    kelas_harga_id : trxData.kelas_harga_id,
                    buku_harga_id : trxData.buku_harga_id,
                    nilai : data.nilai,
                    diskon : data.diskon,
                    diskon_persen : data.diskon_persen,
                    harga_diskon : data.harga_diskon,
                    subtotal : data.nilai * data.jumlah * 1,
                    dokter_nama : data.dokter_nama,
                    dokter_id : data.dokter_id,
                    dokter_pengirim_id : trxData.dokter_pengirim_id,
                    is_aktif : true,
                    is_bhp : false,
                    bhp_tindakan : false,
                    komponen : data.komponen,
                    bhp : data.bhp,
                });

                data.bhp.forEach(dt => { 
                    dt['bhp_tindakan'] = true; 
                    dt['hna'] = 0;
                    dt['subtotal'] = 0;
                    dt['jml_formula'] = dt['jumlah']*1;
                    dt['jumlah'] = dt['jumlah'] * data.jumlah;
                    dt['tindakan_id'] = data.item_id;
                    dt['tindakan_nama'] = data.item_nama;
                    this.addBhp(dt); 
                });
                this.calculateTotal();
                this.clearInputTindakan();
            }
            else { 
                exist.is_aktif = true; 
                //this.calculateTotal(); 
                this.clearInputTindakan();
            }
        },

        qtyTindakanChange(dt){
            this.tindakan.subtotal = this.tindakan.jumlah * this.tindakan.nilai * 1;
            this.calculateTotal();
        },

        changeActivationAct(dt){
            this.examinationItems.forEach(data => {
                if(data.is_aktif && data.tindakan_id == dt.item_id) {
                    data.is_aktif = dt.is_aktif; 
                    this.calculateTotal();
                }
            });
        
            let acts = this.examinationItems.filter( item => { return item.detail_id !== null || item.is_aktif == true });
            this.SET_EXAMINATION_ITEMS(acts);
            this.calculateTotal();
        },

        changeActivationBhp(dt){
            let items = this.examinationItems.filter( item => { return item.detail_id !== null || item.is_aktif == true });
            this.SET_EXAMINATION_ITEMS(items);
            this.calculateTotal();
        },

        simpanPemeriksaan(){
            this.CLR_ERRORS();
            if(this.isUpdate) {
                if(confirm(`Proses ini akan mengubah data pemeriksaan dan pemakaian BHP. Lanjutkan ?`)){
                    this.updatePemeriksaan(this.examination).then((response) => {
                        if (response.success == true) {
                            this.isUpdate = true;
                            //alert(response.message);
                            this.confirmFinishData(this.actId);
                        }
                        else { 
                            alert (response.message);
                        }
                    });
                };
            }
            else {
                if(confirm(`Proses ini akan membuat data baru pemeriksaan dan pemakaian BHP. Lanjutkan ?`)){
                    this.createPemeriksaan(this.examination).then((response) => {
                        if (response.success == true) {
                            this.isUpdate = true;
                            this.isLoading = false;
                            //this.actId = response.data.trx_id;
                            //alert(response.message);
                            this.confirmFinishData(response.data.trx_id);
                        }
                        else { 
                            alert (response.message) 
                        }
                    });
                };
            }
        },

        confirmFinishData(trxid){
            this.CLR_ERRORS();
            if(confirm(`Penyimpanan data selesai namun masih berstatus DRAFT. klik OK, untuk mengkonfirmasi data sudah selesai di-input dan bisa dikunci, atau CANCEL untuk lanjut melakukan pengisian data. Kunci data ?`)){
                this.isLoading = true;
                this.confirmPemeriksaan(trxid).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert(response.message);
                        this.formClosed();
                    }
                    else { 
                        alert(response.message); 
                        this.isLoading = false;
                    }
                })
            }
            else {
                this.formClosed();
            }
        },

        konfirmasiData(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan Mengubah status data ${this.actId} - ${this.examination.nama_pasien} dan mengunci perubahan. Lanjutkan ?`)){
                this.isLoading = true;
                this.isLoading = true;
                this.confirmPemeriksaan(this.actId).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert(response.message);
                        this.formClosed();
                    }
                    else { 
                        alert(response.message); 
                        this.isLoading = false;
                    }
                })
            }
        },

    }
}
</script>