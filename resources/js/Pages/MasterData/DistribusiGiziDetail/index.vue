<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER DETAIL DISTRIBUSI GIZI" subTitle="master data detail distribusi gizi"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableDistribusiGiziDetail"
                :columns = "tbColumns" 
                :config="configTable"
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
                configTable : {                
                    isExpandable : false,
                    filterType : 'ADVANCED', 
                },
                tbColumns : [                
                    { 
                        name : 'detail_id', 
                        title : 'ID', 
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                            { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                        ],
                    },
                    { 
                        name : 'dist_gizi_id', 
                        title : 'ID Distribusi Gizi', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'reg_id', 
                        title : 'No. Registrasi', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'trx_id', 
                        title : 'No. Transaksi', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'status', 
                        title : 'Status', 
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
                // buttons : [
                //     { title : 'Detail Distribusi Gizi Baru', icon:'plus-circle', 'callback' : this.addDetailDistribusiGizi },
                // ],
                searchUrl : '/distributions/detail/nutrient'
             }
        },
        methods: {
            ...mapActions('distribusiGiziDetail',['listDDGizi','deleteDDGizi']),
            ...mapMutations(['CLR_ERRORS']),
    
            /**tampikan modal untuk membuat distGizi baru */
            addDetailDistribusiGizi(){        
                this.CLR_ERRORS();
                this.$refs.formDetailDistribusiGizi.newDetailDistribusiGizi();        
            },

            refreshTable() {
                this.$refs.tableDistribusiGiziDetail.retrieveData();
            },
    
            updateData(data){
                this.CLR_ERRORS();
                this.$refs.formDetailDistribusiGizi.editDetailDistribusiGizi(data.detail_id);
            },

            deleteData(data) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menonaktifkan ${data.detail_id}. Lanjutkan ?`)){
                    this.deleteDDGizi(data.detail_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableDistribusiGiziDetail.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }
        }
    }
</script>