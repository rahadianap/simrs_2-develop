<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" :id="modalId" style="min-height:50vh;padding:0;margin:0;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Master Data Pasien</h2>
            <div class="hims-form-container" style="padding:0; margin:0;">
                <base-table ref="tableMasterPasien"
                    :columns = "tbBaseColumns" 
                    :config = "configBaseTable"
                    :buttons = "buttons"
                    :urlSearch="masterPasienUrl" 
                    v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="pasienLists">
                            <row-pasien v-for="dt in pasienLists" :rowData="dt" :rowFunctions = rowFunctions></row-pasien>
                        </tbody>
                    </template>
                </base-table>
            </div>
            <div style="padding-top:1em;">
                <p class="uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">TUTUP</button>
                </p>
            </div>

        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import RowPasien from '@/Pages/Pendaftaran/RawatJalan/components/ListPasienPoli/RowPasien.vue';

export default {
    props : {
        modalId : { type:String, required:true },
    },
    name : 'modal-pasien',
    components : { BaseTable,RowPasien },
    data() {
        return {
            isUpdate : false,
            isLoading : true,
            masterPasienUrl : '/patients',
            
            configBaseTable : { isExpandable : false, filterType : 'SIMPLE', },
            rowFunctions :[
                { 'title':'Booking Antrian', 'icon':'calendar','callback':this.pasienBookingSelected },
                { 'title':'Daftar Rawat Jalan', 'icon':'check','callback':this.pasienSelected },
                { 'title':'Ubah Data Pasien', 'icon':'file-edit','callback':this.editDataPasien },
            ],
            tbBaseColumns : [
                { name : 'no_rm', title : 'No.RM',colType : 'linkmenus', 
                  functions: [
                        { 'title':'Daftar Rawat Jalan', 'icon':'check','callback':this.pasienSelected },
                        { 'title':'Ubah Data Pasien', 'icon':'file-edit','callback':this.editDataPasien },
                    ],
                },
                { name : 'nama_lengkap', title : 'Nama Pasien', colType : 'text', }, 
                { name : 'tempat_lahir', title : 'Kelahiran', colType : 'text', },
                { name : 'nik', title : 'NIK/KK', colType : 'text', },
                { name : 'jns_penjamin', title : 'Penjamin', colType : 'text',},                
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }        
            ], 

            buttons : [
                // { title : 'Master Pasien', icon:'list', 'callback' : this.showMasterPasien },
                // { title : 'Pasien Poli (IGD)', icon:'users', 'callback' : this.showPasienPoli },
                // { title : 'Pasien Baru', icon:'plus-circle', 'callback' : this.addPasien },                
            ],
            pasienLists : null,
        }
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data){
            this.pasienLists = null;
            if(data) { this.pasienLists = data.data;}
        },

        showModal(){
            UIkit.modal(`#${this.modalId}`).show();
        },
        closeModal(){
            this.$emit('modalPasienClosed',true);
            UIkit.modal(`#${this.modalId}`).hide();
        },
        selectedPasien(data){
            if(data) {
                this.$emit('pasienSelected',data);
                UIkit.modal(`#${this.modalId}`).hide();
            }
        }
        
    }
}
</script>