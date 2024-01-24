<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;margin:0;">    
        <header-page title="PERMINTAAN DAN DISTRIBUSI DARAH" subTitle="daftar permintaan dan distribusi darah"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:0;" class="uk-responsive">
            <base-table ref="tablePengiriman"
                :columns = "tbColumns" 
                :config = "configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists" style="min-weight:50vh;">
                        <row-pengiriman 
                            v-for="dt in dataLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-pengiriman>
                    </tbody>
                </template>
            </base-table>
        </div>
        <create-permintaan ref="createPengirimanDarah" v-on:modalCreateDarahClosed="refreshTable"></create-permintaan>
        <modal-pengiriman ref="modalPengirimanDarah" v-on:modalDistribusiDarahClosed="refreshTable"></modal-pengiriman>
        <preview-pengiriman ref="previewPengirimanDarah"></preview-pengiriman>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import headerPage from '@/Components/HeaderPage.vue';
import CreatePermintaan from '@/Pages/Penunjang/BankDarah/Pengiriman/components/CreatePermintaan.vue';
import PreviewPengiriman from '@/Pages/Penunjang/BankDarah/Pengiriman/components/PreviewPengiriman.vue';
import ModalPengiriman from '@/Pages/Penunjang/BankDarah/Pengiriman/components/ModalPengiriman.vue';
import RowPengiriman from '@/Pages/Penunjang/BankDarah/Pengiriman/components/RowPengiriman.vue';

export default {
    name : 'penerimaan',
    components : {
        BaseTable,
        CreatePermintaan,
        PreviewPengiriman,
        RowPengiriman,
        ModalPengiriman,
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
                { 'title':'Lihat Permintaan', 'icon':'file','callback':this.previewData },   
                { 'title':'Ubah Permintaan', 'icon':'ban','callback':this.updateData },   
                { 'title':'Hapus Permintaan', 'icon':'ban','callback':this.deleteData },
                { 'title':'Realisasi Permintaan', 'icon':'ban','callback':this.realisasiData },
            ],
            
            tbColumns : [
                { name : 'darah_dist_id', title : 'Distribusi ID', colType : 'text', },
                { name : 'pasien_nama', title : 'Pasien', colType : 'text', },
                { name : 'unit_nama', title : 'Unit', colType : 'text', },
                { name : 'Darah', title : 'Kebutuhan', colType : 'text', },
                { name : 'catatan', title : 'Catatan', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
            ], 
            buttons : [
                { title : 'Permintaan Baru', icon:'plus-circle', 'callback' : this.addData },
            ],

            searchUrl : null,
            dataLists : null,
         }
    },
    mounted() {
        this.preparedData();
    },

    methods : {
        ...mapActions('cssd',['getCssdLists','createPengirimanCssd','updatePengirimanCssd','deletePengirimanCssd','dataPengirimanCssd']),
        ...mapMutations(['CLR_ERRORS']),

        preparedData(){
            if(this.searchUrl == null || this.searchUrl == '' ) {
                this.searchUrl = '/bloods/requests';
            } 
        },

        addData(){
            this.$refs.createPengirimanDarah.newData();
        },

        updateData(data) {
            if(data){ this.$refs.createPengirimanDarah.editData(data); }
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
            if(confirm(`Proses ini akan menghapus pengiriman ini. Lanjutkan ?`)){
                this.deletePengirimanCssd(data.cssd_dist_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePengiriman.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        previewData(data) {
            this.$refs.previewPengirimanDarah.previewData(data);
        },

        refreshTable(){
            this.$refs.tablePengiriman.retrieveData();
        },

        realisasiData(data) {
            this.$refs.modalPengirimanDarah.editData(data);
        }
    }
}
</script>