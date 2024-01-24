<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER MENU" subTitle="master data menu makanan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableMenu"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
    </div>
    <form-menu ref="formMenu" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-menu>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormMenu from '@/Pages/MasterData/Menu/components/FormMenu.vue'
    
    export default {
        components : {
            headerPage,
            BaseTable,
            FormMenu,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isExpanded : false,
                configTable : {                
                    isExpandable : false,
                    filterType : 'ADVANCED', 
                },
                tbColumns : [                
                    { 
                        name : 'menu_id', 
                        title : 'ID', 
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                            { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                        ],
                    },
                    { 
                        name : 'nama_menu', 
                        title : 'Nama', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'is_menu_khusus', 
                        title : 'Menu Khusus', 
                        colType : 'boolean',
                        isSearchable : true, 
                        options : [
                            { value:0, text:'Menu Khusus' },
                            { value:1, text:'Menu Umum' },
                        ]
                    },
                    { 
                        name : 'catatan', 
                        title : 'Catatan', 
                        colType : 'text', 
                        isSearchable : false,
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
                    { title : 'Menu Baru', icon:'plus-circle', 'callback' : this.addMenu },
                ],
                searchUrl : '/menu'
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('menu',['listMenu','deleteMenu']),
            ...mapMutations(['CLR_ERRORS']),
    
            /**tampikan modal untuk membuat menu baru */
            addMenu(){        
                this.CLR_ERRORS();
                this.$refs.formMenu.newMenu();        
            },
    
            refreshTable() {
                this.$refs.tableMenu.retrieveData();
            },
    
            updateData(data){
                this.CLR_ERRORS();
                this.$refs.formMenu.editMenu(data.menu_id);
            },
    
            deleteData(data) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menonaktifkan menu ${data.nama_menu}. Lanjutkan ?`)){
                    this.deleteMenu(data.menu_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableMenu.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }
        }
    }
    </script>