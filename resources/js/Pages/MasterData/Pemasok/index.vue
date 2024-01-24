<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER PEMASOK DAN PABRIKAN" subTitle="master data penyedia dan pabrikan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tablePemasok"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists" style="min-weight:50vh;">
                        <row-pemasok
                            v-for="dt in dataLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-pemasok>
                    </tbody>
                </template>
            </base-table>
        </div>
        <form-pemasok ref="formPemasok" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-pemasok>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormPemasok from '@/Pages/MasterData/Pemasok/components/FormPemasok.vue'
import PemasokFormFilter from '@/Pages/MasterData/Pemasok/components/PemasokFormFilter.vue';
import RowPemasok from '@/Pages/MasterData/Pemasok/components/RowPemasok.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormPemasok,
        PemasokFormFilter,
        RowPemasok,
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
            rowFunctions :[
                { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData }, 
            ],
            tbColumns : [                
                { 
                    name : 'vendor_id', 
                    title : 'PEMASOK', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                // { 
                //     name : 'vendor_nama', 
                //     title : 'Nama', 
                //     colType : 'text', 
                //     isSearchable : true,
                // },
                { 
                    name : 'narahubung', 
                    title : 'Narahubung', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'no_kontrak', 
                    title : 'Kontrak', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'alamat', 
                    title : 'Alamat', 
                    colType : 'text',
                    isSearchable  : true,
                },
                { 
                    name : 'status', 
                    title : 'Status', 
                    colType : 'text',
                    isSearchable  : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    options : [
                        { value:0, text:'Aktif' },
                        { value:1, text:'Nonaktif' },
                    ]
                },
                
            ], 
            buttons : [
                { title : 'Pemasok Baru', icon:'plus-circle', 'callback' : this.addPemasok },
            ],
            searchUrl : '/vendors',
            dataLists : null,
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('vendor',['listVendor','deleteVendor']),
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data) {
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        /**tampikan modal untuk membuat vendor baru */
        addPemasok(){        
            this.CLR_ERRORS();
            this.$refs.formPemasok.newPemasok();        
        },

        refreshTable() {
            this.$refs.tablePemasok.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formPemasok.editPemasok(data.vendor_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan pemasok (pabrikan) ${data.vendor_nama}. Lanjutkan ?`)){
                this.deleteVendor(data.vendor_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePemasok.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>