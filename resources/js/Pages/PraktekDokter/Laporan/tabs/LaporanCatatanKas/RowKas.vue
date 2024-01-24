<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">   
        <td style="background-color:white;padding:1em; color:black;">
            <p style="margin:0;padding:0;">{{rowData.tgl_transaksi}}</p>
        </td>
        <td style="background-color:white;padding:1em; color:black;">
            <p style="margin:0;padding:0;">{{rowData.deskripsi}}</p>
        </td>
        <td style="background-color:white;padding:1em; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;">{{rowData.metode_pembayaran}}</p>
        </td>
        <td style="background-color:white;padding:1em; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;">{{rowData.jenis_transaksi}}</p>
        </td>
        <td style="background-color:white;padding:1em; width:150px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;text-align: right;">{{ formatCurrency(rowData.jml_bayar) }}</p>
        </td>
        <td style="background-color:white;padding:1em; width:150px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;text-align: right;">{{ formatCurrency(rowData.jml_terima) }}</p>
        </td>
    </tr>
</template>

<script>
import { mapActions, mapMutations } from 'vuex';
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null },
    },
    components : {
    },
    computed : {
    },
    name : 'row-kas',
    data() {
        return {
            isExpandedReg : false,
            selectAll : true,
            buffer : [],
        }
    },
    methods : {        
        ...mapMutations(['CLR_ERRORS']),

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
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