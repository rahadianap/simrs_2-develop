<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="Cetak PDF" subTitle="List Laporan Cetak IHS"></header-page>
        <!-- <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div> -->
        <div style="margin-top:1em;">
            <div class="uk-card" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                <div class="uk-width-auto header" 
                :class="{ 'header--hidden': !showNavbar }"
                style="position: sticky;"
                >
                    <!-- <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Cetakan/Laporan</label> -->
                    <div>
                        <div class="uk-grid uk-justify-center uk-form-controls">
                            <select class="uk-form-medium dropdown" style="width:800px;margin-left:auto;margin-right:auto;z-index:100;" @change="selectJnsCetakan($event)">
                                <option value="Template Cetak">Template</option>
                                <option value="Cetakan Tracer">Cetakan Tracer</option>
                                <option value="Cetakan Label">Cetakan Label</option>
                                <option value="Cetakan Riwayat">Cetakan Riwayat</option>
                                <option value="Cetakan In Out">Cetakan In Out</option>
                                <option value="Cetakan Form Inap">Cetakan Form Inap</option>
                                <option value="Cetakan Waktu Tunggu">Cetakan Waktu Tunggu</option>
                                <option value="Cetakan Harian Kasir Rajal">Cetakan Harian Kasir Rawat Jalan</option>
                                <option value="Cetakan Harian Kasir Ranap">Cetakan Harian Kasir Rawat Inap</option>
                                <option value="Cetakan Laporan Pendapatan Rajal">Cetakan Laporan Pendapatan Rawat Jalan</option>
                                <option value="Cetakan Laporan Pendapatan Ranap">Cetakan Laporan Pendapatan Rawat Inap</option>
                                <option value="Cetakan Marketing Per Payer">Cetakan Marketing Per Payer</option>
                                <option value="Cetakan Biaya Perawatan">Cetakan Biaya Perawatan</option>
                                <option value="Cetakan Kwitansi">Cetakan Kwitansi</option>
                                <option value="Cetakan Gelang Dewasa">Cetakan Gelang Dewasa</option>
                                <option value="Cetakan Formulir Informasi Pasien">Cetakan Formulir Informasi Pasien</option>
                                <option value="Cetakan Uang Muka Pasien">Cetakan Uang Muka Pasien</option>
                                <option value="Laporan Pasien Anti HIV">Laporan Pasien Anti HIV</option>
                                <option value="Laporan Pasien Rujukan Dari Luar">Laporan Pasien Rujukan Dari Luar</option>
                                <option value="Laporan Pendapatan Farmasi">Laporan Pendapatan Farmasi</option>
                                <option value="Laporan Penggunaan Obat Karyawan">Laporan Penggunaan Obat Karyawan</option>
                                <option value="Laporan Jumlah Resep">Laporan Jumlah Resep</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div style="margin-top:1em;" v-show="templateCetak">
                    <TemplateCetak ref="templateCetak"></TemplateCetak>
                </div>
                <div style="margin-top:1em;" v-show="cetakanTracer">
                    <CetakTracer></CetakTracer>
                </div>
                <div style="margin-top:1em;" v-show="cetakanLabel">
                    <CetakLabel></CetakLabel>
                </div>
                <div style="margin-top:1em;" v-show="cetakanRiwayat">
                    <CetakRiwayat></CetakRiwayat>
                </div>
                <div style="margin-top:1em;" v-show="cetakanInOut">
                    <CetakInOut></CetakInOut>
                </div>
                <div style="margin-top:1em;" v-show="cetakanFromInap">
                    <CetakFormInap></CetakFormInap>
                </div>
                <div style="margin-top:1em;" v-show="cetakWaktuTunggu">
                    <CetakWaktuTunggu></CetakWaktuTunggu>
                </div>
                <div style="margin-top:1em" v-show="cetakKasirRajal">
                    <CetakKasirRajal></CetakKasirRajal>
                </div>
                <div style="margin-top:1em" v-show="cetakKasirRanap">
                    <CetakKasirRanap></CetakKasirRanap>
                </div>
                <div style="margin-top:1em"  v-show="cetakLaporanRajal">
                    <CetakLaporanRajal></CetakLaporanRajal>
                </div>
                <div style="margin-top:1em"  v-show="cetakLaporanRanap">
                    <CetakLaporanRanap></CetakLaporanRanap>
                </div>
                <div style="margin-top:1em;" v-show="cetakMarketingPayer">
                    <CetakMarketingPayer></CetakMarketingPayer>
                </div>
                <div style="margin-top:1em;" v-show="cetakanBiayaPerawatan">
                    <CetakBiayaPerawatan></CetakBiayaPerawatan>
                </div>
                <div style="margin-top:1em;" v-show="cetakanKwitansi">
                    <CetakKwitansi></CetakKwitansi>
                </div>
                <div style="margin-top:1em;" v-show="cetakanGelangDewasa">
                    <CetakGelangDewasa></CetakGelangDewasa>
                </div>
                <div style="margin-top:1em;" v-show="cetakanFormulirInformasiPasien">
                    <CetakFormulirInformasiPasien></CetakFormulirInformasiPasien>
                </div>
                <div style="margin-top:1em;" v-show="cetakanUangMukaPasien">
                    <CetakUangMukaPasien></CetakUangMukaPasien>
                </div>
                <div style="margin-top:1em;" v-show="lapPasienAntiHIV">
                    <LapRekapPasienAntiHIV></LapRekapPasienAntiHIV>
                </div>
                <div style="margin-top:1em;" v-show="lapPasienRujukanLuar">
                    <LapRekapPasienRujukanLuar></LapRekapPasienRujukanLuar>
                </div>
                <div style="margin-top:1em;" v-show="lapPendapatanFarmasi">
                    <LapPendapatanFarmasi></LapPendapatanFarmasi>
                </div>
                <div style="margin-top:1em;" v-show="lapPenggunaanObatKaryawan">
                    <LapPenggunaanObatKaryawan></LapPenggunaanObatKaryawan>
                </div>
                <div style="margin-top:1em;" v-show="lapJumlahResep">
                    <LapJumlahResep></LapJumlahResep>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import TemplateCetak from './components/template.vue';
    import CetakTracer from './components/cetakTracer.vue';
    import CetakLabel from './components/cetakRegistrationLabel.vue';
    import CetakRiwayat from './components/cetakRiwayatKlinik.vue';
    import CetakInOut from './components/cetakKeluarMasuk.vue';
    import CetakFormInap from './components/cetakFormRawatInap.vue';
    import CetakWaktuTunggu from './components/cetakWaktuTunggu.vue';
    import CetakKasirRajal from './components/cetakKasirRajal.vue'
    import CetakKasirRanap from './components/cetakKasirRanap.vue';
    import CetakLaporanRajal from './components/cetakLaporanRajal.vue';
    import CetakLaporanRanap from './components/cetakLaporanRanap.vue';
    import CetakMarketingPayer from './components/cetakMarketingPayer.vue';
    import CetakKwitansi from './components/cetakKwitansi.vue';
    import CetakBiayaPerawatan from './components/cetakBiayaPerawatan.vue';
    import CetakGelangDewasa from './components/cetakGelangDewasa.vue';
    import CetakFormulirInformasiPasien from './components/cetakFormulirInformasiPasien.vue';
    import CetakUangMukaPasien from './components/cetakUangMukaPasien.vue';
    import LapRekapPasienAntiHIV from './components/laporanRekapPasienAntiHIV.vue';
    import LapRekapPasienRujukanLuar from './components/laporanRekapPasienRujukanLuar.vue';
    import LapPendapatanFarmasi from './components/laporanPendapatanFarmasi.vue';
    import LapPenggunaanObatKaryawan from './components/laporanPenggunaanObatKaryawan.vue';
    import LapJumlahResep from './components/laporanJumlahResep.vue';
    
    export default {
        components : {
            headerPage,
            TemplateCetak,
            CetakTracer,
            CetakLabel,
            CetakRiwayat,
            CetakInOut,
            CetakFormInap,
            CetakWaktuTunggu,
            CetakKasirRajal,
            CetakKasirRanap,
            CetakLaporanRajal,
            CetakLaporanRanap,
            CetakMarketingPayer,
            CetakKwitansi,
            CetakBiayaPerawatan,
            CetakGelangDewasa,
            CetakFormulirInformasiPasien,
            CetakUangMukaPasien,
            LapRekapPasienAntiHIV,
            LapRekapPasienRujukanLuar,
            LapPendapatanFarmasi,
            LapPenggunaanObatKaryawan,
            LapJumlahResep,
        },
        data() {
            return {
                showNavbar: true,
                lastScrollPosition: 0,
                templateCetak : true,
                cetakanTracer : false,
                cetakanLabel : false,
                cetakanRiwayat : false,
                cetakanInOut : false,
                cetakanFromInap : false,
                cetakWaktuTunggu : false,
                cetakKasirRajal : false,
                cetakKasirRanap : false,
                cetakLaporanRajal : false,
                cetakLaporanRanap : false,
                cetakMarketingPayer : false,
                cetakanBiayaPerawatan : false,
                cetakanKwitansi : false,
                cetakanGelangDewasa : false,
                cetakanFormulirInformasiPasien : false,
                cetakanUangMukaPasien : false,
                lapPasienAntiHIV : false,
                lapPasienRujukanLuar : false,
                lapPendapatanFarmasi : false,
                lapPenggunaanObatKaryawan : false,
                lapJumlahResep : false,
            }
        },
        computed : {
            ...mapGetters(['errors']),
        },
        mounted () {
            window.addEventListener('scroll', this.onScroll)
        },
        beforeDestroy () {
            window.removeEventListener('scroll', this.onScroll)
        },
    
        methods : {
            hideCetakan(data){
                this.templateCetak = data;
                this.cetakanTracer = data;
                this.cetakanLabel = data;
                this.cetakanRiwayat = data;
                this.cetakanInOut = data;
                this.cetakanFromInap = data;
                this.cetakWaktuTunggu = data;
                this.cetakKasirRajal = data;
                this.cetakKasirRanap = data;
                this.cetakLaporanRajal = data;
                this.cetakLaporanRanap = data;
                this.cetakMarketingPayer = data;
                this.cetakanBiayaPerawatan = data;
                this.cetakanKwitansi = data;
                this.cetakanGelangDewasa = data;
                this.cetakanFormulirInformasiPasien = data;
                this.cetakanUangMukaPasien = data;
                this.lapPasienAntiHIV = data;
                this.lapPasienRujukanLuar = data;
                this.lapPendapatanFarmasi = data;
                this.lapPenggunaanObatKaryawan = data;
                this.lapJumlahResep = data;
            },

            selectJnsCetakan(event) {
                this.hideCetakan(false);
                let jnsCetakan = event.target.value;
                const cetakanMap = {
                    "Template Cetak": "templateCetak",
                    "Cetakan Tracer": "cetakanTracer",
                    "Cetakan Label": "cetakanLabel",
                    "Cetakan Riwayat": "cetakanRiwayat",
                    "Cetakan In Out": "cetakanInOut",
                    "Cetakan Form Inap": "cetakanFromInap",
                    "Cetakan Waktu Tunggu": "cetakWaktuTunggu",
                    "Cetakan Harian Kasir Rajal": "cetakKasirRajal",
                    "Cetakan Harian Kasir Ranap": "cetakKasirRanap",
                    "Cetakan Laporan Pendapatan Rajal": "cetakLaporanRajal",
                    "Cetakan Laporan Pendapatan Ranap": "cetakLaporanRanap",
                    "Cetakan Marketing Per Payer": "cetakMarketingPayer",
                    "Cetakan Biaya Perawatan": "cetakanBiayaPerawatan",
                    "Cetakan Kwitansi": "cetakanKwitansi",
                    "Cetakan Gelang Dewasa": "cetakanGelangDewasa",
                    "Cetakan Formulir Informasi Pasien": "cetakanFormulirInformasiPasien",
                    "Cetakan Uang Muka Pasien": "cetakanUangMukaPasien",
                    "Laporan Pasien Anti HIV": "lapPasienAntiHIV",
                    "Laporan Pasien Rujukan Dari Luar": "lapPasienRujukanLuar",
                    "Laporan Pendapatan Farmasi": "lapPendapatanFarmasi",
                    "Laporan Jumlah Resep": "lapJumlahResep",
                    "Laporan Penggunaan Obat Karyawan": "lapPenggunaanObatKaryawan",
                };

                if (cetakanMap.hasOwnProperty(jnsCetakan)) {
                    this[cetakanMap[jnsCetakan]] = true;
                }
            },

            onScroll () {
                const currentScrollPosition = window.pageYOffset || document.documentElement.scrollTop
                if (currentScrollPosition < 0) {
                    return
                }
                // Stop executing this function if the difference between
                // current scroll position and last scroll position is less than some offset
                if (Math.abs(currentScrollPosition - this.lastScrollPosition) < 60) {
                    return
                }
                this.showNavbar = currentScrollPosition < this.lastScrollPosition
                this.lastScrollPosition = currentScrollPosition
            }
        
        }
    }
    </script>

<style>
.show {
  position: sticky;
  top: 10px;
  z-index: 100;
}

.dropdown {
        background-color: var(--color-primary);
        border: 2px solid var(--color-black);
        border-radius: 6px;
        box-shadow: inset 0 2px 0 var(--color-secondary-light),
                    3px 3px 0 var(--color-black);
        cursor: pointer;
        font-family: inherit;
        font-size: 16px;
        font-weight: bold;
        color: var(--color-black);
        outline: none;
        padding: 0.6rem 1rem;        
        :focus {
            text-decoration: underline;
        }
        
        :active {
            background-color: var(--color-secondary-dark);
            box-shadow: inset 0 2px 0 var(--color-secondary),
                        1px 1px 0 var(--color-black);
            text-decoration: none;
        }
    } 

.header {
	position: sticky;
	top: 10;
	z-index: 100;
    margin-left: 0;
    margin-right: 0;
    transform: translate3d(0, 0, 0);
    transition: 0.1s all ease-out;
}
.header .header--hidden {
    box-shadow: none;
  transform: translate3d(0, -100%, 0);
}


</style>