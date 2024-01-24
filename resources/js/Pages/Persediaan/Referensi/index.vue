<template>
    <div class="uk-container uk-container" style="padding:1em;">
        <!-- <header-page title="REFERENSI PRODUK" subTitle="master data referensi produk dan persediaan"></header-page> -->
        <div style="margin-top:0;">
            <div class="uk-card" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                <ul uk-tab style="padding:0;">
                    <!-- <li><a href="#" style="font-size:14px;font-weight:500;">GOLONGAN</a></li> -->
                    <li><a href="#" style="font-size:16px;font-weight:500;">KLASIFIKASI</a></li>
                    <li><a href="#" style="font-size:16px;font-weight:500;">CARA PAKAI OBAT</a></li>
                    <li><a href="#" style="font-size:16px;font-weight:500;">UNIT SATUAN</a></li>
                    <li><a href="#" style="font-size:16px;font-weight:500;">SEDIAAN</a></li>
                    
                </ul>
                <ul class="uk-switcher" style="padding:0;margin:0;">
                    <!-- <li>
                        <form-golongan ref="refGolongan" :allowEdit="true" :dataRef = "golonganProdukRefs"></form-golongan>
                    </li> -->
                    <li>
                        <form-klasifikasi ref="refKlasifikasi" :allowEdit="true" :dataRef = "klasifikasiProdukRefs"></form-klasifikasi>
                    </li>
                    <li>
                        <form-label-obat ref="refLabelObat" :allowEdit="true" :dataRef = "labelObatRefs"></form-label-obat>
                    </li>
                    <li>
                        <form-satuan ref="refSatuan" :allowEdit="true" :dataRef = "satuanProdukRefs"></form-satuan>
                    </li>
                    <li>
                        <form-sediaan ref="refSediaan" :allowEdit="true" :dataRef = "sediaanObatRefs"></form-sediaan>
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

export default {
    components : {
        headerPage,
        FormGolongan,
        FormKlasifikasi,
        FormLabelObat,
        FormSatuan,
        FormSediaan,
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
            this.listRefProduk().then((response) => {
                if (response.success == true) { this.checkData(); }
                else { alert (response.message) }
            });
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