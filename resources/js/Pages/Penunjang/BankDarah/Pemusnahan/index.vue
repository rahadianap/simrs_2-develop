<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;margin:0;">    
        <header-page title="PEMUSNAHAN DARAH" subTitle="daftar pemusnahan persediaan darah"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:0;" class="uk-responsive">
            <base-table ref="tablePemusnahan"
                :columns = "tbColumns" 
                :config = "configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists" style="min-weight:50vh;">
                        <row-pemusnahan 
                            v-for="dt in dataLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-pemusnahan>
                    </tbody>
                </template>
            </base-table>
        </div>
        <modal-pemusnahan ref="modalPemusnahanDarah" v-on:modalPemusnahanClosed="refreshTable"></modal-pemusnahan>
        <preview-pemusnahan ref="previewPemusnahanDarah"></preview-pemusnahan>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import ModalPemusnahan from '@/Pages/Penunjang/BankDarah/Pemusnahan/components/ModalPemusnahan.vue';
import PreviewPemusnahan from '@/Pages/Penunjang/BankDarah/Pemusnahan/components/PreviewPemusnahan.vue';
import RowPemusnahan from '@/Pages/Penunjang/BankDarah/Pemusnahan/components/RowPemusnahan.vue';

export default {
    name : 'pemusnahan',
    components : {
        BaseTable,
        ModalPemusnahan,
        PreviewPemusnahan,
        RowPemusnahan,
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
                { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
            ],
            
            tbColumns : [
                { name : 'darah_musnah_id', title : 'ID', colType : 'text', },
                { name : 'tgl_pemusnahan', title : 'Tanggal', colType : 'text',},
                { name : 'catatan', title : 'Catatan', colType : 'text', },
                { name : 'pembuat', title : 'Pembuat', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '',
            dataLists : null,
         }
    },
    mounted() {
        this.preparedData();
    },

    methods : {
        ...mapActions('bankDarah',['getDarahLists','dataPemusnahanDarah','deletePemusnahanDarah']),
        ...mapMutations(['CLR_ERRORS']),

        preparedData(){
            if(this.searchUrl == null || this.searchUrl == '' ) {
                this.searchUrl = '/bloods/exterminations';
            } 
        },

        addData(){
            this.$refs.modalPemusnahanDarah.newData();
        },

        updateData(data) {
            if(data){ this.$refs.modalPemusnahanDarah.editData(data); }
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
            if(confirm(`Proses ini akan menghapus pemusnahan darah ini dan mengembalikan persediaan darah. Lanjutkan ?`)){
                this.deletePemusnahanDarah(data.darah_musnah_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePemusnahan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        previewData(data) {
            this.$refs.previewPemusnahanDarah.previewData(data);
        },

        refreshTable(){
            this.$refs.tablePemusnahan.retrieveData();
        },

        statusData(data) {
            this.$refs.statusPemusnahanDarah.showStatus(data);
        }
    }
}
</script>