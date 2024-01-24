<template>
    <tr v-if="rowData" style="border-top:1px solid #ccc;">
        <td style="width:5px;"></td>
        <td style="padding:1em;margin:0;font-weight:500;text-align:center;width:150px;">{{rowData.produk_id}}</td>
        <td style="padding:1em;margin:0;">{{rowData.produk_nama}}</td>
        <td style="padding:1em;margin:0;text-align:center;">{{rowData.jenis_produk}}</td>
        <td style="padding:1em;margin:0;text-align:center;width:80px;">{{rowData.jml_total}}</td>
        <td style="padding:0.5em 0 0 0;margin:0;text-align:center;width:80px;">
            <input type="number" class="uk-input uk-form-small" v-model="rowData.total_so" style="text-align:center;" @change="calculateSelisih(rowData)">
        </td>
        <td style="padding:1em;margin:0;text-align:center;width:80px;">{{rowData.selisih_so}}</td>
    </tr>  
</template>
<script>
export default {
    props : {
        rowData : { type:Object, required:true },
    },
    name : 'form-opname-row',
    data() {
        return { isExpanded : false, buffer : [], }
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

        calculateSelisih(data) {
            data.selisih_so = (data.total_so - data.jml_total);
        },
    }
}
</script>