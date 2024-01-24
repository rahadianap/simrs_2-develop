<template>
    <div class="uk-container uk-container-large" style="padding:0;">
        <div style="padding:1em 1em 1em 1em;">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormRealisasiResep from '@/Pages/Farmasi/components/FormRealisasiResep.vue';
import FormResep from '@/Pages/Farmasi/components/FormResep.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormRealisasiResep,
        FormResep
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
                        { 'title':'Realisasi Resep', 'icon':'file-edit','callback':this.updateData },   
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
            searchUrl : '/prescriptions'
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

        /**tampikan modal untuk membuat vendor baru */
        addResep(){        
            this.CLR_ERRORS();
            this.$refs.formResep.resepBaru(this.poliTransaction);
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
        }
    }
}
</script>