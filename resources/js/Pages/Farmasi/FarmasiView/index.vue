<template>
    <div class="uk-container uk-container-large" style="margin-top:0; padding:1em 1em 2em 1em;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="formClosed" style="background-color:white;padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:1"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">RESEP OBAT</h4>
            </div>
        </div>

        <div  style=" background-color:#fff; margin-top:2em; padding:2em 1em 1em 1em;">
            <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                <div uk-spinner="ratio : 2"></div>
                <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
            </div>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-1">
                    <h4 style="font-weight:500;">Data Transaksi</h4>
                </div>
                <div class="uk-width-1-3@m" style="border-right:1px solid #ccc;">
                    <dl class="uk-description-list resep-description-list">
                        <dt>No. Registrasi</dt>
                        <dd>{{transaksi.trx_id}}</dd>
                        <dd>{{transaksi.dokter_nama}}</dd>
                    </dl>
                </div>
                <div class="uk-width-1-3@m" style="border-right:1px solid #ccc;">
                    <dl class="uk-description-list resep-description-list">
                        <dt>Pasien</dt>
                        <dd>{{transaksi.nama_pasien}}</dd>
                        <dd>{{transaksi.no_rm}} - {{transaksi.pasien_id}}</dd>
                    </dl>
                </div>
                <div class="uk-width-1-3@m" style="border-right:0px solid #ccc;">
                    <dl class="uk-description-list resep-description-list">
                        <dt>Status</dt>
                        <dd>{{transaksi.status}}</dd>
                    </dl>
                </div>
                
            </div>

            <div style="margin-top:2em;">
                <div class="uk-width-1-1">
                    <h4 style="font-weight:500;">Item Resep</h4>
                </div>
                <div class="uk-width-1-1" style="margin:0;padding:0;">
                    <p style="padding:0;margin:0;font-size: 14px;font-weight: 500;">
                        <span style="font-weight:700;">Depo : </span>{{ transaksi.depo_id }} - {{ transaksi.depo_nama }}
                    </p>
                </div>            
            </div>

            <div style="margin-top:1em;">
                <div style="padding:0;">
                    <table class="uk-table uk-table-striped1 child-table1" style="padding:0;margin:0;">
                        <thead>
                            <th class="tb-header-reg" style="text-align:center;color:white;background-color: #cc0202;">Tipe</th>
                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Item Resep</th>
                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Cara Pakai</th>
                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Harga</th>
                            <th class="tb-header-reg" style="width:80px;text-align:center;color:white;background-color: #cc0202;">Juml Ambil</th>
                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Subtotal</th>        
                        </thead>
                        <tbody>
                            <row-view-farmasi
                                v-if="transaksi.items" 
                                v-for="dt in transaksi.items"
                                :rowData = "dt">
                            </row-view-farmasi>
                            <tr>
                                <td colspan="5" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL</td>
                                <td style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(transaksi.total)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="padding:1em;text-align:right;">
                    <button type="button" class="uk-button" @click.prevent="cancelResep" style="border:2px solid black; background-color: whitesmoke;color:black;">Ubah Data</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowViewFarmasi from '@/Pages/Farmasi/FarmasiView/RowViewFarmasi.vue';

export default {
    name : 'farmasi-pasien',
    props : {
        trxId : { type:String, required:true },
    },

    components : {
        headerPage,
        BaseTable,
        RowViewFarmasi,
    },
    computed : {
        ...mapGetters(['errors']),
        
    },
    data() {
        return {
            isLoading : true,
            transaksi : {
                reg_id : null,
                trx_id : null,
                reff_trx_id : null,

                pasien_id : null,
                nama_pasien : null,
                no_rm : null,
                usia : null,
                
                unit_nama : null,
                unit_id : null,
                dokter_id : null,
                dokter_nama : null,
                
                tanggal_lahir : null,
                tempat_lahir : null,
                penjamin_id : null,
                penjamin_nama : null,
                depo_id : null,
                depo_nama : null,
                total : 0,
                status : null,
                is_aktif : true,
                jns_transaksi : 'RESEP',
                resep_lists : [],
            },
            
            isUpdate : false,

         }
    },
    mounted() {
        this.getResepData();
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('farmasi',['dataFarmasi','realisasiFarmasi','cancelFarmasi']),

        getResepData(){
            this.dataFarmasi(this.trxId).then((response) => {
                if (response.success == true) {
                    let dt = response.data;
                    this.fillDataTransaksi(dt);
                    this.isLoading = false;
                    this.isUpdate = true;
                }
                else { 
                    alert (response.message);
                    this.isLoading = false;
                }
            });
        },

        fillDataTransaksi(dt){
            this.transaksi.trx_id = dt.trx_id;
            this.transaksi.reg_id = dt.reg_id;
            this.transaksi.reff_trx_id = this.trxId;
            
            this.transaksi.dokter_id = dt.dokter_id;
            this.transaksi.dokter_nama = dt.dokter_nama;
            
            this.transaksi.pasien_id = dt.pasien_id;
            this.transaksi.nama_pasien = dt.nama_pasien.toUpperCase();
            this.transaksi.no_rm = dt.no_rm;
            this.transaksi.usia = dt.usia;
            this.transaksi.tanggal_lahir = dt.tgl_lahir;
            this.transaksi.tempat_lahir = dt.tempat_lahir;
            
            this.transaksi.unit_id = dt.unit_id;
            this.transaksi.unit_nama = dt.unit_nama;
            this.transaksi.depo_id = dt.depo_id;
            this.transaksi.depo_nama = dt.depo_nama;

            this.transaksi.total = dt.total;
            this.transaksi.status = dt.status;
            this.transaksi.is_aktif = dt.is_aktif;
            this.transaksi.items = dt.items;
        },

        clearTransaksiData(){
            this.transaksi.reff_trx_id = null;
            this.transaksi.reg_id = null;
            this.transaksi.trx_id = null;

            this.transaksi.dokter_id = null;
            this.transaksi.dokter_nama = null;
            this.transaksi.pasien_id = null;
            this.transaksi.nama_pasien = null;
            this.transaksi.no_rm = null;
            this.transaksi.unit_id = null;
            this.transaksi.unit_nama = null;
            this.transaksi.usia = null;
            this.transaksi.tanggal_lahir = null;
            this.transaksi.tempat_lahir = null;

            this.transaksi.penjamin_id = null;
            this.transaksi.penjamin_nama = null;
        },

        formClosed(){
            this.clearTransaksiData();
            this.$router.push({ name:'resepIndex' });
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

        calculateTotal(){
            let total = 0;
            this.transaksi.items.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.transaksi.total = total; 
            })
        },
        
        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        cancelResep() {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengembalikan resep ke DRAFT kembali. Data transaksi dan billing akan dihapus dan stock akan dikembalikan. Lanjutkan ?`)){
                this.cancelFarmasi(this.trxId).then((response) => {
                    if (response.success == true) {
                        alert('data transaksi farmasi berhasil dikembalikan ke draft.');
                        this.isLoading = false;
                        this.$router.push({ name:'resepIndex'});
                    }
                    else { 
                        this.isLoading = false;    
                        alert (response.message);
                    }
                });
            };
            
        }
    }
}
</script>
<style>
dl.resep-description-list dt {
    margin:0;
    padding:0;
    font-size:11px;
    font-weight: bold;
    color:#333;
}

dl.resep-description-list dd {
    margin:0;
    padding:0;
    font-size:12px;
    font-weight: 400;
    color:#000;
}
</style>