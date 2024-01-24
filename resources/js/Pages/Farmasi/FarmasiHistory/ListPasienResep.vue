<template>
    <div class="uk-container uk-container-xlarge uk-grid-small" uk-grid style="padding:0.5em 0 1em 0;">        
        <div class="uk-width-1-1 uk-grid-small" uk-grid>
            <div class="uk-width-expand@m" style="margin:0;padding:0 0 0 0.5em;">
                <h5 style="font-weight: 500;margin:0.2em 0 0 1em; padding:0;">{{asalPasien}}</h5>
            </div>
            <div class="uk-width-auto@m">
                <div class="uk-button-group uk-box-shadow-large" style="padding:0;margin:0;border-radius:5px;">                
                    <!-- <button class="hims-table-btn" @click.prevent="showPasienPoli">
                        <span uk-icon="users"></span>Pasien Poli
                    </button>
                    <button class="hims-table-btn" @click.prevent="showPasienInap">
                        <span uk-icon="users"></span>Pasien Inap
                    </button> -->
                    <button
                        v-for="(button, index) in tableButtons"
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

        <template v-if=" asalPasien == 'PASIEN INAP' ">
            <div class="uk-width-1-1" style="margin:0.5em 0 0 0;padding:0 1em 0.5em 1em;">
                <!-- <base-table ref="tablePasienInap"
                    :columns = "tbInapColumns" 
                    :config = "configBaseTable" 
                    :urlSearch="pasienInapUrl"
                    v-on:updateListDataTable="updateListDataInap">
                    <template v-slot:tbl-body>
                        <tbody v-if="pasienInapLists">
                            <row-pasien-inap v-for="dt in pasienInapLists" :rowData="dt" :linkCallback = "fnPasienInapSelected"></row-pasien-inap>
                        </tbody>
                    </template>
                </base-table> -->
                <store-table ref="tablePasienInap"
                    :columns = "tbInapColumns" 
                    :config = "configBaseTable"
                    :storeData = "inpatientLists"
                    :searchCallback = "listAdmisiInap"
                    :paramFilter = "inapFilterTable">
                    <template v-slot:tbl-body>
                        <tbody style="min-weight:50vh;">
                            <!-- <row-pelayanan-inap  v-if="inpatientLists"
                                v-for="dt in inpatientLists.data" 
                                :rowData="dt"
                                :linkCallback="editData"
                                :lockCallback="konfirmasiInap"
                                :rowFunctions="rowFunctions"
                                >
                            </row-pelayanan-inap> -->
                            <row-pasien-inap v-if="inpatientLists" 
                                v-for="dt in inpatientLists.data" 
                                :rowData="dt" 
                                :linkCallback = "fnPasienInapSelected">
                            </row-pasien-inap>
                        </tbody>
                    </template>
                    <template v-slot:form-filter>                    
                        <filter-pasien-inap></filter-pasien-inap>
                    </template>
                </store-table>
            </div>
        </template>
        <template v-else>
            <div class="uk-width-1-1" style="margin:0.5em 0 0 0;padding:0 1em 0.5em 1em;">
                <!-- <base-table ref="tablePasienPoli"
                    :columns = "tbPoliColumns" 
                    :config = "configBaseTable" 
                    :urlSearch="pasienPoliUrl" v-on:updateListDataTable="updateListDataPoli">
                    <template v-slot:tbl-body>
                        <tbody v-if="pasienPoliLists">
                            <row-pasien-poli 
                                v-for="dt in pasienPoliLists" 
                                :rowData="dt" 
                                :linkCallback = "pasienPoliSelected">
                            </row-pasien-poli>
                        </tbody>
                    </template>
                </base-table> -->
                <store-table ref="tablePasienPoli"
                    :columns = "tbPoliColumns" 
                    :config = "configBaseTable"
                    :storeData = "poliTransactionLists"
                    :searchCallback = "listTransaksiPoli"
                    :paramFilter = "poliFilterTable">
                    <template v-slot:tbl-body>
                        <tbody style="min-weight:50vh;">
                            <row-pasien-poli  v-if="poliTransactionLists"
                                v-for="dt in poliTransactionLists.data" 
                                :rowData="dt"
                                :linkCallback="pasienPoliSelected">
                            </row-pasien-poli>
                        </tbody>
                    </template>
                    <template v-slot:form-filter>                    
                        <filter-pasien-poli></filter-pasien-poli>
                    </template>
                </store-table>
            </div>
        </template>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import RowPasienPoli from '@/Pages/Farmasi/FarmasiHistory/RowPasienPoli.vue';
