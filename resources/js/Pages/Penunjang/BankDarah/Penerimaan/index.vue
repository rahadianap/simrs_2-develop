<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;margin:0;">    
        <header-page title="PENERIMAAN DARAH" subTitle="daftar penerimaan persediaan darah"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:0;" class="uk-responsive">
            <base-table ref="tablePenerimaan"
                :columns = "tbColumns" 
                :config = "configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists" style="min-weight:50vh;">
                        <row-penerimaan 
                            v-for="dt in dataLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-penerimaan>
                    </tbody>
                </template>
            </base-table>
        </div>
        <modal-penerimaan ref="modalPenerimaanDarah" v-on:modalDarahClosed="refreshTable"></modal-penerimaan>
        <preview-penerimaan ref="previewPenerimaanDarah"></preview-penerimaan>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import ModalPenerimaan from '@/Pages/Penunjang/BankDarah/Penerimaan/components/ModalPenerimaan.vue';
import PreviewPenerimaan from '@/Pages/Penunjang/BankDarah/Penerimaan/components/PreviewPenerimaan.vue';
import RowPenerimaan from '@/Pages/Penunjang/BankDarah/Penerimaan/components/RowPenerimaan.vue';

export default {
    name : 'penerimaan',
    components : {
        BaseTable,
        ModalPenerimaan,
        PreviewPenerimaan,
        RowPenerimaan,
        headerPage
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'REGULAR', },
            rowFunctions: [
                { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                { 'title':'Ubah Data', 'icon':'ban','callback':this.updateData },   
                { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
            ],
            
            tbColumns : [
                { name : 'darah_terima_id', title : 'ID', colType : 'text', },
                { name : 'tgl_terima', title : 'Tanggal', colType : 'text',  width : '50px',},
                { name : 'asal_darah', title : 'Asal', colType : 'text',},
                { name : 'nama_donor', title : 'Donor', colType : 'text',},
                { name : 'penerima', title : 'Penerima', colType : 'text', },
                { name : 'catatan', title : 'Catatan', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
                
            ], 
            buttons : [
                { title : 'Penerimaan Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '',
            dataLists : null,
         }
    },
    mounted() {
        this.preparedData();
    },

    methods : {
        ...mapActions('bankDarah',['getDarahLists','createPenerimaanDarah','updatePenerimaanDarah','deletePenerimaanDarah','dataPenerimaanDarah']),
        ...mapMutations(['CLR_ERRORS']),

        preparedData(){
            if(this.searchUrl == null || this.searchUrl == '' ) {
                this.searchUrl = '/bloods/receive';
            } 
        },

        addData(){
            this.$refs.modalPenerimaanDarah.newData();
        },

        updateData(data) {
            if(data){ this.$refs.modalPenerimaanDarah.editData(data); }
        },
        
        updateListData(data) {
            if(data) {
                if(data.data) { this.dataLists = data.data; }
                else { this.dataLists = null; }
            }
            else { this.dataLists = null; }
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus penerimaan ini. Lanjutkan ?`)){
                this.deletePenerimaanDarah(data.darah_terima_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePenerimaan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        previewData(data) {
            this.$refs.previewPenerimaanDarah.previewData(data);
        },

        refreshTable(){
            this.$refs.tablePenerimaan.retrieveData();
        },
    }
}
</script>