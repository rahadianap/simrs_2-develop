<template>
    <div style="padding:1em;" class="uk-container uk-container-large">
        <header-page title="MASTER PASIEN" subTitle="master data pasien dan detail info"></header-page>
        <div style="margin:1em 0 0 0;padding:0 1em 1em 1em; background-color:#fff;">
            <form-pasien ref="formPasien" urlBack="/master/pasien" v-on:formPatientClosed = "formClosed"></form-pasien>
        </div>
    </div>
</template>
<script>
import FormPasien from '@/Pages/MasterData/Pasien/components/FormPasien.vue';
import headerPage from '@/Components/HeaderPage.vue';
export default {
    props : {
        pasienId : { type:String, required:true, default:'baru' },
    },
    components : {
        FormPasien,headerPage
    },
    mounted(){
        this.initialize();
    },
    methods : {
        initialize() {
            if(this.pasienId == '' || this.pasienId == 'baru' || this.pasienId == null) {
                this.$refs.formPasien.newPasien();
            }
            else {
                this.$refs.formPasien.editPasien(this.pasienId);
            }            
        },

        formClosed(){
            this.$router.push(`/master/pasien/${pasienId}`);
        }
    }
}
</script>