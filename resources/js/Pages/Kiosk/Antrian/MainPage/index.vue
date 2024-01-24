<template>
    <div style="padding:1em;">
        <div>
            <header-antrian :mainText="mainText" :tagLine="myTagLine"></header-antrian>
        </div>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-1" style="padding:2em 0 1em 1em;">
                <h2 style="padding:0;margin:0;">Selamat Datang,</h2>
                <h5 style="padding:0;margin:0;">Silahkan pilih layanan dibawah ini </h5>
            </div>
            <div class="uk-child-width-1-3@m uk-child-width-1-2@s uk-grid-small" uk-grid style="padding:1em 0 1em 1em;" uk-height-match="target: > div > .card-button">
                <div class="card-button-container">
                    <card-button cardTitle="PASIEN UMUM" cardSubTitle="pendaftaran pasien umum (pribadi)" :fnCallback="pasienUmumClicked"></card-button>
                </div>
                <div class="card-button-container">
                    <card-button cardTitle="PASIEN ASURANSI / PERUSAHAAN" cardSubTitle="pendaftaran pasien asuransi/perusahaan non BPJS" :fnCallback="pasienAsuransiClicked"></card-button>
                </div>

                <div class="card-button-container">
                    <card-button cardTitle="PASIEN BPJS" cardSubTitle="pendaftaran pasien rujukan BPJS (JKN)"  :fnCallback="pasienBpjsClicked"></card-button>
                </div>

                <div class="card-button-container">
                    <card-button cardTitle="ANTRIAN JKN" cardSubTitle="konfirmasi kehadiran antrian JKN" :fnCallback="antrianJknClicked"></card-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import HeaderAntrian from '@/Pages/Kiosk/components/HeaderAntrian.vue'
import CardButton from '@/Pages/Kiosk/components/CardButton.vue'

export default {
    name : 'main-page',
    components : {
        HeaderAntrian,CardButton
    },
    data() {
        return {
            mainText : 'RS KARYA PRIMA PERKASA',
            subMainText : "Jakarta",
            myTagLine : "membangun masyarakat sehat dan kuat"
        }
    },

    methods : {
        antrianJknClicked(){

        },
        
        pasienBpjsClicked(){

        },

        pasienAsuransiClicked(){

        },

        pasienUmumClicked(){

        }
    }
}
</script>
<style>
.card-button-container {
    padding : 0.5em;
}

.card-button-container .card-button {
    padding : 1em;
}
</style>