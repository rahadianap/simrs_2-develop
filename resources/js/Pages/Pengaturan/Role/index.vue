<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="ROLE PENGGUNA" subTitle="master data role pengguna"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableRole"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
                <template v-slot:form-filter="slotProps" >
                    <role-form-filter></role-form-filter>
                </template>
            </base-table>
        </div>
        <form-role ref="formRole" v-on:saveSucceed="refreshTable"></form-role>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormRole from '@/Pages/Pengaturan/Role/components/FormRole.vue'
import RoleFormFilter from '@/Pages/Pengaturan/Role/components/RoleFormFilter.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormRole,
        RoleFormFilter,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('role',['roleLists']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            tbColumns : [                
                { 
                    name : 'role_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'role_nama', 
                    title : 'Nama', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'date',
                    isSearchable  : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    options : [
                        { value:0, text:'Aktif' },
                        { value:1, text:'Nonaktif' },
                    ]
                },
                
            ], 
            buttons : [
                { title : 'Role Baru', icon:'plus-circle', 'callback' : this.addRole },
            ],
            searchUrl : '/roles'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('role',['listRole','deleteRole']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat role baru */
        addRole(){        
            this.CLR_ERRORS();
            this.$refs.formRole.newRole();        
        },

        refreshTable() {
            this.$refs.tableRole.retrieveData();
        },

        linkTableClicked(data){
            this.CLR_ERRORS();
        },

        previewData(data){
            this.CLR_ERRORS();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formRole.editRole(data.role_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan role pengguna ${data.role_nama}. Lanjutkan ?`)){
                this.deleteRole(data.role_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableRole.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>