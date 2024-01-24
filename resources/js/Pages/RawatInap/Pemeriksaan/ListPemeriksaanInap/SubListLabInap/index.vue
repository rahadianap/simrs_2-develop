<template>
    <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">            
        <div class="uk-width-1-1@m">
            <button @click.prevent="tambahPemeriksaanLab">Tambah Pemeriksaan Laboratorium</button>
        </div>                                        
    </div>
    <div style="padding:0;margin:0;">
        <table class="uk-table uk-table-striped1 child-table1" style="padding:0;margin:0;">
            <thead>
                <th  style="background-color:#cc0202;color:white;" class="tb-header-transaksi" colspan="6">PEMERIKSAAN LABORATORIUM</th>
            </thead>
            <tbody>
                <row-sub-list-lab-inap v-if="inpatientLabLists" 
                    v-for="dt in inpatientLabLists" 
                    :rowData = "dt"
                    :fnEditCallback = "ubahDataPemeriksaan"
                    :fnDeleteCallback = "hapusDataPemeriksaan">
                </row-sub-list-lab-inap>
            </tbody>
        </table>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowSubListLabInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListLabInap/RowSubListLabInap.vue';

export default {
    name : 'sub-list-lab-inap',
    props : {
        trxId : { type:String, required:true },
        fnAddCallback : { type:Function, required:false },
    },
    emits : ['refreshLab'],
    
    components : {
        RowSubListLabInap,
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatInap',['inpatientTransaction','inpatientLabLists']),
    },


    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('labRegistrasi',['deleteRegistrasi']),
        

        ubahDataPemeriksaan(data){
            if(data) {
                this.$router.push({ name: 'dataLabInap', params: { trxId: this.trxId, labId: data.trx_id } });
            }
        },

        hapusDataPemeriksaan(data){            
            if(data) {                
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menghapus data permintaan pemeriksaan lab ini ${data.trx_id}. Lanjutkan ?`)){
                    this.deleteRegistrasi(data.trx_id).then((response) => {
                        if (response.success == true) {
                            alert('registrasi lab BERHASIL dibatalkan.');
                            this.retrieveTransaksiInap();
                        }
                        else { 
                            alert('registrasi lab GAGAL dibatalkan.'); 
                        }
                    })
                }
            }
        },

        retrieveTransaksiInap(){
            let trx = JSON.parse(JSON.stringify(this.inpatientTransaction));
            this.dataTransaksiInap(trx.trx_id);
        },

        tambahPemeriksaanLab(){
            this.$router.push({ name: 'dataLabInap', params: { trxId: this.trxId, labId: 'baru' } });
        },

    }
}
</script>