<template>
    <div>
        <header-page title="STOCK ADJUSTMENTS" subTitle="penyesuaian jumlah persediaan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableAdjustment"
                :columns = "tbColumns" 
                :config = "configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists" style="min-weight:50vh;">
                        <row-adjustment
                            v-for="dt in dataLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-adjustment>
                    </tbody>
                </template>
            </base-table>
        </div>
        <preview-adjustment ref="previewAdjustment"></preview-adjustment>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import PreviewAdjustment from '@/Pages/Persediaan/Adjustment/components/PreviewAdjustment.vue';
import RowAdjustment from '@/Pages/Persediaan/Adjustment/components/RowAdjustment.vue';

export default {
    name : 'list-adjustment',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
        approveFunction : { type : Function, required:false, default: null },
        showFunction : { type : Function, required:false, default: null },
    },

    components : {
        headerPage,
        BaseTable,
        PreviewAdjustment,
        RowAdjustment,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : true,
                filterType : 'REGULAR', 
            },
            rowFunctions : [
                { 'title':'Ubah Data', 'icon':'ban','callback':this.editFunction },
                { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
            ],
            tbColumns : [
                { 
                    name : 'sa_id', 
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
                    isSearchable : true,
                },
                // { 
                //     name : 'tgl_sa', 
                //     title : 'Dibuat', 
                //     colType : 'text', 
                //     isSearchable : true,
                // },
                { 
                    name : 'tgl_selesai', 
                    title : 'Realisasi', 
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
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addFunction },
            ],
            searchUrl : '/stock/adjustments',
            dataLists : null,
         }
    },
    mounted() {        
    },

    methods : {
        ...mapActions('adjustment',['listAdjustment','deleteAdjustment','dataAdjustment','approveAdjustment']),
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data){
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        deleteData(data) {
            if(data.status !== 'RENCANA') {
                alert('penyesuaian yang sudah/sedang berjalan/dibatalkan tidak dapat diproses.');
                return false;
            }

            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus rencana / data sementara penyesuaian ini. Lanjutkan ?`)){
                this.deleteAdjustment(data.sa_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableAdjustment.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        approveData(data) {
            this.CLR_ERRORS();
            if(data.status !== 'RENCANA') {
                alert('penyesuaian yang sudah/sedang berjalan/dibatalkan tidak dapat diproses.');
                return false;
            }

            if(confirm(`Proses ini akan menyesuaikan jumlah persediaan di depo ${this.penyesuaian.depo_nama}. Lanjutkan ?`)){
                this.approveAdjustment(data.sa_id).then((response) => {
                    if (response.success == true) {
                        alert("persetujuan penyesuaian berhasil dibuat.");
                        this.$refs.tableAdjustment.retrieveData();
                    }
                })
            }
        },

        previewData(data) {
            this.$refs.previewAdjustment.viewData(data.sa_id);
        },

        refreshTable(){
            this.$refs.tableAdjustment.retrieveData();
        },
    }
}
</script>