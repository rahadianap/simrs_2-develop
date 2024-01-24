<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="DISTRIBUSI MENU" subTitle="distribusi menu"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableDistribusiGizi"
                :columns = "tbColumns" 
                :config="configTable"
                :childColumns = "tbChildColumns"
                childNameIndex = "detail_distribusi"
                :rowFunctions = rowFunctions
                :urlSearch="searchUrl">
            </base-table>
        </div>
    </div>
    <form-distribusi-gizi ref="formDistribusiGizi" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-distribusi-gizi>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormDistribusiGizi from '@/Pages/MasterData/DistribusiGizi/components/FormDistribusiGizi.vue';
    
    export default {
        components : {
            headerPage,
            BaseTable,
            FormDistribusiGizi,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isExpanded : false,
                configTable : {                
                    isExpandable : true,
                    filterType : 'ADVANCED', 
                },
                tbColumns : [                
                    { 
                        name : 'pasien_diet_id', 
                        title : 'ID', 
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Distribute', 'icon':'file-edit','callback':this.distributedGizi },
                        ],
                    },          
                    { name : 'trx_id', title : 'Transaksi ID', colType : 'text' },
                    { name : 'nama_pasien', title : 'Nama Pasien', colType : 'text', },
                    { name : 'nama_unit', title : 'Unit', colType : 'text', },
                    { name : 'status', title : 'Status', colType : 'text', },              
                    { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }  
                    
                ],
                tbChildColumns :[
                    // { name : 'pasien_diet_id', title : 'Diet ID', colType : 'linkmenus', textAlign : 'left',
                    // functions: [
                    //     { 'title':'Approve', 'icon':'check','callback':this.approvalDiet },
                    // ]
                    // },
                    { name : 'nama_diet', title : 'Nama Diet', colType : 'linkmenus', textAlign : 'left',
                        functions: [
                            { 'title':'Distribute', 'icon':'file-edit','callback':this.distributedGizi },
                        ]
                    },
                    { name : 'start_date', title : 'Tanggal', colType : 'text', textAlign : 'left', },
                    { name : 'meal_set', title : 'Set', colType : 'text', textAlign : 'left', },
                    { name : 'schedule', title : 'Jadwal', colType : 'text', textAlign : 'left', },
                    { name : 'nama_menu', title : 'Nama Menu', colType : 'text', textAlign : 'left', },
                    { name : 'qty', title : 'Qty', colType : 'text', textAlign : 'left', },  
                    { name : 'status', title : 'Status', colType : 'text', textAlign : 'left', },
                ], 
                // buttons : [
                //     { title : 'Distribusi Gizi Baru', icon:'plus-circle', 'callback' : this.addDistribusiGizi },
                // ],
                searchUrl : '/distributions/nutrient'
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('distribusiGizi',['listDGizi','deleteDGizi','distributeGizi']),
            ...mapMutations(['CLR_ERRORS']),
    
            /**tampikan modal untuk membuat distGizi baru */
            addDistribusiGizi(){        
                this.CLR_ERRORS();
                this.$refs.formDistribusiGizi.newDistribusiGizi();        
            },
    
            refreshTable() {
                this.$refs.tableDistribusiGizi.retrieveData();
            },
    
            updateData(data){
                this.CLR_ERRORS();
                this.$refs.formDistribusiGizi.editDistribusiGizi(data.dist_gizi_id);
            },
    
            deleteData(data) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menonaktifkan ${data.dist_gizi_id}. Lanjutkan ?`)){
                    this.deleteDGizi(data.dist_gizi_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableDistribusiGizi.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            },

            distributedGizi(data) {
                this.CLR_ERRORS();
                if(confirm(`Distribusikan menu diet untuk transaksi ini?`)){
                console.log(data);
                this.distributeGizi(data.pasien_diet_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableDistribusiGizi.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }
        }
    }
    </script>