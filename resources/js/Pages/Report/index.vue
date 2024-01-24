<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="LIST REPORT" subTitle="list report"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableReport"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <!-- CRUD REPORT -->
        <form-report ref="formReport" v-on:closed="refreshTable"></form-report>

        <!-- LABORATORIUM -->
        <report-pemeriksaan-lab ref="reportPemeriksaanLab" v-on:closed="refreshTable" v-on:showReport="showReportModal"></report-pemeriksaan-lab>
        <report-kunjungan-lab ref="reportKunjunganLab" v-on:closed="refreshTable" v-on:showReport="showReportModal"></report-kunjungan-lab>
        <report-skrining-penyakit ref="reportSkriningPenyakit" v-on:closed="refreshTable"></report-skrining-penyakit>
        <report-rujukan-lab ref="reportRujukanLab" v-on:closed="refreshTable"></report-rujukan-lab>
        <report-rujukan-detail-lab ref="reportRujukanDetailLab" v-on:closed="refreshTable"></report-rujukan-detail-lab>

        <!-- LOGISTIK -->
        <filter-penerimaan-barang-medis ref="filterPenerimaanBarangMedis" v-on:closed="refreshTable"></filter-penerimaan-barang-medis>
        <filter-penerimaan-barang-non-medis ref="filterPenerimaanBarangNonMedis" v-on:closed="refreshTable"></filter-penerimaan-barang-non-medis>

        <!-- CETAKAN -->
        <!-- <cetakan-gelang-dewasa ref="cetakanGelangDewasa" v-on:closed="refreshTable"></cetakan-gelang-dewasa> -->
        <!-- <cetakan-registrasi-label ref="cetakanRegistrasiLabel" v-on:closed="refreshTable"></cetakan-registrasi-label> -->
        <!-- <cetakan-ringkasan-masuk-keluar ref="cetakanRingkasanMasukKeluar" v-on:closed="refreshTable"></cetakan-ringkasan-masuk-keluar> -->
        <cetakan-form-penempatan-kelas-ri ref="cetakanFormPenempatanKelasRi" v-on:closed="refreshTable"></cetakan-form-penempatan-kelas-ri>
        <cetakan-form-informasi-pasien ref="cetakanFormInformasiPasien" v-on:closed="refreshTable"></cetakan-form-informasi-pasien>
        <!-- <cetakan-kwitansi ref="cetakanKwitansi" v-on:closed="refreshTable"></cetakan-kwitansi> -->
        <!-- <cetakan-uang-muka-pasien ref="cetakanUangMukaPasien" v-on:closed="refreshTable"></cetakan-uang-muka-pasien> -->
        <cetakan-pendaftaran-rajal ref="cetakanPendaftaranRajal" v-on:closed="refreshTable"></cetakan-pendaftaran-rajal>
        <!-- <cetakan-tracer ref="cetakanTracer" v-on:closed="refreshTable"></cetakan-tracer> -->

        <!-- AKUNTING -->
        <report-pendapatan-ranap ref="reportPendapatanRanap" v-on:closed="refreshTable"></report-pendapatan-ranap>
        <report-pendapatan-rajal ref="reportPendapatanRajal" v-on:closed="refreshTable"></report-pendapatan-rajal>
        <report-pendapatan-farmasi ref="reportPendapatanFarmasi" v-on:closed="refreshTable"></report-pendapatan-farmasi>
        <report-pendapatan-harian-kasir ref="reportPendapatanHarianKasir" v-on:closed="refreshTable"></report-pendapatan-harian-kasir>
        <report-harian-kasir-ranap ref="reportHarianKasirRanap" v-on:closed="refreshTable"></report-harian-kasir-ranap>
        <report-harian-kasir-rajal ref="reportHarianKasirRajal" v-on:closed="refreshTable"></report-harian-kasir-rajal>
        <report-marketing-per-payer ref="reportMarketingPerPayer" v-on:closed="refreshTable"></report-marketing-per-payer>
        
        <!-- FARMASI -->
        <report-pemakaian-obat ref="reportPemakaianObat" v-on:closed="refreshTable"></report-pemakaian-obat>
        <report-jumlah-resep ref="reportJumlahResep" v-on:closed="refreshTable"></report-jumlah-resep>
        <report-waktu-tunggu ref="reportWaktuTunggu" v-on:closed="refreshTable"></report-waktu-tunggu>

        <view-report ref="viewReport"></view-report>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';

import formReport from '@/Pages/Report/components/FormReport.vue';

