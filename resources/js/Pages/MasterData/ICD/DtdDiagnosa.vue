<template>
    <div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableDtd"
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
        <form-dtd-diagnosa ref="formDtd" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-dtd-diagnosa>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import FormDtdDiagnosa from '@/Pages/MasterData/ICD/Components/FormDtdDiagnosa.vue';

export default {
    name : 'dtd-diagnosa',
    components : {
        BaseTable,
        FormDtdDiagnosa,
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
                    name : 'no_dtd', 
                    title : 'No. DTD', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'nama_dtd', 
                    title : 'Nama DTD', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'label_dtd', 
                    title : 'Label', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ], 
            buttons : [
                { title : 'DTD Baru', icon:'plus-circle', 'callback' : this.addDtd },
                { title : 'Import Excel', icon:'pull', 'callback' : this.import },
            ],
            searchUrl : '/dtd'
        }
    },
    methods : {
        ...mapActions('dtd',['listDtd','deleteDtd']),
        ...mapActions('importExcel',['importExcelDtd']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat dokter baru */
        addDtd(){        
            this.CLR_ERRORS();
            this.$refs.formDtd.newDtd();        
        },

        refreshTable() {
            this.$refs.tableDtd.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formDtd.editDtd(data.no_dtd);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus DTD ${data.nama_dtd}. Lanjutkan ?`)){
                this.deleteDtd(data.no_dtd).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableDtd.retrieveData();
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

            this.importExcelDtd(formData).then((response)=>{
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