<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" :id="modalId" style="min-height:50vh;padding:0;margin:0;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Item BHP</h2>
            <div class="hims-form-container" style="padding:0; margin:0;">
                <base-table ref="tableMasterPasien"
                    :columns = "tbBaseColumns" 
                    :config = "configBaseTable"
                    :urlSearch="masterActUrl" 
                    v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="bhpLists">
                            <modal-bhp-row 
                                v-for="dt in bhpLists" 
                                :rowData="dt" 
                                :fnActSelected="fnSelected">
                        </modal-bhp-row>
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
import ModalBhpRow from '@/Pages/RawatJalan/components/ModalBhp/ModalBhpRow.vue';

export default {
    props : {
        modalId : { type:String, required:true },
        fnSelected : {type:Function, required : true },
    },
    name : 'modal-signa',
    components : { 
        BaseTable,
        ModalBhpRow 
    },
    data() {
        return {
            isUpdate : false,
            isLoading : true,
            masterActUrl : '',
            
            configBaseTable : { isExpandable : false, filterType : 'SIMPLE', },            
            tbBaseColumns : [
                { name : 'produk_id', title : 'ID',colType : 'linkmenus', },
                { name : 'produk_nama', title : 'Nama', colType : 'text', }, 
                { name : 'jenis_produk', title : 'Jenis', colType : 'text', },
                { name : 'satuan', title : 'Satuan', colType : 'text', },
                { name : 'is_aktif', title : '...', colType : 'text', },
            ],
            bhpLists : null,
        }
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data){
            this.bhpLists = null;
            if(data) { this.bhpLists = data.data;}
        },

        showModal(searchUrl){
            this.masterActUrl = searchUrl;
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