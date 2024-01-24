<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <!-- <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a> -->
        </td> 
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.tgl_resep}}</p>
        </td>
        <td>
            <div style="margin:0;padding:0;">
                <a href="#" @click.prevent="linkCallback(rowData)" class="row-link-menu">
                    {{rowData.trx_id}}
                    <p style="margin:0;padding:0;font-weight:350;">{{rowData.reg_id}}</p>
                </a>
            </div>      
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.nama_pasien}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.dokter_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.dokter_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.depo_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">ID. {{rowData.depo_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.status}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
</template>

<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        selectAllCallback : { type:Function, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null }
    },
    emits : ['resepSelected'],
    name : 'row-antrian-apotek',
    data() {
        return {
            isExpanded : false,
            selectAll : true,
            buffer : [],
        }
    },
    methods : {
        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

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