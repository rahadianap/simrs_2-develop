<template>
    <div>
        <!-- DROPDOWN ICON -->
        <slot name="tbl-praktek-button">
            <div class="uk-width-expand" style="padding:0;margin:0;">
   
                <span style="margin: 0 1em;color:black;">
                    <div class="uk-inline">
                        <button 
                            uk-icon="more-vertical" 
                            type="button"
                            tooltip="pilih tindakan"
                        >
                        </button>
                        <div uk-dropdown="animation: slide-top; animate-out: true; duration: 700;" style="border-radius:10px">
                            <ul class="uk-nav uk-dropdown-nav">
                                <!-- dropdown icon -->
                                <li>
                                    <a href="#" style="color:#808080"><span uk-icon="icon:trash;ratio:0.7" class="uk-margin-small-right"></span>
                                        Detail
                                    </a>
                                </li>
                                <li 
                                    class="uk-nav-divider"
                                >
                                </li>
                                <li>
                                    <a href="#" style="color:#F1595F"><span uk-icon="icon:trash;ratio:0.7" class="uk-margin-small-right"></span>
                                        Batal
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </span>
            </div>   
        </slot>
        <!-- END DROPDOWN ICON -->

        <!-- TABLE PRAKTEK DOKTER -->
        <slot>
            <div class="uk-card uk-card-default card-table-bg">
            <!-- Tabel dalam card -->
                <table class="uk-table" style="padding: 0 3em;">
                <thead>
                    <tr>
                        <th v-for="column in columns" :key="column">
                            <a @click="sortBy(column)" :class="{ active: sortKey === column }">
                            <td class="table-th-text">{{ column.toUpperCase() }}</td>
                            <td></td>
                            </a>
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
                    <!-- <tr class="uk-card uk-card-default" style="border-radius: 7px; padding: 15px; background-color: #FFFFFF;"> -->
                    <tr v-if="pencatatanKasLists" class="uk-card uk-card-default" style="border-radius: 7px; background-color: #FFFFFF;" v-for="item in pencatatanKasLists.data" :key="item.kas_id">
                        <td>{{ item.tgl_transaksi }}</td>
                        <td>{{ item.deskripsi }}</td>
                        <td>{{ item.metode_pembayaran }}</td>
                        <td>{{ item.jenis_transaksi }}</td>
                        <td>{{ item.nominal }}</td>
                        <td>
                        <span style="margin: 0 1em;color:#ABA7A7;">
                            <div class="uk-inline">
                                <button 
                                    uk-icon="more-vertical" 
                                    type="button"
                                    tooltip="pilih tindakan"
                                >
                                </button>
                                <div uk-dropdown="pos: bottom-right;animation: slide-top; animate-out: true; duration: 700;" style="border-radius:10px">
                                    <ul class="uk-nav uk-dropdown-nav">
                                        <!-- dropdown icon -->
                                        <li>
                                            <a @click.prevent="showDetailKas(item)" style="color:#808080" uk-toggle="target: #offcanvas-flip"><span class="uk-margin-small-right"><font-awesome-icon :icon="['far', 'note-sticky']" size="lg" style="color: #808080;"/></span>
                                                Detail
                                            </a>
                                        </li>
                                        <li 
                                            class="uk-nav-divider"
                                        >
                                        </li>
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
                    </tbody>
                </table>
            </div>
         </slot>
         <div style="margin-top:1em; background-color: transparent;padding:0;">
            <full-pagination ref="pagination" 
                :pagination = "pagination" 
                v-on:numRowsChange = "updateNumRowChange" 
                v-on:pageChange="pageChange">
            </full-pagination>
        </div>
        <!-- END TABLE PRAKTEK DOKTER -->
    </div>
