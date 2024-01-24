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
                    <div uk-grid>
                        <div class="uk-width-expand">
                            <h5 style="font-weight:500;padding:0;margin:0;" class="uk-width-expand">ITEM PERMINTAAN</h5>
                        </div>
                        <div class="uk-width-auto">
                            <button class="uk-width-auto" @click.prevent="addItemToPurchase"><span uk-icon="icon:plus-circle;ratio:0.7"></span> Tambahkan Item PO</button>
                        </div>
                    </div>
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:1em 0 0 0;">
                        <thead>
                            <th class="child-table-header" style="text-align:center;padding:1em;width:10px;">
                                <input class="uk-checkbox" @change="selectAllChange" type="checkbox" v-model="selectAll" style="margin:0;padding:0;">
                            </th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">JENIS</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;width:160px;">Permintaan</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Produk ID</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Produk</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">JML</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Satuan</th>
                            <th class="child-table-header" style="width:80px; text-align:right;padding:1em;">Harga Beli</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Vendor</th>
                        </thead>
                        <tbody>
                            <tr v-for="child in rowData.detail_req">
                                <td style="width:10px;text-align:center;padding:1em;margin:0;">
                                    <input class="uk-checkbox" @change="itemSelectedChanged(child)" type="checkbox" v-model="child.is_pilih" style="margin:0;padding:0;">
                                </td>
                                <td style="width: 80px;padding:1em; text-align:left;font-weight: 500;">{{child.jenis_produk}}</td>
                                <td style="padding:1em;font-weight: 500;">
                                    <p style="margin:0;padding:0;">{{child.pr_id}}</p>
                                    <!-- <p style="margin:0;padding:0;font-weight: 300;font-size:10px;">{{child.jenis_produk}}</p> -->
                                </td>
                                <td style="padding:1em;font-weight: 400;">
                                    <p style="margin:0;padding:0;">{{child.produk_id}}</p>
                                    <!-- <p style="margin:0;padding:0;font-weight: 300;font-size:10px;">{{child.produk_id}}</p> -->
                                </td>
                                <td style="padding:1em;font-weight: 400;">
                                    <p style="margin:0;padding:0;">{{child.produk_nama}}</p>
                                    <!-- <p style="margin:0;padding:0;font-weight: 300;font-size:10px;">{{child.produk_id}}</p> -->
                                </td>
                                <td style="width: 80px;padding:1em; text-align:center;">{{child.jml_pr}}</td>
                                <td style="width: 80px;padding:1em; text-align:center;">{{child.pr_satuan}}</td>
                                <td style="width: 80px;padding:1em; text-align:right;">{{child.harga_beli}}</td>
                                <td style="padding:1em; text-align:center;">{{child.vendor_nama}}</td>
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
        selectAllCallback : { type:Function, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null }
    },
    emits : ['itemRequestSelected'],
    name : 'row-permintaan',
    data() {
        return {
            isExpanded : false,
            selectAll : true,
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
        
        selectAllChange(){
            if(this.selectAll) {
                this.rowData.detail_req.forEach(element => { element.is_pilih = true; });
            }
            else {
                this.rowData.detail_req.forEach(element => { element.is_pilih = false; });
            }
        },

        itemSelectedChanged(data){
            let itemUnchecked = this.rowData.detail_req.find(item => item.is_pilih == false);
            if(itemUnchecked) { this.selectAll = false; }
            else { this.selectAll = true; }
        },

        addItemToPurchase(){
            let data = JSON.parse(JSON.stringify(this.rowData.detail_req));
            this.$emit('itemRequestSelected',data);
        }
    }
}
</script>