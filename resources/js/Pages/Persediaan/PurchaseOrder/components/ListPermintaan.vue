<template>
    <div class="uk-container uk-container-large" style="padding:0;">
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
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
                                :rowData="dt">
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
                    :buttons = "detailButtons"
                    :urlSearch="searchUrlDetail"  v-on:updateListDataTable="updateListDataDetail">
                    <template v-slot:tbl-body>
                        <tbody v-if="detailLists" style="min-weight:50vh;">
                            <row-permintaan-detail
                                v-for="dt in detailLists" 
                                :rowData="dt"
                                :removeSelected="removeFunction" 
                                :addSelected="callbackFunction">
                            </row-permintaan-detail>
                        </tbody>
                    </template>
                </base-table>        
            </div>
        </template>
    </div>
    <form-purchase-request ref="formPurchaseRequest" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-purchase-request>
    <form-purchase-request-to-purchase-order ref="formPurchaseRequestToPurchaseOrder" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-purchase-request-to-purchase-order>
    <form-req-detail-to-order ref="formReqDetailToOrder"></form-req-detail-to-order>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormPurchaseRequest from '@/Pages/Persediaan/PurchaseRequest/components/FormPurchaseRequest.vue';
    import FormPurchaseRequestToPurchaseOrder from '@/Pages/Persediaan/PurchaseRequest/components/FormPurchaseRequestToPurchaseOrder.vue';
    import RowPermintaan from '@/Pages/Persediaan/PurchaseOrder/components/RowPermintaan.vue';
    import RowPermintaanDetail from '@/Pages/Persediaan/PurchaseOrder/components/RowPermintaanDetail.vue';
    
    import FormReqDetailToOrder from '@/Pages/Persediaan/PurchaseOrder/components/FormReqDetailToOrder.vue';
    
    export default {
        name : 'list-permintaan',
        components : {
            headerPage,
            BaseTable,
            FormPurchaseRequest,
            FormPurchaseRequestToPurchaseOrder,
            RowPermintaan,
            RowPermintaanDetail,
            FormReqDetailToOrder,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isExpanded : false,
                isListMode : true,
                configTable : { isExpandable : false, filterType : 'REGULAR', },
                configTableDetail : { isExpandable : false, filterType : 'SIMPLE', },

                rowRequestFunctions : [
                    { 'title':'Create PO', 'icon':'cart','callback':this.createPOFromPR },
                    // { 'title':'Ubah', 'icon':'file-edit','callback':this.editPR },
                    // { 'title':'Approve', 'icon':'check','callback':this.approvePR },
                    // { 'title':'Hapus', 'icon':'close','callback':this.cancelPR },
                ],
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
                    
                    // { name : 'depo_nama', title : 'Depo', colType : 'text', },
                    // { name : 'jml_pr', title : 'Jumlah', colType : 'text', },
                    // { name : 'status', title : 'Status', colType : 'text', textAlign : 'center', },
                    // { name : 'vendor_nama', title : 'Vendor', colType : 'text', },
                    // { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
                ],

                buttons : [
                    { title : 'PR Baru', icon:'plus-circle', 'callback' : this.addPurchaseRequest },
                    { title : 'Item Detail', icon:'thumbnails', 'callback' : this.showDetailMode },
                ],
                detailButtons : [
                    { title : 'Buat PO', icon:'plus-circle', 'callback' : this.createPurchaseOrder },
                    { title : 'PR List', icon:'settings', 'callback' : this.showListMode },
                    { title : 'Reset', icon:'refresh', 'callback' : this.resetSelectedData },
                ],

                searchUrl : '/purchases/requests',
                searchUrlDetail : '/purchases/requests/details',
                
                dataLists : null,
                detailLists : null,
                selectedDetails : [],
             }
        },
        methods: {
            ...mapActions('purchaseRequest',['listPR','deletePR', 'updateApprovePR', 'updateCancelPR']),
            ...mapActions('cetakan',['dataPR']),
            ...mapMutations(['CLR_ERRORS']),
            
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

            editPR(data){
                if(data){
                    if (data.status !== 'PERMINTAAN') {
                        alert('Data tidak bisa diubah karena telah berubah status.'); return false;
                    }
                    this.$refs.formPurchaseRequest.editPurchaseRequest(data.pr_id); 
                }
            },

            /**tampikan modal untuk membuat distGizi baru */
            addPurchaseRequest(){        
                this.CLR_ERRORS();
                this.$refs.formPurchaseRequest.newPurchaseRequest();        
            },

            refreshTable() {
                this.$refs.tablePurchaseRequest.retrieveData();
            },
    
            viewData(data){
                this.CLR_ERRORS();
                this.$refs.formPurchaseRequest.viewPR(data.pr_id);
            },

            createPOFromPR(data){
                // console.log(data);
                if(data.status == 'PERMINTAAN'){
                    alert('Dokumen masih dalam status permintaan, silahkan approve terlebih dahulu untuk membuat PO.');
                    return false;
                }else if(data.status == 'PO'){
                    alert('Dokumen sudah diproses.');
                    return false;
                }
                this.CLR_ERRORS();                
                this.$refs.formPurchaseRequestToPurchaseOrder.newPurchaseOrderPR(data.pr_id);
            },

            approvePR(data){
                if (data.status !== 'PERMINTAAN') {
                    alert('Data tidak bisa diubah karena telah diproses.');
                    return false;
                }
                if(confirm(`Proses approval akan mengunci permintaan dan tidak dapat diubah lagi. Lanjutkan ?`)){
                    this.updateApprovePR(data.pr_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tablePurchaseRequest.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            },

            cancelPR(data){
                if (data.status == 'PERMINTAAN' || data.status == 'PENGAJUAN') {
                    this.CLR_ERRORS();
                    if(confirm(`Proses ini akan menghapus data permintaan pembelian. Lanjutkan?`)){
                        this.updateCancelPR(data.pr_id).then((response) => {
                            if (response.success == true) {
                                alert(response.message);
                                this.$refs.tablePurchaseRequest.retrieveData();
                            }
                            else { alert (response.message) }
                        });
                    };
                }
                else {
                    alert('Data tidak bisa diubah karena telah diproses.');
                    return false;
                }
                
            },

            cetakDataPR(data){
                this.dataPR(data.pr_id).then((response) => {
                    if (response.success == true) { this.$refs.cetakanPurchaseRequest.generateReport(response.data); }
                    else { alert (response.message) }
                });
            },

            createPurchaseOrder(){
                alert('create purchase order');
                
            }
        }
    }
</script>