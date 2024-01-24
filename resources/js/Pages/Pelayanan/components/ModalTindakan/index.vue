<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" :id="modalId" style="min-height:50vh;padding:0;margin:0;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Tindakan / Pemeriksaan</h2>
            <div class="hims-form-container" style="padding:0; margin:0;">
                <base-table ref="tableMasterPasien"
                    :columns = "tbBaseColumns" 
                    :config = "configBaseTable"
                    :urlSearch="masterActUrl" 
                    v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="actLists">
                            <modal-tindakan-row 
                                v-for="dt in actLists" 
                                :rowData="dt" 
                                :fnActSelected="fnSelected">
                        </modal-tindakan-row>
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
import ModalTindakanRow from '@/Pages/Pelayanan/components/ModalTindakan/ModalTindakanRow.vue';

export default {
    props : {
        modalId : { type:String, required:true },
        fnSelected : {type:Function, required : true },
    },
    name : 'modal-tindakan',
    components : { 
        BaseTable,
        ModalTindakanRow 
    },
    data() {
        return {
            isUpdate : false,
            isLoading : true,
            masterActUrl : '',
            
            configBaseTable : { isExpandable : false, filterType : 'SIMPLE', },
            rowFunctions :[
                { 'title':'Booking Antrian', 'icon':'calendar','callback':this.pasienBookingSelected },
                { 'title':'Daftar Rawat Jalan', 'icon':'check','callback':this.pasienSelected },
                { 'title':'Ubah Data Pasien', 'icon':'file-edit','callback':this.editDataPasien },
            ],
            tbBaseColumns : [
                { name : 'tindakan_id', title : 'ID',colType : 'linkmenus', 
                  functions: [
                        { 'title':'Daftar Rawat Jalan', 'icon':'check','callback':this.pasienSelected },
                        { 'title':'Ubah Data Pasien', 'icon':'file-edit','callback':this.editDataPasien },
                    ],
                },
                { name : 'tindakan_nama', title : 'Nama Pemeriksaan', colType : 'text', }, 
                { name : 'deskripsi', title : 'Deskripsi', colType : 'text', },
                { name : 'is_aktif', title : '...', colType : 'text', },
            ],
            actLists : null,
        }
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data){
            this.actLists = null;
            if(data) { this.actLists = data.data;}
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