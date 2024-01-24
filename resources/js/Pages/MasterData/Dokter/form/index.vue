<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="DOKTER DAN SPESIALIS" subTitle="master data dokter dan spesialis"></header-page>
        <div style="margin-top:1em;background-color:#fff;">
            <form-dokter ref="formDokter"></form-dokter>
        </div>
    </div>
</template>
<script>
import FormDokter from '@/Pages/MasterData/Dokter/components/FormDokter.vue';
import headerPage from '@/Components/HeaderPage.vue';
export default {
    props : {
        dokterId : { type:String, required:true, default:'baru' },
    },
    components : {
        FormDokter,headerPage
    },
    mounted(){
        this.initialize();
    },
    methods : {

        initialize() {
            if(this.dokterId == '' || this.dokterId == 'baru' || this.dokterId == null) {
                this.$refs.formDokter.newDokter();
            }
            else {
                this.$refs.formDokter.editDokter(this.dokterId);
            }
        }
    }
}
</script>