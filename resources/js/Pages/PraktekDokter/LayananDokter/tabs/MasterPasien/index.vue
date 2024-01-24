<template>
    <div class="uk-container uk-container-large">
        <!-- <header-page title="MASTER PASIEN" subTitle="master data pasien"></header-page> -->
        <div class="uk-grid uk-child-width-1-2 uk-grid-small uk-text-small">
            <form action="" class="uk-search uk-search-default" @submit.prevent="submitFilter">
                <div class="uk-width-2-3@m uk-flex uk-flex-column uk-inline">
                    <label for="searchFilter">Pencarian</label>
                    <span class="uk-search-icon-flip uk-inline" style="margin-top:20px" uk-search-icon></span>
                    <input 
                        class="uk-input uk-border-rounded" 
                        type="text" 
                        v-model="patientFilter.keyword" 
                        placeholder="Ketik no. RM / nama pasien" 
                        id="searchFilter"  
                        @change="submitFilter"
                        style="width:auto"
                        aria-label="Search"
                    >

                </div>
            </form>
            <div class="uk-flex uk-flex-right">
                <button class="uk-button uk-button-primary uk-margin-top" title="Tambah Data Baru" style="border-radius: 7px;background:#427BBF" @click.prevent="tambahPasien">
                    <font-awesome-icon :icon="['fas', 'plus']" style="color: #ffffff;" /> Pasien Baru
                </button>
            </div>
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
                    <tr v-else-if="patientLists.data && patientLists.data.length > 0" v-for="item in patientLists.data" class="uk-card uk-card-default" style="border-radius: 7px; background-color: #FFFFFF;" :key="item.pasien_id" >
                        <!-- <td>{{ item.pasien_id }}</td> -->
                        <td>{{ item.no_rm }}</td>
                        <td>{{ item.nama_lengkap }}</td>
                        <td>{{ item.jns_kelamin }}</td>
                        <td>{{ item.tempat_lahir }}</td>
                        <td>{{ item.tgl_lahir }}</td>
                        <td>
                            <span style="margin: 0 1em;color:#ABA7A7;">
                                <div class="uk-inline">
                                    <button uk-icon="more-vertical" type="button" tooltip="pilih tindakan"></button>
                                    <div uk-dropdown="pos: bottom-right;animation: slide-top; animate-out: true; duration: 700;" style="border-radius:10px">
                                        <ul class="uk-nav uk-dropdown-nav">
                                            <li>
                                                <a @click.prevent="daftarAntrian(item)" style="color:#808080">
                                                    <span class="uk-margin-small-right"><font-awesome-icon :icon="['fas', 'circle-plus']" size="lg" style="color: #808080;"/></span>
                                                    Daftar Antrian
                                                </a>
                                            </li>
                                            <li 
                                                class="uk-nav-divider"                                            >
                                            </li>
                                            <li>
                                                <a href="#" style="color:#808080" @click.prevent="ubahPasien(item)">
                                                    <span class="uk-margin-small-right"><font-awesome-icon :icon="['fas', 'user-pen']" size="lg" style="color: #808080;"/></span>
                                                    Ubah Pasien
                                                </a>
                                            </li>
                                            <li 
                                                class="uk-nav-divider"                                            >
                                            </li>
                                            <li>
                                                <a href="#" style="color:#F1595F" @click.prevent="confirmHapusPasien(item)">
                                                    <span class="uk-margin-small-right"><font-awesome-icon :icon="['fas', 'user-xmark']" size="lg" style="color: #F1595F;"/></span>
                                                    Hapus Pasien
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

    <form-registrasi 
        ref="formRegistrasi"
        v-on:modalClosed="submitFilter"
        :isModalOpen="isModalOpen"
    ></form-registrasi>

    <!-- Alert hapus pasien -->
    <div class="uk-margin-top">
        <AlertInput
            class="uk-text-center uk-padding-remove-bottom"
            ref="alertInput"
            :showComponentAlertInput="showAlertInput"
            titleAlert="Hapus Data"
            :subtitleAlert="`Proses ini akan menonaktifkan data pasien '${selectedItem ? selectedItem.nama_lengkap : ''}'. Anda yakin?`"
            confirm="Lanjutkan"
            cancel="Tutup"
            @tutup-alert="showAlertInput = false"
            v-on:confirm = "hapusPasien"
        />
    </div>

    <div class="uk-margin-top">
        <AlertSuccess
            class="uk-text-center uk-padding-remove-bottom"
            ref="alertSuccessHapus"
            :showComponentAlertSuccess="showAlertSuccessHapus"
            titleSuccess="Sukses"
            :subtitleSuccess="`Berhasil menonaktifkan data pasien '${selectedItem ? selectedItem.nama_lengkap : ''}'.`"
            confirmSuccess="Tutup"
            :iconSuccess="iconSuccess"
            @tutup-alert="showAlertSuccessHapus = false"
        />
    </div>




    <form-pasien-praktek ref="formPasien" v-on:formClosed="submitFilter"></form-pasien-praktek>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import FormRegistrasi from '@/Pages/PraktekDokter/LayananDokter/tabs/MasterPasien/FormRegistrasi/index.vue';
    import FormPasienPraktek from '@/Pages/PraktekDokter/LayananDokter/tabs/MasterPasien/FormPasienPraktek/index.vue';
    import '../../../Dashboard/tabs/dashboardStyle.css';
    import FullPagination from '@/Pages/PraktekDokter/components/BaseTablePraktek/FullPagination.vue';
    import AlertInput from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertInput.vue';
    import AlertSuccess from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertSuccess.vue';

    export default {
        name : 'master-pasien',
        components : {
            headerPage,
            FormRegistrasi,
            FormPasienPraktek,
            FullPagination,
            AlertInput,
            AlertSuccess,
        },
        data() {
            return {
                pagination : { current_page :0,per_page:10,total_data:0,num_pages:0, },
                sortKey : null,
                isLoading : true,
                columns: ['No.RM','Nama Pasien', 'Gender', 'Tempat Lahir', 'Tanggal Lahir'],
                // pasienList : [],
                dokterList : null,
                isModalOpen : false,
                showAlertInput : false,
                showAlertSuccessHapus : false,
                iconSuccess : true,
                selectedItem: null,
            }
        },

        computed: {
        ...mapGetters(['errors']),
        ...mapGetters('pasien',['patientLists','patientFilter']),
        ...mapGetters('dokter',['doctorLists']),
        },

        mounted() {
        this.initialize();
        },

        methods : {
            ...mapActions('pasien',['listPasien','deletePasien','dataPasien']),
            ...mapActions('dokter',['listDokter']),
            ...mapMutations(['CLR_ERRORS']),

            initialize(){
                this.submitFilter();
                // this.retrieveDokter();
            },
            //* START PAGINATION *//
            submitFilter() {
                this.isLoading = true;
                this.patientFilter.page = this.pagination.current_page;
                this.patientFilter.per_page = this.pagination.per_page;

                this.listPasien(this.patientFilter).then((response) => {
                    if (response.success == true) {
                        this.pasienList = response.data.data;
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

            retrieveDokter(){
                let params = {
                    is_aktif : true,
                    // per_page : 'ALL',
                };
                this.listDokter(params).then((response) => {
                    if (response.success == true) {
                        // console.log(this.doctorLists.data);
                        this.dokterList = this.doctorLists.data;
                    }
                    this.isLoading = false;
                })
            },

            tambahPasien() {
                this.$refs.formPasien.newPasien();
            },

            ubahPasien(data) {
                if(data) {
                    this.$refs.formPasien.ubahDataPasien(data);
                }
            },

            confirmHapusPasien(item) {
                if(item) {
                    this.selectedItem = item;
                    this.showAlertInput = true;
                }
            },

            hapusPasien() {
                this.CLR_ERRORS();

                if (this.selectedItem) {
                    this.deletePasien(this.selectedItem.pasien_id).then((response) => {
                        if (response.success) {
                            this.showAlertSuccessHapus = true;
                            this.submitFilter();
                        } else {
                            alert(response.message);
                        }
                        this.showAlertInput = false;        
                    });
                };
            },

            daftarAntrian(data) {
                if(data) {
                    this.$refs.formRegistrasi.newRegistrasi(data);
                }
            }

        }
    }
</script>
<style>


.uk-offcanvas-bar-animation {
  transition: transform 0.3s ease-out;
}

.empty-state-icon {
    width:175px;
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
</style>
