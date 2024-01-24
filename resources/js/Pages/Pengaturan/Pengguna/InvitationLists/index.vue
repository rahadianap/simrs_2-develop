<template>
    <div class="uk-container uk-container-large" style="padding:1em;">        
        <ul id="switcherPengguna" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List</a></li>
            <li><a href="#">Form</a></li>
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <div>
                    <p style="font-size: 12px;font-weight: 400;color: #333;margin:0;padding:0;font-style: italic;">Daftar dan status undangan bergabung</p>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:0.2em;">
                        <base-table ref="invitationLists"
                            :columns = "tbColumns" 
                            :buttons = "buttons"
                            :config = "configTable" 
                            :urlSearch="searchUrl">
                        </base-table>
                    </div>
                </div>
            </li>
            <li style="padding:0;margin:0;">
                <user-form ref="formPengguna" v-on:closed="formClosed" v-on:succeed="formClosed"></user-form>
            </li>
        </ul>
        <div>
            <modal-invitation ref="modalInvitation" v-on:modalInvitationClosed="formClosed"></modal-invitation>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import UserForm from '@/Pages/Pengaturan/Pengguna/UserLists/UserForm.vue';
import ModalInvitation from '@/Pages/Pengaturan/Pengguna/InvitationLists/ModalInvitation.vue';
import ListPengguna from '@/Pages/Pengaturan/Pengguna/components/ListPengguna.vue';

export default {
    name : 'invitation-lists',
    components : {
        headerPage,
        ListPengguna,
        ModalInvitation,
        UserForm,
        BaseTable,
    },
    data() {
        return {
            searchUrl : '/invitations',
            configTable : { isExpandable : false, filterType : 'REGULAR' },
            tbColumns : [
                { name : 'username', title : 'Username', colType : 'link', linkCallback: this.editPengguna },
                { name : 'email', title : 'Email', colType : 'link', linkCallback: this.editPengguna },
                { name : 'nama_lengkap', title : 'Nama Lengkap', colType : 'text',},
                { name : 'tgl_dibuat', title : 'Tgl Undangan', colType : 'text', isSearchable : true, },
                { name : 'expired_at', title : 'Kadaluarsa', colType : 'text', },
                { name : 'response_at', title : 'Tgl.Konfirmasi', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean'},                
            ], 
            buttons : [
                { title : 'Undangan Baru', icon:'plus-circle', 'callback' : this.addInvitation },
            ],
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        addInvitation(){
            this.CLR_ERRORS();
            this.$refs.modalInvitation.invite(); 
            // UIkit.switcher('#switcherPengguna').show(1);       
        },

        formClosed(){
            this.$refs.invitationLists.refreshTable();
            UIkit.switcher('#switcherPengguna').show(0);
        },

        editPengguna(data){
            this.$refs.formPengguna.editPengguna(data);
            UIkit.switcher('#switcherPengguna').show(1);
        }
    }

}
</script>