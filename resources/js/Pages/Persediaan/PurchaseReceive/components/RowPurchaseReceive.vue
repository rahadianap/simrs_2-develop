<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td>
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu" @click.prevent="rowFunctions(rowData)">{{rowData.trx_id}}</a>
                <p style="font-size:11px; margin:0;padding:0;font-weight: 400;">{{rowData.tgl_transaksi}}</p>            
            </div>    
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.unit_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.depo_nama}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.vendor_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.vendor_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-size:14px;font-weight: 500;">{{formatCurrency(rowData.total)}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">Inv. {{rowData.no_invoice}}</p>
            <p style="margin:0;padding:0;font-size:11px;">{{rowData.catatan}}</p>
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
            <td colspan="9" v-if="isExpanded" style="background-color:#fefefe;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                    <div uk-grid>
                        <div class="uk-width-expand">
                            <h5 style="font-weight:500;padding:0;margin:0;" class="uk-width-expand">ITEM PENERIMAAN</h5>
                        </div>
                    </div>
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:1em 0 0 0;">
                        <thead>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Produk ID</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Produk</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">JML</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Satuan</th>
                            <th class="child-table-header" style="width:80px; text-align:right;padding:1em;">Harga</th>
                            <th class="child-table-header" style="width:80px; text-align:right;padding:1em;">Diskon</th>
                            <th class="child-table-header" style="width:80px; text-align:right;padding:1em;">Subtotal</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Pajak</th>                            
                        </thead>
                        <tbody>
                            <tr v-for="child in rowData.detail">
                                <td style="padding:1em;font-weight: 500;">
                                    <p style="margin:0;padding:0;">{{child.produk_id}}</p>
                                    <!-- <p style="margin:0;padding:0;font-weight: 300;font-size:10px;">{{child.produk_id}}</p> -->
                                </td>
                                <td style="padding:1em;font-weight: 400;">
                                    <p style="margin:0;padding:0;">{{child.produk_nama}}</p>
                                    <!-- <p style="margin:0;padding:0;font-weight: 300;font-size:10px;">{{child.produk_id}}</p> -->
                                </td>
                                <td style="width: 80px;padding:1em; text-align:center;">{{child.jml_por}}</td>
                                <td style="width: 80px;padding:1em; text-align:center;">{{child.satuan_beli}}</td>
                                <td style="width: 80px;padding:1em; text-align:right;">{{formatCurrency(child.harga)}}</td>
                                <td style="padding:1em; text-align:right;">{{child.diskon}}%</td>
                                <td style="padding:1em; text-align:right;">{{formatCurrency(child.subtotal)}}</td>
                                <td style="padding:1em; text-align:center;">
                                    <div class="uk-form-controls"><input class="uk-checkbox" type="checkbox" disabled v-model="child.is_pajak"></div>
                                </td>                                
                            </tr>
                            <tr>
                                <td colspan="6" style="padding:1em;font-weight: 500;text-align: right;">Total</td>
                                <td style="padding:1em;font-weight: 500;text-align: right;">{{formatCurrency(rowData.subtotal)}}</td>
                                <td style="padding:1em;font-weight: 500;"></td>
                            </tr>
                            <tr>
                                <td colspan="6" style="padding:1em;font-weight: 500;text-align: right;">Ppn</td>
                                <td style="padding:1em;font-weight: 500;text-align: right;">{{formatCurrency(rowData.total_pajak)}}</td>
                                <td style="padding:1em;font-weight: 500;"></td>
                            </tr>
                            <tr>
                                <td colspan="6" style="padding:1em;font-weight: 500;text-align: right;">Grand Total</td>
                                <td style="padding:1em;font-weight: 500;text-align: right;">{{formatCurrency(rowData.total)}}</td>
                                <td style="padding:1em;font-weight: 500;"></td>
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
        rowFunctions : { type:Function, required:false, default:null }
    },
    emits : ['itemRequestSelected'],
    name : 'row-purchase-receive',
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