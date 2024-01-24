<template>
    <div style="padding:1em 0 0 0;">
        <!-- <header-page title="FARMASI - RESEP & OBAT" subTitle="resep dan obat"></header-page> -->
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div class="hims-form-subpage" style="padding:2em 0 0 0; margin:0; border:none; width:100%;">
            <ul id="switcherPemeriksaanPoli" class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0;margin:0;">
                <li style="padding:0;"><div><a href="#"><h5>Antrian Resep</h5></a></div></li>
                <li style="padding:0;"><div><a href="#"><h5>Daftar Pasien</h5></a></div></li>
            </ul>
            <ul class="uk-switcher">
                <li style="padding:0;">
                    <list-antrian-resep></list-antrian-resep>
                </li>
                <li style="padding:0;">
                    <list-pasien-resep></list-pasien-resep>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormRealisasiResep from '@/Pages/Farmasi/components/FormRealisasiResep.vue';
import FormResep from '@/Pages/Farmasi/components/FormResep.vue';

import ListPasienResep from '@/Pages/Farmasi/FarmasiHistory/ListPasienResep.vue';
import ListAntrianResep from '@/Pages/Farmasi/FarmasiHistory/ListAntrianResep.vue';

export default {
    name : 'resep-history',
    components : {
        headerPage,
        BaseTable,
        FormRealisasiResep,
        FormResep,
        ListPasienResep,
        ListAntrianResep,
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
            // console.log(data);
            // UIkit.modal('#formRealisasiResep').show();
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