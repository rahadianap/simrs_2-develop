<template>
    <div class="uk-container uk-container-xlarge">
        <div class="uk-width-1-1" style="margin:0;padding:0;">
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
        <modal-form-pasien ref="modalFormPasien"></modal-form-pasien>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import RowPasien from '@/Pages/Pendaftaran/RawatJalan/components/ListPasienPoli/RowPasien.vue';
import ModalFormPasien from '@/Pages/Pendaftaran/RawatJalan/components/ListPasienPoli/ModalFormPasien.vue';

export default {
    name :'list-pasien',
    components : { BaseTable,RowPasien,ModalFormPasien },
    data() {
        return {
            isUpdate : false,
            isLoading : true,
            masterPasienUrl : '/patients',
            
            configBaseTable : { isExpandable : false, filterType : 'REGULAR', },
            rowFunctions :[
                { 'title':'Booking Antrian', 'icon':'calendar','callback':this.pasienBookingSelected },
                { 'title':'Daftar Rawat Jalan', 'icon':'check','callback':this.pasienSelected },
                { 'title':'Ubah Data Pasien', 'icon':'file-edit','callback':this.editDataPasien },
            ],
            tbBaseColumns : [
                { name : 'no_rm', title : 'No.RM',colType : 'linkmenus', },
                { name : 'nama_lengkap', title : 'Nama Pasien', colType : 'text', }, 
                { name : 'tempat_lahir', title : 'Kelahiran', colType : 'text', },
                { name : 'nik', title : 'NIK/KK', colType : 'text', },
                { name : 'jns_penjamin', title : 'Penjamin', colType : 'text',},                
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }        
            ], 
            buttons : [
                { title : 'Pasien Baru', icon:'plus-circle', 'callback' : this.addPasien },
            ],
            pasienLists : null,
        }
    },
    mounted(){
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data){
            this.pasienLists = null;
            if(data) { this.pasienLists = data.data;}
        },

        addPasien(){
            this.CLR_ERRORS();
            this.$refs.modalFormPasien.createNewPasien();
        },

        editDataPasien(data){
            if(data){
                this.CLR_ERRORS();
                this.$refs.modalFormPasien.editDataPasien(data.pasien_id);
            }
        },

        pasienSelected(data){
            if(data) {
                this.$router.push({ name: 'formAntrianPoli', params: { tipe:'pasien', dataId: data.pasien_id } });
            }
        },

        pasienBookingSelected(data) {
            if(data) {
                this.$router.push({ name: 'formBookingPoli', params: { tipe:'pasien', dataId: data.pasien_id } });
            }
        },
    }
}
</script>