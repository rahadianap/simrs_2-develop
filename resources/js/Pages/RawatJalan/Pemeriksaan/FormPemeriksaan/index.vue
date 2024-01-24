<template>
    <div style="padding:0;min-height:70vh;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="formClosed" style="background-color:white;padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:1"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">PEMERIKSAAN PASIEN</h4>
            </div>
        </div>
        <div style="background-color:#fff;margin-top:1em;">
            <div class="" style="margin:0;padding:0;">   
                <div style="margin:0;padding:0;">
                    <form action="" @submit.prevent="submisiRegPoli" style="margin:0;padding:0;">
                        <div style="padding:0.5em 0.5em 0.5em 1em;position: sticky; position: -webkit-sticky;top: 0; z-index:99;">
                            <div class="uk-grid-small uk-card1 uk-card-default1 hims-form-header" uk-grid style="padding:0;margin:0;">                        
                                <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                                    <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                                        <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="formClosed" style="padding:0.5em;"><span uk-icon="arrow-left"></span> Tutup</button>
                                        <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="retrieveData(poliTransaction)" style="padding:0.5em;"><span uk-icon="refresh"></span> Refresh</button>
                                        <button :disabled="isLoading" type="button" @click.prevent="batalKonfirmasiReg" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-edit"></span> Ubah Pemeriksaan</button>
                                        <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                                        <button :disabled="isLoading" type="submit" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-text"></span> Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                            <div uk-spinner="ratio : 2"></div>
                            <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                        </div>
                        
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>

                        <div v-if="poliTransaction.reg_id" class="uk-container uk-container-large">                    
                            <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;"> 
                                <div class="uk-grid-small hims-form-subpage" uk-grid style="border:none;padding:0.5em;background-color:#fafafa;">
                                    <div class="uk-width-1-1@m">
                                        <p style="font-size:14px;padding:0;margin:0;">ID.{{ poliTransaction.pasien_id }}</p>
                                        <h5 style="color:black;padding:0;margin:0;font-weight: 500;">{{ poliTransaction.nama_pasien }} ({{ poliTransaction.no_rm }})</h5>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>Dokter</dt>
                                            <dd>{{poliTransaction.dokter_nama}}</dd>
                                        </dl>
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>No.Antrian</dt>
                                            <dd>{{poliTransaction.no_antrian}}</dd>
                                        </dl>
                                    </div>
                                    <div class="uk-width-1-3@m">                                
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>Unit</dt>
                                            <dd>{{poliTransaction.unit_nama}}</dd>
                                        </dl>
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>Ruang</dt>
                                            <dd>{{poliTransaction.ruang_nama}}</dd>
                                        </dl>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>Penjamin</dt>
                                            <dd>{{poliTransaction.penjamin_nama}}</dd>
                                        </dl>
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>No.Registrasi</dt>
                                            <dd>{{poliTransaction.trx_id}}</dd>
                                        </dl>
                                    </div>                        
                                </div> 
                                <div class="hims-form-subpage" style="padding:0;margin:0;border:none;width:100%;">
                                    <div>
                                        <ul id="switcherPemeriksaanPoli" class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0;margin:0;">
                                            <li style="padding:0;"><div><a href="#"><h5>Assesmen</h5></a></div></li>
                                            <li style="padding:0;"><div><a href="#"><h5>Pemeriksaan dan BHP</h5></a></div></li>
                                            <li style="padding:0;"><div><a href="#"><h5>BHP</h5></a></div></li>
                                            <li style="padding:0;"><div><a href="#"><h5>Resep (Obat)</h5></a></div></li>
                                        </ul>
                                        <ul class="uk-switcher">
                                            <li style="padding:0;">
                                            </li>
                                            <li style="padding:0;">
                                                <sub-form-tindakan ref="subFormTindakan"></sub-form-tindakan>
                                            </li>
                                            <li style="padding:0;">
                                                <sub-form-bhp ref="subFormBhp"></sub-form-bhp>
                                            </li>
                                            <li style="padding:0;">
                                                <sub-form-resep ref="subFormResep"></sub-form-resep>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

import SubFormTindakan from '@/Pages/RawatJalan/Pemeriksaan/SubFormTindakan';
import SubFormBhp from '@/Pages/RawatJalan/Pemeriksaan/SubFormBhp';
import SubFormResep from '@/Pages/RawatJalan/Pemeriksaan/SubFormResep';

