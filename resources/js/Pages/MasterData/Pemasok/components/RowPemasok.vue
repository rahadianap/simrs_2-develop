<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <!-- <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a> -->
            <span v-if="rowData.status === 'Terverifikasi'" uk-icon="check"></span>
            <span v-else uk-icon="question"></span>
        </td> 
        <td>
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">
                    {{rowData.vendor_nama}} ({{rowData.inisial}})
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
                <p style="font-size:11px; margin:0;padding:0;font-weight: 400;">{{rowData.vendor_id}}</p>            
            </div>    
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.narahubung}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.telepon}} | {{rowData.email}}</p>
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.no_kontrak}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{dateFormatting(rowData.tgl_kontrak)}} sd {{dateFormatting(rowData.tgl_akhir_kontrak)}}</p>
        </td>
        <td>
            <p style="margin:0;padding:0;font-size:12px;">{{rowData.alamat}}</p>
        </td>        
        <td>
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
        rowFunctions : { type:Object, required:false, default:null }
    },
    name : 'row-pemasok',
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