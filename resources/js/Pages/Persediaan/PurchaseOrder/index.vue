<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <ul id="mainTabPO" uk-tab style="padding:0;" class="uk-hidden">
            <li><a href="#" style="font-size:14px;font-weight:500;">ITEM 1</a></li>
            <li><a href="#" style="font-size:14px;font-weight:500;">ITEM 2</a></li>           
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li>
                <div>
                    <header-page title="PURCHASE ORDER" subTitle="daftar pembelian item persediaan"></header-page>
                    <div style="margin:0.5em 0 0 0;">
                        <div class="uk-card" style="padding:0;margin:0;border-radius:10px;min-height:400px;">                           
                            <div class="uk-container uk-container-xlarge" style="padding:0;margin:0;">
                                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                                    <p>{{ errors.invalid }}</p>
                                </div>
                                <div style="margin:0;padding:0;">
                                    <base-table ref="tablePurchaseOrder"
                                        :columns = "tbColumns" 
                                        :config="configTable"
                                        :buttons = "buttons"                                        
                                        :urlSearch="searchUrl"  v-on:updateListDataTable="updateListData">
                                        <template v-slot:tbl-body>
                                            <tbody v-if="dataLists" style="min-weight:50vh;">
                                                <row-pembelian
                                                    v-for="dt in dataLists" 
                                                    :rowData="dt" :rowFunctions = "editPO">
                                                </row-pembelian>
                                            </tbody>
                                        </template>
                                    </base-table>
                                </div>
                            </div>
                        </div>
                    </div>        
                </div>
            </li>
            <li>
                <form-purchase-order ref="formPurchaseOrder" v-on:saveSucceed="refreshTable" v-on:modalPoClosed="refreshTable"></form-purchase-order>
            </li>
        </ul>
    </div>

</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormPurchaseOrder from '@/Pages/Persediaan/PurchaseOrder/components/FormPurchaseOrder.vue';
    import ListPermintaan from '@/Pages/Persediaan/PurchaseOrder/components/ListPermintaan.vue';
    import ListPembelian from '@/Pages/Persediaan/PurchaseOrder/components/ListPembelian.vue';
    import RowPembelian from '@/Pages/Persediaan/PurchaseOrder/components/RowPembelian.vue';

    export default {
        components : {
            headerPage,
            BaseTable,
            FormPurchaseOrder,
            ListPermintaan,
            ListPembelian, 
            RowPembelian,           
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
                    { 'title':'Ubah PO', 'icon':'check','callback':this.editPO },
                    { 'title':'Proses PO', 'icon':'cart','callback':this.prosesPO },
                    { 'title':'Hapus PO', 'icon':'trash','callback':this.removePO },
                ],

                tbColumns : [                
                    { 
                        name : 'pengadaan_id', 
                        title : 'ID', 
                        colType : 'link', 
                        functions: [
                            { 'title':'Approve', 'icon':'check','callback':this.approvePO },
                            // { 'title':'Lihat Detail', 'icon':'file-edit','callback':this.viewData },
                            // { 'title':'Cancel', 'icon':'close','callback':this.cancelPO },
                            { 'title':'Order PO', 'icon':'cart','callback':this.prosesPO },
                        ],
                    },
                    { 
                        name : 'unit_nama', 
                        title : 'Unit Pengadaaan', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'vendor_nama', 
                        title : 'Vendor', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    // { 
                    //     name : 'tgl_dibutuhkan', 
                    //     title : 'Tgl.Dibutuhkan', 
                    //     colType : 'text', 
                    //     isSearchable : true,
                    // },
                    { 
                        name : 'termin_bayar', 
                        title : 'Pembayaran', 
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
                searchUrl : '/purchases/orders',
                dataLists : null,
             }
        },
        methods: {
            ...mapActions('purchaseOrder',['listPO','deletePO', 'updateApprovePO', 'updateCancelPO', 'updateProsesPO']),
            ...mapMutations(['CLR_ERRORS']),
            
            updateListData(data){
                this.dataLists = null;
                if(data){ this.dataLists = data.data; }
            },

            /**tampikan modal untuk membuat distGizi baru */
            addPurchaseOrder(){        
                this.CLR_ERRORS();
                this.$refs.formPurchaseOrder.newPurchaseOrder(); 
                UIkit.switcher('#mainTabPO').show(1);      
            },

            refreshTable() {
                this.$refs.tablePurchaseOrder.retrieveData();
                UIkit.switcher('#mainTabPO').show(0);
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
                if (data.status == 'PEMBELIAN') {
                    alert('Data tidak bisa diubah karena telah diproses.');
                    return false;
                }

                else if(data.status == 'DRAFT'){
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

            editPO(data) {
                if(data) {
                    this.CLR_ERRORS();
                    this.$refs.formPurchaseOrder.editPurchaseOrder(data.pengadaan_id); 
                    UIkit.switcher('#mainTabPO').show(1);    
                }
            },

            removePO(data) {
                if (data.status !== 'DRAFT') {
                    alert('Data tidak bisa diubah karena telah diproses.');
                    return false;
                }
                this.CLR_ERRORS();
                if(confirm(`Hapus data pembelian?`)){
                    this.deletePO(data.pengadaan_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tablePurchaseOrder.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }


        }
    }
</script>