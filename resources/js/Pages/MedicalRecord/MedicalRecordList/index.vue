<template>
    <div class="uk-container uk-container-large">
        <div style="margin:0;padding:1em;">
            <div class="uk-grid uk-grid-small">
                <div class="uk-width-auto">
                    <router-link :to = "{ name:'listBilling' }" style="background-color:white;padding:0.3em;display:inline-block;">
                        <span uk-icon="icon:chevron-left;ratio:1"></span>
                    </router-link>
                </div>
                <div class="uk-width-expand">
                    <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">REKAM MEDIS PASIEN</h4>
                </div>
            </div>
            
                    
            <div class="uk-grid-small" uk-grid style="padding:0 0.5em 2em 0.5em;margin:1em 0 0 0;background-color: #fff;">
                <div class="uk-width-1-1 uk-margin-small" style="background-color:#ffff;padding:1em 0.5em 1em 0.5em;border-bottom:1px solid #ccc;">
                    <div style="background-color:#fff;padding:0;margin-top:0;">
                        <p style="margin:0;padding:0;color:#000;font-size: 10px;font-weight: 500;">{{ pasienId }}</p>
                        <h5 style="margin:0;padding:0;color:#000;font-weight: bold;">{{ pasien.nama_pasien }} ( {{ pasien.no_rm }} )</h5>
                        <p style="margin:0;padding:0;color:#000;font-size: 12px;">{{ pasien.penjamin_nama }}</p>
                    </div>
                </div>

                <div class="uk-width-1-4@m uk-margin-small" style="background-color:#fff;padding:0.5em;">
                    <div v-if="medrecLists" v-for="dt in medrecLists">
                        <a href="#" @click.prevent="retrieveData(dt)" class="uk-card-hover" uk-card style="text-decoration: none; display:block; padding:1em;border-radius:5px;border-bottom:1px solid #eee;cursor:pointer;">
                            <p style="padding:0;margin:0;font-size:10px;color:#414141;">{{ dt.tgl_registrasi }}
                                <span class="uk-label" style="margin:0;padding:0 0.5em 0 0.5em;font-size: 10px;border-radius:25px;color:#fcfcfc;">{{ dt.status_reg }}</span>  
                            </p>
                            <p style="padding:0;margin:0;font-size:10px;color:#313131;">{{ dt.reg_id }}</p>
                            <h1 style="padding:0;margin:5px 0 0 0;font-size:14px;font-weight: 500;color:#000;">{{ dt.dokter_nama }}</h1>
                            <p style="padding:0;margin:0;font-size:12px;color:#313131;">{{ dt.unit_nama }}</p>
                        </a>
                    </div>
                </div>

                <div class="uk-width-expand" style="background-color:#f5f5f7;padding:0;">
                    <div>
                        <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                            <div uk-spinner="ratio : 2"></div>
                            <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                        </div>
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>                        
                    </div>
                    <div v-if="dataMedrec" style="padding:1em 0 1em 0; background-color:#fff;">
                        <div class="hims-form-container" style="padding:0; margin:0;"> 
                            <div class="uk-grid-small hims-form-subpage" uk-grid style="padding:0;margin:0;border:none;background-color:#fff;">
                                <div class="uk-width-1-1@m">
                                    <p style="font-size:10px;padding:0;margin:0;font-style:italic;">{{ dataMedrec.reg_id }} {{ dataMedrec.tgl_registrasi }}</p>
                                    <p style="color:black;padding:0;margin:0;font-weight: 500;">{{ dataMedrec.dokter_nama }}</p>
                                    <p style="color:black;padding:0;margin:0;font-weight: 400;font-size:12px;">{{ dataMedrec.unit_nama }}</p>
                                    <!-- <button type="button" @click.prevent="RefreshPemeriksaan">Refresh Pemeriksaan</button> -->
                                </div>
                            </div> 
                        </div>
                    </div>

                    <div style="padding:1em 0.5em 1em 0.5em; background-color:#fff;">
                        <ul uk-tab>
                            <li><a href="#">Pemeriksaan</a></li>
                            <li><a href="#">Resep</a></li>
                            <li><a href="#">Penunjang</a></li>
                        </ul>

                        <ul class="uk-switcher uk-margin">
                            <li>
                                <list-pemeriksaan :data="dataMedrec"></list-pemeriksaan>
                                <!-- <table class="uk-table uk-table-striped1 child-table1" style="padding:0;margin:0;">
                                    <thead>
                                        <th class="tb-header-transaksi" style="width:20px;text-align:center;"></th>
                                        <th class="tb-header-transaksi" style="text-align:left;">ID</th>
                                        <th class="tb-header-transaksi" style="text-align:left;">ITEM</th>
                                        <th class="tb-header-transaksi" style="width:40px;text-align:right;">PASIEN</th>
                                        <th class="tb-header-transaksi" style="width:40px;text-align:right;">PENJAMIN</th>
                                        <th class="tb-header-transaksi" style="text-align:right;">Subtotal</th>
                                        <th class="tb-header-transaksi" style="width:40px;text-align:center;">
                                            <input type="checkbox" class="uk-checkbox" @change="checkAllRow" v-model="isCheckAllRow">
                                        </th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table> -->
                            </li>
                            <li>
                                <div style="padding:0;margin:0;">
                                    <table class="uk-table uk-table-striped child-table1" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-transaksi" style="width:20px;text-align:center;color:white;"></th>
                                            <th class="tb-header-transaksi" style="width:200px;text-align:left;color:white;">ID</th>
                                            <th class="tb-header-transaksi" style="width:120px;text-align:left;color:white;">Tanggal</th>
                                            <th class="tb-header-transaksi" style="text-align:left;color:white;">Dokter</th>
                                            <!-- <th class="tb-header-transaksi" style="width:80px;text-align:right;color:white;">Total</th>                                                 -->
                                            <th class="tb-header-transaksi" style="width:40px;text-align:center;color:white;">Status</th>
                                            <!-- <th class="tb-header-transaksi" style="width:120px;text-align:center;color:white;">...</th> -->
                                        </thead>
                                        <tbody>
                                            <row-sub-resep 
                                                v-if="dataMedrec && dataMedrec.resep" 
                                                v-for="dt in dataMedrec.resep"
                                                :rowData = "dt">
                                            </row-sub-resep>
                                        </tbody>
                                    </table>
                                </div>
                            </li>
                            <li>
                                <!-- <div style="padding:0;margin:0;">
                                    <table class="uk-table uk-table-striped child-table1" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-transaksi" style="width:20px;text-align:center;color:white;"></th>
                                            <th class="tb-header-transaksi" style="text-align:left;color:white;">ID</th>
                                            <th class="tb-header-transaksi" style="text-align:left;color:white;">Dokter</th>
                                            <th class="tb-header-transaksi" style="width:40px;text-align:center;color:white;">Status</th>
                                        </thead>
                                        <tbody>
                                            <row-sub-lab
                                                v-if="data && data.lab" 
                                                v-for="dt in data.lab"
                                                :rowData = "dt">
                                            </row-sub-lab>
                                        </tbody>
                                    </table>
                                </div> -->
                                <list-laboratorium :data="dataMedrec"></list-laboratorium>
                                <list-radiologi :data="dataMedrec"></list-radiologi>
                            </li>
                        </ul>
                    </div>
                    <!-- <div>
                        <table class="uk-table uk-table-striped1 child-table1" style="padding:0;margin:0;">
                            <thead>
                                <th class="tb-header-transaksi" style="text-align:left;">TIPE</th>
                                <th class="tb-header-transaksi" style="text-align:left;">TANGGAL</th>
                                <th class="tb-header-transaksi" style="text-align:right;">DOKTER</th>
                                <th class="tb-header-transaksi" style="text-align:right;">STATUS</th>
                            </thead>
                            <tbody>
                                <row-medrec
                                    v-if="medrecLists" 
                                    v-for="dt in medrecLists"
                                    :rowData = "dt"
                                    :linkCallback="updateRowData">
                                </row-medrec>
                                <tr v-else>
                                    <td colspan="7" style="font-style:italic;font-size:12px;color:black;">data tidak ditemukan</td>
                                </tr>
                            </tbody>
                        </table>
                    </div> -->
                    <!-- <div>
                        <ul uk-tab>
                            <li><a href="#">Transaksi</a></li>
                            <li><a href="#">Pembayaran</a></li>
                        </ul>

                        <ul class="uk-switcher uk-margin">
                            <li>
                                <table class="uk-table uk-table-striped1 child-table1" style="padding:0;margin:0;">
                                    <thead>
                                        <th class="tb-header-transaksi" style="width:20px;text-align:center;"></th>
                                        <th class="tb-header-transaksi" style="text-align:left;">ID</th>
                                        <th class="tb-header-transaksi" style="text-align:left;">ITEM</th>
                                        <th class="tb-header-transaksi" style="width:40px;text-align:right;">PASIEN</th>
                                        <th class="tb-header-transaksi" style="width:40px;text-align:right;">PENJAMIN</th>
                                        <th class="tb-header-transaksi" style="text-align:right;">Subtotal</th>
                                        <th class="tb-header-transaksi" style="width:40px;text-align:center;">
                                            <input type="checkbox" class="uk-checkbox" @change="checkAllRow" v-model="isCheckAllRow">
                                        </th>
                                    </thead>
                                    <tbody>
                                        <row-data-billing 
                                            v-if="billingData.transaksi" 
                                            v-for="dt in billingData.transaksi"
                                            :rowData = "dt"
                                            :linkCallback="updateRowData">
                                        </row-data-billing>
                                        <tr v-else>
                                            <td colspan="7" style="font-style:italic;font-size:12px;color:black;">data tidak ditemukan</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </li>
                            <li>
                                <table class="uk-table uk-table-striped child-table1" style="padding:0;margin:0;">
                                    <thead>
                                        <th class="tb-header-transaksi" style="text-align:left;">ID</th>
                                        <th class="tb-header-transaksi" style="text-align:left;">JENIS</th>
                                        <th class="tb-header-transaksi" style="text-align:right;">JML BAYAR</th>
                                        <th class="tb-header-transaksi" style="text-align:center;">...</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="pay in billingData.histori_bayar">
                                            <td>
                                                <p style="margin:0;padding:0;font-size: 12px;font-weight: 500;color:black;">{{ pay.tgl_bayar }}</p>
                                                <p style="margin:0;padding:0;font-size: 10px;font-weight: 400;">{{ pay.payment_id }}</p>
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0;font-size: 12px;font-weight: 500;color:black;">{{ pay.jns_payment }}</p>
                                                <p style="margin:0;padding:0;font-size: 10px;font-weight: 400;">{{ pay.penjamin_nama }} {{ pay.jenis_kartu }} {{ pay.mesin_edc }} {{ pay.no_kartu }}</p>
                                            </td>
                                            <td>
                                                <p style="margin:0;padding:0;font-size: 12px;font-weight: 500;text-align: right;color:black;">{{ formatCurrency(pay.jml_bayar) }}</p>
                                            </td>
                                            <td style="text-align:center;">
                                                <button style="margin-right:5px;"><span uk-icon="icon:print"></span>Cetak</button>
                                                <button type="button" @click.prevent="deletePaymentHistory(pay)"><span uk-icon="icon:trash"></span>Hapus</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </li>
                        </ul>
                    </div> -->
                </div>
                <!-- <div class="uk-width-1-3 uk-card-default uk-margin-small" style="background-color:#fcfcfc;padding:1em 2em 1em 1em;border-radius: 5px;"> -->
                    <!-- <h4 style="font-weight: 500;">Tagihan</h4>
                    <div class="uk-card-default" style="padding:1em;margin:0; border-top-right-radius:5px; border-top-left-radius:5px;">
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-expand">
                                <p style="margin:0;padding:0.3em 0 0 0;font-size: 11px; font-weight: bold;color:black;">TOTAL TAGIHAN</p>
                            </div>
                            <div class="uk-width-auto">
                                <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency(billingData.grand_total) }}
                                </p>
                            </div>
                            <div class="uk-width-auto">
                                <button type="button" style="border:none; background-color: #fff;"><span uk-icon="icon:info;ratio:0.8"></span></button>
                            </div>
                        </div>
                    </div>

                    <div class="uk-card-default" style="padding:1em;margin:0;">
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-expand">
                                <p style="margin:0;padding:0.3em 0 0 0;font-size: 11px; font-weight: bold;color:black;">TOTAL BAYAR</p>
                            </div>
                            <div class="uk-width-auto">
                                <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency(billingData.total_bayar) }}
                                </p>
                            </div>
                            <div class="uk-width-auto">
                                <button type="button" style="border:none; background-color: #fff;"><span uk-icon="icon:info;ratio:0.8"></span></button>
                            </div>
                        </div>
                    </div>

                    <div class="uk-card-default" style="padding:1em;margin:0; border-bottom-right-radius:5px; border-bottom-left-radius:5px;">
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-expand">
                                <p style="margin:0;padding:0.3em 0 0 0;font-size: 11px; font-weight: bold;color:black;">SISA TAGIHAN</p>
                            </div>
                            <div class="uk-width-auto">
                                <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency((billingData.grand_total - billingData.total_bayar)) }}
                                </p>
                            </div>
                            <div class="uk-width-auto">
                                <button type="button" style="border:none; background-color: #fff;"><span uk-icon="icon:info;ratio:0.8"></span></button>
                            </div>
                        </div>
                    </div>

                    <h4 style="font-weight: 500;padding:1em 0 0 0;margin:0;">Pembayaran</h4>
                    <div class="uk-margin-small">
                        <div v-if="billingData.pembayaran.length > 0" class="uk-card-default" v-for="pay in billingData.pembayaran" style="padding:1em;margin:5px 0 0 0; border-radius:5px;">
                            <div class="uk-grid-small" uk-grid>
                                <div class="uk-width-expand">
                                    <p style="margin:0;padding:0.3em 0 0 0;font-size: 11px; font-weight: bold;color:black;">{{ pay.jns_payment }}</p>
                                </div>
                                <div class="uk-width-auto">
                                    <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                        {{ formatCurrency(pay.jml_bayar) }}
                                    </p>
                                </div>
                                <div class="uk-width-auto">
                                    <button type="button" style="border:none; background-color: #fff;" @click.prevent="removePayment(pay)"><span uk-icon="icon:trash;ratio:0.8"></span></button>
                                </div>
                            </div>
                            <p style="margin:0;padding:0;font-size: 10px; font-weight: 400;color:black;">{{ pay.penjamin_nama }}</p>
                            <p style="margin:0;padding:0;font-size: 10px; font-weight: 400;color:black;">{{ pay.penjamin_id }}</p>
                            <p style="margin:0;padding:0;font-size: 10px; font-weight: 400;color:black;" v-if="pay.jns_payment == 'NON TUNAI'">{{ pay.jenis_kartu }} #{{ pay.no_kartu }}</p>
                            <p style="margin:0;padding:0;font-size: 10px; font-weight: 400;color:black;">{{ pay.mesin_edc }}</p>
                        </div>
                        <div v-else>
                            <p style="font-size: 12px; color:#666; font-weight: 500; font-style: italic;">belum ada data pembayaran</p>
                        </div>
                    </div>
                    <div>
                        <div class="uk-inline uk-width-1-1"  style="margin-top:1em;">
                            <button class="uk-button uk-button-default uk-width-1-1" type="button" style="width:100%;">Tambah Pembayaran</button>
                            <div uk-dropdown>
                                <ul class="uk-nav uk-dropdown-nav">
                                    <li class="uk-active"><a href="#" @click.prevent="addCashPayment">Tunai</a></li>
                                    <li><a href="#" @click.prevent="addNonCashPayment">Non Tunai</a></li>
                                    <li><a href="#" @click.prevent="addInsurancePayment">Asuransi</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="margin-top:0.5em;">
                            <button :disabled="isLoading" class="uk-button uk-button-default uk-width-1-1 uk-box-shadow-large" type="button" @click.prevent="submitPayment" style="color:white; width:100%;background-color: #FF2C2C;border:2px solid #FF2C2C;">Simpan Pembayaran</button>
                        </div>
                    </div> -->
                <!-- </div> -->
            </div>
        </div>        
        <div>

        </div>
        <payment-modal modalId="modalPayment" ref="modalPayment"></payment-modal>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowMedrec from '@/Pages/MedicalRecord/MedicalRecordList/RowMedrec.vue';
