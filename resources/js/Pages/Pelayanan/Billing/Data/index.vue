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
                    <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">DETAIL TAGIHAN PASIEN</h4>
                </div>
            </div>

            <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                <div uk-spinner="ratio : 2"></div>
                <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
            </div>
            
            <div class="uk-grid-small" uk-grid style="padding:0 0.5em 2em 0;margin:1em 0 0 0;background-color: #fff;">
                <div class="uk-width-2-3" style="background-color:#fff;padding:1em;">
                    <div style="background-color:#fff;padding:1em;margin-top:0;" uk-grid>
                        <div class="uk-width-1-2" style="margin-top:0;">
                            <p style="margin:0;padding:0;color:#000;font-size: 10px;font-weight: 500;">{{ regId }}</p>
                            <h5 style="margin:0;padding:0;color:#000;font-weight: bold;">{{ billingData.nama_pasien }} ( {{ billingData.no_rm }} )</h5>
                            <p style="margin:0;padding:0;color:#000;font-size: 12px;">{{ billingData.penjamin_nama }}</p>
                        </div>
                        <div class="uk-width-1-2" style="margin-top:0;">
                            <button type="button" style="margin-right:5px;float:right;" @click.prevent="cetakTagihan('ringkas',billingData.reg_id)"><span uk-icon="icon:print"></span>Cetak Ringkas</button>
                            <button type="button" style="margin-right:5px;float:right;" @click.prevent="cetakTagihan('lengkap',billingData.reg_id)"><span uk-icon="icon:print"></span>Cetak Lengkap</button>
                        </div>
                    </div>
                    <div>
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
                                        <th class="tb-header-transaksi" style="text-align:left;">UNIT DOKTER</th>
                                        <th class="tb-header-transaksi" style="width:40px;text-align: center;">JENIS</th>
                                        <!-- <th class="tb-header-transaksi" style="width:40px;text-align:right;">PENJAMIN</th> -->
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
                                                <button type="button" style="margin-right:5px;" @click.prevent="cetakTagihan('kwitansi',pay)"><span uk-icon="icon:print"></span>Cetak</button>
                                                <button type="button" @click.prevent="deletePaymentHistory(pay)"><span uk-icon="icon:trash"></span>Hapus</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="uk-width-1-3 uk-card-default uk-margin-small" style="background-color:#fcfcfc;padding:1em 2em 1em 1em;border-radius: 5px;">
                    <h4 style="font-weight: 500;">Tagihan</h4>
                    <div class="uk-card-default" style="padding:1em;margin:0; border-top-right-radius:5px; border-top-left-radius:5px;">
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-expand">
                                <p style="margin:0;padding:0.3em 0 0 0;font-size: 11px; font-weight: bold;color:black;">TOTAL TAGIHAN</p>
                            </div>
                            <!-- <div class="uk-width-auto">
                                <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency(billingData.grand_total) }}
                                </p>
                            </div> -->
                            <div class="uk-width-auto">
                                <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency(billingData.all_trx_total) }}
                                </p>
                            </div>
                            <div class="uk-width-auto">
                                <button type="button" style="border:none; background-color: #fff;"><span uk-icon="icon:info;ratio:0.8" title="total seluruh tagihan"></span></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="uk-card-default" style="padding:1em;margin:0;">
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-expand">
                                <p style="margin:0;padding:0.3em 0 0 0;font-size: 11px; font-weight: bold;color:black;">JML SUDAH DIBAYAR</p>
                            </div>
                            <div class="uk-width-auto">
                                <!-- <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency(billingData.total_bayar) }}
                                </p> -->
                                <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency(billingData.all_trx_bayar) }}
                                </p>
                            </div>
                            <div class="uk-width-auto">
                                <button type="button" style="border:none; background-color: #fff;"><span uk-icon="icon:info;ratio:0.8" title="total yang sudah dibayarkan"></span></button>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="uk-card-default" style="padding:1em;margin:0;">
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-expand">
                                <p style="margin:0;padding:0.3em 0 0 0;font-size: 11px; font-weight: bold;color:black;">JML AKAN DIBAYAR</p>
                            </div>
                            <div class="uk-width-auto">
                                <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency(billingData.total_bayar) }}
                                </p>
                            </div>
                            <div class="uk-width-auto">
                                <button type="button" style="border:none; background-color: #fff;"><span uk-icon="icon:info;ratio:0.8" title="total yang akan dibayar (lagi)"></span></button>
                            </div>
                        </div>
                    </div> -->

                    <div class="uk-card-default" style="padding:1em;margin:0; border-bottom-right-radius:5px; border-bottom-left-radius:5px;">
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-expand">
                                <p style="margin:0;padding:0.3em 0 0 0;font-size: 11px; font-weight: bold;color:black;">SISA TAGIHAN</p>
                            </div>
                            <!-- <div class="uk-width-auto">
                                <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency((billingData.grand_total - billingData.total_bayar)) }}
                                </p>
                            </div> -->
                            <div class="uk-width-auto">
                                <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency((billingData.all_trx_sisa)) }}
                                </p>
                            </div>
                            <div class="uk-width-auto">
                                <button type="button" style="border:none; background-color: #fff;"><span uk-icon="icon:info;ratio:0.8" title="sisa tagihan yang masih harus dibayar untuk lunas"></span></button>
                            </div>
                        </div>
                    </div>
                    
                    <h4 style="font-weight: 500;padding:1em 0 0 0;margin:0;">Total akan dibayar</h4>
                    <div class="uk-card-default" style="padding:1em;margin:0.5em 0 0 0; border-bottom-right-radius:5px; border-bottom-left-radius:5px;">
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-expand">
                                <p style="margin:0;padding:0.3em 0 0 0;font-size: 11px; font-weight: bold;color:black;">TOTAL</p>
                            </div>
                            <div class="uk-width-auto">
                                <p style="margin:2px 0 0 0;padding:0;font-weight: 400;text-align:right;color:black;font-size:14px;">
                                    {{ formatCurrency(billingData.grand_total - billingData.total_bayar) }}
                                </p>
                            </div>
                            <div class="uk-width-auto">
                                <button type="button" style="border:none; background-color: #fff;"><span uk-icon="icon:info;ratio:0.8" title="sisa tagihan yang masih harus dibayar untuk lunas"></span></button>
                            </div>
                        </div>
                    </div>

                    <h4 style="font-weight: 500;padding:1em 0 0 0;margin:0;">Pembayaran</h4>
                    <div class="uk-margin-small">
                        <div v-if="billingData.pembayaran != NULL" class="uk-card-default" v-for="pay in billingData.pembayaran" style="padding:1em;margin:5px 0 0 0; border-radius:5px;">
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
                    </div>
                </div>
            </div>
        </div>        
        <div>

        </div>
        <payment-modal modalId="modalPayment" ref="modalPayment" ></payment-modal>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowDataBilling from '@/Pages/Pelayanan/Billing/Data/RowDataBilling.vue';
