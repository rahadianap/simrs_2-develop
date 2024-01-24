<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <div style="padding:0;margin:0 0 0 0;background-color:#fff;">
            <ul id="switcherPasienPoli" class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0;margin:0;">
                <li style="padding:0;"  :class=" tabName.name == 'listPendaftaran' ? 'uk-active' : ''">
                    <div>
                        <router-link :to = "{ name:'listPendaftaran' }"><h5>Antrian Poli</h5></router-link>
                    </div>
                </li>
                <li style="padding:0;"  :class=" tabName.name == 'listBookingPoli' ? 'uk-active' : ''">
                    <div>
                        <router-link :to = "{ name:'listBookingPoli' }"><h5>Booking Poli</h5></router-link>
                    </div>
                </li>
                <li style="padding:0;"  :class=" tabName.name == 'listMasterPasien' ? 'uk-active' : ''">
                    <div>
                        <router-link :to = "{ name:'listMasterPasien' }"><h5>Master Pasien</h5></router-link>
                    </div>
                </li>                
            </ul>
            <div style="padding:1em;min-height:40vh;">
                <router-view></router-view>
            </div>
        </div>
        <!-- <div style="margin-top:1em;">
            <base-table ref="tablePendaftaran"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div> -->
        <!-- <form-pendaftaran ref="formPendaftaran" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-pendaftaran>
        <cetakan-pendaftaran ref="cetakanPendaftaran" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></cetakan-pendaftaran>
        <cetakan-tracer ref="cetakanTracer" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></cetakan-tracer> -->
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
// import headerPage from '@/Components/HeaderPage.vue';
// import BaseTable from '@/Components/BaseTable';
//import FormPendaftaran from '@/Pages/Pendaftaran/RawatJalan/components/FormPendaftaran.vue';
// import CetakanPendaftaran from '@/Pages/Pendaftaran/RawatJalan/cetakan/cetakanPendaftaran.vue';
// import CetakanTracer from '@/Pages/Pendaftaran/RawatJalan/cetakan/cetakanTracer.vue';

