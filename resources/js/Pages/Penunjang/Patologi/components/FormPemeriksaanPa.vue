<template>
    <div class="" style="margin:0;padding:0;">        
        <div style="margin:0;padding:0;">
            <form action="" @submit.prevent="submisiRegistrasiPa" style="margin:0;padding:0;">
                <div style="padding:0.5em 0.5em 0.5em 1em;">
                    <div class="uk-grid-small uk-card uk-card-default1 hims-form-header" uk-grid>                        
                        <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                            <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                                <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="modalClosed" style="padding:0.5em;"><span uk-icon="arrow-left"></span> Kembali</button>
                                <button type="button" class="hims-table-btn uk-width-auto" @click="hapusRegistrasi" :disabled="!isUpdate" style="padding:0.5em;"><span uk-icon="trash"></span> Hapus</button>
                                <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                                <button :disabled="isLoading" type="submit" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-text"></span> Simpan</button>
                                <button v-if="isUpdate" :disabled="isLoading" type="button" @click.prevent="konfirmasiReg" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-edit"></span> Proses Hasil</button>
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
                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                                <div class="uk-width-1-1" style="margin:0;padding:0;"></div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                                    <div class="uk-inline uk-width-1-1">
                                        <a v-if="!isUpdate" class="uk-form-icon uk-form-icon-flip" href="#" uk-icon="icon: search" @click.prevent="searchPasien" style="color:black; background-color:white;border:1px solid #aaa;"></a>
                                        <input class="uk-input uk-form-small" v-model="registrasi.nama_pasien" 
                                            type="text" aria-label="pilih pasien" 
                                            style="color:black;" 
                                            disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Pasien*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="registrasi.pasien_id" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-1">
                                    <p style="color:black; font-size: 10px; font-style: italic;padding:0;">
                                        {{registrasi.buku_harga_id}} {{registrasi.buku_harga}}
                                        <input v-model="registrasi.buku_harga_id" type="hidden" />
                                        <input v-model="registrasi.buku_harga" type="hidden" />
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                            <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DIAGNOSA</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;"> detail data diagnosa.</p>
                            </div>
                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diagnosa*</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="registrasi.diag_klinis" type="text" style="color:black;" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>          
                                
                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                            <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA PEMERIKSAAN</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    detail data pemeriksaan patologi.
                                </p>
                            </div>
                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Permintaan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_permintaan" style="color:black;"  required>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Dibutuhkan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_dibutuhkan" style="color:black;"  required>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Periksa*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_diperiksa" style="color:black;"  required>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Periksa*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="time" v-model="registrasi.jam_periksa" style="color:black;" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lokasi Jaringan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="registrasi.lokasi_jaringan">
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Cito*</label>
                                    <div class="uk-form-controls">
                                        <search-list ref="jenisCito"
                                            :config = configSelect
                                            :dataLists = jenisCitoDataLists
                                            placeholder = "jenis cito"
                                            captionField = "caption"
                                            valueField = "value"
                                            :detailItems = jenisCitoDesc
                                            :value = "registrasi.jenis_cito"
                                            v-on:item-select = "citoSelected"
                                        ></search-list>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Cara Pengambilan*</label>
                                    <div class="uk-form-controls">
                                        <search-list ref="caraPengambilan"
                                            :config = configSelect
                                            :dataLists = caraPengambilanDataLists
                                            placeholder = "cara pengambilan"
                                            captionField = "caption"
                                            valueField = "value"
                                            :detailItems = caraPengambilanDesc
                                            :value = "registrasi.cara_pengambilan"
                                            v-on:item-select = "caraPengambilanSelected"
                                        ></search-list>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="registrasi.unit_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="unitSelected"> 
                                            <option v-for="option in unitLists" v-bind:value="option.unit_id" v-bind:key="option.unit_id">{{option.unit_nama}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Haid Terakhir</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_haid_terakhir" style="color:black;" >
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Spesimen*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="registrasi.no_spesimen" type="text" style="color:black;" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Patologi Anatomi*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="registrasi.dokter_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="dokterSelected"> 
                                            <option v-for="option in dokterLists" v-bind:value="option.dokter_id" v-bind:key="option.dokter_id">{{option.dokter_nama}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Layanan*</label>
                                    <div class="uk-form-controls">
                                        <select v-model = "registrasi.kelas_harga_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;" required :disabled="isLocked" @change.prevent="updateKelasHarga">
                                            <option v-for="option in kelasHargaLists" v-bind:value="option.kelas_id">{{option.kelas_nama}}</option>
                                        </select>
                                    </div>
                                </div>    
                            </div>
                        </div>

                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0 0.5em 0 0.5em;">
                                <div class="uk-width-expand@m">
                                    <h5 style="color:indigo;padding:0;margin:0;">PEMERIKSAAN PATOLOGI
                                        <span style="font-size:12px;font-style:italic;padding:0;margin:0 0 0 0.2em;">detail pemeriksaan patologi.</span>
                                    </h5>
                                </div>
                                <div class="uk-width-auto@m">
                                    <button @click.prevent="searchTindakan">Pilih Pemeriksaan</button>
                                </div>                                        
                            </div>
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                    <thead>
                                        <th class="tb-header-reg" style="width:20px;text-align:center;color:white;"></th>
                                        <th class="tb-header-reg" style="width:100px;text-align:left;color:white;">ID</th>
                                        <th class="tb-header-reg" style="text-align:left;color:white;">Tindakan</th>
                                        <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Jumlah</th>
                                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">Satuan</th>
                                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Harga</th>
                                        <th class="tb-header-reg" style="width:60px;text-align:right;color:white;">Diskon(Rp)</th>
                                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>                                                
                                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                                    </thead>
                                    <tbody>                                               
                                        <row-form-registrasi-pa 
                                            v-if="registrasi.detail" 
                                            v-for="dt in registrasi.detail"
                                            :rowData = "dt"
                                            :dataChange = "calculateTotalPa"
                                            :activationChange = "changeActivationActItem">
                                        </row-form-registrasi-pa>
                                        <tr>
                                            <td colspan="7" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL</td>
                                            <td style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(registrasi.total)}}</td>
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
        <modal-picker ref="actModalPicker" 
            title="Pilih Tindakan dan Pemeriksaan" 
            :fieldDatas = pickerColumns
            :urlSearch = "urlPicker" 
            viewType="table"
            :proceedFunction="addPemeriksaan"
        ></modal-picker>
    </div>
    
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';
import ModalPicker from '@/Components/ModalPicker';

