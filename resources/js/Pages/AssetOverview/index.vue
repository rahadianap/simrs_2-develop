<template>
    <div class="uk-container uk-container-large" style="padding: 1em;">
      <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
        <p>{{ errors.invalid }}</p>
      </div>
      <div style="margin-top: 1em;">
        <div class="uk-card" style="padding-right: 15px; margin: 0; border-radius: 10px; min-height: 400px;">
          <div style="color: black; padding: 0;">
            <h3 class="uk-text-uppercase" style="font-weight: 500; margin: 0; padding: 0;">Overview Asset Management</h3>
            <p style="font-weight: 300; font-style: italic; margin: 0; padding: 0;">
              ikhtisar keseluruhan data tercatat
            </p>
          </div>
  
          <div class="uk-grid uk-child-width-1-3@l uk-child-width-1-2@m uk-child-width-1-1@s uk-child-width-1-1@xs" style="padding: 0; margin-top: 65px;" uk-grid>
            <Box1
              totalAsetTerdata="Total Aset Terdata"
              :value="overview.totalAsetTerdata"
            />
            <Box2
              totalNilaiAset="Total Nilai Aset"
              :value="overview.totalNilaiAset"
            />
            <Box3
              totalLokasiTerdata="Total Lokasi Terdata"
              :value="overview.totalLokasiTerdata"
            />
            <Box4
              totalBarangMasuk="Total Barang Masuk"
              :value="overview.totalBarangMasuk"
            />
            <Box5
              totalBarangKeluar="Total Barang Keluar"
              :value="overview.totalBarangKeluar"
            />
            <Box6
              totalBarangDigunakan="Total Barang Digunakan"
              :value="overview.totalBarangDigunakan"
            />
            <Box7
              totalPengaduan="Total Pengaduan Dibuat"
              :value="overview.totalPengaduan"
            />
            <Box8
              totalPengaduanProses="Pengaduan Diproses"
              :value="overview.totalPengaduanProses"
            />
            <Box9
              totalPengaduanSelesai="Pengaduan Selesai"
              :value="overview.totalPengaduanSelesai"
            />
            <Box10
              totalSparepart="Total Sparepart Terdata"
              :value="overview.totalSparepart"
            />
            <Box11
              kadaluarsa="Item Kadaluarsa Terdata"
              :value="overview.kadaluarsa"
            />
          </div>
        </div>
      </div>
    </div>
</template>  
  
<script>
    import { mapGetters } from 'vuex';
    import $axios from '@/Stores/httpReq';
    import Box1 from './component/Box1/index.vue';
    import Box2 from './component/Box2/index.vue';
    import Box3 from './component/Box3/index.vue';
    import Box4 from './component/Box4/index.vue';
    import Box5 from './component/Box5/index.vue';
    import Box6 from './component/Box6/index.vue';
    import Box7 from './component/Box7/index.vue';
    import Box8 from './component/Box8/index.vue';
    import Box9 from './component/Box9/index.vue';
    import Box10 from './component/Box10/index.vue';
    import Box11 from './component/Box11/index.vue';


    
    export default {
        components : {
            Box1,
            Box2,
            Box3,
            Box4,
            Box5,
            Box6,
            Box7,
            Box8,
            Box9,
            Box10,
            Box11
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                overview: {
                    totalAsetTerdata:0,
                    totalNilaiAset: 0,
                    totalLokasiTerdata: 0,
                    totalBarangMasuk: 0,
                    totalBarangKeluar: 0,
                    totalBarangDigunakan: 0,
                    totalPengaduan: 0,
                    totalPengaduanProses: 0,
                    totalPengaduanSelesai: 0,
                    totalSparepart: 0,
                    kadaluarsa: 0
                }
            }
        },
        mounted() {
            this.getData();
        },
    
        methods : {
            getData() {
                $axios.get('/assets/overview')
                .then(res => {
                    this.overview.totalAsetTerdata = res?.data?.data[0]?.totalasetterdata;
                    this.overview.totalNilaiAset = res?.data?.data[0]?.totalnilaiaset;
                    this.overview.totalLokasiTerdata = res?.data?.data[0]?.totallokasi;
                })
            },
        }
    }
    </script>