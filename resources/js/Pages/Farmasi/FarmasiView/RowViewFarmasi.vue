<template>
    <template v-if="rowData.item_tipe == 'ITEM_RACIKAN' ">
        <tr style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">
            <td style="width:20px; padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_tipe}}</p> 
            </td>
            <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.group_racikan}} - {{rowData.item_nama}}</p>
            </td>
            <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p style="margin:0;padding:0;font-weight: 400;font-style:italic;"><strong>{{ rowData.signa }}</strong> {{ rowData.signa_deskripsi }}</p>
            </td>
            <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.nilai)}}</td>
            <td style="width:40px;padding:1em; font-size: 12px; text-align:center; color:black;">{{ rowData.jml_ambil }} {{rowData.satuan}}</td>
            <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.subtotal)}}</td>
        </tr> 
    </template>
    <template v-else-if="rowData.item_tipe == 'HEADER_RACIKAN' ">
        <tr style="border-bottom:1px solid #ccc;background-color:fff;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">
            <td style="width:20px; padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_tipe}}</p> 
            </td>
            <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.group_racikan}}</p>
            </td>
            <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <!-- <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.group_racikan}}</p> -->
                <p style="margin:0;padding:0;font-weight: 400;font-style:italic;"><strong>{{ rowData.signa }}</strong> {{ rowData.signa_deskripsi }}</p>
            </td>
            <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.nilai)}}</td>
            <td style="width:40px;padding:1em; font-size: 12px; text-align:center; color:black;">{{ rowData.jml_ambil }} {{rowData.satuan}}</td>
            <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.subtotal)}}</td>
        </tr>
    </template>
    <template v-else>
        <tr style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">
            <td style="width:20px; padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_tipe}}</p> 
            </td>
            <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_nama}}</p>
            </td>
            <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p style="margin:0;padding:0;font-weight: 400;font-style:italic;"><strong>{{ rowData.signa }}</strong> {{ rowData.signa_deskripsi }}</p>
            </td>
            <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.nilai)}}</td>
            <td style="width:40px;padding:1em; font-size: 12px; text-align:center; color:black;">{{ rowData.jml_ambil }} {{rowData.satuan}}</td>
            <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.subtotal)}}</td>
        </tr>
    </template>
</template>

<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
    },
    name : 'row-view-farmasi',
    data() {
        return {
            isExpandedReg : false,
            selectAll : true,
            buffer : [],
        }
    },
    methods : {
        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        },
    }
}
</script>