<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formCatatanKas" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body uk-border-rounded">
            <div class="hims-form-container" style="min-height:50vh;border-radius:10px;background-color:#fff;">
                <form action="" @submit.prevent="submitCatatanKas" >
                    <h5 class="uk-text-uppercase" style="text-align: center; font-weight: bold;">Tambah Data Pencatatan Kas</h5>
                    <hr>
                    <div>
                        <div class="uk-card uk-card-default" style="padding:25px;margin-top:1em;border-radius:10px;min-height:400px;background-color:#fff;border: 1px solid #f0f0f0;">
                            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                                <p>{{ errors.invalid }}</p>
                            </div>                
                                <div class="uk-width-1-1@s uk-grid-small" uk-grid> 
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" for="transactionDate">Tanggal Transaksi</label>
                                        <div class="uk-form-controls">
                                            <input type="datetime-local" class="uk-input" v-model="formData.tgl_transaksi" style="border-radius:7px" placeholder="Pilih Tanggal">
                                            <!-- <input id="transactionDate" type="date" class="uk-input" v-model="pencatatankas.tgl_transaksi" style="border-radius:7px" placeholder="Pilih Tanggal"> -->
                                        </div>
                                    </div>
                                    
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" for="transactionType">Jenis Transaksi</label>
                                        <div class="uk-form-controls">
                                            <select class="uk-select" v-model="formData.jenis_transaksi" style="border-radius:7px" placeholder="Pilih Jenis Transaksi" @change="jnsTransaksiChange">
                                            <!-- <select id="transactionType" class="uk-select" v-model="pencatatankas.jenis_transaksi" style="border-radius:7px" placeholder="Pilih Jenis Transaksi"> -->
                                                <option v-for="jns in jnsTransaksiKas" v-bind:value="jns.value"  v-bind:key="jns.value">{{ jns.text }}</option>
                                                <!-- <option value="pemasukan">Pemasukan</option>
                                                <option value="pengeluaran">Pengeluaran</option> -->
                                            </select>
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

                                    <div v-if="isTerima" class="uk-width-1-2@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="amount">Nominal Terima</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" v-model="formData.jml_terima" style="border-radius:7px" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="uk-width-1-2@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="amount">Nominal Bayar</label>
                                            <div class="uk-form-controls">
                                                <input type="number" class="uk-input" v-model="formData.jml_bayar" style="border-radius:7px" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-1@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="description">Deskripsi</label>
                                            <div class="uk-form-controls">
                                                <textarea class="uk-textarea" v-model="formData.deskripsi" style="border-radius:7px" placeholder="Ketik Deskripsi"></textarea>
                                                <!-- <textarea id="description" class="uk-textarea" v-model="pencatatankas.deskripsi" style="border-radius:7px" placeholder="Ketik Deskripsi"></textarea> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-1@m">
                                        <!-- <div class="uk-margin">
                                            <label class="uk-form-label" for="proof">Unggah Bukti Pembayaran</label>
                                            <div class="uk-form-controls">
                                                <input id="proof" type="file" class="uk-input" accept="image/*" @change="uploadProof" style="border-radius:7px; height: 80px;"> -->
                                                <!-- <div class="placeholder-icon">
                                                    <font-awesome-icon :icon="['fas', 'upload']" />
                                                    <span class="placeholder-text">Pilih Lampiran Gambar</span>
                                                </div> -->
                                            <!-- </div>
                                        </div> -->
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="proof">Unggah Bukti Pembayaran</label>
                                            <div class="uk-width-1-1@m" style="padding-top:0.5em;">
                                                <div>
                                                    <div v-if="formData.bukti_bayar == null || formData.bukti_bayar == ''" class="uk-width-1-1"  style="overflow:hidden; background-color:whitesmoke; height:80px; border-radius:10px; border:1px solid #aaa; margin:0; padding:0;">
                                                        <!-- <img id="fileWrapper" src="@/Assets/profile_pic.png" alt="" uk-img @click.prevent="browseFile"> -->
                                                    </div>
                                                    <div v-else class="uk-width-1-1" :style=" {'background-image': `url(${formData.bukti_bayar})` }">
                                                        <img id="fileWrapper" :data-src="formData.bukti_bayar" alt="" uk-img @click.prevent="browseFile">
                                                    </div>
                                                    <!-- <span style="font-size:10px; font-style:italic;">Klik gambar untuk mengubah.</span> -->
                                                    <button type="button" @click.prevent="browseFile" class="uk-width-1-1@m" style="margin-top:0.5em; border-radius:5px;height:40px;">Pilih lampiran gambar</button>
                                                    <button type="button" @click.prevent="removeTempFile" class="uk-width-1-1@m" style="margin-top:0.5em; border-radius:5px;height:40px;">Hapus lampiran gambar</button>
                                                    <div class="uk-width-1-1" style="margin:0;padding:0;">
                                                        <div uk-form-custom="target: true">
                                                            <input type="file" ref="file" @input="selectedFileKas" accept=".jpg,.jpeg,.png" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>   
                                        </div>
                                        <div class="uk-flex uk-flex-between" style="margin-top:-2em">
                                            <p class="uk-text-meta uk-margin-small-top">Format yang didukung: JPEG, PNG, GIF</p>
                                            <p class="uk-text-meta uk-margin-small-top">Ukuran maksimal: 5 MB</p>
                                        </div>
                                    </div>
                                    <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex uk-flex-right"> 
                                        <div class="uk-flex">
                                            <button class="uk-button uk-button-default uk-modal-close" type="button" @click.prevent="closeCatatanKasModal" style="border-radius: 7px; font-size: 14px; text-transform: none;">Tutup</button>
                                            <button class="uk-button uk-button-primary" type="submit" style="border-radius: 7px; font-size: 14px; text-transform: none;">Tambah</button>
                                        </div>
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
    name : 'form-catatan-kas', 
            
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

            let hours = dt.getHours();
            if(hours < 10) {hours = `0${hours}`}
            let minutes = dt.getMinutes();
            if(minutes < 10) {minutes = `0${minutes}`}

            let str_dt = `${year}-${month}-${date} ${hours}:${minutes}`;
            return str_dt;
        },

        closeCatatanKasModal(){
            this.$emit('formCatatanKasClosed',true);
            UIkit.modal('#formCatatanKas').hide();
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

        submitCatatanKas() {
            this.CLR_ERRORS();

            if (!this.isUpdate) {
                this.createKas(this.formData).then((response) => {
                    if (response.success == true) {
                        if (this.selectedFile) {
                        // cek validasi sblm upload file
                            if (this.isTerima && this.formData.jml_terima === 0) {
                            alert("Nominal terima harus lebih dari 0.");
                        return;
                        }

                        if (!this.isTerima && this.formData.jml_bayar === 0) {
                            alert("Nominal bayar harus lebih dari 0.");
                        return;
                        }

                        // proses data include file yg di upload
                        this.uploadFileLampiran(response.data);
                        } else {
                            // validasi nominal jumlah terima
                            if (this.isTerima && this.formData.jml_terima === 0) {
                            alert("Nominal terima harus lebih dari 0.");
                        return;
                        }

                        // validasi nominal jumlah bayar
                        if (!this.isTerima && this.formData.jml_bayar === 0) {
                            alert("Nominal bayar harus lebih dari 0.");
                        return;
                        }

                        alert("Pencatatan kas berhasil disimpan.");
                        this.clearPencatatanKas();
                        this.closeCatatanKasModal();
                    }
                    }
                });
            }
        },

        uploadFileLampiran(data){
            this.CLR_ERRORS();

            if(this.selectedFile.name.lastIndexOf(".") > 0) {
                let filename = this.selectedFile.name;
                let fileExtension = filename.substring(filename.lastIndexOf(".") + 1, filename.length);
                if(fileExtension.toUpperCase() !== 'JPEG' && fileExtension.toUpperCase() !== 'JPG' && fileExtension.toUpperCase() !== 'PNG' ) {
                    return false;
                }
                else {
                    let formData = new FormData();
                    formData.append("image", this.selectedFile);
                    formData.append("kas_id", data.kas_id);

                    this.uploadBukti(formData).then((response)=>{
                        if (response.success == true) {
                            this.formData.bukti_bayar = response.data.path_url;                    
                        } else {
                            alert(response.message);
                        }
                        this.clearPencatatanKas();
                        this.closeCatatanKasModal();
                    })        
                }
            }            
        },

        newPencatatanKas(){
            this.clearPencatatanKas();
            this.isUpdate = false;
            UIkit.modal('#formCatatanKas').show();
        },

        clearPencatatanKas(){
            this.formData.client_id = null;
            this.formData.kas_id = null;
            this.formData.is_aktif = null;
            this.formData.tgl_transaksi = this.getTodayDate();
            this.formData.jenis_transaksi = this.jnsTransaksiKas[0].value;
            this.formData.metode_pembayaran = this.metodeBayarKas[0].value;
            this.formData.jml_bayar = 0;
            this.formData.jml_terima = 0;
            this.formData.bukti_bayar = null;
            this.formData.deskripsi = null;
            this.isUpdate = false;
            this.isTerima = !this.jnsTransaksiKas[0].isExpense;

        },

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