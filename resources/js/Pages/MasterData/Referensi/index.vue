<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="REFERENSI APLIKASI" subTitle="pengaturan data referensi aplikasi"></header-page>
        <div style="padding:1em 0 1em 0;" class="uk-container">
            <div style="padding:0; margin-top:1em; border-radius:10px;min-height:400px;">
                <!-- <div> -->
                    <ul uk-accordion="multiple:false" class="ref-accordion" style="width:100%;padding:0;margin:0;background-color:whitesmoke;">
                        <li class="uk-open" style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#" >Agama</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refAgama" :dataRef = "agamaRefs"></sub-referensi>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Pendidikan</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refPendidikan" :dataRef = "pendidikanRefs"></sub-referensi>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Pekerjaan</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refPekerjaan" :dataRef = "pekerjaanRefs"></sub-referensi>
                            </div>
                        </li>   
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Cara Registrasi</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refCaraRegistrasi" :dataRef = "caraRegistrasiRefs"></sub-referensi>
                            </div>
                        </li>  
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Status Perkawinan</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refStatusKawin" :dataRef = "statusKawinRefs"></sub-referensi>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#" >Hubungan Keluarga</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refHubKeluarga" :dataRef = "hubKeluargaRefs"></sub-referensi>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Jenis Penjamin</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-ref-jenis-penjamin ref="refJenisPenjamin" :dataRef = "jenisPenjaminRefs"></sub-ref-jenis-penjamin>
                            </div>
                        </li>                        
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Asal Pasien</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-ref-asal-pasien ref="refAsalPasien" :dataRef = "asalPasienRefs"></sub-ref-asal-pasien>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Jenis Alergi</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refJenisAlergi" :dataRef = "jenisAlergiRefs"></sub-referensi>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Suku Bangsa</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refSukuBangsa" :dataRef = "sukuBangsaRefs"></sub-referensi>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Kebangsaan</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refKebangsaan" :dataRef = "kebangsaanRefs"></sub-referensi>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">STATUS BED INAP</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-ref-status-bed ref="refStatusBedInap" :dataRef = "statusBedInapRefs"></sub-ref-status-bed>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#" >Cara Pulang</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refCaraPulang" :dataRef = "caraPulangRefs"></sub-referensi>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#" >Status Pulang</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-ref-status-pulang ref="refStatusPulang" :dataRef = "statusPulangRefs"></sub-ref-status-pulang>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#" >Kasus Igd</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refKasusIgd" :dataRef = "kasusIgdRefs"></sub-referensi>
                            </div>
                        </li>
                        <li style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#" >Jenis Pelayanan</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <sub-referensi ref="refJenisPelayanan" :allowEdit="false" :dataRef = "jenisPelayananRefs"></sub-referensi>
                            </div>
                        </li>
 
                    </ul>
                <!-- </div> -->
                <!-- <div>
                    <ul uk-accordion="multiple:false" class="ref-accordion" style="width:100%;">
                        <li class="uk-box-shadow-small">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-medium" href="#">STATUS BED INAP</a>
                            <div class="uk-accordion-content" style="margin:0;padding:0.5em;">                                            
                                <sub-ref-status-bed ref="refStatusBedInap" :dataRef = "statusBedInapRefs"></sub-ref-status-bed>
                            </div>
                        </li>
                        <li class="uk-box-shadow-small" style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-medium" href="#" >Cara Pulang</a>
                            <div class="uk-accordion-content" style="margin:0;padding:0.5em;">                                            
                                <sub-referensi ref="refCaraPulang" :dataRef = "caraPulangRefs"></sub-referensi>
                            </div>
                        </li>
                        <li class="uk-box-shadow-small" style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-medium" href="#" >Status Pulang</a>
                            <div class="uk-accordion-content" style="margin:0;padding:0.5em;">                                            
                                <sub-ref-status-pulang ref="refStatusPulang" :dataRef = "statusPulangRefs"></sub-ref-status-pulang>
                            </div>
                        </li>
                        <li class="uk-box-shadow-small" style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-medium" href="#" >Kasus Igd</a>
                            <div class="uk-accordion-content" style="margin:0;padding:0.5em;">                                            
                                <sub-referensi ref="refKasusIgd" :dataRef = "kasusIgdRefs"></sub-referensi>
                            </div>
                        </li>
                        <li class="uk-box-shadow-small" style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-medium" href="#" >Jenis Pelayanan</a>
                            <div class="uk-accordion-content" style="margin:0;padding:0.5em;">                                            
                                <sub-referensi ref="refJenisPelayanan" :allowEdit="false" :dataRef = "jenisPelayananRefs"></sub-referensi>
                            </div>
                        </li>
                    </ul>
                </div>                 -->

                <!-- <div class="uk-card uk-card-default">
                    referensi status kawin
                </div>
                <div class="uk-card uk-card-default">
                    referensi hubungan keluarga
                </div>
                <div class="uk-card uk-card-default">
                    referensi status suku bangsa
                </div>
                <div class="uk-card uk-card-default">
                    referensi status kewarganegaraan
                </div>
                <div class="uk-card uk-card-default">
                    jenis pelayanan
                </div>
                <div class="uk-card uk-card-default">
                    kasus igd
                </div> -->

            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import SubRefJenisPenjamin from '@/Pages/MasterData/Referensi/components/RefJenisPenjamin.vue';
