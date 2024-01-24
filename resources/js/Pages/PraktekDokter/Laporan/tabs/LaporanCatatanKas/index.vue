<template>
  <div class="uk-width-1-1 uk-grid-small uk-grid-match kas-container" style="justify-content:right;margin-top:-20px;">
    <div class="uk-width-1-6@l uk-width-1-4@m uk-width-1-4@s">  
     <div class="box-laporan-small uk-flex uk-flex-middle" style="padding: 4px 3px; gap: 10px;margin-top: -73px; margin-bottom: 1em;">
        <div class="icon-box-laporan uk-flex uk-flex-middle uk-flex-center" style="font-size: 24px; background: rgba(51, 195, 226, 0.5)">
          <font-awesome-icon :icon="['fas', 'arrow-right-to-bracket']" style="color: #ffffff;" />
        </div>
        <div class="uk-flex-column uk-flex uk-flex-middle uk-width-1-1" style="align-items: flex-start;">
          <p class="uk-margin-remove-bottom lato-md-14-medium">Rp. {{ formatCurrency(totalMasuk) }}</p>
          <p class="uk-margin-remove-top uk-margin-remove-bottom lato-sm-14-medium" style="font-size: 11px;">Total Pemasukan</p>
        </div>
      </div>
    </div>

    <div class="uk-width-1-6@l uk-width-1-4@m uk-width-1-4@s kas-column">
      <div class="box-laporan-small uk-flex uk-flex-middle" style="padding: 4px 3px; gap: 10px;margin-top: -73px; margin-bottom: 1em;">
        <div class="icon-box-laporan uk-flex uk-flex-middle uk-flex-center" style="font-size: 24px; background: rgba(150, 135, 255, 0.50)">
          <font-awesome-icon :icon="['fas', 'arrow-right-from-bracket']" style="color: #ffffff;" />
        </div>
        <div class="uk-flex-column uk-flex uk-flex-middle uk-width-1-1" style="align-items: flex-start;">
          <p class="uk-margin-remove-bottom lato-md-14-medium">Rp. {{ formatCurrency(totalKeluar) }}</p>
          <p class="uk-margin-remove-top uk-margin-remove-bottom lato-sm-14-medium" style="font-size: 11px;">Total Pengeluaran</p>
        </div>
      </div>
    </div>
  </div>
  <div class="uk-container uk-container-large">
    <!-- <div>
      <div class="box-laporan-small uk-flex uk-flex-middle" style="padding: 4px 3px; gap: 10px; margin-left: auto; margin-top: -73px; margin-bottom: 1em;">
        <div class="icon-box-laporan uk-flex uk-flex-middle uk-flex-center" style="font-size: 24px; background: rgba(150, 135, 255, 0.50)">
          <font-awesome-icon :icon="['fas', 'arrow-right-from-bracket']" style="color: #ffffff;" />
        </div>
        <div class="uk-flex-column uk-flex uk-flex-middle uk-width-1-1" style="align-items: flex-start;">
          <p class="uk-margin-remove-bottom lato-md-14-medium">Rp. {{ formatCurrency(totalKeluar) }}</p>
          <p class="uk-margin-remove-top uk-margin-remove-bottom lato-sm-14-medium" style="font-size: 11px;">Total Pengeluaran</p>
        </div>
      </div>
    </div> -->
    <div class="uk-flex uk-width-1-1 uk-child-width-1-1@s">
      <div class="uk-grid uk-child-width-1-5@l uk-child-width-1-3@s uk-grid-small uk-text-small" style="margin-top:1em;row-gap:20px;">
        <div>
          <label for="searchFilter">Pencarian</label>
          <input class="uk-input uk-border-rounded" type="text" v-model="paymentFilter.keyword" placeholder="Ketik Deskripsi" id="searchFilter"  @change="submitFilter">
        </div>
        <div>
          <label for="paymentMethodFilter">Metode Pembayaran</label>
          <select class="uk-select uk-border-rounded" id="paymentMethodFilter" v-model="paymentFilter.metode_pembayaran" @change="submitFilter">
            <option value="">Pilih Metode Pembayaran</option>
            <option value="TUNAI"> TUNAI </option>
            <option value="CREDIT CARD"> CREDIT CARD </option>
            <option value="QRIS"> QRIS </option>
            <option value="E-WALLET"> E-WALLET </option>
            <option value="VIRTUAL ACCOUNT"> VIRTUAL ACCOUNT </option>
          </select>
        </div>
        <div>
          <label for="transactionTypeFilter" id="transactionTypeFilter" >Jenis Transaksi</label>
          <select class="uk-select uk-border-rounded" id="transactionTypeFilter" v-model="paymentFilter.jenis_transaksi" @change="submitFilter">
            <option value="">Pilih Jenis Transaksi</option>
            <option value="Pengeluaran"> Pengeluaran </option>
            <option value="Pemasukan"> Pemasukkan </option>
          </select>
        </div>
        <div>
          <label for="dateFilter">Periode Mulai</label>
          <input class="uk-input uk-border-rounded" type="date" v-model="paymentFilter.start_date"  @change="submitFilter">
          <!-- <select class="uk-select uk-border-rounded"  id="dateFilter" v-model="paymentFilter.periode" @change="submitFilter">
            <option value="">Pilih Periode</option>
            <option value="hari-ini"><font-awesome-icon :icon="['fas', 'calendar-day']" /> Hari Ini </option>
            <option value="bulan-ini"><font-awesome-icon :icon="['fas', 'calendar']" /> Bulan Ini </option>
          </select> -->
        </div>
        <div>
          <label for="dateFilter">Periode Akhir</label>
          <input class="uk-input uk-border-rounded" type="date" v-model="paymentFilter.end_date" @change="submitFilter">
          <!-- <select class="uk-select uk-border-rounded"  id="dateFilter" v-model="paymentFilter.periode" @change="submitFilter">
            <option value="">Pilih Periode</option>
            <option value="hari-ini"><font-awesome-icon :icon="['fas', 'calendar-day']" /> Hari Ini </option>
            <option value="bulan-ini"><font-awesome-icon :icon="['fas', 'calendar']" /> Bulan Ini </option>
          </select> -->
        </div>
      </div>
      <div class="uk-width-1-5">
        <button class="uk-button uk-button-primary uk-margin-top uk-align-right" title="Cetak" style="border-radius: 7px;width:max-content;" @click.prevent="printKas()">
          <font-awesome-icon :icon="['fas', 'print']" style="color: #ffffff;" /> Cetak      </button>
      </div>
    
    </div>
    
    <div>
      <div class="uk-card uk-card-default card-table-bg uk-overflow-auto">
        <table class="uk-table" style="padding:0 3em;">
            <thead>
              <tr>
                <th v-for="column in columns" :key="column">
                    <td class="table-th-text" style="font-size: var(--font-md-14);font-weight: var(--bold);">{{ column.toUpperCase() }}</td>
                </th>
            </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                  <td style="text-align: center; background-color: #fff;padding:1em;" :colspan="7"> 
                      <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                          <span uk-spinner></span>
                          sedang mengambil data
                      </p>
                  </td>
              </tr>

              <!--START Data table -->
              <row-kas
                v-else-if="paymentLists.data && paymentLists.data.length > 0"
                v-for="dt in paymentLists.data"
                :rowData = "dt" 
                :key="dt.id"
              />
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
    </div>
  </div>
