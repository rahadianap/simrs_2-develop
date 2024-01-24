<template>
    <div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableIcdDiagnosa"
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
        <form-icd-diagnosa ref="formIcdDiagnosa" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-icd-diagnosa>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import FormIcdDiagnosa from '@/Pages/MasterData/ICD/Components/FormIcdDiagnosa.vue';

export default {
    name : 'icd-diagnosa',
    components : {
        BaseTable,
        FormIcdDiagnosa,
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
                    name : 'no_dtd', 
                    title : 'DTD', 
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
                { title : 'ICD10 Baru', icon:'plus-circle', 'callback' : this.addIcd10 },
                { title : 'Import Excel', icon:'pull', 'callback' : this.import },
            ],
            searchUrl : '/icd10'
        }
    },
    methods : {
        ...mapActions('icd10',['listIcdDiagnosa','deleteIcdDiagnosa']),
        ...mapActions('importExcel',['importExcelICD10']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat dokter baru */
        addIcd10(){        
            this.CLR_ERRORS();
            this.$refs.formIcdDiagnosa.newIcdDiagnosa();        
        },

        refreshTable() {
            this.$refs.tableIcdDiagnosa.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formIcdDiagnosa.editIcdDiagnosa(data.kode_icd);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus Kode ICD ${data.nama_icd}. Lanjutkan ?`)){
                this.deleteIcdDiagnosa(data.kode_icd).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableIcdDiagnosa.retrieveData();
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

            this.importExcelICD10(formData).then((response)=>{
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