import RowFormRegistrasiPa from '@/Pages/Penunjang/Patologi/Pendaftaran/components/RowFormRegistrasiPa.vue';

export default {
    name : 'form-pemeriksaan-pa',
    components : { 
        SearchSelect,
        SearchList,
        //ModalPickerRuang, 
        //ListPasien,
        ModalPicker,
        RowFormRegistrasiPa,
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

            jenisCitoDesc : [
                { colName : 'caption', labelData : '', isTitle : true },
                { colName : 'desc', labelData : '', isTitle : false },
            ], 

            caraPengambilanDesc : [
                { colName : 'caption', labelData : '', isTitle : true },
                { colName : 'desc', labelData : '', isTitle : false },
            ], 

            asalPasienList : [
                { value : 'DATANG SENDIRI', title : 'DATANG SENDIRI', isInternal : false, isPasienLuar:false, },
                { value : 'RAWAT JALAN', title : 'RAWAT JALAN', isInternal : true, isPasienLuar:false, },
                { value : 'RAWAT INAP', title : 'RAWAT INAP', isInternal : true, isPasienLuar:false, },
                { value : 'PASIEN LUAR', title : 'PASIEN LUAR', isInternal : false, isPasienLuar: true, },
            ],

            jenisCitoLists: [
                { value : 'SEGERA', caption:'SEGERA', desc : 'CITO'},
                { value : 'PENTING', caption:'PENTING', desc : 'STATIM'},
                { value : 'SANGAT_PENTING', caption:'SANGAT PENTING', desc : 'URGENT'},
                { value : 'BERBAHAYA', caption:'P.I.M', desc : 'PERICULUM IN MORA'},
            ],

            caraPengambilanLists:[
                { value : 'OPERASI', caption:'OPERASI'},
                { value : 'URINE', caption:'URINE'},
                { value : 'PLEURA', caption:'PLEURA'},
                { value : 'BIOPSI', caption:'BIOPSI'},
            ],
            
            registrasi : {
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                client_id : null,
                jns_registrasi : 'PATOLOGI ANATOMI',
                tgl_permintaan : this.getTodayDate(),
                tgl_dibutuhkan : this.getTodayDate(),
                tgl_diperiksa : this.getTodayDate(),
                tgl_haid_terakhir : this.getTodayDate(),
                jam_periksa : null,
                
                // Data Pasien
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                tempat_lahir : null,
                tgl_lahir : null,
                nik : null,
                jns_kelamin : null,
                jns_penjamin : null,
                penjamin_id : null,                
                penjamin_nama : null,

                dokter_unit_id : null,
                dokter_id : null,
                dokter_nama : null,

                status : null,
                is_aktif : true,
                client_id : null,
                reff_trx_id : null,

                diag_klinis : null,
                lokasi_jaringan : null,
                cara_pengambilan : null,
                jenis_cito : null, 
                is_cito : null,
                no_spesimen : null,

                buku_harga_id : null,
                buku_harga : null,
                unit_id : null,
                unit_nama : null,

                kelas_harga_id : null,
                kelas_harga : null,

                detail : [],
            },

            unitLists : [],
            dokterLists : [],
            roomLists : [],
            kelasHargaLists : [],
            jenisCitoDataLists : [],
            caraPengambilanDataLists : [],
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
        ...mapActions('rawatInap',['dataAdmisiInap']),
        ...mapActions('pasien',['dataPasien']),
        
        ...mapActions('patologi',['createPatologi','deletePatologi','updatePatologi','updateHasilPatologi','dataPatologi','confirmPatologi']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('asuransi',['listAsuransi','dataAsuransi']),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('kelas',['listKelas']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();     
            if(!this.isRefExist) { this.listReferensi({per_page:'ALL'}); }     
            this.jenisCitoDataLists = JSON.parse(JSON.stringify(this.jenisCitoLists));
            this.caraPengambilanDataLists = JSON.parse(JSON.stringify(this.caraPengambilanLists));
            this.listUnitPelayanan({'jenis_reg':this.registrasi.jns_registrasi, 'per_page':'ALL'}).then((response) => {
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

        citoSelected(data) {
            this.registrasi.is_cito = null;
            this.registrasi.jenis_cito = null;
            if(data) {
                this.registrasi.is_cito = '1';
                this.registrasi.jenis_cito = data.value;
            }
        },

        kelasSelected() {
            let data = this.kelasHargaLists.find( item => item.kelas_id == this.registrasi.kelas_harga_id );
            if(data) {
                this.registrasi.kelas_harga_id = data.kelas_id;
                this.registrasi.kelas_harga = data.kelas_nama;
            }
        },

        caraPengambilanSelected(data) {
            this.registrasi.cara_pengambilan = null;
            if(data) {
                this.registrasi.cara_pengambilan = data.value;
            }
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
        modalClosed(){
            this.clearRegistrasi();
            this.isUpdate = false;
            this.$emit('registrasiLabClosed',true);
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
        editData(refData) {
            this.CLR_ERRORS();
            this.clearRegistrasi();
            this.isUpdate = true;
            this.isLoading = true;
            if(refData) {
                this.dataPatologi(refData.trx_id).then((response) => {
                    if (response.success == true) {
                        this.fillRegistrasi(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                    }
                    else { 
                        alert('data registrasi radiologi tidak ditemukan'); 
                        this.isLoading = false; 
                    }
                })
            }
        },

        clearRegistrasi() {
            this.registrasi.reg_id = null;
            this.registrasi.trx_id = null;
            this.registrasi.sub_trx_id = null;
            this.registrasi.client_id = null;
            // this.registrasi.is_rujukan_int = false;
            this.registrasi.jns_registrasi = 'PATOLOGI';
            this.registrasi.tgl_permintaan = this.getTodayDate();
            this.registrasi.tgl_diperiksa = this.getTodayDate();
            this.registrasi.jam_periksa = this.getTimeNow();
            this.registrasi.tgl_dibutuhkan = this.getTodayDate(),
            this.registrasi.tgl_haid_terakhir = this.getTodayDate(),
                
            this.registrasi.dokter_unit_id = null;
            this.registrasi.dokter_id = null;
            this.registrasi.dokter_nama = null;
                
            // this.registrasi.dokter_pengirim_id= null;
            // this.registrasi.dokter_pengirim= null;
                
            this.registrasi.unit_id = null;
            this.registrasi.unit_nama = null;
            // this.registrasi.ruang_id = null;
            // this.registrasi.ruang_nama = null;
                
            // this.registrasi.unit_asal_id = null;
            // this.registrasi.unit_asal = null;
            // this.registrasi.ruang_asal_id = null;
            // this.registrasi.ruang_asal = null;
            // this.registrasi.bed_asal_id = null;
            // this.registrasi.no_asal_bed = null;

            // this.registrasi.cara_registrasi = null;
            // this.registrasi.asal_pasien = null;
            // this.registrasi.ket_asal_pasien = null;
                
            this.registrasi.pasien_id = null;
            this.registrasi.no_rm = null;
            this.registrasi.nama_pasien = null;
            this.registrasi.tempat_lahir = null;
            this.registrasi.tgl_lahir = null;
            this.registrasi.nik = null;
            this.registrasi.jns_kelamin = null;

            // this.registrasi.is_pasien_baru = null;
            // this.registrasi.is_pasien_luar = false;
                
            this.registrasi.jns_penjamin = null;
            this.registrasi.penjamin_id = null;      
            this.registrasi.buku_harga_id = null;    
            this.registrasi.buku_harga = null;    
            this.registrasi.total = 0;      
                      
            this.registrasi.penjamin_nama = null;
            // this.registrasi.is_bpjs = false;
            this.registrasi.status = null;
            this.registrasi.is_aktif = true;
            this.registrasi.client_id = null;
            this.registrasi.reff_trx_id = null;
            // this.registrasi.is_realisasi = false;

            this.registrasi.diag_klinis = null;
            this.registrasi.lokasi_jaringan = null;
            this.registrasi.cara_pengambilan = null;
            this.registrasi.jenis_cito = null; 
            this.registrasi.is_cito = null;
            this.registrasi.no_spesimen  = null;
            this.registrasi.kelas_harga_id = null;
            this.registrasi.kelas_harga = null;
            // this.registrasi.media_hasil = null;
            // this.registrasi.expertise_id = null;
            // this.registrasi.expertise_nama = null;
                
            this.registrasi.detail = [];
            this.minDate = this.getTodayDate();   

        },

        fillRegistrasi(data){
            let unit = this.unitLists.find( item => item.unit_id == data.unit_id );
            if(unit) { 
                this.dokterLists = unit.dokter; 
            }

            var tgl_periksa_split = data.tgl_diperiksa.split(' ');

            this.registrasi.reg_id = data.reg_id;
            this.registrasi.trx_id = data.trx_id;
            this.registrasi.sub_trx_id = data.sub_trx_id;
            this.registrasi.reff_trx_id = data.reff_trx_id;
            // this.registrasi.is_rujukan_int = data.is_rujukan_int;
            
            this.registrasi.client_id = data.client_id;
            this.registrasi.jns_registrasi = data.jns_registrasi;
            // this.registrasi.cara_registrasi = data.cara_registrasi;
            this.registrasi.tgl_permintaan = data.tgl_permintaan.split(' ')[0];
            this.registrasi.tgl_dibutuhkan = data.tgl_dibutuhkan.split(' ')[0];
            this.registrasi.tgl_haid_terakhir = data.tgl_haid_terakhir.split(' ')[0];
                
            this.registrasi.tgl_diperiksa = tgl_periksa_split[0];
            this.registrasi.jam_periksa = tgl_periksa_split[1];
                
            // this.registrasi.dokter_unit_id = data.dokter_unit_id;
            this.registrasi.dokter_id = data.dokter_id;
            this.registrasi.dokter_nama = data.dokter_nama;
            // this.registrasi.dokter_pengirim_id = data.dokter_pengirim_id;
            // this.registrasi.dokter_pengirim = data.dokter_pengirim;
                
            this.registrasi.unit_id = data.unit_id;
            this.registrasi.unit_nama = data.unit_nama;
            // this.registrasi.ruang_id = data.ruang_id;
            // this.registrasi.ruang_nama = data.ruang_nama;
            
            // this.registrasi.unit_asal_id = data.unit_asal_id;
            // this.registrasi.unit_asal = data.unit_asal;
            // this.registrasi.ruang_asal_id = data.ruang_asal_id;
            // this.registrasi.ruang_asal = data.ruang_asal;
            // this.registrasi.bed_asal_id = data.bed_asal_id;
            // this.registrasi.no_asal_bed = data.no_asal_bed;
            
            this.registrasi.kelas_harga_id = data.kelas_harga_id;
            this.registrasi.kelas_harga = data.kelas_harga;
            
            this.registrasi.total = data.transaksi.total;
            
            // this.registrasi.asal_pasien = data.asal_pasien;
            // this.registrasi.ket_asal_pasien = data.ket_asal_pasien;

            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.nama_pasien = data.nama_pasien;
            this.registrasi.no_rm = data.no_rm;
            this.registrasi.jns_kelamin = data.jns_kelamin;
            this.registrasi.nik = data.nik;
            
            this.registrasi.tempat_lahir = data.tempat_lahir;
            this.registrasi.tgl_lahir = data.tgl_lahir;
            
            // this.registrasi.is_pasien_baru = data.is_pasien_baru;
            // this.registrasi.is_pasien_luar = data.is_pasien_luar;
                
            this.registrasi.jns_penjamin = data.jns_penjamin;
            this.registrasi.penjamin_id = data.penjamin_id;                
            this.registrasi.penjamin_nama = data.penjamin_nama;
            // this.registrasi.no_kepesertaan = data.no_kepesertaan;
            // this.registrasi.is_bpjs = data.is_bpjs;

            this.registrasi.buku_harga = data.buku_harga;
            this.registrasi.buku_harga_id = data.buku_harga_id;
                
            this.registrasi.status = data.status;
            this.registrasi.is_aktif = data.is_aktif;
            this.registrasi.client_id = data.client_id;
            // this.registrasi.is_realisasi = data.is_realisasi;
            this.registrasi.detail = data.detail;
            this.minDate = this.registrasi.tgl_diperiksa;

            this.registrasi.diag_klinis = data.diag_klinis;
            this.registrasi.lokasi_jaringan = data.lokasi_jaringan;
            this.registrasi.cara_pengambilan = data.cara_pengambilan;
            this.registrasi.jenis_cito = data.jenis_cito; 
            this.registrasi.is_cito = data.is_cito;
            this.registrasi.no_spesimen  = data.no_spesimen;
            // this.registrasi.media_hasil = data.media_hasil;
            // this.registrasi.expertise_id = data.expertise_id;
            // this.registrasi.expertise_nama = data.expertise_nama;
            
            this.isUpdate = true;
        },

        ruangSelected() {
            let ruangSelected = this.roomLists.find(item => item.ruang_id === this.registrasi.ruang_id);
            if(ruangSelected) {
                this.registrasi.ruang_nama = ruangSelected.ruang_nama;
            }
        },

        dokterSelected() {
            let data = this.dokterLists.find( item => item.dokter_id == this.registrasi.dokter_id );
            if(data) {
                this.registrasi.dokter_id = data.dokter_id;
                this.registrasi.dokter_nama = data.dokter_nama;
                this.registrasi.dokter_unit_id = data.dokter_unit_id;
            }
        },

        expertiseSelected(data){
            if(data) {
                this.registrasi.expertise_nama = data.dokter_nama;
                this.registrasi.expertise_id = data.dokter_id;
            }
        },

        unitSelected() {
            let data = this.unitLists.find( item => item.unit_id == this.registrasi.unit_id );
            if(data) {
                this.registrasi.unit_id = data.unit_id;
                this.registrasi.unit_nama = data.unit_nama;
                this.roomLists = data.ruang;
                this.dokterLists = data.dokter;
                this.registrasi.ruang_id = null;
                this.registrasi.ruang_nama = null;
                this.registrasi.bed_id = null;
                this.registrasi.no_bed = null;
            }
        },

        searchTindakan(data){
            if(this.registrasi.unit_id == null || 
                this.registrasi.unit_id == '' || 
                this.registrasi.kelas_harga_id == null ||
                this.registrasi.kelas_harga_id == '') 
            {
                alert('Pilih unit pelayanan dan kelas terlebih dahulu.');
            }

            else {
                this.urlPicker = `units/${this.registrasi.unit_id}/acts`;
                this.$refs.actModalPicker.showModalPicker(this.urlPicker);
            }
        },

        roomSelected(data){
            console.log(data);
            if(data) { 
                this.registrasi.no_bed = data.no_bed;
                this.registrasi.bed_id = data.bed_id;
                this.registrasi.ruang_id = data.ruang_id;
                this.registrasi.ruang_nama = data.ruang_nama;  
                this.registrasi.kelas_harga_id = data.kelas_harga_id; 
                this.$refs.roomModalPicker.closeModalPicker(); 
            }
        },

        submisiRegistrasiPa(){
            this.CLR_ERRORS();
            if(this.isUpdate) {
                this.isLoading = true;
                this.updateHasilPatologi(this.registrasi).then((response) => {
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
                this.isLoading = true;
                this.createPatologi(this.registrasi).then((response) => {
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
            if(confirm(`Proses ini akan menghapus data registrasi ${this.registrasi.trx_id} - ${this.registrasi.nama_pasien}. Lanjutkan ?`)){
                this.isLoading = true;
                this.deletePatologi(this.registrasi.trx_id).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; this.isUpdate = false;
                        alert('data registrasi lab BERHASIL dihapus.');
                        this.modalClosed();
                    }
                    else { 
                        alert('data registrasi lab tidak berhasil dihapus.'); 
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
                    let exist = this.registrasi.detail.find(item => item.item_id == data.tindakan_id);
                    let hargaRes = data.harga.find(item => item.buku_harga_id == this.registrasi.buku_harga_id && item.kelas_id == this.registrasi.kelas_harga_id);

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

                        this.registrasi.detail.push({
                            detail_id : null,
                            reg_id : null,
                            trx_id : null,
                            sub_trx_id : null,
                            item_id : data.tindakan_id,
                            item_nama : data.tindakan_nama,
                            jumlah : 1,
                            satuan : 'X',
                            kelas_harga_id : this.registrasi.kelas_harga_id,
                            buku_harga_id : this.registrasi.buku_harga_id,
                            nilai : hargaRes.nilai,
                            diskon : 0,
                            diskon_persen : 0,
                            harga_diskon : 0,
                            subtotal : hargaRes.nilai,
                            dokter_id : this.registrasi.dokter_id,
                            dokter_pengirim_id : this.registrasi.dokter_pengirim_id,
                            is_aktif : true,
                            komponen : komp,
                        });
                    }
                    else { exist.is_aktif = true; }
                    this.calculateTotalPa();
                });

            }
        },

        konfirmasiReg(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan Mengubah status data registrasi ${this.registrasi.trx_id} - ${this.registrasi.nama_pasien}. Lanjutkan ?`)){
                this.isLoading = true;
                this.confirmPatologi(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('data registrasi patologi anatomi BERHASIL dikonfirmasi.');
                        this.modalClosed();
                    }
                    else { 
                        alert('data registrasi patologi anatomi tidak berhasil dikonfirmasi.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        changeActivationActItem(){
            this.registrasi.detail = this.registrasi.detail.filter(
                item => { return item.detail_id !== null || item.is_aktif == true }
            );
        },

        calculateTotalPa(){  
            let total = 0;          
            this.registrasi.detail.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.registrasi.total = total; 
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