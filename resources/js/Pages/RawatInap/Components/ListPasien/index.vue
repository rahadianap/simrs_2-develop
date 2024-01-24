<template>
    <div class="uk-container uk-container-xlarge uk-grid-small" uk-grid style="padding:0.5em 0.5em 0.5em 1em;">
        <div class="uk-width-1-1" style="margin:0;padding:0;">
            <h5 style="font-weight: 500;margin:0.5em 0 0 1em;">{{asalPasien}}</h5>
        </div>
        <template v-if="asalPasien == 'MASTER PASIEN'">
            <div class="uk-width-1-1" style="margin:0;padding:0 1em 0.5em 1em;">
                <base-table ref="tableMasterPasien"
                    :columns = "tbBaseColumns" 
                    :config = "configBaseTable"
                    :buttons = "buttons"
                    :urlSearch="masterPasienUrl" v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="pasienLists">
                            <row-pasien v-for="dt in pasienLists" :rowData="dt" :rowFunctions = rowFunctions></row-pasien>
                        </tbody>
                    </template>
                </base-table>
            </div>
        </template>
        <template v-else>
            <div class="uk-width-1-1" style="margin:0;padding:0 1em 0.5em 1em;">
                <base-table ref="tablePasienPoli"
                    :columns = "tbBaseColumns" 
                    :config = "configBaseTable" 
                    :buttons = "buttons"
                    :urlSearch="pasienPoliUrl" v-on:updateListDataTable="updateListData">
                </base-table>
            </div>
        </template>
        <modal-form-pasien ref="modalPasien" v-on:modalPasienClosed="refreshPasien"></modal-form-pasien>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import RowPasien from '@/Pages/RawatInap/Components/ListPasien/RowPasien.vue';
import ModalFormPasien from '@/Pages/RawatInap/Components/ListPasien/ModalFormPasien.vue';


export default {
    name :'list-pasien',
    components : { BaseTable,RowPasien,ModalFormPasien },
    data() {
        return {
            isUpdate : false,
            isLoading : true,
            masterPasienUrl : '',
            pasienPoliUrl : '',
            asalPasien : 'MASTER PASIEN',
            
            configBaseTable : { isExpandable : false, filterType : 'SIMPLE', },
            rowFunctions :[
                { 'title':'Pilih Pasien', 'icon':'check','callback':this.pasienSelected },
                { 'title':'Ubah Data Pasien', 'icon':'file-edit','callback':this.editDataPasien },
            ],
            tbBaseColumns : [
                { name : 'no_rm', title : 'No.RM',colType : 'linkmenus', 
                  functions: [
                        { 'title':'Pilih Pasien', 'icon':'check','callback':this.pasienSelected },
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
                { title : 'Master Pasien', icon:'list', 'callback' : this.showMasterPasien },
                { title : 'Pasien Poli (IGD)', icon:'users', 'callback' : this.showPasienPoli },
                { title : 'Pasien Baru', icon:'plus-circle', 'callback' : this.addPasien },                
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

        showMasterPasien(){
            this.asalPasien = 'MASTER PASIEN';
            this.masterPasienUrl = '/patients';
        },

        showPasienPoli(){
            this.asalPasien = 'PASIEN POLI / IGD';
            this.pasienPoliUrl = '/registrations';
        },

        addPasien(){
            this.CLR_ERRORS();
            this.$refs.modalPasien.createNewPasien();
        },

        editDataPasien(data){
            if(data){
                this.CLR_ERRORS();
                this.$refs.modalPasien.editDataPasien(data.pasien_id);
            }
        },

        pasienSelected(data){
            if(data) {
                this.$emit('pasienInapSelected',data);
            }
        },

        refreshPasien(){

        }
    }
}
</script>