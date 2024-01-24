<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER DATA SATUAN (UNIT)" subTitle="master data unit satuan persediaan dan jasa"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableSatuan"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-satuan ref="formSatuan" v-on:closed="refreshTable"></form-satuan>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import formSatuan from '@/Pages/MasterData/Satuan/components/FormSatuan.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        formSatuan,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'SIMPLE', 
            },
            tbColumns : [                
                { 
                    name : 'satuan_id', 
                    title : 'Satuan', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'satuan_nama', 
                    title : 'Nama', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addSatuan },
            ],
            searchUrl : '/uoms'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('satuan',['deleteSatuan']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addSatuan(){        
            this.CLR_ERRORS();
            this.$refs.formSatuan.newSatuan();
        },

        refreshTable() {
            this.$refs.tableSatuan.retrieveData();
        },
        
        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formSatuan.editSatuan(data.satuan_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus satuan ${data.satuan_id}. Lanjutkan ?`)){
                this.deleteSatuan(data.satuan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableSatuan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>