import RowPasienInap from '@/Pages/Farmasi/FarmasiHistory/RowPasienInap.vue';
import StoreTable from '@/Components/StoreTable';
import FilterPasienPoli from '@/Pages/Farmasi/FarmasiHistory/FilterPasienPoli.vue';
import FilterPasienInap from '@/Pages/Farmasi/FarmasiHistory/FilterPasienInap.vue';


export default {
    name :'list-pasien-resep',
    props : {
        // fnPasienInapSelected : {type:Function, required:true},
        // fnPasienSelected : {type:Function, required:true},
        // fnPasienPoliSelected : {type:Function, required:true},
    },
    components : { 
        BaseTable,
        RowPasienInap,
        RowPasienPoli,
        StoreTable,
        FilterPasienPoli,
        FilterPasienInap
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransactionLists','poliFilterTable']),
        ...mapGetters('rawatInap',['inapFilterTable','inpatientLists']),
    },

    data() {
        return {
            isUpdate : false,
            isLoading : true,
            masterPasienUrl : '',
            pasienPoliUrl : '',
            pasienInapUrl : '',
            asalPasien : 'PASIEN POLI',
            activeButton : 'Pasien Poli',
            
            configBaseTable : { 
                isExpandable : false, 
                filterType : 'ADVANCED', 
            },
            rowFunctions :[
                { 'title':'Pilih Pasien', 'icon':'check','callback':this.fnPasienSelected },
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

            tableButtons : [
                { title : 'Pasien Poli', icon:'users', 'callback' : this.showPasienPoli },
                { title : 'Pasien Inap', icon:'users', 'callback' : this.showPasienInap },
            ],

            /** list pasien  inap */
            configInapTable : { isExpandable : false, filterType : 'SIMPLE', },
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

            tbPoliColumns :[
            { name : 'trx_id', title : 'Transaksi ID', colType : 'linkmenus', },   
                { name : 'no_antrian', title : 'Antrian', colType : 'text', isSearchable : true, },
                { name : 'nama_pasien', title : 'PASIEN', colType : 'linkmenus',  },
                { name : 'dokter_nama', title : 'Dokter Unit', colType : 'text', isSearchable : true, },
                { name : 'penjamin_nama', title : 'Penjamin', colType : 'text', isSearchable : true, },
                { name : 'status', title : 'STATUS', colType : 'text', isSearchable : true, },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }  
            ],
            pasienLists : null,
            pasienInapLists : null,
            pasienPoliLists : null,
        }
    },

    mounted() {
        this.showPasienPoli();
    },

    methods : {
        ...mapActions('rawatJalan',['listTransaksiPoli']),
        ...mapActions('rawatInap',['listAdmisiInap']),
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data){
            this.pasienLists = null;
            if(data) { this.pasienLists = data.data;}
        },

        updateListDataInap(data) {
            this.pasienInapLists = null;
            if(data) { this.pasienInapLists = data.data;}
        },
        updateListDataPoli(data) {
            this.pasienPoliLists = null;
            if(data) { this.pasienPoliLists = data.data;}
        },

        fnPasienInapSelected(data){
            if(data){
                this.$router.push({ name:'dataResep', params:{ trxId:data.trx_id, trxType:'inap' } });
            }
        },

        showPasienInap(){
            this.pasienInapLists = null;
            this.asalPasien = 'PASIEN INAP';
            this.activeButton = 'Pasien Inap';
            this.pasienInapUrl = '/inpatients/admissions';//?status=RAWAT';
        },

        showPasienPoli(){
            this.pasienLists = null;
            this.asalPasien = 'PASIEN POLI';
            this.activeButton = 'Pasien Poli';
            this.pasienPoliUrl = '/registrations';
        },

        editDataPasien(data){
            if(data){
                this.CLR_ERRORS();
                this.$refs.modalPasien.editDataPasien(data.pasien_id);
            }
        },

        pasienPoliSelected(data) {
            this.CLR_ERRORS();
            if(confirm(`Tambahkan resep baru untuk pasien ${data.nama_pasien}?`)){
                this.$router.push({ name:'dataResep', params:{ trxId:data.trx_id, trxType:'poli' } });
            };

        },
        
        refreshPasien(){

        }
    }
}
</script>
<style scoped>
.hims-btn-active {
    background-color: #cc0202 !important;
    color: white !important;
}
</style>