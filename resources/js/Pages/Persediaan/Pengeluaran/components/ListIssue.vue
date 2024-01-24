<template>
    <div>
        <header-page title="PENGELUARAN PERSEDIAAN" subTitle="pengeluaran / pemakaian item persediaan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableInventoryIssue"
                :columns = "tbColumns" 
                :config = "configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl"  v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists" style="min-weight:50vh;">
                        <row-issue
                            v-for="dt in dataLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-issue>
                    </tbody>
                </template>
            </base-table>
        </div>
        <preview-issue ref="previewIssue"></preview-issue>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import PreviewIssue from '@/Pages/Persediaan/Pengeluaran/components/PreviewIssue.vue';
import RowIssue from '@/Pages/Persediaan/Pengeluaran/components/RowIssue.vue';


export default {
    name : 'list-issue',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
        approveFunction : { type : Function, required:false, default: null },
        showFunction : { type : Function, required:false, default: null },
    },

    components : {
        headerPage,
        BaseTable,
        PreviewIssue,
        RowIssue,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            rowFunctions :[
                { 'title':'Ubah Data', 'icon':'ban','callback':this.editFunction },
                { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
            ],
            tbColumns : [
                { 
                    name : 'inven_issue_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'ban','callback':this.editFunction },
                        { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                        { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
                    ]
                },
                { 
                    name : 'depo_nama', 
                    title : 'Depo Persediaan', 
                    colType : 'linkmenus', 
                    functions: [
                    { 'title':'Ubah Data', 'icon':'ban','callback':this.editFunction },
                        { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                        { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
                    ]
                },
                { 
                    name : 'catatan', 
                    title : 'Catatan', 
                    colType : 'text', 
                },
                { 
                    name : 'tgl_selesai', 
                    title : 'Realisasi', 
                    colType : 'text', 
                },
                { 
                    name : 'status', 
                    title : 'Status', 
                    colType : 'text', 
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                },                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addFunction },
            ],
            searchUrl : '/inventory/issue',
            dataLists : null,
         }
    },
    mounted() {        
    },

    methods : {
        ...mapActions('inventoryIssue',['listInventoryIssue','deleteInventoryIssue','dataInventoryIssue','approveInventoryIssue']),
        ...mapMutations(['CLR_ERRORS']),
        
        updateListData(data){
            this.dataLists = null;
            if(data){ this.dataLists = data.data; }
        },

        deleteData(data) {
            if(data.status !== 'RENCANA') {
                alert('pengeluaran yang sudah/sedang berjalan/dibatalkan tidak dapat diproses.');
                return false;
            }

            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus rencana / data sementara pengeluaran persediaan ini. Lanjutkan ?`)){
                this.deleteInventoryIssue(data.inven_issue_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableInventoryIssue.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        approveData(data) {
            this.CLR_ERRORS();
            if(data.status !== 'RENCANA') {
                alert('pengeluaran yang sudah/sedang berjalan/dibatalkan tidak dapat diproses.');
                return false;
            }

            if(confirm(`Proses ini akan mengurangi jumlah persediaan di depo ${this.issue.depo_nama}. Lanjutkan ?`)){
                this.approveInventoryIssue(data.penyesuaian_id).then((response) => {
                    if (response.success == true) {
                        alert("persetujuan pengeluaran berhasil dibuat.");
                        this.$refs.tableAdjustment.retrieveData();
                    }
                })
            }
        },

        previewData(data) {
            this.$refs.previewIssue.viewData(data.inven_issue_id);
        },

        refreshTable(){
            this.$refs.tableInventoryIssue.retrieveData();
        },
    }
}
</script>