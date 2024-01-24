<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <header-page title="ASURANSI DAN PENJAMIN" subTitle="master data asuransi dan penjamin"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableAsuransi"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl"  v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="asuransiLists" style="min-weight:50vh;">
                        <row-asuransi
                            v-for="dt in asuransiLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-asuransi>
                    </tbody>
                </template>
            </base-table>
        </div>
        <form-asuransi ref="formAsuransi" v-on:formAsuransiClosed="refreshTable"></form-asuransi>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormAsuransi from '@/Pages/MasterData/Asuransi/components/FormAsuransi.vue'
import AsuransiFormFilter from '@/Pages/MasterData/Asuransi/components/AsuransiFormFilter.vue';
import RowAsuransi from '@/Pages/MasterData/Asuransi/components/RowAsuransi.vue';


export default {
    components : {
        headerPage,
        BaseTable,
        FormAsuransi,
        AsuransiFormFilter,
        RowAsuransi,
    },
    computed : {
        ...mapGetters(['errors']), 
        ...mapGetters('referensi',['jenisPenjaminRefs','isRefExist']),
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
                    name : 'penjamin_id', 
                    title : 'Penjamin',
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'narahubung', 
                    title : 'Kontak', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'buku_harga', 
                    title : 'Buku Harga', 
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
                { title : 'Asuransi Baru', icon:'plus-circle', 'callback' : this.addAsuransi },
            ],
            searchUrl : '/guarantors',
            asuransiLists : null,
         }
    },
    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('asuransi',['listAsuransi','deleteAsuransi']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),
        
        initialize(){
            if(!this.isRefExist) { 
                this.listReferensi(); 
            };
        },

        updateListData(data){
            this.asuransiLists = null;
            if(data) { this.asuransiLists = data.data; }
        },

        /**tampikan modal untuk membuat role baru */
        addAsuransi(){        
            this.CLR_ERRORS();
            this.$refs.formAsuransi.newAsuransi();        
        },

        refreshTable() {
            this.$refs.tableAsuransi.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formAsuransi.editAsuransi(data.penjamin_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan penjamin (asuransi) ${data.penjamin_nama}. Lanjutkan ?`)){
                this.deleteAsuransi(data.penjamin_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableAsuransi.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>