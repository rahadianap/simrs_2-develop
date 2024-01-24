<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">            
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.produk_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.produk_nama}}</p>
        </td>
        <td class="uk-text-uppercase" style="text-align:center;width:80px;">
            <p style="margin:0;padding:0;font-size:12px;font-weight: 500;">{{rowData.jumlah}}</p>
        </td>
        <td class="uk-text-uppercase" style="text-align:center;width:80px;">
            <p style="margin:0;padding:0;font-size:12px;">{{rowData.pr_satuan}}</p>
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
                            <button class="uk-width-auto"  @click.prevent="addItemToPurchase"><span uk-icon="icon:plus-circle;ratio:0.7"></span> Tambahkan Item PO</button>
                        </div>
                    </div>
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:1em 0 0 0;">
                        <thead>
                            <th class="child-table-header" style="text-align:left;padding:1em;">
                                <input class="uk-checkbox" @change.prevent="selectAllChange" v-model="selectAll" type="checkbox" style="margin:0 1em 0 0;padding:0;">
                            </th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">JENIS</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;width:160px;">Permintaan</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Produk</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">JML</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Satuan</th>
                            <th class="child-table-header" style="width:80px; text-align:right;padding:1em;">Harga Beli</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Vendor</th>
                            <!-- <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Aktif</th> -->
                        </thead>
                        <tbody>
                            <tr v-for="child in rowData.details">  
                                <td style="width: 20px;padding:1em; text-align:left;font-weight: 500;">
                                    <input class="uk-checkbox" @change="itemSelectedChanged(child)" v-model="child.is_pilih" type="checkbox" style="margin:0 1em 0 0;padding:0;">
                                </td>                                
                                <td style="width: 80px;padding:1em; text-align:left;font-weight: 500;">{{child.jenis_produk}}</td>
                                <td style="padding:1em;font-weight: 500;">
                                    <p style="margin:0;padding:0;">{{child.pr_id}}</p>
                                </td>
                                <td style="padding:1em;font-weight: 500;">
                                    <p style="margin:0;padding:0;">{{child.produk_nama}}</p>
                                    <p style="margin:0;padding:0;font-weight: 300;font-size:10px;">{{child.produk_id}}</p>
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
        rowFunctions : { type:Object, required:false, default:null },
        removeSelected : { type:Function, required:false, default:null },
        addSelected : { type:Function, required:false, default:null },        
    },
    emits :['itemRequestSelected'],
    name : 'row-permintaan-detail',
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
                this.rowData.details.forEach(element => { element.is_pilih = true; this.calculateTotalItem(); });
            }
            else {
                this.rowData.details.forEach(element => { element.is_pilih = false; this.calculateTotalItem(); });
            }
        },

        itemSelectedChanged(data){
            let itemUnchecked = this.rowData.details.find(item => item.is_pilih == false);
            if(itemUnchecked) { this.selectAll = false;  this.calculateTotalItem(); }
            else { this.selectAll = true; this.calculateTotalItem(); }
        },

        calculateTotalItem(){
            this.rowData.jumlah = 0;
            this.rowData.details.forEach(element => { 
                if(element.is_pilih == true) {
                    this.rowData.jumlah = this.rowData.jumlah + element.jml_pr;
                } 
            });
        },

        addItemToPurchase(){
            let data = JSON.parse(JSON.stringify(this.rowData.details));
            this.$emit('itemRequestSelected',data);
        }
    }
}
</script>