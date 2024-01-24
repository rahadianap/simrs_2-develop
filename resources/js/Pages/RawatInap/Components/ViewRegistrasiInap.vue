<template>
    <div style="margin:0;padding:0;">
        <div>
            <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">DATA PASIEN INAP</h4>
        </div>
        
        <div style="background-color:#fff;margin-top:1em;" >               
            <div style="padding:0.5em 0.5em 0.5em 1em;"  class="uk-position-z-index" uk-sticky>                    
                <div class="uk-grid-small hims-form-header" uk-grid style="padding:0;margin:0;">                        
                    <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                        <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                            <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="formPasienPulangClosed" style="padding:0.5em;"><span uk-icon="arrow-left"></span> Kembali</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="refreshData" style="padding:0.5em;"><span uk-icon="refresh"></span> Ambil Ulang</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="refreshData" style="padding:0.5em;"><span uk-icon="trash"></span> Hapus</button>
                            <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="gantiPenjamin" style="padding:0.5em;"><span uk-icon="tag"></span> Ubah Penjamin</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="gantiDokter" style="padding:0.5em;"><span uk-icon="users"></span> Ganti DPJP</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="gantiRuang" style="padding:0.5em;"><span uk-icon="sign-in"></span> Pindah Ruang</button>
                            <button type="button" class="hims-table-btn uk-width-auto" @click="pasienPulang" style="padding:0.5em;"><span uk-icon="happy"></span> Pasien Pulang</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-container uk-container-large">
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>
                <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                    <div uk-spinner="ratio : 2"></div>
                    <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                </div>
                <div v-else class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">     
                    <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">                        
                        <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA RAWAT INAP</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                detail data rawat inap.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                            <div class="uk-width-1-1">
                                <h5 style="font-weight: 500;padding:0;margin:0;">{{registrasi.pasien.no_rm}} - {{registrasi.pasien.nama_lengkap}} ({{registrasi.pasien.jns_kelamin}})</h5>
                            </div>         
                            <div class="uk-width-1-1"></div>
                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Reg.ID</dt>
                                    <dd>{{registrasi.reg_id}}</dd>
                                </dl>
                            </div>  
                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Transaksi.ID</dt>
                                    <dd>{{registrasi.trx_id}}</dd>
                                </dl>
                            </div>  

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
                                    <dt>Tgl. Masuk</dt>
                                    <dd>{{registrasi.tgl_masuk}} {{registrasi.jam_masuk}}</dd>
                                </dl>
                            </div>
                            <div class="uk-width-1-4@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Dokter ID</dt>
                                    <dd>{{registrasi.dokter_id}}</dd>
                                </dl>
                            </div>
                            <div class="uk-width-3-4@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Dokter DPJP</dt>
                                    <dd>{{registrasi.dokter_nama}}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                        <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PENJAMIN DAN KELAS</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                data penjamin ,penanggung dan kelas layanan.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Penjamin </dt>
                                    <dd>{{registrasi.penjamin_nama}}</dd>
                                </dl>
                            </div>
                            <div class="uk-width-1-4@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Jenis Penjamin </dt>
                                    <dd>{{registrasi.jns_penjamin}}</dd>
                                </dl>
                            </div>

                            <div class="uk-width-1-4@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>ID Kepesertaan</dt>
                                    <dd>{{registrasi.no_kepesertaan}}</dd>
                                </dl>
                            </div>
                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Penanggung </dt>
                                    <dd>{{registrasi.nama_penanggung}} ({{registrasi.hub_penanggung}})</dd>
                                </dl>
                            </div>
                            <div class="uk-width-1-4@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Kelas Penjamin</dt>
                                    <dd>{{getKelasValue(registrasi.kelas_penjamin)}}</dd>
                                </dl>
                            </div>
                            <div class="uk-width-1-4@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Kelas Pelayanan</dt>
                                    <dd>{{getKelasValue(registrasi.kelas_harga)}}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                        <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN PULANG</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                detail data pasien pulang.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Tgl Keluar </dt>
                                    <dd>{{registrasi.tgl_pulang}} {{registrasi.jam_pulang}}</dd>
                                </dl>
                            </div>
                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Cara Pulang </dt>
                                    <dd>{{registrasi.cara_pulang}}</dd>
                                </dl>
                            </div>

                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Status Pulang </dt>
                                    <dd>{{registrasi.status_pulang}}</dd>
                                </dl>
                            </div>

                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Catatan Pulang </dt>
                                    <dd>{{registrasi.catatan_pulang}}</dd>
                                </dl>
                            </div>

                            <!-- <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Keluar*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_pulang" style="color:black;" :min="minDate" required>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Keluar*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="time" v-model="registrasi.jam_pulang" style="color:black;" required>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Cara Pulang*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.cara_pulang" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="caraPulangSelected"> 
                                        <option v-for="option in caraPulangLists" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status Pulang*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.status_pulang" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="statusSelected"> 
                                        <option v-for="option in statusPulangLists" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="text" v-model="registrasi.catatan_pulang" style="color:black;" required>
                                </div>
                            </div> -->
                        </div>
                    </div> 
                    
                    <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                        <div class="uk-width-1-1@m" style="padding:0;">
                            <h5 style="color:indigo;padding:0;margin:0;">RIWAYAT PEMAKAIAN BED
                                <span style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    riwayat pemakaian bed inap dan ruang.
                                </span>                                    
                            </h5>
                        </div>
                        <div class="uk-width-1-1@m" style="padding:0;">
                            <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                <thead>
                                    <th style="text-align:center;padding:1em; background-color:#cc0202; color:white;">Tgl Masuk</th>
                                    <th style="text-align:center;padding:1em; background-color:#cc0202; color:white;">Tgl.Keluar</th>
                                    <th style="text-align:left;padding:1em; background-color:#cc0202; color:white;">Unit</th>
                                    <th style="text-align:left;padding:1em; background-color:#cc0202; color:white;">Ruang</th>
                                    <th style="text-align:center;padding:1em; background-color:#cc0202; color:white;">No.Bed</th>
                                </thead>
                                <tbody>
                                    <tr v-for="bed in registrasi.beds">
                                        <td style="width:130px;padding:1em; font-size: 12px; text-align:center;color:black;">{{bed.tgl_masuk}} {{bed.jam_masuk}}</td>
                                        <td style="width:130px;padding:1em; font-size: 12px; text-align:center;color:black;">{{bed.tgl_keluar}} {{bed.jam_keluar}}</td>
                                        <td style="padding:1em; font-size: 12px; text-align:left; color:black;">{{bed.unit_nama}}</td>
                                        <td style="padding:1em; font-size: 12px; text-align:left; color:black;">{{bed.ruang_nama}}</td>
                                        <td style="width: 80px; font-size: 12px; padding:1em; text-align:center;color:black;">{{bed.no_bed}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <modal-picker-ruang ref="roomModalPicker" 
            title="Pilih Ruang Inap" 
            :fieldDatas = pickerColumns 
            :urlSearch = "urlPicker" 
            viewType = "table"
            :proceedFunction="roomSelected"
        ></modal-picker-ruang> -->
        <modal-ganti-dokter ref="modalGantiDokter" v-on:modalGantiDokterClosed="refreshData"></modal-ganti-dokter>
        <modal-ganti-ruang ref="modalGantiRuang" v-on:modalGantiRuangClosed="refreshData"></modal-ganti-ruang>
        <modal-pasien-pulang ref="modalPasienPulang" v-on:modalPasienPulangClosed="refreshData"></modal-pasien-pulang>
        <modal-ganti-penjamin ref="modalGantiPenjamin" v-on:modalGantiPenjaminClosed="refreshData"></modal-ganti-penjamin>
    </div>
    
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
//import ModalPickerRuang from '@/Pages/Pendaftaran/RawatInap/components/ModalPickerRuang';
import ModalGantiDokter from '@/Pages/RawatInap/Components/ModalGantiDokter.vue';
import ModalGantiRuang from '@/Pages/RawatInap/Components/ModalGantiRuang.vue';
import ModalPasienPulang from '@/Pages/RawatInap/Components/ModalPasienPulang.vue';
import ModalGantiPenjamin from '@/Pages/RawatInap/Components/ModalGantiPenjamin.vue';

export default {
    name : 'view-registrasi-inap',
    components : { 
        //ModalPickerRuang, 
        ModalGantiDokter,
        ModalGantiRuang,
        ModalPasienPulang,
        ModalGantiPenjamin,
    },
    data() {
        return {
            formTitle : 'REGISTRASI BARU',
            isUpdate : false,
            isLoading : false,
            //urlPicker : '',
            dokterUrl : '/doctors',

            // pickerColumns : [ 
            //     { name : 'bed_id', title : 'Bed ID', colType : 'text', isCaption : true, },
            //     { name : 'unit_nama', title : 'Unit', colType : 'text', isCaption : false, },
            //     { name : 'ruang_nama', title : 'Ruang', colType : 'text', isCaption : false, },            
            //     { name : 'kelas_kamar', title : 'Kelas', colType : 'text', isCaption : false, },            
            //     { name : 'no_bed', title : 'No Bed', colType : 'text', isCaption : false, },
            //     { name : 'status', title : 'Status', colType : 'text', isCaption : false, },
            // ],
            registrasi : {
                reg_id : null,
                client_id : null,
                jns_registrasi : 'RAWAT_INAP',
                cara_registrasi : null,
                asal_data_pasien : null,
                tgl_periksa : this.getTodayDate(),
                
                tgl_masuk : this.getTodayDate(),
                jam_masuk : null,

                tgl_pulang : null,
                jam_pulang : null,
                catatan_pulang : null,
                
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
                jam_pulang : null,

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
                beds : [],
            },
            itemUrl : '',
            unitUrl : '',            
            kelasHargaLists : [],
            caraPulangLists : [],
            statusPulangLists : [],

            minDate : this.getTodayDate(),
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelas',['classLists']),
        ...mapGetters('referensi',['isRefExist','caraPulangRefs','statusPulangRefs']),    
    },

    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapActions('rawatInap',['dataAdmisiInap','deleteAdmisiInap','updatePasienPulang','confirmPasienPulang']),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('kelas',['listKelas']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();     
            if(!this.isRefExist) { 
                this.listReferensi({per_page:'ALL'}).then((response) => {
                    if (response.success == true) {
                        this.retrieveReferensi();
                    }
                })   
            }     

            this.listKelas({per_page:'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.kelasHargaLists = response.data.data.filter(function (el) { return el.is_kelas_harga == true; });
                } 
                else { alert(response.message); }
            })  
        },

        retrieveReferensi(){
            this.caraPulangLists = JSON.parse(JSON.stringify(this.caraPulangRefs.json_data));
            this.statusPulangLists = JSON.parse(JSON.stringify(this.statusPulangRefs.json_data));            
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

        getKelasValue(data) {
            if(data) {
                let val = this.kelasHargaLists.find( item => item.kelas_id == data)
                if(val) { return val.kelas_nama; }
                else { return null; }
            }
        },

        /** function called before modal closed */
        formPasienPulangClosed(){
            this.clearRegistrasi();
            this.$emit('formPasienPulangClosed',true);
        },
        
        refreshData(){
            let data = JSON.parse(JSON.stringify(this.registrasi));
            this.editData(data);
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
            this.registrasi.beds = [];
            this.minDate = this.getTodayDate();            
            this.clearPasien();
        },

        fillRegistrasi(data){
            // let unit = this.unitLists.find( item => item.unit_id == data.unit_id );
            // if(unit) { this.dokterLists = unit.dokter; }
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
            this.registrasi.jam_pulang= data.jam_pulang;
            this.registrasi.is_aktif = data.is_aktif;
            this.registrasi.client_id = data.client_id;
            this.registrasi.is_konfirmasi = data.is_konfirmasi;
            this.registrasi.beds = data.beds;
            this.minDate = this.registrasi.tgl_masuk;

            this.fillPasien(data.pasien);
            this.isUpdate = true;
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
                        this.formPasienPulangClosed();
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

        },

        gantiDokter(){
            this.$refs.modalGantiDokter.editDokterPasien(this.registrasi);
        },

        gantiRuang(){
            this.$refs.modalGantiRuang.editRuangInap(this.registrasi);
        },

        pasienPulang(){
            this.$refs.modalPasienPulang.showData(this.registrasi);
        },

        gantiPenjamin(){
            this.$refs.modalGantiPenjamin.editPenjamin(this.registrasi);
        }

    }
}
</script>
<style>
</style>