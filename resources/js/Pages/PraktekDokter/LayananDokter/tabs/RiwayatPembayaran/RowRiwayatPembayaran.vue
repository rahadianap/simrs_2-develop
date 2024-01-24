<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">   
        <td style="background-color:white;border:none;width:20px;padding:1em 0.5em 0.5em 1em;">
            <a v-if="isExpandedReg" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
        </td>      
        <td style="background-color:white;padding:1em; color:black;">
            <p style="margin:0;padding:0;">{{rowData.tgl_bayar}}</p>
        </td>
        <td style="background-color:white;padding:1em; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;">{{rowData.interim_id}}</p>
        </td>
        <td style="background-color:white;padding:1em; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;">{{rowData.nama_pasien}}</p>
        </td>
        <td style="background-color:white;padding:1em; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;">{{rowData.dokter_nama}}</p>
        </td>
        <td style="background-color:white;padding:1em; width:150px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;text-align: right;">{{ formatCurrency(rowData.total_tagihan) }}</p>
        </td>
        <td style="background-color:white;padding:1em; width:150px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;text-align: right;"> 
                {{ formatCurrency(calculateDiscount(rowData.total_tagihan, rowData.diskon)) }}
                ({{ rowData.diskon }}%)
            </p>
        </td>
        <td style="background-color:white;padding:1em; width:150px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;text-align: right;">{{ formatCurrency(rowData.total_akhir) }}</p>
        </td>
        <td style="background-color:white;width:40px;margin:0;padding:1em 0.5em 0.2em 0.2em;">
            <span style="margin: 0 1em;color:#ABA7A7;">
                <div class="uk-inline">
                    <button uk-icon="more-vertical" type="button" tooltip="pilih tindakan"></button>
                    <div uk-dropdown="pos: bottom-right;animation: slide-top; animate-out: true; duration: 700;" style="border-radius:10px">
                        <ul class="uk-nav uk-dropdown-nav">
                            <li><a href="#" style="color:#333;" @click.prevent="fnPrintKwitansiCallback(rowData)">Cetak Kwitansi</a></li>
                            <li><a href="#" style="color:#333;" @click.prevent="fnEditCallback(rowData)">Ubah Pembayaran</a></li>
                            <li><a href="#" style="color:#F1595F;" @click.prevent="fnDeleteCallback(rowData)">Batalkan</a></li>
                        </ul>
                    </div>
                </div>
            </span>
        </td>
    </tr>
    <tr style="padding:0;margin:0;">
        <Transition>
            <td colspan="9" v-if="isExpandedReg" style="padding:0;margin:0;">
                <div style="padding:0;margin:0;">                    
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">                        
                        <tbody>
                            <tr style="border-bottom:1px solid #333;">
                                <td></td>
                                <td colspan="5" style="padding:0;">
                                    <h5 style="margin:0; padding:0; text-align: left; font-weight: 700;">DETAIL PEMBAYARAN</h5>
                                </td>
                            </tr>
                            <template v-if="rowData.detail" v-for="dtl in rowData.detail">
                                <tr style="border-bottom:1px solid #333;" v-bind:class="dtl.is_aktif != true ? 'inaktif':'' ">
                                    <td></td>
                                    <td style="padding:1em; font-size: 14px; color:#666;">{{ dtl.reg_id }}</td>
                                    <td style="padding:1em; font-size: 14px; color:#666;">{{ dtl.payment_id }}</td>
                                    <td style="padding:1em; font-size: 14px; color:#666;">{{ dtl.jns_payment }}</td>
                                    <td style="padding:1em; font-size: 14px; color:#666;text-align:right;">{{ formatCurrency(dtl.jml_bayar) }}</td>
                                    <td style="width:30px;"></td>                                    
                                </tr>
                            </template>
                            <tr>
                                <td></td>
                                <td colspan="3" style="padding:1em; font-size: 14px; color:#666;border-top:1px solid #333;text-align:right;">Total Bayar</td>
                                <td style="padding:1em; font-size: 14px; color:#666;text-align:right;border-top:1px solid #333;">{{ formatCurrency(rowData.jml_bayar) }}</td>
                                <td style="width:30px;"></td>                                    
                            </tr>

                            <tr>
                                <td></td>
                                <td colspan="3" style="padding:1em; font-size: 14px; color:#666;text-align:right;">Kembalian</td>
                                <td style="padding:1em; font-size: 14px; color:#666;text-align:right;">({{ formatCurrency(rowData.kembalian) }})</td>
                                <td style="width:30px;"></td>                                    
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="3" style="padding:1em; font-size: 14px; color:#666;border-top:1px solid #333;text-align:right;">Total Terima</td>
                                <td style="padding:1em; font-size: 14px; color:#666;text-align:right;border-top:1px solid #333;">{{ formatCurrency(rowData.total_akhir) }}</td>
                                <td style="width:30px;"></td>                                    
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </Transition>
    </tr>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowFormResep from '@/Pages/RawatJalan/Pemeriksaan/SubFormResep/RowFormResep.vue';
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null },
        fnEditCallback : { type:Function, required:true },
        fnDeleteCallback : { type:Function, required:true },
        fnPrintKwitansiCallback : { type:Function, required:true },
    },
    components : {
        RowFormResep,
    },
    computed : {
        ...mapGetters('rawatJalan',['poliTransaction']),
    },
    name : 'row-riwayat-pembayaran',
    data() {
        return {
            isExpandedReg : false,
            selectAll : true,
            buffer : [],
        }
    },
    methods : {        
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('resep',['deletePrescription']),
        ...mapActions('rawatJalan',['dataTransaksiPoli']),

        calculateDiscount(totalTagihan, diskonPercentage) {
            const diskon = (diskonPercentage / 100) * totalTagihan;
            return diskon;
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        changeRowRegExpand() {
            this.isExpandedReg = !this.isExpandedReg;
            return false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        },

        getTotal(data){
            let total = 0;
            data.forEach(function(dt){
                total = (total*1) + (dt.nilai*1);
            });
            return total;
        },

        rowDataChange(){            
            this.rowData.diskon = 0;
            this.rowData.subtotal = this.rowData.jumlah * this.rowData.nilai * 1;            
            this.dataChange();
        },
    }
}
</script>
<style>
th.tb-header-reg-sub {
    padding:0.5em; 
    background-color:#fff; 
    color:black;
    font-weight: 500;
    font-size:14px;
}
</style>