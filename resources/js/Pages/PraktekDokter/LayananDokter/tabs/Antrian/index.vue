
<template>
    <div class="uk-container uk-container-large">
    <!-- <div class="uk-flex uk-width-1-1@m" style="gap:31px;margin-bottom:55px"> -->
        <!-- <div class="uk-flex uk-flex-middle uk-flex-center">
            <button class="offcanvas-button" type="button" uk-toggle="target: #offcanvas-flip" style="width:auto" >Buka Offcanvas</button>
        </div>
        <div id="offcanvas-flip" class="uk-flex uk-flex-column" uk-offcanvas="flip: true; overlay: true;esc-close:true;bg-close:false;mode: slide">
            <div class="uk-offcanvas-bar offcanvas-bg uk-padding-small uk-width-1-2">
                <div style="background:white;z-index:100;position:sticky;top:0;width:auto;right:0" class="uk-width-1-1" >    
                    <div style="top: 0;">
                        <div style="border-bottom: solid #D4D4D4 1px;width:auto;">
                            <div class="uk-offcanvas-close">
                                <font-awesome-icon :icon="['fas', 'xmark']" size="xl" style="color: #d4d4d4;" />
                            </div>
                            <h3 style="color: black;" class="offcanvas-title uk-margin-remove-bottom"> {{ canvasTitle }}</h3>
                        </div>

                        <div class="offcanvas-content-header uk-margin-small uk-flex" style="color: black;">
                            <div class="uk-width-1-1 uk-padding-small uk-align-left"> 
                                
                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">No. RM : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">00000002</p>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">Nama Pasien : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">TN. UTSMAN BIN AFFAN (L)</p>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">Tgl Lahir : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">20-09-1999 (24 th 0 bln 1 hr)</p>
                                </div>
                                    
                            </div>
                            <div class="uk-width-1-1 uk-padding-small uk-align-right"> 
                                
                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">No.Registrasi : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">20230321-REG00001 (INT001)</p>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">Nama Layanan : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">KLINIK PENYAKIT DALAM</p>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">Nama Dokter : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">DR. MUHAMMAD AL KAHFI SP. PD</p>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="margin-top:10px">
                        <button
                            v-for="letter in letterOfButtons"
                            :key="letter"
                            @click="scrollToCard(letter)"
                            :class="{'offcanvas-button-active': activeLetter === letter, 'offcanvas-button': activeLetter !== letter }"
                        >
                            {{ letter }}
                        </button>
                    </div>
                </div>

                

            </div>      
            <div style="margin-top:365px" class="uk-offcanvas-bar uk-offcanvas-bar-animation uk-offcanvas-slide uk-width-1-2 offcanvas-bg uk-padding-small">
                <div 
                    v-for="letter in letterOfButtons"
                    :key="letter"
                    class="uk-card uk-card-default uk-card-body offcanvas-content-subject offcanvas-content-body"
                    :id="'card-' + letter"
                >
                    Form {{ letter }}
                </div>
            </div>
            
        </div> -->

        <div>
            <form action="" @submit.prevent="submitFilter">
                
                <div class="uk-grid uk-child-width-1-6 uk-grid-small uk-text-small">                
                    <div>
                        <label for="searchFilter">Pencarian</label>
                        <input class="uk-input uk-border-rounded" type="text" v-model="antrianFilterTable.keyword" placeholder="Ketik Nama Pasien">
                    </div>
                    <div>
                        <label for="searchFilter">Tgl Periksa</label>
                        <input class="uk-input uk-border-rounded" type="date" v-model="antrianFilterTable.tgl_periksa" @change="submitFilter">
                    </div>
                    <div>
                        <label for="dateFilter">Status</label>
                        <select class="uk-select uk-border-rounded"  id="dateFilter" v-model="antrianFilterTable.status" @change="submitFilter">
                            <option value=""></option>
                            <option v-for="stat in statusAntrian" v-bind:value="stat.value" v-bind:key="stat.value">
                                {{ stat.text }}
                            </option>
                        </select>
                    </div>

                </div>
            </form>
                
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
                        <tr v-else-if="antrianLists.data && antrianLists.data.length > 0"  v-for="item in antrianLists.data" class="uk-card uk-card-default" style="border-radius: 7px; background-color: #FFFFFF;" :key="item.pasien_id">
                            <td>
                                {{ formatRegId(item.reg_id) }}
                                <span v-if="item.is_bayar" class="uk-label" style="font-size: 11px;">Bayar</span>
                            </td>
                            <td>{{ datetimeLocal(item.tgl_periksa) }}</td>
                            <td>{{ item.nama_pasien }}</td>
                            <td>{{ item.no_antrian }}</td>
                            <td>{{ item.dokter_nama }}</td>
                            <td>{{ item.jns_penjamin }}</td>
                            <td>                                
                                <div class="uk-inline">     
                                    <div :class="{ 'status-element': item.status.toUpperCase() === 'PERIKSA' }"
                                        :style="{ background: getStatusColor(item.status), opacity: getStatusOpacity(item.status), color: 'white',  display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '5px', width: '70px', borderRadius: '10px', cursor: 'pointer' }"
                                        @click="handleStatusButtonClick(item.status)"
                                        >
                                        {{ item.status }}
                                    </div>
                                    <div v-if="item.status.toUpperCase() !== 'PERIKSA'" uk-dropdown>
                                        <ul class="uk-nav uk-dropdown-nav">
                                            <li class="uk-nav-header">Ubah Status</li>
                                            <template v-if="statusAntrian" v-for="st in statusAntrian" :key="st in statusAntrian">
                                                <li v-if="st.value !== item.status"><a href="#" class="uk-text-capitalize" @click.prevent="ubahStatus(item,st)">{{ st.text }}</a></li>
                                                <!-- <li v-if="st.value !== item.status && st.text.toUpperCase() !== 'ANTRI' && st.text.toUpperCase() !== 'BOOKING'">
                                                    <a href="#" class="uk-text-capitalize" @click.prevent="ubahStatus(item, st)">{{ st.text }}</a>
                                                </li> -->
                                            </template>
                                        </ul>
                                    </div>
                                    <!-- && !item.status.toUpperCase() !== 'ANTRI' -->
                                    <!-- <div v-if="item.status.toUpperCase() !== 'BOOKING'" uk-dropdown>
                                        <ul class="uk-nav uk-dropdown-nav">
                                            <li class="uk-nav-header">Ubah Status</li>
                                            <template v-if="statusAntrian" v-for="st in statusAntrian" :key="st in statusAntrian">
                                                <li v-if="st.value !== item.status"><a href="#" class="uk-text-capitalize" @click.prevent="ubahStatus(item,st)">{{ st.text && !item.status.toUpperCase() !== 'ANTRI' }}</a></li>
                                            </template>
                                        </ul>
                                    </div> -->
                                </div>
                            </td>
                            
                            <td>
                                <span style="margin: 0 1em;color:#ABA7A7;">
                                    <div class="uk-inline">
                                        <button uk-icon="more-vertical" type="button" tooltip="pilih tindakan"></button>
                                        <div uk-dropdown="pos: bottom-right;animation: slide-top; animate-out: true; duration: 700;" style="border-radius:10px">
                                            <ul class="uk-nav uk-dropdown-nav">
                                                <!-- <li><a @click.prevent="pembayaran(item)" style="color:#808080" uk-toggle1="target: #offcanvas-flip">Pembayaran</a></li> -->

                                                <!-- Memunculkan Popup "Pembayaran sudah dilakukan" pada Status Antri dan Booking -->
                                                <li v-if="(item.status.toUpperCase() === 'ANTRI' || item.status.toUpperCase() === 'BOOKING')  && !item.is_bayar">
                                                    <a @click.prevent="handlePembayaranButtonClick(item.status)" style="color:#808080" uk-toggle1="target: #offcanvas-flip">Pembayaran</a>
                                                </li>
                                                <li v-else-if="!item.is_bayar">
                                                    <a @click.prevent="pembayaran(item)" style="color:#808080" uk-toggle1="target: #offcanvas-flip">Pembayaran</a>
                                                </li>
                                                <li v-if="item.status.toUpperCase() !== 'BOOKING' && item.status.toUpperCase() !== 'ANTRI' "><a @click.prevent="bukaCatatanMedis(item)" style="color:#808080" uk-toggle1="target: #offcanvas-flip" uk-offcanvas1="flip: true; overlay: true;esc-close:true;bg-close:false;mode: slide">Catatan Medis</a></li>
                                                <li v-if="item.status.toUpperCase() !== 'BOOKING' && item.status.toUpperCase() !== 'ANTRI' "><a @click.prevent="printSuratPasien('suratSehat',item)" style="color:#808080" uk-toggle1="target: #offcanvas-flip" uk-offcanvas1="flip: true; overlay: true;esc-close:true;bg-close:false;mode: slide">Cetak Surat Sehat</a></li>
                                                <li v-if="item.status.toUpperCase() !== 'BOOKING' && item.status.toUpperCase() !== 'ANTRI' "><a @click.prevent="printSuratPasien('suratSakit',item)" style="color:#808080" uk-toggle1="target: #offcanvas-flip" uk-offcanvas1="flip: true; overlay: true;esc-close:true;bg-close:false;mode: slide">Cetak Surat Sakit</a></li>
                                                <li><a @click.prevent="printBuktiPendaftaran(item)" style="color:#808080" uk-toggle1="target: #offcanvas-flip">Cetak Bukti Daftar</a></li>
                                                <li class="uk-nav-divider"></li>
                                                <li><a href="#" v-if=(!item.is_bayar) style="color:#F1595F" @click.prevent="ubahAntrian(item)">Ubah Antrian</a></li>
                                                <li><a href="#" style="color:#F1595F" @click.prevent="batalAntrian(item)">Batalkan</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </span>
                            </td>
                        </tr>
                        <!-- END Data table.If data not exists, will show EmptyState below -->

                        <!-- START Empty state -->
                        <tr v-else>
                            <td :colspan="columns.length" style="text-align: center; background-color: #fff; padding: 1em;top: 0;flex: 1 0 calc(33.333% - 31px);">
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
                        style="flex: 1 0 calc(100% - 31px);"
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
        <modal-pembayaran ref="modalPembayaran" v-on:saveSucceed="submitFilter"></modal-pembayaran>
        <modal-surat-sakit ref="modalSuratSakit" v-on:saveSucceed="submitFilter"></modal-surat-sakit>
        <modal-surat-sehat ref="modalSuratSehat" v-on:saveSucceed="submitFilter"></modal-surat-sehat>
        <form-registrasi ref="formRegistrasi" v-on:saveSucceed="submitFilter"></form-registrasi>
        <!-- <canvas-medrec ref="offCanvasMR"></canvas-medrec> -->
        <offCanvas ref="offCanvasMedrec"
            isCanvasTitle="Elektronik Rekam Medis"
            canvasTitle="RIWAYAT ELEKTRONIK REKAM MEDIS"
        ></offCanvas>

        <!-- popup batal antrian.
            ~ props subtitleAlert sudah otomatis manggil username yg login,
            karena sudah di declare pada komponen AlertInput.vue
        -->
        <div class="uk-margin-top">
            <AlertInput
            ref="alertInput"
            :showComponentAlertInput="showAlertInput"
            :showInput="showInput"
            titleAlert="Pembatalan"
            subtitleAlert="Pembatalan data antrian oleh" 
            subjectAlert="Alasan Pembatalan"
            confirm="Lanjutkan"
            cancel="Tutup"
            @tutup-alert="showAlertInput = false"
            v-on:confirm = submitBatal
            />
        </div>

         <!-- popup alert warning untuk kondisi Belum Bayar di Status Periksa. -->
         <div class="uk-margin-top">
            <AlertSuccess
                ref="alertBayar"
                :showComponentAlertSuccess="showAlertBayar"
                titleSuccess="Warning"
                subtitleSuccess="Pemeriksaan sudah dibayar. Batalkan terlebih dahulu pembayaran untuk menghapus data."
                confirmSuccess="Tutup"
                :iconFailed="iconFailed"
                @tutup-alert="showAlertBayar = false"
                style="text-align: center;"
            />
        </div>


        <!-- popup sukses batal antrian.
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

        <!-- popup alert warning untuk kondisi Status Periksa. -->
        <div class="uk-margin-top">
            <AlertSuccess
                ref="alertSuccessStatusPeriksa"
                :showComponentAlertSuccess="showAlertSuccessStatusPeriksa"
                titleSuccess="Warning"
                subtitleSuccess="Status Periksa tidak dapat kembali ke Status Booking maupun Status Antrian"
                confirmSuccess="Tutup"
                :iconFailed="iconFailed"
                @tutup-alert="showAlertSuccessStatusPeriksa = false"
                style="text-align: center;"
            />
        </div>

        <!-- popup alert warning dgn kondisi tidak bisa melakukan pembayaran jika bukan Status Periksa. -->
        <div class="uk-margin-top">
            <AlertSuccess
                ref="alertPembayaran"
                :showComponentAlertSuccess="showAlertPembayaran"
                titleSuccess="Warning"
                subtitleSuccess="Aksi Pembayaran hanya bisa dilakukan pada Status Periksa"
                confirmSuccess="Tutup"
                :iconFailed="iconFailed"
                @tutup-alert="showAlertPembayaran = false"
                style="text-align:center"
            />
        </div>

        <!-- popup alert untuk kondisi sudah bayar. -->
        <div class="uk-margin-top">
            <AlertSuccess
                ref="alertSuccessBayar"
                :showComponentAlertSuccess="showAlertSuccessBayar"
                titleSuccess="Warning"
                subtitleSuccess="Pembayaran sudah dilakukan."
                confirmSuccess="Tutup"
                :iconFailed="iconFailed"
                @tutup-alert="showAlertSuccessBayar = false"
            />
        </div>

        <!-- <offCanvas ref="offCanvasMedrec"
                isCanvasTitle="Elektronik Rekam Medis"
                canvasTitle="RIWAYAT ELEKTRONIK REKAM MEDIS"
            /> -->

    </div>

