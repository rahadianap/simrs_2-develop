<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <header-page title="MASTER PASIEN" subTitle="master data pasien dan detail info"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">            
            <base-table ref="tablePasien"
                :columns = "tbColumns" 
                :config = "configTable"
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="pasienLists" style="min-weight:50vh;">
                        <row-pasien
                            v-for="dt in pasienLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-pasien>
                    </tbody>
                </template>
            </base-table>
        </div>
        <!-- <div class="uk-width-1-1" style="margin:0;padding:0;">
            <div uk-form-custom="target: true">
                <input type="file" ref="file" @change="importFile" >
            </div>
        </div> -->
    </div>
    <!-- <cetakan-riwayat-pasien ref="cetakanRiwayatPasien"></cetakan-riwayat-pasien> -->
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowPasien from '@/Pages/MedicalRecord/MedicalRecordPasien/RowPasien.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        RowPasien,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',['isRefExist']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : true,
                filterType : 'REGULAR', 
            },
            rowFunctions : [
                { 'title':'Data Rekam Medis', 'icon':'file-edit','callback':this.medrecData },
            ],
            tbColumns : [             
                { name : 'no_rm', title : 'No.RM', colType : 'linkmenus', },  
                { name : 'nama_lengkap', title : 'Nama Pasien', colType : 'text', },
                { name : 'nik', title : 'NIK/KK', colType : 'text', },
                { name : 'jns_penjamin', title : 'Jenis Penjamin', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }
            ], 
            searchUrl : '/patients',
            pasienLists : null,
         }
    },
    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('pasien',['deletePasien']),
        ...mapActions('importExcel',['importExcel']),
        ...mapActions('cetakan', ['dataRiwayat']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
        },

        updateListData(data){
            this.pasienLists = null;
            if(data){ this.pasienLists = data.data; }
        },

        medrecData(data) {
            if(data) {
                this.$router.push({ name: 'listMedrec', params: { pasienId: data.pasien_id } });
            }
        },

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$router.push(`/master/pasien/form/baru`);
        },

        refreshTable() {
            this.$refs.tablePasien.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$router.push(`/master/pasien/form/${data.pasien_id}`);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan data pasien ${data.no_rm} - ${data.nama_lengkap}. Lanjutkan ?`)){
                this.deletePasien(data.pasien_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePasien.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        // import() {
        //     this.$refs.file.click();
        // },
        
        // importFile(){
        //     let formData = new FormData();
        //     formData.append("file", this.$refs.file.files[0]);

        //     this.importExcel(formData).then((response)=>{
        //         if (response.success == true) {
        //             alert(response.message)
        //             this.refreshTable()
        //         } else {
        //             alert(response.message)
        //         }
        //     })
        // },

        // exportExcel() {
        //     this.CLR_ERRORS();
        //     axios.get('/coa/exportExcel', {responseType: 'blob'})
        //     .then(response => {
        //         if (response) {
        //             const blob = new Blob([response.data], { type: 'application/pdf' })
        //             const link = document.createElement('a')
        //             link.href = URL.createObjectURL(blob)
        //             link.download = 'MASTER_DATA_COA.xlsx'
        //             link.click()
        //             URL.revokeObjectURL(link.href)
        //         }
        //     })
        //     .catch((error) => {
        //         this.$store.commit('SET_ERRORS', { invalid: error }, { root: true });
        //     })
        // },
        
        // cetakRiwayat(data){
        //     this.dataRiwayat(data.pasien_id).then((response) => {
        //         if (response.success == true) {
        //             this.$refs.cetakanRiwayatPasien.generateReport(response.data);
        //         }
        //         else { alert (response.message) }
        //     });
        // }
    }
}
</script>