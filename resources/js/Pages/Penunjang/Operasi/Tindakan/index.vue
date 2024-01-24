<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <router-view></router-view>
        <!-- <ul id="switcherHasilOps" class="uk-subnav uk-subnav-pill uk-hidden1" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List Transaksi</a></li>
            <li><a href="#">Form Hasil</a></li>
            <li><a href="#">Form Transaksi</a></li>
        </ul> -->
        <!-- <ul class="uk-switcher" style="padding:0;margin:0;"> -->
            <!-- <li style="padding:0;margin:0;">
                <div>
                    <header-page title="TINDAKAN OPERASI" subTitle="transaksi dan hasil operasi"></header-page>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
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
                                        :linkCallback = viewHasilOps>
                                    </row-registrasi-ops>
                                </tbody>
                            </template>
                            <template v-slot:form-filter>                    
                                <filter-operasi></filter-operasi>
                            </template>
                        </store-table>
                    </div>
                </div>
            </li> -->
            <!-- <li style="padding:0;margin:0;">
                <div style="padding:0;min-height:70vh;">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-auto">
                            <a href="#" @click.prevent="formHasilOpsClosed" style="background-color:white;padding:0.3em;display:inline-block;">
                                <span uk-icon="icon:chevron-left;ratio:1"></span>
                            </a>
                        </div>
                        <div class="uk-width-expand">
                            <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">DATA OPERASI</h4>
                        </div>
                    </div>
                    <div style="background-color:#fff;margin-top:1em;">
                        <form-realisasi-ops ref="formRealisasiOps" 
                            v-on:resultLabClosed = "formHasilOpsClosed"
                            v-on:confirmLabCancel = "ubahDataRegistrasi"
                            v-on:saveSucceed = "regSaveSucceed"
                            v-on:searchPasien = "searchPasien">
                        </form-realisasi-ops>
                    </div>
                </div>
            </li> -->
            <!-- <li style="padding:0;margin:0;"> 
                <div style="padding:0;min-height:70vh;">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-auto">
                            <a href="#" @click.prevent="formHasilOpsClosed" style="background-color:white;padding:0.3em;display:inline-block;">
                                <span uk-icon="icon:chevron-left;ratio:1"></span>
                            </a>
                        </div>
                        <div class="uk-width-expand">
                            <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">FORM OPERASI</h4>
                        </div>
                    </div>
                    <div style="background-color:#fff;margin-top:1em;"> -->
                        <!-- <form-realisasi-ops ref="formRealisasiOps" 
                            v-on:formRealisasiClosed = "formHasilOpsClosed"
                            v-on:saveSucceed = "regSaveSucceed">
                        </form-realisasi-ops> -->
                    <!-- </div>
                </div>
            </li> -->
        <!-- </ul>
        <div>
            <view-tindakan-ops ref="viewTindakanOps" 
                v-on:viewRegistrasiOpsClosed = "formHasilOpsClosed" 
                v-on:changeOpsReg = "ubahDataRegistrasi">
            </view-tindakan-ops>
        </div>
        <div>
            <modal-realisasi-ops ref="modalRealisasiOps" 
                v-on:modalRealisasiOpsClosed = "formHasilOpsClosed" 
                v-on:changeOpsReg = "ubahDataRegistrasi">
            </modal-realisasi-ops>
        </div> -->
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import StoreTable from '@/Components/StoreTable';

import RowRegistrasiOps from '@/Pages/Penunjang/Operasi/components/RowRegistrasiOps.vue';
import ViewTindakanOps from '@/Pages/Penunjang/Operasi/Tindakan/ViewTindakanOps.vue';
import ModalRealisasiOps from '@/Pages/Penunjang/Operasi/Tindakan/ModalRealisasiOps.vue';
import FormRealisasiOps from '@/Pages/Penunjang/Operasi/Tindakan/FormRealisasiOps.vue';

import FilterOperasi from '@/Pages/Penunjang/Operasi/Tindakan/FilterOperasi.vue';

// import formHasilOps from '@/Pages/Penunjang/Laboratorium/Pemeriksaan/components/FormHasilLab.vue';
//import formRealisasiOps from '@/Pages/Penunjang/Laboratorium/Pemeriksaan/components/formPemeriksaanLab.vue';

export default {
    components : {
        headerPage,
        StoreTable,
        RowRegistrasiOps,
        ViewTindakanOps,
        ModalRealisasiOps,
        FormRealisasiOps,
        FilterOperasi,
        //formHasilOps,
        //formRealisasiOps,
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
                { name : 'no_antrian', title : 'No', colType : 'text',},
                { name : 'status', title : 'Status', colType : 'text', },
                { name : 'transaksi.total', title : 'Total', colType : 'text',textAlign:'right' },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
            ], 
            buttons : [
                // { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addRegistrasi },
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
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('operasi',['listOperasi']),

        // updateListData(data) {
        //     if(data) {
        //         if(data.data) { this.dataLists = data.data; }
        //         else { this.dataLists = null; }
        //     }
        //     else { this.dataLists = null; }
        // },

        viewHasilOps(data){
            if(data){
                // this.$refs.formRealisasiOps.editData(data);
                // UIkit.switcher('#switcherHasilOps').show(1);
                if(data.is_realisasi) {
                    //jika data sudah direalisasi , paket tindakan , ruang tidak boleh diubah

                }
                else {
                    if(data.status == 'PROSES'){
                        this.$refs.formRealisasiOps.editData(data);
                        UIkit.switcher('#switcherHasilOps').show(1);
                    }
                    else {
                        //this.$refs.viewTindakanOps.viewData(data);
                        this.$refs.modalRealisasiOps.editData(data);
                    }
                }
            }
        },
        
        addRegistrasi(){
            this.CLR_ERRORS();
            this.isUpdate = false;
            UIkit.switcher('#switcherHasilOps').show(2);
            UIkit.switcher('#switcherFormRegOps').show(0);
            this.$refs.listAsalPasien.showMasterPasien();
            this.$refs.formRegistrasiOps.newData(); 
        },

        ubahDataRegistrasi(data){
            if(data){
                this.$refs.formRealisasiOps.editData(data);
                UIkit.switcher('#switcherHasilOps').show(2);
            }
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
                this.$refs.viewTindakanOps.viewData(data);
            }
        },

        formHasilOpsClosed(){
            this.isUpdate = false;
            this.$refs.tableOps.retrieveData();
            UIkit.switcher('#switcherHasilOps').show(0);
        },

        regSaveSucceed(){
            this.isUpdate = true;
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
</style>