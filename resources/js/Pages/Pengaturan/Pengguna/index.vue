<template>
    <div class="uk-container uk-container-large" style="padding:1em; ">        
        <ul id="switcherUserManagement" class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0;margin:0;background-color: whitesmoke;">
            <li><div><a href="#"><h5>Manajemen Pengguna</h5></a></div></li>
            <li style="padding:0;"><div><a href="#"><h5>Undangan Bergabung</h5></a></div></li> 
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;background-color: #fff;">
            <li style="padding:0;margin:0;">                
                <user-lists></user-lists>
            </li>
            <li style="padding:0;margin:0;">
                <!-- <form-pengguna ref="formPengguna" v-on:closed="formClosed" v-on:succeed="formClosed"></form-pengguna> -->
                <invitation-lists></invitation-lists>
            </li>
        </ul>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
// import ListPengguna from '@/Pages/Pengaturan/Pengguna/components/ListPengguna.vue';
// import FormPengguna from '@/Pages/Pengaturan/Pengguna/components/FormPengguna.vue';
import UserLists from '@/Pages/Pengaturan/Pengguna/UserLists';
import InvitationLists from '@/Pages/Pengaturan/Pengguna/InvitationLists';
export default {
    components : {
        headerPage,
        // ListPengguna,
        // FormPengguna,
        UserLists,
        InvitationLists,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        addPengguna(){
            this.CLR_ERRORS();
            this.$refs.formPengguna.newPengguna(); 
            UIkit.switcher('#switcherUserManagement').show(1);       
        },

        formClosed(){
            this.$refs.listPengguna.refreshTable();
            UIkit.switcher('#switcherUserManagement').show(0);
        },

        editPengguna(data){
            this.$refs.formPengguna.editPengguna(data);
            UIkit.switcher('#switcherUserManagement').show(1);
        }
    }

}
</script>