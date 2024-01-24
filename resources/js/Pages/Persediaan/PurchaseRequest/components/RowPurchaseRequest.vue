<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td>
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">
                    {{rowData.pr_id}}
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
                <p style="font-size:11px; margin:0;padding:0;font-weight: 400;">{{rowData.tgl_pr}}</p>            
            </div>    
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.unit_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.unit_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.depo_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.depo_id}}</p>
        </td>
        
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{dateFormatting(rowData.tgl_kebutuhan)}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-size:12px;">{{rowData.catatan}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.status}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="8" v-if="isExpanded" style="background-color:#fefefe;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                    <h5 style="font-weight:500;padding:0;margin:0;">ITEM PERMINTAAN</h5>
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:1em 0 0 0;">
                        <thead>
                            <th class="child-table-header" style="text-align:center;padding:1em;">JENIS</th>
                            <th class="child-table-header" style="width:150px;text-align:left;padding:1em;">Produk ID</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Nama Produk</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">JML</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Satuan</th>
                        </thead>
                        <tbody>
                            <tr v-for="child in rowData.detail_req">
                                <td style="width: 80px;padding:1em; text-align:center;font-weight: 500;">{{child.jenis_produk}}</td>
                                <td style="width: 150px;padding:1em;font-weight: 500;">{{child.produk_id}}</td>
                                <td style="width: auto;padding:1em;">{{child.produk_nama}}</td>
                                <td style="width: 80px;padding:1em; text-align:center;">{{child.jml_pr}}</td>
                                <td style="width: 80px;padding:1em; text-align:center;">{{child.pr_satuan}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </Transition>
    </tr>
</template>
<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null }
    },
    name : 'row-purchase-request',
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