<template>
  <!-- popup batal pencatatan kas.
        ~ props subtitleAlert sudah otomatis manggil username yg login,
        karena sudah di declare pada komponen AlertInput.vue
    -->
  <!-- Alert Input  -->
  <div class="uk-margin-top">
    <AlertInput
      ref="alertInput"
      :showComponentAlertInput="showAlertInput"
      :showInput="showInput"
      titleAlert="Pembatalan"
      subtitleAlert="Pembatalan data oleh "
      subjectAlert="Alasan Pembatalan"
      v-model="formData"
      confirm="Lanjutkan"
      @confirm="submitBatal"
      cancel="Tutup"
      @tutup-alert="onTutupAlert"
      v-on:confirm="submitBatal"
      style="z-index: 1000"
    />
  </div>

  <!-- popup sukses batal pencatatan kas.
      ~ props subtitleAlert juga manggil username yg login,
      tapi declare pada computed dibawah
  -->
  <!-- Alert Success  -->
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

  <!-- Data Table -->
  <div class="uk-container uk-container-large" style="padding:1em 2em;">
    <div class="uk-grid uk-flex-between" style="gap:10px">
      <div class="uk-width-expand@m uk-width-expand@s">
        <header-page title="PENCATATAN KAS" subTitle="pencatatan kas berdasarkan pemasukan dan pengeluaran" class="uk-margin-bottom"></header-page>

        <div class="uk-grid uk-child-width-1-5 uk-grid-small uk-text-small" style="margin-top:0.1em;">
          <form-catatan-kas ref="formCatatanKas" v-on:formCatatanKasClosed="submitFilter"></form-catatan-kas>

          <form action="" @submit.prevent="submitFilter">
            <div>
              <label for="searchFilter">Pencarian</label>
              <input class="uk-input uk-border-rounded" type="text" v-model="filterDataKas.keyword" placeholder="Ketik Deskripsi" id="searchFilter"  @change="submitFilter">
            </div>
          </form>
          <div>
            <label for="paymentMethodFilter">Metode Pembayaran</label>
            <select class="uk-select uk-border-rounded" id="paymentMethodFilter" v-model="filterDataKas.metode_pembayaran" @change="submitFilter">
              <option value="">Pilih Metode Pembayaran</option>
              <option v-for="mtd in metodeBayarKas" v-bind:value="mtd.value" v-bind:key="mtd.value">
                <font-awesome-icon :icon="['fas', mtd.iconClass ]" style="color: #2BD09F;" />{{ mtd.text }}
              </option>
            </select>
          </div>
          <div>
            <label for="transactionTypeFilter" id="transactionTypeFilter" >Jenis Transaksi</label>
            <select class="uk-select uk-border-rounded" id="transactionTypeFilter" v-model="filterDataKas.jns_transaksi" @change="submitFilter">
              <option value="">Pilih Jenis Transaksi</option>
              <option v-for="jns in jnsTransaksiKas" v-bind:value="jns.value" v-bind:key="jns.value">
                <font-awesome-icon :icon="['fas', jns.iconClass ]" style="color: #2BD09F;" />{{ jns.text }}
              </option>
            </select>
          </div>
          <div>
            <label for="dateFilter">Periode</label>
            <select class="uk-select uk-border-rounded"  id="dateFilter" v-model="filterDataKas.periode" @change="submitFilter">
              <option value="">Pilih Periode</option>
              <option value="hari-ini"><font-awesome-icon :icon="['fas', 'calendar-day']" /> Hari Ini </option>
              <option value="bulan-ini"><font-awesome-icon :icon="['fas', 'calendar']" /> Bulan Ini </option>
            </select>
          </div>
          <div>
            <button class="uk-button uk-button-primary uk-margin-top" title="Tambah Data Baru" style="border-radius: 7px;" @click.prevent="tambahCatatanKas">
              <font-awesome-icon :icon="['fas', 'plus']" style="color: #ffffff;" />
            </button>
          </div>
        
          <div>
            <Detail ref="offcanvasDetail" canvasTitle="Detail Transaksi" v-on:saved="submitFilter"/>
          </div>
        </div>
      </div>
      <div class="uk-width-1-5@l uk-width-1-3@m uk-width-auto@s uk-grid@s uk-padding-remove-left">
        <div class="box-laporan-small uk-flex uk-flex-middle uk-margin-bottom" style="padding: 4px 3px; gap: 10px; margin-left: auto; margin-top: 1px;">
          <div class="icon-box-laporan uk-flex uk-flex-middle uk-flex-center" style="font-size: 24px; background: rgba(51, 195, 226, 0.5)">
            <font-awesome-icon :icon="['fas', 'arrow-right-to-bracket']" style="color: #ffffff;" />
          </div>
          <div class="uk-flex-column uk-flex uk-flex-middle uk-width-1-1" style="align-items: flex-start;">
            <p class="uk-margin-remove-bottom lato-md-14-medium">Rp. {{ formatCurrency(pencatatanKasTotal.terima) }}</p>
            <p class="uk-margin-remove-top uk-margin-remove-bottom lato-sm-14-medium" style="font-size: 11px;">Total Pemasukan</p>
          </div>
        </div>
        <div class="box-laporan-small uk-flex uk-flex-middle" style="padding: 4px 3px; gap: 10px;margin-left:auto;margin-top: 1px;">
          <div class="icon-box-laporan uk-flex uk-flex-middle uk-flex-center" style="font-size: 24px; background: rgba(150, 135, 255, 0.50)">
            <font-awesome-icon :icon="['fas', 'arrow-right-from-bracket']" style="color: #ffffff;" />
          </div>
          <div class="uk-flex-column uk-flex uk-flex-middle uk-width-1-1" style="align-items: flex-start;">
            <p class="uk-margin-remove-bottom lato-md-14-medium">Rp. {{ formatCurrency(pencatatanKasTotal.keluar) }}</p>
            <p class="uk-margin-remove-top uk-margin-remove-bottom lato-sm-14-medium" style="font-size: 11px;">Total Pengeluaran</p>
          </div>
        </div>
      </div>
    </div>
 
    <div>
      <div class="uk-card uk-card-default card-table-bg">
      <!-- Tabel dalam card -->
        <div class="uk-overflow-auto">
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
                  <td style="text-align: center; background-color: #fff;padding:1em;" :colspan="columns.length + 1"> 
                      <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                          <span uk-spinner></span>
                          sedang mengambil data
                      </p>
                  </td>
              </tr>
              <!-- START Data table -->
              <!-- <tr class="uk-card uk-card-default" style="border-radius: 7px; padding: 15px; background-color: #FFFFFF;"> -->
              <tr v-else-if="kasLists.data && kasLists.data.length > 0" v-for="item in kasLists.data" class="uk-card uk-card-default" style="border-radius: 7px; background-color: #FFFFFF;" :key="item.kas_id">
                <td>{{ dateFormatting(item.tgl_transaksi) }}</td>
                <td>{{ item.deskripsi }}</td>
                <td>{{ item.metode_pembayaran }}</td>
                <td>{{ item.jenis_transaksi }}</td>
                <td style="text-align:right;">{{ formatCurrency(item.nominal) }}</td>
                <td>
                  <span style="margin: 0 1em;color:#ABA7A7;">
                      <div  v-if="item.interim_id == null" class="uk-inline">
                          <button 
                            v-if="item.interim_id == null"
                            uk-icon="more-vertical" 
                            type="button"
                            tooltip="pilih tindakan"
                          >
                          </button>
                          <button 
                            v-else
                            style="display: none;"
                          >
                          </button>
                          <div uk-dropdown="pos: bottom-right; animation: slide-top; animate-out: true; duration: 700;" style="border-radius: 10px">
                            <ul class="uk-nav uk-dropdown-nav">
                              <!-- dropdown icon -->
                              <li>
                                  <a @click.prevent="showDetailKas(item)" style="color:#808080" uk-toggle="target: #offcanvas-flip"><span class="uk-margin-small-right"><font-awesome-icon :icon="['far', 'note-sticky']" size="lg" style="color: #808080;"/></span>
                                      Detail
                                  </a>
                              </li>
                              <li class="uk-nav-divider"></li>
                              <li>
                                  <a href="#" style="color:#F1595F" @click.prevent="batalTambahData(item)"><span class="uk-margin-small-right"><font-awesome-icon :icon="['fas', 'xmark']" size="lg" style="color: #F1595F;"/></span>
                                      Batalkan
                                  </a>
                              </li>
                            </ul>
                          </div>
                      </div>
                  </span>
                </td>
              </tr>
              <!-- END Data table.If data not exists, will show EmptyState below -->

              <!-- START Empty state -->
              <tr v-else>
                  <td :colspan="columns.length" style="text-align: center; background-color: #fff; padding: 1em;">
                      <img src="../../../Assets/icon-data-not-found.png" class="empty-state-icon"/>
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
        </div>
        <div class="hims-table-margin" style="margin-top:1em; background-color: transparent;padding:0;">
          <full-pagination 
            ref="pagination"
            :pagination="pagination"
            :isLoading="isLoading" 
            v-on:numRowsChange="updateNumRowChange"
            v-on:pageChange="pageChange"
          ></full-pagination>
        </div>
      </div>
    </div>

 


  </div>

