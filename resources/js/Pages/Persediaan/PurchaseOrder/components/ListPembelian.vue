<template>
    <div class="uk-container uk-container-large" style="padding:0;margin:0;">
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin:0;padding:0;">
            <base-table ref="tablePurchaseOrder"
                :columns = "tbColumns" 
                :config="configTable"
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <!-- <form-purchase-order ref="formPurchaseOrder" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-purchase-order> -->
    </div>
</template>
<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormPurchaseOrder from '@/Pages/Persediaan/PurchaseOrder/components/FormPurchaseOrder.vue';
    import ListPermintaan from '@/Pages/Persediaan/PurchaseOrder/components/ListPermintaan.vue';

    export default {
        components : {
            headerPage,
            BaseTable,
            FormPurchaseOrder,
            ListPermintaan,
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
                tbColumns : [                
                    { 
                        name : 'pengadaan_id', 
                        title : 'ID', 
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Approve', 'icon':'check','callback':this.approvePO },
                            // { 'title':'Lihat Detail', 'icon':'file-edit','callback':this.viewData },
                            // { 'title':'Cancel', 'icon':'close','callback':this.cancelPO },
                            { 'title':'Order PO', 'icon':'cart','callback':this.prosesPO },
                        ],
                    },
                    { 
                        name : 'unit_nama', 
                        title : 'Unit Pengadaan', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'tgl_transaksi', 
                        title : 'Tanggal Transaksi', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'tgl_dibutuhkan', 
                        title : 'Tanggal Dibutuhkan', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'vendor_nama', 
                        title : 'Vendor', 
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
                        name : 'catatan', 
                        title : 'Catatan', 
                        colType : 'text', 
                        isSearchable : false,
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
                    { title : 'PO Baru', icon:'plus-circle', 'callback' : this.addPurchaseOrder },
                ],
                searchUrl : '/purchases/orders'
             }
        },
        methods: {
            ...mapActions('purchaseOrder',['listPO','deletePO', 'updateApprovePO', 'updateCancelPO', 'updateProsesPO']),
            ...mapMutations(['CLR_ERRORS']),
    
            /**tampikan modal untuk membuat distGizi baru */
            addPurchaseOrder(){        
                this.CLR_ERRORS();
                this.$refs.formPurchaseOrder.newPurchaseOrder();        
            },

            refreshTable() {
                this.$refs.tablePurchaseOrder.retrieveData();
            },
    
            viewData(data){
                this.CLR_ERRORS();
                this.$refs.formPurchaseOrder.viewPO(data.pengadaan_id);
            },

            approvePO(data){
                if (data.status == 'APPROVED') {
                    alert('Data tidak bisa diubah karena telah diproses.');
                    return false;
                }
                this.CLR_ERRORS();
                if(confirm(`Approve data pembelian?`)){
                    this.updateApprovePO(data.pengadaan_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tablePurchaseOrder.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            },

            cancelPO(data){
                if (data.status == 'ORDERED') {
                    alert('Data tidak bisa diubah karena telah diproses.');
                    return false;
                }
                this.CLR_ERRORS();
                if(confirm(`Cancel data pembelian?`)){
                    this.updateCancelPO(data.pengadaan_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tablePurchaseOrder.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            },

            prosesPO(data){
                if (data.status == 'ORDERED') {
                    alert('Data tidak bisa diubah karena telah diproses.');
                    return false;
                }else if(data.status == 'DRAFT'){
                    alert('Dokumen masih dalam status draft, silahkan approve terlebih dahulu untuk memproses PO.');
                    return false;
                }
                if(confirm(`Proses data pembelian?`)){
                    this.updateProsesPO(data.pengadaan_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tablePurchaseOrder.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            },
        }
    }
</script>