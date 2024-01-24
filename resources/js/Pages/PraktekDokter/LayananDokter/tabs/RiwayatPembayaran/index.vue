<template>
    <div class="uk-container uk-container-large">
        <!-- <header-page title="LAPORAN KUNJUNGAN DOKTER" subTitle="periode oktober"></header-page> -->
        <!-- <form-pembayaran-kasir ref="formPembayaranKasir" v-on:formCatatanKasClosed="submitFilter"></form-pembayaran-kasir> -->
        <form action="" @submit.prevent="submitFilter">
            <div class="uk-grid uk-child-width-1-6 uk-grid-small uk-text-small">
                <div>
                    <label>Pencarian</label>
                    <input class="uk-input uk-border-rounded" type="text" placeholder="Ketik kata pencarian" v-model="paymentFilter.keyword" @change="submitFilter">
                </div>
                <!-- <div>
                    <label for="transactionTypeFilter">Status</label>
                    <select class="uk-select uk-border-rounded" v-model="paymentFilter.status" @change="submitFilter">
                        <option value="">Pilih Status Antrian</option>
                        <option value="antri">
                            <i class="fas fa-arrow-up"></i> Antri
                        </option>
                        <option value="batal">
                            <i class="fas fa-arrow-down"></i> Batal
                        </option>
                        <option value="dilayani">
                            <i class="fas fa-arrow-up"></i> Dilayani
                        </option>
                        <option value="selesai">
                            <i class="fas fa-arrow-down"></i> Selesai
                        </option>
                    </select>
                </div> -->
                <div>
                    <label for="dateFilter">Periode</label>
                    <select class="uk-select uk-border-rounded" v-model="paymentFilter.periode" @change="submitFilter">
                        <option value="">Pilih Periode</option>
                        <option value="hari-ini"><i class="fas fa-calendar-day"></i> Hari Ini</option>
                        <option value="bulan-ini"><i class="fas fa-calendar"></i> Bulan Ini</option>
                    </select>
                </div>
                <!-- <div>
                    <button type="button" class="uk-button uk-button-primary uk-margin-top" title="Tambah Data Baru" style="border-radius: 7px;" @click.prevent="tambahPembayaranKasir">
                        <font-awesome-icon :icon="['fas', 'plus']" style="color: #ffffff;" />
                    </button>
                </div> -->
            </div>
        </form>
            
    </div>
    <div>
        <div class="uk-card uk-card-default card-table-bg uk-overflow-auto">
        <!-- Tabel dalam card -->
            <table class="uk-table" style="padding: 0 3em;">
                <thead>
                    <tr>
                        <th v-for="column in columns" :key="column">
                            <td class="table-th-text" style="font-size: var(--font-md-14);font-weight: var(--bold);">{{ column.toUpperCase() }}</td>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="isLoading">
                        <td style="text-align: center; background-color: #fff;padding:1em;" :colspan="9"> 
                            <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                                <span uk-spinner></span>
                                sedang mengambil data
                            </p>
                        </td>
                    </tr>
                    <!--START Data table -->
                    <row-riwayat-pembayaran
                    v-else-if="paymentLists.data && paymentLists.data.length > 0" 
                        v-for="dt in paymentLists.data"
                        :rowData = "dt" 
                        :fnEditCallback = "ubahDataPembayaran"
                        :fnDeleteCallback = "hapusDataPembayaran"
                        :fnPrintKwitansiCallback = "cetakKwitansi">
                    </row-riwayat-pembayaran>
                    <!-- END Data table.If data not exists, will show EmptyState below -->

                    <!-- START Empty state -->
                    <tr v-else>
                        <td :colspan="columns.length" style="text-align: center; background-color: #fff; padding: 1em;">
                            <img src="../../../../../Assets/icon-data-not-found.png" class="empty-state-icon"/>
                            <h3 style="font-size: 17px; font-weight: 700; color: black;">
                            Maaf, tidak ada data yang ditemukan.
                            </h3>
                            <p style="font-size: 14px; font-weight: 500; font-style: italic; color: black;margin-bottom:10px">
                            Silahkan cek dengan kata kunci lainnya dan filter yang tersedia.
                            </p>
                        </td>
                    </tr>
                    <!-- END Empty state -->
                </tbody>
            </table>
        <!-- END TABLE -->

            <div class="hims-table-margin" style="margin-top:1em; background-color: transparent;padding:0;">
                <full-pagination 
                    ref="pagination"
                    :pagination="pagination"
                    :isLoading="isLoading" 
                    v-on:numRowsChange="updateNumRowChange"
                    v-on:pageChange="pageChange"
                >
                </full-pagination>
            </div>
        </div>
        <modal-pembayaran ref="modalPembayaran" v-on:saveSucceed="submitFilter"></modal-pembayaran>

        <!-- popup batal antrian.
            ~ props subtitleAlert sudah otomatis manggil username yg login,
            karena sudah di declare pada komponen AlertInput.vue
        -->
        <div class="uk-margin-top">
            <AlertInput
                ref="alertInput"
                :showComponentAlertInput="showAlertInput"
                titleAlert="Pembatalan"
                subtitleAlert="Pembatalan pembayaran oleh "
                subjectAlert="Alasan Pembatalan"
                :showInput="showInput"
                confirm="Lanjutkan"
                cancel="Tutup"
                @tutup-alert="showAlertInput = false"
                v-on:confirm = submitBatal
            />
        </div>

        <!-- popup sukses batal pembayaran.
            ~ props subtitleAlert juga manggil username yg login,
            tapi declare pada computed dibawah
        -->
        <div class="uk-margin-top">
            <AlertSuccess
                ref="alertSuccess"
                :showComponentAlertSuccess="showAlertSuccess"
                titleSuccess="Success"
                :subtitleSuccess="subtitleSuccess"
                confirmSuccess="Tutup"
                :iconSuccess="iconSuccess"
                @tutup-alert="showAlertSuccess = false"
            />
        </div>
    </div>