import RowSubResep from '@/Pages/MedicalRecord/MedicalRecordList/SubResep/RowSubResep.vue';
import ListLaboratorium from '@/Pages/MedicalRecord/MedicalRecordList/SubPenunjang/ListLaboratorium.vue';
import ListRadiologi from '@/Pages/MedicalRecord/MedicalRecordList/SubPenunjang/ListRadiologi.vue';
import ListPemeriksaan from '@/Pages/MedicalRecord/MedicalRecordList/SubPemeriksaan/ListPemeriksaan.vue';

import PaymentModal from '@/Pages/Pelayanan/Billing/Data/PaymentModal.vue';

export default {
    name  : 'data-billing',
    props : {
        pasienId : { type:String, required:true }, 
    },
    components : {
        RowMedrec,
        PaymentModal,
        RowSubResep,
        ListLaboratorium,
        ListRadiologi,
        ListPemeriksaan,
    },
    data(){
        return {
            isCheckAllRow : true,
            isLoading : false,
            medrecLists : null,
            pasien : {
                pasien_id : null,
                nama_lengkap : null,
                jns_kelamin : null,
                no_rm : null,
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('pembayaran',['billingLists','billingFilterTable','billingData']),
        ...mapGetters('rawatJalan',['poliTransactionLists','poliFilterTable','poliTransaction']),
        ...mapGetters('medicalrecord',['dataMedrec']),
        
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapActions('pembayaran',['dataBilling','createPayment','deletePayment']),
        ...mapMutations('pembayaran',['SET_BILLING_DATA']),

        ...mapActions('pasien',['dataPasien']),
        ...mapActions('medicalrecord',['listMedicalRecord','dataMedicalRecord']),
        ...mapMutations('medicalrecord',['SET_PASIEN_ID','CLR_MEDREC_DATA']),
        ...mapMutations(['CLR_ERRORS']),

        ...mapMutations('rawatJalan',[
            'CLR_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION_LISTS',
            'CLR_POLI_TRANSACTION_LISTS',
            'SET_FILTER_TABLE_POLI'
        ]),

        ...mapActions('rawatJalan',['dataTransaksiPoli','updateTransaksiPoli','createTransaksiPoli']),
        ...mapActions('pasien',['dataPasien']),

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        initialize(){   
            this.CLR_ERRORS();
            this.SET_PASIEN_ID(this.pasienId);
            this.getPasienData();
            this.getMedrecData();
        },


        /**modal call from other component for update data entry */
        retrieveData(refData) {
            this.CLR_ERRORS();
            this.CLR_MEDREC_DATA();
            this.isUpdate = true;
            this.isLoading = true;
            if(refData) {
                this.dataMedicalRecord(refData.reg_id).then((response) => {
                    if (response.success == true) {
                        this.isUpdate = true;
                        this.isLoading = false;
                    }
                    else { 
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
        },

        getMedrecData(){
            let params = { is_aktif : true, per_page : 'ALL' };
            this.listMedicalRecord(params).then((response) => {
                if (response.success == true) {
                    //console.log(response.data);
                    this.medrecLists = response.data.data;
                    //this.SET_BILLING_DATA(JSON.parse(JSON.stringify(response.data)));
                }
                else { alert (response.message) }
            });
        },

        getPasienData(){
            this.dataPasien(this.pasienId).then((response) => {
                if (response.success == true) {
                    console.log(response.data);
                    this.pasien = response.data;
                    //this.SET_BILLING_DATA(JSON.parse(JSON.stringify(response.data)));
                }
                else { alert (response.message) }
            });
        },

        checkAllRow(){
            this.billingData.transaksi.forEach(dt => {
                if(dt.interim_id == null || dt.interim_id == '') {
                    dt.is_bayar = this.isCheckAllRow;
                    this.updateRowData(dt);
                }
            });
        },

        updateRowData(data){
            if(data) {
                let grandTotal = 0;
                this.billingData.transaksi.forEach(dt => {
                    if(dt.is_bayar) { grandTotal = (grandTotal + (dt.total*1)) * 1; }
                    this.billingData.grand_total = grandTotal;
                });
            }
        },

        addCashPayment(){
            this.$refs.modalPayment.newCashPayment();
        },

        addNonCashPayment(){
            this.$refs.modalPayment.newNonCashPayment();
        },

        addInsurancePayment(){
            this.$refs.modalPayment.newInsurancePayment();
        },

        removePayment(data){
            if(data) {
                data.is_aktif = false;                
                let payment = this.billingData.pembayaran.filter(dt => { return dt.is_aktif == true });
                this.billingData.pembayaran = JSON.parse(JSON.stringify(payment));
            }
        },

        submitPayment(){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.createPayment(this.billingData).then((response) => {
                if (response.success == true) {
                    alert(response.message);
                    this.getTransactionData(this.billingData.reg_id);
                    this.isLoading = false;
                }
                else { 
                    alert (response.message);
                    this.isLoading = false;
                }
            });
        },

        deletePaymentHistory(data) {
            alert('delete payment histori');
            if(data) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan Menghapus data pembayaran. Lanjutkan ?`)){
                    this.isLoading = true;
                    this.deletePayment(data.payment_id).then((response) => {
                        if (response.success == true) {
                            this.isLoading = false; 
                            alert('pembayaran berhasil dibatalkan.');
                            this.getTransactionData(this.billingData.reg_id);
                        }
                        else { 
                            this.isLoading = false;
                            alert('pembayaran tidak berhasil dibatalkan.'); 
                        }
                    })
                }
            }
        }
    }
}
</script>
<style>
th.tb-header-transaksi {
    padding:1em; 
    background-color:#cc0202; 
    color:white;
    font-weight: 400;
    font-size:14px;
}

</style>