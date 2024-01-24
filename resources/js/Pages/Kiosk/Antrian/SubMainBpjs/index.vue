<template>
    <div style="padding:1em;">
        <!-- <div class="uk-width-1-1" style="padding:0 0 2em 0;">
            <h2 style="padding:0;margin:0;">ANTRIAN PASIEN RUJUKAN BPJS</h2>
            <h5 style="padding:0;margin:0;">gunakan no rujukan / no kepesertaan BPJS anda dibawah ini: </h5>
        </div> -->
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-auto" style="padding:0 0 1em 0;">
                <button class="uk-button button-jkn" type="button" @click.prevent="returnMainPage" style="width:60px;padding:0.5em;border:none;">
                    <span  uk-icon="icon: arrow-left; ratio: 2"></span>
                </button>
            </div>
            <div class="uk-width-expand" style="padding:0 0 1em 1em;">
                <h2 style="padding:0;margin:0;">ANTRIAN PASIEN RUJUKAN BPJS</h2>
                <h5 style="padding:0;margin:0;">gunakan no rujukan / no kepesertaan BPJS anda dibawah ini: </h5>
            </div>
        </div>
        <div style="padding: 1em 0 0 1em;">
            <form action="" @submit.prevent="searchKodeBooking">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-4" style="margin:0;padding-right:0;padding-left:0;">
                        <select class="uk-select input-select-jkn" v-model="tipeData" @change="tipeDataChange">
                            <option value="RUJUKAN">NO.RUJUKAN</option>
                            <option value="KARTU">NO.KEPESERTAAN</option>
                        </select>
                    </div>
                    <div class="uk-width-1-3" style="margin:0;padding-right:0;padding-left:0;">
                        <input class="uk-input input-text-jkn" type="text" v-model="keyword" :placeholder="[[placeholder]]" required style="color:black;">
                    </div>
                    <div class="uk-width-auto uk-grid-small" uk-grid>
                        <div class="uk-width-auto@s" style="margin:0;padding-right:0; padding-left:0.5em;">
                            <button class="uk-button button-jkn-primer" type="submit">OK</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-3@m">
                <dl class="uk-description-list hims-description-list">
                    <dt>NO ANTRIAN</dt>
                    <dd>{{responseData.no_antrian}}</dd>
                </dl>
            </div>
            <div class="uk-width-1-3@m">
                <dl class="uk-description-list hims-description-list">
                    <dt>UNIT</dt>
                    <dd>{{responseData.unit_nama}}</dd>
                </dl>
            </div>
            <div class="uk-width-1-3@m">
                <dl class="uk-description-list hims-description-list">
                    <dt>DOKTER</dt>
                    <dd>{{responseData.dokter_nama}}</dd>
                </dl>
            </div>
        </div> -->
        <div v-if="isLoading" style="text-align:center;padding:1em;">
            <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                <span uk-spinner></span> sedang mengambil data
            </p>
        </div>        
        <div v-else>
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
        </div>
        <div class="uk-grid-small" uk-grid>
            <button class="uk-button-large uk-button" style="background-color:#cc0202;color:#fff; margin-right:10px;">
                <span  uk-icon="icon: check; ratio: 2"></span>
                BENAR, ini data saya
            </button>
            <button class="uk-button-large uk-button" style="background-color:#fff;color:#000;">
                <span  uk-icon="icon: close; ratio: 2"></span>
                SALAH, ini bukan data saya
            </button>            
        </div>
    </div>
