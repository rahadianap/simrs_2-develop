<template>
    <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">            
        <div class="uk-width-1-1@m">
            <button @click.prevent="fnAddCallback">Tambah Pemeriksaan Laboratorium</button>
        </div>                                        
    </div>
    <div style="padding:0;margin:0;">
        <table class="uk-table uk-table-striped1 child-table1" style="padding:0;margin:0;">
            <thead>
                <th  style="background-color:#cc0202;color:white;" class="tb-header-transaksi" colspan="6">PEMERIKSAAN LABORATORIUM</th>
            </thead>
            <tbody>
                <row-sub-list-lab v-if="data" 
                    v-for="dt in data" 
                    :rowData = "dt"
                    :fnEditCallback = "ubahDataPemeriksaan"
                    :fnDeleteCallback = "hapusDataPemeriksaan">
                </row-sub-list-lab>
            </tbody>
        </table>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowSubListLab from '@/Pages/RawatJalan/Pemeriksaan/SubListLaboratorium/RowSubListLab.vue';

export default {
    name : 'sub-list-laboratorium',
    props : {
        trxId : { type:String, required:true },
        data : { type:Object, required:false },
        fnAddCallback : { type:Function, required:true },
    },
    emits : ['refreshLab'],
    
    components : {
        RowSubListLab,
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('labRegistrasi',['deleteRegistrasi']),
        

        ubahDataPemeriksaan(data){
            if(data) {
                // console.log(data);
                this.$router.push({ name: 'dataLabPoli', params: { trxId: this.trxId, labId: data.trx_id } });
            }
        },

        hapusDataPemeriksaan(data){            
            if(data) {                
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menghapus data permintaan pemeriksaan lab ini ${data.trx_id}. Lanjutkan ?`)){
                    this.deleteRegistrasi(data.trx_id).then((response) => {
                        if (response.success == true) {
                            // this.isLoading = false; 
                            // this.isUpdate = false;
                            alert('registrasi lab BERHASIL dibatalkan.');
                            this.$emit('refreshLab',true);
                        }
                        else { 
                            // this.isLoading = false;
                            alert('registrasi lab tidak berhasil dibatalkan.'); 
                        }
                    })
                }
            }
        }
    }
}
</script>