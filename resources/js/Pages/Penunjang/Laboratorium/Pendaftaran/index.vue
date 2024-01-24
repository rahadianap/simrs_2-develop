<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">        
        <ul id="switcherRegistrasiLab" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List Registrasi</a></li>
            <li><a href="#">Form Registrasi</a></li>
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <div>
                    <header-page title="PENDAFTARAN LABORATORIUM" subTitle="daftar pasien pelayanan laboratorium"></header-page>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:1em;" class="uk-responsive">
                        <base-table ref="tableLab"
                            :columns = "tbColumns" 
                            :config = "configTable" 
                            :buttons = "buttons"
                            :urlSearch="searchUrl" 
                            v-on:updateListDataTable="updateListData">
                            <template v-slot:tbl-body>
                                <tbody v-if="dataLists">
                                    <row-registrasi-lab v-for="dt in dataLists" :rowData="dt" :linkCallback = editRegistrasi></row-registrasi-lab>
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
                            <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">REGISTRASI LABORATORIUM</h4>
                        </div>
                    </div>
                    <div style="background-color:#fff;margin-top:1em;">
                        <ul class="uk-tab hims-penunjang-tab uk-hidden" uk-switcher id="switcherFormRegLab" style="padding:0;margin:0;">
                            <li><div><a href="#"><h5>Pilih Pasien</h5></a></div></li>
                            <li style="padding:0;"><div><a href="#"><h5>Form Registrasi</h5></a></div></li> 
                        </ul>
                        <ul class="uk-switcher">
                            <li>
                                <list-pasien ref="listAsalPasien" 
                                    :fnPasienInapSelected="refreshPasienInap" 
                                    :fnPasienSelected="refreshPasien" 
                                    :fnPasienPoliSelected="refreshPasienPoli" 
                                ></list-pasien>
                            </li>
                            <li style="padding:0;margin:0;">
                                <form-registrasi-lab ref="formRegistrasiLab" 
                                    v-on:registrasiLabClosed = "formRegistrasiClosed"
                                    v-on:saveSucceed = "regSaveSucceed"
                                    v-on:searchPasien = "searchPasien">
                                </form-registrasi-lab>
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
import RowRegistrasiLab from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/RowRegistrasiLab.vue';
import FormRegistrasiLab from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/FormRegistrasiLab.vue';

import ListPasien from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/ListPasien';
import ListRegistrasi from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/ListRegistrasi.vue';
import FormRegistrasi from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/FormRegistrasi.vue';

import ViewRegistrasiLab from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/ViewRegistrasiLab.vue';

export default {
    components : {
        headerPage,
        ListRegistrasi,
        FormRegistrasi,
        FormRegistrasiLab,
        BaseTable,
        RowRegistrasiLab,
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
                { name : 'no_antrian', title : 'No', colType : 'text',},
                //{ name : 'penjamin_nama', title : 'Penjamin', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text', },
                { name : 'subtotal', title : 'Total', colType : 'text',textAlign:'right' },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addRegistrasi },
            ],
            searchUrl : '/labs/registrations',
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
            UIkit.switcher('#switcherRegistrasiLab').show(1);
            UIkit.switcher('#switcherFormRegLab').show(0);
            this.$refs.listAsalPasien.showMasterPasien();
            this.$refs.formRegistrasiLab.newData(); 
        },

        editRegistrasi(data){
            if(data.status == 'DAFTAR') {
                this.CLR_ERRORS();
                this.isUpdate = true;
                UIkit.switcher('#switcherFormRegLab').show(1);
                UIkit.switcher('#switcherRegistrasiLab').show(1);       
                this.$refs.formRegistrasiLab.editData(data); 
            }
            else {
                this.$refs.viewRegLab.viewData(data);
                //alert('Data dengan status bukan PERMINTAAN sudah tidak dapat diubah.');
            }
        },

        formRegistrasiClosed(){
            this.isUpdate = false;
            this.$refs.tableLab.retrieveData();
            UIkit.switcher('#switcherRegistrasiLab').show(0);
        },

        regSaveSucceed(){
            this.isUpdate = true;
        },

        approveDistribusi(data) {
            this.$refs.approveDistribusi.refreshData(data);
            UIkit.switcher('#switcherRegistrasiLab').show(2);
        },

        refreshPasienInap(data){
            this.CLR_ERRORS();
            if(data){ 
                this.$refs.formRegistrasiLab.refreshPasienInap(data);
                UIkit.switcher('#switcherFormRegLab').show(1);
            }
        },
        
        refreshPasien(data){
            this.CLR_ERRORS();
            if(data) {
                this.$refs.formRegistrasiLab.refreshPasien(data);
                UIkit.switcher('#switcherFormRegLab').show(1);
            }
        },

        refreshPasienPoli(data){
            this.CLR_ERRORS();
            if(data) { 
                this.$refs.formRegistrasiLab.refreshPasienPoli(data);
                UIkit.switcher('#switcherFormRegLab').show(1);
            }
        },

        searchPasien(){
            UIkit.switcher('#switcherFormRegLab').show(0);
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