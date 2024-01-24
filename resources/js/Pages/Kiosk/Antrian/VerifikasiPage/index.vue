<template>
    <div style="padding:0.5em 1em 1em 1em;">
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-auto" style="padding:0 0 0.5em 0;">
                <button class="uk-button button-jkn" type="button" @click.prevent="returnMainPage" style="width:50px;padding:0.5em;border:none;">
                    <span  uk-icon="icon: arrow-left; ratio: 1.4"></span>
                </button>
            </div>
            <div class="uk-width-expand" style="padding:0.3em 0 0.5em 1em;">
                <h2 style="padding:0;margin:0;">VERIFIKASI KEHADIRAN</h2>
            </div>
        </div>

        <div style="padding:0 0 1em 0;margin: 0.5em 0 0 0;">
            <form action="" @submit.prevent="searchKodeBooking">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-expand" style="margin:0;padding-right:0;padding-left:0;max-width:500px;">
                        <input class="uk-input input-text-jkn" type="text" v-model="keyword" placeholder="masukkan kode booking" required style="color:black;">
                    </div>
                    <div class="uk-width-auto" style="margin:0;padding-right:0; padding-left:0.5em;">
                            <button class="uk-button button-jkn-primer" type="submit">CARI</button>
                    </div>
                </div>
            </form>
        </div>
        <div v-if="isLoading" style="text-align:center;padding:1em;">
            <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                <span uk-spinner></span> sedang mengambil data
            </p>
        </div>
        <div v-if="is_valid">
            <div>
                <div class="uk-grid-small" uk-grid style="margin-top:1em;padding-left:1em;">    
                    <div class="uk-width-1-1" style="padding:0;">
                        <p class="title-info">Pelayanan : </p>
                    </div>
                    <div class="uk-width-auto" style="padding:1em;background-color: whitesmoke;">
                        <h4 class="modal-subtitle">No. Antrian</h4>
                        <h2 class="modal-title">{{ responseData.no_antrian }}</h2>
                    </div>
                    <div class="uk-width-expand" style="padding:1em;">
                        <h4 class="modal-subtitle">{{ responseData.unit_nama }}</h4>
                        <h2 class="modal-title">{{ responseData.dokter_nama }}</h2>
                    </div>
                </div>

                <div class="uk-grid-small" uk-grid>                    
                    <div class="uk-width-1-4@l uk-width-1-3@m">
                        <p class="title-info">Tgl. Periksa : </p>
                        <p class="content-info">{{ responseData.tgl_periksa }} {{ responseData.jam_periksa }}</p>
                    </div>
                    <div class="uk-width-1-4@l uk-width-1-3@m">
                        <p class="title-info">Jadwal : </p>
                        <p class="content-info">{{ responseData.nama_hari }} {{ responseData.jadwal_mulai }} - {{ responseData.jadwal_selesai }}</p>
                    </div>
                </div>
                <div class="uk-width-1-1 uk-grid-small" uk-grid>
                    <div class="uk-width-1-1" style="padding-top:1em;">
                        <p class="title-info">Nama : </p>
                        <h1 style="padding:0;margin:0;">{{ responseData.nama_pasien }}</h1>
                    </div>

                    <div class="uk-width-1-1">
                        <p class="title-info">No.RM : </p>
                        <p class="content-info">{{ responseData.no_rm }}</p>
                    </div>
                    <div class="uk-width-1-1">
                        <p class="title-info">Tanggal Lahir : </p>
                        <p class="content-info">{{ responseData.tgl_lahir }}</p>
                    </div>
                    <div class="uk-width-1-1">
                        <p class="title-info">Tgl. Registrasi : </p>
                        <p class="content-info">{{ responseData.tgl_registrasi }}</p>
                    </div>
                </div>
            </div>
            <div class="uk-grid-small" uk-grid style="padding:2em 1em 1em 1em;">
                <button class="uk-button-large uk-button" type="button" @click.prevent="clearData" style="background-color:#fff;color:#000;">
                    <span  uk-icon="icon: close; ratio: 2"></span>
                    SALAH, ini bukan data saya
                </button>
                <button class="uk-button-large uk-button" @click.prevent="confirmBooking" style="background-color:#cc0202;color:#fff; margin-left:10px;">
                    <span  uk-icon="icon: check; ratio: 2"></span>
                    BENAR, ini data saya
                </button>
            </div>
        </div>
        <modal-verif-confirm ref="modalVerif" :modeCetakFull = "isCetakWalkIn" v-on:closedModalVerifConfirm="returnMainPage"></modal-verif-confirm>
    
        <!-- <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-1">
                <data-rujukan ref="dataRujukan" :rujukan="responseData"></data-rujukan>
            </div>
            <div class="uk-width-1-1">

            </div>
        </div>         -->
        <!-- <div v-else>
            <div v-if="responseData.rujukan">                
            </div>        
            <div v-else>
                data rujukan tidak ditemukan
            </div>
        </div>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-2@m uk-card-default" style="padding:1em;border-radius: 10px;">
                <table class="uk-table jkn-antrian-table">                
                    <tbody>
                        <tr>
                            <td colspan="3" style="font-weight:500; width:160px; font-size:16px;">DATA ANTRIAN</td>
                        </tr>
                        <tr>
                            <td style="font-weight:500; width:160px;">Unit</td>
                            <td>:</td>
                            <td>{{ responseData.unit_nama }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:500; width:160px;">Dokter</td>
                            <td>:</td>
                            <td>{{ responseData.dokter_nama }}</td>
                        </tr>                 
                        <tr>
                            <td style="font-weight:500; width:160px;">No.Antrian</td>
                            <td>:</td>
                            <td>{{ responseData.no_antrian }}</td>
                        </tr>   
                        <tr>
                            <td style="font-weight:500; width:160px;">Kode Booking</td>
                            <td>:</td>
                            <td>{{ responseData.booking_id }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:500; width:160px;">No Registrasi</td>
                            <td>:</td>
                            <td>{{ responseData.reg_id }}</td>
                        </tr>
                        
                        <tr>
                            <td style="font-weight:500; width:160px;">Tanggal Periksa</td>
                            <td>:</td>
                            <td>{{ responseData.tgl_periksa }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:500; width:160px;">Tanggal Registrasi</td>
                            <td>:</td>
                            <td>{{ responseData.tgl_registrasi }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:500; width:160px;">Nama</td>
                            <td>:</td>
                            <td>{{ responseData.pasien_nama }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:500; width:160px;">Tempat/tgl.Lahir</td>
                            <td>:</td>
                            <td>{{ responseData.tempat_lahir }} {{ responseData.tanggal_lahir }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:500; width:160px;">NIK</td>
                            <td>:</td>
                            <td>{{ responseData.nik }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:500; width:160px;">No.Kepesertaan</td>
                            <td>:</td>
                            <td>{{ responseData.no_kartu }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:500; width:160px;">No.Rujukan</td>
                            <td>:</td>
                            <td>{{ responseData.no_rujukan }}</td>
                        </tr>
                    </tbody>
                    
                </table>

            </div>
            <div class="uk-width-1-2@m">

            </div>
        </div> -->
        <!-- <div class="uk-grid-small" uk-grid>
            <button class="uk-button-large uk-button" style="background-color:#cc0202;color:#fff; margin-right:10px;">
                <span  uk-icon="icon: check; ratio: 2"></span>
                BENAR, ini data saya
            </button>
            <button class="uk-button-large uk-button" style="background-color:#fff;color:#000;">
                <span  uk-icon="icon: close; ratio: 2"></span>
                SALAH, ini bukan data saya
            </button>            
        </div> -->
    </div>
</template>

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import DataRujukan from '@/Pages/Kiosk/Antrian/components/DataRujukan.vue';
import ModalVerifConfirm from '@/Pages/Kiosk/Antrian/VerifikasiPage/ModalVerifConfirm.vue';

export default {
    name : 'verifikasi-page',
    components :{ DataRujukan, ModalVerifConfirm },
    data() {
        return {
            isLoading : false,
            keyword : null,
            is_valid : false,
            isCetakWalkIn : true,
            responseData : {
                antrian_id : null,
                client_id : null,
                reg_id : null,
                angka_antrian : null,
                no_antrian: null,
                tgl_periksa : null,
                jam_periksa : null,

                jadwal_id : null,
                hari : null,
                nama_hari : null,
                jadwal_mulai : null,
                jadwal_selesai : null,

                dokter_id : null,
                dokter_nama : null,
                unit_id : null,
                unit_nama : null,
                
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                tgl_lahir : null,
                nik : null,
                no_kk : null,

                tgl_registrasi : null,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    }, 
    methods: {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('kiosk',['jknAntrianData','dataBooking','verifikasiBooking']),
        
        
        searchKodeBooking() {
            this.is_valid = false;
            this.isLoading = true;
            this.CLR_ERRORS();
            this.dataBooking(this.keyword).then((response)=>{
                if (response.success == true) {
                    this.fillData(response.data);
                    this.isLoading = false;  
                } else {
                    this.isLoading = false;
                    alert(response.message);
                }
            })
        },

        returnMainPage(){
            this.clearData();
            this.$emit('returnMainPage',true);
        },

        clearData(){
            this.is_valid = false;
            this.keyword = null;
            this.responseData.antrian_id = null;
            this.responseData.client_id = null;
            this.responseData.reg_id = null;
            this.responseData.angka_antrian = null;
            this.responseData.no_antrian= null;
            this.responseData.tgl_periksa = null;
            this.responseData.jam_periksa = null;

            this.responseData.jadwal_id = null;
            this.responseData.hari = null;
            this.responseData.nama_hari = null;
            this.responseData.jadwal_mulai = null;
            this.responseData.jadwal_selesai = null;

            this.responseData.dokter_id = null;
            this.responseData.dokter_nama = null;
            this.responseData.unit_id = null;
            this.responseData.unit_nama = null;
                
            this.responseData.pasien_id = null;
            this.responseData.no_rm = null;
            this.responseData.nama_pasien = null;
            this.responseData.tgl_lahir = null;
            this.responseData.nik = null;
            this.responseData.no_kk = null;

            this.responseData.tgl_registrasi = null;
        },

        fillData(data) {
            this.responseData.antrian_id = data.kode_booking;
            this.responseData.client_id = data.client_id;
            this.responseData.reg_id = data.reg_id;
            this.responseData.angka_antrian = data.no_urut;
            this.responseData.no_antrian= data.no_antrian;
            this.responseData.tgl_periksa = data.tgl_periksa;
            this.responseData.jam_periksa = data.jam_periksa;
            this.responseData.tgl_registrasi = data.tgl_registrasi;
            this.responseData.dokter_id = data.dokter_id;
            this.responseData.dokter_nama = data.dokter_nama;
            this.responseData.unit_id = data.unit_id;
            this.responseData.unit_nama = data.unit_nama;
            
            this.responseData.jadwal_id = data.jadwal_id;
            this.responseData.hari = data.jadwal.hari;
            this.responseData.nama_hari = data.jadwal.nama_hari;
            this.responseData.jadwal_mulai = data.jadwal.mulai;
            this.responseData.jadwal_selesai = data.jadwal.selesai;
                
            this.responseData.pasien_id = data.pasien_id;
            this.responseData.no_rm = data.no_rm;
            this.responseData.nama_pasien = data.nama_pasien;
            this.responseData.tgl_lahir = data.tgl_lahir;
            this.responseData.nik = data.nik;
            this.responseData.no_kk = data.no_kk;
            this.is_valid = true;
        },

        confirmBooking(){
            this.CLR_ERRORS();
            this.verifikasiBooking(this.responseData).then((response)=>{
                if (response.success == true) {
                    this.showConfirmTiket(response.data);
                    this.isLoading = false;
                } else {
                    this.isLoading = false;
                    alert(response.message);
                }
            })
        },

        showConfirmTiket(data){
            this.$refs.modalVerif.showModal(data);                    
        }
    }
}
</script>

<style>
</style>