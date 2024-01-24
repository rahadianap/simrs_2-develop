<template>
    <tr v-if="data" v-bind:class="data.is_aktif != true ? 'inaktif':'' ">
        <td style="padding:1em 0.5em;margin:0;color:black;font-size:12px;max-width:80px;font-weight:500;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td>
        <td style="padding:1em 0.5em; margin:0; color:black; font-size:12px; max-width:80px; font-weight:500;">
            {{data.jenis_produk}}
        </td>
        <td style="padding:1em 0.5em; margin:0; color:black; font-size:12px;">
            {{data.produk_nama}}
        </td>
        <td style="padding:0.5em; margin:0; color:black; font-size:12px;">
            <div class="uk-form-controls">
                <input class="uk-input uk-form-small" type="number" v-model="data.jml_po"  style="text-align:center;" @change="updateData(data)">
            </div>
        </td>
        <td style="padding:1em 0.5em; margin:0; color:black; font-size:12px; text-align:center;">
            {{data.satuan_beli}}
        </td>
        <td style="padding:0.5em; margin:0; color:black; font-size:12px;">
            <div class="uk-form-controls">
                <input class="uk-input uk-form-small" type="number" v-model="data.harga" style="text-align:right;"  @change="updateData(data)">
            </div>
        </td>
        <td style="padding:0.5em; margin:0; color:black; font-size:12px;">
            <div class="uk-form-controls">
                <input class="uk-input uk-form-small" type="number"  style="text-align:center;"  v-model="data.diskon"  @change="updateData(data)">
            </div>
        </td>
        <td style="padding:1em 0.5em; margin:0; color:black; font-size:14px; text-align:right;">
            {{formatCurrency(data.subtotal)}}
        </td>
        <td style="padding:1em 0.5em; margin:0; color:black; font-size:12px; text-align:center;">
            <div class="uk-form-controls"><input class="uk-checkbox" type="checkbox" v-model="data.is_pajak"  @change="updateData(data)"></div>
        </td>
        <td style="padding:1em 0.5em; margin:0; color:black; font-size:12px; text-align:center;">
            <div class="uk-form-controls"><input class="uk-checkbox" type="checkbox" v-model="data.is_aktif" @change="updateData(data)"></div>
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="10" v-if="isExpanded" style="background-color:#fafafa;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">                                                        
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                        <thead>
                            <th class="child-table-header" style="text-align:left;padding:1em;width:160px;">Permintaan</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;width:140px;">Produk ID</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Produk</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">JML</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Satuan</th>
                            <th class="child-table-header" style="width:40px; text-align:center;padding:1em;">...</th>
                        </thead>
                        <tbody>
                            <tr v-for="child in data.items">
                                <td style="width:160px;padding:1em;font-weight: 500;">
                                    <p style="margin:0;padding:0;">{{child.pr_id}}</p>
                                </td>
                                <td style="width:140px; padding:1em;font-weight: 400;">
                                    <p style="margin:0;padding:0;">{{child.produk_id}}</p>
                                </td>
                                <td style="padding:1em;font-weight: 400;">
                                    <p style="margin:0;padding:0;">{{child.produk_nama}}</p>
                                </td>
                                <td style="width: 80px;padding:1em; text-align:center;">{{child.jml_pr}}</td>
                                <td style="width: 80px;padding:1em; text-align:center;">{{child.pr_satuan}}</td>
                                <td style="padding:1em 0.5em;margin:0;color:black;font-size:12px;width:40px;font-weight:500;text-align:center;">
                                    <a href="#" uk-icon="icon:trash;ratio:1;" @click.prevent="removeItem(child)"></a>
                                </td>
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
    name:'row-item-order',
    props : {
        data : { type:Object, required:false, default:null },
        removeData : { type:Function, required:false, default:null },
        updateData : { type:Function, required:false, default:null },
    },
    data() {
        return {
            isExpanded : false,
        }
    },
    watch : {
        'data' : function(newVal, oldVal){
            this.init();
        },

        'data.items' : function(newVal, oldVal){
            this.init();
        },
    },

    methods : {
        init(){
            alert('update data');
            this.calculateData();
            
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        changeRowExpand(){
            this.isExpanded = !this.isExpanded; return false;
        },

        removeItem(data){
            if(confirm(`Proses ini akan menghapus referensi data dari ${data.produk_nama}. Lanjutkan ?`)){
                if(this.data.items.length > 1) {
                    this.data.items = this.data.items.filter(item => { return item.pr_detail_id !== data.pr_detail_id });
                    this.calculateData();
                }
                else {
                    alert('item detail tidak boleh kurang dari satu data. untuk menghapus item PO, gunakan fitur di baris item.');
                }
            }
        },

        calculateData(){
            this.data.jumlah = 0;
            let val = 0;
            this.data.items.forEach(function(dt){
                val = val + dt.jml_pr;
            });
            this.data.jumlah = val;
        }
    },
}
</script>