<template>
    <div class="uk-container uk-container-large" style="padding:0;"> 
        <header-page title="MASTER ITEM MEDIS" subTitle="master data item persediaan medis"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableItemMedis"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                urlSearch='/products/medical/items'>
            </base-table>
        </div>        
    </div>
    <cetakan-kartu-stock ref="cetakanKartuStock"></cetakan-kartu-stock>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import HeaderPage from '@/Components/HeaderPage.vue';
import CetakanKartuStock from '@/Pages/MasterData/Produk/ItemMedis/cetakan/cetakanKartuStock.vue';

export default {
    name : 'list-item-medis',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
    },

    components : {
        BaseTable,
        HeaderPage,
        CetakanKartuStock
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refProduk',['isRefProdukExist'])
    },
    data() {
        return {
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            tbColumns : [                
                { 
                    name : 'produk_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Cetak Kartu Stock', 'icon':'file-pdf','callback':this.cetakKartuStock },
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.editFunction },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'produk_nama', 
                    title : 'Item Medis', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'jenis_produk', 
                    title : 'JENIS', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'golongan.value', 
                    title : 'golongan', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'klasifikasi.value', 
                    title : 'metadata', 
                    colType : 'json', 
                    isSearchable : true,
                },
                { 
                    name : 'vendor', 
                    title : 'vendor', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }                
            ], 
            buttons : [
                { title : 'Item Baru', icon:'plus-circle', 'callback' : this.addFunction },
            ],
        }
    },
    mounted(){
    },
    methods : {
        ...mapActions('produk',['deleteProduk']),
        ...mapActions('cetakan',['dataKartuStock']),
        ...mapMutations(['CLR_ERRORS']),

        addData(){        
            this.CLR_ERRORS();
        },

        refreshTable() {
            this.$refs.tableItemMedis.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus item ${data.produk_nama}. Lanjutkan ?`)){
                this.deleteProduk(data.produk_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableItemMedis.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        cetakKartuStock(data){
            this.dataKartuStock(data.produk_id).then((response) => {
                if (response.success == true) {
                    this.$refs.cetakanKartuStock.generateReport(response.data);
                }
                else { alert (response.message) }
            });
        }
    }
    
}
</script>