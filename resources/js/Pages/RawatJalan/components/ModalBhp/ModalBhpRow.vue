<template>
    <tr v-if="rowData" style="border-top:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:10px;">
        </td> 
        <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu" @click.prevent="fnActSelected(rowData)">
                    {{rowData.produk_id}}
                </a>
            </div>    
        </td>
        <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu" @click.prevent="fnActSelected(rowData)">
                    {{rowData.produk_nama}}
                </a>
            </div>    
        </td>
        <td>
            <p style="margin:0;padding:0;">{{rowData.jenis_produk}}</p>
        </td>
        <td>
            <p style="margin:0;padding:0;">{{rowData.satuan}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <button @click.prevent="fnActSelected(rowData)">Pilih</button>
        </td>
    </tr>
</template>

<script>
export default {
    name : 'modal-bhp-row',
    props : {
       rowData : { type:Object, required:false, default:null },
       fnActSelected : {type:Function, required:true },
    },
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
    }
}
</script>