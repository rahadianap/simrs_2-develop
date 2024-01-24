<template>
  <div style="padding: 0.8rem" class="uk-padding-remove-top">
    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
      <p>{{ errors.invalid }}</p>
    </div>
    <div class="uk-margin-medium-top">
      <tab-switcher
        class="uk-width-1-1 uk-width-auto@m uk-margin-auto uk-margin-large-bottom uk-padding-small"
        :tabList="tabList"
        :jumlahBoxMonitoring1=" 10 "

      >
        <template v-slot:tabPanel-1> 
          <Summary
            judulKunjungan="Kunjungan Bulan ini"
            :jumlahKunjungan=summary.totalKunjungan
            judulPasienBaru="Pasien Baru"
            :jumlahPasienBaru=summary.totalPasienBaru
            judulPasienLama="Pasien Lama"
            :jumlahPasienLama=summary.totalPasienLama
            judulPendapatan="Pendapatan Bulan ini"
            judulKas="Kas Bulan ini"
            judulGenderPasien="Ringkasan Pasien"
            judulPaymentMethod="Ringkasan Payment Method"
            judulPenjamin="Ringkasan Penjamin"
          />
        </template>
        <template v-slot:tabPanel-2> 
          <Monitoring
            judulBoxMonitoring1=" Antrian "
            subJudulBoxMonitoring1=" Pasien dalam antrian "
            :jumlahBoxMonitoring1=monitoring.totalPasienAntri
            judulBoxMonitoring2=" Dilayani "
            subJudulBoxMonitoring2=" Pasien sedang dilayani "
            :jumlahBoxMonitoring2=monitoring.totalPasienDilayani
            judulBoxMonitoring3=" Selesai "
            subJudulBoxMonitoring3=" Pasien selesai berobat "
            :jumlahBoxMonitoring3=monitoring.totalPasienSelesai
          />

        </template>
      </tab-switcher>
    </div>
  </div>
</template>  

<script>
  import { mapGetters, mapActions, mapMutations } from 'vuex';
  import Summary from './tabs/Summary/index.vue';
  import Monitoring from './tabs/Monitoring/index.vue';
  import TabSwitcher from './components/tabSwitcher.vue'
     
  export default {
      components : {
        Summary,
        Monitoring,
        TabSwitcher
      },
      computed : {
          ...mapGetters(['errors']),
      },
      data() {
          return {
              summary: {
                totalKunjungan:0,
                totalPasienBaru:0,
                totalPasienLama:0,
              },
              
              monitoring: {
                totalPasienAntri:0,
                totalPasienDilayani:0,
                totalPasienSelesai:0,
              },

              tabList: ["Summary", "Monitoring"],
          }
      },
      mounted() {
          this.getData();
          this.initialize();
      },
  
      methods : {
        ...mapActions('praktekDokter',['dashboardSummaryKunjungan','dashboardMonitoringStatus']),

        initialize(){
          this.dashboardSummaryKunjungan().then((response) => {
              if (response.success == true) {
                this.summary.totalKunjungan = response.data.ttl_kunjungan;
                this.summary.totalPasienBaru = response.data.pasien_baru;
                this.summary.totalPasienLama = response.data.pasien_lama;
              } else { alert (response.message) }
          });
          this.dashboardMonitoringStatus().then((response) => {
              if (response.success == true) {
                this.monitoring.totalPasienAntri = response.data.antrian;
                this.monitoring.totalPasienDilayani = response.data.dilayani;
                this.monitoring.totalPasienSelesai = response.data.selesai;
              } else { alert (response.message) }
          });
        },

        getData() {
            // $axios.get('/assets/overview')
            // .then(res => {
            //     this.overview.totalAsetTerdata = res?.data?.data[0]?.totalasetterdata;
            //     this.overview.totalNilaiAset = res?.data?.data[0]?.totalnilaiaset;
            //     this.overview.totalLokasiTerdata = res?.data?.data[0]?.totallokasi;
            // })
        },
      }
  }
  </script>
    
<style>
    /* ----------- Non-Retina Screens ----------- */
@media screen 
  and (min-device-width: 1200px) 
  and (max-device-width: 1600px) 
  and (-webkit-min-device-pixel-ratio: 1) { 
}

/* ----------- Retina Screens ----------- */
@media screen 
  and (min-device-width: 1200px) 
  and (max-device-width: 1600px) 
  and (-webkit-min-device-pixel-ratio: 2)
  and (min-resolution: 192dpi) { 
} 
</style>
