<!-- <template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER MENU MAKANAN" subTitle="master data menu makanan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableMenuMakanan"
                :columns = "tbColumns"
                :config="configTable"
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
    </div>
    <form-menu-makanan ref="formMenuMakanan" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-menu-makanan>
</template> -->
<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <ul id="switcherMenuMakanan" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List</a></li>
            <li><a href="#">Form</a></li>
        </ul>
        <header-page title="MASTER MENU MAKANAN" subTitle="master data menu makanan"></header-page>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
                </div>
                <div style="margin-top:1em;">
                    <base-table ref="tableMenuMakanan"
                        :columns = "tbColumns"
                        :childColumns = "tbChildColumns"
                        childNameIndex = "detail_menu"
                        :config="configTable"
                        :buttons = "buttons"
                        :urlSearch="searchUrl">
                    </base-table>
                </div>
            </li>
            <li style="padding:0;margin:0;">
                <form-menu-makanan ref="formMenuMakanan" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-menu-makanan>
            </li>
        </ul>
    </div>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormMenuMakanan from '@/Pages/MasterData/MenuMakanan/components/FormMenuMakanan.vue';
    
    export default {
        components : {
            headerPage,
            BaseTable,
            FormMenuMakanan
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isExpanded : true,
                configTable : {                
                    isExpandable : true,
                    filterType : 'SIMPLE', 
                },
                buttons : [
                    { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addMenuMakanan },
                ],
                tbColumns : [                
                    { 
                        name : 'menu_makanan_id', 
                        title : 'ID',
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        ],
                    },
                    { 
                        name : 'nama_menu', 
                        title : 'Nama Menu', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'seq_no', 
                        title : 'Sequence', 
                        colType : 'text',
                    },
                    { 
                        name : 'kelas', 
                        title : 'Kelas', 
                        colType : 'text',
                    },
                    { 
                        name : 'is_aktif', 
                        title : 'Aktif', 
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Aktif' },
                            { value:1, text:'Nonaktif' },
                        ]
                    }
                    
                ],
                tbChildColumns :[
                    { name : 'nama_makanan', title : 'Nama Makanan', colType : 'text', textAlign : 'left', },
                    // { name : 'is_optional', title : 'Optional', colType : 'boolean', textAlign : 'center',
                    //     options : [
                    //         { value:0, text:'Aktif' },
                    //         { value:1, text:'Nonaktif' },
                    // ]},
                    // { name : 'is_standard', title : 'Standard', colType : 'boolean', textAlign : 'center',
                    //     options : [
                    //         { value:0, text:'Aktif' },
                    //         { value:1, text:'Nonaktif' },
                    // ]},
                ],
                searchUrl : '/mealsmenu'
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('menu',['listMenu','deleteMenu']),
            ...mapMutations(['CLR_ERRORS']),
    
            /**tampikan modal untuk membuat menu baru */
            addMenuMakanan(){        
                this.CLR_ERRORS();
                UIkit.switcher('#switcherMenuMakanan').show(1);       
            },
    
            refreshTable() {
                this.$refs.tableMenuMakanan.retrieveData();
            },
    
            updateData(data){
                this.CLR_ERRORS();
                this.$refs.formMenuMakanan.editMenu(data.menu_id);
            },
    
            deleteData(data) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menonaktifkan menu ${data.nama_menu}. Lanjutkan ?`)){
                    this.deleteMenuMakanan(data.menu_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableMenuMakanan.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            },
        }
    }
    </script>