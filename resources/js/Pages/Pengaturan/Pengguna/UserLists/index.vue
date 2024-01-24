<template>
    <div class="uk-container uk-container-large" style="padding:1em;">        
        <ul id="switcherPengguna" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List</a></li>
            <li><a href="#">Form</a></li>
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <div>
                    <p style="font-size: 12px;font-weight: 400;color: #333;margin:0;padding:0;font-style: italic;">Pengaturan hak akses pengguna</p>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:0.2em;">
                        <base-table ref="tablePengguna"
                            :columns = "tbColumns" 
                            :config = "configTable" 
                            :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                            <template v-slot:tbl-body>
                                <tbody v-if="dataLists">
                                    <row-user-lists
                                        v-for="dt in dataLists" 
                                        :rowData="dt" 
                                        :linkCallback = editPengguna>
                                    </row-user-lists>
                                </tbody>
                            </template>
                        </base-table>
                    </div>
                </div>
            </li>
            <li style="padding:0;margin:0;">
                <user-form ref="formPengguna" v-on:closed="formClosed" v-on:succeed="formClosed"></user-form>
            </li>
        </ul>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import ListPengguna from '@/Pages/Pengaturan/Pengguna/components/ListPengguna.vue';
import UserForm from '@/Pages/Pengaturan/Pengguna/UserLists/UserForm.vue';
import RowUserLists from '@/Pages/Pengaturan/Pengguna/UserLists/RowUserLists.vue';

export default {
    name : 'user-lists',
    components : {
        headerPage,
        ListPengguna,
        UserForm,
        RowUserLists,
        BaseTable,
    },
    data() {
        return {
            configTable : { isExpandable : false, filterType : 'REGULAR' },
            tbColumns : [
                { name : 'username', title : 'USER ID', colType : 'link', linkCallback: this.editPengguna },
                { name : 'nama_lengkap', title : 'Nama Lengkap', colType : 'text',},
                { name : 'tanggal_lahir', title : 'Kelahiran', colType : 'text',},
                { name : 'role_nama', title : 'Role', colType : 'text', isSearchable : true, },
                { name : 'tgl_gabung', title : 'Tgl. Gabung', colType : 'boolean', },
                { name : 'bio_singkat', title : 'Bio Singkat', colType : 'boolean', },
                { name : 'is_admin', title : 'Admin', colType : 'boolean', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean'},                
            ], 
            searchUrl : '/users',
            dataLists : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data) {
            if(data) {
                if(data.data) { this.dataLists = data.data; }
                else { this.dataLists = null; }
            }
            else { this.dataLists = null; }
        },

        addPengguna(){
            this.CLR_ERRORS();
            this.$refs.formPengguna.newPengguna(); 
            UIkit.switcher('#switcherPengguna').show(1);       
        },

        formClosed(){
            this.$refs.tablePengguna.refreshTable();
            UIkit.switcher('#switcherPengguna').show(0);
        },

        editPengguna(data){
            this.$refs.formPengguna.editPengguna(data);
            UIkit.switcher('#switcherPengguna').show(1);
        }
    }

}
</script>