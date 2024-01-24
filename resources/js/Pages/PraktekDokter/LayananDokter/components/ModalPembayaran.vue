<template>
    <div class="uk-modal" uk-modal="bg-close:false;" :id="modalId">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container hims-form-container1" style="background-color:#fff;padding:0.5em;">
                <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;text-align:center;">
                    <h4 class="uk-text-uppercase">PEMBAYARAN KASIR</h4>
                </div> 
                <form action="" @submit.prevent="submitPembayaran" >
                    <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                        <div uk-spinner="ratio : 2"></div>
                        <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                    </div>
                    
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header1" uk-grid style="margin:0;padding:0.5em;">                        
                        <div class="uk-width-1-1 uk-grid-small" uk-grid>
                            <div class="uk-width-1-1@m">
                                <h5 style="font-weight:500;">Tagihan</h5>
                            </div>
                            <div class="uk-width-2-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Tagihan</label>
                                <div class="uk-form-controls">
                                    <p class="uk-input" style="cursor:not-allowed;background:#e7e6e7a1;color:black;border-radius:7px;text-align:right;" required @input="calculateTotal">{{ formatCurrency(pembayaran.total_tagihan) }}</p>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diskon (%)</label>
                                <div class="uk-form-controls">
                                    <input type="number" class="uk-input" style="color:black;border-radius:7px;text-align:right;" v-model="pembayaran.diskon" required @change="calculateTotal">
                                </div>
                            </div>
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Total Akhir</label>
                                <div class="uk-form-controls">
                                    <p class="uk-input" style="cursor:not-allowed;background:#e7e6e7a1;font-weight:500; color:black; border-radius:7px; text-align:right; border:1px solid #666; line-height:35px;">{{ formatCurrency(pembayaran.total_akhir) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="uk-width-1-1 uk-grid-small" uk-grid>
                            <div class="uk-width-1-1@m" style="border-top:1px solid #aaa;">
                                <h5 style="font-weight:500;">Pembayaran</h5>
                            </div>
                            <template v-for="dt in pembayaran.detail">
                                <div  v-if="dt.is_aktif" class="uk-width-1-1 uk-grid-small" uk-grid>
                                    <div class="uk-width-expand@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Metode Pembayaran</label>
                                        <div class="uk-form-controls">
                                            <select class="uk-select uk-border-rounded" v-model="dt.jns_payment">
                                                <option value="">Pilih Metode Pembayaran</option>
                                                <option v-for="mtd in metodeBayarKas" v-bind:value="mtd.value" v-bind:key="mtd.value">
                                                    {{ mtd.text }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-width-expand@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jumlah</label>
                                        <div class="uk-form-controls">
                                            <input type="number" class="uk-input" style="color:black;border-radius:7px;text-align:right;" v-model="dt.jml_bayar" required @change="calculateJumlahBayar">
                                        </div>
                                    </div>
                                    <div class="uk-width-auto">
                                        <button @click.prevent="removeMetodeBayar(dt)" style="border:none;margin-top:2.4em;">
                                            <span uk-icon="icon: minus-circle"></span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                            <div class="uk-width-1-1">
                                <p style="margin:0;padding:0;font-size: 11px;font-style: italic;"><a href="#" @click.prevent="addMetodePembayaran">tambah metode pembayaran</a></p>
                            </div>
                            <!--isi dengan array detail pembayaran-->
                        </div>
                        
                        <div class="uk-width-1-1 uk-grid-small" uk-grid>
                            <div class="uk-width-1-1@m" style="border-top:1px solid #aaa;">
                                <h5 style="font-weight:500;">Total Pembayaran</h5>
                            </div>                            
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jumlah Bayar</label>
                                <div class="uk-form-controls">
                                    <p class="uk-input" style="font-weight:500; background:#e7e6e7a1; cursor:not-allowed; color:black; border-radius:7px; text-align:right; border:1px solid #666; line-height:35px;">{{ formatCurrency(pembayaran.jml_bayar) }}</p>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kembalian</label>
                                <div class="uk-form-controls">
                                    <p class="uk-input" style="cursor:not-allowed;background:#e7e6e7a1;font-weight:500; color:black; border-radius:7px; text-align:right; border:1px solid #666; line-height:35px;">{{ formatCurrency(pembayaran.kembalian) }}</p>
                                    <!-- <input type="number" class="uk-input" style="color:black;border-radius:7px;text-align:right;" v-model="pembayaran.kembalian" disabled> -->
                                </div>
                            </div>
                        </div> 
                        <div class="uk-width-1-1"  style="text-align:right; margin-top:2em;padding:1em;">
                            <button type="button" class="uk-button uk-modal-close" @click.prevent="closeModal" style="border:1px solid #999; border-radius:7px; height:40px;">Tutup</button>
                            <button type="submit" class="uk-button uk-button-primary" style="border-radius:7px; height:40px;">Bayar</button>
                        </div>
                    </div>  
                </form>  
            </div>  
        </div>
    </div>    
</template>
<script>

import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    name : 'modal-pembayaran',
    components : {

    },

    data() {
        return {
            modalId : 'formPembayaran',
            isLoading : false,
            isUpdate : true,
            pembayaran : {
                payment_id : null,
                reg_id : null,
                interim_id : null,
                tgl_bayar : null,
                jns_payment : null,
                total_tagihan : 0,
                diskon : 0,
                total_akhir : 0,
                jml_bayar : 0,
                kembalian : 0,
                jenis_kartu : null,
                penjamin_id : null,
                penjamin_nama : null,
                mesin_edc : null,
                no_kartu : null,
                is_aktif : true,
                detail : [],
            },

            detailBayar : {
                payment_id : null,
                jns_payment : null,
                jml_bayar : 0,
                is_aktif : true,
            }
        }
    },

    computed : {
        ...mapGetters(['errors']),
      ...mapGetters('pencatatankas',['jnsTransaksiKas','metodeBayarKas','filterDataKas','dataPemeriksaan']),      
    },

    methods : {
        ...mapActions('praktekDokter',['createPayment','updatePayment','deletePayment','listPayment','dataPayment','dataPemeriksaanPasien']),
        ...mapMutations(['CLR_ERRORS']),

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        initialize() {
            this.modalId = `formPembayaran-${this.getTodayDate()}`;
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

        closeModal(){
            UIkit.modal(`#${this.modalId}`).hide();
        },

        newPayment(data) {
            if(data.is_bayar) {
                console.log('pembayaran sudah dilakukan.');
            }
            else {
                this.CLR_ERRORS();
                this.isUpdate = false;
                this.clrPembayaran();
                this.pembayaran.reg_id = data.reg_id;
                this.retrievePemeriksaan();
                UIkit.modal(`#${this.modalId}`).show();
            }
        },

        retrievePemeriksaan(){
            this.dataPemeriksaanPasien(this.pembayaran.reg_id).then((response) => {
                if (response.success == true) {
                    let total = response.data.total;
                    this.pembayaran.total_tagihan = total*1;
                    this.pembayaran.total_akhir = total*1;                    
                }
                else {
                    alert(response.message);
                }
            })
        },

        editPayment(data){
            this.CLR_ERRORS();
            this.isUpdate = true;
            this.clrPembayaran();
            this.pembayaran.reg_id = data.reg_id;
            this.retrievePayment(data);
        },

        retrievePayment(data) {
            this.CLR_ERRORS();
            this.isLoading = true;
            this.dataPayment(data.reg_id).then((response) => {
                this.isLoading = false;
                if (response.success == true) {
                    this.isUpdate = true;
                    this.fillPembayaran(response.data);
                    UIkit.modal(`#${this.modalId}`).show();
                    this.calculateTotal();
                    this.calculateJumlahBayar();
                }
                else {
                    alert(response.message);
                }
            })
        },

        submitPembayaran(){
            this.calculateTotal();
            this.calculateJumlahBayar();
            if(this.pembayaran.kembalian >= 0 && this.pembayaran.jml_bayar > 0) {
                this.savePembayaran();
                return false;
            }
            else {
                alert('Jumlah tagihan dan jumlah bayar selisih. Data tidak dapat disimpan.');
                return false;
            }
        },

        savePembayaran(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.isLoading = true;
                this.createPayment(this.pembayaran).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        alert("Data pembayaran berhasil disimpan.");
                        this.$emit('saveSucceed',response.data);
                        this.isUpdate = true;
                        UIkit.modal(`#${this.modalId}`).hide();
                    }
                })
            }
            else {
                this.isLoading = true;
                this.updatePayment(this.pembayaran).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        alert("Data pembayaran berhasil diubah.");
                        this.$emit('saveSucceed',response.data);
                        this.isUpdate = true;
                        UIkit.modal(`#${this.modalId}`).hide();
                    }
                })
            }
        },
        
        clrPembayaran(){
            this.pembayaran.payment_id = null;
            this.pembayaran.reg_id = null;
            this.pembayaran.interim_id = null;
            this.pembayaran.tgl_bayar = null;
            this.pembayaran.jns_payment = null;
            
            this.pembayaran.total_tagihan = 0;
            this.pembayaran.diskon = 0;
            this.pembayaran.total_akhir = 0;
            this.pembayaran.jml_bayar = 0;
            this.pembayaran.kembalian = 0;

            this.pembayaran.jenis_kartu = null;
            this.pembayaran.penjamin_id = null;
            this.pembayaran.penjamin_nama = null;
            this.pembayaran.mesin_edc = null;
            this.pembayaran.no_kartu = null;
            this.pembayaran.is_aktif = true;
            this.pembayaran.detail =  [];
            this.pembayaran.detail.push(JSON.parse(JSON.stringify(this.detailBayar)));

            console.log(this.pembayaran);
            
        },

        fillPembayaran(data) {
            this.pembayaran.interim_id = data.interim_id;
            this.pembayaran.reg_id = data.reg_id;
            this.pembayaran.tgl_bayar = data.tgl_bayar;
            this.pembayaran.total_tagihan = data.total_tagihan;
            this.pembayaran.diskon = data.diskon;
            this.pembayaran.total_akhir = data.total_akhir;
            this.pembayaran.jml_bayar = data.jml_bayar;
            this.pembayaran.kembalian = data.kembalian;
            this.pembayaran.is_aktif = data.is_aktif;
            this.pembayaran.detail = data.detail;
        },

        calculateTotal() {
            let totalAkhir = 0;
            let diskon = 0;
            if(this.pembayaran.diskon > 0 && this.pembayaran.total_tagihan > 0) {
                diskon = ((this.pembayaran.total_tagihan * this.pembayaran.diskon) / 100 * 1);
                totalAkhir = this.pembayaran.total_tagihan - diskon;
                this.pembayaran.total_akhir = (totalAkhir * 1);
            }
            else {
                totalAkhir = this.pembayaran.total_tagihan;
                this.pembayaran.total_akhir = (totalAkhir * 1);
            }
            
            this.calculateJumlahBayar();
        },

        addMetodePembayaran(){
            this.pembayaran.detail.push(JSON.parse(JSON.stringify(this.detailBayar)));
            this.calculateJumlahBayar();
        },

        removeMetodeBayar(data){
            // console.log(this.pembayaran);
            //if(data.payment_id !== null && data.payment_id !== ''){
            data.is_aktif =  false;
            // }
            
            let finalDetail = this.pembayaran.detail.filter(
                item => { return item.is_aktif == true || (item.payment_id !== null && item.payment_id !== '') }
            ); 

            this.pembayaran.detail = JSON.parse(JSON.stringify(finalDetail));
            this.calculateJumlahBayar();
        },

        calculateJumlahBayar(){
            if(this.pembayaran.detail.length > 0) {
                let jmlBayar = 0;
                this.pembayaran.detail.forEach(dt => {
                    if(dt.is_aktif) {
                        jmlBayar = jmlBayar + (dt.jml_bayar * 1);
                    }
                });
                let jmlKembalian = jmlBayar - this.pembayaran.total_akhir;
                this.pembayaran.jml_bayar = jmlBayar;
                this.pembayaran.kembalian = jmlKembalian;
            }    
        }
    }
}
</script>