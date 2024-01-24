<template>
    <div class="uk-container uk-container" style="padding:1em;">
        <!-- <header-page title="REFERENSI PRODUK" subTitle="master data referensi produk dan persediaan"></header-page> -->
        <div style="margin-top:0;">
            <div class="uk-card" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                <ul uk-tab style="padding:0;">
                    <!-- <li><a href="#" style="font-size:14px;font-weight:500;">GOLONGAN</a></li> -->
                    <li><a href="#" style="font-size:16px;font-weight:500;">PRODUK DARAH</a></li>
                    <li><a href="#" style="font-size:16px;font-weight:500;">ASAL DARAH</a></li>
                    
                </ul>
                <ul class="uk-switcher" style="padding:0;margin:0;">
                    <li>
                        <list-produk-darah></list-produk-darah>
                    </li>
                    <li>
                        <list-asal-darah></list-asal-darah>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';


import FormGolongan from '@/Pages/Persediaan/Referensi/components/FormGolongan.vue';
import FormKlasifikasi from '@/Pages/Persediaan/Referensi/components/FormKlasifikasi.vue';
import FormLabelObat from '@/Pages/Persediaan/Referensi/components/FormLabelObat.vue';
import FormSatuan from '@/Pages/Persediaan/Referensi/components/FormSatuan.vue';
import FormSediaan from '@/Pages/Persediaan/Referensi/components/FormSediaan.vue';

import ListProdukDarah from '@/Pages/Penunjang/BankDarah/Referensi/components/ListProdukDarah.vue';
import ListAsalDarah from '@/Pages/Penunjang/BankDarah/Referensi/components/ListAsalDarah.vue';

export default {
    components : {
        headerPage,
        FormGolongan,
        FormKlasifikasi,
        FormLabelObat,
        FormSatuan,
        FormSediaan,
        ListProdukDarah,
        ListAsalDarah,
        //BaseTable,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refProduk',[
            'golonganProdukRefs',
            'klasifikasiProdukRefs',
            'jenisProdukRefs',
            'labelObatRefs', 
            'satuanProdukRefs',
            'sediaanObatRefs',       
        ]),
    },



    mounted() {
        this.initialize();
    },
    methods :{
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('refProduk',['listRefProduk']),

        initialize(){
            // this.listRefProduk().then((response) => {
            //     if (response.success == true) { this.checkData(); }
            //     else { alert (response.message) }
            // });
        },

        checkData(){
            // this.$refs.refGolongan.refreshData(this.golonganProdukRefs);
            this.$refs.refKlasifikasi.refreshData(this.klasifikasiProdukRefs);       
            this.$refs.refLabelObat.refreshData(this.labelObatRefs);     
            this.$refs.refSatuan.refreshData(this.satuanProdukRefs);  
            this.$refs.refSediaan.refreshData(this.sediaanObatRefs); 
        }
    }
}
</script>