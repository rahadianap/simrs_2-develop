<template>
    <div class="uk-container uk-container-xlarge uk-grid-small" uk-grid style="padding:0.5em 0 1em 0;">        
        <header-page title="APOTEK" subTitle="penjualan resep dan obat apotek"></header-page>
        <div class="uk-width-1-1" style="margin:0.5em 0 0 0;padding:0 1em 0.5em 1em;">
            <base-table ref="tableAntrianResep"
                :columns = "tbColumns" 
                :config = "configTable" 
                :urlSearch="apotekUrl"
                :buttons = "tableButtons"
                v-on:updateListDataTable="updateListResep">
                <template v-slot:tbl-body>
                    <tbody v-if="resepLists">
                        <row-antrian-apotek v-for="dt in resepLists" :rowData="dt" :linkCallback = "fnResepSelected"></row-antrian-apotek>
                    </tbody>
                </template>
            </base-table>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import headerPage from '@/Components/HeaderPage.vue';
import RowAntrianApotek from '@/Pages/Apotek/ApotekHistory/RowApotekHistory.vue';


export default {
    name :'list-antrian-apotek',
    props : {
    },
    components : { 
        BaseTable,
        RowAntrianApotek,
        headerPage,
    },
    data() {
        return {
            isUpdate : false,
            isLoading : true,
            apotekUrl : '/pharmacy',
            
            rowFunctions :[
            ],
            
            tableButtons : [
                { title : 'Resep Baru', icon:'plus-circle', 'callback' : this.addResep },
            ],

            /** list pasien  inap */
            configTable : { isExpandable : false, filterType : 'SIMPLE', },
            tbColumns : [
                { name : 'tgl_resep', title : 'Tgl Resep', colType : 'text', },
                { name : 'trx_id', title : 'TRX. ID', colType : 'text', },
                { name : 'nama_pasien', title : 'Nama Pasien', colType : 'text', },
                { name : 'dokter_nama', title : 'Peresep', colType : 'text', },
                { name : 'depo_nama', title : 'Depo Resep', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },                
            ], 

            resepLists : null,
        }
    },

    mounted() {
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),

        addResep(){        
            this.CLR_ERRORS();
            this.$router.push({ name:'formApotek', params:{ trxId:'baru' } });
        },

        updateListResep(data) {
            this.resepLists = null;
            if(data) { this.resepLists = data.data;}
        },

        fnResepSelected(data){
            if(data){
                if(data.status.toUpperCase() == 'REALISASI') {
                    this.$router.push({ name:'viewApotek', params:{ trxId:data.trx_id } });
                }
                else {
                    this.$router.push({ name:'formApotek', params:{ trxId:data.trx_id } });
                }
            }
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