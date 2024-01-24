<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="dialogKonfirmasiBatal" style="min-height:50vh;">
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
                                            <input id="transactionDate" type="date" class="uk-input" v-model="hrkeluarga.catatan" style="border-radius:7px" placeholder="Pilih Tanggal">
                                            <!-- <input id="transactionDate" type="date" class="uk-input" v-model="pencatatankas.tgl_transaksi" style="border-radius:7px" placeholder="Pilih Tanggal"> -->
                                        </div>
                                    </div>
                                    
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" for="transactionType">Jenis Transaksi</label>
                                        <div class="uk-form-controls">
                                        <select id="transactionType" class="uk-select" v-model="hrkeluarga.pendidikan" style="border-radius:7px" placeholder="Pilih Jenis Transaksi">
                                        <!-- <select id="transactionType" class="uk-select" v-model="pencatatankas.jenis_transaksi" style="border-radius:7px" placeholder="Pilih Jenis Transaksi"> -->
                                            <option value="pemasukan">Pemasukan</option>
                                            <option value="pengeluaran">Pengeluaran</option>
                                        </select>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" for="paymentMethod">Metode Pembayaran</label>
                                        <div class="uk-form-controls">
                                            <select id="paymentMethod" class="uk-select" v-model="hrkeluarga.tempat_lahir" style="border-radius:7px" placeholder="Pilih Metode Pembayaran">
                                            <!-- <select id="paymentMethod" class="uk-select" v-model="pencatatankas.metode_pembayaran" style="border-radius:7px" placeholder="Pilih Metode Pembayaran"> -->
                                                <option value="tunai">Tunai</option>
                                                <option value="kartu-kredit">Kartu Kredit/Debit</option>
                                                <option value="e-wallet">E-wallet</option>
                                                <option value="qris">QRIS</option>
                                                <option value="virtual-account">Virtual Account</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-2@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="amount">Nominal</label>
                                            <div class="uk-form-controls">
                                            <input id="amount" type="number" class="uk-input" v-model="hrkeluarga.jns_kelamin" style="border-radius:7px" placeholder="0">
                                            <!-- <input id="amount" type="number" class="uk-input" v-model="pencatatankas.jml_bayar" style="border-radius:7px" placeholder="0"> -->
                                        </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-1@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="description">Deskripsi</label>
                                            <div class="uk-form-controls">
                                                <textarea id="description" class="uk-textarea" v-model="hrkeluarga.hubungan" style="border-radius:7px" placeholder="Ketik Deskripsi"></textarea>
                                                <!-- <textarea id="description" class="uk-textarea" v-model="pencatatankas.deskripsi" style="border-radius:7px" placeholder="Ketik Deskripsi"></textarea> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-1@m">
                                        <div class="uk-margin">
                                            <label class="uk-form-label" for="proof">Unggah Bukti Pembayaran</label>
                                            <div class="uk-form-controls">
                                                <input id="proof" type="file" class="uk-input" accept="image/*" @change="uploadProof" style="border-radius:7px; height: 80px;">
                                                <!-- <div class="placeholder-icon">
                                                    <font-awesome-icon :icon="['fas', 'upload']" />
                                                    <span class="placeholder-text">Pilih Lampiran Gambar</span>
                                                </div> -->
                                            </div>
                                        </div>
                                        <div class="uk-flex uk-flex-between" style="margin-top:-2em">
                                            <p class="uk-text-meta uk-margin-small-top">
                                            Format yang didukung: JPEG, PNG, GIF
                                            </p>
                                            <p class="uk-text-meta uk-margin-small-top">
                                            Ukuran maksimal: 5 MB
                                            </p>
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
    name : 'dialog-konfirmasi-batal', 
            
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
            hrkeluarga : {
                client_id:null,
                hr_keluarga_id:null,
                karyawan_id:null,
                nama_keluarga : null,
                hubungan : null,
                tempat_lahir : null,
                tanggal_lahir : null,
                pendidikan : null,
                jns_kelamin : null,
                catatan : null,
                is_aktif : true,          
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        // ...mapGetters('referensi',['pendidikanRefs','isRefExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('hrkeluarga',['createKeluarga','updateKeluarga','deleteKeluarga']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
        },

        closeCatatanKasModal(){
            this.$emit('formCatatanKasClosed',true);
            UIkit.modal('#formCatatanKas').hide();
            return false;
        },

        submitCatatanKas(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.hrkeluarga.karyawan_id = this.karyawan.karyawan_id;
                this.createKeluarga(this.hrkeluarga).then((response) => {
                    if (response.success == true) {
                        alert("Anggota keluarga baru berhasil dibuat.");
                        this.fillKeluarga(response.data);
                        this.isUpdate = true;
                        this.closeCatatanKasModal();
                    }
                })
            }
            console.log(this.hrkeluarga); 
        },

        newPencatatanKas(){
            this.isUpdate = false;
            UIkit.modal('#dialogKonfirmasiBatal').show();
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