<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formCatatanKas" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body uk-border-rounded">
            disini
            <div class="hims-form-container" style="min-height:50vh;border-radius:10px;background-color:#fff;">
                <form action="" @submit.prevent="submitPembayaranKasir" >
                    <h5 class="uk-text-uppercase" style="text-align: center; font-weight: bold;">Form Pembayaran Kasir</h5>
                    <hr>
                    <div>
                        <div class="uk-card uk-card-default" style="padding:25px;margin-top:1em;border-radius:10px;min-height:400px;background-color:#fff;border: 1px solid #f0f0f0;">
                            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                                <p>{{ errors.invalid }}</p>
                            </div>                
                                <div class="uk-width-1-1@s uk-grid-small" uk-grid> 
                                    <div class="uk-width-1-1@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="amount">Total Tagihan Pasien</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" v-model="formData.jml_terima" style="border-radius:7px" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="uk-width-1-2@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="amount">Diskon</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" v-model="formData.jml_terima" style="border-radius:7px" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-2@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="amount">Total Bayar Pasien</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" v-model="formData.jml_terima" style="border-radius:7px" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" for="paymentMethod">Metode Pembayaran</label>
                                        <div class="uk-form-controls">
                                            <select class="uk-select" v-model="formData.metode_pembayaran" style="border-radius:7px" placeholder="Pilih Metode Pembayaran">
                                            <!-- <select id="paymentMethod" class="uk-select" v-model="pencatatankas.metode_pembayaran" style="border-radius:7px" placeholder="Pilih Metode Pembayaran"> -->
                                                <option v-for="mtd in metodeBayarKas" v-bind:value="mtd.value"  v-bind:key="mtd.value">{{ mtd.text }}</option>
                                                <!-- <option value="kartu-kredit">Kartu Kredit/Debit</option>
                                                <option value="e-wallet">E-wallet</option>
                                                <option value="qris">QRIS</option>
                                                <option value="virtual-account">Virtual Account</option> -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-2@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="amount">Jumlah</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" v-model="formData.jml_terima" style="border-radius:7px" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-1@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="amount">Jumlah Bayar</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" v-model="formData.jml_terima" style="border-radius:7px" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-2@m">
                                    </div>

                                    <div class="uk-width-1-2@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="amount">Kembalian</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" v-model="formData.jml_terima" style="border-radius:7px" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m"></div>
                                    <div class="uk-width-1-4@m"></div>
                                    <div class="uk-width-1-4@m">
                                        <button class="uk-button uk-button-default uk-modal-close" type="button" @click.prevent="closePembayaranKasirModal" style="margin-top: 1.5em; border-radius: 7px; font-size: 14px; text-transform: none;">Tutup</button>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <button class="uk-button uk-button-primary" type="submit" style="margin-top: 1.5em; border-radius: 7px; font-size: 14px; text-transform: none;">Tambah</button>
                                    </div>
                                </div>
                            </div>  
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'form-pembayaran-kasir', 
            
    data() {
        return {
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : true,
            },

            isUpdate : true,
            isTerima : false,
            selectedFile : null,
            formData : {
                client_id:null,
                kas_id:null,
                tgl_transaksi:null,
                jenis_transaksi : null,
                deskripsi : null,
                metode_pembayaran : null,
                jml_bayar : null,
                jml_terima : null,
                bukti_bayar : null,
                is_aktif : true,          
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('pencatatankas',['jnsTransaksiKas','metodeBayarKas','filterDataKas']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('pencatatankas',['createKas','updateKas','deleteKas','dataKas','uploadBukti']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
            this.clearPencatatanKas();
        },

        browseFile() {
            this.$refs.file.click();
        },
        removeTempFile(){
            this.formData.bukti_bayar = null;
            this.selectedFile = null;
            this.$refs.file = null;
        },

        selectedFileKas(){
            let input = this.$refs.file;
            let file = input.files;
            
            if(file && file[0]){
                this.selectedFile = file[0];
                console.log(this.selectedFile.name);
                if(this.selectedFile.name.lastIndexOf(".") > 0) {
                    let filename = this.selectedFile.name;
                    let fileExtension = filename.substring(filename.lastIndexOf(".") + 1, filename.length);
                    if(fileExtension.toUpperCase() !== 'JPEG' && fileExtension.toUpperCase() !== 'JPG' && fileExtension.toUpperCase() !== 'PNG' ) {
                        alert('format file hanya boleh JPEG, JPG, dan PNG');
                        this.removeTempFile();
                        return false;
                    }
                    else {
                        let reader = new FileReader;
                        reader.onload = e => {
                            this.formData.bukti_bayar = e.target.result
                        }
                        reader.readAsDataURL(file[0])
                        this.$emit('input', file[0])
                    }
                }
            }
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

        closePembayaranKasirModal(){
            this.$emit('formPembayaranKasirClosed',true);
            UIkit.modal('#formPembayaranKasir').hide();
            return false;
        },

        jnsTransaksiChange(){
            console.log(this.formData.jenis_transaksi);
            let jenisSelected = this.jnsTransaksiKas.find(item => item.value === this.formData.jenis_transaksi);
            if(jenisSelected) {
                this.formData.jml_bayar = 0;
                this.formData.jml_terima = 0;
                this.isTerima = !jenisSelected.isExpense;
            }
        },

        submitPembayaranKasir(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createKas(this.formData).then((response) => {
                    if (response.success == true) {
                        if(this.selectedFile) {
                            this.uploadFileLampiran(response.data);
                        }
                        else {
                            alert("pencatatan kas berhasil disimpan.");
                            this.clearPencatatanKas();
                            this.closePembayaranKasirModal();
                        }
                    }
                })
            }
        },

        // uploadFileLampiran(data){
        //     this.CLR_ERRORS();

        //     if(this.selectedFile.name.lastIndexOf(".") > 0) {
        //         let filename = this.selectedFile.name;
        //         let fileExtension = filename.substring(filename.lastIndexOf(".") + 1, filename.length);
        //         if(fileExtension.toUpperCase() !== 'JPEG' && fileExtension.toUpperCase() !== 'JPG' && fileExtension.toUpperCase() !== 'PNG' ) {
        //             return false;
        //         }
        //         else {
        //             let formData = new FormData();
        //             formData.append("image", this.selectedFile);
        //             formData.append("kas_id", data.kas_id);

        //             this.uploadBukti(formData).then((response)=>{
        //                 if (response.success == true) {
        //                     this.formData.bukti_bayar = response.data.path_url;                    
        //                 } else {
        //                     alert(response.message);
        //                 }
        //                 this.clearPencatatanKas();
        //                 this.closePembayaranKasirModal();
        //             })        
        //         }
        //     }            
        // },

        newPembayaranKasir(){
            // this.clearPembayaranKasir();
            this.isUpdate = false;
            UIkit.modal('#formPembayaranKasir').show();
        },

        // clearPembayaranKasir(){
        //     this.formData.client_id = null;
        //     this.formData.kas_id = null;
        //     this.formData.is_aktif = null;
        //     this.formData.tgl_transaksi = this.getTodayDate();
        //     this.formData.jenis_transaksi = this.jnsTransaksiKas[0].value;
        //     this.formData.metode_pembayaran = this.metodeBayarKas[0].value;
        //     this.formData.jml_bayar = 0;
        //     this.formData.jml_terima = 0;
        //     this.formData.bukti_bayar = null;
        //     this.formData.deskripsi = null;
        //     this.isUpdate = false;
        //     this.isTerima = !this.jnsTransaksiKas[0].isExpense;

        // },

    }
}
</script>

<style>
.uk-form-label {
  margin-bottom: 10px;
}

.uk-form-controls {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.uk-form-controls .uk-form-icon {
  font-size: 24px;
  margin-bottom: 10px;
}

.uk-form-controls .uk-position-center {
  text-align: center;
}

.uk-form-controls .uk-text-meta {
  font-size: 12px;
}
</style>