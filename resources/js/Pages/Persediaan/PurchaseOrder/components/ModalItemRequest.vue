<template>
    <div class="uk-modal uk-modal-container" id="modalItemRequest">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Pilih Item Permintaan</h2>            
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            <!-- <div class="uk-grid-small hims-table-filter-container" uk-grid style="margin:0;padding:0.5em;">             -->
                <!-- <div class="uk-width-auto" style="margin:0;padding:0 0.5em 0 0.5em;">
                    <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                        <form action="" @submit.prevent="retrieveData" style="padding:0;margin:0;">
                            <input type="text" v-model="params.keyword" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="kata pencarian">
                            <button type="submit" class="hims-table-btn uk-width-auto" style="background-color:#cc0202;color:#fff;"><span uk-icon="search"></span> Cari</button>
                        </form>
                    </div>                    
                </div> -->
                <!-- <div class="uk-width-expand" style="text-align:right;">
                    <div class="uk-button-group uk-box-shadow-large" style="padding:0;margin:0;border-radius:5px;">
                        <button class="hims-table-btn" @click.prevent="changeView(false)"><span uk-icon="thumbnails"></span></button>
                        <button class="hims-table-btn" @click.prevent="changeView(true)"><span uk-icon="table"></span></button>
                    </div>
                </div> -->
            <!-- </div> -->

            <template v-if="isListMode">
                <div style="margin-top:0;padding:0;">
                    <base-table ref="tablePurchaseRequest"
                        :columns = "tbRequestColumns" 
                        :config = "configTable"
                        :buttons = "buttons"
                        :urlSearch="searchUrl"  v-on:updateListDataTable="updateListData">
                        <template v-slot:tbl-body>
                            <tbody v-if="dataLists" style="min-weight:50vh;">
                                <row-permintaan
                                    v-for="dt in dataLists" 
                                    :rowData="dt" 
                                    v-on:itemRequestSelected="itemSelected">
                                </row-permintaan>
                            </tbody>
                        </template>
                    </base-table>        
                </div>
            </template>

            <template v-else>
                <div style="margin-top:0;padding:0;">
                    <base-table ref="tablePurchaseRequestDetail"
                        :columns = "tbDetailRequestColumns" 
                        :config = "configTableDetail"
                        :buttons = "buttons"
                        :urlSearch="searchUrlDetail"  v-on:updateListDataTable="updateListDataDetail">
                        <template v-slot:tbl-body>
                            <tbody v-if="detailLists" style="min-weight:50vh;">
                                <row-permintaan-detail
                                    v-for="dt in detailLists" 
                                    :rowData="dt"
                                    :removeSelected="removeFunction" 
                                    :addSelected="callbackFunction"
                                    v-on:itemRequestSelected="itemSelected">
                                </row-permintaan-detail >
                            </tbody>
                        </template>
                    </base-table>        
                </div>
            </template>
        </div>
            
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import RowPermintaan from '@/Pages/Persediaan/PurchaseOrder/components/RowPermintaan.vue';
import RowPermintaanDetail from '@/Pages/Persediaan/PurchaseOrder/components/RowPermintaanDetail.vue';

