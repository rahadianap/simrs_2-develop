<template>
    <div class="uk-modal uk-modal1" :id="idModal">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h4 class="uk-modal-title1" style="font-size:18px;font-weight: 500;">{{title}}</h4>
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            <form action="" @submit.prevent="addPayment" class="uk-form-horizontal">
                <div class="uk-grid-small" uk-grid>
                    <h5 style="font-weight: 500;">Jumlah Pembayaran</h5>
                    <div class="uk-width-1-1@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Tagihan</label>
                        <div class="uk-form-controls">
                            <!-- <input type="text" class="uk-input uk-form-small1" style="color:black;text-align:right;" v-model="totalTagihan" disabled> -->
                            <label class="uk-input uk-form-small1" style="color:black;text-align:right;" disabled>{{ this.totalTagihan }}.00</label>
                        </div>
                    </div>
                    <div class="uk-width-1-1@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jumlah Bayar</label>
                        <div class="uk-form-controls">
                            <input class="uk-checkbox" type="checkbox" style="border:1px solid black;font-size:10px;" ref="checkBayarLunas" @change="bayarLunas" v-model="checkedLunas"><i style="font-size:12px;margin-left:5px;">Bayar Lunas</i>
                        </div>
                        <div class="uk-form-controls">
                            <input class="uk-checkbox" type="checkbox" style="border:1px solid black;font-size:10px;" ref="checkPembulatan" @change="pembulatanKeatas" v-model="checkedPembulatan"><i style="font-size:12px;margin-left:5px;">Bulatkan Keatas</i>
                        </div>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input uk-form-small1" style="color:black;text-align:right;" v-model="paymentData.jml_bayar" @input="calculateSisa">
                        </div>
                        
                    </div>
                    <div class="uk-width-1-1@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Sisa Bayar</label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input uk-form-small1" style="color:black;text-align:right;" v-model="sisaTagihan" disabled >
                        </div>
                    </div>
                    <h5 v-if="tipePayment !== 0" style="font-weight: 500;">Data Pembayaran</h5>
                    <div class="uk-width-1-1@m" v-if="tipePayment == 1">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kartu</label>
                        <div class="uk-form-controls">
                            <select class="uk-select" v-model="paymentData.jenis_kartu">
                                <option>pilih jenis kartu</option>
                                <option value="DEBIT_CARD">DEBIT CARD</option>
                                <option value="CREDIT_CARD">CREDIT CARD</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-1-1@m" v-if="tipePayment == 1">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Kartu</label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input uk-form-small1" style="color:black;" v-model="paymentData.no_kartu">
                        </div>
                    </div>
                    <div class="uk-width-1-1@m" v-if="tipePayment == 1">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Mesin EDC</label>
                        <div class="uk-form-controls">
                            <select class="uk-select" v-model="paymentData.mesin_edc">
                                <option>pilih mesin edc</option>
                                <option value="MANDIRI">MANDIRI #12345678</option>
                                <option value="BNI">BNI #005412233</option>
                                <option value="BCA">BCA #33221200</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-1-1@m" v-if="tipePayment == 2">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Asuransi</label>
                        <div class="uk-form-controls">
                            <input type="text" class="uk-input uk-form-small1" style="color:black;" v-model="paymentData.penjamin_nama">
                        </div>
                    </div>
                    
                    <div class="uk-width-1-1" style="text-align:right;">
                        <button class="uk-button uk-box-shadow-large" type="submit" style="background-color: #FF2C2C; color:white; border:none;">Tambahkan</button>
                    </div>                
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'payment-modal',
    props : {
        idModal : { type:String, required:false, default:'paymentModal' },
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('pembayaran',['billingLists','billingFilterTable','billingData']),
    },
    data(){
        return {
            title : null,
            tipePayment : 0,
            totalTagihan : 0,
            sisaTagihan : 0,
            hasilPembulatan : 0,
            paymentData : {
                reg_id : null,
                jns_payment : null,
                jml_bayar : 0,
                sisa_tagihan : 0,
                sisa_bayar : 0,
                jenis_kartu : null,
                no_kartu : null,
                mesin_edc : null,
                penjamin_nama : null,
                penjamin_id : null,   
                pembulatan : null,
                is_aktif : true,
            },
            cardType : [
                {jenis_kartu:'DEBIT CARD', nama_jenis_bayar : 'Uang Muka (Deposit)', },
                {jenis_kartu:'CREDIT CARD', nama_jenis_bayar : 'Pembayaran', },
                {jenis_kartu:'VISA', nama_jenis_bayar : 'Pembayaran', },                
            ],
            paymentType : [
                {jenis_bayar:'UANG MUKA', nama_jenis_bayar : 'Uang Muka (Deposit)', },
                {jenis_bayar:'PEMBAYARAN', nama_jenis_bayar : 'Pembayaran', },
            ],
            penjaminLists : null,
            checkedLunas : false,
            checkedPembulatan : false,
        }
    },
    methods : {
        ...mapMutations('pembayaran',['SET_BILLING_DATA']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            UIkit.modal(`#${this.idModal}`).hide();
        },

        clearData(){
            this.paymentData.reg_id = null;
            this.paymentData.jns_payment = null;
            this.paymentData.jml_bayar = Number(0).toFixed(2);
            this.paymentData.sisa_tagihan = 0;
            this.paymentData.jenis_kartu = null;
            this.paymentData.no_kartu = null;
            this.paymentData.mesin_edc = null;
            this.paymentData.penjamin_nama = null;
            this.paymentData.penjamin_id = null;   
            this.paymentData.pembulatan = null;
            this.paymentData.is_aktif = true;
            this.checkedLunas = false;
            this.checkedPembulatan = false;
        },

        calculateSisaTagihan(){
            // alert('calculate sisa tagihan');
            this.totalTagihan = Number(this.billingData.grand_total - this.billingData.total_bayar).toFixed(2);
            this.sisaTagihan = 0;
            this.billingData.pembayaran.forEach(dt => {
                this.totalTagihan = this.totalTagihan - (dt.jml_bayar * 1);
                this.paymentData.jml_bayar = this.totalTagihan;
            });
        },

        newCashPayment(){
            this.clearData();
            this.title = "Pembayaran Tunai";
            this.tipePayment = 0;
            this.paymentData.jns_payment = "TUNAI";
            this.calculateSisaTagihan();
            UIkit.modal(`#${this.idModal}`).show();
        },
        
        newNonCashPayment(){
            this.clearData();
            this.title = "Pembayaran Non Tunai";
            this.tipePayment = 1;
            this.paymentData.jns_payment = "NON TUNAI";
            this.calculateSisaTagihan();
            UIkit.modal(`#${this.idModal}`).show();
        },

        newInsurancePayment(){
            this.clearData();
            this.title = "Pembayaran Asuransi";
            this.tipePayment = 2;
            this.paymentData.jns_payment = "ASURANSI";
            this.paymentData.penjamin_nama = this.billingData.penjamin_nama;
            this.paymentData.penjamin_id = this.billingData.penjamin_id;
            this.calculateSisaTagihan();
            UIkit.modal(`#${this.idModal}`).show();
        },

        addPayment(){
            if(this.paymentData.jml_bayar > 0) {
                this.billingData.pembayaran.push(JSON.parse(JSON.stringify(this.paymentData)));
                this.closeModal();
            }
            else {
                alert('jumlah pembayaran tidak boleh kurang dari atau sama dengan nol, ATAU nilai sisa tidak boleh kurang dari 0.');
            }
        },

        calculateSisa(){
            this.sisaTagihan = Number(this.billingData.grand_total-this.billingData.total_bayar - this.paymentData.jml_bayar).toFixed(2);
        },

        bayarLunas(){
            let jml_bayar=0;
            if(this.$refs.checkBayarLunas.checked == false) {
                this.paymentData.jml_bayar = Number(0).toFixed(2);
            } 
            else {
                // jml_bayar = this.paymentData.jml_bayar = Number(this.billingData.grand_total-this.billingData.total_bayar).toFixed(2);
                // if(this.$refs.checkPembulatan.checked == true) {
                //     jml_bayar = Math.round(Number(this.paymentData.jml_bayar).toFixed(0) / 100) * 100;
                //     this.paymentData.pembulatan = Number(jml_bayar - this.billingData.grand_total-this.billingData.total_bayar).toFixed(2);
                // }
                // this.paymentData.jml_bayar = Number(jml_bayar).toFixed(2);
                jml_bayar = this.paymentData.jml_bayar = Number(this.billingData.grand_total - this.billingData.total_bayar).toFixed(2);
                if(this.$refs.checkPembulatan.checked == true) {
                    jml_bayar = Math.round(Number(this.paymentData.jml_bayar).toFixed(0) / 100) * 100;
                    this.paymentData.pembulatan = Number(jml_bayar - this.billingData.grand_total - this.billingData.total_bayar).toFixed(2);
                }
                this.paymentData.jml_bayar = Number(jml_bayar).toFixed(2);
            }
            this.calculateSisa();
        },

        pembulatanKeatas(){
            if(this.$refs.checkPembulatan.checked == true) {
                this.hasilPembulatan = Math.round(Number(this.billingData.grand_total-this.billingData.total_bayar).toFixed(0)/ 100) * 100;
                this.paymentData.pembulatan = Number(this.hasilPembulatan - this.billingData.grand_total-this.billingData.total_bayar).toFixed(2);
            } else {
                this.hasilPembulatan = Number(this.billingData.grand_total-this.billingData.total_bayar).toFixed(2);
                this.paymentData.pembulatan = Number(0).toFixed(2);
            }
            this.paymentData.jml_bayar = Number(this.hasilPembulatan).toFixed(2);
            this.calculateSisa();
        }
    }
}
</script>