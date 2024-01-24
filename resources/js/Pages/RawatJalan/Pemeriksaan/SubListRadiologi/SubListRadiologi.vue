<template>
    <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">            
        <div class="uk-width-1-1@m">
            <button @click.prevent="fnAddCallback">Tambah Pemeriksaan Radiologi</button>
        </div>                                        
    </div>
    <div style="padding:0;margin:0;">
        <table class="uk-table uk-table-striped child-table1" style="padding:0;margin:0;">
            <thead>
                <th style="background-color:#cc0202;color:white;" class="tb-header-transaksi" colspan="6">PEMERIKSAAN RADIOLOGI</th>
            </thead>
            
            <tbody>
                <row-sub-list-rad
                    v-if="data" 
                    v-for="dt in data"
                    :rowData = "dt" 
                    :fnEditCallback = "ubahDataPemeriksaanRad"
                    :fnDeleteCallback = "hapusDataPemeriksaanRad">
                </row-sub-list-rad>
            </tbody>
        </table>
    </div>
</template>

<script>

import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowSubListRad from '@/Pages/RawatJalan/Pemeriksaan/SubListRadiologi/RowSubListRad.vue';

export default {
    name : 'sub-list-radiologi',
    props : {
        trxId : { type:String, required:true },
        data : { type:Object, required:false },
        fnAddCallback : { type:Function, required:true },
        // fnEditCallback : { type:Function, required:false },
    },
    
    emits : ['refreshRad'],
    components : {
        RowSubListRad,
    },
    mounted(){

    },
    methods :{
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('labRegistrasi',['deleteRegistrasi']),
        ...mapActions('radiologi',['createRadiologi','deleteRadiologi','updateRadiologi','updateHasilRadiologi','dataRadiologi','listsRegRad','dataRegRad']),
        
        

        ubahDataPemeriksaanRad(data){
            if(data) {
                this.$router.push({ name: 'dataRadPoli', params: { trxId: this.trxId, radId: data.trx_id } });
            }
        },

        hapusDataPemeriksaanRad(data){            
            if(data) {                
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menghapus data permintaan pemeriksaan ini ${data.trx_id}. Lanjutkan ?`)){
                    this.deleteRadiologi(data.trx_id).then((response) => {
                        if (response.success == true) {
                            alert('registrasi BERHASIL dibatalkan.');
                            this.$emit('refreshLab',true);
                        }
                        else { 
                            alert('registrasi tidak berhasil dibatalkan.'); 
                        }
                    })
                }
            }
        }
    }
}
</script>