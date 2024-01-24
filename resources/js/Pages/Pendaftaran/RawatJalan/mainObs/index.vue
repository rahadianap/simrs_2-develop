<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <header-page title="PENDAFTARAN PASIEN" subTitle=""></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">            
            <base-table ref="tablePasien"
                :columns = "tbColumns" 
                :config = "configTable" 
                :buttons = "buttons"
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
        <div class="uk-width-1-1" style="margin:0;padding:0;">
            <div uk-form-custom="target: true">
                <input type="file" ref="file" @change="importFile" >
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import importExcel from '@/Stores/importExcel';
import axios from '@/Stores/httpReq';
import RowPasien from '@/Pages/MasterData/Pasien/components/RowPasien.vue';

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
                { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
            ],
            tbColumns : [             
                { 
                    name : 'no_rm', 
                    title : 'No.RM', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                    ],
                },   
                // { 
                //     name : 'no_rm', 
                //     title : 'RM', 
                //     colType : 'linkmenus', 
                //     functions: [
                //         { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                //         { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                //     ],
                // },   
                { 
                    name : 'nama_pasien', 
                    title : 'Nama Pasien', 
                    colType : 'text',
                },   
                { 
                    name : 'tempat_lahir', 
                    title : 'Kelahiran', 
                    colType : 'text',
                },   
                // { 
                //     name : 'jns_kelamin', 
                //     title : 'JK', 
                //     colType : 'text',
                // },   
                { 
                    name : 'nik', 
                    title : 'NIK/KK', 
                    colType : 'text', 
                },
                { 
                    name : 'jns_penjamin', 
                    title : 'Jenis Penjamin', 
                    colType : 'text',
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
                { title : 'Import Excel', icon:'pull', 'callback' : this.import },
                { title : 'Export Excel', icon:'push', 'callback' : this.export },
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
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
        },

        updateListData(data){
            this.pasienLists = null;
            if(data){ this.pasienLists = data.data; }
        },

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$router.push(`/master/pasien/baru`);
        },

        refreshTable() {
            this.$refs.tablePasien.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$router.push(`/master/pasien/${data.pasien_id}`);
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

        import() {
            this.$refs.file.click();
        },
        
        importFile(){
            let formData = new FormData();
            formData.append("file", this.$refs.file.files[0]);

            this.importExcel(formData).then((response)=>{
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