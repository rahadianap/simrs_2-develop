<template>
    <div>
        <base-table ref="tableProdukDarah"
            :columns = "tbColumns" 
            :config = "configTable" 
            :buttons = "buttons"
            :urlSearch="searchUrl">
        </base-table>
        <modal-produk-darah ref="modalProdukDarah" v-on:closedModalProdukDarah="refreshTable"></modal-produk-darah>
    </div>
</template>
<script>
import BaseTable from '@/Components/BaseTable';
import { mapGetters, mapActions, mapMutations } from 'vuex';
import ModalProdukDarah from '@/Pages/Penunjang/BankDarah/Referensi/components/ModalProdukDarah.vue';

export default {
    name : 'list-produk-darah',
    components : {
        BaseTable,
        ModalProdukDarah,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'REGULAR', },
            tbColumns : [
                { name : 'jenis_produk_darah_id', title : 'ID', colType : 'link', linkCallback : this.showData},
                { name : 'jenis_produk_darah', title : 'NAMA', colType : 'text',},
                { name : 'inisial', title : 'INISIAL', colType : 'text',  width : '50px', textAlign:'center'},
                { name : 'deskripsi', title : 'DESKRIPSI', colType : 'text',},
                { name : 'tindakan_nama', title : 'BIAYA', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/bloods/groups',

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
                this.searchUrl = '/bloods/groups';
            } 
        },

        refreshTable(){ 
            this.$refs.tableProdukDarah.retrieveData(); 
        },

        showData(data){
            if(data){
                this.CLR_ERRORS();
                this.$refs.modalProdukDarah.editData(data.jenis_produk_darah_id);
            }
        },

        addData(){
            this.CLR_ERRORS();
            this.$refs.modalProdukDarah.newData();
        },
    }
}
</script>