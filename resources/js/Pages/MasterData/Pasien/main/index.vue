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
    <cetakan-riwayat-pasien ref="cetakanRiwayatPasien"></cetakan-riwayat-pasien>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import importExcel from '@/Stores/importExcel';
import axios from '@/Stores/httpReq';
import RowPasien from '@/Pages/MasterData/Pasien/components/RowPasien.vue';
import cetakanRiwayatPasien from '@/Pages/MasterData/Pasien/cetakan/cetakanRiwayatPasien.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        RowPasien,
        cetakanRiwayatPasien
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
                { 'title':'Cetak Riwayat', 'icon':'file-pdf','callback':this.cetakRiwayat },
                { 'title':'Create Pasien SATUSEHAT', 'icon':'file-pdf','callback':this.createPasienSatuSehat },
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
                    name : 'nama_lengkap', 
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
        ...mapActions('cetakanMaster', ['dataRiwayat']),
        ...mapActions('satusehatPasien',['createPatient']),
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
        
        cetakRiwayat(data){
            this.dataRiwayat(data.pasien_id).then((response) => {
                var newNode = document.createElement('p');
                newNode.appendChild(document.createTextNode('html string'));
                this.printDiv(response,"a4");
            });
        },

        getDateFormated(curDate){
                const today = new Date(curDate);
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();

                if (dd < 10) dd = '0' + dd;
                if (mm < 10) mm = '0' + mm;

                const formattedToday = dd + '/' + mm + '/' + yyyy;
                return formattedToday;
        },

        printDiv(pdf_body, paperSize) {
            const customPaperSize = paperSize;
            let frame1 = document.createElement('iframe');
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            let frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();

            // Set custom paper size using CSS @page rule
            const customStyles = `
                <style>
                    @page {
                        size: ${customPaperSize};
                    }
                </style>
            `;

            frameDoc.document.write(customStyles);

            // Use the entire content as one page
            const fullHtml = pdf_body;

            frameDoc.document.write(fullHtml);
            frameDoc.document.close();

            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 1000);

            return false;
        },

        createPasienSatuSehat(data){
            this.createPatient(data.pasien_id).then((response) => {
                if (response.success == true) {
                    alert("abc");
                }
                else { alert (response.message) }
            });
        }
    }
}
</script>