export default {
    name : 'form-pemeriksaan',
    components : { 
        SearchSelect,
        SearchList,
        SubFormTindakan,
        SubFormBhp,
        SubFormResep,
     },
    
    data() {
        return {
            isUpdate : false,
            isLoading : false,
            urlPicker : '',
            dokterUrl : '/doctors',
            isLocked : false,
            isExpandedReg : false,
            pickerColumns : [ 
                { name : 'tindakan_id', title : 'ID', colType : 'text', isCaption : false, },
                { name : 'tindakan_nama', title : 'Tindakan', colType : 'text', isCaption : true, },
                { name : 'deskripsi',title : 'Kelompok', colType : 'text', isCaption : false, },            
                { name : 'klasifikasi',title : 'Kelompok', colType : 'text', isCaption : false, },            
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            penjaminDesc: [
                { colName : 'penjamin_nama', labelData : '', isTitle : true },
                { colName : 'penjamin_id', labelData : '', isTitle : false },
            ],
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],

            asalPasienList : [
                { value : 'DATANG SENDIRI', title : 'DATANG SENDIRI', isInternal : false, isPasienLuar:false, },
                { value : 'RAWAT JALAN', title : 'RAWAT JALAN', isInternal : true, isPasienLuar:false, },
                { value : 'RAWAT INAP', title : 'RAWAT INAP', isInternal : true, isPasienLuar:false, },
                { value : 'PASIEN LUAR', title : 'PASIEN LUAR', isInternal : false, isPasienLuar: true, },
            ],
            
            transaksi : {
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                client_id : null,
                is_rujukan_int : false,
                jns_registrasi : null,
                tgl_transaksi : this.getTodayDate(),
                tgl_periksa : this.getTodayDate(),
                jam_periksa : null,
                no_antrian : null,

                jadwal_id : null,
                dokter_id : null,
                dokter_nama : null,
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                
                dokter_pengirim_id: null,
                dokter_pengirim: null,
                
                cara_registrasi : null,
                asal_pasien : null,
                ket_asal_pasien : null,
                
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                tempat_lahir : null,
                tgl_lahir : null,
                nik : null,
                jns_kelamin : null,
                is_pasien_baru : null,
                is_pasien_luar : false,                
                jns_penjamin : null,
                penjamin_id : null,                
                penjamin_nama : null,

                kelas_harga : null,
                buku_harga_id : null,
                buku_harga : null,
                total : 0,

                is_bpjs : false,
                status : null,
                is_aktif : true,
                client_id : null,
                reff_trx_id : null,
                is_realisasi : false,     
                tindakan : [],           
                detail : [],
                pasien : {
                    pasien_id : null,
                    nama_pasien : null,
                    no_rm : null,
                    tempat_lahir : null,
                    tgl_lahir : null,
                    usia : null,
                },
            },

            unitLists : [],
            dokterLists : [],
            roomLists : [],
            kelasHargaLists : [],
            minDate : this.getTodayDate(),
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliTransactionLists','filterTable']),
        
        ...mapGetters('kelas',['classLists']),
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
        ...mapGetters('asuransi',['asuransiLists']),
        ...mapGetters('referensi',['isRefExist','jenisPelayananRefs','jenisRegistrasiRefs','asalPasienRefs',
            'caraRegistrasiRefs','salutRefs','jenisPenjaminRefs','hubKeluargaRefs',]),    
    },

    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapActions('rawatJalan',['dataTransaksiPoli','updateTransaksiPoli','createTransaksiPoli']),
        ...mapActions('pasien',['dataPasien']),

        
        ...mapActions('labRegistrasi',['dataRegistrasi','createRegistrasi','updateRegistrasi','deleteRegistrasi','confirmRegistrasi']),
        
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('asuransi',['listAsuransi','dataAsuransi']),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('kelas',['listKelas']),
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',[
            'CLR_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION_LISTS',
            'CLR_POLI_TRANSACTION_LISTS',
            'SET_FILTER_TABLE_POLI'
        ]),

        initialize(){
            this.CLR_ERRORS();     
            if(!this.isRefExist) { this.listReferensi({per_page:'ALL'}); }     

            this.listUnitPelayanan({'jenis_reg':this.poliTransaction.jns_registrasi, 'per_page':'ALL'}).then((response) => {
                if (response.success == true) { 
                    this.unitLists = response.data.data;
                }
            })      

            this.listKelas({per_page:'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.kelasHargaLists = response.data.data.filter(function (el) { return el.is_kelas_harga == true; });
                } 
                else { alert(response.message); }
            })  
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

        getTimeNow(){
            let currentDate = new Date();
            let hours = currentDate.getHours();
            if(hours < 10){ hours = `0${hours}` };
            let minutes = currentDate.getMinutes();
            if(minutes < 10){ minutes = `0${minutes}` };
            
            let time = hours + ":" + minutes;
            return time;
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        /** function called before modal closed */
        formClosed(){
            this.clearRegistrasi();
            this.isUpdate = false;
            this.$emit('formPemeriksaanClosed',true);
        },
        
        /**modal call from other component for new data entry */
        newData(){
            this.CLR_ERRORS();
            this.isUpdate = false;
            this.clearRegistrasi();
            if(this.$refs.listAsalPasien) {
                this.$refs.listAsalPasien.showMasterPasien();
            }
        },

        searchPasien(){
            this.$emit('searchPasien',true);
        },
        
        /**modal call from other component for update data entry */
        retrieveData(refData) {
            this.CLR_ERRORS();
            //this.clearTransaksi();
            this.isUpdate = true;
            this.isLoading = true;
            if(refData) {
                this.dataTransaksiPoli(refData.trx_id).then((response) => {
                    if (response.success == true) {
                        this.SET_POLI_TRANSACTION(response.data);
                        this.isUpdate = true;
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

        refreshData(){
            // this.CLR_ERRORS();
            // this.isLoading = true;
            // this.dataTransaksiPoli(this.poliTransaction.trx_id).then((response) => {
            //     if (response.success == true) {
            //         this.fillTransaksi(response.data);
            //         this.isUpdate = true;
            //         this.isLoading = false;
            //     }
            //     else { alert('data transaksi pasien poli tidak ditemukan'); this.isLoading = false; }
            // })
        },

        clearTransaksi() {
            this.poliTransaction.reg_id = null;
            this.poliTransaction.trx_id = null;
            this.poliTransaction.sub_trx_id = null;
            this.poliTransaction.client_id = null;
            this.poliTransaction.is_rujukan_int = false;
            this.poliTransaction.jns_registrasi = null;
            this.poliTransaction.tgl_transaksi = this.getTodayDate();
            this.poliTransaction.tgl_periksa = this.getTodayDate();
            this.poliTransaction.jam_periksa = this.getTimeNow();
            this.poliTransaction.no_antrian = null;

            this.poliTransaction.jadwal_id = null;
            this.poliTransaction.dokter_id = null;
            this.poliTransaction.dokter_nama = null;
            this.poliTransaction.unit_id = null;
            this.poliTransaction.unit_nama = null;
            this.poliTransaction.ruang_id = null;
            this.poliTransaction.ruang_nama = null;

            this.poliTransaction.dokter_pengirim_id= null;
            this.poliTransaction.dokter_pengirim= null;

            this.poliTransaction.cara_registrasi = null;
            this.poliTransaction.asal_pasien = null;
            this.poliTransaction.ket_asal_pasien = null;
                
            this.poliTransaction.pasien_id = null;
            this.poliTransaction.no_rm = null;
            this.poliTransaction.nama_pasien = null;
            this.poliTransaction.tempat_lahir = null;
            this.poliTransaction.tgl_lahir = null;
            this.poliTransaction.nik = null;
            this.poliTransaction.jns_kelamin = null;

            this.poliTransaction.is_pasien_baru = null;
            this.poliTransaction.is_pasien_luar = false;
                
            this.poliTransaction.jns_penjamin = null;
            this.poliTransaction.penjamin_id = null;      
            this.poliTransaction.buku_harga_id = null;    
            this.poliTransaction.total = 0;      
                      
            this.poliTransaction.penjamin_nama = null;
            this.poliTransaction.is_bpjs = false;
            this.poliTransaction.status = null;
            this.poliTransaction.is_aktif = true;
            this.poliTransaction.client_id = null;
            this.poliTransaction.reff_trx_id = null;
            this.poliTransaction.is_realisasi = false;
                
            this.poliTransaction.detail = [];
            this.minDate = this.getTodayDate();   
            this.clearPasien();
        },

        clearPasien() {
            this.poliTransaction.pasien.pasien_id = null;
            this.poliTransaction.pasien.nama_pasien = null;
            this.poliTransaction.pasien.no_rm = null;
            this.poliTransaction.pasien.tempat_lahir = null;
            this.poliTransaction.pasien.tgl_lahir = null;
            this.poliTransaction.pasien.usia = null;
        },

        fillTransaksi(data){
            //let unit = this.unitLists.find( item => item.unit_id == data.unit_id );
            //this.listAsuransi({per_page:'ALL',jns_penjamin:data.jns_penjamin, is_aktif:true }).then((response) => {});

            // if(unit) { 
            //     this.roomLists = unit.ruang; 
            //     this.dokterLists = unit.dokter; 
            // }

            this.poliTransaction.reg_id = data.reg_id;
            this.poliTransaction.trx_id = data.trx_id;
            this.poliTransaction.sub_trx_id = data.sub_trx_id;
            this.poliTransaction.is_rujukan_int = data.is_rujukan_int;
            
            this.poliTransaction.client_id = data.client_id;
            this.poliTransaction.jns_registrasi = data.jns_registrasi;
            this.poliTransaction.cara_registrasi = data.cara_registrasi;
            this.poliTransaction.tgl_transaksi = data.tgl_transaksi;
                
            this.poliTransaction.tgl_periksa = data.tgl_periksa;
            this.poliTransaction.jam_periksa = data.jam_periksa;
            this.poliTransaction.no_antrian = data.no_antrian;
                
            this.poliTransaction.jadwal_id = data.jadwal_id;
            this.poliTransaction.dokter_id = data.dokter_id;
            this.poliTransaction.dokter_nama = data.dokter_nama;
            this.poliTransaction.dokter_pengirim_id = data.dokter_pengirim_id;
            this.poliTransaction.dokter_pengirim = data.dokter_pengirim;
                
            this.poliTransaction.unit_id = data.unit_id;
            this.poliTransaction.unit_nama = data.unit_nama;
            this.poliTransaction.ruang_id = data.ruang_id;
            this.poliTransaction.ruang_nama = data.ruang_nama;
            
            
            this.poliTransaction.kelas_harga = data.kelas_harga_id;
            //this.poliTransaction.total = data.poliTransaction.total;
            
            this.poliTransaction.asal_pasien = data.asal_pasien;
            this.poliTransaction.ket_asal_pasien = data.ket_asal_pasien;

            this.poliTransaction.pasien_id = data.pasien_id;
            this.poliTransaction.nama_pasien = data.nama_pasien;
            this.poliTransaction.no_rm = data.no_rm;
            this.poliTransaction.jns_kelamin = data.jns_kelamin;
            this.poliTransaction.nik = data.nik;
            
            this.poliTransaction.tempat_lahir = data.tempat_lahir;
            this.poliTransaction.tgl_lahir = data.tgl_lahir;
            
            this.poliTransaction.is_pasien_baru = data.is_pasien_baru;
            this.poliTransaction.is_pasien_luar = data.is_pasien_luar;
                
            this.poliTransaction.jns_penjamin = data.jns_penjamin;
            this.poliTransaction.penjamin_id = data.penjamin_id;                
            this.poliTransaction.penjamin_nama = data.penjamin_nama;
            this.poliTransaction.no_kepesertaan = data.no_kepesertaan;
            this.poliTransaction.is_bpjs = data.is_bpjs;

            this.poliTransaction.buku_harga = data.buku_harga;
            this.poliTransaction.buku_harga_id = data.buku_harga_id;
                
            this.poliTransaction.status = data.status;
            this.poliTransaction.is_aktif = data.is_aktif;
            this.poliTransaction.client_id = data.client_id;
            this.poliTransaction.is_realisasi = data.is_realisasi;
            this.poliTransaction.detail = data.detail;
            this.minDate = this.poliTransaction.tgl_periksa;
            this.isUpdate = true;
            this.fillPasien(data.pasien);
            this.$refs.subFormTindakan.setValue(this.transaksi);
        },

        fillPasien(data) {
            this.poliTransaction.pasien.pasien_id = data.pasien_id;
            this.poliTransaction.pasien.nama_pasien = data.nama_pasien;
            this.poliTransaction.pasien.no_rm = data.no_rm;
            this.poliTransaction.pasien.tempat_lahir = data.tempat_lahir;
            this.poliTransaction.pasien.tgl_lahir = data.tgl_lahir;
            this.poliTransaction.pasien.usia = data.usia;
        },

        refreshPasien(data){
            this.CLR_ERRORS();
            if(data) {
                this.isLoading = true;
                this.dataPasien(data.pasien_id).then((response) => {
                    if (response.success == true) {
                        this.fillDataFromMasterPasien(response.data);
                        this.isUpdate = false;
                        this.isLoading = false;
                    }
                    else { alert('data pasien tidak ditemukan'); this.isLoading = false; }
                })                
            }
        },

        fillDataFromMasterPasien(data) {
            this.poliTransaction.nama_pasien = data.nama_lengkap;
            this.poliTransaction.pasien_id = data.pasien_id;
            this.poliTransaction.nik = data.nik;
            this.poliTransaction.tempat_lahir = data.tempat_lahir;
            this.poliTransaction.tgl_lahir = data.tgl_lahir;
            this.poliTransaction.no_rm = data.no_rm;
            this.poliTransaction.jns_kelamin = data.jns_kelamin;
            
            this.poliTransaction.penjamin_id = data.penjamin_id;
            this.poliTransaction.penjamin_nama = data.penjamin_nama;
            this.poliTransaction.jns_penjamin = data.jns_penjamin;
            this.poliTransaction.no_kepesertaan = data.no_kepesertaan;
            this.poliTransaction.asal_pasien = 'DATANG SENDIRI';
            this.poliTransaction.is_rujukan_int = false;

            this.dataAsuransi(data.penjamin_id).then((response) => {
                if (response.success == true) {
                    this.poliTransaction.buku_harga_id = response.data.buku_harga_id;
                    this.poliTransaction.buku_harga = response.data.buku_harga;
                }
                else { alert('data penjamin tidak ditemukan'); }
            })   

            if(this.unitLists.length == 1) {
                this.poliTransaction.unit_id = this.unitLists[0].unit_id;
                this.poliTransaction.unit_nama = this.unitLists[0].unit_nama;
                this.roomLists = this.unitLists[0].ruang;
                this.dokterLists = this.unitLists[0].dokter;
                if(this.roomLists.length == 1) {
                    this.poliTransaction.ruang_id = this.roomLists[0].ruang_id;
                    this.poliTransaction.ruang_nama = this.roomLists[0].ruang_nama;
                }
                if(this.dokterLists.length == 1) {
                    this.poliTransaction.dokter_id = this.dokterLists[0].dokter_id;
                    this.poliTransaction.dokter_nama = this.dokterLists[0].dokter_nama;
                }                
            }
        },

        refreshPasienInap(data) {
            this.CLR_ERRORS();
            this.clearRegistrasi();
            if(data){                
                this.isLoading = true;
                this.dataAdmisiInap(data.trx_id).then((response) => {
                    if (response.success == true) {
                        this.fillDataFromPasienInap(response.data);
                        this.isUpdate = false;
                        this.isLoading = false;
                        //UIkit.switcher('#switcherFormRegLab').show(1);
                    }
                    else { alert('data pasien tidak ditemukan'); this.isLoading = false; }
                })       
            }
            //UIkit.switcher('#switcherFormRegLab').show(1);
        },

        fillDataFromPasienInap(data){
            this.poliTransaction.reg_id = data.reg_id;
            this.poliTransaction.trx_id = data.trx_id;
            this.poliTransaction.sub_trx_id = null;
            
            /*isi unit dari rawat inap*/
            this.poliTransaction.unit_asal_id = data.unit_id;
            this.poliTransaction.unit_asal = data.unit_nama;
            this.poliTransaction.ruang_asal_id = data.ruang_id;
            this.poliTransaction.ruang_asal = data.ruang_nama;
            this.poliTransaction.bed_asal_id = data.bed_asal_id;
            this.poliTransaction.no_asal_bed = data.no_bed;
            
            this.poliTransaction.kelas_harga = data.kelas_harga_id;
            this.poliTransaction.dokter_pengirim_id = data.dokter_id;
            this.poliTransaction.dokter_pengirim = data.dokter_nama;
            
            this.poliTransaction.nama_pasien = data.nama_pasien;
            this.poliTransaction.pasien_id = data.pasien_id;
            this.poliTransaction.nik = data.pasien.nik;
            this.poliTransaction.tempat_lahir = data.pasien.tempat_lahir;
            this.poliTransaction.tgl_lahir = data.pasien.tgl_lahir;
            this.poliTransaction.no_rm = data.pasien.no_rm;
            this.poliTransaction.jns_kelamin = data.pasien.jns_kelamin;
            
            this.poliTransaction.penjamin_id = data.penjamin_id;
            this.poliTransaction.penjamin_nama = data.penjamin_nama;
            this.poliTransaction.jns_penjamin = data.jns_penjamin;
            this.poliTransaction.no_kepesertaan = data.no_kepesertaan;
            this.poliTransaction.asal_pasien = 'RAWAT_INAP';
            this.poliTransaction.is_rujukan_int = true;

            this.dataAsuransi(data.penjamin_id).then((response) => {
                if (response.success == true) {
                    this.poliTransaction.buku_harga_id = response.data.buku_harga_id;
                    this.poliTransaction.buku_harga = response.data.buku_harga;
                }
                else { alert('data penjamin tidak ditemukan'); }
            })
            if(this.unitLists.length == 1) {
                this.poliTransaction.unit_id = this.unitLists[0].unit_id;
                this.poliTransaction.unit_nama = this.unitLists[0].unit_nama;
                this.roomLists = this.unitLists[0].ruang;
                this.dokterLists = this.unitLists[0].dokter;
                if(this.roomLists.length == 1) {
                    this.poliTransaction.ruang_id = this.roomLists[0].ruang_id;
                    this.poliTransaction.ruang_nama = this.roomLists[0].ruang_nama;
                }
                if(this.dokterLists.length == 1) {
                    this.poliTransaction.dokter_id = this.dokterLists[0].dokter_id;
                    this.poliTransaction.dokter_nama = this.dokterLists[0].dokter_nama;
                }                
            }
        },

        refreshPasienPoli(data){
            this.CLR_ERRORS();
            this.clearRegistrasi();
        },

        clearRegistrasi() {
            this.poliTransaction.reg_id = null;
            this.poliTransaction.trx_id = null;
            this.poliTransaction.sub_trx_id = null;
            this.poliTransaction.client_id = null;
            this.poliTransaction.is_rujukan_int = false;
            this.poliTransaction.jns_registrasi = 'LABORATORIUM';
            this.poliTransaction.tgl_transaksi = this.getTodayDate();
            this.poliTransaction.tgl_periksa = this.getTodayDate();
            this.poliTransaction.jam_periksa = this.getTimeNow();
                
            this.poliTransaction.dokter_unit_id = null;
            this.poliTransaction.dokter_id = null;
            this.poliTransaction.dokter_nama = null;
                
            this.poliTransaction.dokter_pengirim_id= null;
            this.poliTransaction.dokter_pengirim= null;
                
            this.poliTransaction.unit_id = null;
            this.poliTransaction.unit_nama = null;
            this.poliTransaction.ruang_id = null;
            this.poliTransaction.ruang_nama = null;
                
            this.poliTransaction.unit_asal_id = null;
            this.poliTransaction.unit_asal = null;
            this.poliTransaction.ruang_asal_id = null;
            this.poliTransaction.ruang_asal = null;
            this.poliTransaction.bed_asal_id = null;
            this.poliTransaction.no_asal_bed = null;

            this.poliTransaction.cara_registrasi = null;
            this.poliTransaction.asal_pasien = null;
            this.poliTransaction.ket_asal_pasien = null;
                
            this.poliTransaction.pasien_id = null;
            this.poliTransaction.no_rm = null;
            this.poliTransaction.nama_pasien = null;
            this.poliTransaction.tempat_lahir = null;
            this.poliTransaction.tgl_lahir = null;
            this.poliTransaction.nik = null;
            this.poliTransaction.jns_kelamin = null;

            this.poliTransaction.is_pasien_baru = null;
            this.poliTransaction.is_pasien_luar = false;
                
            this.poliTransaction.jns_penjamin = null;
            this.poliTransaction.penjamin_id = null;      
            this.poliTransaction.buku_harga_id = null;    
            this.poliTransaction.total = 0;      
                      
            this.poliTransaction.penjamin_nama = null;
            this.poliTransaction.is_bpjs = false;
            this.poliTransaction.status = null;
            this.poliTransaction.is_aktif = true;
            this.poliTransaction.client_id = null;
            this.poliTransaction.reff_trx_id = null;
            this.poliTransaction.is_realisasi = false;
                
            this.poliTransaction.detail = [];
            this.minDate = this.getTodayDate();   

        },

        fillRegistrasi(data){
            let unit = this.unitLists.find( item => item.unit_id == data.unit_id );
            //this.listAsuransi({per_page:'ALL',jns_penjamin:data.jns_penjamin, is_aktif:true }).then((response) => {});

            if(unit) { 
                this.roomLists = unit.ruang; 
                this.dokterLists = unit.dokter; 
            }
            this.poliTransaction.reg_id = data.reg_id;
            this.poliTransaction.trx_id = data.trx_id;
            this.poliTransaction.sub_trx_id = data.sub_trx_id;
            this.poliTransaction.is_rujukan_int = data.is_rujukan_int;
            
            this.poliTransaction.client_id = data.client_id;
            this.poliTransaction.jns_registrasi = data.jns_registrasi;
            this.poliTransaction.cara_registrasi = data.cara_registrasi;
            this.poliTransaction.tgl_transaksi = data.tgl_transaksi;
                
            this.poliTransaction.tgl_periksa = data.tgl_periksa;
            this.poliTransaction.jam_periksa = data.jam_periksa;
                
            this.poliTransaction.dokter_unit_id = data.dokter_unit_id;
            this.poliTransaction.dokter_id = data.dokter_id;
            this.poliTransaction.dokter_nama = data.dokter_nama;
            this.poliTransaction.dokter_pengirim_id = data.dokter_pengirim_id;
            this.poliTransaction.dokter_pengirim = data.dokter_pengirim;
                
            this.poliTransaction.unit_id = data.unit_id;
            this.poliTransaction.unit_nama = data.unit_nama;
            this.poliTransaction.ruang_id = data.ruang_id;
            this.poliTransaction.ruang_nama = data.ruang_nama;
            
            this.poliTransaction.unit_asal_id = data.unit_asal_id;
            this.poliTransaction.unit_asal = data.unit_asal;
            this.poliTransaction.ruang_asal_id = data.ruang_asal_id;
            this.poliTransaction.ruang_asal = data.ruang_asal;
            this.poliTransaction.bed_asal_id = data.bed_asal_id;
            this.poliTransaction.no_asal_bed = data.no_asal_bed;
            
            this.poliTransaction.kelas_harga = data.kelas_harga;
            //this.poliTransaction.total = data.poliTransaction.total;
            
            this.poliTransaction.asal_pasien = data.asal_pasien;
            this.poliTransaction.ket_asal_pasien = data.ket_asal_pasien;

            this.poliTransaction.pasien_id = data.pasien_id;
            this.poliTransaction.nama_pasien = data.nama_pasien;
            this.poliTransaction.no_rm = data.no_rm;
            this.poliTransaction.jns_kelamin = data.jns_kelamin;
            this.poliTransaction.nik = data.nik;
            
            this.poliTransaction.tempat_lahir = data.tempat_lahir;
            this.poliTransaction.tgl_lahir = data.tgl_lahir;
            
            this.poliTransaction.is_pasien_baru = data.is_pasien_baru;
            this.poliTransaction.is_pasien_luar = data.is_pasien_luar;
                
            this.poliTransaction.jns_penjamin = data.jns_penjamin;
            this.poliTransaction.penjamin_id = data.penjamin_id;                
            this.poliTransaction.penjamin_nama = data.penjamin_nama;
            this.poliTransaction.no_kepesertaan = data.no_kepesertaan;
            this.poliTransaction.is_bpjs = data.is_bpjs;

            this.poliTransaction.buku_harga = data.buku_harga;
            this.poliTransaction.buku_harga_id = data.buku_harga_id;
                
            this.poliTransaction.status = data.status;
            this.poliTransaction.is_aktif = data.is_aktif;
            this.poliTransaction.client_id = data.client_id;
            this.poliTransaction.is_realisasi = data.is_realisasi;
            this.poliTransaction.detail = data.detail;
            this.minDate = this.poliTransaction.tgl_periksa;
            this.isUpdate = true;
        },

        ambilPenjaminList(){
            this.poliTransaction.penjamin_id = null;
            this.poliTransaction.penjamin_nama = null;
            let dt = this.jenisPenjaminRefs.json_data.find(item => item.value === this.poliTransaction.jns_penjamin);
            if(dt) { this.poliTransaction.is_bpjs = dt.is_bpjs; }            
            this.listAsuransi({per_page:'ALL',jns_penjamin:this.poliTransaction.jns_penjamin, is_aktif:true }).then((response) => {
                console.log(response.data);
            });
        },

        penjaminSelected(data){
            if(data) {
                this.poliTransaction.penjamin_id = data.penjamin_id;
                this.poliTransaction.penjamin_nama = data.penjamin_nama;
                this.poliTransaction.buku_harga_id = data.buku_harga_id;
                this.poliTransaction.buku_harga = data.buku_harga;
                
                this.poliTransaction.is_bpjs = false;
                if(data.is_bpjs) {
                    this.poliTransaction.is_bpjs = data.is_bpjs;
                }

                if(this.isUpdate) { this.submisiRegPoli(); }
            }
        },

        ruangSelected() {
            let ruangSelected = this.roomLists.find(item => item.ruang_id === this.poliTransaction.ruang_id);
            if(ruangSelected) {
                this.poliTransaction.ruang_nama = ruangSelected.ruang_nama;
            }
        },

        dokterSelected() {
            let data = this.dokterLists.find( item => item.dokter_id == this.poliTransaction.dokter_id );
            if(data) {
                this.poliTransaction.dokter_id = data.dokter_id;
                this.poliTransaction.dokter_nama = data.dokter_nama;
                this.poliTransaction.dokter_unit_id = data.dokter_unit_id;
            }
        },

        dokterPengirimSelected(data){
            this.poliTransaction.dokter_pengirim_id = null;
            this.poliTransaction.dokter_pengirim = null;

            if(data) {
                this.poliTransaction.dokter_pengirim_id = data.dokter_id;
                this.poliTransaction.dokter_pengirim = data.dokter_nama;
            }
        },

        unitSelected() {
            let data = this.unitLists.find( item => item.unit_id == this.poliTransaction.unit_id );
            if(data) {
                this.poliTransaction.unit_id = data.unit_id;
                this.poliTransaction.unit_nama = data.unit_nama;
                this.roomLists = data.ruang;
                this.dokterLists = data.dokter;
                this.poliTransaction.ruang_id = null;
                this.poliTransaction.ruang_nama = null;
                this.poliTransaction.bed_id = null;
                this.poliTransaction.no_bed = null;
            }
        },

        searchTindakan(data){
            if(this.poliTransaction.unit_id == null || 
                this.poliTransaction.unit_id == '' || 
                this.poliTransaction.kelas_harga == null ||
                this.poliTransaction.kelas_harga == '') 
            {
                alert('Pilih unit pelayanan dan kelas terlebih dahulu.');
            }

            else {
                this.urlPicker = `units/${this.poliTransaction.unit_id}/acts`;
                this.$refs.actModalPicker.showModalPicker(this.urlPicker);
            }
        },

        roomSelected(data){
            console.log(data);
            if(data) { 
                this.poliTransaction.no_bed = data.no_bed;
                this.poliTransaction.bed_id = data.bed_id;
                this.poliTransaction.ruang_id = data.ruang_id;
                this.poliTransaction.ruang_nama = data.ruang_nama;  
                this.poliTransaction.kelas_harga = data.kelas_harga_id; 
                this.$refs.roomModalPicker.closeModalPicker(); 
            }
        },

        updateKelasHarga(){
            if(this.isUpdate){
                this.submisiRegPoli();
            }
        },

        asalPasienChange(){
            let val = this.asalPasienList.find(item => item.value == this.poliTransaction.asal_pasien );
            if(val) {
                this.poliTransaction.is_rujukan_int = val.isInternal;
                this.poliTransaction.is_pasien_luar = val.isPasienLuar;
            }
        },

        submisiRegPoli(){
            alert('disini bro');
            this.CLR_ERRORS();
            if(this.isUpdate) {
                this.isLoading = true;
                this.updateTransaksiPoli(this.poliTransaction).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        this.isUpdate = true;
                        //this.fillRegistrasi(response.data);
                        alert('Data berhasil disimpan.');
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
            else {
                //data baru
                this.isLoading = true;
                this.createTransaksiPoli(this.poliTransaction).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        this.isUpdate = true;
                        //this.fillRegistrasi(response.data);
                        alert('Data berhasil disimpan.');
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
        },

        hapusRegistrasi(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus data registrasi ${this.poliTransaction.sub_trx_id} - ${this.poliTransaction.nama_pasien}. Lanjutkan ?`)){
                this.isLoading = true;
                this.deleteRegistrasi(this.poliTransaction.sub_trx_id).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; this.isUpdate = false;
                        alert('data registrasi lab BERHASIL dihapus.');
                        this.formClosed();
                    }
                    else { 
                        alert('data registrasi lab tidak berhasil dihapus.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        lockData(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan MENGUNCI data registrasi ${this.poliTransaction.trx_id} - ${this.poliTransaction.pasien.nama_pasien} dan membuat data transaksi dari pemakaian bed. Lanjutkan ?`)){
                this.isLoading = true;
                this.confirmAdmisiInap(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('data registrasi inap BERHASIL dikonfirmasi.');
                        this.formClosed();
                    }
                    else { 
                        alert('data registrasi inap tidak berhasil dikonfirmasi.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        addPemeriksaan(dataVal){

            this.CLR_ERRORS();
            if(dataVal.length < 1) { return false; }
            else {
                let addedTindakan = JSON.parse(JSON.stringify(dataVal));
                addedTindakan.forEach(data => {
                    let exist = this.poliTransaction.detail.find(item => item.item_id == data.tindakan_id);
                    let hargaRes = data.harga.find(item => item.buku_harga_id == this.poliTransaction.buku_harga_id && item.kelas_id == this.poliTransaction.kelas_harga);

                    if(!exist) {
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

                        this.poliTransaction.detail.push({
                            detail_id : null,
                            reg_id : null,
                            trx_id : null,
                            sub_trx_id : null,
                            item_id : data.tindakan_id,
                            item_nama : data.tindakan_nama,
                            jumlah : 1,
                            satuan : 'X',
                            kelas_harga_id : this.poliTransaction.kelas_harga,
                            buku_harga_id : this.poliTransaction.buku_harga_id,
                            nilai : hargaRes.nilai,
                            diskon : 0,
                            diskon_persen : 0,
                            harga_diskon : 0,
                            subtotal : hargaRes.nilai,
                            dokter_id : this.poliTransaction.dokter_id,
                            dokter_pengirim_id : this.poliTransaction.dokter_pengirim_id,
                            is_aktif : true,
                            komponen : komp,
                        });
                    }
                    else { exist.is_aktif = true; }
                    this.calculateTotalLab();
                });

            }
        },

        konfirmasiReg(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan Mengubah status data registrasi ${this.poliTransaction.sub_trx_id} - ${this.poliTransaction.nama_pasien}. Lanjutkan ?`)){
                this.isLoading = true;
                this.confirmRegistrasi(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('data registrasi lab BERHASIL dikonfirmasi.');
                        this.formClosed();
                    }
                    else { 
                        alert('data registrasi lab tidak berhasil dikonfirmasi.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        changeActivationActItem(){
            this.poliTransaction.detail = this.poliTransaction.detail.filter(
                item => { return item.detail_id !== null || item.is_aktif == true }
            );
        },

        calculateTotalLab(){  
            let total = 0;          
            this.poliTransaction.detail.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.poliTransaction.total = total; 
            })
        },


    }
}
</script>
<style>
th.tb-header-reg {
    padding:1em; 
    background-color:#cc0202; 
    color:white;
}
</style>