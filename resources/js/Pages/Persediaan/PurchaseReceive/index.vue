<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <ul id="mainTabPOR" uk-tab style="padding:0;" class="uk-hidden">
            <li><a href="#" style="font-size:14px;font-weight:500;">ITEM 1</a></li>
            <li><a href="#" style="font-size:14px;font-weight:500;">ITEM 2</a></li>           
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li>
                <div class="uk-container uk-container-xlarge" style="padding:0;">
                    <header-page title="PURCHASE RECEIVE" subTitle="penerimaan pembelian item persediaan"></header-page>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:1em;">
                        <base-table ref="tablePurchaseReceive"
                            :columns = "tbColumns" 
                            :config="configTable"
                            :urlSearch="searchUrl"
                            :buttons = "buttons"  v-on:updateListDataTable="updateListData">
                            <template v-slot:tbl-body>
                                <tbody v-if="dataLists" style="min-weight:50vh;">
                                    <row-purchase-receive
                                        v-for="dt in dataLists" 
                                        :rowData="dt" :rowFunctions = "editPurchaseReceive">
                                    </row-purchase-receive>
                                </tbody>
                            </template>
                        </base-table>
                    </div>
                </div>
            </li>
            <li>
                <form-purchase-receive ref="formPurchaseReceive" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-purchase-receive>
            </li>
        </ul>
    </div>
    
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormPurchaseReceive from '@/Pages/Persediaan/PurchaseReceive/components/FormPurchaseReceive.vue';
import RowPurchaseReceive from '@/Pages/Persediaan/PurchaseReceive/components/RowPurchaseReceive.vue';

export default {
    components : {
            headerPage,
            BaseTable,
            FormPurchaseReceive,
            RowPurchaseReceive
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
                    { title : 'POR Baru', icon:'plus-circle', 'callback' : this.addPurchaseReceive },
                ],
                searchUrl : '/purchases/receives',
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
                this.$refs.tablePurchaseReceive.retrieveData();
                UIkit.switcher('#mainTabPOR').show(0);
            },

            addPurchaseReceive() {
                this.$refs.formPurchaseReceive.newReceive();
                UIkit.switcher('#mainTabPOR').show(1);
            },

            editPurchaseReceive(data) {
                // console.log(data);
                if(data) {
                    UIkit.switcher('#mainTabPOR').show(1);
                    this.$refs.formPurchaseReceive.viewReceive(data);
                }
            },
        }
}
</script>