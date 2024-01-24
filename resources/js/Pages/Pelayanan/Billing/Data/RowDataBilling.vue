<template>
    <tr v-if="rowData" style="border-top:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;margin:0;padding:0.5em 1em 1em 1em;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td style="padding:0.5em;">
            <p style="margin:0; padding:0; font-weight:500; font-size: 12px; color:#000;">{{rowData.tgl_transaksi}}</p>
            <p style="margin:0; padding:0; font-weight:350; font-size: 11px;">{{rowData.trx_id}}</p>
        </td>
        <td style="background-color:#fff; padding:0.5em; margin:0;">
            <p style="color:#000; margin:0;padding:0;font-weight:500;font-size: 12px;">{{rowData.unit_nama}}</p>
            <p style="font-size:11px; color:#000; margin:0;padding:0;font-weight: 400;">{{rowData.dokter_nama}}</p>
        </td>
        <td style="background-color:#fff; padding:0.5em; width:80px; text-align:center;">
            <p class="uk-text-uppercase" style="width:80px; font-size:12px; color:#000; margin:0;padding:0;font-weight: 500;text-align:center;">{{ rowData.jns_transaksi }}</p>
        </td>    
        <td style="background-color:#fff;padding:0.5em; width:100px;">
            <p class="uk-text-uppercase" style="text-align:right; font-size:14px; color:#000; margin:0;padding:0;font-weight: 500;">{{ formatCurrency(rowData.total) }}</p>
        </td>
        <td style="width:50px;text-align: center;padding:0.5em;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_bayar" :disabled="rowData.interim_id !== null && rowData.interim_id !== ''" style="margin-left:5px;border:1px solid black;" @change="linkCallback(rowData)">
        </td>
    </tr>

    <tr v-if="isExpanded" v-for="trx in rowData.detail" style="background-color: #f5f5f5;">
        <td></td>
        <td style="width: 180px;padding:0.5em; text-align:left;">
            <p style="font-size:11px; color:#000; margin:0;padding:0;font-weight:500;">{{trx.item_id}}</p>
        </td>
        <td colspan="2" style="padding:0.5em; text-align:left;">
            <p style="font-size:11px; color:#000; margin:0;padding:0;font-weight:400;">{{trx.item_nama}}</p>
        </td>
        <td style="width: 80px;padding:0.5em; text-align:right;">
            <p style="font-size:11px; color:#000; margin:0;padding:0;font-weight:400;">{{formatCurrency(trx.subtotal)}}</p>
        </td>
        <td></td>
    </tr> 
</template>
<script>

export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null }
    },
    name : 'row-data-billing',
    data() {
        return {
            isExpanded : false,
            buffer : [],
        }
    },
    methods : {
        changeRowExpand() {
            this.isExpanded = !this.isExpanded;
            return false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString(); 
            return val;
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
    }
}
</script>