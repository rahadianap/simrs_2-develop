<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" :id="modalId" style="min-height:50vh;padding:0;margin:0;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Signa</h2>
            <div class="hims-form-container" style="padding:0; margin:0;">
                <base-table ref="tableMasterSigna"
                    :columns = "tbBaseColumns" 
                    :config = "configBaseTable"
                    :urlSearch="masterSignaUrl" 
                    v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="signaLists">
                            <tr v-for="signa in signaLists">
                                <td style="width:5px;"></td>
                                <td><p style="margin:0;padding:0;font-weight: 500;">{{ signa.signa }}</p></td>
                                <td><p style="margin:0;padding:0;font-weight: 400;">{{ signa.deskripsi }}</p></td>
                                <td style="width:60px;"><button @click.prevent="fnSelected(signa)">Pilih</button></td>                                
                            </tr>
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
// import ModalSigna from '@/Pages/RawatJalan/components/ModalSigna/index.vue';

export default {
    props : {
        modalId : { type:String, required:true },
        fnSelected : {type:Function, required : true },
    },
    name : 'modal-signa',
    components : { 
        BaseTable,
        // ModalBhpRow 
    },
    data() {
        return {
            isUpdate : false,
            isLoading : true,
            masterSignaUrl : '/signas',
            
            configBaseTable : { isExpandable : false, filterType : 'SIMPLE', },            
            tbBaseColumns : [
                { name : 'signa', title : 'Signa',colType : 'text', },
                { name : 'deskripsi', title : 'Deskripsi', colType : 'text', }, 
                { name : 'is_aktif', title : '...', colType : 'text', },
            ],
            signaLists : null,
        }
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data){
            this.signaLists = null;
            if(data) { this.signaLists = data.data;}
        },

        showModal(){
            UIkit.modal(`#${this.modalId}`).show();
        },
        closeModal(){
            this.$emit('modalActClosed',true);
            UIkit.modal(`#${this.modalId}`).hide();
        },

        selectedTindakan(data){
            if(data) {
                this.$emit('signaSelected',data);
                UIkit.modal(`#${this.modalId}`).hide();
            }
        }
        
    }
}
</script>