import SubRefStatusBed from '@/Pages/MasterData/Referensi/components/RefStatusBed.vue';
import SubRefAsalPasien from'@/Pages/MasterData/Referensi/components/RefAsalPasien.vue';
import SubRefStatusPulang from'@/Pages/MasterData/Referensi/components/RefStatusPulang.vue';
import SubReferensi from '@/Pages/MasterData/Referensi/components/SubReferensi.vue';

export default {
    components : {
        headerPage,
        SubRefJenisPenjamin,
        SubRefStatusBed,
        SubRefAsalPasien,
        SubRefStatusPulang,
        SubReferensi
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',[
            'agamaRefs',
            'pekerjaanRefs',
            'pendidikanRefs',
            'jenisPenjaminRefs',
            'hubKeluargaRefs',
            'statusKawinRefs',
            'sukuBangsaRefs',
            'kebangsaanRefs',
            'caraPulangRefs',
            'statusPulangRefs',
            'asalPasienRefs',
            'jenisAlergiRefs',
            'statusBedInapRefs',
            'kasusIgdRefs',
            'jenisPelayananRefs',
            'caraRegistrasiRefs',
        ]),
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('referensi',['listReferensi']),

        initialize(){
            this.listReferensi().then((response) => {
                if (response.success == true) { this.checkData(); }
                else { alert (response.message) }
            });
        },

        checkData(){
            this.$refs.refAgama.refreshData(this.agamaRefs);
            this.$refs.refPendidikan.refreshData(this.pendidikanRefs);
            this.$refs.refPekerjaan.refreshData(this.pekerjaanRefs);
            this.$refs.refJenisPenjamin.refreshData(this.jenisPenjaminRefs);
            this.$refs.refHubKeluarga.refreshData(this.hubKeluargaRefs);

            this.$refs.refStatusKawin.refreshData(this.statusKawinRefs);

            this.$refs.refStatusBedInap.refreshData(this.statusBedInapRefs);
            this.$refs.refCaraPulang.refreshData(this.caraPulangRefs);
            this.$refs.refCaraRegistrasi.refreshData(this.caraRegistrasiRefs);
            this.$refs.refAsalPasien.refreshData(this.asalPasienRefs);
            this.$refs.refJenisAlergi.refreshData(this.jenisAlergiRefs);
            this.$refs.refSukuBangsa.refreshData(this.sukuBangsaRefs);
            this.$refs.refKebangsaan.refreshData(this.kebangsaanRefs);

            this.$refs.refKasusIgd.refreshData(this.kasusIgdRefs);
            this.$refs.refStatusPulang.refreshData(this.statusPulangRefs);
            this.$refs.refJenisPelayanan.refreshData(this.jenisPelayananRefs);
        }

    }
}
</script>
<style>
.ref-accordion li {
    padding:0;
    margin:0;
    background-color:white;
    /* border-top:1px solid #cc0202; */
}
li.uk {
    margin:0;
    padding:0;
}

.ref-accordion li a.ref-accordion-title {
    padding: 1em;
    color: #cc0202;
    font-weight:500;
    font-size:14px;
    margin:0;
}

.ref-accordion li.uk-open a.ref-accordion-title {
    color: #fff;
    background-color:#cc0202;
}

a.ref-accordion-title {        
    display: block;
    padding:0 0 0 0; 
    font-size:1em; 
    font-weight:500;
    text-transform: uppercase;
}

div.ref-accordion-content {
    margin:0;
    padding:0.5em;
    background-color:whitesmoke;
}
</style>