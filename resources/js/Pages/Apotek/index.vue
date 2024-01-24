<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <!-- <header-page title="APOTEK" subTitle="penjualan resep dan obat apotek"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div> -->
        <div>
            <router-view></router-view>
        </div>


        <!-- <div style="margin-top:1em;">
            <base-table ref="tableResep"
                :columns = "tbColumns" 
                :childColumns = "tbChildColumns"
                childNameIndex = "detail"
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-apotek ref="formApotek" v-on:closed="refreshTable"></form-apotek>
        <cetakan-resep ref="cetakanResep" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></cetakan-resep> -->
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormApotek from '@/Pages/Apotek/components/FormApotek.vue';
import CetakanResep from '@/Pages/Apotek/cetakan/cetakanEtiket.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormApotek,
        CetakanResep,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransactionLists','poliFilterTable','poliTransaction']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : true,
                filterType : 'SIMPLE', 
            },
            tbColumns : [                
                { 
                    name : 'trx_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Cetak Etiket', 'icon':'file-edit','callback':this.printEtiket },   
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },
                    ],
                },
                { 
                    name : 'tgl_transaksi', 
                    title : 'Tanggal', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'jns_resep', 
                    title : 'Jenis', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'status', 
                    title : 'Status', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ],
            tbChildColumns :[
                { 
                    name : 'item_id', 
                    title : 'ID', 
                    colType : 'text', 
                    textAlign : 'left',
                },   
                { 
                    name : 'item_nama', 
                    title : 'Resep', 
                    colType : 'text', 
                    textAlign : 'left',
                },   
                { 
                    name : 'satuan', 
                    title : 'Satuan', 
                    textAlign : 'center',
                    colType : 'text',
                },
                { 
                    name : 'jml_resep', 
                    title : 'Jumlah', 
                    colType : 'text',
                    textAlign : 'center',                    
                },
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addResep },
            ],
            searchUrl : '/pharmacy'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('signa',['deleteSigna']),
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',[
            'CLR_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION_LISTS',
            'CLR_POLI_TRANSACTION_LISTS',
            'SET_FILTER_TABLE_POLI'
        ]),

        ...mapActions('rawatJalan',['dataTransaksiPoli','updateTransaksiPoli','createTransaksiPoli']),
        ...mapActions('cetakan', ['dataResep']),

        /**tampikan modal untuk membuat vendor baru */
        addResep(){        
            this.CLR_ERRORS();
            this.$refs.formApotek.resepBaru(this.poliTransaction);
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formRealisasiResep.approveResep(data.trx_id);
        },

        refreshTable() {
            this.$refs.tableFarmasi.retrieveData();
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus signa ${data.signa}. Lanjutkan ?`)){
                this.deleteSigna(data.signa_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableFarmasi.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        printEtiket(data){
            this.dataResep(data.trx_id).then((response) => {
                if (response.success == true) {
                    this.$refs.cetakanResep.generateReport(response.data);
                }
                else { alert (response.message) }
            });
        }
    }
}
</script>