</template>
    
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowRiwayatPembayaran from '@/Pages/PraktekDokter/LayananDokter/tabs/RiwayatPembayaran/RowRiwayatPembayaran.vue';
import "../../../Dashboard/tabs/dashboardStyle.css";
import ModalPembayaran from '@/Pages/PraktekDokter/LayananDokter/components/ModalPembayaran.vue';
import AlertInput from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertInput.vue';
import AlertSuccess from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertSuccess.vue';
import FullPagination from '@/Pages/PraktekDokter/components/BaseTablePraktek/FullPagination.vue';

// import headerPage from '@/Components/HeaderPage.vue';
// import formPembayaranKasir from '@/Pages/PraktekDokter/LayananDokter/tabs/RiwayatPembayaran/FormRiwayatPembayaran.vue';

export default {
    name: 'riwayat-pembayaran',
    components : {
        RowRiwayatPembayaran,
        ModalPembayaran,
        AlertInput,
        AlertSuccess,
        FullPagination
        // headerPage,
        // formPembayaranKasir,
    },
    
    data(){
        return {
            columns: ['', 'Tanggal', 'No. Trans', 'Nama Pasien', 'Dokter', 'Total Tagihan', 'Diskon', 'Total Akhir'],
            pagination : { current_page :0,per_page:10,total_data:0,num_pages:0, },
            isLoading : true,
            showAlertSuccess : false,
            showAlertInput : false,
            iconSuccess:true,
            showInput:true,
            selectedData : null,
        }
    },

    computed: {
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['paymentLists','paymentFilter']),
        ...mapGetters(['user','clientId']), 

        subtitleSuccess() {
            return this.user ? `Pembayaran sukses dibatalkan oleh ${this.user.username} ` : '';
        }
    },

    mounted() {
        this.initialize();
    },

    methods : {
        //...mapActions('praktekDokter',['listPayment','dataPayment','deletePayment','updatePayment']),
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('praktekDokter',['listPayment','deletePayment','dataPayment','updatePayment','cetakKwitansiPDM']),

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        },

        initialize(){
            this.submitFilter();
        },

        // tambahPembayaranKasir() {
        //     // console.log('Tombol tambah diklik');
        //     this.$refs.formPembayaranKasir.newPembayaranKasir();
        // },

        hapusDataPembayaran(data) {            
            if(data) {
                this.selectedData = data;
                this.showAlertInput = true;
            }
        },

        submitBatal(data){
            let dt = {
                reg_id : this.selectedData.reg_id,
                interim_id : this.selectedData.interim_id,
                alasan_batal : data,
            }
            this.CLR_ERRORS();
            this.deletePayment(dt).then((response) => {
                if (response.success == true) {
                    this.showAlertInput = false;
                    this.showAlertSuccess = true;
                    this.submitFilter();
                }
                else { alert (response.message) }
            });
        },

        // submitFilter(){
        //     this.isLoading = true;
        //     this.listPayment(this.paymentFilter).then((response) => {
        //         if (response.success == true) {
        //             console.log(this.paymentLists.data);
        //         }
        //         this.isLoading = false;
        //     })
        // },

        
            //* START PAGINATION *//
            submitFilter() {
                this.isLoading = true;
                this.paymentFilter.page = this.pagination.current_page;
                this.paymentFilter.per_page = this.pagination.per_page;

                this.listPayment(this.paymentFilter).then((response) => {
                    if (response.success == true) {
                        this.paymentLists = response.data.data;
                        this.pagination.total_data = response.data.total;
                        this.pagination.per_page = response.data.per_page;
                        this.pagination.current_page = response.data.current_page;
                        this.pagination.last_page = response.data.last_page;
                        this.pagination.num_pages = response.data.num_pages;
                        this.$refs.pagination.createPagination();
                    }
                    this.isLoading = false;
                });
            },

            updateNumRowChange(num){
                this.pagination.current_page = 0;
                this.pagination.per_page = num;
                this.pagination.num_pages = 0;
                this.pagination.total_data = 0;
                this.pagination.page = 1;

                this.isLoading = true;
                this.submitFilter();
            },

            
            pageChange(index) {
                this.isLoading = true;
                this.pagination.current_page = index;
                this.submitFilter();
            },

            nextIndexClick() {
                this.isLoading = true;
                if (this.pagination.current_page < this.pagination.num_pages) {
                    this.pagination.current_page++;
                    this.submitFilter();
                }
            },

            prevIndexClick() {
                this.isLoading = true;
                if (this.pagination.current_page > 1) {
                    this.pagination.current_page--;
                    this.submitFilter();
                }
            },
            //* END PAGINATION *//

        ubahDataPembayaran(data) {
            if(data) {
                this.$refs.modalPembayaran.editPayment(data);
            }
        },

        cetakKwitansi(data) {
            let ids = data['reg_id'];
            this.cetakKwitansiPDM(ids).then((response) => {
                var newNode = document.createElement('p');
                newNode.appendChild(document.createTextNode('html string'));
                this.printDiv(response,"3.4in 4.5in");
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
    },

}
</script>
<style>
    .empty-state-icon {
        width: 175px;
    }
/* ipad pro */
@media screen and (max-width: 1024px) {
    .table-th-text {
        font-size: var(--font-sm-12)!important;
        font-weight: var(--bold);
    }
    .empty-state-icon {
        width: 135px;
    }
}
</style>