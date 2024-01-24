<template>
    <div>
        <base-table ref="tableAsalDarah"
            :columns = "tbColumns" 
            :config = "configTable" 
            :buttons = "buttons"
            :urlSearch="searchUrl">
        </base-table>
        <modal-asal-darah ref="modalAsalDarah" v-on:closedModalAsalDarah="refreshTable"></modal-asal-darah>
    </div>
</template>
<script>
import BaseTable from '@/Components/BaseTable';
import { mapGetters, mapActions, mapMutations } from 'vuex';
import ModalAsalDarah from '@/Pages/Penunjang/BankDarah/Referensi/components/ModalAsalDarah.vue';

export default {
    name : 'list-asal-darah',
    components : {
        BaseTable,
        ModalAsalDarah,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'REGULAR', },
            tbColumns : [
                { name : 'asal_darah_id', title : 'ID', colType : 'link', linkCallback : this.showData},
                { name : 'asal_darah', title : 'NAMA', colType : 'text',},
                { name : 'deskripsi', title : 'DESKRIPSI', colType : 'text',},
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/bloods/sources',

            dataLists : null,
         }
    },
    mounted() {
        this.preparedData();
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),

        preparedData(){
            if(this.searchUrl == null || this.searchUrl == '' ) {
                this.searchUrl = '/bloods/sources';
            } 
        },

        refreshTable(){ 
            this.$refs.tableAsalDarah.retrieveData(); 
        },

        showData(data){
            if(data){
                this.CLR_ERRORS();
                this.$refs.modalAsalDarah.editData(data.asal_darah_id);
            }
        },

        addData(){
            this.CLR_ERRORS();
            this.$refs.modalAsalDarah.newData();
        },
    }
}
</script>