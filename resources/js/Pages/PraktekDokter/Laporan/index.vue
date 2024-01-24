<template>
  <div class="uk-container uk-container-large" style="padding:1em 2em;">
      <header-page title="LAPORAN" subTitle="periode oktober"></header-page>
      <div style="margin-top:1em;">
        <tab-switcher-table
          class="uk-margin-bottom uk-grid uk-width-1-1@l uk-width-2-1@m uk-width-3-1@s"
          :tabList="tabList" style="margin-top: 1em;margin-left:0;"
        >
          <!-- <template v-slot:tabPanel-1> 
            <Pendapatan/>
          </template>

          <template v-slot:tabPanel-2> 
            <Kunjungan/>
          </template>

          <template v-slot:tabPanel-3> 
            <LaporanCatatanKas/>
          </template> -->

        </tab-switcher-table>
        <router-view></router-view>
      </div>
        <!-- <div class="uk-card" style="padding-right: 15px; margin: 0; border-radius: 10px; min-height: 400px;"> -->
          <!-- <div style="padding: 0; margin-top: 65px;"> -->
            

            <!-- <Monitoring
            /> -->
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import LaporanCatatanKas from './tabs/LaporanCatatanKas/index.vue';
import Kunjungan from './tabs/Kunjungan/index.vue';
import Pendapatan from './tabs/Pendapatan/index.vue';
// import TabSwitcherTable from './components/tabSwitcherTable.vue';
import BaseTablePraktek from '../components/BaseTablePraktek/index.vue';
import TabSwitcherTable from '@/Pages/PraktekDokter/LayananDokter/components/tabSwitcherTable.vue';

export default {
    components : {
        headerPage,
        BaseTablePraktek,
        TabSwitcherTable,
        LaporanCatatanKas,
        Kunjungan,
        Pendapatan,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            // tabList: ["Pendapatan", "Kunjungan", "Catatan Kas"],
            tabList: [
              { title : "Pendapatan", linkName : 'lapPendapatanPraktekDokter' },
              { title : "Kunjungan", linkName : 'lapKunjunganPraktekDokter' },  
              { title : "Pencatatan Kas", linkName : 'lapKasPraktekDokter' },
            ],
        }
    },
    mounted() {
        this.getData();
    },

    methods : {
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