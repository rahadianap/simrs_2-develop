<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="KELOMPOK MENU" subTitle="master kelompok menu"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKelompokMenu"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-kelompok-menu ref="formKelompokMenu" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-kelompok-menu>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormKelompokMenu from '@/Pages/MasterData/KelompokMenu/components/FormKelompokMenu.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormKelompokMenu,
    },
    computed : {
        ...mapGetters(['errors']),
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
                    name : 'kelompok_menu_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'kelompok', 
                    title : 'Nama', 
                    colType : 'text',
                    isSearchable : true,
                },
                { 
                    name : 'catatan', 
                    title : 'Deskripsi', 
                    colType : 'text',
                    isSearchable : false,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/menu/groups'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('kelompokMenu',['deleteKelompokMenu']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$refs.formKelompokMenu.newKelompokMenu();
        },

        refreshTable() {
            this.$refs.tableKelompokMenu.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKelompokMenu.editKelompokMenu(data.kelompok_menu_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus kelompok tagihan ${data.kelompok}. Lanjutkan ?`)){
                this.deleteKelompokMenu(data.kelompok_menu_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKelompokMenu.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>