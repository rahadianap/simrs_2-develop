<template>
    <div style="padding:0;margin:0;">
        <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">            
            <div class="uk-width-auto@m">
                <button @click.prevent="TambahPemeriksaan">Tambah Pemeriksaan</button>
            </div>                                        
        </div>
        <div style="padding:0 0.5em 0.2em 0.5em;">
            <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                <thead>
                    <th class="tb-header-reg" style="width:20px;text-align:center;color:white; background-color: #cc0202;"></th>
                    <th class="tb-header-reg" style="width:200px;text-align:left;color:white; background-color: #cc0202;">ID</th>
                    <th class="tb-header-reg" style="width:120px;text-align:left;color:white; background-color: #cc0202;">Tanggal</th>
                    <th class="tb-header-reg" style="text-align:left;color:white; background-color: #cc0202;">Dokter</th>
                    <th class="tb-header-reg" style="width:80px;text-align:right;color:white; background-color: #cc0202;">Total</th>                                                
                    <th class="tb-header-reg" style="width:40px;text-align:center;color:white; background-color: #cc0202;">Status</th>
                    <th class="tb-header-reg" style="width:120px;text-align:center;color:white; background-color: #cc0202;">...</th>
                </thead>
                <tbody>
                    <row-list-tindakan-inap 
                        v-if="inpatientExamination" 
                        v-for="dt in inpatientExamination"
                        :rowData = "dt"
                        :linkCallback = "EditPemeriksaan">
                    </row-list-tindakan-inap>
                    <tr v-else>
                        <td colspan="7" style="font-style:italic;font-size:12px;color:black;">data tidak ditemukan</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowListTindakanInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListTindakanInap/RowListTindakanInap.vue';

export default {
    name : 'sub-list-tindakan-inap',
    props : {
        fnAddCallback : { type:Function, required:true },
        fnEditCallback : { type:Function, required:true },
    },
    components : { 
        RowListTindakanInap 
    },

    computed : {
        ...mapGetters('error'),
        ...mapGetters('rawatInap',['inpatientTransaction','inpatientExamination']),
    },
    methods : {
        ...mapActions('pemeriksaan',['createPemeriksaan','updatePemeriksaan','dataPemeriksaan','confirmPemeriksaan','unconfirmPemeriksaan']),
        ...mapActions('rawatInap',['dataTransaksiInap']),
        
        TambahPemeriksaan(){
            this.$router.push({ 
                name: 'dataTindakanInap', 
                params: { trxId: this.inpatientTransaction.trx_id, actId: 'baru' } 
            });
        },

        EditPemeriksaan(data){
            if(data.status.toUpperCase == 'DRAFT') {
                this.$router.push({ 
                    name: 'dataTindakanInap', 
                    params: { trxId: this.inpatientTransaction.trx_id, actId: data.trx_id } 
                });
            }
            else {
                this.needUnconfirmData(data);
            }
        },
        

        needUnconfirmData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan Mengubah status data pemeriksaan ${data.trx_id} kembali ke DRAFT. Lanjutkan ?`)){
                this.isLoading = true;
                this.unconfirmPemeriksaan(data.trx_id).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('konfirmasi pemeriksaan dibatalkan. Anda dapat mengubah kembali data pemeriksaan ini.');
                        this.$router.push({ 
                            name: 'dataTindakanInap', 
                            params: { trxId: this.inpatientTransaction.trx_id, actId: data.trx_id } 
                        });
                    }
                    else { 
                        this.isLoading = false;
                        alert('pembatalan konfirmasi pemeriksaan tidak berhasil.'); 
                    }
                })
            }
        },
    }
}
</script>