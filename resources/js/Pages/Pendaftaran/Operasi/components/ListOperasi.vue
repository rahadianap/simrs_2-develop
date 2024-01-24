<template>
    <div>
        <header-page title="DAFTAR OPERASI" subTitle="daftar operasi pasien"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;" class="uk-responsive">
            <base-table ref="tableOperasi"
                :columns = "tbColumns" 
                :config = "configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists">
                        <row-operasi v-for="dt in dataLists" :rowData="dt" :rowFunctions = rowFunctions></row-operasi>
                    </tbody>
                </template>
            </base-table>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowOperasi from '@/Pages/Pendaftaran/Operasi/components/RowOperasi.vue';

export default {
    name : 'list-operasi',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
        approveFunction : { type : Function, required:false, default: null },
        showFunction : { type : Function, required:false, default: null },
    },

    components : {
        headerPage,
        BaseTable,
        RowOperasi,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'REGULAR', },
            rowFunctions: [
                { 'title':'Ganti Dokter', 'icon':'ban','callback':this.editFunction },   
                { 'title':'Konfirmasi', 'icon':'ban','callback':this.approveFunction },   
                { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
            ],
            tbColumns : [
                { name : 'reg_id', title : 'ID', colType : 'text', width : '120px', },
                { name : 'tgl_periksa', title : 'JADWAL PERIKSA', colType : 'text', },
                { name : 'no_antrian', title : 'No', colType : 'text',},
                { name : 'pasien', title : 'Pasien', width : '180px', colType : 'text', },
                { name : 'unit_nama', title : 'Dokter Unit', width : '200px', colType : 'text', },
                { name : 'tgl_registrasi', title : 'Tgl.Daftar', colType : 'text', },
                { name : 'status_reg', title : 'Status', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
                
            ], 
            buttons : [
                { title : 'Jadwal Baru', icon:'plus-circle', 'callback' : this.addFunction },
            ],
            searchUrl : '/registrations',
            dataLists : null,
         }
    },
    mounted() {
        
    },

    methods : {
        ...mapActions('distribusi',['listDistribusi','deleteDistribusi','dataDistribusi','approveDistribusi']),
        ...mapMutations(['CLR_ERRORS']),
        
        updateListData(data) {
            if(data) {
                if(data.data) { this.dataLists = data.data; }
                else { this.dataLists = null; }
            }
            else { this.dataLists = null; }
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
                        this.$refs.tableOperasi.retrieveData();
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
            this.$refs.tableOperasi.retrieveData();
        },

        penerimaanItem(data) {
            this.$refs.penerimaanDistribusi.viewData(data.distribusi_id);
        }
    }
}
</script>