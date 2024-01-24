<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formPendaftaran" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">

                <ul id="switcherPendaftaran" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
                    <li><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">FORM REGISTRASI</h5></a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">LIST PASIEN</h5></a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">FORM PASIEN</h5></a></li>
                </ul>
                <ul class="uk-switcher" style="padding:0;margin:0;">
                    <!-- FORM PENDAFTARAN -->
                    <li>
                        <form action="" @submit.prevent="submitPendaftaran" style="padding:0;" >

                            <!-- FORM HEADER -->
                            <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                                <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                                    <h5 class="uk-text-uppercase">FORM REGISTRASI</h5>
                                </div>
                                <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                                    <button type="button" @click="clearPendaftaran" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Refresh</button>
                                </div>
                                <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                                    <button type="button" @click="switchListPasien" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">List Pasien</button>
                                </div>
                                <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                                    <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>
                                </div>
                                <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                                    <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>
                                </div>
                            </div>

                            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                                <p>{{ errors.invalid }}</p>
                            </div>

                            <!-- DATA PENDAFTARAN -->
                            <div>
                                <div class="uk-grid-small" uk-grid style="padding:1em;margin:0.5em;border-top:1px solid #cc0202;">
                                    <div class="uk-width-1-4@s">
                                        <h5 style="color:#cc0202;margin:0;padding:0;">DATA REGISTRASI</h5>
                                        <p style="font-size:12px;font-style:italic;margin:0;padding:0;">informasi singkat registrasi</p>
                                    </div>
                                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Registrasi*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="daftar.jns_registrasi" class="uk-select uk-form-small" required>
                                                    <option v-if="isRefExist" v-for="dt in jenisRegistrasiRefs.json_data" :value="dt.text">{{dt.text}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Cara Registrasi*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="daftar.cara_registrasi" class="uk-select uk-form-small" required>
                                                    <option v-if="isRefExist" v-for="dt in caraRegistrasiRefs.json_data" :value="dt.text">{{dt.text}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Asal Pasien*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="daftar.asal_pasien" class="uk-select uk-form-small" required>
                                                    <option v-if="isRefExist" v-for="dt in asalPasienRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <search-list
                                            :config = configSelect
                                            :dataLists = asuransiLists.data
                                            label = "Nama Penjamin*"
                                            placeholder = "Pilih nama penjamin"
                                            captionField = "penjamin_nama"
                                            valueField = "penjamin_id"
                                            :detailItems = listPenjaminDesc
                                            :value = "daftar.penjamin_id"
                                            v-on:item-select = "selectedPenjamin"
                                            ></search-list>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Penanggung</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" name="nama_penanggung" v-model="daftar.nama_penanggung">
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Hub. Penanggung</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" name="hub_penanggung" v-model="daftar.hub_penanggung">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DATA PASIEN -->
                            <div>
                                <div class="uk-grid-small" uk-grid style="padding:1em;margin:0.5em;border-top:1px solid #cc0202;">
                                    <div class="uk-width-1-4@s">
                                        <h5 style="color:#cc0202;margin:0;padding:0;">DATA PASIEN</h5>
                                        <p style="font-size:12px;font-style:italic;margin:0;padding:0;">berisi informasi singkat pasien</p>
                                    </div>
                                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" name="nama_pasien" v-model="daftar.nama_pasien" :readonly="disabledInput == 1">
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat & Tgl. lahir</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" name="tempat_lahir" v-model="daftar.tempat_lahir" :readonly="disabledInput == 1">
                                            </div>
                                        </div>

                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" name="jk" v-model="daftar.jk" :readonly="disabledInput == 1">
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No.KTP</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" name="no_ktp" v-model="daftar.no_ktp" :readonly="disabledInput == 1">
                                            </div>
                                        </div>

                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No.HP</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" name="no_hp" v-model="daftar.no_hp" :readonly="disabledInput == 1">
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alamat tinggal</label>
                                            <div class="uk-form-controls">
                                                <textarea class="uk-textarea uk-form-small" rows="2" type="text" name="alamat_tinggal" v-model="daftar.alamat_tinggal" :readonly="disabledInput == 1"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DATA UNIT & DOKTER -->
                            <div>
                                <div class="uk-grid-small" uk-grid style="padding:1em;margin:0.5em;border-top:1px solid #cc0202;">
                                    <div class="uk-width-1-4@s">
                                        <h5 style="color:#cc0202;margin:0;padding:0;">DATA UNIT & DOKTER</h5>
                                        <p style="font-size:12px;font-style:italic;margin:0;padding:0;">berikan informasi unit dan dokter</p>
                                    </div>
                                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                                        <!-- <div class="uk-width-1-2@m">
                                            <Datepicker v-model="date" />
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Periksa*</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="daftar.tgl_periksa"
                                                    type="date" placeholder="tanggal periksa"
                                                    required readonly :disabled="isUpdate">
                                            </div>
                                        </div> -->
                                        <div class="uk-width-1-2@m">
                                            <search-list
                                            :config = configSelect
                                            :dataLists = unitLists.data
                                            label = "Unit Pelayanan*"
                                            placeholder = "Pilih unit tujuan"
                                            captionField = "unit_nama"
                                            valueField = "unit_id"
                                            :detailItems = listUnitDesc
                                            :value = "daftar.unit_id"
                                            v-on:item-select = "selectedUnit"
                                            ></search-list>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Ruang Pelayanan*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="daftar.ruang_id" class="uk-select uk-form-small" required>
                                                    <option v-if="unitRuangLists" v-for="dt in unitRuangLists" :value="dt.ruang_id">{{dt.ruang_nama}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Unit*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="daftar.dokter_id" class="uk-select uk-form-small" @change="selectedDokter" required>
                                                    <option v-if="unitDokterLists" v-for="dt in unitDokterLists" :value="dt.dokter_id">{{dt.dokter_nama}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jadwal Dokter*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="daftar.jadwal_id" class="uk-select uk-form-small" required>
                                                    <option v-if="jadwalDokterLists" v-for="dt in jadwalDokterLists" :value="dt.jadwal_id">{{ dt.mulai + ' : ' + dt.selesai}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Keterangan asal pasien</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="daftar.ket_asal_pasien" type="text" name="tempat_lahir">
                                            </div>
                                        </div>
                                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="daftar.is_pasien_baru" style="margin-left:5px;"> Pasien baru</label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </li>

                    <!-- LIST PASIEN -->
                    <li>
                        <!-- LIST HEADER -->
                        <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                            <div class="uk-width-expand" style="padding:0;margin:0;">
                                <h5 class="uk-text-uppercase">LIST PASIEN</h5>
                            </div>
                        </div>
                        <div style="margin-top:1em;">
                            <base-table ref="tablePasien"
                                :columns = "tbColumns" 
                                :config="configTable" 
                                :buttons = "buttons"
                                :urlSearch="searchUrlPatient">
                            </base-table>
                        </div>
                    </li>

                    <!-- FORM PASIEN -->
                    <li>
                        <!-- <form-pasien ref="switchFormPasien" v-on:formPatientClosed="formPasienClosed">
                        </form-pasien> -->
                    </li>
                </ul>
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import Dropdown from '@/Components/Dropdown.vue';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';
import FormPasien from '@/Pages/MasterData/Pasien/components/FormPasien.vue';
import BaseTable from '@/Components/BaseTable';

import { ref } from 'vue';
// import Datepicker from '@vuepic/vue-datepicker';
// import '@vuepic/vue-datepicker/dist/main.css';

const date = ref();

export default {
    name : 'form-pendaftaran', 
    components: {
        Dropdown,
        SearchSelect,
        SearchList,
        FormPasien,
        BaseTable,
        // Datepicker,
    },
    data() {
        return {
            isUpdate : true,
            isEdit : false,
            kelompok : {
                client_id:null,
                kelompok_tagihan_id:null,
                kelompok_tagihan : null,
                deskripsi:null,
                is_aktif : true,
            },

            daftar : {
                // data registrasi
                reg_id : null,
                tgl_registrasi : null,
                jns_registrasi : null,
                cara_registrasi : null,
                tgl_periksa : null,
                kode_booking : null,
                no_antrian : null,
                jadwal_id : null,
                dokter_id : null,
                unit_id : null,
                ruang_id : null,
                bed_id : null,
                asal_pasien : null,
                ket_asal_pasien : null,
                pasien_id : null,
                jns_penjamin : null,
                penjamin_id : null,
                nama_penanggung : null,
                hub_penanggung : null,
                is_bpjs : null,
                is_pasien_baru : false,
                status_reg : null,
                status_pulang : null,
                cara_pulang : null,
                tgl_pulang : null,
            },

            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            configTable : {
                isExpandable : false,
                filterType : 'REGULAR', 
            },

            tbColumns : [
                { 
                    name : 'pasien_id', 
                    title : 'ID', 
                    colType : 'text', 
                },   
                { 
                    name : 'no_rm', 
                    title : 'RM', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Pilih Pasien', 'icon':'user','callback':this.pilihPasien },
                        // { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        // { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                    ],
                },   
                { 
                    name : 'nama_lengkap', 
                    title : 'Nama Pasien', 
                    colType : 'text',
                },   
                { 
                    name : 'tempat_lahir', 
                    title : 'Tempat Lahir', 
                    colType : 'text',
                },   
                { 
                    name : 'tgl_lahir', 
                    title : 'Tgl. Lahir', 
                    colType : 'text',
                },   
                { 
                    name : 'jns_kelamin', 
                    title : 'JK', 
                    colType : 'text',
                },   
                { 
                    name : 'jns_penjamin', 
                    title : 'Jenis Penjamin', 
                    colType : 'text',
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }
            ], 

            buttons : [
                { title : 'Kembali', icon:'arrow-left', 'callback' : this.listPasienBack },
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.switchFormPasien },
            ],
            searchUrlPatient : '/patients',
            searchUrl : '/registrations',

            // search-select unit pelayanan dan unit depo
            listPenjaminDesc : [
                { colName : 'penjamin_id', labelData : '', isTitle : false},
                { colName : 'penjamin_nama', labelData : '', isTitle : true},
            ],
            listUnitDesc : [
                { colName : 'unit_id', labelData : '', isTitle : false},
                { colName : 'unit_nama', labelData : '', isTitle : true},
            ],
            listRuangDesc : [
                { colName : 'ruang_id', labelData : '', isTitle : false},
                { colName : 'ruang_nama', labelData : '', isTitle : true},
            ],
            listBedDesc : [
                { colName : 'bed_id', labelData : '', isTitle : false},
                { colName : 'no_bed', labelData : '', isTitle : true},
            ],
            listDokterDesc : [
                { colName : 'dokter_id', labelData : '', isTitle : false},
                { colName : 'dokter_nama', labelData : '', isTitle : true},
            ],
            listJadwalDesc : [
                { colName : 'jadwal_id', labelData : '', isTitle : false},
                { colName : 'jam_praktik', labelData : '', isTitle : true},
            ],

            kota :[],
            kecamatan: [],
            kelurahan: [],
            kotaTinggal : [],
            kecamatanTinggal : [],
            kelurahanTinggal : [],

            unitList : [],
            unitRuangLists : [],
            unitBedLists : [],
            unitDokterLists : [],
            jadwalDokterLists : [],
            urlGetUnit : '',
            urlGetRuang : '',
            urlGetBed : '',
            urlGetDokter : '',
            urlGetJadwal : '',
            pasienKeyword : '',
            disabledInput : '1',
            arrData : [],
        }
    },
    created(){
        this.getDisabled(1);
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('asuransi',['asuransiLists']),
        ...mapGetters('unitPelayanan',['unitLists'],['doctorUnitLists']),
        ...mapGetters('ruang',['roomLists']),
        ...mapGetters('bed',['bedLists']),
        ...mapGetters('dokter',['doctorLists']),
        ...mapGetters('jadwalDokter',['scheduleLists']),
        ...mapGetters('referensi',[
            'salutRefs',
            'rhesusRefs',
            'golDarahRefs',            
            'agamaRefs',
            'pekerjaanRefs',
            'pendidikanRefs',
            'jenisPenjaminRefs',
            'hubKeluargaRefs',
            'statusKawinRefs',
            'sukuBangsaRefs',
            'kebangsaanRefs',
            'caraPulangRefs',
            'statusPulangRefs',
            'asalPasienRefs',
            'jenisAlergiRefs',
            'statusBedInapRefs',
            'kasusIgdRefs',
            'jenisPelayananRefs',
            'jenisRegistrasiRefs',
            'caraRegistrasiRefs',
            'isRefExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('pendaftaran',['createRegistrasi','dataRegistrasi','updateRegistrasi']),
        ...mapActions('kelompokTagihan',['createKelompokTagihan','dataKelompokTagihan','updateKelompokTagihan']),
        ...mapActions('pasien',['listPasien','dataPasien']),
        ...mapActions('unitPelayanan',['listUnitPelayanan','dataUnitPelayanan','listUnitDokter']),
        ...mapActions('ruang',['listRuang','dataRuang']),
        ...mapActions('bed',['listBed','dataBed']),
        ...mapActions('dokter',['listDokter','dataDokter']),
        ...mapActions('jadwalDokter',['listJadwalDokter','dataJadwalDokter']),
        ...mapActions('propinsi',['listPropinsi']),
        ...mapActions('kabupaten',['listKabupaten']),
        ...mapActions('kecamatan',['listKecamatan']),
        ...mapActions('kelurahan',['listKelurahan']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let param = {per_page:'ALL'};
            if(this.unitLists == null || this.unitLists.length == 0) {
                this.listUnitPelayanan(param).then((response) => {
                    if (response.success == true) {
                        this.unitList = response.data.data;
                    }
                })
            };
            this.memberUrl = "/workplaces/members";

            if(!this.isRefExist) { 
                this.listReferensi(); 
            };
        },

        clearPendaftaran(){
            // data pasien
            this.daftar.pasien_id = null;
            this.daftar.nama_pasien = null;
            this.daftar.tempat_lahir = null;
            this.daftar.jk = null;
            this.daftar.nama_penjamin = null;
            this.daftar.no_ktp = null;
            this.daftar.no_hp = null;
            this.daftar.alamat_tinggal = null;

            // data registrasi
            var today = new Date();
            this.daftar.tgl_registrasi = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
            this.daftar.jns_registrasi = null;
            this.daftar.cara_registrasi = null;
            this.daftar.tgl_periksa = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
            this.daftar.kode_booking = null;
            this.daftar.no_antrian = null;
            this.daftar.jadwal_id = null;
            this.daftar.dokter_id = null;
            this.daftar.unit_id = null;
            this.daftar.ruang_id = null;
            this.daftar.bed_id = null;
            this.daftar.asal_pasien = null;
            this.daftar.ket_asal_pasien = null;
            this.daftar.pasien_id = null;
            this.daftar.jns_penjamin = null;
            this.daftar.penjamin_id = null;
            this.daftar.nama_penanggung = null;
            this.daftar.hub_penanggung = null;
            this.daftar.is_bpjs = null;
            this.daftar.is_pasien_baru = false;
            this.daftar.status_reg = null;
            this.daftar.status_pulang = null;
            this.daftar.cara_pulang = null;
            this.daftar.tgl_pulang = null;
        },

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formPendaftaran').hide();
            return false;
        },

        // Disable input
        getDisabled(val){
            if(val == "1"){
                this.disabledInput = 1;
            } else {
                this.disabledInput = 0;
            }
        },

        submitPendaftaran(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                console.log(this.daftar);
                this.createRegistrasi(this.daftar).then((response) => {
                    if (response.success == true) {
                        alert("Pendaftaran baru berhasil dibuat.");
                        this.clearPendaftaran();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeModal();
                    }
                })
            }    
            else {
                this.updateRegistrasi(this.daftar).then((response) => {
                    if (response.success == true) {
                        alert("Data Pendaftaran berhasil diubah.");
                        this.clearPendaftaran();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeModal();
                    }
                })
            }         
        },

        switchListPasien(){
            UIkit.switcher('#switcherPendaftaran').show(1);
        },

        switchFormPasien(){
            this.clearParam
            UIkit.switcher('#switcherPendaftaran').show(2);
        },

        newPendaftaran(){
            this.isUpdate = false;
            this.clearPendaftaran();
            this.getDisabled(1);
            UIkit.modal('#formPendaftaran').show();
            this.$refs.switchFormPasien.newPasien();
        },  

        listPasienBack(){
            UIkit.switcher('#switcherPendaftaran').show(0);
        },

        formPasienClosed(){
            UIkit.switcher('#switcherPendaftaran').show(1);
        },

        editRegistrasi(data){
            this.clearPendaftaran();
            this.dataRegistrasi(data.reg_id).then((dataReg)=>{
                if (dataReg.success == true) {
                    this.dataPasien(data.pasien_id).then((dataPas) => {
                    if (dataPas.success == true) {
                        this.arrData = [
                            dataPas,
                            dataReg.data
                        ];
                        this.isUpdate = true;
                        this.isEdit = true;
                        this.fillData(this.arrData);
                        UIkit.modal('#formPendaftaran').show();
                        this.$refs.switchFormPasien.newPasien();
                    } else {
                        alert(dataPas.message);
                    }})
                } else {
                    alert(dataReg.message);
                }
            })
        },

        selectedPenjamin(data){
            this.daftar.penjamin_id = null;
            if (data) {
                this.daftar.penjamin_id = data.penjamin_id;
                this.daftar.jns_penjamin = data.jns_penjamin;
                this.daftar.is_bpjs = data.is_bpjs;
            }
        },

        selectedUnit(data){
            this.daftar.unit_id = null;
            if (data) {
                this.daftar.unit_id = data.unit_id;
                this.unitRuangLists = data.ruang;
                this.unitDokterLists = data.dokter;
                
                //if(this.isUpdate = true){
                    // this.urlGetRuang = `/rooms?unit_id=${data}&&per_page=ALL`;
                    // this.dataUnitPelayanan(data).then((response)=>{
                    //     if (response.success == true) {
                    //         this.unitRuangLists = response.data.ruang;
                    //         this.unitDokterLists = response.data.dokter;
                    //         this.daftar.unit_id = response.data.unit_id;
                    //     } else {
                    //         alert(response.message);
                    //     }
                    // });
                // } else {
                //     // this.urlGetRuang = `/rooms?unit_id=${data.unit_id}&&per_page=ALL`;
                //     this.dataUnitPelayanan(data.unit_id).then((response)=>{
                //         if (response.success == true) {
                //             this.unitRuangLists = response.data.ruang;
                //             this.unitDokterLists = response.data.dokter;
                //             this.daftar.unit_id = response.data.unit_id;
                //         } else {
                //             alert(response.message);
                //         }
                //     });
                // };
            }
        },

        selectedRuang(data){
            if(data) {
                let selRuang = this.unitRuangLists.find(item => item.ruang_id == this.daftar.ruang_id);
                this.unitBedLists = JSON.parse(JSON.stringify(selRuang.beds));
                //this.urlGetBed = `/beds?ruang_id=${data.ruang_id}&&per_page=ALL`;
                //this.daftar.ruang_id = data.ruang_id;
            }
        },

        // selectedBed(data){
        //     if(data) {
        //         this.daftar.bed_id = data.ruang_id;
        //     }
        // },

        selectedDokter(){
            let selDoc = this.unitDokterLists.find(item => item['dokter_id'] == this.daftar.dokter_id);
            this.dataDokter(selDoc.dokter_id).then((response)=>{
                if (response.success == true) {
                    this.jadwalDokterLists = response.data.units[0].jadwal;
                } else {
                    alert(response.message);
                }
            });
        },

        selectedJadwal(data){
            if(data) {
                this.daftar.jadwal_id = data.jadwal_id;
            }
        },

        pilihPasien(data) {
            this.CLR_ERRORS();
            this.dataPasien(data.pasien_id).then((response) => {
                if (response.success == true) {
                this.arrData = [
                    response
                ];
                this.fillData(this.arrData);
                this.selectedPenjamin(response.data);
                UIkit.switcher('#switcherPendaftaran').show(0);
            }
            else { alert(response.message); }
            })
        },

        fillData(val){
            // fill data pasien
            this.daftar.pasien_id = val[0].data.pasien_id;
            this.daftar.nama_pasien = val[0].data.nama_pasien;
            this.daftar.tempat_lahir = val[0].data.tempat_lahir;
            this.daftar.jk = val[0].data.jns_kelamin == 'L' ? 'Laki-laki' : 'Perempuan';
            this.daftar.nama_penjamin = val[0].data.penjamin_nama;
            this.daftar.no_ktp = val[0].data.nik;
            this.daftar.no_hp = val[0].data.detail.no_telepon;
            this.daftar.alamat_tinggal = val[0].data.alamat.alamat;
            this.daftar.penjamin_id = val[0].data.penjamin_id;


            if(this.isEdit == true){
                this.daftar.reg_id = val[1].reg_id;
                this.daftar.unit_id = val[1].unit_id;
                let unitSel = this.unitList.find(item => item.unit_id == val[1].unit_id);
                this.unitRuangLists = unitSel.ruang;
                this.unitDokterLists = unitSel.dokter;
                let docSel = this.unitDokterLists.find(item => item.dokter_id == val[1].dokter_id);
                this.jadwalDokterLists = docSel.jadwal;
                this.daftar.jns_registrasi = val[1].jns_registrasi;
                this.daftar.cara_registrasi = val[1].cara_registrasi;
                this.daftar.asal_pasien = val[1].asal_pasien;
                this.daftar.nama_penanggung = val[1].nama_penanggung;
                this.daftar.hub_penanggung = val[1].hub_penanggung;
                this.daftar.ruang_id = val[1].ruang_id;
                this.daftar.dokter_id = val[1].dokter_id;
                this.daftar.jadwal_id = val[1].jadwal_id;
                this.daftar.ket_asal_pasien = val[1].ket_asal_pasien;
                this.daftar.is_pasien_baru = val[1].is_pasien_baru;
                this.daftar.jns_penjamin = val[1].jns_penjamin;
                this.daftar.is_bpjs = val[1].is_bpjs;
            }
        },

        clearParam(){
            this.isEdit = false;
            this.isUpdate = true;
            this.unitList = [];
            this.unitRuangLists = [];
            this.unitBedLists = [];
            this.unitDokterLists = [];
            this.jadwalDokterLists = [];
            this.urlGetUnit = '';
            this.urlGetRuang = '';
            this.urlGetBed = '';
            this.urlGetDokter = '';
            this.urlGetJadwal = '';
            this.pasienKeyword = '';
            this.disabledInput = '1';
            this.arrData = [];
        },
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
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

</style>