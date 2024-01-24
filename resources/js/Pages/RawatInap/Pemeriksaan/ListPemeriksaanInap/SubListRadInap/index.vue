<template>
    <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">            
        <div class="uk-width-1-1@m">
            <button @click.prevent="tambahPemeriksaanRad">Tambah Pemeriksaan Radiologi</button>
        </div>                                        
    </div>
    <div style="padding:0;margin:0;">
        <table class="uk-table uk-table-striped child-table1" style="padding:0;margin:0;">
            <thead>
                <th style="background-color:#cc0202;color:white;" class="tb-header-transaksi" colspan="6">PEMERIKSAAN RADIOLOGI</th>
            </thead>
            
            <tbody>
                <row-sub-list-rad-inap
                    v-if="inpatientRadLists" 
                    v-for="dt in inpatientRadLists"
                    :rowData = "dt" 
                    :fnEditCallback = "ubahDataPemeriksaanRad"
                    :fnDeleteCallback = "hapusDataPemeriksaanRad">
                </row-sub-list-rad-inap>
            </tbody>
        </table>
    </div>
</template>

<script>

import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowSubListRadInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListRadInap/RowSubListRadInap.vue';

export default {
    name : 'sub-list-rad-inap',
    props : {
        trxId : { type:String, required:true },
        fnAddCallback : { type:Function, required:false },
    },
    
    emits : ['refreshRad'],
    components : {
        RowSubListRadInap,
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatInap',['inpatientTransaction','inpatientRadLists']),
    },

    mounted(){

    },
    methods :{
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('radiologi',['createRadiologi','deleteRadiologi','updateRadiologi','updateHasilRadiologi','dataRadiologi','listsRegRad','dataRegRad']),
               

        ubahDataPemeriksaanRad(data){
            if(data) {
                this.$router.push({ name: 'dataRadinap', params: { trxId: this.trxId, radId: data.trx_id } });
            }
        },

        hapusDataPemeriksaanRad(data){            
            if(data) {                
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menghapus data permintaan pemeriksaan ini ${data.trx_id}. Lanjutkan ?`)){
                    this.deleteRadiologi(data.trx_id).then((response) => {
                        if (response.success == true) {
                            alert('data registrasi pemeriksaan Radiologi BERHASIL dibatalkan.');
                            this.retrieveTransaksiInap();
                        }
                        else { 
                            alert('registrasi lab tidak berhasil dibatalkan.'); 
                        }
                    })
                }
            }
        },

        retrieveTransaksiInap(){
            let trx = JSON.parse(JSON.stringify(this.inpatientTransaction));
            this.dataTransaksiInap(trx.trx_id);
        },

        tambahPemeriksaanRad(){
            this.$router.push({ name: 'dataRadInap', params: { trxId: this.trxId, radId: 'baru' } });
        }
    }
}
</script>