</template>

<script>
  import { mapGetters, mapActions, mapMutations } from 'vuex';
  import "../../../Dashboard/tabs/dashboardStyle.css";
  import headerPage from '@/Components/HeaderPage.vue';
  import RowKas from '@/Pages/PraktekDokter/Laporan/tabs/LaporanCatatanKas/RowKas.vue';    
  import FullPagination from '@/Pages/PraktekDokter/components/BaseTablePraktek/FullPagination.vue';

  export default {
    name: 'laporan-catatan-kas',
    components : {
        headerPage,
        RowKas,
        FullPagination,
    },

    data() {
      return {
        columns: ['Tanggal','Deskripsi','Metode Pembayaran', 'Jenis Transaksi', 'Trans Keluar', 'Trans. Masuk'],
        pagination : { current_page :0,per_page:10,total_data:0,num_pages:0, },
        totalMasuk : [],
        totalKeluar : [],
        isLoading : true,
      }
    },

    computed: {
      ...mapGetters(['errors']),
      ...mapGetters('praktekDokter',['paymentLists','paymentFilter']),
    },

    mounted() {
      this.initialize();
    },

    methods : {
      ...mapMutations(['CLR_ERRORS']),
      ...mapActions('praktekDokter',['lapKas','cetakKas']),

      initialize(){
        this.paymentFilter.start_date = this.getTodayDate();
        this.paymentFilter.end_date = this.getTodayDate();
        this.submitFilter();
      },

      dateFormatting(data) {
          let d = new Date(data);
          let val = d.toLocaleDateString();
          return val;
      },

      getTodayDate(){
        let dt = new Date();
        let year = dt.getFullYear();
        let month = dt.getMonth() + 1;
        if(month < 10) {month = `0${month}`}
        let date = dt.getDate();
        if(date < 10) {date = `0${date}`}
        let str_dt = `${year}-${month}-${date}`
        return str_dt;
      },
      
      formatCurrency(value) {
          let val = (value/1).toFixed(2).replace('.', ',')
          return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
      },
      
      // submitFilter() {
      //   this.isLoading = true;
      //   this.lapKas(this.paymentFilter).then((response) => {
      //       if (response.success == true) {
      //         this.totalMasuk = response.totalPemasukan;
      //         this.totalKeluar = response.totalPengeluaran;
      //       }
      //       this.isLoading = false;
      //   })
      // },
      
      /* START PAGINATION */
      submitFilter() {
      this.isLoading = true;
      this.paymentFilter.page = this.pagination.current_page;
      this.paymentFilter.per_page = this.pagination.per_page;
      this.lapKas(this.paymentFilter).then((response) => {
          if (response.success == true) {
              this.paymentLists = response.data.data;
              this.totalMasuk = response.totalPemasukan;
              this.totalKeluar = response.totalPengeluaran;
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
      
      printKas(){
        this.cetakKas(this.paymentFilter).then((response) => {
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
                      size:landscape;
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
 .empty-state-icon {
    width: 175px;
  }
/* ukuran ipad pro */
@media screen and (max-width: 1024px) {
    .table-th-text {
        font-size: var(--font-sm-12)!important;
        font-weight: var(--bold);
    }
    .empty-state-icon {
        width: 135px;
    }
}

  .card-table-bg {
    margin-top: 1em; 
    border-radius: 12px; 
    height: auto;
    padding: 0 0 2em 0;

  }
  .uk-table {
      border-collapse: inherit;
      border-spacing: 0px 12px;
      width: 100%;
  }
  .uk-table td {
    font-family: var(--lato);
    color: #4D4D4D;
    font-size: var(--font-sm-12);
    font-weight: var(--bold);
    line-height:20px;
    letter-spacing:0.1px;
  }

  .uk-table th {
    font-family: var(--inter);
    color: #000000;
    font-size: var(--font-md-14);
    font-weight: var(--semibold);
    line-height:24px;
    letter-spacing:0.1px;
    padding: 0;
    text-align: left;
    vertical-align: center;
  }

  td:first-child,
  th:first-child {
    border-radius: 10px 0 0 10px;
  }

  td:last-child,
  th:last-child {
    border-radius: 0 10px 10px 0;
  }

  .uk-card-default {
      background-color: #F8F8F8;
      color: #666;
      box-shadow: 0 5px 15px rgba(0,0,0,.08);
  }

  .kas-container {
      display: flex;
  }

  /* ukuran ipad mini potrait */
  @media all and (device-width: 768px) 
  and (device-height: 1024px) 
  and (orientation:portrait) {
      .kas-container {
          display: flex;
          flex-direction: column!important;
          align-items: flex-end;
      }
      .kas-column {
        margin-top:60px!important;
        margin-bottom:-25px!important;
      }
      .cetak-button{
      width: 140px!important;
    }
    .box-laporan-small {
        width: 175px!important;
      }
  }

  /* ukuran ipad air potrait */
  @media all and (device-width: 820px) 
  and (device-height: 1180px) 
  and (orientation:portrait) {
  .kas-container {
      display: flex;
      flex-direction: column!important;
      align-items: flex-end;
    }
    .kas-column {
      margin-top:60px!important;
      margin-bottom:-25px!important;
    }
  }

  
  /* ukuran ipad pro potrait */
  @media all and (device-width: 1024px) 
  and (device-height: 1366px) 
  and (orientation:portrait) {
  .kas-container {
      display: flex;
      flex-direction: column!important;
      align-items: flex-end;
    }
    .kas-column {
      margin-top:60px!important;
      margin-bottom:-25px!important;
    }
  }

  /* ukuran ipad mini landscape */
  @media all and (device-width: 1024px) 
  and (device-height: 768px) 
  and (orientation:landscape) {
    .kas-container {
      display: flex;
      flex-direction: column!important;
      align-items: flex-end;
    }
    .kas-column {
      margin-top:50px!important;
      margin-bottom:-25px!important;
    }
  }

  /* ukuran ipad pro landscape */
  @media all and (device-width: 1366px) 
  and (device-height: 1024px) 
  and (orientation:landscape) {
    .cetak-button {
      width: 145px!important;
    }
    .box-laporan-small {
      width: max-content!important;
    }
  }

  .box-laporan {
    background: white;
    width: max-content;
    height: 47px;
    border-radius: var(--padding-margin-8, 8px);
    border: 1px solid rgba(217, 217, 217, 1);
  }
  .icon-box-laporan {
    width:60px;
    height: 40px!important;
    border-radius: 13px;
    background: rgba(51, 195, 226, 0.5);
  }
</style>