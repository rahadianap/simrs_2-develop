<template>
    <div>
        <div style="margin-top:0;padding:0;">            
            <store-table ref="tablePendaftaran"
                :columns = "tbColumns" 
                :buttons = "buttons"
                :config = "configTable"
                :storeData = "registrationLists"
                :searchCallback = "listRegistrasi"
                :paramFilter = "regFilterTable">
                <template v-slot:tbl-body>
                    <tbody style="min-weight:50vh;">
                        <row-antrian  v-if="registrationLists"
                            v-for="dt in registrationLists.data" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-antrian>
                    </tbody>
                </template>
                <template v-slot:form-filter>                    
                    <filter-antrian></filter-antrian>
                </template>
            </store-table>
        </div>
        <cetakan-pendaftaran ref="cetakanPendaftaran" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></cetakan-pendaftaran>
        <cetakan-tracer ref="cetakanTracer" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></cetakan-tracer>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import StoreTable from '@/Components/StoreTable';
import RowAntrian from '@/Pages/RawatJalan/Pendaftaran/AntrianPoli/RowAntrian.vue';
import FilterAntrian from '@/Pages/RawatJalan/Pendaftaran/AntrianPoli/FilterAntrian.vue';
import CetakanPendaftaran from '@/Pages/RawatJalan/Pendaftaran/cetakan/cetakanPendaftaran.vue';
import CetakanTracer from '@/Pages/RawatJalan/Pendaftaran/cetakan/cetakanTracer.vue';

export default {
    name : 'antrian-poli',
    components : {
        StoreTable,
        RowAntrian,
        FilterAntrian,
        CetakanPendaftaran,
        CetakanTracer
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('pendaftaran',['registrationLists','regFilterTable']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'ADVANCED', },
            rowFunctions : [
                { 'title':'Lihat Data', 'icon':'file-edit','callback':this.updateData },
                { 'title':'Cetak Bukti Pendaftaran', 'icon':'file-pdf','callback':this.cetakPendaftaran },
                { 'title':'Cetak Tracer', 'icon':'file-pdf','callback':this.cetakTracer },
                // { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
            ],

            tbColumns : [
                { name : 'trx_id', title : 'Transaksi ID', colType : 'linkmenus', },   
                { name : 'no_antrian', title : 'Antrian', colType : 'text', isSearchable : true, },
                { name : 'nama_pasien', title : 'PASIEN', colType : 'linkmenus',  },
                { name : 'dokter_nama', title : 'Dokter Unit', colType : 'text', isSearchable : true, },
                { name : 'penjamin_nama', title : 'Penjamin', colType : 'text', isSearchable : true, },
                { name : 'status', title : 'STATUS', colType : 'text', isSearchable : true, },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }                
            ], 

            buttons : [
                { title : 'Antrian Baru', icon:'plus-circle', 'callback' : this.AddBooking },
            ],
            searchUrl : '/registrations',
            regPoliLists : null,
        }
    },
    mounted(){
    },
    methods : {
        ...mapActions('pendaftaran',['deleteRegistrasi','listRegistrasi']),
        ...mapActions('cetakan', ['dataCetakanPendaftaran', 'dataCetakanTracer']),
        ...mapMutations(['CLR_ERRORS']),

        AddBooking(){
            this.$router.push({ name: 'formAntrianPoli', params: { tipe:'form', dataId: 'baru' } });
        },

        refreshTable() {
            this.$refs.tablePendaftaran.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            if(data) {
                this.$router.push({ name: 'formAntrianPoli', params: { tipe:'form',dataId: data.trx_id } });
            }
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