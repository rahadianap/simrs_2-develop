<template>
    <div>
        <header-page title="MANAJEMEN PENGGUNA" subTitle="pengaturan hak akses pengguna"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tablePengguna"
                :columns = "tbColumns" 
                :config = "configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';

export default {
    name : 'list-pengguna',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
        approveFunction : { type : Function, required:false, default: null },
        showFunction : { type : Function, required:false, default: null },
    },

    components : {
        headerPage,
        BaseTable,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            rowFunction : [ { 'title':'Ubah Data Pengguna', 'icon':'ban','callback':this.editFunction },],
            configTable : { isExpandable : false, filterType : 'REGULAR' },
            tbColumns : [
                { name : 'username', title : 'Username', colType : 'link', linkCallback: this.userData },
                { name : 'email', title : 'Email', colType : 'link', linkCallback: this.userData },
                { name : 'nama_lengkap', title : 'Nama Lengkap', colType : 'text',},
                { name : 'role_nama', title : 'Role', colType : 'text', isSearchable : true, },
                { name : 'is_admin', title : 'Admin', colType : 'boolean', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean'},                
            ], 
            buttons : [
                { title : 'Pengguna Baru', icon:'plus-circle', 'callback' : this.addFunction },
            ],
            searchUrl : '/users',
            
         }
    },
    mounted() {        
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        
        userData(data){
            this.$emit('edit',data);
        },

        refreshTable(){
            this.$refs.tablePengguna.retrieveData();
        },
    }
}
</script>