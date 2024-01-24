<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="AKUN PRODUK" subTitle="master pemetaan akun coa untuk produk"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableAkunProduk"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists" style="min-weight:50vh;">
                        <row-akun-produk
                            v-for="dt in dataLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-akun-produk>
                    </tbody>
                </template>
            </base-table>
        </div>
        <form-akun-produk ref="formAkunProduk" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-akun-produk>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import formAkunProduk from '@/Pages/MasterData/AkunProduk/Components/FormAkunProduk.vue';
import RowAkunProduk from '@/Pages/MasterData/AkunProduk/Components/RowAkunProduk.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        formAkunProduk,
        RowAkunProduk,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            rowFunctions : [
                { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData }, 
                { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },  
            ],
            tbColumns : [
                
                { 
                    name : 'produk_akun', 
                    title : 'Nama', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData }, 
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },                
                
                { 
                    name : 'coa_inventory', 
                    title : 'Akun Persediaan', 
                    colType : 'text', 
                    isSearchable : false,
                },
                { 
                    name : 'coa_revenue', 
                    title : 'Akun Pendapatan', 
                    colType : 'text', 
                    isSearchable : false,
                },
                { 
                    name : 'coa_cogs', 
                    title : 'Akun Biaya', 
                    colType : 'text', 
                    isSearchable : false,
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text', 
                    isSearchable : false,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/products/accounts',
            dataLists : null,
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('produkAkun',['deleteProdukAkun']),
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data) {
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },
        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$refs.formAkunProduk.newAkunProduk();
        },

        refreshTable() {
            this.$refs.tableAkunProduk.retrieveData();
        },
        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formAkunProduk.editAkunProduk(data.produk_akun_id);
        },
        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus akun produk ${data.produk_akun}. Lanjutkan ?`)){
                this.deleteProdukAkun(data.produk_akun_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableAkunProduk.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>