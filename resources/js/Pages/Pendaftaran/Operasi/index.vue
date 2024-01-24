<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">        
        <ul id="switcherRegistrasiOps" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List Registrasi</a></li>
            <li><a href="#">Form Registrasi</a></li>
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <div>
                    <header-page title="JADWAL OPERASI" subTitle="penjadwalan operasi"></header-page>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:1em;" class="uk-responsive">
                        <base-table ref="tableOps"
                            :columns = "tbColumns" 
                            :config = "configTable" 
                            :buttons = "buttons"
                            :urlSearch="searchUrl" 
                            v-on:updateListDataTable="updateListData">
                            <template v-slot:tbl-body>
                                <tbody v-if="dataLists">
                                    <row-registrasi-ops v-for="dt in dataLists" :rowData="dt" :linkCallback = editRegistrasi></row-registrasi-ops>
                                </tbody>
                            </template>
                        </base-table>
                    </div>
                </div>
            </li>
            <li style="padding:0;margin:0;">
                <div style="padding:0;min-height:70vh;">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-auto">
                            <a href="#" @click.prevent="formRegistrasiClosed" style="background-color:white;padding:0.3em;display:inline-block;">
                                <span uk-icon="icon:chevron-left;ratio:1"></span>
                            </a>
                        </div>
                        <div class="uk-width-expand">
                            <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">PENJADWALAN OPERASI</h4>
                        </div>
                    </div>
                    <div style="background-color:#fff;margin-top:1em;">
                        <ul class="uk-tab hims-penunjang-tab uk-hidden" uk-switcher id="switcherFormRegOps" style="padding:0;margin:0;">
                            <li><div><a href="#"><h5>Pilih Pasien</h5></a></div></li>
                            <li style="padding:0;"><div><a href="#"><h5>Form Registrasi</h5></a></div></li> 
                        </ul>
                        <ul class="uk-switcher">
                            <li>
                                <list-pasien ref="listAsalPasien" 
                                    v-on:pasienInapSelected="refreshPasienInap" 
                                    v-on:pasienSelected="refreshPasien" 
                                    v-on:pasienPoliSelected="refreshPasienPoli" 
                                ></list-pasien>
                            </li>
                            <li style="padding:0;margin:0;">
                                <form-registrasi-ops ref="formRegistrasiOps" 
                                    v-on:registrasiLabClosed = "formRegistrasiClosed"
                                    v-on:saveSucceed = "regSaveSucceed"
                                    v-on:searchPasien = "searchPasien">
                                </form-registrasi-ops>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
        <div>
            <view-registrasi-lab ref="viewRegLab" 
                v-on:viewRegistrasiLabClosed = "formRegistrasiClosed" 
                v-on:confirmLabCancel = "editRegistrasi">
            </view-registrasi-lab>
        </div>

    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormRegistrasiOps from '@/Pages/Pendaftaran/Operasi/components/FormRegistrasiOps.vue';
import ListPasien from '@/Pages/Pendaftaran/Operasi/components/ListPasien';

import RowRegistrasiOps from '@/Pages/Pendaftaran/Operasi/components/RowRegistrasiOps.vue';


import ListRegistrasi from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/ListRegistrasi.vue';
import FormRegistrasi from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/FormRegistrasi.vue';

import ViewRegistrasiLab from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/ViewRegistrasiLab.vue';

export default {
    components : {
        headerPage,
        ListRegistrasi,
        FormRegistrasi,
        FormRegistrasiOps,
        BaseTable,
        RowRegistrasiOps,
        ListPasien,
        ViewRegistrasiLab
    },
    data() {
        return {
            isExpanded : true,
            isUpdate : false,
            configTable : { isExpandable : true, filterType : 'REGULAR', },            
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
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data) {
            if(data) {
                if(data.data) { this.dataLists = data.data; }
                else { this.dataLists = null; }
            }
            else { this.dataLists = null; }
        },
        
        addRegistrasi(){
            this.CLR_ERRORS();
            this.isUpdate = false;
            UIkit.switcher('#switcherRegistrasiOps').show(1);
            UIkit.switcher('#switcherFormRegOps').show(0);
            this.$refs.listAsalPasien.showMasterPasien();
            this.$refs.formRegistrasiOps.newData(); 
        },

        editRegistrasi(data){
            if(data.status == 'DAFTAR') {
                this.CLR_ERRORS();
                this.isUpdate = true;
                UIkit.switcher('#switcherFormRegOps').show(1);
                UIkit.switcher('#switcherRegistrasiOps').show(1);       
                this.$refs.formRegistrasiOps.editData(data); 
            }
            else {
                this.$refs.viewRegLab.viewData(data);
                //alert('Data dengan status bukan PERMINTAAN sudah tidak dapat diubah.');
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
            if(data){ 
                this.$refs.formRegistrasiOps.refreshPasienInap(data);
                UIkit.switcher('#switcherFormRegOps').show(1);
            }
        },
        
        refreshPasien(data){
            this.CLR_ERRORS();
            if(data) {
                this.$refs.formRegistrasiOps.refreshPasien(data);
                UIkit.switcher('#switcherFormRegOps').show(1);
            }
        },

        refreshPasienPoli(data){
            this.CLR_ERRORS();
            if(data) { 
                UIkit.switcher('#switcherFormRegOps').show(1);
            }
        },

        searchPasien(){
            UIkit.switcher('#switcherFormRegOps').show(0);
        }
    }

}
</script>
<style>
ul.hims-penunjang-tab li {
    margin:0;
    padding:0;
}

ul.hims-penunjang-tab li h5{
    padding:0 1em 0 1em;
    margin:0;
}

ul.hims-penunjang-tab li div a {
    padding:0;
    margin:0;
    color:#000; 
    display:block; 
    padding:0.5em;
    text-decoration: none;
    font-weight: 500;
}

ul.hims-penunjang-tab li div {
    margin:0; 
    background-color: #eee; 
    color:#000;
    padding:0;
}

ul.hims-penunjang-tab li.uk-active div {
    margin:0; 
    background-color: #fff; 
    color:#000;
    padding:0;
}

ul.hims-penunjang-tab li div a h5 {
    color:#666;
    font-weight: 500;
}

ul.hims-penunjang-tab li.uk-active div a h5 {
    color:#000;
    font-weight: 500;
}
</style>