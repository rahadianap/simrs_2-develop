<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">        
        <ul id="switcherRegistrasi" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List Registrasi</a></li>
            <li><a href="#">Form Registrasi</a></li>
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-registrasi ref="listRegistrasi" 
                    :addFunction="addRegistrasi" 
                    :editFunction="updateRegistrasi"
                ></list-registrasi>
            </li>
            <li style="padding:0;margin:0;">
                <form-registrasi ref="formRegistrasi" 
                    v-on:closed="formClosed" 
                    v-on:succeed="formClosed"
                    v-on:redirectMainTab="redirectMainTab" 
                    v-on:redirectPatientTab="pasienListShow">
                </form-registrasi>
            </li>
        </ul>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import ListRegistrasi from '@/Pages/Penunjang/Radiologi/components/ListRegistrasi.vue';
import FormRegistrasi from '@/Pages/Penunjang/Radiologi/components/FormRegistrasi.vue';

export default {
    components : {
        headerPage,
        ListRegistrasi,
        FormRegistrasi,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        addRegistrasi(){
            this.CLR_ERRORS();
            this.$refs.formRegistrasi.newRegistrasi(); 
            UIkit.switcher('#switcherRegistrasi').show(1);
        },

        updateRegistrasi(data){
            this.CLR_ERRORS();
            this.$refs.formRegistrasi.editRadiologi(data); 
            UIkit.switcher('#switcherRegistrasi').show(1);       
        },

        redirectMainTab(){
            this.$refs.listRegistrasi.refreshTable();
            UIkit.switcher('#switcherRegistrasi').show(0);
        },

        pasienListShow(data){
            UIkit.switcher('#switcherRegistrasi').show(1);
        },

        registrationFormShow(data){
            this.$refs.formRegistrasi.refreshPasien(data.pasien_id);
            UIkit.switcher('#switcherRegistrasi').show(1);
        },
        
        formClosed(){
            this.$refs.listRegistrasi.refreshTable();
            UIkit.switcher('#switcherRegistrasi').show(0);
        },

        approveDistribusi(data) {
            this.$refs.approveDistribusi.refreshData(data);
            UIkit.switcher('#switcherRegistrasi').show(2);
        }
    }

}
</script>