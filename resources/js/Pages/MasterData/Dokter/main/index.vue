<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="DOKTER DAN SPESIALIS" subTitle="master data dokter dan spesialis"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableDokter"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl"
                v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists">
                        <row-master-dokter v-for="dt in dataLists" 
                            :rowData="dt" 
                            :fnEditCallback = updateData
                            :fnDeleteCallback = deleteData
                            v-on:itemUpdated="refreshTable">
                        </row-master-dokter>
                    </tbody>
                </template>
                
            </base-table>
        </div>
    </div>
    <cetakan-data-dokter ref="cetakanDataDokter"></cetakan-data-dokter>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import CetakanDataDokter from '@/Pages/MasterData/Dokter/cetakan/cetakanDataDokter.vue';
import RowMasterDokter from '@/Pages/MasterData/Dokter/main/RowMasterDokter.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        CetakanDataDokter,
        RowMasterDokter,
    },  
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('ksm',['ksmLists']),
        ...mapGetters('spesialisasi',['spesialisasiLists']),
    
    },
    data() {
        return {
            dataLists : [],
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            tbColumns : [                
                { 
                    name : 'dokter_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                        { 'title':'Cetak Data Dokter', 'icon':'file-pdf','callback':this.cetakDataDokter },   
                    ],
                },
                { 
                    name : 'dokter_nama', 
                    title : 'Nama', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'smf.smf_nama', 
                    title : 'KSM', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'no_sip', 
                    title : 'SIP', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'tgl_akhir_sip', 
                    title : 'TGL. AKHIR SIP', 
                    colType : 'text',
                    isSearchable  : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Status', 
                    colType : 'boolean', 
                    options : [
                        { value:0, text:'Aktif' },
                        { value:1, text:'Nonaktif' },
                    ]
                },
                {
                    name : 'button', 
                    title : '...', 
                }
                
            ], 
            buttons : [
                { title : 'Dokter Baru', icon:'plus-circle', 'callback' : this.addDokter },
            ],
            searchUrl : '/doctors',
         }
    },
    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('dokter',['listDokter','deleteDokter']),
        ...mapActions('cetakan',['dataDokter']),
        ...mapActions('ksm',['listKsm']),
        ...mapActions('spesialisasi',['listSpesialisasi']),
        
        ...mapMutations(['CLR_ERRORS']),

        editData(data) {
            
        },

        activeData(data){

        },

        initialize() {
            let param = {per_page:'ALL'};
            if(this.ksmLists == null || this.ksmLists.length == 0){
                this.listKsm(param);
            }
            if(this.spesialisasiLists == null || this.spesialisasiLists.length == 0) {
                this.listSpesialisasi(param);
            }
        },
        /**tampikan modal untuk membuat dokter baru */
        addDokter(){  
            this.CLR_ERRORS();
            this.$router.push({ name:'editDataDokter', params:{ dokterId:'baru' } });
        },

        refreshTable() {
            this.$refs.tableDokter.retrieveData();
        },

        updateListData(data) {
            if(data) {
                this.dataLists = data.data;
            }
        },

        updateData(data){
            this.CLR_ERRORS();
            //this.$router.push(`/master/dokter/${data.dokter_id}`);
            this.$router.push({ name:'editDataDokter', params:{ dokterId:data.dokter_id } });
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan dokter ${data.dokter_nama}. Lanjutkan ?`)){
                this.deleteDokter(data.dokter_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableDokter.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        cetakDataDokter(data){
            this.dataDokter(data.dokter_id).then((response) => {
                if (response.success == true) {
                    this.$refs.cetakanDataDokter.generateReport(response.data);
                }
                else { alert (response.message) }
            });
        }
    }
}
</script>