</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import { far } from '@fortawesome/free-regular-svg-icons';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import formCatatanKas from '@/Pages/PraktekDokter/PencatatanKas/FormCatatanKas.vue';
import AlertInput from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertInput.vue';
import AlertSuccess from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertSuccess.vue';
import Detail from '@/Pages/PraktekDokter/PencatatanKas/OffcanvasCatatanKas.vue';
import '../Dashboard/tabs/dashboardStyle.css';
import FullPagination from '@/Pages/PraktekDokter/components/BaseTablePraktek/FullPagination.vue';


library.add(far, fas);

export default {
    tanggal: 'pencatatan-kas',
    components : {
        headerPage,
        FontAwesomeIcon,
        library,
        formCatatanKas,
        Detail,
        AlertInput,
        AlertSuccess,
        FullPagination
    },

    data() {
      return {        
        // kasLists: [],  
        columns: ['tanggal', 'deskripsi','metode pembayaran', 'Jenis Transaksi', 'jumlah transaksi'],
        pagination: { current_page: 1, per_page: 10, total_data: 0, num_pages: 0,last_page: undefined, },
        showAlertInput: false,
        showInput: true,
        showAlertSuccess : false,
        iconSuccess:true,
        selectedData : null,

        
        // formData: "",
        formData : {
          alasan_batal: null,
        },

        sortKey: '',
        search: '',
        selectedPaymentMethod: '',
        selectedJenisTransaksi:'',
        selectedPeriod: '',
        reverse: false,
        pencatatanKasTotal : [],

        pencatatanKasData: [],  

        isLoading : true,
      }
    },

    mounted() {
      // console.log('Component mounted. kasLists.data:', this.kasLists.data);
      this.initialize();
    },

    computed: {
      ...mapGetters(['errors']),
      ...mapGetters('pencatatankas',['jnsTransaksiKas','kasLists','metodeBayarKas','filterDataKas']),
      ...mapGetters(['user','clientId']), 

      subtitleSuccess() {
            return this.user ? `Penambahan data Sukses dibatalkan oleh ${this.user.username} ` : '';
        }

      // pencatatanKasFiltered() {
      //   const isHariIni = (tanggal) => {
      //     const today = new Date();
      //     const itemDate = new Date(tanggal);
      //     return (
      //       itemDate.getDate() === today.getDate() &&
      //       itemDate.getMonth() === today.getMonth() &&
      //       itemDate.getFullYear() === today.getFullYear()
      //     );
      //   };

      //   const isBulanIni = (tanggal) => {
      //     const today = new Date();
      //     const itemDate = new Date(tanggal);
      //     return (
      //       itemDate.getMonth() === today.getMonth() &&
      //       itemDate.getFullYear() === today.getFullYear()
      //     );
      //   };

      //   return this.pencatatanKas.filter(item => {
      //     return (
      //       (this.selectedPeriod === '' ||
      //         (this.selectedPeriod === 'hari-ini' && isHariIni(item.tanggal)) ||
      //         (this.selectedPeriod === 'bulan-ini' && isBulanIni(item.tanggal))
      //       ) &&
      //       (this.selectedJenisTransaksi === '' || item.jenisTransaksi === this.selectedJenisTransaksi) &&
      //       (this.selectedPaymentMethod === '' || item.metodePembayaran === this.selectedPaymentMethod) &&
      //       item.deskripsi.toLowerCase().includes(this.search.toLowerCase())
      //     );
      //   });
      // },
    },

    watch: {
      // showAlertInput(newVal) {
      //   console.log('showAlertInput changed:', newVal);
      //   this.clearAlasanBatal();
      // },
    },


    methods : {
      ...mapActions('pencatatankas',['listKas','deleteKas']),
      ...mapMutations(['CLR_ERRORS']),

      initialize(){
        //this.showAlertInput = true;
        this.clearAlasanBatal();
        this.submitFilter();
      },

      formatCurrency(value) {
          let val = (value/1).toFixed(2).replace('.', ',')
          return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
      },

      dateFormatting(data) {
          let dt = new Date(data);
          // let val = d.toLocaleDateString();
          // return val;
          let year = dt.getFullYear();
          let month = dt.getMonth() + 1;
          if(month < 10) {month = `0${month}`}
          let date = dt.getDate();
          if(date < 10) {date = `0${date}`}

          let hours = dt.getHours();
          if(hours < 10) {hours = `0${hours}`}
          let minutes = dt.getMinutes();
          if(minutes < 10) {minutes = `0${minutes}`}

          let str_dt = `${year}-${month}-${date} ${hours}:${minutes}`;
          return str_dt;
      },
      
      batalTambahData(data) {
        this.CLR_ERRORS();
        this.clearAlasanBatal();
        this.selectedData = data;
        this.showAlertInput = true;
        
      },

      tambahCatatanKas() {
        // console.log('Tombol tambah diklik');
        this.$refs.formCatatanKas.newPencatatanKas();
      },
      
      showDetailKas(data) {
        if(data) {
            this.$refs.offcanvasDetail.showData(data);
        }
      },

      // submitFilter(){
      //   this.selectedData = null;
      //   this.isLoading = true;
      //   this.listKas(this.filterDataKas).then((response) => {
      //       if (response.success == true) {
      //         this.kasLists = response.data.detail;
      //         this.pencatatanKasTotal = response.data.total;
      //         console.log(this.kasLists.data);
      //       }
      //       this.isLoading = false;
      //   })
      // },

      //* START PAGINATION *//
      submitFilter() {
      this.isLoading = true;
      this.filterDataKas.page = this.pagination.current_page;
      this.filterDataKas.per_page = this.pagination.per_page;

        this.listKas(this.filterDataKas).then((response) => {
          if (response.success == true) {
            console.log(response.data);  
            this.pencatatanKasLists = response.data.detail.data;
              this.pencatatanKasTotal = response.data.total;

              this.pagination.total_data = response.data.detail.total;
              this.pagination.per_page = response.data.detail.per_page;
              this.pagination.current_page = response.data.detail.current_page;
              this.pagination.last_page = response.data.detail.last_page;
              this.pagination.num_pages = response.data.detail.last_page;
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

      submitBatal(data) {
          let dt = {
            kas_id : this.selectedData,
            alasan_batal : data,
          };

          this.CLR_ERRORS();
          this.clearAlasanBatal();


          this.deleteKas(dt).then((response) => {
            if (response.success == true) {
              // alert('Data berhasil dihapus.');
              this.showAlertInput = false;
              this.showAlertSuccess = true;
              this.submitFilter();
            }
            else {
              alert(response.message);
            }
          });         
      },

      batalTambahData(data) {
        // console.log('Konfirmasi Batal tambah data', data);
        if (data) {
          this.selectedData = data.kas_id;
          this.showAlertInput = true;
          console.log('showAlertInput:', this.showAlertInput);
        }
      },

      sortBy(sortKey) {
        this.reverse = this.sortKey === sortKey ? !this.reverse : false;
        this.sortKey = sortKey;
      },

      onTutupAlert() {
        this.showAlertInput = false;
        this.clearAlasanBatal();
      },

      clearAlasanBatal() {
        this.formData.alasan_batal = null;
      }
    },
};
</script>

<style>
  .empty-state-icon {
      width: 175px;
  }

  @media screen and 
    (max-width: 768px) and (max-height: 1024px),
    (max-width: 1024px) and (max-height: 768px) 
    { /* resolusi potrait & landscape ipad mini */
      #searchFilter,
      #dateFilter {
        margin-top: 20px;
    }
  }

  /* ----------- Non-Retina Screens ----------- */
  @media screen 
    and (min-device-width: 1200px) 
    and (max-device-width: 1600px) 
    and (-webkit-min-device-pixel-ratio: 1) { 
  }

  /* ----------- Retina Screens ----------- */
  @media screen 
    and (min-device-width: 1200px) 
    and (max-device-width: 1600px) 
    and (-webkit-min-device-pixel-ratio: 2)
    and (min-resolution: 192dpi) { 
  } 

/* ipad pro */
@media only screen and (max-width: 1024px) {
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
    width: 210px;
    height: 48px;
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