<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="COA - Code of Account" subTitle="master data kode akun"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <!-- <tabel-coa ref="tableCoa"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </tabel-coa> -->
            <base-table ref="tableCoa"
                :columns = "tbColumns" 
                :config= "configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl"  v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists" style="min-weight:50vh;">
                        <row-coa
                            v-for="dt in dataLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-coa>
                    </tbody>
                </template>
            </base-table>
        </div>
        <div class="uk-width-1-1" style="margin:0;padding:0;">
            <div uk-form-custom="target: true">
                <input type="file" ref="file" @change="importFile" >
            </div>
        </div>
        <form-coa ref="formCoa" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-coa>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowCoa from '@/Pages/MasterData/Coa/components/RowCoa.vue';

import TabelCoa from '@/Pages/MasterData/Coa/components/TabelCoa.vue';
import FormCoa from '@/Pages/MasterData/Coa/components/FormCoa.vue';
import importExcel from '@/Stores/importExcel';
import axios from '@/Stores/httpReq';

export default {
    components : {
        headerPage,
        TabelCoa,
        FormCoa,
        BaseTable,
        RowCoa,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            rowFunctions :[
                { 'title':'Tambah Sub-akun', 'icon':'plus-circle','callback':this.addCoa },
                { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                { 'title':'Hapus', 'icon':'ban','callback':this.deleteData },
            ],
            tbColumns : [                
                { 
                    name : 'kode_coa', 
                    title : 'Kode COA', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Tambah Sub-akun', 'icon':'plus-circle','callback':this.addCoa },
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'nama_coa', 
                    title : 'COA', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'nilai_normal', 
                    title : 'D/K', 
                    colType : 'text', 
                    textAlign:'center',
                    isSearchable : true,
                },
                { 
                    name : 'level_coa', 
                    title : 'Level', 
                    colType : 'text',
                    textAlign:'center',
                    isSearchable  : true,
                },
                { 
                    name : 'coa_header', 
                    title : 'Header', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : true,
                }                
            ], 
            buttons : [
                { title : 'COA Baru', icon:'plus-circle', 'callback' : this.addCoaGroup },
                { title : 'Import Excel', icon:'pull', 'callback' : this.import },
                { title : 'Export Excel', icon:'push', 'callback' : this.exportExcel },
            ],
            searchUrl : '/coa/accounts',
            dataLists : null, 
        }
    },
    methods : {
        ...mapActions('coa',['deleteCoa']),
        ...mapActions('importExcel',['importExcelCOA']),
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data) {
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        refreshTable(){
            this.$refs.tableCoa.retrieveData();
        },
        addCoa(data) {
            if(data) {
                this.CLR_ERRORS();
                this.$refs.formCoa.newCoa(data);
            }
        },
        addCoaGroup() {
            this.CLR_ERRORS();
            this.$refs.formCoa.newGroupCoa();
        },
        
        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formCoa.editCoa(data.coa_id);
        },
        
        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus akun COA ${data.coa_id} | ${data.kode_coa} - ${data.nama_coa}. Lanjutkan ?`)){
                this.deleteCoa(data.coa_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableCoa.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        import() {
            this.$refs.file.click();
        },
        importFile(){
            let formData = new FormData();
            formData.append("file", this.$refs.file.files[0]);

            this.importExcelCOA(formData).then((response)=>{
                if (response.success == true) {
                    alert(response.message)
                    this.refreshTable()
                } else {
                    alert(response.message)
                }
            })
        },

        exportExcel() {
            this.CLR_ERRORS();
            axios.get('/coa/exportExcel', {responseType: 'blob'})
            .then(response => {
                if (response) {
                    const blob = new Blob([response.data], { type: 'application/pdf' })
                    const link = document.createElement('a')
                    link.href = URL.createObjectURL(blob)
                    link.download = 'MASTER_DATA_COA.xlsx'
                    link.click()
                    URL.revokeObjectURL(link.href)
                }
            })
            .catch((error) => {
                this.$store.commit('SET_ERRORS', { invalid: error }, { root: true });
            })
        }, 
    }

}
</script>