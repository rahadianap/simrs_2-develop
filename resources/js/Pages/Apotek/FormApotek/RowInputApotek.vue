<template>
    <tr style="border-bottom:1px solid #ccc;">
        <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
            <select class="uk-select uk-form-small" v-model="itemResep.item_tipe" @change="racikanChange">
                <option value="NON_RACIKAN">NON RACIKAN</option>
                <option value="HEADER_RACIKAN">HEADER RACIKAN</option>
                <option value="ITEM_RACIKAN">ITEM RACIKAN</option>
            </select>
        </td>

        <td colspan="2" v-if="itemResep.item_tipe == 'HEADER_RACIKAN' " style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
            <input v-model="itemResep.group_racikan" class="uk-input uk-form-small" type="text" placeholder="ketik nama racikan">
        </td>

        <template v-else-if="itemResep.item_tipe == 'ITEM_RACIKAN' ">
            <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
                <select class="uk-select uk-form-small" v-model="itemResep.group_racikan">
                    <option  v-for="dt in groupRacikanLists" :value="dt.group_racikan">{{ dt.group_racikan }}</option>
                </select>
            </td>
            <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
                <search-select  ref="searchObat"
                    :config = configSelect
                    :searchUrl = depoProdukUrl
                    placeholder = "pilih item obat"
                    captionField = "produk_nama"
                    valueField = "produk_nama"
                    :itemListData = produkDesc
                    :value = "itemResep.produk_nama"
                    v-on:item-select = "itemSelected"
                ></search-select>
            </td>
        </template>
        
        <td colspan="2" v-else style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
            <search-select  ref="searchObat"
                :config = configSelect
                :searchUrl = depoProdukUrl
                placeholder = "pilih item obat"
                captionField = "produk_nama"
                valueField = "produk_nama"
                :itemListData = produkDesc
                :value = "itemResep.item_nama"
                v-on:item-select = "itemSelected"
            ></search-select>
        </td>
        
        <td style="width:60px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
            <input v-model="itemResep.jumlah" class="uk-input uk-form-small" type="number" style="text-align: center;" @change="rowDataChange">
        </td>

        <td style="padding:1em; font-size: 12px; text-align:center; color:black;">
            <!-- <search-select  ref="searchSatuan"
                :config = configSelect
                searchUrl = '/uoms'
                placeholder = "pilih satuan"
                captionField = "satuan_id"
                valueField = "satuan_id"
                :itemListData = satuanDesc
                :value = "itemResep.satuan"
                v-on:item-select = "satuanSelected"
            ></search-select> -->
            {{ itemResep.satuan }}
        </td>
        <td style="padding:0.4em; font-size: 12px; color:black;">
            <search-select  ref="searchSigna"
                :config = configSelect
                :disabled = "itemResep.item_tipe == 'ITEM_RACIKAN'"
                searchUrl = '/signas'
                placeholder = "pilih signa"
                captionField = "signa"
                valueField = "signa"
                :itemListData = signaDesc
                :value = "itemResep.signa"
                v-on:item-select = "signaSelected"
            ></search-select>
        </td>

        <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemResep.nilai)}}</td>
        <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemResep.subtotal)}}</td>
        
        <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
            <button type="button" @click.prevent="addItemResep(itemResep)">Tambah</button>
        </td>
    </tr>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    props : {
        resepItems : { type:Object, required:true },
        // groupRacikanLists : { type:Object, required:false },
        fnAddCallback : { type:Function, required:false }
    },
    name : 'row-input-apotek',
    
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('resep',['prescriptionItems','prescription']),
    },

    components : { 
        SearchSelect, 
    },
    data() {
        return {
            // '/products/medical/items'
            depoProdukUrl : null,
            isExpandedReg : false,
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

            itemResep : {
                detail_id : null,
                is_racikan : false,
                item_nama : null,
                item_id : null,
                signa : null,
                signa_deskripsi : null,
                jumlah : 1,
                satuan : null,
                nilai : 0,
                diskon : 0,
                subtotal : 0,
                is_aktif : true,
                diskon_persen : 0,
                harga_diskon : 0,
                group_racikan : null,
                item_tipe : 'NON_RACIKAN',
            },
            groupRacikanLists : [],
        }
    },
    
    watch : {
        // 'transaksi.depo_id'(newVal, oldVal) {   
        //     alert(newVal);
        //     this.depoProdukUrl = `/pharmacy/{newVal}/items`;
        // }
    },

    methods : {
        itemSelected(data) {
            if(data) {
                this.itemResep.item_nama = data.produk_nama;
                this.itemResep.item_id = data.produk_id;
                this.itemResep.signa = data.signa;
                this.itemResep.signa_deskripsi = data.signa_deskripsi;
                this.itemResep.satuan = data.satuan;
                this.itemResep.nilai = data.hna;
                this.itemResep.subtotal = data.hna;

                if(this.itemResep.item_tipe == 'ITEM_RACIKAN') {
                    this.itemResep.signa = null;
                    this.itemResep.signa_deskripsi = null;
                }
            }
        },  

        addItemResep(data){
            if(data) {
                if(data.item_tipe == 'HEADER_RACIKAN') {
                    if(data.signa_deskripsi == "" ||  data.signa_deskripsi == null) {
                        alert('cara pakai harus di isi');
                        return false;
                    }
                    if(data.group_racikan == null || data.group_racikan == '' ) {
                        alert('nama group harus di isi');
                        return false;
                    }
                    data.item_nama = data.group_racikan;
                    data.item_id = data.group_racikan;
                }

                else if(data.item_tipe == 'ITEM_RACIKAN') {
                    if(data.item_nama == null || data.item_nama == '' || data.item_id == null || data.item_id == '') {
                        alert('item resep tidak boleh kosong');
                        return false;
                    }
                    let dt = this.resepItems.find(itm => itm.group_racikan == data.group_racikan);
                    if(dt) {
                        data.signa = dt.signa;
                        data.signa_deskripsi = dt.signa_deskripsi;
                    }
                    else {
                        alert('header racikan belum dipilih');
                        return false;
                    }
                }

                else {
                    if(data.item_nama == null || data.item_nama == '' || data.item_id == null || data.item_id == '') {
                        alert('item resep tidak boleh kosong');
                        return false;
                    }
                
                    if(data.signa_deskripsi == "" ||  data.signa_deskripsi == null) {
                        alert('cara pakai harus di isi');
                        return false;
                    }
                }

                this.resepItems.push(JSON.parse(JSON.stringify(data)));                
                this.groupRacikanLists = this.resepItems.filter(itm => itm.item_tipe == 'HEADER_RACIKAN');
                this.clearInputData();
            
                if(this.fnAddCallback) { this.fnAddCallback(); }
            }
        },

        clearInputData(){

            this.itemResep.detail_id = null;
            this.itemResep.item_id = null;
            this.itemResep.item_nama = null;
            this.itemResep.signa = "";
            this.itemResep.signa_deskripsi = "";
            this.itemResep.jumlah = 1;
            this.itemResep.satuan = null;
            this.itemResep.diskon_persen = 0;
            this.itemResep.nilai = 0;
            this.itemResep.diskon = 0;
            this.itemResep.harga_diskon = 0;
            
            this.itemResep.subtotal = 0;
            this.itemResep.is_aktif = true;
            this.itemResep.group_racikan = null;
            this.itemResep.is_racikan = false;
            this.itemResep.item_tipe = 'NON_RACIKAN';
        },

        signaSelected(data) {
            if(data) {
                this.itemResep.signa = data.signa;
                this.itemResep.signa_deskripsi = data.deskripsi;
            }
            else {
                this.itemResep.signa = null;
                this.itemResep.signa_deskripsi = null;
            }
        },

        satuanSelected(data) {
            if(data) { this.itemResep.satuan = data.satuan_id; }
        },

        racikanChange(){
            // if(this.itemResep.item_tipe == 'ITEM_RACIKAN' || this.itemResep.item_tipe == 'HEADER_RACIKAN' ) {
            //     this.itemResep.is_racikan = true; 
            // }
            // else { this.itemResep.is_racikan = false; }
            if(this.itemResep.item_tipe == 'ITEM_RACIKAN' || this.itemResep.item_tipe == 'HEADER_RACIKAN' ) {
                let dt = this.itemResep.item_tipe;
                this.clearInputData();
                this.itemResep.item_tipe = dt;
                this.itemResep.is_racikan = true; 
            }
            else { 
                let dt = this.itemResep.item_tipe;
                this.clearInputData();
                this.itemResep.item_tipe = dt;
                this.itemResep.is_racikan = false; 
            }
        },

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
            data.forEach(function(dt){ total = (total*1) + (dt.nilai*1); });
            return total;
        },

        rowDataChange(){
            this.itemResep.subtotal = (this.itemResep.jumlah * (this.itemResep.nilai - this.itemResep.diskon)) * 1;
        },

        updateValue(data) {
            this.rowData = data;
        },

        updateDepo(data){
            if(data) {
                this.depoProdukUrl = `/pharmacy/${data}/items`;
            }
        }
    }
}
</script>