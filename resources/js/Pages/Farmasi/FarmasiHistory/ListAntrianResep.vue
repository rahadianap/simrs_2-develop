<template>
    <div class="uk-container uk-container-xlarge uk-grid-small" uk-grid style="padding:0.5em 0 1em 0;">
        <div class="uk-width-1-1 uk-grid-small" uk-grid>
            <div class="uk-width-expand@m" style="margin:0;">
                <header-page title="RESEP OBAT" subTitle=""></header-page>
            </div>
            <div class="uk-width-auto@m">
                <div class="uk-button-group uk-box-shadow-large" style="padding: 0; border-radius: 5px;">
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
        <div class="uk-width-1-1" style="margin:0.5em 0 0 0;padding:0 1em 0.5em 1em;">
            <base-table ref="tableAntrianResep"
                :columns = "tbColumns" 
                :config = "configTable" 
                :urlSearch="antrianResepUrl"
                v-on:updateListDataTable="updateListResep">
                <template v-slot:tbl-body>
                    <tbody v-if="resepLists">
                        <row-antrian-resep v-for="dt in resepLists" :rowData="dt" :linkCallback = "fnResepSelected"></row-antrian-resep>
                    </tbody>
                </template>
            </base-table>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import RowAntrianResep from '@/Pages/Farmasi/FarmasiHistory/RowAntrianResep.vue';
import headerPage from '@/Components/HeaderPage.vue';


export default {
    name :'list-antrian-resep',
    props : {
        // fnPasienInapSelected : {type:Function, required:true},
        // fnPasienSelected : {type:Function, required:true},
        // fnPasienPoliSelected : {type:Function, required:true},
    },
    components : { 
        headerPage,
        BaseTable,
        RowAntrianResep,
        // RowPasienPoli 
    },
    data() {
        return {
            isUpdate : false,
            isLoading : true,
            antrianResepUrl : '/farmasi?status=DRAFT',
            tipeResep : 'DRAFT',
            activeButton : 'Draft',
            
            rowFunctions :[
            ],
            
            tableButtons : [
                { title : 'Draft', icon:'file-edit', 'callback' : this.showDraftResep },
                { title : 'Antrian', icon:'tag', 'callback' : this.showAntrianResep },
                { title : 'Selesai', icon:'check', 'callback' : this.showResepSelesai },
            ],

            /** list pasien  inap */
            configTable : { isExpandable : false, filterType : 'SIMPLE', },
            tbColumns : [
                { name : 'tgl_resep', title : 'Tgl Resep', colType : 'text', },
                { name : 'trx_id', title : 'TRX. ID', colType : 'text', },
                { name : 'nama_pasien', title : 'Nama Pasien', colType : 'text', },
                { name : 'dokter_nama', title : 'Peresep', colType : 'text', },
                // { name : 'no_antrian', title : 'No.Antrian', colType : 'text', },
                { name : 'depo_nama', title : 'Depo Resep', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },                
            ], 

            resepLists : null,
        }
    },

    // mounted() {
    //     this.showDraftResep();
    // },

    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListResep(data) {
            this.resepLists = null;
            if(data) { this.resepLists = data.data;}
        },

        fnResepSelected(data){
            if(data){
                if(data.status.toUpperCase() == 'REALISASI') {
                    this.$router.push({ name:'viewResep', params:{ trxId:data.trx_id, trxType:'view' } });
                }
                else {
                    this.$router.push({ name:'dataResep', params:{ trxId:data.trx_id, trxType:'edit' } });
                }
            }
        },

        showDraftResep(){
            this.pasienLists = null;
            this.tipeResep = 'DRAFT';
            this.activeButton = 'Draft';
            this.antrianResepUrl = `/farmasi?status=DRAFT`;
        },

        showAntrianResep(){
            this.pasienLists = null;
            this.tipeResep = 'FARMASI';
            this.activeButton = 'Antrian';
            this.antrianResepUrl = `/farmasi?status=FARMASI`;

        },

        showResepSelesai(){
            this.pasienLists = null;
            this.tipeResep = 'REALISASI';
            this.activeButton = 'Selesai';
            this.antrianResepUrl = `/farmasi?status=REALISASI`;
        },

        editDataPasien(data){
            if(data){
                this.CLR_ERRORS();
                this.$refs.modalPasien.editDataPasien(data.pasien_id);
            }
        },

        pasienPoliSelected(data) {
            this.$router.push({ name:'dataResep', params:{ trxId:data.trx_id, trxType:'poli' } });
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