<template>
  <!-- <div class="uk-container uk-container-large" style="padding:1em;">
    <div>
      <div>
        <ul class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0.2em 0 0 0;margin:0;">
            <li><div><a href="#"><h5>Master Pasien</h5></a></div></li>
            <li style="padding:0;"><div><a href="#" @click="updateAntrian"><h5>Antrian Dokter</h5></a></div></li>
            <li style="padding:0;"><div><a href="#"><h5>Riwayat Pembayaran</h5></a></div></li>
        </ul>
        <ul class="uk-switcher">
            <li>
              <master-pasien></master-pasien>
            </li>
            <li>
              <antrian ref="listAntrian"></antrian>
            </li>
            <li></li>
        </ul>
      </div>  
    </div>
  </div> -->

    <div class="uk-container uk-container-large" style="padding:1em 2em;">
        <header-page title="LAYANAN DOKTER" subTitle="layanan praktek dokter mandiri"></header-page>
        <div style="margin-top:1em;">
            <tab-switcher-table
            class="uk-margin uk-grid uk-grid-small uk-width-1-1@m"
            :tabList="tabList" style="margin-top: 1em;">
            <!-- class="uk-width-11-12@lg uk-width-10-12@m uk-width-auto@m uk-margin-auto uk-margin-large-bottom uk-padding-small" -->

            <!-- <template v-slot:tabPanel-1> 
                <MasterPasien/>
            </template>

            <template v-slot:tabPanel-2> 
                <Antrian/>
            </template>

            <template v-slot:tabPanel-3> 
                <RiwayatPembayaran/>
            </template>-->

            </tab-switcher-table>
            
            <!-- <offCanvas
                isCanvasTitle="Elektronik Rekam Medis"
                canvasTitle="RIWAYAT ELEKTRONIK REKAM MEDIS"
            /> -->
            

          <!-- <master-pasien></master-pasien> -->
        </div>
        <div>
          <router-view></router-view>
        </div>
    </div>
</template>  
  
<script>
    import { mapGetters } from 'vuex';
    // import $axios from '@/Stores/httpReq';
    import headerPage from '@/Components/HeaderPage.vue';
    import TabSwitcherTable from './components/tabSwitcherTable.vue';
    import Antrian from './tabs/Antrian/index.vue';
    // import offCanvas from './components/offCanvas.vue';
    import MasterPasien from './tabs/MasterPasien/index.vue';
    import RiwayatPembayaran from './tabs/RiwayatPembayaran/index.vue';
    //import TabSwitcherTable from '@/Pages/PraktekDokter/Laporan/components/tabSwitcherTable.vue';
    
    export default {
        components : {
          headerPage,
          TabSwitcherTable,
          Antrian,
          // offCanvas,
          MasterPasien,
          RiwayatPembayaran,
          //TabSwitcherTable,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                tabList: [
                  { title : "Master Pasien", linkName : 'listPasienPraktekDokter' },
                  { title : "Antrian Dokter", linkName : 'listAntrianPraktekDokter' },  
                  { title : "Riwayat Pembayaran", linkName : 'listPembayaranPraktekDokter' },
                ],
            }
        },
        mounted() {
            this.getData();
        },
    
        methods : {
          updateAntrian(){
            this.$refs.listAntrian.submitFilter();
          },
            
          getData() {
               
          },
            // openOffCanvas1() {
            //   this.isForm = true; //membuka offcanvas yang berisi Form 
            // },
            // openOffCanvas2() {
            //   this.isDropdown = true; //membuka offcanvas yang berisi dropdown
            // },
        }
    }
    </script>