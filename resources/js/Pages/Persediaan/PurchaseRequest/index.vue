<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="PURCHASE REQUEST" subTitle="permintaan pembelian item persediaan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tablePurchaseRequest"
                :columns = "tbColumns" 
                :config="configTable"
                :buttons = "buttons"
                :urlSearch="searchUrl"  v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists" style="min-weight:50vh;">
                        <row-purchase-request
                            v-for="dt in dataLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-purchase-request>
                    </tbody>
                </template>
            </base-table>
        </div>
    </div>
    <form-purchase-request ref="formPurchaseRequest" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-purchase-request>
    <form-purchase-request-to-purchase-order ref="formPurchaseRequestToPurchaseOrder" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-purchase-request-to-purchase-order>
    <cetakan-purchase-request ref="cetakanPurchaseRequest"></cetakan-purchase-request>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormPurchaseRequest from '@/Pages/Persediaan/PurchaseRequest/components/FormPurchaseRequest.vue';
    import FormPurchaseRequestToPurchaseOrder from '@/Pages/Persediaan/PurchaseRequest/components/FormPurchaseRequestToPurchaseOrder.vue';
    import CetakanPurchaseRequest from '@/Pages/Persediaan/PurchaseRequest/cetakan/cetakanPurchaseRequest.vue';
    import RowPurchaseRequest from '@/Pages/Persediaan/PurchaseRequest/components/RowPurchaseRequest.vue';
    
    export default {
        components : {
            headerPage,
            BaseTable,
            FormPurchaseRequest,
            FormPurchaseRequestToPurchaseOrder,
            CetakanPurchaseRequest,
            RowPurchaseRequest,
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
                rowFunctions : [
                    { 'title':'Ubah', 'icon':'file-edit','callback':this.editPR },
                    { 'title':'Approve', 'icon':'check','callback':this.approvePR },
                    // { 'title':'Create PO', 'icon':'cart','callback':this.createPOFromPR },
                    { 'title':'Hapus', 'icon':'close','callback':this.cancelPR },
                    { 'title':'Cetak Data', 'icon':'file-pdf','callback':this.cetakDataPR },
                ],
                tbColumns : [                
                    { 
                        name : 'pr_id', 
                        title : 'ID PERMINTAAN', 
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Ubah', 'icon':'check','callback':this.editPR },
                            { 'title':'Approve', 'icon':'check','callback':this.approvePR },
                            { 'title':'Lihat Detail', 'icon':'file-edit','callback':this.viewData },
                            { 'title':'Create PO', 'icon':'cart','callback':this.createPOFromPR },
                            { 'title':'Cancel', 'icon':'close','callback':this.cancelPR },
                            { 'title':'Cetak Data', 'icon':'file-pdf','callback':this.cetakDataPR },
                        ],
                    },
                    { 
                        name : 'unit_nama', 
                        title : 'Unit', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'depo_nama', 
                        title : 'Depo', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'tgl_dibutuhkan', 
                        title : 'Tgl. Butuh', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'catatan', 
                        title : 'Catatan', 
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
                    { title : 'PR Baru', icon:'plus-circle', 'callback' : this.addPurchaseRequest },
                ],
                searchUrl : '/purchases/requests',
                dataLists : null,
             }
        },
        methods: {
            ...mapActions('purchaseRequest',['listPR','deletePR', 'updateApprovePR', 'updateCancelPR']),
            ...mapActions('cetakan',['dataPR']),
            ...mapMutations(['CLR_ERRORS']),
            
            updateListData(data){
                this.dataLists = null;
                if(data) { this.dataLists = data.data; }
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
                console.log(data);
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
                    if (response.success == true) {
                        this.$refs.cetakanPurchaseRequest.generateReport(response.data);
                    }
                    else { alert (response.message) }
                });
            }
        }
    }
</script>