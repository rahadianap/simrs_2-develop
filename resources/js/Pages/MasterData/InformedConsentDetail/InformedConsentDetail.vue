<template>
  <div>
    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
      <p>{{ errors.invalid }}</p>
    </div>
    <div style="margin-top:1em;">
      <base-table ref="tableDetail"
        :columns = "tbColumns" 
        :config="configTable"
        :buttons = "buttons"
        :urlSearch="searchUrl">
      </base-table>
    </div>
    <form-informed-consent-detail ref="formDetail" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-informed-consent-detail>
  </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import FormDtdDiagnosa from '@/Pages/MasterData/ICD/Components/FormDtdDiagnosa.vue';
import FormInformedConsentDetail from './components/FormInformedConsentDetail.vue';

export default {
    name : 'informed-detail',
    components : {
      BaseTable,
      FormDtdDiagnosa,
      FormInformedConsentDetail,
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
            name : 'detail_id', 
            title : 'ID', 
            colType : 'text', 
          },
          { 
            name : 'pertanyaan', 
            title : 'Pertanyaan', 
            colType : 'linkmenus', 
            isSearchable : true,
            functions: [
              { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
              { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
            ],
          },
          { 
            name : 'item_tipe', 
            title : 'Jenis', 
            colType : 'text', 
            isSearchable : true,
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
          { title : 'Kembali', icon:'arrow-left', 'callback' : this.backInformed },
          { title : 'Tambah', icon:'plus-circle', 'callback' : this.addPertanyaan },
        ],
          searchUrl : '/informed/detail'
      }
    },
    methods : {
        ...mapActions('dtd',['listDtd','deleteDtd']),
        ...mapActions('importExcel',['importExcelDtd']),
        ...mapActions('informedConsentDetail',['listInformedDetail','deleteInformedDetail']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat dokter baru */
        addPertanyaan(){
          this.CLR_ERRORS();
          this.$refs.formDetail.newDetail();        
        },

        refreshTable() {
          this.$refs.tableDtd.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formDetail.editDtd(data.no_dtd);
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