import PaymentModal from '@/Pages/Pelayanan/Billing/Data/PaymentModal.vue';

export default {
    name  : 'data-billing',
    props : {
        regId : { type:String, required:true }, 
    },
    components : {
        RowDataBilling,
        PaymentModal
    },
    data(){
        return {
            isCheckAllRow : true,
            isLoading : true,
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('pembayaran',['billingLists','billingFilterTable','billingData']),
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapActions('pembayaran',['dataBilling','createPayment','deletePayment']),
        ...mapActions('cetakan',['cetakPembayaran']),
        ...mapMutations('pembayaran',['SET_BILLING_DATA']),
        ...mapMutations(['CLR_ERRORS']),

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        initialize(){   
            this.isLoading = true;
            this.CLR_ERRORS();
            this.getTransactionData(this.regId);
        },

        getTransactionData(regId){
            this.dataBilling(regId).then((response) => {
                if (response.success == true) {
                    this.SET_BILLING_DATA(JSON.parse(JSON.stringify(response.data)));
                    this.isLoading = false;
                }
                else { alert (response.message); this.isLoading = false; }
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
                    if(dt.is_bayar) { 
                        grandTotal = (grandTotal + (dt.total*1)) * 1; 
                    }
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
            var totalBayarSebelumnya = 0;
            var totalBayarSekarang = 0;
            totalBayarSebelumnya = this.billingData.total_bayar;
            this.billingData.pembayaran.forEach(function(data) {
                totalBayarSekarang += Number(data.jml_bayar) - data.pembulatan;
            });
            if(this.billingData.grand_total != (totalBayarSekarang + totalBayarSebelumnya)){
                alert("Maaf, anda tidak dapat memproses karena transaksi yang belum dibayar belum dipilih.")
                return false;
            }
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
        },

        cetakTagihan(jenisCetakan, data){
            let ids = "";
            console.log(data);
            if(jenisCetakan == "kwitansi"){
                ids = data.payment_id;
            } else {
                ids = data;
            }
            let param = [
                { 
                    'jnsCetakan' : jenisCetakan,
                    'ids': ids,
                },
            ];
            this.cetakPembayaran(param).then((response) => {
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
                        margin: 0;
                    }
                    .page-number {
                        font-size:9px;
                        position: absolute;
                        bottom: 10px;
                        right: 10px;
                    }
                    .left-text {
                        font-size:9px;
                        position: absolute;
                        bottom: 10px;
                        left: 10px;
                    }
                </style>
            `;

            // Append page number to each page
            const pages = pdf_body.split('<div class="page-break"></div>'); // Assuming you have a class for page breaks
            let fullHtml = customStyles;
            var Currentdate = this.getDateFormated(new Date());
            for (let i = 0; i < pages.length; i++) {
                const pageNumber = i + 1;
                const pageHtml = pages[i];
                const pageWithNumber = `
                    <div class="page-container">
                        ${pageHtml}
                        <div class="left-text">${Currentdate}</div>
                        <div class="page-number">Page ${pageNumber} of ${pages.length}</div>
                    </div>
                `;
                fullHtml += pageWithNumber;
                if (i < pages.length - 1) {
                    fullHtml += '<div class="page-break"></div>';
                }
            }

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
th.tb-header-transaksi {
    padding:1em; 
    background-color:#cc0202; 
    color:white;
    font-weight: 400;
    font-size:14px;
}

</style>