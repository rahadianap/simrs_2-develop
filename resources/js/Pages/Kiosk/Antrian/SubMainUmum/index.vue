<template>
    <div>
        <ul id="switcherAntrianUmum" class="uk-subnav uk-subnav-pill uk-hidden1" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">Pilih Dokter</a></li>
            <li><a href="#">Data Pasien</a></li>
            <li><a href="#">Selesai</a></li>
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <jadwal-dokter ref="jadwalDokter" v-on:returnMainPage="returnMain"></jadwal-dokter>
            </li>
            <li style="padding:0;margin:0;">
                <pasien-umum ref="pasienUmum" v-on:returnMainPage="returnMain"></pasien-umum>
            </li>
            <li style="padding:0;margin:0;"></li>
        </ul>
    </div>
</template>

<script>
import JadwalDokter from '@/Pages/Kiosk/Antrian/SubMainUmum/JadwalDokter.vue'
import PasienUmum from '@/Pages/Kiosk/Antrian/SubMainUmum/PasienUmum.vue'

export default {
    components : {
        JadwalDokter,PasienUmum
    },
    methods : {
        returnMain() {
            this.$emit('returnMainPage',true);
        }
    }
}
</script>