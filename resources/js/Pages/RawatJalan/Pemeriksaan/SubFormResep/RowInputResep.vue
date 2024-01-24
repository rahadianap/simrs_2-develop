<template>
    <tr style="border-bottom:1px solid #ccc;">
        <td style="padding:1em; font-size: 12px; text-align:center; color:black;">
            <!-- <input v-model="itemResep.jumlah" class="uk-input uk-form-small" type="number" style="text-align: center;" @change="rowDataChange"> -->
            <!-- <select class="uk-select uk-form-small">
                <option value="0">Item</option>
                <option value="1">Racikan</option>
            </select> -->
            <input class="uk-checkbox" type="checkbox" v-model="itemResep.is_racikan" style="text-align: center;" @change="racikanChange">
        </td>

        <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
            <search-select  ref="searchObat"
                :config = configSelect
                searchUrl = '/products/medical/items'
                placeholder = "pilih item obat"
                captionField = "produk_nama"
                valueField = "produk_nama"
                :itemListData = produkDesc
                :value = "itemResep.produk_nama"
                v-on:item-select = "itemSelected"
            ></search-select>
        </td>

        <td style="padding:0.4em; font-size: 12px; color:black;">
            <search-select ref="searchSigna"
                :config = configSelect
                searchUrl = '/signas'
                placeholder = "pilih signa"
                captionField = "signa"
                valueField = "signa"
                :itemListData = signaDesc
                :value = "itemResep.signa"
                v-on:item-select = "signaSelected"
            ></search-select>
        </td>

        <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
            <input v-model="itemResep.jumlah" class="uk-input uk-form-small" type="number" style="text-align: center;" @change="rowDataChange">
        </td>
        <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
            <search-select  ref="searchSatuan"
                :config = configSelect
                searchUrl = '/uoms'
                placeholder = "pilih satuan"
                captionField = "satuan_id"
                valueField = "satuan_id"
                :itemListData = satuanDesc
                :value = "itemResep.satuan"
                v-on:item-select = "satuanSelected"
            ></search-select>
        </td>
        <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemResep.harga)}}</td>
        <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemResep.subtotal)}}</td>
        
        <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
            <button type="button" @click.prevent="addFunction(itemResep)">Tambah</button>
        </td>
    </tr>
</template>

<script>
import SearchSelect from '@/Components/SearchSelect.vue';
export default {
    props : {
        addFunction : { type:Function, required : true },
        itemResep : { type:Object, required:true },
    //     urlTindakan : { type:String, required:false, default:''},
    //     dataChange : { type:Function, required:false, default:null },
    //     activationChange : { type:Function, required:false, default:null },
    //     linkCallback : { type:Function, required:false, default:null },
    //     actSelected : { type:Function , required:true },
    //     docSelected : { type:Function, required:true }
    },
    name : 'row-input-resep',
    components : {
        SearchSelect,
    },
    data() {
        return {
            isExpandedReg : false,
            selectAll : true,
            buffer : [],
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m uk-text-uppercase",
                compStyle : "padding:0;margin:0;",
                retrieveAll : false,
            },

            produkDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],

            signaDesc : [
                { colName : 'signa', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
            ],
            satuanDesc : [
                { colName : 'satuan_id', labelData : '', isTitle : true },
                { colName : 'satuan_nama', labelData : '', isTitle : false },
            ],
            produk : {
                produk_nama : null,
                produk_id : null,
                signa : null,
                jumlah : 1,
                satuan : null,
                harga : 0,
                subtotal : 0,
            },
        }
    },
    methods : {
        itemSelected(data) {
            if(data) {
                console.log(data);
                this.itemResep.produk_nama = data.produk_nama;
                this.itemResep.produk_id = data.produk_id;
                this.itemResep.signa = data.signa;
                this.itemResep.satuan = data.satuan;
                this.itemResep.harga = data.hna;
                this.itemResep.subtotal = data.hna;
            }
        },  

        signaSelected(data) {
            if(data) {
                this.itemResep.signa = data.signa;
                this.itemResep.signa_deskripsi = data.deskripsi;
            }
        },

        satuanSelected(data) {
            if(data) { this.itemResep.satuan = data.satuan_id; }
        },

        racikanChange(){

        },
        // addFunction(data){

        // },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        changeRowRegExpand() {
            this.isExpandedReg = !this.isExpandedReg;
            return false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        },

        getTotal(data){
            let total = 0;
            data.forEach(function(dt){
                total = (total*1) + (dt.nilai*1);
            });
            return total;
        },

        
        // kompChange(){
        //     let subtotal = 0;
        //     let diskon = 0;
        //     this.rowData.komponen.forEach(el => {
        //         diskon = (diskon + el.diskon) * 1;
        //         el.subtotal = el.nilai - el.diskon;
        //         subtotal = subtotal + el.subtotal;
        //     });
        //     this.rowData.diskon = diskon;
        //     this.rowData.subtotal = subtotal;
        //     this.dataChange();
        // },

        rowDataChange(){
            let total = 0;
            let diskon = 0;
            this.rowData.subtotal = 0;
            this.rowData.diskon = 0;
            
            this.rowData.komponen.forEach(komp => {
                komp.jumlah = this.rowData.jumlah;
                komp.subtotal = komp.jumlah * (komp.nilai - komp.diskon);
                this.rowData.subtotal = (this.rowData.subtotal*1 ) + komp.subtotal;
                this.rowData.diskon = (this.rowData.diskon*1 ) + komp.diskon;
            });
            //this.dataChange();
        },

        updateValue(data) {
            this.rowData = data;
        }
    }
}
</script>