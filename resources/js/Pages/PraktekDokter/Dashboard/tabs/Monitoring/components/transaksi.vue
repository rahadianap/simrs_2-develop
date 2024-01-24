<template>
    <div class="uk-container uk-container-large">
        <!-- <header-page title="LAPORAN KUNJUNGAN DOKTER" subTitle="periode oktober"></header-page> -->
        <div class="uk-flex uk-width-1-1 uk-grid-small uk-text-small">
            <div>
                <label for="searchFilter">Pencarian</label>
                <input class="uk-input uk-border-rounded" type="text" placeholder="Cari Data Pasien" id="searchFilter" v-model="filterDataTransaksiDashboard.keyword" @change="submitFilter">
            </div>
            <div>
                <search-list
                    :config = configSelect
                    :dataLists = dokLists
                    searchName = "dokter"
                    label = "Dokter"
                    placeholder = "pilih dokter"
                    captionField = "dokter_nama"
                    valueField = "dokter_id"
                    :detailItems = dokterDesc
                    :value = "filterDataTransaksiDashboard.dokter"
                    v-on:item-select = "dokterSelected"
                ></search-list>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default card-table-bg uk-overflow-auto">
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
                        <!--START Data table -->
                        <tr v-else-if="transaksiDashboardLists.data && transaksiDashboardLists.data.length > 0"  v-for="item in transaksiDashboardLists.data" class="uk-card uk-card-default" style="border-radius: 7px; background-color: #FFFFFF;" :key="item.reg_id">
                            <td>{{ item.no_antrian }}</td>
                            <td>{{ item.tgl_bayar }}</td>
                            <td>{{ item.no_rm }}</td>
                            <td>{{ item.nama_pasien }}</td>
                            <td>{{ item.dokter_nama }}</td>
                            <td>{{ item.jns_penjamin }}</td>
                            <td>{{formatCurrency(item.total_akhir) }}</td>
                        </tr>
                        <!-- END Data table.If data not exists, will show EmptyState below -->

                        <!-- START Empty state -->
                        <tr v-else>
                            <td :colspan="columns.length" style="text-align: center; background-color: #fff; padding: 1em;">
                                <img src="../../../../../../Assets/icon-data-not-found.png" width="185" />
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
    import "../../../../Dashboard/tabs/dashboardStyle.css";
    import FullPagination from '@/Pages/PraktekDokter/components/BaseTablePraktek/FullPagination.vue';
    import SearchList from '@/Components/SearchList.vue';

        export default {
            name: 'transaksi',
            
            components : {
                FullPagination,
                SearchList,
            },

        data() {
            return {
                columns: ['No.Antrian', 'Tgl.Bayar', 'No.RM', 'Nama Pasien', 'Dokter', 'Penjamin', 'Total'],
                pagination : { current_page :0,per_page:10,total_data:0,num_pages:0, },
                isLoading : true,
                dokLists:[],
                configSelect : {
                    required : false,
                    compClass : "uk-width-1-1@m",
                    compStyle : "padding:0;margin:0px;color: black;height: 40px;vertical-align: middle;display: inline-block;width: 240px;border-radius: 5px;",
                },
                dokterDesc: [
                    { colName : 'dokter_nama', labelData : '', isTitle : true },
                    { colName : 'dokter_id', labelData : '', isTitle : false },
                ],
            }
        },

        computed: {
            ...mapGetters(['errors']),
            ...mapGetters('praktekDokter',['transaksiDashboardLists','filterDataTransaksiDashboard']),
        },

        mounted(){
            this.initialize();
        },

        methods : {
            ...mapActions('praktekDokter',['listTransaksiDashboard','listDokter']),
            ...mapMutations(['CLR_ERRORS']),

                initialize(){
                    this.submitFilter();
                    this.getDoctorList();
                },

                formatCurrency(value) {
                    let val = (value/1).toFixed(2).replace('.', ',')
                    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                },

                getDoctorList(){
                    if(!this.doctorLists || this.doctorLists.length < 1) {
                        this.listDokter().then((response) => {
                            if (response.success == true) { this.dokLists = response.data.data; }
                        });
                    }
                    else { this.dokLists = this.doctorLists.data; }
                },  


                //* START PAGINATION *//
                submitFilter() {
                    this.isLoading = true;
                    this.filterDataTransaksiDashboard.page = this.pagination.current_page;
                    this.filterDataTransaksiDashboard.per_page = this.pagination.per_page;

                this.listTransaksiDashboard(this.filterDataTransaksiDashboard).then((response) => {
                if (response.success == true) {
                    this.transaksiDashboardLists = response.data;
                    this.pagination.per_page = response.data.per_page;
                    this.pagination.total_data = response.data.total;
                    this.pagination.current_page = response.data.current_page;
                    this.pagination.last_page = response.data.last_page;
                    this.pagination.num_pages = response.data.last_page;
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

            sortBy(sortKey) {
                this.reverse = this.sortKey === sortKey ? !this.reverse : false;
                this.sortKey = sortKey;
            },
            
            dokterSelected(data) {
                this.filterDataTransaksiDashboard.dokter = null;
                if(data) {
                    this.filterDataTransaksiDashboard.dokter = data.dokter_id;
                }
                this.pagination.current_page = 1;
                this.submitFilter();
            }
        },
    }
</script>
<style>
/* ukuran ipad pro */
@media screen and (max-width: 1024px) {
    .table-th-text {
        font-size: var(--font-sm-12)!important;
        font-weight: var(--bold);
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