export default {
    name : 'modal-item-request',
    components : {
        BaseTable,
        RowPermintaan,
        RowPermintaanDetail,
    },

    data() {
        return {
            isExpanded : false,
            isListMode : true,
            configTable : { isExpandable : false, filterType : 'SIMPLE', },
            configTableDetail : { isExpandable : false, filterType : 'SIMPLE', },

            tbRequestColumns : [                
                { name : 'pr_id', title : 'ID PERMINTAAN', colType : 'linkmenus', },
                { name : 'unit_nama', title : 'Unit', colType : 'text', },
                { name : 'depo_nama', title : 'Depo', colType : 'text', },
                { name : 'tgl_dibutuhkan', title : 'Tgl. Butuh', colType : 'text', },
                { name : 'catatan', title : 'Catatan', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text',  },
                { name : 'is_aktif', title : 'Aktif',  colType : 'boolean', },                
            ], 

            tbDetailRequestColumns : [
                { name : 'produk_id', title : 'ID PRODUK', colType : 'linkmenus', width:'150px' },
                { name : 'produk_nama', title : 'Nama Produk', colType : 'text', },
                { name : 'jumlah', title : 'Jumlah', colType : 'text', textAlign : 'center', },
                { name : 'pr_satuan', title : 'Satuan', colType : 'text', textAlign : 'center', },
            ],

            buttons : [
                // { title : 'PR Baru', icon:'plus-circle', 'callback' : this.addPurchaseRequest },
                { title : 'List Permintaan', icon:'table', 'callback' : this.showListMode },
                { title : 'Item Permintaan', icon:'thumbnails', 'callback' : this.showDetailMode },
                { title : 'Reset', icon:'refresh', 'callback' : this.resetSelectedData },
            ],

            searchUrl : '/purchases/requests/PENGAJUAN',
            searchUrlDetail : '/purchases/requests/details/PENGAJUAN',
            
            dataLists : null,
            detailLists : null,
            selectedDetails : [],
        }
    },
    watch :{         
    },
    computed : {
        ...mapGetters(['errors']),
    },
    mounted() {
        // this.initialize();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        showModalItemRequest() {
            if(this.isListMode) { this.$refs.tablePurchaseRequest.retrieveData(); }
            else { this.$refs.tablePurchaseRequestDetail.retrieveData(); }
            UIkit.modal(`#modalItemRequest`).show();
        },

        closeModalItemRequest() {
            UIkit.modal(`#modalItemRequest`).hide();
        },

        showDetailMode(){ this.isListMode = false; },
        showListMode(){ this.isListMode = true; },

        updateListData(data){
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        updateListDataDetail(data){
            this.detailLists = null;
            if(data) {  this.detailLists = data.data; }
        },

        callbackFunction(data) {
            if(data) {
                this.selectedDetails.push(JSON.parse(JSON.stringify(data)));
            }
        },

        removeFunction(data) {
            if(data) {
                this.selectedDetails = this.selectedDetails.filter(item => { return item.produk_id !== data.produk_id; });
            }
        },

        resetSelectedData(){
            this.selectedDetails = [];
            this.detailLists = null;
            this.$refs.tablePurchaseRequestDetail.retrieveData();
        },

        itemSelected(data){
            this.$emit('itemSelected',data);
        },
        // initialize() {
        //     this.idModal = `modalpicker-${Date.now()}`;
        // },
        // changeView(data){
        //     this.isTableView = data;
        // },
        // rowNumberChange(data){
        //     this.params.per_page = data;
        //     this.retrieveData();
        // },
        // pageChange(data) {
        //     this.params.page = data;
        //     this.retrieveData();
        // },

        // selectedDataChange(data){
        //     this.selectedDatas = data;
        // },

        // dataChange(data) {
        //     this.selectedDatas = data;
        // },

        // showModalPicker(url) {
        //     if(url) { this.urlData = url };
        //     this.retrieveData();
        //     UIkit.modal(`#${this.idModal}`).show();
        // },

        // closeModalPicker() {
        //     UIkit.modal(`#${this.idModal}`).hide();
        // },

        // selectAllChange(){
        //     if(this.isSelectAll) {
        //         this.datas = this.listItems;
        //     }
        //     else {
        //         this.datas = [];
        //     }
        // },

        // /**ambil data berdasarkan url search yang diberikan**/
        // retrieveData() {
        //     this.CLR_ERRORS();  
        //     this.listItems = [];
        //     if(this.retrieveAll) { this.params.per_page = 'ALL'; }
        //     if(this.urlData == '' || this.urlData == null) { return false; }
            
        //     httpReq.get(this.urlData, { params : this.params })
        //     .then((response) => {
        //         if (response.data.success == false) {
        //             this.$store.commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //         } else {
        //             this.listItems = response.data.data.data;
        //             this.pagination.current_page = response.data.data.current_page;
        //             this.pagination.last_page = response.data.data.last_page;
        //             this.pagination.per_page = response.data.data.per_page;
        //             this.pagination.total_data = response.data.data.total;
        //             this.pagination.num_pages = response.data.data.last_page;
        //         }
        //     })            
        //     .catch((error) => {
        //         this.$store.commit('SET_ERRORS', { invalid: error }, { root: true });
        //     })
        // },

        // nextPage() {
        //     this.params.page = this.params.page + 1;
        //     this.retrieveData();
        // },

        // getValue(row,col) {
        //     let data = row;
        //     let cols = col.split('.');
        //     if(cols.length > 0) { cols.forEach(el => { data = data[el]; }); } 
        //     else { data = row.col; }
        //     return data;
        // },
    }

}
</script>