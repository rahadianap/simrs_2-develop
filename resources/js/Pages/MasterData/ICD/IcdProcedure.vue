<template>
    <div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableIcdProcedure"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <div class="uk-width-1-1" style="margin:0;padding:0;">
            <div uk-form-custom="target: true">
                <input type="file" ref="file" @change="importFile" >
            </div>
        </div>
        <form-icd-prosedur ref="formIcdProcedure" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-icd-prosedur>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import FormIcdProsedur from '@/Pages/MasterData/ICD/Components/FormIcdProsedur.vue';

export default {
    name : 'icd-procedure',
    components : {
        BaseTable,
        FormIcdProsedur,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            configTable : {                
                isExpandable : false,
                filterType : 'SIMPLE', 
            },
            tbColumns : [                
                { 
                    name : 'kode_icd', 
                    title : 'Kode', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'nama_icd', 
                    title : 'Nama ICD', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'kata_kunci', 
                    title : 'Kata Pencarian', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ], 
            buttons : [
                { title : 'ICD9 Baru', icon:'plus-circle', 'callback' : this.addIcd9 },
                { title : 'Import Excel', icon:'pull', 'callback' : this.import },
            ],
            searchUrl : '/icd9'
        }
    },
    methods : {
        ...mapActions('icd9',['listIcdProcedure','deleteIcdProcedure']),
        ...mapActions('importExcel',['importExcelICD9']),
        ...mapMutations(['CLR_ERRORS']),


        /**tampikan modal untuk membuat dokter baru */
        addIcd9(){        
            this.CLR_ERRORS();
            this.$refs.formIcdProcedure.newIcdProcedure();        
        },

        refreshTable() {
            this.$refs.tableIcdProcedure.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formIcdProcedure.editIcdProcedure(data.kode_icd);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus Kode ICD ${data.nama_icd}. Lanjutkan ?`)){
                this.deleteIcdProcedure(data.kode_icd).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableIcdProcedure.retrieveData();
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

            this.importExcelICD9(formData).then((response)=>{
                if (response.success == true) {
                    alert(response.message)
                    this.refreshTable()
                } else {
                    alert(response.message)
                }
            })
        },
    }
    
}
</script>