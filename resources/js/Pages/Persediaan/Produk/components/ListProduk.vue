<template>
    <div class="uk-container uk-container-large" style="padding:0;"> 
        <header-page title="PRODUK PERSEDIAAN" subTitle="master data item persediaan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableProduk"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                urlSearch='/products/all/items'>
            </base-table>
        </div>        
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import HeaderPage from '@/Components/HeaderPage.vue';

export default {
    name : 'list-produk',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
    },

    components : {
        BaseTable,
        HeaderPage
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
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.editFunction },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'jenis_produk', 
                    title : 'JENIS', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'produk_nama', 
                    title : 'Item Medis', 
                    colType : 'text', 
                    isSearchable : true,
                },
                // { 
                //     name : 'golongan.value', 
                //     title : 'golongan', 
                //     colType : 'text', 
                //     isSearchable : true,
                // },
                { 
                    name : 'klasifikasi.value', 
                    title : 'klasifikasi', 
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
        ...mapMutations(['CLR_ERRORS']),

        addData(){        
            this.CLR_ERRORS();
        },

        refreshTable() {
            this.$refs.tableProduk.retrieveData();
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
                        this.$refs.tableProduk.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
    
}
</script>