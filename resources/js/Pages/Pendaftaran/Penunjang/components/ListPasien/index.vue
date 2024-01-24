<template>
    <div class="uk-container uk-container-xlarge uk-grid-small" uk-grid style="padding:0.5em 0.5em 0.5em 1em;">
        <div class="uk-width-1-1 uk-grid-small" uk-grid>
            <div class="uk-width-expand@m" style="margin:0;padding:0;">
                <h5 style="font-weight: 500;margin:0.5em 0 0 1em;">{{asalPasien}}</h5>
            </div>
            <div class="uk-width-auto@m">
                <div class="uk-button-group uk-box-shadow-large" style="padding: 0; border-radius: 5px;">
                    <button
                    v-for="(button, index) in switchButtons"
                    :key="index"
                    :class="{ 'hims-table-btn': true, 'hims-btn-active': activeButton === button.title }"
                    @click.prevent="button.callback"
                    >
                    <span v-if="button.icon" :uk-icon="button.icon"></span>
                    {{ button.title }}
                    </button>
                </div>
            </div>
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
        <template v-else-if=" asalPasien == 'PASIEN INAP' ">
            <div class="uk-width-1-1" style="margin:0;padding:0 1em 0.5em 1em;">
                <base-table ref="tablePasienInap"
                    :columns = "tbInapColumns" 
                    :config = "configInapTable" 
                    :buttons = "buttons"
                    :urlSearch="pasienInapUrl"
                    v-on:updateListDataTable="updateListDataInap">
                    <template v-slot:tbl-body>
                        <tbody v-if="pasienInapLists">
                            <row-pasien-inap v-for="dt in pasienInapLists" :rowData="dt" :linkCallback = "pasienInapSelected"></row-pasien-inap>
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
import RowPasien from '@/Pages/Pendaftaran/Penunjang/components/ListPasien/RowPasien.vue';
import RowPasienInap from '@/Pages/Pendaftaran/Penunjang/components/ListPasien/RowPasienInap.vue';
import ModalFormPasien from '@/Pages/Pendaftaran/Penunjang/components/ListPasien/ModalFormPasien.vue';


export default {
    name :'list-pasien',
    components : { BaseTable,RowPasien,ModalFormPasien, RowPasienInap },
    data() {
        return {
            isUpdate : false,
            isLoading : true,
            masterPasienUrl : '',
            pasienPoliUrl : '',
            pasienInapUrl : '',
            asalPasien : 'MASTER PASIEN',
            activeButton: 'Master Pasien',
            
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

            switchButtons : [
                { title : 'Master Pasien', icon:'list', 'callback' : this.showMasterPasien },
                { title : 'Pasien Inap', icon:'users', 'callback' : this.showPasienInap },
                { title : 'Pasien Poli (IGD)', icon:'users', 'callback' : this.showPasienPoli },
                // { title : 'Pasien Baru', icon:'plus-circle', 'callback' : this.addPasien },                
            ],

            buttons : [
                { title : 'Pasien Baru', icon: 'plus-circle', 'callback' : this.addPasien }
            ],

            /** list pasien  inap */
            configInapTable : { isExpandable : true, filterType : 'SIMPLE', },
            tbInapColumns : [
                { name : 'reg_id', title : 'REG. ID', colType : 'text', },
                { name : 'pasien', title : 'Pasien', colType : 'text', },
                { name : 'dokter_nama', title : 'DPJP', colType : 'text', },
                { name : 'unit_nama', title : 'Unit', colType : 'text', },
                { name : 'no_bed', title : 'Bed', colType : 'text', },
                { name : 'penjamin_nama', title : 'Penjamin', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },                
            ], 
            inapButtons : [
                { title : 'Pasien Inap Baru', icon:'plus-circle', 'callback' : this.addRegistrasi },
            ],
            pasienLists : null,
            pasienInapLists : null,
        }
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data){
            this.pasienLists = null;
            if(data) { this.pasienLists = data.data;}
        },

        updateListDataInap(data) {
            this.pasienInapLists = null;
            if(data) { this.pasienInapLists = data.data;}
        },

        showMasterPasien(){
            this.pasienLists = null;
            this.asalPasien = 'MASTER PASIEN';
            this.activeButton = 'Master Pasien';
            this.masterPasienUrl = '/patients';
        },

        showPasienInap(){
            this.pasienInapLists = null;
            this.asalPasien = 'PASIEN INAP';
            this.activeButton = 'Pasien Inap';
            this.pasienInapUrl = '/inpatients/admissions';//?status=RAWAT';
        },

        showPasienPoli(){
            this.pasienLists = null;
            this.asalPasien = 'PASIEN POLI / IGD';
            this.activeButton = 'Pasien Poli (IGD)';
            this.pasienPoliUrl = '';
        },

        addPasien(){
            this.pasienLists = null;
            this.asalPasien = 'MASTER PASIEN';
            this.masterPasienUrl = '/patients';
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
                this.$emit('pasienSelected',data);
            }
        },

        pasienInapSelected(data){
            if(data) {
                this.$emit('pasienInapSelected',data);
            }
        },

        refreshPasien(){

        }
    }
}
</script>
<style>
.hims-btn-active {
    background-color: #cc0202;
    color: white;
}
</style>