</template>

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
export default {
    name : 'sub-main-bpjs',
    data() {
        return {
            isLoading : false,
            keyword : null,
            tipeData : "RUJUKAN",
            placeholder : 'masukkan no rujukan',
            responseData : {
                booking_id : null,
                client_id : null,
                reg_id : null,
                angka_antrian : null,
                asal_booking : null,
                dokter_id : null,
                dokter_nama : null,
                unit_id : null,
                unit_nama : null,
                jadwal_id : null,
                tgl_registrasi : null,
                tgl_periksa : null,
                jam_periksa : null,
                jenis_kunjungan : null,
                kode_kunjungan : null,
                nik : null,
                no_antrian: null,
                no_kartu : null,
                no_rm : null,
                no_rujukan : null,
                pasien_id : null,
                keterangan_batal : null,
                json_request : null,
                json_response : null,
                rujukan : null,
                tgl_checkin : null,
                is_aktif : false,
                is_checkin : false,
                pasien_nama : null,
                tanggal_lahir : null,
                tempat_lahir : null,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    }, 
    methods: {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('kiosk',['jknAntrianData']),

        searchKodeBooking() {
            this.isLoading = true;
            this.CLR_ERRORS();
            this.jknAntrianData(this.keyword).then((response)=>{
                if (response.success == true) {
                    this.fillData(response.data);
                    this.isLoading = false;  
                } else {
                    this.isLoading = false;
                    alert(response.message);
                }
            })
        },

        tipeDataChange(){
            if(this.tipeData == 'RUJUKAN') { this.placeholder = 'masukkan no rujukan'; }
            else if(this.tipeData == 'KARTU') { this.placeholder = 'masukkan no peserta bpjs'; }
        },

        returnMainPage(){
            this.$emit('returnMainPage',true);
        },

        clearData(){

        },

        fillData(data) {
            this.responseData.booking_id = data.booking_id;
            this.responseData.client_id = data.client_id;
            this.responseData.reg_id = data.reg_id;
            this.responseData.angka_antrian = data.angka_antrian;
            this.responseData.asal_booking = data.asal_booking;
            this.responseData.dokter_id = data.dokter_id;
            this.responseData.dokter_nama = data.dokter_nama;
            this.responseData.unit_id = data.unit_id;
            this.responseData.unit_nama = data.unit_nama;
            this.responseData.jadwal_id = data.jadwal_id;
            this.responseData.tgl_registrasi = data.tgl_registrasi;
            this.responseData.tgl_periksa = data.tgl_periksa;
            this.responseData.jam_periksa = data.jam_periksa;
            this.responseData.jenis_kunjungan = data.jenis_kunjungan;
            this.responseData.kode_kunjungan = data.kode_kunjungan;
            this.responseData.nik = data.nik;
            this.responseData.no_antrian= data.no_antrian;
            this.responseData.no_kartu = data.no_kartu;
            this.responseData.no_rm = data.no_rm;
            this.responseData.no_rujukan = data.no_rujukan;
            this.responseData.pasien_id = data.pasien_id;
            this.responseData.keterangan_batal = data.keterangan_batal;
            this.responseData.tgl_checkin = data.tgl_checkin;
            this.responseData.is_aktif = data.is_aktif;
            this.responseData.is_checkin = data.is_checkin;
            
            if(data.json_request) {
                this.responseData.json_request = JSON.parse(JSON.stringify(data.json_request));
            }
            if(data.json_response) {
                this.responseData.json_response = JSON.parse(JSON.stringify(data.json_response));
            }
            this.responseData.rujukan = data.rujukan;     
            
            if(data.pasien) {
                this.responseData.pasien_nama = data.pasien.nama_lengkap;
                this.responseData.tanggal_lahir = data.pasien.tgl_lahir;
                this.responseData.tempat_lahir = data.pasien.tempat_lahir;
            }
        }
    }
}
</script>

<style>
.input-text-jkn {
    border:none;
    border-bottom:3px solid #cc0202;
}

.input-select-jkn {
    border:none;
    border-bottom:3px solid #cc0202;
    font-weight: 500;
}

.button-jkn-primer {
    border :2px solid #cc0202;
    font-weight: 500;
    background-color: #cc0202;
    color: #aaa;
}

.button-jkn-primer:hover {
    border :2px solid #cc0202;
    font-weight: 500;
    background-color: #cc0202;
    color: #fff;
}

.button-jkn {
    border :2px solid #cc0202;
    font-weight: 500;
    background-color: whitesmoke;
    color: #999;
}

.button-jkn:hover {
    border :2px solid #cc0202;
    font-weight: 500;
    background-color: whitesmoke;
    color: #000;
}

.jkn-antrian-table tr {
    padding:0.2em;
    margin:0;
}
.jkn-antrian-table tr td {
    padding:0.5em;
    margin:0;
    font-size:14px;
    color:black;
}

</style>