<template>
    <div>
        <header-page title="DISTRIBUSI PERSEDIAAN" subTitle="daftar permintaan distribusi persediaan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableDistribusi"
                :columns = "tbColumns" 
                :config = "configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists" style="min-weight:50vh;">
                        <row-distribusi 
                            v-for="dt in dataLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunction>
                        </row-distribusi>
                    </tbody>
                </template>
            </base-table>
        </div>
        <preview-distribusi ref="previewDistribusi" v-on:formClosed="refreshTable"></preview-distribusi>
        <penerimaan-distribusi ref="penerimaanDistribusi" v-on:formClosed="refreshTable"></penerimaan-distribusi>
        <cetakan-distribusi ref="cetakanDistribusi"></cetakan-distribusi>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import PreviewDistribusi from '@/Pages/Persediaan/Distribusi/components/PreviewDistribusi.vue';
import PenerimaanDistribusi from '@/Pages/Persediaan/Distribusi/components/PenerimaanDistribusi.vue';
import RowDistribusi from '@/Pages/Persediaan/Distribusi/components/RowDistribusi.vue';
import CetakanDistribusi from '@/Pages/Persediaan/Distribusi/cetakan/cetakanDistribusi.vue';

export default {
    name : 'list-distribusi',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
        approveFunction : { type : Function, required:false, default: null },
        showFunction : { type : Function, required:false, default: null },
    },

    components : {
        headerPage,
        BaseTable,
        PreviewDistribusi,
        PenerimaanDistribusi,
        RowDistribusi,
        CetakanDistribusi
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            rowFunction : [
                { 'title':'Ubah Permintaan', 'icon':'ban','callback':this.editFunction },   
                { 'title':'(Ubah) Persetujuan', 'icon':'ban','callback':this.approveFunction },   
                { 'title':'Penerimaan', 'icon':'file','callback':this.penerimaanItem },   
                { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                // { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
                { 'title':'Cetak Data', 'icon':'file-pdf','callback':this.cetakData },
            ],
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            tbColumns : [
                { 
                    name : 'distribusi_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Permintaan', 'icon':'ban','callback':this.editFunction },   
                        { 'title':'(Ubah) Persetujuan', 'icon':'ban','callback':this.approveFunction },   
                        { 'title':'Penerimaan', 'icon':'file','callback':this.penerimaanItem },   
                        { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                        { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
                    ]
                },
                { 
                    name : 'depo_pengirim', 
                    title : 'Depo Pengirim', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Permintaan', 'icon':'ban','callback':this.editFunction },   
                        { 'title':'(Ubah) Persetujuan', 'icon':'ban','callback':this.approveFunction },   
                        { 'title':'Penerimaan', 'icon':'file','callback':this.penerimaanItem },   
                        { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                        { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
                    ]
                },
                { 
                    name : 'depo_penerima', 
                    title : 'Depo Penerima', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Permintaan', 'icon':'ban','callback':this.editFunction },   
                        { 'title':'(Ubah) Persetujuan', 'icon':'ban','callback':this.approveFunction },   
                        { 'title':'Penerimaan', 'icon':'file','callback':this.penerimaanItem },   
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
                //     name : 'tgl_dibuat', 
                //     title : 'Tgl Dibuat', 
                //     colType : 'text', 
                //     isSearchable : true,
                // },
                { 
                    name : 'tgl_dibutuhkan', 
                    title : 'Tgl Butuh', 
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
            searchUrl : '/distributions/products',
            dataLists : null,
         }
    },
    mounted() {
        
    },

    methods : {
        ...mapActions('distribusi',['listDistribusi','deleteDistribusi','dataDistribusi','approveDistribusi']),
        ...mapActions('cetakan',['dataDist']),
        ...mapMutations(['CLR_ERRORS']),
        
        updateListData(data) {
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        statusListChange(stat) {
            this.searchUrl = `/distributions/${stat}`;
            if(stat == 'request') { this.buttons = [ { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addFunction },]; }
            else if(stat == 'approved') { this.buttons = null; }
            else if(stat == 'received') { this.buttons = null; }
            else if(stat == 'cancelled') { this.buttons = null; }
        },

        deleteData(data) {
            if(data.status !== 'DISETUJUI' && data.status !== 'PERMINTAAN' ) {
                alert('distribusi yang sudah/sedang berjalan tidak dapat diproses.');
                return false;
            }

            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus rencana / data sementara distribusi ini. Lanjutkan ?`)){
                this.deleteDistribusi(data.distribusi_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableDistribusi.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        approveData(data) {
            if(data.status == 'PERMINTAAN' || data.status == 'DISETUJUI' ) {
                this.CLR_ERRORS();
                this.$refs.formCreateOpname.editOpname(data.so_id);
            }
            else {
                alert('Proses distribusi sedang berjalan/ sudah dihapus / sudah selesai. Data sudah tidak dapat diubah.');
            }
        },

        previewData(data) {
            this.$refs.previewDistribusi.viewData(data.distribusi_id);
        },

        refreshTable(){
            this.$refs.tableDistribusi.retrieveData();
        },

        penerimaanItem(data) {
            this.$refs.penerimaanDistribusi.viewData(data.distribusi_id);
        },

        cetakData(data){
            this.dataDist(data.distribusi_id).then((response) => {
                if (response.success == true) {
                    this.$refs.cetakanDistribusi.generateReport(response.data);
                }
                else { alert (response.message) }
            });
        }
    }
}
</script>