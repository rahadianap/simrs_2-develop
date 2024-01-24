<template>    
    <div class="hims-form-subpage">
        <div class="uk-card">
            <div class="uk-width-1-1" style="padding:0.5em;">
                <p style="color:#333; font-size:12px; padding:0;margin:0; font-style: italic; ">*item BHP akan terupdate setelah disimpan. Hanya sebagai referensi, penambahan tergantung ketersediaan item di depo.</p>
            </div>
            <form class="uk-width-1-1" action="" @submit.prevent="saveData" style="padding:0;margin:0 0 1em 0;">
                <div class="uk-width-1-1 uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;margin:0;">         
                    <div class="uk-grid-small uk-width-full" uk-grid style="margin:0;padding:0;">
                        <select ref="selectJenisProduk" name="jenis_produk" @change="jenisProdukChange" style="border:none;border-right:1px solid #ccc;border-bottom-left-radius: 5px;border-top-left-radius: 5px;">
                            <option value="medical">MEDIS</option>
                            <option value="general">NON MEDIS</option>
                            <option value="kitchen">DAPUR</option>
                        </select>
                        <search-select
                            :config = configSelect
                            :searchUrl = urlPicker
                            placeholder = "pilih produk"
                            captionField = "produk_nama"
                            valueField = "produk_id"
                            :itemListData = produkDesc
                            :value = "addedItem.produk_id"
                            v-on:item-select = "produkSelected"
                        ></search-select>
                        <input class="uk-input uk-form-small uk-width-auto" type="number" placeholder="jumlah item" v-model="addedItem.jumlah" style="border:none;border-left:1px solid #ccc;margin:0;padding:0.2em 0 0 0;text-align:center;">
                        <button type="submit" class="uk-width-auto hims-table-btn-submit" style="border:none;border-top-right-radius: 5px;border-bottom-right-radius: 5px;">
                            <span uk-icon="plus-circle"></span> Tambah Item
                        </button>                        
                    </div>
                </div>
            </form>
            <div style="margin:0;padding:0;" class="uk-overflow-auto">
                <table class="uk-table hims-table uk-table-responsive">
                    <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                        <tr>
                            <th style="width:150px;">ID</th>
                            <th>Item</th>
                            <th>Jenis</th>
                            <th>Satuan</th>
                            <th>Jumlah</th>
                            <th style="text-align:center;">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="tindakan.bhpTindakan" v-for="dt in tindakan.bhpTindakan">
                            <td style="font-weight:500;width:150px;">{{dt.produk_id}}</td>
                            <td class="uk-text-uppercase">{{dt.produk_nama}}</td>
                            <td class="uk-text-uppercase" style="text-align:center;">{{dt.jenis_produk}}</td>
                            <td class="uk-text-uppercase" style="text-align:center;">{{dt.satuan}}</td>
                            <td class="uk-text-uppercase" style="text-align:center;">{{dt.jumlah}}</td>
                            <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
                                <a href="#" uk-icon="icon:trash;ratio:0.8" @click.prevent="deleteData(dt)"></a>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="6">
                                <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template> 

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'sub-form-bhp',
    props : { 
        tindakan : { type : Object, required: false, default: null }, 
    },
    components : { SearchSelect, },
    data() {
        return {
            // tindakan : {
            //     tindakan_id : null,
            //     tindakan_nama : null,
            //     unitTindakan : [],
            //     bhpTindakan : [],
            // },
            addedItem : {
                produk_id : null,
                produk_nama : null,
                satuan : null,
                jumlah : null,
            },
            produkDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
                { colName : 'jenis_produk', labelData : '', isTitle : false },
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-expand",
                compStyle : "padding:0 0 0 0.2em;margin:0;border:none;",
                retrieveAll : false,
                qtyPerPage : 20,
            },
            urlPicker : '/products/medical/items'
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },  
    mounted() {
        this.initialize();
    },
    methods :{
        ...mapMutations(['CLR_ERRORS']), 
        ...mapActions('tindakan',['addBhpTindakan','deleteBhpTindakan']),    

        initialize(){
            this.$emit('ready',true);
        },
        
        saveData(data){
            this.CLR_ERRORS();            
            let dt = { 
                tindakan_id : this.tindakan.tindakan_id,
                tindakan_nama : this.tindakan.tindakan_nama,  
                bhpTindakan :[], 
            }
            dt.bhpTindakan.push(this.addedItem);
            this.addBhpTindakan(dt).then((response) => {
                if (response.success == true) {
                    this.addedItem.produk_id = null;
                    this.addedItem.produk_nama = null;                    
                    this.addedItem.jumlah = null;                    
                    alert("Bhp berhasil dipetakan.");
                    this.$emit('updateBhp',this.tindakan);
                }
            }) 
        },

        refreshData(data){
            this.tindakan.tindakan_id = data.tindakan_id;
            this.tindakan.tindakan_nama = data.tindakan_nama;
            this.tindakan.bhpTindakan = data.bhpTindakan;
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus unit ${data.produk_nama} dari mappping tindakan ${this.tindakan.tindakan_nama}. Lanjutkan ?`)){
                this.deleteBhpTindakan(data.tindakan_bhp_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$emit('updated',this.tindakan);
                    }
                    else { alert (response.message) }
                });
            };
        },

        jenisProdukChange(){
            let jenis = this.$refs.selectJenisProduk.value;
            this.urlPicker = `/products/${jenis}/items`;
        },

        produkSelected(data) {
            this.addedItem.produk_id = null;
            this.addedItem.produk_nama = null;
            this.addedItem.jumlah = null;
            this.addedItem.satuan = null;
            this.addedItem.jenis_produk = null;
            if(data) {
                this.addedItem.produk_id = data.produk_id;
                this.addedItem.produk_nama = data.produk_nama;
                this.addedItem.satuan = data.satuan;
                this.addedItem.jumlah = 1;
                this.addedItem.jenis_produk = data.jenis_produk;
            }
        }
    }
}
</script>
