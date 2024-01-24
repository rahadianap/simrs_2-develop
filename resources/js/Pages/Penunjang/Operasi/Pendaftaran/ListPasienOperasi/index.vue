<template>
    <div style="padding:1em;">
        <header-page title="JADWAL OPERASI" subTitle="penjadwalan operasi"></header-page>
        <div style="margin-top:1em;" class="uk-responsive">
            <store-table ref="tableOps"
                :columns = "tbColumns" 
                :buttons = "buttons"
                :config = "configTable"
                :storeData = "operasiLists"
                :searchCallback = "listOperasi"
                :paramFilter = "operasiFilterTable">
                <template v-slot:tbl-body>
                    <tbody style="min-height:50vh;">                        
                        <row-registrasi-ops  v-if="operasiLists"
                            v-for="dt in operasiLists.data" 
                            :rowData="dt"
                            :linkCallback = editRegistrasi>
                        </row-registrasi-ops>
                    </tbody>
                </template>
                <template v-slot:form-filter>                    
                    <filter-list-operasi></filter-list-operasi>
                </template>
            </store-table>
        </div>
        <div>
            <view-registrasi-ops ref="viewRegOps" 
                v-on:viewRegistrasiOpsClosed = "formRegistrasiClosed" 
                v-on:confirmOpsCancel = "editRegistrasi">
            </view-registrasi-ops>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import StoreTable from '@/Components/StoreTable';

import RowRegistrasiOps from '@/Pages/Penunjang/Operasi/components/RowRegistrasiOps.vue';
import FilterListOperasi from '@/Pages/Penunjang/Operasi/Pendaftaran/ListPasienOperasi/FilterListOperasi.vue';
import ViewRegistrasiOps from '@/Pages/Penunjang/Operasi/Pendaftaran/components/ViewRegistrasiOps.vue';

export default {
    name : 'list-pasien-operasi',
    components : {
        headerPage,
        StoreTable,
        RowRegistrasiOps,
        FilterListOperasi,
        ViewRegistrasiOps,
    },
    data() {
        return {
            isExpanded : true,
            isUpdate : false,
            configTable : { isExpandable : true, filterType : 'ADVANCED', },            
            tbColumns : [
                { name : 'registrasi', title : 'REG', colType : 'text', },
                { name : 'pasien', title : 'Pasien', colType : 'text', },
                { name : 'dokter_nama', title : 'Dokter', colType : 'text', },
                { name : 'unit_nama', title : 'Unit', colType : 'text', },
                { name : 'tindakan_nama', title : 'Tindakan', colType : 'text', },
                { name : 'no_antrian', title : 'No', colType : 'text',},
                { name : 'status', title : 'Status', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
            ], 
            buttons : [
                { title : 'Jadwal Baru', icon:'plus-circle', 'callback' : this.addRegistrasi },
            ],
            searchUrl : '/operation/registrations',
            dataLists : null,
         }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('operasi',['operasiLists','operasiFilterTable'])
    },
    methods : {
        ...mapActions('operasi',['listOperasi']),
        ...mapMutations(['CLR_ERRORS']),

        addRegistrasi(){
            this.CLR_ERRORS();
            this.$router.push({ name: 'formPendaftaranOperasi', params: { tipe:'form', dataId: 'baru' } });
        },

        editRegistrasi(data){
            if(data.status == 'DAFTAR') {
                this.CLR_ERRORS();
                this.isUpdate = true;
                this.$router.push({ name: 'formPendaftaranOperasi', params: { tipe:'form', dataId: data.trx_id } });
            }
            else {
                this.$refs.viewRegOps.viewData(data);
            }
        },

        formRegistrasiClosed(){
            this.isUpdate = false;
            this.$refs.tableOps.retrieveData();
            UIkit.switcher('#switcherRegistrasiOps').show(0);
        },

        regSaveSucceed(){
            this.isUpdate = true;
        },

        approveDistribusi(data) {
            this.$refs.approveDistribusi.refreshData(data);
            UIkit.switcher('#switcherRegistrasiOps').show(2);
        },

        refreshPasienInap(data){
            this.CLR_ERRORS();
            if(data){  this.$refs.modalRegistrasiOps.refreshPasienInap(data); }
        },
        
        refreshPasien(data){
            this.CLR_ERRORS();
            if(data) { this.$refs.modalRegistrasiOps.refreshPasien(data); }
        },

        refreshPasienPoli(data){
            this.CLR_ERRORS();
            if(data) { this.$refs.modalRegistrasiOps.refreshPasienPoli(data); }
        },

        searchPasien(){
        }
    }
}
</script>