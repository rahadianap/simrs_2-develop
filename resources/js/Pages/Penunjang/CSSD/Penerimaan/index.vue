<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;margin:0;">    
        <header-page title="PENERIMAAN CSSD" subTitle="daftar penerimaan item steril"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:0.5em;" class="uk-responsive">
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
        <modal-penerimaan ref="modalPenerimaanCssd" v-on:modalCssdClosed="refreshTable"></modal-penerimaan>
        <preview-penerimaan ref="previewPenerimaanCssd"></preview-penerimaan>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import headerPage from '@/Components/HeaderPage.vue';
import ModalPenerimaan from '@/Pages/Penunjang/CSSD/Penerimaan/components/ModalPenerimaan.vue';
import PreviewPenerimaan from '@/Pages/Penunjang/CSSD/Penerimaan/components/PreviewPenerimaan.vue';
import RowPenerimaan from '@/Pages/Penunjang/CSSD/Penerimaan/components/RowPenerimaan.vue';

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
                { name : 'cssd_terima_id', title : 'ID', colType : 'text', },
                { name : 'tgl_terima', title : 'Tgl Terima', colType : 'text',  width : '50px',},
                { name : 'unit_pengirim', title : 'Unit Pengirim', colType : 'text',},
                { name : 'pengirim', title : 'Nama Pengirim', colType : 'text', },
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
        ...mapActions('cssd',['getCssdLists','createPenerimaanCssd','updatePenerimaanCssd','deletePenerimaanCssd','dataPenerimaanCssd']),
        ...mapMutations(['CLR_ERRORS']),

        preparedData(){
            if(this.searchUrl == null || this.searchUrl == '' ) {
                this.searchUrl = '/sterilizations/receive';
            } 
        },

        addData(){
            this.$refs.modalPenerimaanCssd.newData();
        },

        updateData(data) {
            if(data){ this.$refs.modalPenerimaanCssd.editData(data); }
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
                this.deletePenerimaanCssd(data.cssd_terima_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePenerimaan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        previewData(data) {
            this.$refs.previewPenerimaanCssd.previewData(data);
        },

        refreshTable(){
            this.$refs.tablePenerimaan.retrieveData();
        },

        statusData(data) {
            this.$refs.statusPenerimaanCssd.showStatus(data);
        }
    }
}
</script>