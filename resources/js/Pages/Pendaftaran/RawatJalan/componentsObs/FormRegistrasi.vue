<template>    
    <div style="margin:0;padding:0;">
        <form action="" @submit.prevent="submitAdmisiInap" style="margin:0;padding:0;">
            <div style="padding:0.5em 0.5em 0.5em 1em;">
                <div class="uk-grid-small uk-card1 uk-card-default1 hims-form-header" uk-grid style="padding:0;margin:0;">                        
                    <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                        <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                            <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="modalClosed" style="padding:0.5em;"><span uk-icon="arrow-left"></span> Kembali</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="hapusRegistrasi" :disabled="!isUpdate" style="padding:0.5em;"><span uk-icon="trash"></span> Hapus</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="sepInap" :disabled="!isUpdate" style="padding:0.5em;"><span uk-icon="file"></span> SEP BPJS</button>
                            <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                            <button type="submit" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-text"></span> Simpan</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="lockData" :disabled="!isUpdate" style="padding:0.5em;"><span uk-icon="file-edit"></span> Konfirmasi</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-container uk-container-large">                
                <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                    <div uk-spinner="ratio : 2"></div>
                    <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                </div>
                
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>
                
                <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">     
                    <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">                        
                        <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                detail data pasien.
                            </p>
                        </div>
                        <div v-if="registrasi.pasien.pasien_id == null" class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;text-align:center;">                        
                            <p style="font-style: italic;color:black;">(pasien belum dipilih)</p>
                        </div>
                        <div v-else class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">       
                            <div class="uk-width-1-1">
                                <h5 style="font-weight: 500;padding:0;margin:0;">{{registrasi.pasien.no_rm}} - {{registrasi.pasien.nama_lengkap}} ({{registrasi.pasien.jns_kelamin}})</h5>
                                <p style="margin:0;padding:0;font-size: 11px; font-style:italic;">*Data pasien akan mengikuti perubahan master data pasien.</p>
                            </div>         
                            <div class="uk-width-1-1"></div>
                            <div class="uk-width-1-4@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>ID Pasien </dt>
                                    <dd>{{registrasi.pasien.pasien_id}}</dd>
                                </dl>
                            </div>        
                            <div class="uk-width-1-4@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Tempat | Tgl Lahir </dt>
                                    <dd>{{registrasi.pasien.tempat_lahir}} , {{registrasi.pasien.tgl_lahir}}</dd>
                                </dl>
                            </div>
                            <div class="uk-width-1-4@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>KTP (ID Card)</dt>
                                    <dd>{{registrasi.pasien.nik}}</dd>
                                </dl>
                            </div>
                            <div class="uk-width-1-4@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Jenis Kelamin</dt>
                                    <dd v-if="registrasi.pasien.jns_kelamin == 'L'">Laki-laki</dd>
                                    <dd v-else-if="registrasi.pasien.jns_kelamin == 'P'">Perempuan</dd>
                                    <dd v-else>(Tidak tahu)</dd>                                
                                </dl>
                            </div>
                            <div class="uk-width-1-1"></div>
                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Alamat KTP</dt>
                                    <dd>{{registrasi.pasien.alamat}}</dd>
                                </dl>
                            </div>
                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">                                
                                    <dt>Alamat Tinggal</dt>
                                    <dd>{{registrasi.pasien.alamat_tinggal}}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                        <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA REGISTRASI</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                detail data pendaftaran rawat inap.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">                        
                            <div class="uk-width-1-1">
                                <h5 style="font-weight:500;">{{registrasi.reg_id}}</h5>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit Pelayanan*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.unit_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="unitSelected"> 
                                        <option v-for="option in unitLists" v-bind:value="option.unit_id" v-bind:key="option.unit_id">{{option.unit_nama}}</option>
                                    </select>
                                    <!-- <input v-else class="uk-input uk-form-small" type="text" v-model="registrasi.unit_nama" style="color:black;" readonly :disabled="isUpdate"> -->
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Ruang Inap*</label>
                                <div class="uk-inline uk-width-1-1">
                                    <a class="uk-form-icon uk-form-icon-flip" href="#" uk-icon="icon: search" @click.prevent="searchRuangInap"></a>
                                    <input class="uk-input uk-form-small" readonly v-model="registrasi.ruang_nama" type="text" aria-label="Clickable icon" style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Bed*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="text" v-model="registrasi.no_bed" style="color:black;" readonly>
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Periksa*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_masuk" style="color:black;" :min="minDate">
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Periksa*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="time" v-model="registrasi.jam_masuk" style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter DPJP*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.dokter_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="dpjpSelected"> 
                                        <option v-for="option in dokterLists" v-bind:value="option.dokter_id" v-bind:key="option.dokter_id">{{option.dokter_nama}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Cara Registrasi*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.cara_registrasi" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-for="option in caraRegistrasiRefs.json_data" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Asal Pasien*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.asal_pasien" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-for="option in asalPasienRefs.json_data" v-bind:value="option.value" v-bind:key="option.value">{{option.value}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Keterangan Asal Pasien</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="text" v-model="registrasi.ket_asal_pasien" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <search-select
                                    :config = configItemSelect
                                    :searchUrl = dokterUrl
                                    label = "Dokter Pengirim"
                                    placeholder = "dokter pengirim"
                                    captionField = "dokter_nama"
                                    valueField = "dokter_nama"
                                    :itemListData = dokterDesc
                                    :value = "registrasi.dokter_pengirim"
                                    v-on:item-select = "dokterPengirimSelected"
                                ></search-select>
                            </div>
                            
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Reff. transaksi</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="text" v-model="registrasi._id" style="color:black;" disabled>
                                </div>
                            </div>     
                        </div>
                    </div>

                    <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                        <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">PENJAMIN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                detail data penjamin dan penanggung.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                    
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penanggung*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.hub_penanggung" required class="uk-select uk-form-small">
                                        <option v-if="isRefExist" v-for="dt in hubKeluargaRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-width-3-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Penanggung*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" required v-model="registrasi.nama_penanggung" type="text" style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Penjamin*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.jns_penjamin" class="uk-select uk-form-small" required @change="ambilPenjaminList">
                                        <option v-if="isRefExist" v-for="dt in jenisPenjaminRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-width-1-2@m">
                                <search-list
                                    :config = configSelect
                                    :dataLists = asuransiLists.data
                                    label = "Penjamin / Asuransi*"
                                    placeholder = "pilih penjamin (asuransi)"
                                    captionField = "penjamin_nama"
                                    valueField = "penjamin_nama"
                                    :detailItems = penjaminDesc
                                    :value = "registrasi.penjamin_nama"
                                    v-on:item-select = "penjaminSelected"
                                ></search-list>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Kepesertaan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="registrasi.no_kepesertaan" type="text" style="color:black;" required>
                                </div>
                            </div>
                            
                            

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Plafon*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="registrasi.plafon" type="number" style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Layanan</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.kelas_harga" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-for="option in kelasHargaLists" v-bind:value="option.kelas_id">{{option.kelas_nama}}</option>
                                    </select>
                                </div>
                            </div>   

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Penjamin</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.kelas_penjamin" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-for="option in kelasHargaLists" v-bind:value="option.kelas_id">{{option.kelas_nama}}</option>
                                    </select>
                                </div>
                            </div>    
                            
                            <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;">
                                        <input class="uk-checkbox" type="checkbox" value="1" v-model="registrasi.is_bpjs" > Asuransi BPJS
                                        <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.2em; margin:0;">asuransi pasien adalah BPJS atau turunannya.</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                                            
                </div>
            </div>
        </form>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';
import ModalPickerRuang from '@/Pages/Pendaftaran/RawatInap/components/ModalPickerRuang';
import ListPasien from '@/Pages/Pendaftaran/RawatInap/components/ListPasien';

export default {
    name : 'form-registrasi1',
    components : { SearchSelect,SearchList,ModalPickerRuang, ListPasien },
    data() {
        return {
            isUpdate : false,
            isLoading : false,
            urlPicker : '',
            dokterUrl : '/doctors',

            pickerColumns : [ 
                { name : 'bed_id', title : 'Bed ID', colType : 'text', isCaption : true, },
                { name : 'unit_nama', title : 'Unit', colType : 'text', isCaption : false, },
                { name : 'ruang_nama', title : 'Ruang', colType : 'text', isCaption : false, },            
                { name : 'kelas_kamar', title : 'Kelas', colType : 'text', isCaption : false, },            
                { name : 'no_bed', title : 'No Bed', colType : 'text', isCaption : false, },
                { name : 'status', title : 'Status', colType : 'text', isCaption : false, },
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
            
            registrasi : {
                reg_id : null,
                client_id : null,
                jns_registrasi : 'RAWAT_INAP',
                cara_registrasi : null,
                asal_data_pasien : null,
                tgl_periksa : this.getTodayDate(),
                
                tgl_masuk : this.getTodayDate(),
                jam_masuk : null,
                
                dokter_unit_id : null,
                dokter_id : null,
                dokter_nama : null,
                
                dokter_pengirim_id: null,
                dokter_pengirim: null,
                
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                bed_id : null,
                no_bed : null,

                cara_registrasi : null,
                asal_pasien : null,
                ket_asal_pasien : null,
                pasien_id : null,
                is_pasien_baru : null,
                is_pasien_luar : false,
                
                jns_penjamin : null,
                penjamin_id : null,                
                penjamin_nama : null,
                is_bpjs : false,
                
                nama_penanggung : null,
                hub_penanggung : null,
                
                no_sep : null,
                status_reg : null,
                status_pulang : null,
                cara_pulang : null,
                tgl_pulang: null,
                is_aktif : true,
                client_id : null,
                reff_trx_id : null,
                is_konfirmasi : false,
                no_sep : null,

                pasien : {
                    reg_id : null,
                    trx_id : null,
                    is_pasien_luar : false,
                    pasien_id : null,
                    nik : null,
                    no_kk : null,
                    no_rm : null,
                    salut : null,
                    nama_pasien : null,
                    nama_lengkap : null,
                    tempat_lahir : null,
                    tgl_lahir : this.getTodayDate(),
                    usia : null,
                    jns_kelamin : null,
                    propinsi_id : null,
                    kota_id: null,
                    kecamatan_id : null,
                    kelurahan_id : null,
                    kodepos: null,
                    is_hamil : false,
                    is_pasien_baru : false,
                    is_aktif : false,
                    client_id : null,
                    alamat_tinggal : null,
                    alamat : null,
                },
                tindakan : [],
            },

            unitLists : [],
            dokterLists : [],
            roomLists : [],
            bedLists : [],
            kelasHargaLists : [],
            minDate : this.getTodayDate(),
        }
    },

    computed : {
        ...mapGetters(['errors']),
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
        ...mapActions('rawatInap',['createAdmisiInap','deleteAdmisiInap','updateAdmisiInap','dataAdmisiInap','confirmAdmisiInap']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('asuransi',['listAsuransi']),
        ...mapActions('refProduk',['listRefProduk']),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('kelas',['listKelas']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();     
            if(!this.isRefExist) { this.listReferensi({per_page:'ALL'}); }     

            this.listUnitPelayanan({'jenis_reg':this.registrasi.jns_registrasi, 'per_page':'ALL'}).then((response) => {
                if (response.success == true) { this.unitLists = response.data.data; }
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

        /** function called before modal closed */
        modalClosed(){
            this.clearRegistrasi();
            this.$emit('registrasiPoliClosed',true);
        },
        
        /**modal call from other component for new data entry */
        newData(){
            this.CLR_ERRORS();            
            this.isUpdate = false;
            this.clearRegistrasi();
            UIkit.switcher('#switcherFormAdmisi').show(0);
            if(this.$refs.listAsalPasienInap) {
                this.$refs.listAsalPasienInap.showMasterPasien();
            }
        },
        
        /**modal call from other component for update data entry */
        editData(refData) {
            this.CLR_ERRORS();
            this.clearRegistrasi();
            this.isUpdate = true;
            this.isLoading = true;
            if(refData) {
                this.dataAdmisiInap(refData.trx_id).then((response) => {
                    if (response.success == true) {
                        this.fillRegistrasi(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                    }
                    else { alert('data registrasi inap tidak ditemukan'); this.isLoading = false; }
                })
            }
        },


        refreshPasien(data){
            this.clearPasien();
            this.fillPasien(data);
            UIkit.switcher('#switcherFormAdmisi').show(1);
        },

        clearPasien(){
            this.registrasi.pasien.reg_id = null;
            this.registrasi.pasien.is_pasien_luar = false;
            this.registrasi.pasien.pasien_id = null;
            this.registrasi.pasien.nik = null;
            this.registrasi.pasien.no_kk = null;
            this.registrasi.pasien.no_rm = null;
            this.registrasi.pasien.salut = null;
            this.registrasi.pasien.nama_pasien = null;
            this.registrasi.pasien.nama_lengkap = null;
            this.registrasi.pasien.tempat_lahir = null;
            this.registrasi.pasien.tgl_lahir = this.getTodayDate();
            this.registrasi.pasien.usia = null;
            this.registrasi.pasien.jns_kelamin = null;
            this.registrasi.pasien.propinsi_id = null;
            this.registrasi.pasien.kota_id= null;
            this.registrasi.pasien.kecamatan_id = null;
            this.registrasi.pasien.kelurahan_id = null;
            this.registrasi.pasien.kodepos= null;
            this.registrasi.pasien.is_hamil = false;
            this.registrasi.pasien.is_pasien_baru = false;
            this.registrasi.pasien.is_aktif = false;
            this.registrasi.pasien.client_id = null;

            this.registrasi.pasien.alamat_tinggal = null;
            this.registrasi.pasien.alamat = null;            

            this.registrasi.penjamin_id = null;
            this.registrasi.penjamin_nama = null;
            this.registrasi.jns_penjamin = null;
            this.registrasi.is_bpjs = null;
            this.registrasi.pasien.alamat_tinggal = null;
            this.registrasi.pasien.alamat = null;
        },

        fillPasien(data) {
            this.registrasi.pasien.reg_id = null;
            //this.registrasi.pasien.is_pasien_luar = data.is_pasien_luar;
            this.registrasi.pasien.pasien_id = data.pasien_id;
            this.registrasi.pasien.nik = data.nik;
            this.registrasi.pasien.no_kk = data.no_kk;
            this.registrasi.pasien.no_rm = data.no_rm;
            this.registrasi.pasien.salut = data.salut;
            this.registrasi.pasien.nama_pasien = data.nama_pasien;
            this.registrasi.pasien.nama_lengkap = data.nama_lengkap;
            this.registrasi.pasien.tempat_lahir = data.tempat_lahir;
            this.registrasi.pasien.tgl_lahir = data.tgl_lahir;
            this.registrasi.pasien.usia = null;
            this.registrasi.pasien.jns_kelamin = data.jns_kelamin;
            this.registrasi.pasien.propinsi_id = data.alamat.propinsi_id;
            this.registrasi.pasien.kota_id= data.alamat.kota_id;
            this.registrasi.pasien.kecamatan_id = data.alamat.kecamatan_id;
            this.registrasi.pasien.kelurahan_id = data.alamat.kelurahan_id;
            this.registrasi.pasien.kodepos= data.kodepos;
            this.registrasi.pasien.is_hamil = data.is_hamil;
            this.registrasi.pasien.is_pasien_baru = data.is_pasien_baru;
            this.registrasi.pasien.is_aktif = data.is_aktif;
            this.registrasi.pasien.client_id = data.client_id;

            if(data.alamat){
                this.registrasi.pasien.alamat_tinggal = `${data.alamat.alamat_tinggal}, RT.${data.alamat.rt_tinggal}/RW.${data.alamat.rw_tinggal}, Kel.${data.alamat.kelurahan_tinggal}, Kec.${data.alamat.kecamatan_tinggal}, ${data.alamat.kota_tinggal} - ${data.alamat.propinsi_tinggal} ${data.alamat.kodepos_tinggal}`;
                this.registrasi.pasien.alamat = `${data.alamat.alamat}, RT.${data.alamat.rt}/RW.${data.alamat.rw}, Kel.${data.alamat.kelurahan}, Kec.${data.alamat.kecamatan}, ${data.alamat.kota} - ${data.alamat.propinsi} ${data.alamat.kodepos}`;
            } 

            if(!this.isUpdate) {
                this.registrasi.pasien_id = data.pasien_id;
                this.registrasi.jns_penjamin = data.jns_penjamin;
                this.registrasi.penjamin_id = data.penjamin_id;
                this.registrasi.penjamin_nama = data.penjamin_nama;
                this.registrasi.no_kepesertaan = data.no_kepesertaan;

                let dt = this.jenisPenjaminRefs.json_data.find(item => item.value === this.registrasi.jns_penjamin);
                if(dt) { this.registrasi.is_bpjs = dt.is_bpjs; }
                
                this.registrasi.is_pasien_luar = data.is_pasien_luar;
                this.registrasi.is_pasien_baru = data.is_pasien_baru;
                this.registrasi.tgl_masuk = this.getTodayDate();
            }
            
        },

        clearRegistrasi() {
            this.registrasi.reg_id = null;
            this.registrasi.trx_id = null;
            this.registrasi.client_id = null;
            this.registrasi.jns_registrasi = 'RAWAT_INAP';
            this.registrasi.cara_registrasi = null;
            this.registrasi.asal_data_pasien = null;
            this.registrasi.tgl_periksa = this.getTodayDate();
            this.registrasi.tgl_masuk = this.getTodayDate();
            this.registrasi.jam_masuk = null;
                
            this.registrasi.reff_trx_id = null;
            this.registrasi.plafon = 0;
            this.registrasi.kelas_harga_id = null;
            this.registrasi.kelas_harga = null;
            this.registrasi.kelas_penjamin = null;
                
            this.registrasi.dokter_unit_id = null;
            this.registrasi.dokter_id = null;
            this.registrasi.dokter_nama = null;
                
            this.registrasi.dokter_pengirim_id= null;
            this.registrasi.dokter_pengirim= null;
                
            this.registrasi.unit_id = null;
            this.registrasi.unit_nama = null;
            this.registrasi.ruang_id = null;
            this.registrasi.ruang_nama = null;
            this.registrasi.bed_id = null;
            this.registrasi.no_bed = null;

            this.registrasi.asal_pasien = null;
            this.registrasi.ket_asal_pasien = null;
            this.registrasi.pasien_id = null;
            this.registrasi.is_pasien_baru = null;
            this.registrasi.is_pasien_luar = false;
                
            this.registrasi.jns_penjamin = null;
            this.registrasi.penjamin_id = null;                
            this.registrasi.penjamin_nama = null;
            this.registrasi.is_bpjs = false;
            this.registrasi.no_sep = null;

            this.registrasi.nama_penanggung = null;
            this.registrasi.hub_penanggung = null;
                
            this.registrasi.status_reg = null;
            this.registrasi.status_pulang = null;
            this.registrasi.cara_pulang = null;
            this.registrasi.tgl_pulang= null;
            this.registrasi.is_aktif = true;
            this.registrasi.client_id = null;
            this.registrasi.is_konfirmasi = false;
            this.minDate = this.getTodayDate();            
            this.clearPasien();

        },

        fillRegistrasi(data){
            let unit = this.unitLists.find( item => item.unit_id == data.unit_id );
            if(unit) { this.dokterLists = unit.dokter; }
            this.registrasi.reg_id = data.reg_id;
            this.registrasi.trx_id = data.trx_id;
            
            this.registrasi.client_id = data.client_id;
            this.registrasi.jns_registrasi = 'RAWAT_INAP';
            this.registrasi.cara_registrasi = data.cara_registrasi;
            this.registrasi.asal_data_pasien = data.asal_data_pasien;
            this.registrasi.tgl_periksa = data.tgl_transaksi;
                
            this.registrasi.tgl_masuk = data.tgl_masuk;
            this.registrasi.jam_masuk = data.jam_masuk;
                
            this.registrasi.dokter_unit_id = data.dokter_unit_id;
            this.registrasi.dokter_id = data.dokter_id;
            this.registrasi.dokter_nama = data.dokter_nama;
                
            this.registrasi.dokter_pengirim_id = data.dokter_pengirim_id;
            this.registrasi.dokter_pengirim = data.dokter_pengirim;
                
            this.registrasi.unit_id = data.unit_id;
            this.registrasi.unit_nama = data.unit_nama;
            this.registrasi.ruang_id = data.ruang_id;
            this.registrasi.ruang_nama = data.ruang_nama;
            this.registrasi.bed_id = data.bed_id;
            this.registrasi.no_bed = data.no_bed;
            this.registrasi.kelas_harga = data.kelas_harga_id;
            this.registrasi.kelas_penjamin = data.kelas_penjamin;

            this.registrasi.asal_pasien = data.asal_pasien;
            this.registrasi.ket_asal_pasien = data.ket_asal_pasien;
            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.is_pasien_baru = data.is_pasien_baru;
            this.registrasi.is_pasien_luar = data.is_pasien_luar;
                
            this.registrasi.jns_penjamin = data.jns_penjamin;
            this.registrasi.penjamin_id = data.penjamin_id;                
            this.registrasi.penjamin_nama = data.penjamin_nama;
            this.registrasi.no_kepesertaan = data.no_kepesertaan;
            this.registrasi.is_bpjs = data.is_bpjs;
            
            this.registrasi.plafon = data.plafon;
                
            this.registrasi.nama_penanggung = data.nama_penanggung;
            this.registrasi.hub_penanggung = data.hub_penanggung;
            this.registrasi.no_sep = data.no_sep;
                
            this.registrasi.status_reg = data.status_reg;
            this.registrasi.status_pulang = data.status_pulang;
            this.registrasi.cara_pulang = data.cara_pulang;
            this.registrasi.tgl_pulang= data.tgl_pulang;
            this.registrasi.is_aktif = data.is_aktif;
            this.registrasi.client_id = data.client_id;
            this.registrasi.is_konfirmasi = data.is_konfirmasi;

            this.minDate = this.registrasi.tgl_masuk;
            this.fillPasien(data.pasien);
            this.isUpdate = true;
        },

        ambilPenjaminList(){
            this.registrasi.penjamin_id = null;
            this.registrasi.penjamin_nama = null;
            let dt = this.jenisPenjaminRefs.json_data.find(item => item.value === this.registrasi.jns_penjamin);
            if(dt) { this.registrasi.is_bpjs = dt.is_bpjs; }            
            this.listAsuransi({per_page:'ALL',jns_penjamin:this.registrasi.jns_penjamin, is_aktif:true });
        },

        penjaminSelected(data){
            if(data) {
                this.registrasi.penjamin_id = data.penjamin_id;
                this.registrasi.penjamin_nama = data.penjamin_nama;
                this.registrasi.is_bpjs = data.is_bpjs;
            }
        },

        ruangSelected() {
            let ruangSelected = this.roomLists.find(item => item.ruang_id === this.registrasi.ruang_id);
            if(ruangSelected) {
                this.registrasi.ruang_nama = ruangSelected.ruang_nama;
                this.bedLists = ruangSelected.beds;
            }
        },

        dpjpSelected() {
            let data = this.dokterLists.find( item => item.dokter_id == this.registrasi.dokter_id );
            if(data) {
                this.registrasi.dokter_id = data.dokter_id;
                this.registrasi.dokter_nama = data.dokter_nama;
                this.registrasi.dokter_unit_id = data.dokter_unit_id;
            }
        },

        dokterPengirimSelected(data){
            this.registrasi.dokter_pengirim_id = null;
            this.registrasi.dokter_pengirim = null;

            if(data) {
                this.registrasi.dokter_pengirim_id = data.dokter_id;
                this.registrasi.dokter_pengirim = data.dokter_nama;
            }
        },

        unitSelected() {
            let data = this.unitLists.find( item => item.unit_id == this.registrasi.unit_id );
            if(data) {
                this.registrasi.unit_id = data.unit_id;
                this.registrasi.unit_nama = data.unit_nama;
                this.dokterLists = data.dokter;
                this.registrasi.ruang_id = null;
                this.registrasi.ruang_nama = null;
                this.registrasi.bed_id = null;
                this.registrasi.no_bed = null;
            }
        },

        searchRuangInap(data){
            if(this.registrasi.unit_id == null || this.registrasi.unit_id == '' ) {
                alert('Pilih unit pelayanan terlebih dahulu.');
            }
            else {
                this.urlPicker = `/beds?unit_id=${this.registrasi.unit_id}`;
                this.$refs.roomModalPicker.showModalPicker(this.urlPicker);
            }
        },

        roomSelected(data){
            console.log(data);
            if(data) { 
                this.registrasi.no_bed = data.no_bed;
                this.registrasi.bed_id = data.bed_id;
                this.registrasi.ruang_id = data.ruang_id;
                this.registrasi.ruang_nama = data.ruang_nama;                
                this.$refs.roomModalPicker.closeModalPicker(); 
            }
        },

        submitAdmisiInap(){
            this.CLR_ERRORS();
            this.isLoading = true;      
            if(this.isUpdate) {
                //update data
                this.updateAdmisiInap(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        this.isUpdate = true;
                        this.fillRegistrasi(response.data);
                        alert('Data berhasil disimpan.');
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
            else {
                //data baru
                this.createAdmisiInap(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        this.isUpdate = true;
                        this.fillRegistrasi(response.data);
                        alert('Data berhasil disimpan.');
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
        },

        hapusRegistrasi(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus data registrasi ${this.registrasi.trx_id} - ${this.registrasi.pasien.nama_pasien}. Lanjutkan ?`)){
                this.isLoading = true;
                this.deleteAdmisiInap(this.registrasi.trx_id).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; this.isUpdate = false;
                        alert('data registrasi inap BERHASIL dihapus.');
                        this.modalClosed();
                    }
                    else { 
                        alert('data registrasi inap tidak berhasil dihapus.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        sepInap(){

        },

        lockData(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan MENGUNCI data registrasi ${this.registrasi.trx_id} - ${this.registrasi.pasien.nama_pasien} dan membuat data transaksi dari pemakaian bed. Lanjutkan ?`)){
                this.isLoading = true;
                this.confirmAdmisiInap(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('data registrasi inap BERHASIL dikonfirmasi.');
                        this.modalClosed();
                    }
                    else { 
                        alert('data registrasi inap tidak berhasil dikonfirmasi.'); 
                        this.isLoading = false;
                    }
                })
            }

        }
    }
}
</script>
<style>
</style>