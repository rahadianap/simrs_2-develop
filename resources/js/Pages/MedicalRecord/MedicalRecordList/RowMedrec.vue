<template>
    <tr v-if="rowData" style="border-top:1px solid #ccc;">
        <td style="background-color:#fff;padding:0.5em;margin:0;">
            <p style="color:#000; margin:0;padding:0;font-weight:500;font-size: 12px;">{{rowData.jns_registrasi}}</p>
        </td>  
        <td style="padding:0.5em;">
            <a href="#" class="row-link-menu" style="margin:0;padding:0;display: block;" @click.prevent="changeRowExpand">
                <p style="margin:0;padding:0;font-weight:500;">{{rowData.reg_id}}</p>
                <p style="margin:0;padding:0;font-weight:350;font-size: 10px;">{{rowData.tgl_registrasi}}</p>
            </a>
        </td>
        <td style="background-color:#fff;padding:0.5em;margin:0;" >
            <p style="color:#000; margin:0;padding:0;font-weight:500;font-size: 12px;">{{rowData.dokter_nama}}</p>
            <p style="font-size:10px; color:#000; margin:0;padding:0;font-weight: 400;">{{rowData.unit_nama}}</p>
        </td>       
             
         
        <td style="background-color:#fff;padding:0.5em;">
            <p class="uk-text-uppercase" style="text-align:center; font-size:14px; color:#000; margin:0;padding:0;font-weight: 400;">{{ rowData.status_reg }}</p>
        </td>
    </tr>

    <!-- <tr v-if="isExpanded" v-for="trx in rowData.detail" style="background-color: #f5f5f5;">
        <td></td>
        <td style="width: 180px;padding:0.5em; text-align:left;">
            <p style="font-size:11px; color:#000; margin:0;padding:0;font-weight:500;">{{trx.item_id}}</p>
        </td>
        <td style="padding:0.5em; text-align:left;">
            <p style="font-size:11px; color:#000; margin:0;padding:0;font-weight:400;">{{trx.item_nama}}</p>
        </td>
        <td style="width: 80px;padding:0.5em; text-align:right;">
            <p style="text-align:right;font-size:11px; color:#000; margin:0;padding:0;font-weight:400;">{{formatCurrency(trx.subtotal)}}</p>
        </td>
        <td style="width: 80px;padding:0.5em; text-align:right;">
            <p style="text-align:right;font-size:11px; color:#000; margin:0;padding:0;font-weight:400;">{{formatCurrency(0)}}</p>
        </td>
        <td style="width: 80px;padding:0.5em; text-align:right;">
            <p style="font-size:11px; color:#000; margin:0;padding:0;font-weight:400;">{{formatCurrency(trx.subtotal)}}</p>
        </td>
        <td></td>
    </tr>  -->
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
            isExpanded : true,
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