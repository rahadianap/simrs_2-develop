<template>
    <div ref="modalMasterPasien" class="uk-modal uk-modal-container" uk-modal="bg-close:false;" :id="modalId" style="padding:1em;">
        <div class="uk-modal-dialog uk-modal-body">
            <header-page title="REFERENSI APLIKASI" subTitle="pengaturan data referensi aplikasi"></header-page>
            <div style="margin-top:2em;">
                <ul uk-accordion="multiple:false" class="ref-accordion" style="width:100%;padding:0;margin:0;background-color:whitesmoke;">
                    <li class="uk-open" style="margin:0;padding:0;">
                        <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#" >Agama</a>
                        <div class="uk-accordion-content ref-accordion-content">                                            
                            <sub-referensi ref="refAgama" :dataRef = "agamaRefs" v-on:refUpdate="checkData"></sub-referensi>
                        </div>
                    </li>
                    <li style="margin:0;padding:0;">
                        <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Pendidikan</a>
                        <div class="uk-accordion-content ref-accordion-content">                                            
                            <sub-referensi ref="refPendidikan" :dataRef = "pendidikanRefs" v-on:refUpdate="checkData"></sub-referensi>
                        </div>
                    </li>
                    <li style="margin:0;padding:0;">
                        <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Pekerjaan</a>
                        <div class="uk-accordion-content ref-accordion-content">                                            
                            <sub-referensi ref="refPekerjaan" :dataRef = "pekerjaanRefs" v-on:refUpdate="checkData"></sub-referensi>
                        </div>
                    </li> 
                </ul>
            </div>
            <div style="margin-top:2em; text-align:right; ">
                <button class="uk-button uk-modal-close" @click="closeModalMaster" style="height:40px; border-radius:7px; border:1px solid #666;">Tutup</button>
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

// import SubDokter from'@/Pages/PraktekDokter/LayananDokter/tabs/MasterPasien/components/SubDokter.vue';
import SubReferensi from '@/Pages/MasterData/Referensi/components/SubReferensi.vue';

export default {
    name : 'master-data-praktek',
    components : {
        headerPage,
        SubRefJenisPenjamin,
        SubRefStatusBed,
        SubRefAsalPasien,
        SubRefStatusPulang,
        SubReferensi,
        // SubDokter,
    },

    data() {
        return {
            modalId : 'modalMasterData',
            isLoading : true,
            doctorLists : [],
        }
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
            // this.listReferensi().then((response) => {
            //     if (response.success == true) { this.checkData(); }
            //     else { alert (response.message) }
            // });
        },

        
        checkData(){
            this.$refs.refAgama.refreshData(this.agamaRefs);
            this.$refs.refPendidikan.refreshData(this.pendidikanRefs);
            this.$refs.refPekerjaan.refreshData(this.pekerjaanRefs);
            // this.$refs.refJenisPenjamin.refreshData(this.jenisPenjaminRefs);
            // this.$refs.refHubKeluarga.refreshData(this.hubKeluargaRefs);

            // this.$refs.refStatusKawin.refreshData(this.statusKawinRefs);

            // this.$refs.refStatusBedInap.refreshData(this.statusBedInapRefs);
            // this.$refs.refCaraPulang.refreshData(this.caraPulangRefs);
            // this.$refs.refCaraRegistrasi.refreshData(this.caraRegistrasiRefs);
            // this.$refs.refAsalPasien.refreshData(this.asalPasienRefs);
            // this.$refs.refJenisAlergi.refreshData(this.jenisAlergiRefs);
            // this.$refs.refSukuBangsa.refreshData(this.sukuBangsaRefs);
            // this.$refs.refKebangsaan.refreshData(this.kebangsaanRefs);

            // this.$refs.refKasusIgd.refreshData(this.kasusIgdRefs);
            // this.$refs.refStatusPulang.refreshData(this.statusPulangRefs);
            // this.$refs.refJenisPelayanan.refreshData(this.jenisPelayananRefs);
        },

        showModalMaster(){
            this.listReferensi().then((response) => {
                if (response.success == true) { this.checkData(); }
                else { alert (response.message) }
            });
            UIkit.modal(`#${this.modalId}`).show();
        },

        closeModalMaster() {
            this.listReferensi();
            this.$emit('formMasterClosed',true);
            UIkit.modal(`#${this.modalId}`).hide();
        },

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
    /* color: #cc0202; */
    font-weight:500;
    font-size:14px;
    margin:0;
}

.ref-accordion li.uk-open a.ref-accordion-title {
    color: #000;
    background-color:#cc0202;
    background-color:#ccc;
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