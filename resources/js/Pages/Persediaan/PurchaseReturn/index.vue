
<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <ul id="mainTabPOR" uk-tab style="padding:0;" class="uk-hidden">
            <li><a href="#" style="font-size:14px;font-weight:500;">ITEM 1</a></li>
            <li><a href="#" style="font-size:14px;font-weight:500;">ITEM 2</a></li>           
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li>
                <div class="uk-container uk-container-xlarge" style="padding:0;">
                    <header-page title="PURCHASE ORDER RETURN" subTitle="pengembalian penerimaan pembelian item persediaan"></header-page>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:1em;">
                        <base-table ref="tablePurchaseReturn"
                            :columns = "tbColumns" 
                            :config="configTable"
                            :urlSearch="searchUrl"
                            :buttons = "buttons"  v-on:updateListDataTable="updateListData">
                            <template v-slot:tbl-body>
                                <tbody v-if="dataLists" style="min-weight:50vh;">
                                    <row-purchase-return
                                        v-for="dt in dataLists" 
                                        :rowData="dt" :rowFunctions = "editPurchaseReturn">
                                    </row-purchase-return>
                                </tbody>
                            </template>
                        </base-table>
                    </div>
                </div>
            </li>
            <li>
                <form-purchase-return ref="formPurchaseReturn" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-purchase-return>
            </li>
        </ul>
    </div>
    
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormPurchaseReturn from '@/Pages/Persediaan/PurchaseReturn/components/FormPurchaseReturn.vue';
import RowPurchaseReturn from '@/Pages/Persediaan/PurchaseReturn/components/RowPurchaseReturn.vue';

export default {
    components : {
            headerPage,
            BaseTable,
            FormPurchaseReturn,
            RowPurchaseReturn
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isExpanded : false,
                configTable : {                
                    isExpandable : false,
                    filterType : 'SIMPLE', 
                },
                
                tbColumns : [                
                    { 
                        name : 'trx_id', 
                        title : 'ID', 
                        colType : 'linkmenus',
                    },
                    { 
                        name : 'unit_nama', 
                        title : 'Unit /Depo Penerimaan', 
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
                        name : 'total', 
                        title : 'Nilai', 
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
                    { title : 'PORR Baru', icon:'plus-circle', 'callback' : this.addPurchaseReceive },
                ],
                searchUrl : '/purchases/returns',
                dataLists : null,
             }
        },
        methods: {
            ...mapActions('purchaseReceive',['listPOR','deletePOR', 'updateApprovePOR', 'updateCancelPOR', 'updateProsesPOR']),
            ...mapMutations(['CLR_ERRORS']),

            updateListData(data){
                this.dataLists = null;
                if(data) { this.dataLists = data.data; }
            },

            refreshTable() {
                this.$refs.tablePurchaseReturn.retrieveData();
                UIkit.switcher('#mainTabPOR').show(0);
            },

            addPurchaseReceive() {
                this.$refs.formPurchaseReturn.newReceive();
                UIkit.switcher('#mainTabPOR').show(1);
            },

            editPurchaseReturn(data) {
                console.log(data);
                if(data) {
                    UIkit.switcher('#mainTabPOR').show(1);
                    this.$refs.formPurchaseReturn.viewReceive(data);
                }
            }
        }
}
</script>

<!-- <template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="PENGEMBALIAN PEMBELIAN PERSEDIAAN" subTitle="pengembalian pembelian item persediaan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tablePurchaseReturn"
                :columns = "tbColumns" 
                :config="configTable"
                :urlSearch="searchUrl"
                :buttons = "buttons">
            </base-table>
        </div>
    </div>
    <form-purchase-return ref="formPurchaseReturn" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-purchase-return>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormPurchaseReturn from '@/Pages/Persediaan/PurchaseReturn/components/FormPurchaseReturn.vue';

export default {
    components : {
            headerPage,
            BaseTable,
            FormPurchaseReturn
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
                            { 'title':'Return PO', 'icon':'check','callback':this.returnPO },
                        ],
                    },
                    { 
                        name : 'no_transaksi', 
                        title : 'Nomor Transaksi', 
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
                        name : 'vendor_id', 
                        title : 'Vendor', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'depo_id', 
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
                    { title : 'PORR Baru', icon:'plus-circle', 'callback' : this.addPurchaseReturn },
                ],
                searchUrl : '/purchases/returns'
             }
        },
        methods: {
            ...mapActions('purchaseReturn',['listPORR','deletePORR', 'updateApprovePORR', 'updateCancelPORR', 'updateProsesPORR']),
            ...mapMutations(['CLR_ERRORS']),

            refreshTable() {
                this.$refs.tablePurchaseReturn.retrieveData();
            },
    
            returnPO(data){
                this.CLR_ERRORS();
                this.$refs.formPurchaseReturn.viewPO(data.pengadaan_id);
            },

            addPurchaseReturn(){
                
            }
        }
}
</script> -->