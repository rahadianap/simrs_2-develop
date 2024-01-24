<template>
    <div class="uk-modal uk-modal-container" id="modalOrderLists">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Pilih Pembelian</h2>            
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            <div style="margin-top:0;padding:0;">
                <base-table ref="tablePurchaseRequest"
                    :columns = "tbRequestColumns" 
                    :config = "configTable"
                    :urlSearch="searchUrl"  v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="dataLists" style="min-weight:50vh;">
                            <row-list-order
                                v-for="dt in dataLists" 
                                :rowData="dt" 
                                :rowFunctions = "itemSelected">
                            </row-list-order>
                        </tbody>
                    </template>
                </base-table>        
            </div>
        </div>
            
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import RowListOrder from '@/Pages/Persediaan/PurchaseReceive/components/RowListOrder.vue';

export default {
    name : 'modal-list-order',
    components : {
        BaseTable,
        RowListOrder,
    },

    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'SIMPLE', },

            tbRequestColumns : [                
                { name : 'trx_id', title : 'ID.PO', colType : 'linkmenus', },
                { name : 'unit_nama', title : 'Unit', colType : 'text', },
                { name : 'depo_nama', title : 'Depo', colType : 'text', },
                { name : 'tgl_dibutuhkan', title : 'Tgl. Butuh', colType : 'text', },
                { name : 'termin', title : 'Pembayaran', colType : 'text', },
                { name : 'catatan', title : 'Catatan', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text',  },
                { name : 'is_aktif', title : 'Aktif',  colType : 'boolean', },                
            ], 

            searchUrl : '/purchases/orders/PEMBELIAN',            
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
        ...mapActions('purchaseOrder', ['dataPO']),
        ...mapMutations(['CLR_ERRORS']),

        showModalItemRequest() {
            this.$refs.tablePurchaseRequest.retrieveData();
            UIkit.modal(`#modalOrderLists`).show();
        },

        closeModalItemRequest() {
            UIkit.modal(`#modalOrderLists`).hide();
        },

        updateListData(data){
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        itemSelected(data){
            this.$emit('poSelected',data);
        },

        
    }

}
</script>