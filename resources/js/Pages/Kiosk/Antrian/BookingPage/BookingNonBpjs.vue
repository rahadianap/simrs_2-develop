<template>
    <div>
        <div style="padding: 0 0 0 1em;">
            <form action="" @submit.prevent="searchPasien">
                <div class="uk-grid-small" uk-grid style="padding:1em 1em 1em 0;">
                    <div class="uk-width-1-4" style="margin:0;padding-right:0;padding-left:0;">
                        <select v-model="pasien.jenis_id" class="uk-select input-select-jkn" @change="jenisIdPasienChange">
                            <option value="RM">NO. RM</option>
                            <option value="NIK">NIK / KTP</option>
                        </select>
                    </div>
                    <div class="uk-width-expand" style="margin:0;padding-right:0;padding-left:0;">
                        <input id="keywordPasien" class="uk-input input-text-jkn" type="text" v-model="pasien.keyword" required style="color:black;"  :placeholder="[[placeholderPasien]]">
                    </div>
                    <div class="uk-width-auto uk-grid-small" uk-grid>
                        <div class="uk-width-auto@s" style="margin:0;padding-right:0; padding-left:0.5em;">
                            <button class="uk-button button-jkn-primer" type="submit">CARI</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div v-if="isLoading" style="text-align:left;padding:1em;">
            <p style="font-size:18px; font-weight: 500; font-style:italic; padding:0.5em; color:black;">
                <span uk-spinner></span> sedang mengambil data
            </p>
        </div>        
        <template v-if="show_pasien_data">
            <div>
                <div class="uk-grid-small" uk-grid style="margin-top:0.2em;">
                    <div class="uk-width-expand@m" style="padding:0.3em;">  
                        <div class="uk-card-default" style="padding:1em 1em 0.5em 1em;border-radius: 0;border-top:3px solid #ccc;">
                            <div class="uk-width-1-1" style="padding:0.5em 1em 0 0.5em;">
                                <h4 style="font-weight:500;">Identitas Pasien</h4>
                            </div>                      
                            <data-pasien ref="dataPasien" :pasien="pasien"></data-pasien>
                        </div>
                    </div>
                    <div v-if="pasien.is_valid" class="uk-width-1-2@m" style="padding:0.3em;">  
                        <div class="uk-card-default" style="padding:1em 1em 1em 1em;border-radius: 0;border-top:3px solid #ccc;">
                            <div class="uk-grid-small" uk-grid>
                                <div class="uk-width-1-1" style="padding:0.4em 1em 1em 0.5em;">
                                    <h4 style="font-weight:500;">Pilih Poli dan Dokter</h4>
                                </div>
                                <template v-if="!dokter_selected">
                                    <div class="uk-width-1-1 uk-grid-small" uk-grid>
                                        <div class="uk-width-auto">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Konsultasi*</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input input-text-jkn" type="date" v-model="jadwal.tgl_periksa" style="color:black;" :min="minDate" @change="dateJadwalChange">
                                            </div>
                                        </div>
                                        <div class="uk-width-expand">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Filter Poli*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="jadwal.unit_id" class="uk-select input-select-jkn" @change="poliChange">
                                                    <option v-for="poli in poliLists" v-bind:value="poli.unit_id" v-bind:key="poli.unit_id">{{poli.unit_nama}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-1" style="padding-top:1em;">
                                            <p style="font-weight:400;font-size:12px;margin:0;padding:0;font-style: italic;" class="uk-text-uppercase">
                                                Dokter {{jadwal.unit_nama}}:
                                            </p>
                                        </div>
                                        <div class="uk-width-1-1" style="margin-top:0.5em;">
                                            <div  style="padding:0;margin:0;background-color: #eee;">
                                                <div class="uk-child-width-1-2@m uk-child-width-1-2@s uk-grid-small" uk-grid style="padding:0;margin:0;" uk-height-match="target: > div > .card-button">
                                                    <div v-for="jd in jadwalLists" class="card-button-container">
                                                        <dokter-button :data="jd" :fnCallback="dokterClicked"></dokter-button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="uk-width-1-1">
                                        <div class="uk-grid-small" uk-grid>
                                            <div class="uk-width-auto">
                                                <img class="uk-border-circle1" width="120" height="120" :src="jadwal.url_avatar" alt="Avatar">
                                            </div>
                                            <div class="uk-width-expand" style="padding-top:0.5em;">
                                                <h4 class="modal-subtitle">{{ jadwal.unit_nama }}</h4>
                                                <h2 class="modal-title">{{ jadwal.dokter_nama }}</h2>
                                                <p style="margin:0; padding:0; font-size:16px; color:black;">{{ jadwal.nama_hari }}, {{ jadwal.tgl_periksa }}</p>
                                                <p style="margin:0; padding:0; font-size:16px; color:black;">{{ jadwal.mulai }} - {{ jadwal.selesai }}</p>
                                            </div>
                                            <div class="uk-width-1-1">
                                                <button type="button" class="uk-button" @click.prevent="gantiDokterClicked">Ganti Dokter / Jadwal</button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-1" style="padding:1em; text-align:center;">
                    <!-- <div class="uk-grid-small" uk-grid style=" text-align:center;"> -->
                    <div>
                        <button class="uk-button-large uk-button uk-modal-close" type="button" style="background-color:#fff;color:#000;">
                            <span  uk-icon="icon: close; ratio: 2"></span>
                            BATAL
                        </button>
                        <button class="uk-button-large uk-button" @click.prevent="bookingSubmit" style="background-color:#cc0202;color:#fff; margin-left:10px;">
                            <span  uk-icon="icon: check; ratio: 2"></span>
                            DAFTAR
                        </button>
                    </div>
                </div>
            </div>
        </template>
        <modal-dokter ref="modalDokter" :fnSelect="dokterSelected"></modal-dokter>
        <modal-pasien ref="modalPasien" :fnSelect="pasienSelected"></modal-pasien>
        <modal-no-antrian ref="modalNoAntrian" :modeCetakFull = "isCetakWalkIn" v-on:closedModalNoAntrian="returnToMainPage"></modal-no-antrian>
    </div>
</template>

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';

import DokterButton from '@/Pages/Kiosk/Antrian/SubMainUmum/DokterButton.vue';
import ModalDokter from '@/Pages/Kiosk/Antrian/components/ModalDokter.vue';
import ModalPasien from '@/Pages/Kiosk/Antrian/components/ModalPasien.vue';
import DataPasien from '@/Pages/Kiosk/Antrian/components/DataPasien.vue';
import ModalNoAntrian from '@/Pages/Kiosk/Antrian/components/ModalNoAntrian.vue';

export default {
    name : 'booking-non-bpjs',
    components : { DataPasien, DokterButton, ModalDokter, ModalPasien, ModalNoAntrian },
    data() {
        return {
            isLoading : false,
            is_pasien_exist : false,
            show_pasien_data : false,
            dokter_selected : false,
            jadwal_selected : false,
            isCetakWalkIn : true,
            keyword : null,
            placeholder : null,
            placeholderPasien : 'masukkan no rekam medis',
            minDate : null,
            pasien : {
                jenis_id : 'RM',
                keyword : null,
                is_valid : false,
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                salut : null,
                nama_lengkap : null,
                nik : null,
                no_kk : null,
                no_telepon : null,
                jns_kelamin : null,
                tgl_lahir : null,
                tempat_lahir : null,        
                alamat_tinggal : null,
                alamat_ktp : null,
            },
            // poli : {
            //     unit_id : null,
            //     unit_nama : null,
            // },
            jadwalLists : null,
            // booking : {
            //     is_bpjs : null,
            //     is_walkin : true,
            //     jenis_identitas : 'KARTU',
            //     tgl_periksa : null,  
            // },

            jadwal : {
                tipe_registrasi : 'NONBPJS',
                no_kepesertaan : '-',
                no_rujukan : '-',
                pasien_id : null,
                jadwal_id : null,
                unit_id : null,
                unit_nama : null,
                dokter_id : null,
                dokter_nama : null,
                hari : null,
                nama_hari : null,
                mulai : null,
                selesai : null,
                tanggal_registrasi : null,        
                biografi : null,
                url_avatar : null,
                tempat_lahir : null,
                tgl_lahir : null,
                pend_akhir : null,   
                tgl_periksa : null,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kiosk',['kioskData','poliLists']),
    },
    mounted() {
        this.init();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('kiosk',['SET_PASIEN','CLR_DATA','SET_JADWAL']),
        ...mapActions('kiosk',['jknAntrianData','pasienData','listUnitPoli','bookingAntrian']),

        init(){
            this.minDate = this.getTodayDate();
            this.jadwal.tgl_periksa = this.getTodayDate();
            this.setJadwalLists();
        },

        returnToMainPage(){
            this.$emit('closedForm',true);
        },

        clearData(){
            this.clearPasienData();
            this.clearJadwal();            
            this.jadwal_selected = false;
            this.dokter_selected = false;
            this.pasien.is_valid = false;
            this.is_pasien_exist = false;
            this.show_pasien_data = false;
            this.dateJadwalChange();
        },

        clearPasienData(){
            this.pasien.jenis_id = 'RM';
            this.pasien.keyword = null;
            this.pasien.is_valid = false;
            this.pasien.pasien_id = null;
            this.pasien.no_rm = null;
            this.pasien.nama_pasien = null;
            this.pasien.salut = null;
            this.pasien.nama_lengkap = null;
            this.pasien.nik = null;
            this.pasien.no_kk = null;
            this.pasien.no_telepon = null;
            this.pasien.jns_kelamin = null;
            this.pasien.tgl_lahir = null;
            this.pasien.tempat_lahir = null;        
            this.pasien.alamat_tinggal = null;
            this.pasien.alamat_ktp = null;
        },

        clearJadwal(){
            this.jadwal.jadwal_id = null;
            this.jadwal.unit_id = null;
            this.jadwal.unit_nama = null;
            this.jadwal.dokter_id = null;
            this.jadwal.dokter_nama = null;
            this.jadwal.hari = null;
            this.jadwal.nama_hari = null;
            this.jadwal.mulai = null;
            this.jadwal.selesai = null;
            this.jadwal.tanggal_registrasi = null;        
            this.jadwal.biografi = null;
            this.jadwal.url_avatar = null;
            this.jadwal.tempat_lahir = null;
            this.jadwal.tgl_lahir = null;
            this.jadwal.pend_akhir = null;   
            this.jadwal.tgl_periksa = this.getTodayDate();
        },

        isBpjsChange(){
            this.setJadwalLists();
        },

        jenisIdPasienChange(){
            if(this.pasien.jenis_id == 'RM') { this.placeholderPasien = 'masukkan no rekam medis'; }
            else if(this.pasien.jenis_id == 'NIK') { this.placeholderPasien = 'masukkan no nik/ktp'; }
        },

        setJadwalLists() {
            this.jadwalLists = [];
            if(this.poliLists) {
                this.poliLists.forEach(el => {
                    let dt = el.jadwal;
                    if(dt){
                        dt.forEach(d => {
                            //this.jadwalLists.push(el.jadwal);
                            console.log(d);
                            this.jadwalLists.push(d);
                        });                    
                    }
                });
            }
        },

        getTodayDate: function getTodayDate() {
            var dt = new Date();
            var year = dt.getFullYear();
            var month = dt.getMonth() + 1;
            if (month < 10) { month = "0".concat(month); }        
            var date = dt.getDate();
            if (date < 10) { date = "0".concat(date); }
            var str_dt = "".concat(year, "-").concat(month, "-").concat(date);
            return str_dt;
        },

        tipeIdCardChange(){
            if(this.tipeData == 'RUJUKAN') { this.placeholder = 'masukkan no rujukan'; }
            else if(this.tipeData == 'KARTU') { this.placeholder = 'masukkan no peserta bpjs'; }
            this.setJadwalLists();
        },

        poliChange(){
            let selPoli = this.poliLists.find(item => item.unit_id == this.jadwal.unit_id); 
            if(selPoli) {
                this.jadwal.unit_nama = selPoli.unit_nama;
                this.jadwalLists = selPoli.jadwal;
            }
        },

        dateJadwalChange(){
            this.CLR_ERRORS();
            this.jadwal.unit_id = null;
            this.jadwal.unit_nama = null;
            
            this.listUnitPoli(this.jadwal.tgl_periksa).then((response)=>{
                if (response.success == true) {
                    this.isLoading = false;
                    this.setJadwalLists();
                }
                else {
                    this.isLoading = false;
                    alert(response.message);
                }
            })
        },

        dokterClicked(data){
            data.tgl_periksa = this.jadwal.tgl_periksa;
            this.$refs.modalDokter.showModal(data);
        },

        dokterSelected(data){
            this.jadwal.jadwal_id = data.jadwal_id;
            this.jadwal.unit_id = data.unit_id;
            this.jadwal.unit_nama = data.unit_nama;
            this.jadwal.dokter_id = data.dokter_id;
            this.jadwal.dokter_nama = data.dokter_nama;
            this.jadwal.hari = data.hari;
            this.jadwal.nama_hari = data.nama_hari;
            this.jadwal.mulai = data.mulai;
            this.jadwal.selesai = data.selesai;
            this.jadwal.biografi = data.biografi;
            this.jadwal.url_avatar = data.url_avatar;
            this.jadwal.tempat_lahir = data.tempat_lahir;
            this.jadwal.tgl_lahir = data.tgl_lahir;
            this.jadwal.pend_akhir = data.pend_akhir;

            this.jadwal_selected = true;
            this.dokter_selected = true;
            this.$refs.modalDokter.closeModal();
        },

        searchPasien(){
            this.isLoading = true;
            this.show_pasien_data = false;
            this.pasien.is_valid = false;
            this.CLR_ERRORS();
            this.CLR_DATA();
            let dt = { 'tipe':this.pasien.jenis_id, 'id':this.pasien.keyword };

            this.pasienData(dt).then((response)=>{
                if (response.success == true) {
                    this.$refs.modalPasien.showModal(response.data);
                    this.isLoading = false;
                    this.jadwal.tgl_periksa = this.getTodayDate();
                    this.dateJadwalChange();
                } else {
                    this.isLoading = false;
                    alert(response.message);
                }
            })
        },

        fillPasien(data) {
            //this.show_pasien_data = true;
            this.pasien.pasien_id = data.pasien_id;
            this.pasien.no_rm = data.no_rm;
            this.pasien.nama_pasien = data.nama_pasien;
            this.pasien.salut = data.salut;
            this.pasien.nama_lengkap = data.nama_lengkap;
            this.pasien.nik = data.nik;
            this.pasien.no_kk = data.no_kk;
            this.pasien.jns_kelamin = data.jns_kelamin;
            this.pasien.no_telepon = data.no_telepon;
            this.pasien.tgl_lahir = data.tgl_lahir;
            this.pasien.tempat_lahir = data.tempat_lahir; 
            this.pasien.alamat_tinggal = data.alamat_tinggal;
            this.pasien.alamat_ktp = data.alamat_ktp;
        },

        pasienSelected(data){
            this.$refs.modalPasien.closeModal();
            if(data){
                this.pasien.is_valid = true;
                this.show_pasien_data = true;
                this.pasien.pasien_id = data.pasien_id;
                this.pasien.no_rm = data.no_rm;
                this.pasien.nama_pasien = data.nama_pasien;
                this.pasien.salut = data.salut;
                this.pasien.nama_lengkap = data.nama_lengkap;
                this.pasien.nik = data.nik;
                this.pasien.no_kk = data.no_kk;
                this.pasien.jns_kelamin = data.jns_kelamin;
                this.pasien.no_telepon = data.no_telepon;
                this.pasien.tgl_lahir = data.tgl_lahir;
                this.pasien.tempat_lahir = data.tempat_lahir; 
                this.pasien.alamat_tinggal = data.alamat_tinggal;
                this.pasien.alamat_ktp = data.alamat_ktp;
                this.jadwal.pasien_id = data.pasien_id;
                this.setJadwalLists();
            }
        },

        gantiDokterClicked(){
            this.jadwal_selected = false;
            this.dokter_selected = false;
            this.clearJadwal();
        },

        bookingSubmit(){
            
            console.log(this.jadwal);
            this.CLR_ERRORS();
            if(this.jadwal.jadwal_id == null || this.jadwal.pasien_id == null ||  this.jadwal.tgl_periksa == null) {
                alert('data belum lengkap. booking / pemesanan pelayanan konsultasi tidak dapat dibuat.');
                return false;
            }
            if(this.jadwal.jadwal_id == '' || this.jadwal.pasien_id == '') {
                alert('data belum lengkap. booking / pemesanan pelayanan konsultasi tidak dapat dibuat.');
                return false
            }
            else {
                this.isLoading  = true;
                this.bookingAntrian(this.jadwal).then((response)=>{
                    if (response.success == true) {
                        this.isLoading = false;
                        this.setPrintOut(response.data);
                    } 
                    else {
                        this.isLoading = false;
                        alert(response.message);
                    }
                })
            }
        },

        setPrintOut(data) {
            if(data.mode_reg == 'WALK IN') { this.isCetakWalkIn = true; }
            else { this.isCetakWalkIn = false; }
            this.$refs.modalNoAntrian.showModal(data);
        }
    }
}
</script>