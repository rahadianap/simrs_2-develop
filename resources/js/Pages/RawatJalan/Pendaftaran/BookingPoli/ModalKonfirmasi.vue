<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" :id="modalId" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Pemesanan Antrian Rawat Jalan</h2>
            <form action="" @submit.prevent="submitBookingPoli" style="margin:0;padding:0;">
                <div style="padding:0.5em 0.5em 0.5em 1em;">
                    <div class="uk-grid-small uk-card1 uk-card-default1 hims-form-header" uk-grid style="padding:0;margin:0;">                        
                        <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                            <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                                <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="modalClosed" style="padding:0.5em;"><span uk-icon="close"></span> Tutup</button>
                                <button type="button" class="hims-table-btn uk-width-auto" @click="hapusBooking" :disabled="!isUpdate" style="padding:0.5em;"><span uk-icon="trash"></span> Hapus</button>
                                <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                                <button type="submit" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-text"></span> Simpan</button>
                                <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="confirmData" :disabled="!isUpdate" style="padding:0.5em;"><span uk-icon="file-edit"></span> Konfirmasi</button>
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
                            <div v-if="registrasi.pasien_id == null" class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;text-align:center;">                        
                                <p style="font-style: italic;color:black;">(pasien belum dipilih)</p>
                            </div>
                            <div v-else class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">       
                                <div class="uk-width-1-1">
                                    <h5 style="font-weight: 500;padding:0;margin:0;">{{pasien.no_rm}} - {{pasien.nama_lengkap}} ({{pasien.jns_kelamin}})</h5>
                                    <p style="margin:0;padding:0;font-size: 11px; font-style:italic;">*Data pasien akan mengikuti perubahan master data pasien.</p>
                                </div>         
                                <div class="uk-width-1-1"></div>
                                <div class="uk-width-1-4@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>ID Pasien </dt>
                                        <dd>{{pasien.pasien_id}}</dd>
                                    </dl>
                                </div>        
                                <div class="uk-width-1-4@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Tempat | Tgl Lahir </dt>
                                        <dd>{{pasien.tempat_lahir}} , {{pasien.tgl_lahir}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>KTP (ID Card)</dt>
                                        <dd>{{pasien.nik}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Jenis Kelamin</dt>
                                        <dd v-if="pasien.jns_kelamin == 'L'">Laki-laki</dd>
                                        <dd v-else-if="pasien.jns_kelamin == 'P'">Perempuan</dd>
                                        <dd v-else>(Tidak tahu)</dd>                                
                                    </dl>
                                </div>
                                <div class="uk-width-1-1"></div>
                                <div class="uk-width-1-2@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Alamat KTP</dt>
                                        <dd>{{pasien.alamat}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <dl class="uk-description-list hims-description-list">                                
                                        <dt>Alamat Tinggal</dt>
                                        <dd>{{pasien.alamat_tinggal}}</dd>
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
                                
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Periksa*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_periksa" style="color:black;" required :min="minDate" @change="getTodayUnit"  :disabled="isUpdate">
                                    </div>
                                </div>

                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Periksa*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="time" v-model="registrasi.jam_periksa" required style="color:black;" disabled>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit Pelayanan*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="registrasi.unit_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="unitSelected" :disabled="isUpdate"> 
                                            <option v-for="option in unitLists" v-bind:value="option.unit_id" v-bind:key="option.unit_id">{{option.unit_nama}}</option>
                                        </select>
                                        <!-- <input v-else class="uk-input uk-form-small" type="text" v-model="registrasi.unit_nama" style="color:black;" readonly :disabled="isUpdate"> -->
                                    </div>
                                </div>
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jadwal Dokter*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="registrasi.jadwal_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="dpjpSelected" :disabled="isUpdate"> 
                                            <option v-for="option in dokterLists" v-bind:value="option.jadwal_id" v-bind:key="option.jadwal_id">
                                                {{option.nama_hari}} {{option.mulai}} - {{option.selesai}} | {{option.dokter_nama}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Ruang*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="registrasi.ruang_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="ruangSelected"> 
                                            <option v-for="option in roomLists" v-bind:value="option.ruang_id" v-bind:key="option.ruang_id">{{option.ruang_nama}}</option>
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
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penanggung*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="registrasi.hub_penanggung" required class="uk-select uk-form-small">
                                            <option v-if="isRefExist" v-for="dt in hubKeluargaRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Penanggung*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" required v-model="registrasi.nama_penanggung" type="text" style="color:black;">
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
    </div>
</template>
<script>

import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

import FormPasien from '@/Pages/MasterData/Pasien/components/FormPasien.vue';
import FormPendaftaran from '@/Pages/Pendaftaran/RawatJalan/components/FormPendaftaran.vue';
import headerPage from '@/Components/HeaderPage.vue';

export default {
    name : 'modal-konfirmasi',
    props : {
        modalId : { type:String, required:true },
    },
    components : {
        FormPasien,headerPage,FormPendaftaran,SearchSelect,SearchList,
    },
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

            penjaminDesc: [
                { colName : 'penjamin_nama', labelData : '', isTitle : true },
                { colName : 'penjamin_id', labelData : '', isTitle : false },
            ],

            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],

            pasien : {
                pasien_id : null,
                nik : null,
                no_kk : null,
                no_rm : null,
                salut : null,
                nama_pasien : null,
                nama_lengkap : null,
                tempat_lahir : null,
                tgl_lahir : null,
                jns_kelamin : null,
                alamat_tinggal : null,
                alamat : null,
            },
            
            registrasi : {
                reg_id : null,
                client_id : null,
                tgl_registrasi : this.getTodayDate(),
                jns_registrasi : 'RAWAT_JALAN',
                cara_registrasi : null,
                tgl_periksa : this.getNextDayDate(),
                jam_periksa : this.getTimeNow(),
                asal_pasien : null,
                ket_asal_pasien : null,                
                mode_reg : 'BOOKING',
                is_rujukan_int : false,
                jadwal_id : null,
                dokter_unit_id : null,
                dokter_id : null,
                dokter_nama : null,
                dokter_pengirim_id: null,
                dokter_pengirim: null,
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                pasien_id : null,
                is_pasien_baru : false,
                is_pasien_luar : false,                
                jns_penjamin : null,
                penjamin_id : null,            
                penjamin_nama : null,
                is_bpjs : false,                
                nama_penanggung : null,
                hub_penanggung : null,                
                status_reg : null,
                status_pulang : null,
                cara_pulang : null,
                tgl_pulang: null,
                is_aktif : true,
                client_id : null,
                reff_trx_id : null,
                is_konfirmasi : false,                
                diag_awal : null,
                no_sep : null,
                no_rujukan : null,
                pasien_id : null,
                tindakan : [],
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
        ...mapGetters('kelas',['classLists']),
        ...mapGetters('asuransi',['asuransiLists']),
        ...mapGetters('referensi',['isRefExist','jenisPelayananRefs','jenisRegistrasiRefs','asalPasienRefs',
            'caraRegistrasiRefs','salutRefs','jenisPenjaminRefs','hubKeluargaRefs',]),    
    },
    mounted(){
        this.initialize();
    },
    methods : {
        ...mapActions('booking',['createBooking','dataBooking','updateBooking','listUnitToday','confirmBooking','deleteBooking']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('asuransi',['listAsuransi']),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('kelas',['listKelas']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();     
            if(!this.isRefExist) { this.listReferensi({per_page:'ALL'}); }     
            let param = {
                'jenis_reg':this.registrasi.jns_registrasi, 
                'tanggal': this.registrasi.tgl_periksa,
            };

            this.listUnitToday(param).then((response) => {
                if (response.success == true) { this.unitLists = response.data; }
            })      

            this.listKelas({per_page:'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.kelasHargaLists = response.data.data.filter(function (el) { return el.is_kelas_harga == true; });
                } 
                else { alert(response.message); }
            })  
        },

        getTodayUnit(){
            this.unitLists = [];
            this.roomLists = [];
            this.registrasi.unit_id = null;
            this.registrasi.ruang_id = null;
            this.registrasi.dokter_id = null;
            
            this.CLR_ERRORS();
            let param = {
                'jenis_reg':this.registrasi.jns_registrasi, 
                'tanggal': this.registrasi.tgl_periksa,
            };
            this.listUnitToday(param).then((response) => {
                if (response.success == true) { 
                    this.unitLists = response.data; 
                    this.unitSelected();
                }
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

        getNextDayDate(){
            let dt = new Date();
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate() + 1;
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

        /** function called before modal closed */
        modalClosed(){
            this.clearRegistrasi();
            this.$emit('modalBookingClosed',true);
            UIkit.modal(`#${this.modalId}`).hide();
        },
        
        /**modal call from other component for update data entry */
        editData(refData) {
            this.CLR_ERRORS();
            this.clearRegistrasi();
            this.isUpdate = true;
            this.isLoading = true;
            if(refData) {
                this.dataBooking(refData.reg_id).then((response) => {
                    if (response.success == true) {
                        this.fillRegistrasi(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal(`#${this.modalId}`).show();
                    }
                    else { alert('data booking registrasi poli tidak ditemukan'); this.isLoading = false; }
                })
            }
        },

        clearPasien(){
            this.pasien.pasien_id = null;
            this.pasien.nik = null;
            this.pasien.no_kk = null;
            this.pasien.no_rm = null;
            this.pasien.salut = null;
            this.pasien.nama_pasien = null;
            this.pasien.nama_lengkap = null;
            this.pasien.tempat_lahir = null;
            this.pasien.tgl_lahir = null;
            this.pasien.jns_kelamin = null;
            this.pasien.alamat_tinggal = null;
            this.pasien.alamat = null;
        },

        fillPasien(data) {
            this.registrasi.nama_penanggung = data.nama_lengkap;
            this.registrasi.pasien_id = data.pasien_id;    
            this.pasien.pasien_id = data.pasien_id;
            this.pasien.nik = data.nik;
            this.pasien.no_kk = data.no_kk;
            this.pasien.no_rm = data.no_rm;
            this.pasien.salut = data.salut;
            this.pasien.nama_pasien = data.nama_pasien;
            this.pasien.nama_lengkap = data.nama_lengkap;
            this.pasien.tempat_lahir = data.tempat_lahir;
            this.pasien.tgl_lahir = data.tgl_lahir;
            this.pasien.jns_kelamin = data.jns_kelamin;

            if(data.alamat){
                this.pasien.alamat_tinggal = `${data.alamat.alamat_tinggal}, RT.${data.alamat.rt_tinggal}/RW.${data.alamat.rw_tinggal}, Kel.${data.alamat.kelurahan_tinggal}, Kec.${data.alamat.kecamatan_tinggal}, ${data.alamat.kota_tinggal} - ${data.alamat.propinsi_tinggal} ${data.alamat.kodepos_tinggal}`;
                this.pasien.alamat = `${data.alamat.alamat}, RT.${data.alamat.rt}/RW.${data.alamat.rw}, Kel.${data.alamat.kelurahan}, Kec.${data.alamat.kecamatan}, ${data.alamat.kota} - ${data.alamat.propinsi} ${data.alamat.kodepos}`;
            } 
        },

        clearRegistrasi() {
            this.registrasi.reg_id = null;
            this.registrasi.client_id = null;
            this.registrasi.tgl_registrasi = this.getTodayDate();
            this.registrasi.jns_registrasi = 'RAWAT_JALAN';
            this.registrasi.cara_registrasi = null;
            this.registrasi.tgl_periksa = this.getNextDayDate();
            this.registrasi.jam_periksa = this.getTimeNow();
            this.registrasi.asal_pasien = null;
            this.registrasi.ket_asal_pasien = null;                
            this.registrasi.mode_reg = 'BOOKING';
            this.registrasi.is_rujukan_int = false;
            this.registrasi.jadwal_id = null;
            this.registrasi.dokter_unit_id = null;
            this.registrasi.dokter_id = null;
            this.registrasi.dokter_nama = null;
            this.registrasi.dokter_pengirim_id= null;
            this.registrasi.dokter_pengirim= null;
            this.registrasi.unit_id = null;
            this.registrasi.unit_nama = null;
            this.registrasi.ruang_id = null;
            this.registrasi.ruang_nama = null;
            this.registrasi.pasien_id = null;
            this.registrasi.is_pasien_baru = false;
            this.registrasi.is_pasien_luar = false;                
            this.registrasi.jns_penjamin = null;
            this.registrasi.penjamin_id = null;            
            this.registrasi.penjamin_nama = null;
            this.registrasi.is_bpjs = false;                
            this.registrasi.nama_penanggung = null;
            this.registrasi.hub_penanggung = null;                
            this.registrasi.status_reg = null;
            this.registrasi.status_pulang = null;
            this.registrasi.cara_pulang = null;
            this.registrasi.tgl_pulang= null;
            this.registrasi.is_aktif = true;
            this.registrasi.client_id = null;
            this.registrasi.reff_trx_id = null;
            this.registrasi.is_konfirmasi = false;                
            this.registrasi.no_sep = null;
            this.registrasi.no_rujukan = null;
            this.registrasi.pasien_id = null;
            this.registrasi.tindakan = [];
            this.minDate = this.getNextDayDate();
            this.isUpdate = false;
        },

        fillRegistrasi(data){
            this.registrasi.tgl_periksa = data.tgl_periksa;
            this.getTodayUnit();
            this.registrasi.jns_penjamin = data.jns_penjamin;
            this.ambilPenjaminList();

            this.registrasi.reg_id = data.reg_id;
            this.registrasi.client_id = data.client_id;
            this.registrasi.tgl_registrasi = data.tgl_registrasi;
            this.registrasi.jns_registrasi = data.jns_registrasi;
            this.registrasi.cara_registrasi = data.cara_registrasi;
            this.registrasi.jam_periksa = data.jam_periksa;
            this.registrasi.asal_pasien = data.asal_pasien;
            this.registrasi.ket_asal_pasien = data.ket_asal_pasien;                
            this.registrasi.mode_reg = data.mode_reg;
            this.registrasi.is_rujukan_int = data.is_rujukan_int;
            this.registrasi.jadwal_id = data.jadwal_id;
            this.registrasi.dokter_unit_id = data.dokter_unit_id;
            this.registrasi.dokter_id = data.dokter_id;
            this.registrasi.dokter_nama = data.dokter_nama;
            this.registrasi.dokter_pengirim_id= data.dokter_pengirim_id;
            this.registrasi.dokter_pengirim= data.dokter_pengirim;
            this.registrasi.unit_id = data.unit_id;
            this.registrasi.unit_nama = data.unit_nama;
            this.registrasi.ruang_id = data.ruang_id;
            this.registrasi.ruang_nama = data.ruang_nama;
            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.is_pasien_baru = data.is_pasien_baru;
            this.registrasi.is_pasien_luar = data.is_pasien_luar; 
            this.registrasi.no_kepesertaan = data.no_kepesertaan;
            this.registrasi.penjamin_id = data.penjamin_id;            
            this.registrasi.penjamin_nama = data.penjamin_nama;
            this.registrasi.is_bpjs = data.is_bpjs;                
            this.registrasi.nama_penanggung = data.nama_penanggung;
            this.registrasi.hub_penanggung = data.hub_penanggung;                
            this.registrasi.status_reg = data.status_reg;
            this.registrasi.status_pulang = data.status_pulang;
            this.registrasi.cara_pulang = data.cara_pulang;
            this.registrasi.tgl_pulang= data.tgl_pulang;
            this.registrasi.is_aktif = data.is_aktif;
            this.registrasi.client_id = data.client_id;
            this.registrasi.reff_trx_id = data.reff_trx_id;
            this.registrasi.is_konfirmasi = data.is_konfirmasi;                
            this.registrasi.no_sep = data.no_sep;
            this.registrasi.no_rujukan = data.no_rujukan;
            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.tindakan = data.tindakan;

            let dt = this.jenisPenjaminRefs.json_data.find(item => item.value === this.registrasi.jns_penjamin);
            if(dt) { this.registrasi.is_bpjs = dt.is_bpjs; }
            
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

        dpjpSelected() {
            let data = this.dokterLists.find( item => item.jadwal_id == this.registrasi.jadwal_id );
            if(data) {
                this.registrasi.dokter_id = data.dokter_id;
                this.registrasi.dokter_nama = data.dokter_nama;
                this.registrasi.dokter_unit_id = data.dokter_unit_id;
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

                if(this.roomLists.length > 0) {                    
                    console.log(this.roomLists[0]);
                    this.registrasi.kelas_harga = this.roomLists[0].kelas_kamar;
                    this.registrasi.ruang_id = this.roomLists[0].ruang_id;
                    this.registrasi.ruang_nama = this.roomLists[0].ruang_nama;
                }
            }
        },

        ruangSelected() {
            let data = this.roomLists.find( item => item.ruang_id == this.registrasi.ruang_id );
            if(data) {
                this.registrasi.kelas_harga = data.kelas_kamar;
                this.registrasi.ruang_nama = data.ruang_nama;
            }
        },

        submitBookingPoli(){
            this.CLR_ERRORS();
            this.isLoading = true;      
            if(this.isUpdate) {
                //update data
                this.updateBooking(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        alert('Data berhasil diubah.');
                        this.modalClosed();
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
            else {
                //data baru
                this.createBooking(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        alert('Data berhasil disimpan.');
                        this.modalClosed();
                    }
                    else { 
                        alert(response.message); this.isLoading = false; 
                    }
                })
            }
        },

        hapusBooking(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus data registrasi ${this.registrasi.reg_id} - ${this.pasien.nama_pasien}. Lanjutkan ?`)){
                this.isLoading = true;
                this.deleteBooking(this.registrasi.reg_id).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; this.isUpdate = false;
                        alert('data booking rawat jalan BERHASIL dihapus.');
                        this.modalClosed();
                    }
                    else { 
                        alert('data booking rawat jalan gagal dihapus.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        confirmData(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengkonfirmasi kehadiran booking registrasi ${this.registrasi.reg_id} - ${this.pasien.nama_pasien} dan tidak dapat diubah kembali. Lanjutkan ?`)){
                this.isLoading = true;
                this.updateBooking(this.registrasi).then((response) => {
                    if (response.success == true) { 
                        this.saveConfirmData(); 
                    }
                    else { 
                        alert(response.message); this.isLoading = false; 
                    }
                })
            }
        },

        saveConfirmData(){
            this.confirmBooking(this.registrasi).then((response) => {
                if (response.success == true) {
                    this.isLoading = false; 
                    this.isUpdate = false;
                    this.modalClosed();
                    alert('data booking BERHASIL dikonfirmasi.');
                }
                else { 
                    alert(`Data booking tidak berhasil dikonfirmasi. ${response.message}`); 
                    this.isLoading = false;
                }
            })
        },

        newRegistrasi(data) {
            if(data) {
                this.CLR_ERRORS();
                this.clearRegistrasi();
                this.fillPasien(data);
                let dt = this.getNextDayDate();
                this.registrasi.tgl_periksa = dt;
                this.listUnitToday();
                UIkit.modal(`#${this.modalId}`).show();
            }
        }
    }
}
</script>