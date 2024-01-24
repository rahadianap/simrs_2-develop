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
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.unit_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.vendor_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.vendor_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-size:14px;font-weight: 500;">{{formatCurrency(rowData.total)}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-size:12px;max-width:250px;">{{rowData.catatan}}</p>
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
                    <ul uk-tab style="padding:0;margin:0;border:none;">
                        <li><a href="#" style="font-size:14px;font-weight:500;">ITEM PEMBELIAN</a></li>           
                        <li><a href="#" style="font-size:14px;font-weight:500;">RIWAYAT PEMBELIAN</a></li>
                    </ul>
                    <ul class="uk-switcher" style="padding:0;margin:0;border:none;">
                        <li style="padding:0;margin:0;border:none;">
                            <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                <thead>
                                    <th class="child-table-header" style="text-align:left;padding:1em;">Jenis</th>
                                    <th class="child-table-header" style="text-align:left;padding:1em;">Produk ID</th>
                                    <th class="child-table-header" style="text-align:left;padding:1em;">Produk</th>
                                    <th class="child-table-header" style="text-align:center;padding:1em;">Satuan</th>
                                    <th class="child-table-header" style="width:80px; text-align:right;padding:1em;">PO</th>                      
                                    <th class="child-table-header" style="width:80px; text-align:right;padding:1em;">POR</th>   
                                    <th class="child-table-header" style="width:80px; text-align:right;padding:1em;">PORR</th>                      
                                </thead>
                                <tbody>
                                    <tr v-for="child in rowData.detail">
                                        <td class="uk-text-uppercase" style="padding:1em;font-weight: 500;">
                                            <p style="margin:0;padding:0;">{{child.jenis_produk}}</p>
                                        </td>
                                        <td class="uk-text-uppercase" style="padding:1em;font-weight: 500;">
                                            <p style="margin:0;padding:0;">{{child.produk_id}}</p>
                                        </td>
                                        <td class="uk-text-uppercase" style="padding:1em;">
                                            <p style="margin:0;padding:0;">{{child.produk_nama}}</p>
                                        </td>
                                        <td style="width: 80px;padding:1em; font-size:12px; text-align:center;">{{child.satuan_beli}}</td>
                                                                                
                                        <td style="width: 80px;padding:1em; font-size:12px; text-align:right;">{{child.jml_po}}</td>
                                        <td style="width: 80px;padding:1em; font-size:12px; text-align:right;">{{child.jml_por}}</td>
                                        <td style="width: 80px;padding:1em; font-size:12px; text-align:right;">{{child.jml_porr}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                        <li style="padding:0;margin:0;border:none;">
                            <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                <thead>
                                    <th class="child-table-header" style="text-align:left;padding:1em;">Tanggal</th>
                                    <th class="child-table-header" style="text-align:left;padding:1em;">Jenis</th>
                                    <th class="child-table-header" style="text-align:left;padding:1em;">Unit dan Depo</th>                      
                                    <th class="child-table-header" style="width:100px; text-align:right;padding:1em;">Sub Total</th>   
                                    <th class="child-table-header" style="width:100px; text-align:right;padding:1em;">Diskon</th>   
                                    <th class="child-table-header" style="width:100px; text-align:right;padding:1em;">PPN</th>   
                                    <th class="child-table-header" style="width:100px; text-align:right;padding:1em;">Total</th>   
                                    <th class="child-table-header" style="width:80px; text-align:left;padding:1em;">Status</th>                         
                                </thead>
                                <tbody>
                                    <tr v-for="child in rowData.history">
                                        <td class="uk-text-uppercase" style="padding:1em;font-weight: 500;">
                                            <p style="margin:0;padding:0;">{{child.tgl_transaksi}}</p>
                                        </td>
                                        <td class="uk-text-uppercase" style="padding:1em;font-weight: 500;">
                                            <p style="margin:0;padding:0;">{{child.jns_transaksi}}</p>
                                            <p style="margin:0;padding:0;font-weight:400;font-size: 11px;">{{child.trx_id}}</p>
                                        </td>
                                        <td class="uk-text-uppercase" style="padding:1em;font-weight: 500;">
                                            <p style="margin:0;padding:0;font-size:12px;">{{child.unit_nama}}</p>
                                            <p style="margin:0;padding:0;font-size:11px;font-weight: 300;">{{child.depo_nama}}</p>
                                        </td>
                                        <td style="width: 80px;padding:1em; font-size:12px; font-weight: 500; text-align:right;">{{formatCurrency(child.subtotal)}}</td>
                                        <td style="width: 80px;padding:1em; font-size:12px; font-weight: 500; text-align:right;">{{formatCurrency(child.total_diskon)}}</td>
                                        <td style="width: 80px;padding:1em; font-size:12px; font-weight: 500; text-align:right;">{{formatCurrency(child.total_pajak)}}</td>
                                        <td style="width: 80px;padding:1em; font-size:12px; font-weight: 500; text-align:right;">{{formatCurrency(child.total)}}</td>
                                        <td style="width: 80px;padding:1em; text-align:left;">{{child.status}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                    </ul>
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
    name : 'row-purchase-management',
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