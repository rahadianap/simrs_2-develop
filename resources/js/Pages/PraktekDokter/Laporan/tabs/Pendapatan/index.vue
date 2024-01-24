<template>
  <div class="uk-grid uk-grid-small" style="justify-content:right;margin-top:-20px;">
    <div class="box-laporan-small uk-flex uk-flex-middle" style="padding: 4px 3px; gap: 10px; margin-left: auto; margin-top: -76px; margin-bottom: 1em;">
      <div class="icon-box-laporan uk-flex uk-flex-middle uk-flex-center" style="font-size: 24px; background: #A4D3FF;">
        <font-awesome-icon :icon="['fas', 'wallet']" style="color: #ffffff;" />
      </div>
      <div class="uk-flex-column uk-flex uk-flex-middle uk-width-1-1" style="align-items: flex-start;">
        <p class="uk-margin-remove-bottom lato-md-14-medium">Rp. {{ formatCurrency(totalPendapatan) }}</p>
        <p class="uk-margin-remove-top uk-margin-remove-bottom lato-sm-14-medium" style="font-size: 11px;">Total Pendapatan</p>
      </div>
    </div>
  </div>
  <div class="uk-container uk-container-large">
    <form action="" @submit.prevent="submitFilter">
      <div class="uk-grid uk-child-width-1-6 uk-grid-small uk-text-small">
        <div>
          <label for="searchFilter">Pencarian</label>
          <input class="uk-input uk-border-rounded" type="text" placeholder="Ketik Deskripsi" v-model="revenueFilter.keyword" @change="submitFilter">
        </div>
        <!-- <div>
          <label for="paymentMethodFilter">Metode Pembayaran</label>
          <select class="uk-select uk-border-rounded" id="paymentMethodFilter">
            <option value="">Pilih Metode Pembayaran</option>
            <option value="tunai">
              <i class="fas fa-money-bill"></i> Tunai
            </option>
            <option value="kartu-kredit">
              <i class="fas fa-credit-card"></i> Kartu Kredit/Debit
            </option>
            <option value="e-wallet">
              <i class="fas fa-mobile-alt"></i> E-Wallet
            </option>
            <option value="qris">
              <i class="fas fa-qrcode"></i> QRIS
            </option>
            <option value="virtual-account">
              <i class="fas fa-university"></i> Virtual Account
            </option>
          </select>
        </div> -->
        <!-- <div>
          <label for="dateFilter">Periode</label>
          <select class="uk-select uk-border-rounded" v-model="revenueFilter.periode" @change="submitFilter">
              <option value="">Pilih Periode</option>
              <option value="hari-ini"><i class="fas fa-calendar-day"></i> Hari Ini</option>
              <option value="bulan-ini"><i class="fas fa-calendar"></i> Bulan Ini</option>
          </select>
        </div>
         -->
        <div>
          <label for="searchFilter">Tgl Mulai</label>
          <input class="uk-input uk-border-rounded" type="date" v-model="revenueFilter.start_date" @change="submitFilter">
        </div>
        <div>
          <label for="searchFilter">Tgl Akhir</label>
          <input class="uk-input uk-border-rounded" type="date" v-model="revenueFilter.end_date" @change="submitFilter">
        </div>

        <div></div>
        <div></div>
        <div>
          <button type="button" class="cetak-button uk-button uk-button-primary uk-margin-top uk-align-right" title="Cetak" style="border-radius:7px;width:max-content;" @click.prevent="printPendapatan()">
            <font-awesome-icon :icon="['fas', 'print']" style="color: #ffffff;" /> Cetak
          </button>
        </div>
      </div>
    </form>
    
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
                <td style="text-align: center; background-color: #fff;padding:1em;" :colspan="8"> 
                    <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                        <span uk-spinner></span>
                        sedang mengambil data
                    </p>
                </td>
            </tr>

            <!--START Data table -->
            <row-pendapatan
              v-else-if="revenueList.data && revenueList.data.length > 0"
              v-for="dt in revenueList.data"
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
  import RowPendapatan from '@/Pages/PraktekDokter/Laporan/tabs/Pendapatan/RowPendapatan.vue';
  import FullPagination from '@/Pages/PraktekDokter/components/BaseTablePraktek/FullPagination.vue';



  export default {
      name: 'pendapatan',
      components : {
          headerPage,
          RowPendapatan,
          FullPagination
      },

      data() {
        return {
          columns: ['', 'Tanggal', 'No.Reg', 'Nama Pasien', 'Dokter', 'Total Tagihan', 'Diskon', 'Total Akhir'],
          pagination : { current_page :0,per_page:10,total_data:0,num_pages:0, },
          isLoading : true,
          showAlertSuccess : false,
          showAlertInput : false,
          selectedData : null,
          totalPendapatan : '',
          // revenueFilter: {},
          // revenueList: null,
        }
      },

      computed: {
          ...mapGetters(['errors']),
          ...mapGetters('praktekDokter',['revenueList','revenueFilter']),
      },

      mounted() {
          this.initialize();
      },

      methods : {

        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('praktekDokter',['lapPendapatan','cetakPendapatan']),

 
        formatCurrency(value) {
          let val = (value/1).toFixed(2).replace('.', ',')
          return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
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
        
        dateFormatting(data) {
          let d = new Date(data);
          let val = d.toLocaleDateString();
          return val;
        },

        initialize(){
          this.revenueFilter.start_date = this.getTodayDate();
          this.revenueFilter.end_date = this.getTodayDate();
          this.submitFilter();
        },

        // submitFilter(){
        //   this.isLoading = true;
        //   this.lapPendapatan(this.revenueFilter).then((response) => {
        //       if (response.success == true) {
        //           this.totalPendapatan = response.sumTotal;
        //       }
        //       this.isLoading = false;
        //   })
        // },

        /* START PAGINATION */
        submitFilter() {
          this.isLoading = true;
          this.revenueFilter.page = this.pagination.current_page;
          this.revenueFilter.per_page = this.pagination.per_page;
          this.lapPendapatan(this.revenueFilter).then((response) => {
              if (response.success == true) {
                  this.revenueList = response.data.data;
                  this.totalPendapatan = response.sumTotal;
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

        printPendapatan(){
          console.log("print pendapatan");
          this.cetakPendapatan(this.revenueFilter).then((response) => {
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
          console.log("asd");
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

<style scoped>
 .empty-state-icon {
      width: 175px;
  }

  
/* ukuran ipad potrait */
@media all and (device-width: 768px) 
and (device-height: 1366px) 
and (orientation:portrait) {
 .cetak-button{
  width: 140px!important;
 }
 .box-laporan-small {
    width: 175px!important;
  }
}

/* ukuran ipad landscape */
@media all and (device-width: 1366px) 
and (device-height: 768px) 
and (orientation:landscape) {
  .cetak-button {
    width: 140px!important;
  }
  .box-laporan-small {
    width: max-content!important;
  }
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

  .box-laporan-small {
    width: max-content;
    height: 47px;
    border-radius: var(--padding-margin-8, 8px);
    border: 1px solid #D9D9D9;
    /* border-radius: 5px; */
    background-color: #ffffff;
    margin-top: 10px;
  }

.icon-box-laporan {
  width:60px;
  height: 44px;
  border-radius: 10px;
}
</style> 