</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import ModalPembayaran from '@/Pages/PraktekDokter/LayananDokter/components/ModalPembayaran.vue';
import ModalSuratSakit from '@/Pages/PraktekDokter/LayananDokter/components/ModalSuratSakit.vue';
import ModalSuratSehat from '@/Pages/PraktekDokter/LayananDokter/components/ModalSuratSehat.vue';
import AlertInput from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertInput.vue';
import AlertSuccess from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertSuccess.vue';
import FormRegistrasi from '@/Pages/PraktekDokter/LayananDokter/tabs/MasterPasien/FormRegistrasi/index.vue';
import offCanvas from '@/Pages/PraktekDokter/LayananDokter/components/offCanvas.vue';
import FullPagination from '@/Pages/PraktekDokter/components/BaseTablePraktek/FullPagination.vue';

// import CanvasMedrec from './offCanvas.vue';

export default {
    name: "antrian-poli",
    // props: {
    //     canvasTitle: String,
    // },
    components : {
        FullPagination,
        ModalPembayaran,
        ModalSuratSakit,
        ModalSuratSehat,
        AlertInput,
        AlertSuccess,
        FormRegistrasi,
        offCanvas,
        // CanvasMedrec,
    },

    mounted() {
        // this.chartIncome();
        this.initialize();
    },
    data() {
        return {
            // antrianLists: [],
            pagination : { current_page :0,per_page:10,total_data:0,num_pages:0, },
            columns: ['No.Reg','Tgl.Periksa','Nama Pasien', 'No.Antrian', 'Dokter', 'Penjamin','Status'],
            letterOfButtons: ['Tanda Vital', 'SOAP', 'Diagnosa', 'Tindakan', 'Resep'],
            activeLetter: null,
            isLoading : true,
            showAlertSuccess : false,
            showAlertInput : false,
            showAlertSuccessStatusPeriksa : false,
            showAlertPembayaran : false,
            showAlertSuccessBayar : false, // kondisi bayar hanya bisa dilakukan di status periksa saja
            showAlertBayar : false, // kondisi untuk Sudah Bayar di Status Periksa
            showInput:true,
            iconSuccess:true,
            iconFailed:true,
            selectedAntrianId : null,
            param:[],
        };
     },
     
     computed : {
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['patientLists','antrianFilterTable','antrianLists','statusAntrian','soapData']),
        ...mapGetters(['user','clientId']), 


        // subtitleSuccessPeriksa() {
        //     if (this.clickedStatus && this.clickedStatus.toUpperCase() === 'PERIKSA') {
        //         return "Status Periksa tidak dapat kembali ke status Antrian maupun Status Booking";
        //     }
        //     return "";
        // },

        // subtitleAlert() {
        //     return this.user ? `Pembatalan data antrian oleh ${this.user.username}` : '';
        // },
        subtitleSuccess() {
            return this.user ? `Data antrian sukses dihapus oleh ${this.user.username} ` : '';
        }
     },


    // created() {
    //     this.activeCard = this.letterOfButtons['2'];
    // },
    methods: {
        ...mapActions('pasien',['listPasien','deletePasien','dataPasien']),
        ...mapActions('praktekDokter',['listAntrian','deleteAntrian','updateStatusAntrian','cetakPendaftaran','cetakSuratPasien']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.antrianFilterTable.tgl_periksa = this.getTodayDate();
            //this.$refs.formRegistrasi.closeModalReg();
            //this.$refs.offCanvasMR.closeCanvas();
            this.submitFilter();
        },

        getStatusColor(status) {
            switch (status) {
                case 'BOOKING':
                    return 'linear-gradient(180deg, #048A42 -50%, #50E680 182.35%)';
                case 'ANTRI':
                    return 'linear-gradient(180deg, #FF3408 -50%, #FFEA5F 182.35%)';
                case 'PERIKSA':
                    return 'linear-gradient(180deg, #3F24F7 -50%, #8E7FFE 182.35%)';
                default:
                    return '';
            }
        },

        getStatusOpacity(status) {
            switch (status) {
                case 'BOOKING':
                    return '1';
                case 'ANTRI':
                    return '1';
                case 'PERIKSA':
                    return '0.65';
                default:
                    return '';
            }
        },

        handleStatusButtonClick(status) {
            if (status.toUpperCase() === 'PERIKSA') {
                this.showAlertSuccessStatusPeriksa = true;
            }
        },

        formatRegId(data) {
            let dt = data.split("-");
            if(dt[2] !== null) {
                return dt[2];
            }
            else {
                return data;
            }
        },

        
        handlePembayaranButtonClick(status) {
            if (status.toUpperCase() === 'ANTRI' || status.toUpperCase() === 'BOOKING') {
                this.showAlertPembayaran = true;
            }
        },

        pembayaran(data){
            if(data) {
                this.$refs.modalPembayaran.newPayment(data);
                if(data.is_bayar) {
                    this.showAlertSuccessBayar = true;
                } 
            }
        },

        bukaCatatanMedis(data) {
            if(data) {
                this.$refs.offCanvasMedrec.showCanvas(data);
            }
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        },

        datetimeLocal(data) {
            // const dt = new Date(data);
            // //dt = dt.toLocaleDateString();
            // dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
            // return dt.toISOString().slice(0, 16);
            // let d = new Date(data);
            // let val = d.toLocaleDateString();
            // return val;
            let dt = new Date(data);
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate();
            if(date < 10) {date = `0${date}`}

            let hour = dt.getHours();
            if(hour < 10) {hour = `0${hour}`}

            let minute = dt.getMinutes();
            if(minute < 10) {minute = `0${minute}`}

            let str_dt = `${date}-${month}-${year} ${hour}:${minute}`;
            return str_dt;
        },

        /**
         * get date today for default value input date 
         */
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

        // submitFilter(){
        //     this.isLoading = true;
        //     this.listAntrian(this.antrianFilterTable).then((response) => {
        //         if (response.success == true) {
        //             console.log(this.antrianLists.data);
        //             this.antrianList = this.antrianLists.data;
        //         }
        //         this.isLoading = false;
        //     })
        // },

        //* START PAGINATION *//
        submitFilter() {
            this.isLoading = true;
            this.antrianFilterTable.page = this.pagination.current_page;
            this.antrianFilterTable.per_page = this.pagination.per_page;

            this.listAntrian(this.antrianFilterTable).then((response) => {
                if (response.success == true) {
                    this.antrianLists = response.data.data;
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

        submitBatal(data){
            let dt = {
                reg_id : this.selectedAntrianId,
                alasan_batal : data,
            }
            this.CLR_ERRORS();
            this.deleteAntrian(dt).then((response) => {
                if (response.success == true) {
                    this.showAlertInput = false;
                    this.showAlertSuccess = true;
                    this.submitFilter();
                }
                else { alert (response.message) }
            });
        },
       
        ubahStatus(data,status){
            if(status) {
                if(status.text.toUpperCase() == 'BATAL') {
                    let dt = JSON.parse(JSON.stringify(data));
                    this.batalAntrian(dt);
                }
                else {
                    let dt = JSON.parse(JSON.stringify(data));
                    dt.status = status.value;
                    this.updateStatus(dt);
                }
            }
        },

       

        updateStatus(data){
            this.updateStatusAntrian(data).then((response) => {
                if (response.success == true) {
                    this.submitFilter();   
                }
                else {
                    alert(response.message);
                }
            })
        },



        batalAntrian(data) {
            this.selectedAntrianId = data.reg_id;
            // jika sudah bayar maka menampilkan Popup Warning
            if(!data.is_bayar) {
                this.showAlertInput = true;

            } 
            // jika belum bayar maka akan menampilkan alert input
            else if (data.is_bayar) {
                this.showAlertInput = false;
                this.showAlertBayar = true;
            }
        },

        ubahAntrian(data) {
            this.$refs.formRegistrasi.editRegistrasi(data);
        },


        // showCard(letter) {
        //     this.activeCard = letter;
        // },

        scrollToCard(letter) {
             // Buat ID yang sesuai dengan card yang ingin di-scroll
            const targetID = 'card-' + letter;

            // Cari elemen card yang sesuai
            const targetElement = document.getElementById(targetID);

            // Gulir ke elemen card
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }

            this.activeLetter = letter;

        },

        printBuktiPendaftaran(data){
            this.cetakPendaftaran(data).then((response) => {
                var newNode = document.createElement('p');
                newNode.appendChild(document.createTextNode('html string'));
                this.printDiv(response,"3.4in 4.5in");
            });
        },

        printSuratPasien(jnsSurat,data){
            this.params = [
                {
                    'register': data.reg_id,
                    'jnsSurat': jnsSurat,
                }
            ];
            if(jnsSurat == "suratSehat"){
                if(data) {
                    this.$refs.modalSuratSehat.createSurat(data);
                }
                // this.cetakSuratPasien(this.params).then((response) => {
                //     var newNode = document.createElement('p');
                //     newNode.appendChild(document.createTextNode('html string'));
                //     this.printDiv(response,"a4");
                // });
            } else {
                if(data) {
                    this.$refs.modalSuratSakit.createSurat(data);
                }
            }
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
    },
}
</script>
<style>
/* ipad mini */
/* @media  screen and (max-width: 780px) {
    /* .uk-width-auto\@m {
        width: auto;
    } */
    /* .pagination-flex {
        flex: 1 0 calc(100% - 31px);
    } 
} */

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
.status-element {
    transition: opacity 0.3s ease-in-out;  /* smooth effect */
    cursor: pointer; 
}

.status-element:hover {
    opacity: 1!important;
}

.uk-offcanvas-overlay::before {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,.1);
    opacity: 0;
    transition: opacity .15s linear;
}
.offcanvas-button-active {
    width: 92px;
    height: 45px;
    flex-shrink: 0;
    border-radius: 5px;
    background: #1E87F0;
    color:white;
    text-align: center;
    align-items: flex-start;
    cursor: default;
    margin: 0em;
    padding-block: 1px;
    padding-inline: 6px;
    border-style: none;
    margin:0 6px;
    /* padding: 12px 18px; */
}
.offcanvas-button {
    width: 92px;
    height: 45px;
    flex-shrink: 0;
    border-radius: 5px;
    background: white;
    color:#1E87F0;
    border: 0.8px solid #1E87F0;
    text-align: center;
    align-items: flex-start;
    cursor: default;
    margin: 0em;
    padding-block: 1px;
    padding-inline: 6px;
    margin:0 6px;
    /* padding: 12px 18px; */
}
.offcanvas-button:hover {
  background: #AFDFFF;
  color: #000000;
}

.uk-offcanvas-close {
    top: 0;
    right:0;
    cursor: pointer;
}
.offcanvas-bg {
    background: white;
}
.offcanvas-title {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-lg-26);
    font-weight: var(--semibold);
    letter-spacing: 0.1px;
    line-height: 34px;
}
.offcanvas-content-header {
    width: auto;
    height: 230px;
    flex-shrink: 0;
    background: #E7F9FD;
    border: 1px solid rgba(5, 210, 255, 1);
    border-radius: 3px;
}

.offcanvas-content-body {
    margin-bottom:30px;
    height:300px;
    border:1px solid #BFBDBD;
    border-radius:10px;
}

.offcanvas-content-title {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-md-14);
    font-weight: var(--regular);
    line-height: 20px;
}
.offcanvas-content-subject {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-md-14);
    font-weight: var(--bold);
    line-height: 18px;
}
.uk-width-1-2 {
    width: calc(100% * 1 / 2.589);
}

</style>
