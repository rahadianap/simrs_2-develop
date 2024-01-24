<template>
    <div>
        <header-page title="PASIEN RAWAT INAP" subTitle="daftar pasien rawat inap"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <store-table ref="tableRawatInap"
                :columns = "tbColumns" 
                :config = "configTable"
                :storeData = "inpatientLists"
                :searchCallback = "listAdmisiInap"
                :paramFilter = "inapFilterTable">
                <template v-slot:tbl-body>
                    <tbody style="min-weight:50vh;">
                        <row-pelayanan-inap  v-if="inpatientLists"
                            v-for="dt in inpatientLists.data" 
                            :rowData="dt"
                            :linkCallback="editData"
                            :lockCallback="konfirmasiInap"
                            :rowFunctions="rowFunctions"
                            >
                        </row-pelayanan-inap>
                    </tbody>
                </template>
                <template v-slot:form-filter>                    
                    <filter-pasien-inap></filter-pasien-inap>
                </template>
            </store-table>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import FilterPasienInap from '@/Pages/RawatInap/Pemeriksaan/ListPasienInap/FilterPasienInap.vue';
import RowPelayananInap from '@/Pages/RawatInap/Pemeriksaan/ListPasienInap/RowPelayananInap.vue';

import StoreTable from '@/Components/StoreTable';

export default {
    name : 'list-pasien-poli',
    props : {
        fnEditCallback : { type:Function, required:false },
    },
    components : {
        headerPage,
        RowPelayananInap,
        FilterPasienInap,
        StoreTable,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatInap',['inapFilterTable','inpatientLists'])
    },
    data() {
        return {
            configTable : {
                isExpandable : false,
                filterType : 'ADVANCED', 
                qtyPerPage : 2,
            },
            rowFunctions : [
                { 'title':'Lihat Detail', 'icon':'file-edit','callback':this.editData },
                { 'title':'Cetak Gelang', 'icon':'print','callback':this.printGelang },
            ],
            tbColumns : [
                { name : 'no_bed', title : 'Bed', colType : 'text', },
                { name : 'reg_id', title : 'REG. ID', colType : 'text', },
                { name : 'pasien', title : 'Pasien', colType : 'text', },
                { name : 'dokter_nama', title : 'DPJP', colType : 'text', },
                { name : 'unit_nama', title : 'Unit', colType : 'text', },
                { name : 'penjamin_nama', title : 'Penjamin', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },                  
                { name : '', title : '...', colType : 'text', },                
            ], 

        }
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('rawatInap',['deleteAdmisiInap','listAdmisiInap','confirmAdmisiInap','cetakGelang']),
        
        editData(data) {
            if(data) { 
                if(data.status == 'RAWAT') { 
                    this.$router.push({ name: 'dataPasienRawatInap', params: { trxId: data.trx_id } });
                }
            }
        },

        editPelayananPoli(data) {
            if(data) {
                if(data.status_reg == 'DAFTAR') { 
                    this.$refs.modalRegistrasiPoli.editData(data);
                } 
                else { 
                    this.$router.push({ name: 'dataPasienPoli', params: { trxId: data.trx_id } });
                }
            }
        },

        confirmSucceedCallback(data){
            this.fnEditCallback(data);
        },

        konfirmasiInap(data){
            if(data) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan MENGUNCI data registrasi ${data.trx_id} - ${data.nama_pasien} dan membuat data transaksi dari pemakaian bed. Lanjutkan ?`)){
                    this.isLoading = true;
                    this.confirmAdmisiInap(data).then((response) => {
                        if (response.success == true) {
                            this.isLoading = false; 
                            this.isUpdate = false;
                            alert('data registrasi inap BERHASIL dikonfirmasi.');
                            this.$refs.tableRawatInap.retrieveData();
                        }
                        else { 
                            alert('data registrasi inap tidak berhasil dikonfirmasi.'); 
                            this.isLoading = false;
                        }
                    })
                }
            }
        }, 

        printGelang(data){
            this.cetakGelang(data.trx_id).then((response) => {
                var newNode = document.createElement('p');
                newNode.appendChild(document.createTextNode('html string'));
                this.printDiv(response,"1.0in 11.0in");
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
    }
}
</script>

<style>
ul.hims-inap-tab li {
    margin:0;
    padding:0;
}

ul.hims-inap-tab li h5{
    padding:0 1em 0 1em;
    margin:0;
}

ul.hims-inap-tab li div a {
    padding:0;
    margin:0;
    color:#000; 
    display:block; 
    padding:0.5em;
    text-decoration: none;
    font-weight: 500;
}

ul.hims-inap-tab li div {
    margin:0; 
    background-color: #eee; 
    color:#000;
    padding:0;
}

ul.hims-inap-tab li.uk-active div {
    margin:0; 
    background-color: #fff; 
    color:#000;
    padding:0;
}

ul.hims-inap-tab li div a h5 {
    color:#666;
    font-weight: 500;
}

ul.hims-inap-tab li.uk-active div a h5 {
    color:#000;
    font-weight: 500;
}
</style>