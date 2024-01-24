<template>
    <tr v-if="rowData" style="border-bottom:1px solid #eee;">
        <td style="background-color:#fafafa;border:none;width:5px;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.jns_penjamin}}</p>
        </td> 
        <td>
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">
                    {{rowData.penjamin_nama}} ({{rowData.inisial}})
                </a>
                <div uk-dropdown="mode: click">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="rowFunctions" v-for="rf in rowFunctions">
                            <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                <span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <p style="font-size:11px; margin:0;padding:0;font-weight: 400;">{{rowData.penjamin_id}}</p>            
            </div>    
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.narahubung}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.no_telp}} | {{rowData.email}}</p>
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.buku_harga}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.buku_harga_id}}</p>
        </td>
        <td style="max-width: 220px;">
            <p style="margin:0;padding:0;font-size:12px;">{{rowData.alamat}}</p>
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
        rowFunctions : { type:Object, required:false, default:null }
    },
    name : 'row-asuransi',
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
        }

    }
}
</script>