</template>
<script>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import FullPagination from './FullPagination.vue';
export default {
    tanggal: 'base-table-praktek',
    components : {
        // headerPage,
        FontAwesomeIcon,
        FullPagination,
    },

    data() {
      return {
        pagination : { current_page :0,per_page:10,total_data:0,num_pages:0, },

        sortKey: '',
            
        search: '',

    //     selectedPaymentMethod: '',

    //     selectedJenisTransaksi:'',
        
    //     selectedPeriod: '',

      reverse: false,
        
       columns: ['tanggal', 'deskripsi','metode pembayaran', 'Jenis Transaksi', 'jumlah transaksi'],
        
        pencatatanKas: [
          {tanggal: '2023-10-01', deskripsi: 'Transaksi 1', metodePembayaran: 'Tunai', jenisTransaksi: 'Pemasukan', jumlahTransaksi: '+ Rp. 15.350.000,00', isBulanIni: true, isHariIni:false},
          {tanggal: '2023-10-02', deskripsi: 'Transaksi 2', metodePembayaran: 'Kartu Kredit', jenisTransaksi: 'Pengeluaran', jumlahTransaksi: '- Rp. 750.000,00'},
          {tanggal: '2023-10-03', deskripsi: 'Transaksi 3', metodePembayaran: 'E-Wallet', jenisTransaksi: 'Pemasukan', jumlahTransaksi: '+ Rp. 9.200.000,00'},
          {tanggal: '2023-10-04', deskripsi: 'Transaksi 4', metodePembayaran: 'QRIS', jenisTransaksi: 'Pengeluaran', jumlahTransaksi: '- Rp. 400.000,00'},
          {tanggal: '2023-10-16', deskripsi: 'Transaksi 5', metodePembayaran: 'Virtual Account', jenisTransaksi: 'Pemasukan', jumlahTransaksi: '- Rp. 6.000.000,00'},
          {tanggal: '2023-10-17', deskripsi: 'Transaksi 6', metodePembayaran: 'QRIS', jenisTransaksi: 'Pemasukan', jumlahTransaksi: '- Rp. 6.000.000,00'},
          {tanggal: '2023-10-18', deskripsi: 'Transaksi 7', metodePembayaran: 'Kartu Kredit', jenisTransaksi: 'Pengeluaran', jumlahTransaksi: '- Rp. 6.000.000,00'},
          {tanggal: '2023-10-19', deskripsi: 'Transaksi 8', metodePembayaran: 'Tunai', jenisTransaksi: 'Pemasukan', jumlahTransaksi: '+ Rp. 8.300.000,00'},

        ]
      }
    },

    computed: {
    //   pencatatanKasFiltered() {
    //     const isHariIni = (tanggal) => {
    //       const today = new Date();
    //       const itemDate = new Date(tanggal);
    //       return (
    //         itemDate.getDate() === today.getDate() &&
    //         itemDate.getMonth() === today.getMonth() &&
    //         itemDate.getFullYear() === today.getFullYear()
    //       );
    //     };

    //     const isBulanIni = (tanggal) => {
    //       const today = new Date();
    //       const itemDate = new Date(tanggal);
    //       return (
    //         itemDate.getMonth() === today.getMonth() &&
    //         itemDate.getFullYear() === today.getFullYear()
    //       );
    //     };

    //     return this.pencatatanKas.filter(item => {
    //       return (
    //         (this.selectedPeriod === '' ||
    //           (this.selectedPeriod === 'hari-ini' && isHariIni(item.tanggal)) ||
    //           (this.selectedPeriod === 'bulan-ini' && isBulanIni(item.tanggal))
    //         ) &&
    //         (this.selectedJenisTransaksi === '' || item.jenisTransaksi === this.selectedJenisTransaksi) &&
    //         (this.selectedPaymentMethod === '' || item.metodePembayaran === this.selectedPaymentMethod) &&
    //         item.deskripsi.toLowerCase().includes(this.search.toLowerCase())
    //       );
    //     });
    //   },
    },

    methods : {
    //   tambahCatatanKas() {
    //     console.log('Tombol tambah diklik');
    //     this.$refs.formCatatanKas.newKeluarga();
    //   },
      sortBy(sortKey) {
        this.reverse = this.sortKey === sortKey ? !this.reverse : false;
        this.sortKey = sortKey;
      },

      updateNumRowChange(num){
            this.pagination.current_page = 1;
            this.pagination.num_pages = 0;
            this.pagination.total_data = 0;
            this.params.page = 1;
            this.params.per_page = num;
            this.retrieveData();
        },

        retrieveData() {
            this.isLoading = true;
            this.CLR_ERRORS();  
            if(this.urlSearch == '' || this.urlSearch == null) { return false; }
            httpReq.get(this.urlSearch, { params : this.params })
            .then((response) => {
                this.isLoading = false;
                    
                if (response.data.success == false) {
                    this.$store.commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    this.$emit('on-ready',true);
                } else {
                    this.datas = response.data.data;
                    this.pagination.current_page = response.data.data.current_page;
                    this.pagination.last_page = response.data.data.last_page;
                    this.pagination.per_page = response.data.data.per_page;
                    this.pagination.total_data = response.data.data.total;
                    this.pagination.num_pages = response.data.data.last_page;
                    this.$refs.pagination.createPagination();
                    this.$emit('on-ready',true);
                    this.$emit('updateListDataTable',this.datas);
                }
            })            
            .catch((error) => {
                this.$store.commit('SET_ERRORS', { invalid: error }, { root: true });
            })
        },   
        pageChange(index) {
            this.params.page = index;   
            this.retrieveData();
        },

    },

    

};
</script>
<style scoped>
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
</style>