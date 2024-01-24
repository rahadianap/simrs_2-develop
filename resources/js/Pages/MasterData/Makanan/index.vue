<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER MAKANAN" subTitle="master data makanan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableMakanan"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
                <template v-slot:form-filter="slotProps" >
                    <filter-makanan></filter-makanan>
                </template>
            </base-table>
        </div>
    </div>
    <form-makanan ref="formMakanan" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-makanan>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormMakanan from '@/Pages/MasterData/Makanan/components/FormMakanan.vue'
    import FilterMakanan from '@/Pages/MasterData/Makanan/components/FilterMakanan.vue';
    
    export default {
        components : {
            headerPage,
            BaseTable,
            FormMakanan,
            FilterMakanan,
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
                        name : 'makanan_id', 
                        title : 'ID', 
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                            { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                        ],
                    },
                    { 
                        name : 'nama_makanan', 
                        title : 'Nama', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'kelompok', 
                        title : 'Kelompok', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'jns_makanan', 
                        title : 'Jenis', 
                        colType : 'text', 
                        isSearchable : true,
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
                    { title : 'Makanan Baru', icon:'plus-circle', 'callback' : this.addMakanan },
                ],
                searchUrl : '/meals'
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('makanan',['listMakanan','deleteMakanan']),
            ...mapMutations(['CLR_ERRORS']),
    
            /**tampikan modal untuk membuat makanan baru */
            addMakanan(){        
                this.CLR_ERRORS();
                this.$refs.formMakanan.newMakanan();        
            },
    
            refreshTable() {
                this.$refs.tableMakanan.retrieveData();
            },
    
            updateData(data){
                this.CLR_ERRORS();
                this.$refs.formMakanan.editMakanan(data.makanan_id);
            },
    
            deleteData(data) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menonaktifkan makanan ${data.nama_makanan}. Lanjutkan ?`)){
                    this.deleteMakanan(data.makanan_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableMakanan.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }
        }
    }
    </script>