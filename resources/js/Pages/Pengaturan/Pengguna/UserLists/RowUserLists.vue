<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:40px;">
            <img class="uk-border-circle" :src="rowData.avatar" alt="" uk-img>
        </td> 
        <td>
            <div style="margin:0;padding:0;">
                <a href="#" @click.prevent="linkCallback(rowData)" class="row-link-menu">
                    {{rowData.email}}
                    <p style="margin:0;padding:0;font-weight:350;">{{rowData.username}}</p>
                </a>
            </div>      
        </td>
        <td>
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.nama_lengkap}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.no_telepon}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.tempat_lahir}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.tanggal_lahir}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.role_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.role_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.tgl_gabung}}</p>
        </td>
        <td>
            <p style="margin:0;padding:0;font-weight: 400;">{{rowData.bio_singkat}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_admin" disabled style="margin-left:5px;border:1px solid black;">
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
    emits : ['itemRequestSelected'],
    name : 'row-user-lists',
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