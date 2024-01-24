<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" :id="modalId" style="min-height:50vh;padding:0;margin:0;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Item Resep</h2>
            <div class="hims-form-container" style="padding:0; margin:0;">
                <base-table ref="tableModalResep"
                    :columns = "tbBaseColumns" 
                    :config = "configBaseTable"
                    :urlSearch="masterUrl" 
                    v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="itemLists">
                            <modal-resep-row 
                                v-for="dt in itemLists" 
                                :rowData="dt" 
                                :fnActSelected="fnSelected">
                        </modal-resep-row>
                        </tbody>
                    </template>
                </base-table>
            </div>
            <div style="padding-top:1em;">
                <p class="uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button" >TUTUP</button>
                </p>
            </div>

        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import ModalResepRow from '@/Pages/RawatJalan/components/ModalResep/ModalResepRow.vue';

export default {
    props : {
        modalId : { type:String, required:true },
        fnSelected : {type:Function, required : true },
    },
    name : 'modal-resep',
    components : { 
        BaseTable,
        ModalResepRow 
    },
    data() {
        return {
            isUpdate : false,
            isLoading : true,
            masterUrl : '',
            
            configBaseTable : { isExpandable : false, filterType : 'SIMPLE', },            
            tbBaseColumns : [
                { name : 'produk_id', title : 'ID',colType : 'linkmenus', },
                { name : 'produk_nama', title : 'Nama', colType : 'text', }, 
                { name : 'jenis_produk', title : 'Jenis', colType : 'text', },
                { name : 'satuan', title : 'Satuan', colType : 'text', },
                { name : 'is_aktif', title : '...', colType : 'text', },
            ],
            itemLists : null,
        }
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data){
            this.itemLists = null;
            if(data) { this.itemLists = data.data;}
        },

        showModal(searchUrl){
            this.masterUrl = searchUrl;
            UIkit.modal(`#${this.modalId}`).show();
        },
        closeModal(){
            this.$emit('modalActClosed',true);
            UIkit.modal(`#${this.modalId}`).hide();
        },

        selectedTindakan(data){
            if(data) {
                this.$emit('actSelected',data);
                UIkit.modal(`#${this.modalId}`).hide();
            }
        }
    }
}
</script>