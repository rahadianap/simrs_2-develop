<template>
    <div class="uk-container uk-container-xlarge">
        <div style="padding:1em;">
            <header-antrian :mainText="mainText" :tagLine="myTagLine"></header-antrian>
        </div>
        <div style="padding:1em;">
            <ul id="switcherAntrian" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
                <li><a href="#">Utama</a></li>
                <li><a href="#">Form Pasien Baru</a></li>
                <li><a href="#">Pendaftaran</a></li>
                <li><a href="#">Verifikasi</a></li>
                <li><a href="#">Pembayaran</a></li>
                <li><a href="#">Konfirmasi</a></li>
                <li><a href="#">Selesai</a></li>
            </ul>
            <ul class="uk-switcher" style="padding:0;margin:0;">
                <li style="padding:0;margin:0;">
                    <div class="uk-container uk-container-xlarge">                    
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-1-1" style="padding:2em 0 1em 1em;">
                                <h2 style="padding:0;margin:0;">Selamat Datang,</h2>
                                <h5 style="padding:0;margin:0;">Silahkan pilih layanan dibawah ini </h5>
                            </div>
                            <div class="uk-child-width-1-3@m uk-child-width-1-2@s uk-grid-small" uk-grid uk-height-match="target: > div > .card-button">
                                <div class="card-button-container">
                                    <card-button cardTitle="PENDAFTARAN PASIEN BARU" cardNo="1" cardSubTitle="pendaftaran pasien baru" :fnCallback="pasienBaruClicked"></card-button>
                                </div>
                                <div class="card-button-container">
                                    <card-button cardTitle="JANJI KONSULTASI SPESIALIS" cardNo="2" cardSubTitle="pendaftaran janji konsultasi dokter (spesialis)" :fnCallback="janjiKonsulClicked"></card-button>
                                </div>

                                <div class="card-button-container">
                                    <card-button cardTitle="VERIFIKASI KEHADIRAN" cardNo="3" cardSubTitle="verifikasi (checkin) booking konsultasi"  :fnCallback="verifikasiClicked"></card-button>
                                </div>

                                <div class="card-button-container">
                                    <card-button cardTitle="PEMBAYARAN / KASIR" cardNo="4" cardSubTitle="ambil antrian pembayaran di kasir." :fnCallback="antrianKasirClicked"></card-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li style="padding:0;margin:0;">
                    <pasien-page ref="pasienPage" v-on:returnMainPage = "showMainPage"></pasien-page>
                </li>
                <li style="padding:0;margin:0;">
                    <booking-page ref="bookingPage" v-on:returnMainPage = "showMainPage"></booking-page>
                </li>
                <li style="padding:0;margin:0;">
                    <verifikasi-page ref="verifikasiPage" v-on:returnMainPage = "showMainPage"></verifikasi-page>
                </li>
                
                <li style="padding:0;margin:0;">
                    <cashier-page ref="cashierPage" v-on:returnMainPage = "showMainPage"></cashier-page>
                </li>
                <li style="padding:0;margin:0;">
                    <sub-main-jkn v-on:returnMainPage = "showMainPage"></sub-main-jkn>
                </li>
                <li style="padding:0;margin:0;"></li>
            </ul>
        </div>
            
    </div>
</template>

<script>

import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
//import MainPage from '@/Pages/Kiosk/Antrian/MainPage';
import HeaderAntrian from '@/Pages/Kiosk/Antrian/components/HeaderAntrian.vue'
import CardButton from '@/Pages/Kiosk/Antrian/components/CardButton.vue'
import SubMainJkn from '@/Pages/Kiosk/Antrian/SubMainJkn'
import SubMainBpjs from '@/Pages/Kiosk/Antrian/SubMainBpjs'
import SubMainUmum from '@/Pages/Kiosk/Antrian/SubMainUmum'
import BookingPage from '@/Pages/Kiosk/Antrian/BookingPage'
import VerifikasiPage from '@/Pages/Kiosk/Antrian/VerifikasiPage'
import CashierPage from '@/Pages/Kiosk/Antrian/CashierPage'
import PasienPage from '@/Pages/Kiosk/Antrian/PasienPage'


