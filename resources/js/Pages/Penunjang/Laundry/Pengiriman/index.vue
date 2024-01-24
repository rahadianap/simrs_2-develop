<template>
    <div class="uk-container uk-container-xlarge" style="padding:0.5em;margin:0;">      
        <header-page title="DISTRIBUSI LINEN" subTitle="daftar pengiriman linen"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:0.5em;" class="uk-responsive">
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
        <modal-pengiriman ref="modalPengirimanLinen" v-on:modalDistLinenClosed="refreshTable"></modal-pengiriman>
        <preview-pengiriman ref="previewPengirimanLinen"></preview-pengiriman>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import headerPage from '@/Components/HeaderPage.vue';
import ModalPengiriman from '@/Pages/Penunjang/Laundry/Pengiriman/components/ModalPengiriman.vue';
import PreviewPengiriman from '@/Pages/Penunjang/Laundry/Pengiriman/components/PreviewPengiriman.vue';
import RowPengiriman from '@/Pages/Penunjang/Laundry/Pengiriman/components/RowPengiriman.vue';

export default {
    name : 'penerimaan',
    components : {
        BaseTable,
        ModalPengiriman,
        PreviewPengiriman,
        RowPengiriman,
        headerPage,
        //StatusPenerimaan,
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
                { name : 'linen_dist_id', title : 'ID', colType : 'text', },
                { name : 'tgl_kirim', title : 'Tgl Kirim', colType : 'text',  width : '50px',},
                { name : 'unit_penerima', title : 'Unit Penerima', colType : 'text',},
                { name : 'penerima', title : 'Nama Penerima', colType : 'text', },
                { name : 'catatan', title : 'Catatan', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
                
            ], 
            buttons : [
                { title : 'Pengiriman Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : null,
            dataLists : null,
         }
    },
    mounted() {
        this.preparedData();
    },

    methods : {
        ...mapActions('linen',['getLinenLists','createPengirimanLinen','updatePengirimanLinen','deletePengirimanLinen','dataPengirimanLinen']),
        ...mapMutations(['CLR_ERRORS']),

        preparedData(){
            if(this.searchUrl == null || this.searchUrl == '' ) {
                this.searchUrl = '/linens/distributions';
            } 
        },

        addData(){
            this.$refs.modalPengirimanLinen.newData();
        },

        updateData(data) {
            if(data){ this.$refs.modalPengirimanLinen.editData(data); }
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
                this.deletePengirimanLinen(data.linen_dist_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePengiriman.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        previewData(data) {
            this.$refs.previewPengirimanLinen.previewData(data);
        },

        refreshTable(){
            this.$refs.tablePengiriman.retrieveData();
        },
    }
}
</script>