export default {
    components : {
        // headerPage,
        // BaseTable,
        // FormPendaftaran,
        // CetakanPendaftaran,
        // CetakanTracer
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('unitPelayanan',['unitLists']),
        ...mapGetters('dokter',['doctorLists']),
        ...mapGetters('referensi',['isRefExist','jenisPelayananRefs','jenisRegistrasiRefs','asalPasienRefs',
            'caraRegistrasiRefs','salutRefs','jenisPenjaminRefs','hubKeluargaRefs',]),    
    },

    data() {
        return {
            isExpanded : false,
            tabName : null,
            configTable : {
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            tbColumns : [
                { 
                    name : 'reg_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Cetak Bukti Pendaftaran', 'icon':'file-pdf','callback':this.cetakPendaftaran },
                        { 'title':'Cetak Tracer', 'icon':'file-pdf','callback':this.cetakTracer },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'nama_pasien', 
                    title : 'NAMA PASIEN', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Cetak Bukti Pendaftaran', 'icon':'file-pdf','callback':this.cetakPendaftaran },
                        { 'title':'Cetak Tracer', 'icon':'file-pdf','callback':this.cetakTracer },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'jns_kelamin', 
                    title : 'JENIS KELAMIN', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'status_reg', 
                    title : 'STATUS REG.', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'jns_registrasi', 
                    title : 'JENIS REG.', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'cara_registrasi', 
                    title : 'CARA REG.', 
                    colType : 'text', 
                    isSearchable : true,
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
            ],
            searchUrl : '/registrations'
        }
    },
    watch:{
        'tabName': function(newVal, oldVal) {
            this.tabName = newVal;
        },
    },
    created() {
        this.tabName = this.$router.currentRoute;
    },
    mounted() {
        this.init();
    },

    methods : {
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('dokter',['listDokter']),
        ...mapActions('pendaftaran',['createRegistrasi','dataRegistrasi','updateRegistrasi','deleteRegistrasi']),
        ...mapActions('cetakan', ['dataCetakanPendaftaran', 'dataCetakanTracer']),
        ...mapMutations(['CLR_ERRORS']),

        init(){
            if(!this.isRefExist) { this.listReferensi({per_page:'ALL'}); }  
            if(this.unitLists.length < 1 ){
                let param = {'per_page':'ALL','is_aktif':true };
                this.listUnitPelayanan(param).then((response) => {
                    if (response.success == true) {}
                });
            }
            //ambil daftar dokter
            if(this.doctorLists.length < 1) {
                this.listDokter().then((response) => {
                    if (response.success == true) {}
                });
            }
        },
        // filterDataBooking() {
        //     alert('filter data');
        //     this.searchUrl = `/registrations?status=DAFTAR`;
        //     this.refreshTable();
        // },
        // filterDataAntri() {
        //     alert('filter data');
        //     this.searchUrl = `/registrations?status=ANTRI`;
        //     this.refreshTable();
        // },
        // filterDataPeriksa() {
        //     alert('filter data');
        //     this.searchUrl = `/registrations?status=PERIKSA`;
        //     this.refreshTable();
        // },
        // filterDataSelesai() {
        //     alert('filter data');
        //     this.searchUrl = `/registrations?status=SELESAI`;
        //     this.refreshTable();
        // },

        updateListData(data){
            this.regPoliLists = null;
            if(data){ this.regPoliLists = data.data; }
        },

        updateListDataBooking(data){
            this.regBookingLists = null;
            if(data){ this.regBookingLists = data.data; }
        },

        /**tampikan modal untuk membuat vendor baru */
        addData(){
            this.CLR_ERRORS();
            this.$refs.formPendaftaran.newPendaftaran();
        },

        refreshPasien(){

        },
        refreshTable() {
            this.$refs.tablePendaftaran.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            //this.$refs.formPendaftaran.editRegistrasi(data);
            if(data) {
                this.$refs.modalEditRegPoli.editData(data);
            }
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus registrasi ${data.nama_pasien}. Lanjutkan ?`)){
                this.deleteRegistrasi(data.reg_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePendaftaran.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        cetakPendaftaran(data){
            this.dataCetakanPendaftaran(data.reg_id).then((response) => {
                // if (response.success == true) {
                    // this.$refs.cetakanPendaftaran.generateReport(response.data);
                    var newNode = document.createElement('p');
                    newNode.appendChild(document.createTextNode('html string'));
                    this.printDiv(response,"3.4in 4.5in");
                // }
                // else { alert (response.message) }
            });
        },

        cetakTracer(data){
            this.dataCetakanTracer(data.reg_id).then((response) => {
                // if (response.success == true) {
                    // this.$refs.cetakanTracer.generateReport(response.data);
                    var newNode = document.createElement('p');
                    newNode.appendChild(document.createTextNode('html string'));
                    this.printDiv(response,"3.4in 4.5in");
                // }
                // else { alert (response.message) }
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
                        margin: 0;
                    }
                    .page-number {
                        font-size:9px;
                        position: absolute;
                        bottom: 10px;
                        right: 10px;
                    }
                    .left-text {
                        font-size:9px;
                        position: absolute;
                        bottom: 10px;
                        left: 10px;
                    }
                </style>
            `;

            // Append page number to each page
            const pages = pdf_body.split('<div class="page-break"></div>'); // Assuming you have a class for page breaks
            let fullHtml = customStyles;
            var Currentdate = this.getDateFormated(new Date());
            for (let i = 0; i < pages.length; i++) {
                const pageNumber = i + 1;
                const pageHtml = pages[i];
                const pageWithNumber = `
                    <div class="page-container">
                        ${pageHtml}
                        <div class="left-text">${Currentdate}</div>
                        <div class="page-number">Page ${pageNumber} of ${pages.length}</div>
                    </div>
                `;
                fullHtml += pageWithNumber;
                if (i < pages.length - 1) {
                    fullHtml += '<div class="page-break"></div>';
                }
            }

            frameDoc.document.write(fullHtml);
            frameDoc.document.close();

            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 1000);
            
            return false;
        },
    }
}
</script>
<style>

ul.hims-poli-tab li {
    margin:0;
    padding:0;
}

ul.hims-poli-tab li h5{
    padding:0 1em 0 1em;
    margin:0;
}

ul.hims-poli-tab li div a {
    padding:0;
    margin:0;
    color:#000; 
    display:block; 
    padding:0.5em;
    text-decoration: none;
    font-weight: 500;
}

ul.hims-poli-tab li div {
    margin:0; 
    background-color: #eee; 
    color:#000;
    padding:0;
}

ul.hims-poli-tab li.uk-active div {
    margin:0; 
    background-color: #fff; 
    color:#000;
    padding:0;
}

ul.hims-poli-tab li div a h5 {
    color:#666;
    font-weight: 500;
}

ul.hims-poli-tab li.uk-active div a h5 {
    color:#000;
    font-weight: 500;
}
</style>