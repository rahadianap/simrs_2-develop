<template>
    <div class="uk-container-xlarge">
        <header-page title="DAFTAR PASIEN INAP" subTitle="daftar pasien pelayanan rawat inap"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;" class="uk-responsive">
            <store-table ref="tablePasienInap"
                :columns = "tbColumns" 
                :buttons = "buttons"
                :config = "configTable"
                :storeData = "inpatientLists"
                :searchCallback = "listAdmisiInap"
                :paramFilter = "inapFilterTable">
                <template v-slot:tbl-body>
                    <tbody style="min-weight:50vh;">
                        <row-pasien-inap  v-if="inpatientLists"
                            v-for="dt in inpatientLists.data" 
                            :rowData="dt"
                            :linkCallback = editRegistrasi>
                        </row-pasien-inap>
                    </tbody>
                </template>
                <template v-slot:form-filter>                    
                    <filter-admisi-inap></filter-admisi-inap>
                </template>
            </store-table>
        </div>
        <div id="modalSpinnerInap" class="uk-flex-top" uk-modal>
            <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical" style="text-align:center;">
                <button class="uk-modal-close-default" type="button" uk-close></button>
                <div uk-spinner="ratio: 2"></div>
                <p style="font-weight:bold;">sedang menyiapkan data</p>
            </div>
        </div>
    </div>
</template>
<script>

import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import FormRegistrasiInap from '@/Pages/RawatInap/Components/FormRegistrasiInap.vue';
import ViewRegistrasiInap from '@/Pages/RawatInap/Components/ViewRegistrasiInap.vue';

import StoreTable from '@/Components/StoreTable';
import RowPasienInap from '@/Pages/RawatInap/Components/RowPasienInap.vue';
import ModalGantiDokter from '@/Pages/RawatInap/Components/ModalGantiDokter.vue';
import ModalGantiRuang from '@/Pages/RawatInap/Components/ModalGantiRuang.vue';

import FilterAdmisiInap from '@/Pages/RawatInap/Components/FilterAdmisiInap.vue';

export default {
    components : {
        headerPage,
        FormRegistrasiInap,
        ViewRegistrasiInap,        
        StoreTable,
        RowPasienInap,
        ModalGantiDokter,
        ModalGantiRuang,
        FilterAdmisiInap,
    },
    data() {
        return {
            isUpdate : false,
            isExpanded : false,
            configTable : { isExpandable : true, filterType : 'ADVANCED', },
            rowFunction : this.editRegistrasi,
            tbColumns : [
                { name : 'reg_id', title : 'REG. ID', colType : 'text', },
                { name : 'pasien', title : 'Pasien', colType : 'text', },
                { name : 'dokter_nama', title : 'DPJP', colType : 'text', },
                { name : 'unit_nama', title : 'Unit', colType : 'text', },
                { name : 'no_bed', title : 'Bed', colType : 'text', },
                { name : 'penjamin_nama', title : 'Penjamin', colType : 'text', },
                { name : 'tgl_masuk', title : 'Tgl. Masuk', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },                
            ], 
            buttons : [
                { title : 'Pasien Inap Baru', icon:'plus-circle', 'callback' : this.addRegistrasi },
            ],
            searchUrl : '/inpatients/admissions',
            dataLists : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatInap',['inapFilterTable','inpatientLists'])
    },
    
    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('rawatInap',['deleteAdmisiInap','listAdmisiInap']),

        initialize(){
            this.listReferensi({'per_page':'ALL'}).then((response) => {
                UIkit.modal('#modalSpinnerInap').hide();
            });  
        },

        // updateListData(data) {
        //     if(data) {
        //         if(data.data) { this.dataLists = data.data; }
        //         else { this.dataLists = null; }
        //     }
        //     else { this.dataLists = null; }
        // },

        refreshTable(){ this.$refs.tablePasienInap.retrieveData(); },

        addRegistrasi(){
            this.CLR_ERRORS();
            this.isUpdate = false;
            // this.$refs.formRegistrasiInap.newData(); 
            // UIkit.switcher('#switcherPasienInap').show(1);
            this.$router.push({ name: 'formAdmisi', params: { trxId: 'baru' } });
        },

        editRegistrasi(data) {
            if(data) {
                this.CLR_ERRORS();
                this.isUpdate = true;
                if(data.status == 'DAFTAR' || data.status == 'BOOKING' ) {
                    this.$router.push({ name: 'formAdmisi', params: { trxId: data.trx_id } });
                    // this.$refs.formRegistrasiInap.editData(data); 
                    // UIkit.switcher('#switcherPasienInap').show(1);
                }
                else {
                    this.$router.push({ name: 'viewAdmisi', params: { trxId: data.trx_id } });
                    // this.$refs.viewRegistrasiInap.editData(data); 
                    // UIkit.switcher('#switcherPasienInap').show(2);
                }
            }
        },

        formRegistrasiClosed(){
            this.CLR_ERRORS();
            this.refreshTable();
            UIkit.switcher('#switcherPasienInap').show(0);
        },
    }
}

</script>
<style>
ul.hims-inap-tab li {
    margin:0;
    padding:0;
}

ul.hims-inap-tab li h5{
    padding:0 1em 0 1em;
    margin:0;
}

ul.hims-inap-tab li div a {
    padding:0;
    margin:0;
    color:#000; 
    display:block; 
    padding:0.5em;
    text-decoration: none;
    font-weight: 500;
}

ul.hims-inap-tab li div {
    margin:0; 
    background-color: #eee; 
    color:#000;
    padding:0;
}

ul.hims-inap-tab li.uk-active div {
    margin:0; 
    background-color: #fff; 
    color:#000;
    padding:0;
}

ul.hims-inap-tab li div a h5 {
    color:#666;
    font-weight: 500;
}

ul.hims-inap-tab li.uk-active div a h5 {
    color:#000;
    font-weight: 500;
}
</style>