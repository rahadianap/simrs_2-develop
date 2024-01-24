<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER TEMPLATE GIZI" subTitle="master data template gizi"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableTemplateGizi"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
    </div>
    <form-template-gizi ref="formTemplateGizi" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-template-gizi>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormTemplateGizi from '@/Pages/MasterData/TemplateGizi/components/FormTemplateGizi.vue';
    
    export default {
        components : {
            headerPage,
            BaseTable,
            FormTemplateGizi,
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
                        name : 'temp_gizi_id', 
                        title : 'ID', 
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                            { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                        ],
                    },
                    { 
                        name : 'nama_template', 
                        title : 'Nama', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'tgl_dibuat', 
                        title : 'Tgl Dibuat', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'tgl_berlaku', 
                        title : 'Tgl Berlaku', 
                        colType : 'text', 
                        isSearchable : true,
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
                    { title : 'Template Gizi Baru', icon:'plus-circle', 'callback' : this.addMakanan },
                ],
                searchUrl : '/template/nutrient'
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('templateGizi',['listTGizi','deleteTGizi']),
            ...mapMutations(['CLR_ERRORS']),
    
            /**tampikan modal untuk membuat templateGizi baru */
            addMakanan(){        
                this.CLR_ERRORS();
                this.$refs.formTemplateGizi.newTemplateGizi();        
            },
    
            refreshTable() {
                this.$refs.tableTemplateGizi.retrieveData();
            },
    
            updateData(data){
                this.CLR_ERRORS();
                this.$refs.formTemplateGizi.editTemplateGizi(data.temp_gizi_id);
            },
    
            deleteData(data) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menonaktifkan templateGizi ${data.nama_template}. Lanjutkan ?`)){
                    this.deleteTGizi(data.temp_gizi_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableTemplateGizi.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }
        }
    }
    </script>