import reportPemeriksaanLab from '@/Pages/Report/Laboratorium/reportPemeriksaanLab.vue';
import reportKunjunganLab from '@/Pages/Report/Laboratorium/reportKunjunganLab.vue';
import reportSkriningPenyakit from '@/Pages/Report/Laboratorium/reportSkriningPenyakit.vue';
import reportRujukanLab from '@/Pages/Report/Laboratorium/reportRujukanLab.vue';
import reportRujukanDetailLab from '@/Pages/Report/Laboratorium/reportRujukanDetailLab.vue';

import filterPenerimaanBarangMedis from '@/Pages/Report/Logistik/filterPenerimaanBarangMedis.vue';
import filterPenerimaanBarangNonMedis from '@/Pages/Report/Logistik/filterPenerimaanBarangNonMedis.vue';

import cetakanGelangDewasa from '@/Pages/Report/Cetakan/cetakanGelangDewasa.vue';
import cetakanRegistrasiLabel from '@/Pages/Report/Cetakan/cetakanRegistrasiLabel.vue';
import cetakanRingkasanMasukKeluar from '@/Pages/Report/Cetakan/cetakanRingkasanMasukKeluar.vue';
import cetakanFormInformasiPasien from '@/Pages/Report/Cetakan/cetakanFormInformasiPasien.vue';
import cetakanKwitansi from '@/Pages/Report/Cetakan/cetakanKwitansi.vue';
import cetakanUangMukaPasien from '@/Pages/Report/Cetakan/cetakanUangMukaPasien.vue';
import cetakanPendaftaranRajal from '@/Pages/Report/Cetakan/cetakanPendaftaranRajal.vue';
import cetakanTracer from '@/Pages/Report/Cetakan/cetakanTracer.vue';

import cetakanFormPenempatanKelasRi from '@/Pages/Report/RawatInap/cetakanFormPenempatanKelasRi.vue';

import reportHarianKasirRajal from '@/Pages/Report/Akunting/reportHarianKasirRajal.vue';
import reportHarianKasirRanap from '@/Pages/Report/Akunting/reportHarianKasirRanap.vue';
import reportMarketingPerPayer from '@/Pages/Report/Akunting/reportMarketingPerPayer.vue';
import reportPendapatanFarmasi from '@/Pages/Report/Akunting/reportPendapatanFarmasi.vue';
import reportPendapatanHarianKasir from '@/Pages/Report/Akunting/reportPendapatanHarianKasir.vue';
import reportPendapatanRajal from '@/Pages/Report/Akunting/reportPendapatanRajal.vue';
import reportPendapatanRanap from '@/Pages/Report/Akunting/reportPendapatanRanap.vue';

import reportJumlahResep from '@/Pages/Report/Farmasi/reportJumlahResep.vue';
import reportPemakaianObat from '@/Pages/Report/Farmasi/reportPemakaianObat.vue';
import reportWaktuTunggu from '@/Pages/Report/Farmasi/reportWaktuTunggu.vue';

import ViewReport from '@/Pages/Report/components/ViewReport.vue';


export default {
    components : {
        headerPage,
        BaseTable,
        formReport,
        reportPemeriksaanLab,
        reportKunjunganLab,
        reportSkriningPenyakit,
        reportRujukanLab,
        reportRujukanDetailLab,
        filterPenerimaanBarangMedis,
        filterPenerimaanBarangNonMedis,
        cetakanGelangDewasa,
        cetakanRegistrasiLabel,
        cetakanRingkasanMasukKeluar,
        cetakanFormPenempatanKelasRi,
        cetakanFormInformasiPasien,
        cetakanKwitansi,
        cetakanUangMukaPasien,
        reportHarianKasirRajal,
        reportHarianKasirRanap,
        reportMarketingPerPayer,
        reportPendapatanFarmasi,
        reportPendapatanHarianKasir,
        reportPendapatanRajal,
        reportPendapatanRanap,
        reportJumlahResep,
        reportPemakaianObat,
        reportWaktuTunggu,
        cetakanPendaftaranRajal,
        cetakanTracer,
        ViewReport,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'SIMPLE', 
            },
            tbColumns : [                
                { 
                    name : 'report_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Cetak', 'icon':'file-pdf','callback':this.filterReport },   
                    ],
                },
                { 
                    name : 'report_name', 
                    title : 'Nama Report', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'report_url', 
                    title : 'URL', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addReport },
            ],
            searchUrl : '/report'
         }
    },
    mounted() {
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addReport(){        
            this.CLR_ERRORS();
            this.$refs.formReport.newReport();
        },

        filterReport(data){   
            this.CLR_ERRORS();
            this.$refs[data.report_url].filterReport();
        },

        refreshTable() {
            this.$refs.tableReport.retrieveData();
        },
        
        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formReport.editReport(data.report_id);
        },

        showReportModal(data) {
            this.$refs.viewReport.showReport(data);
        }
    }
}
</script>