export default {
    components : {
        //MainPage,
        HeaderAntrian, 
        CardButton,
        SubMainJkn,
        SubMainBpjs,
        SubMainUmum,
        BookingPage,
        VerifikasiPage,
        CashierPage,
        PasienPage,
    },    
    data() {
        return {
            isLoading : false,
            mainText : 'RS KARYA PRIMA PERKASA',
            subMainText : "Jakarta",
            myTagLine : "membangun masyarakat sehat dan kuat"
        }
    },
    computed : {
        ...mapGetters(['errors']),
    }, 
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('kiosk',['SET_PASIEN','CLR_DATA']),
        ...mapActions('kiosk',['jknAntrianData','pasienData','listUnitPoli']),

        showMainPage(){
            // this.CLR_ERRORS();
            this.retrievePoli();
            UIkit.switcher('#switcherAntrian').show(0);
        },

        janjiKonsulClicked(){
            this.retrievePoli();
            UIkit.switcher('#switcherAntrian').show(2);
        },
        verifikasiClicked(){
            this.CLR_ERRORS();
            this.retrievePoli();
            UIkit.switcher('#switcherAntrian').show(3);
        },

        antrianKasirClicked(){
            this.CLR_ERRORS();
            UIkit.switcher('#switcherAntrian').show(4);
            this.$refs.cashierPage.ambilNoAntrian();
        },        

        pasienAsuransiClicked(){
            // this.CLR_ERRORS();
            this.retrievePoli();
            UIkit.switcher('#switcherAntrian').show(2);
        },

        pasienBaruClicked(){
            UIkit.switcher('#switcherAntrian').show(1);
        },

        retrievePoli(){
            this.CLR_ERRORS();
            this.listUnitPoli().then((response)=>{
                if (response.success == true) {
                    this.isLoading = false;
                    console.log(response.data);
                }
                else {
                    this.isLoading = false;
                    alert(response.message);
                }
            })
        },
    }
}
</script>
<style>
.card-button-container {
    padding:1em;
}

.radio-text {
    color:black; 
    font-size:18px;
    font-weight: 500;
}

.radio-subtext {
    color:#333; 
    font-size:14px;
    font-weight: 350;
    padding:0.2em 0.2em 0.2em 1.5em; 
    margin:0;
}

#switcherBooking {
    border:none;
}

#switcherBooking a {
    margin:0;
    padding :1em;
    display: block;
    background-color: whitesmoke;
}

#switcherBooking a p {
    color: #333;
}

#switcherBooking li.uk-active > a {
    background-color: #cc0202;
}

#switcherBooking li.uk-active > a > p {
    color : #fff;
}

.tab-text {
    color:black; 
    font-size:18px;
    font-weight: 500;
    padding:0; 
    margin:0;
}

.tab-subtext {
    color:#333; 
    font-size:14px;
    font-weight: 350;
    padding:0; 
    margin:0;
}

.input-text-jkn {
    border:none;
    border-bottom:2px solid #cc0202;
}

.button-jkn-primer {
    border :2px solid #cc0202;
    font-weight: 500;
    background-color: #cc0202;
    color: #fff;
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

.modal-title {
    padding:0;
    margin:0;
    font-weight: 500;
}

.modal-subtitle {
    padding:0;
    margin:0;
    font-size: 14px;
    font-weight: 500;
}

div.info-container {
    padding:0.2em 0.2em 1em 0.2em;
}

p.title-info {
    font-size: 14px;
    font-weight: 500;
    padding:0;
    margin:0;
    color: #000;
}

p.content-info {
    font-size: 14px;
    padding:0;
    margin:0;
    color: #000;
}

.modal-jadwal-button {
    border:none;
    font-weight: 500;
    background-color: #fff;    
}

.modal-jadwal-button-primary {
    border:none;
    font-weight: 500;
    color:white;
    background-color: #cc0202;    
}

.modal-jadwal-button:hover {
    color: #000;
    border:2px solid #000;
}

.card-button {
    padding : 1em;
}

a.card-button-text {
    display:block; 
    text-decoration:none;
    cursor: pointer; 
    color:#000;
}

.card-button-title {
    padding:0;
    margin:0;
    font-size: 1.5em;
    font-weight: 500;
}

.card-button-subtitle {
    padding:0;
    margin:0;
    font-size: 0.8em;
    color:#000;
}

div.card-button:hover {
    background-color: #cc0202;
}

div.card-button:hover > a {
    color: #fff;
}

div.card-button:hover > div > a > p {
    color: #fff;
}

.main-text {
    padding:0;
    margin:0;
    color:black;
    font-weight: 500;
}

.sub-main-text {
    padding:0;
    margin:0;
    color:black;
}

.tag-line {
    padding:0;
    font-size:12px;
    font-style: italic;
    margin:0;
    color:#3f3f3f;
}

.jadwal-info {
    background-color: #cc0202;
    color:white;
    border-radius: 20px;
    font-size: 12px;
    padding:0.2em 0.5em 0.2em 0.5em;
    margin:0.1em;
}
</style>