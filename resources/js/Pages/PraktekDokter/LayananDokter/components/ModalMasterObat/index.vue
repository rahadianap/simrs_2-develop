<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalObat" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            
            <h2 class="uk-modal-title">MASTER DATA OBAT</h2>
            <button class="uk-modal-close-default" type="button" uk-close></button>

            <div class="uk-grid-small uk-flex uk-grid" uk-grid>
                <div class="uk-width-1-3@m">
                    <input type="text" style="line-height: 30px; width:100%;" v-model="filterTable.keyword" placeholder="kata pencarian" @change="submitFilter">
                </div>
                <div class="uk-width-2-3@m">
                    <button @click.prevent="submitFilter"  style="height: 35px;width:80px;">Cari</button>
                </div>                                        
            </div>
            <div style="margin-top:1em;">
                <table class="uk-table uk-table-striped">
                    <thead>
                        <tr>
                            <th style="font-size: 14px; font-weight: 700;">Kode</th>
                            <th style="font-size: 14px; font-weight: 700;">Nama Obat</th>
                            <th style="font-size: 14px; font-weight: 700;">Aturan</th>
                            <th style="width:70px; font-size: 14px; font-weight: 700; text-align: center;">...</th>
                        </tr>
                        <tr v-if="isLoading">
                            <th style="text-align: center; background-color: #fff;padding:0.5em;" :colspan="4">
                                <p style="font-size:12px; font-weight: 700; font-style:italic;padding:0.5em; color:black;">
                                    <span uk-spinner></span>
                                    sedang memproses data
                                </p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <tr>
                            <td colspan="2" style="padding:0.2em; margin:0;">
                                <input type="text" v-model="produk.produk_nama" placeholder="nama obat" style="height: 30px;width:99%;">
                            </td>                            
                            <td style="padding:0.2em; margin:0;">
                                <input type="text" v-model="produk.aturan_pakai" placeholder="aturan" style="height: 30px;width:99%;">
                            </td>                            
                            <td style="width:70px; padding:0.2em; margin:0; text-align: center;">
                                <button @click.prevent="saveObat" style="height: 35px;width:60px;">Simpan</button>
                            </td>
                        </tr>

                        <tr v-if="productLists" v-for="item in productLists.data">
                            <td style="padding:0.2em;">{{ item.produk_id }}</td>
                            <td style="padding:0.2em;">{{ item.produk_nama }}</td>
                            <td style="padding:0.2em;">{{ item.aturan_pakai }}</td>
                            <td style="width:70px; text-align: center; padding:0.1em;">
                                <a href="#" @click.prevent="updateData(item)" ><span uk-icon="pencil"></span></a>
                                <a href="#" @click.prevent="deleteData(item)" style="margin-left:5px;"><span uk-icon="trash"></span></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'modal-master-obat',
    components : {
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('icd10',['icdDiagnoses']),
        ...mapGetters('produk',['productLists']),
    },
    data() {
        return {
            isLoading : false,
            dataIcd : {
                kode_icd : null,
                client_id : null,
                nama_icd : null,
                no_dtd : null,
                kata_kunci : null,
                is_valid_icd : true,
                is_aktif : true,
            },

            filterTable : {
                is_aktif : true,
                keyword : null,
                page : 1,
                per_page : 20,
            },

            produk : {
                client_id : null,
                produk_id : null,
                barcode : null,
                produk_nama : null,
                jenis_produk : 'MEDIS',
                satuan : null, //satuan stock persediaan, saat stock sudah dibuat, tidak dapat dihapus.
                satuan_beli : null, //satuan yang digunakan untuk pembelian, hanya sebagai acuan.
                konversi : null,
                harga_beli : null, 
                hna : null, //harga pokok sebelum margin dan ppn
                het : null, //harga eceran tertinggi
                is_hasil_produksi : false, //item adalah hasil produksi
                is_jual : true , //item boleh dijual
                is_pengadaan : true, //item dapat dibeli
                is_sterilisasi : false, // item butuh sterilisasi
                is_kontrol_kadaluarsa : true, //item harus dikontrol kadaluarsa
                is_kontrol_stok : true, //item perlu di kontrol jumlah persediaan
                is_laundry_item : false, //item ini digunakan dan diperlukan untuk di laundry
                cara_pakai : null, // cara penggunaan sebelum dikonsumsi
                signa : null,
                aturan_pakai : null, // signa atau cara pakai
                label_obat : null, //label obat : Obat dalam, obat luar, dsbnya
                klasifikasi : null, //contoh : Narkotik, obat herbal, Obat keras,dsb
                sediaan : null, // contoh : puyer , obat tetes, dsbnya
                golongan : null, //fornas,formularium,generic,non generic
                komposisi : null,
                kontra_indikasi : null,
                vendor_id : null,
                vendor : null,
                pabrikan_id : null,
                pabrikan : null,
                meta_data : null,
                produk_akun : null,
                produk_akun_id : null,
                kelompok_vclaim_id : null,
                kelompok_vclaim : null,
                kelompok_tagihan_id : null,
                kelompok_tagihan : null,
                jenis_etiket : null,
                is_aktif : true,
            },

            searchUrl : '/icd10'
        }
    },
    methods : {
        ...mapActions('icd10',['listIcdDiagnosa','deleteIcdDiagnosa','dataIcdDiagnosa','createIcdDiagnosa','updateIcdDiagnosa']),
        ...mapActions('produk',['listProdukMedis','deleteProduk','dataProduk','createProduk','updateProduk']),
        ...mapActions('importExcel',['importExcelICD10']),
        ...mapMutations(['CLR_ERRORS']),

        showModal(){
            this.submitFilter();
            this.clearProduk();
            UIkit.modal('#modalObat').show();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.fillProduk(data);
            this.isUpdate = true;
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus Obat ${data.produk_nama}. Lanjutkan ?`)){
                this.deleteProduk(data.produk_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.submitFilter();
                        this.clearProduk();
                    }
                    else { 
                        alert (response.message) 
                    }
                });
            };
        },

        submitFilter(){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.listProdukMedis(this.filterTable).then((response) => {
                if (response.success == true) {
                    this.isLoading = false;
                    //console.log(this.icdDiagnoses);
                }
                else { 
                    alert (response.message); 
                    this.isLoading = false;
                }
            });
        },

        saveObat(){
            if(this.produk.produk_nama == null || this.produk.produk_nama == '' ) {
                alert('nama obat tidak boleh kosong');
                return false;
            }
            else {
                this.submitDataObat();
            }
        },

        submitDataObat(){
            this.CLR_ERRORS();
            if(this.isUpdate) {
                this.isLoading = true;
                this.updateProduk(this.produk).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        this.clearProduk();
                        this.submitFilter();
                        this.$emit('dataChanged',true);
                    }
                    else { alert (response.message) }
                });
            }
            else {
                this.isLoading = true;
                this.createProduk(this.produk).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        this.clearProduk();
                        this.submitFilter();
                        this.$emit('dataChanged',true);
                    }
                    else { alert (response.message) }
                });
            }
        },

        clrDataIcd(){
            this.isUpdate = false;
            this.dataIcd.kode_icd = null;
            this.dataIcd.client_id = null;
            this.dataIcd.nama_icd = null;
            this.dataIcd.no_dtd = null;
            this.dataIcd.kata_kunci = null;
            this.dataIcd.is_valid_icd = true;
            this.dataIcd.is_aktif = true;
        },

        fillDataIcd(data) {
            this.isUpdate = true;
            this.dataIcd.kode_icd = data.kode_icd;
            this.dataIcd.client_id = data.client_id;
            this.dataIcd.nama_icd = data.nama_icd;
            this.dataIcd.no_dtd = data.no_dtd;
            this.dataIcd.kata_kunci = data.kata_kunci;
            this.dataIcd.is_valid_icd = data.is_valid_icd;
            this.dataIcd.is_aktif = data.is_aktif;
        },

        clearProduk(){
            this.produk.client_id = null;
            this.produk.produk_id = null;
            this.produk.barcode = null;
            this.produk.produk_nama = null;
            this.produk.jenis_produk = 'MEDIS';
            this.produk.satuan = null;
            this.produk.satuan_beli = null;
            this.produk.konversi = 1;
            this.produk.harga_beli = 0; 
            this.produk.hna = 0;
            this.produk.het = 0;
            this.produk.is_hasil_produksi = false;
            this.produk.is_jual = true ;
            this.produk.is_pengadaan = true;
            this.produk.is_sterilisasi = false;
            this.produk.is_kontrol_kadaluarsa = false;
            this.produk.is_kontrol_stok = false;
            this.produk.is_laundry_item = false;
            this.produk.cara_pakai = null;
            this.produk.signa = null;
            this.produk.aturan_pakai = null;
            this.produk.label_obat = null;
            this.produk.klasifikasi = null;
            this.produk.sediaan = null;
            this.produk.golongan = null;
            this.produk.komposisi = null;
            this.produk.kontra_indikasi = null;
            this.produk.vendor_id = null;
            this.produk.vendor = null;
            this.produk.pabrikan_id = null;
            this.produk.pabrikan = null;
            this.produk.meta_data = null;
            this.produk.produk_akun = null;
            this.produk.kelompok_vclaim_id = null;
            this.produk.kelompok_vclaim = null;
            this.produk.kelompok_tagihan_id = null;
            this.produk.kelompok_tagihan = null;
            this.produk.jenis_etiket = null;
            this.produk.is_aktif = true;
            this.isUpdate = false;
        },

        fillProduk(data) {
            this.produk.client_id = null;
            this.produk.produk_id = data.produk_id;
            this.produk.barcode = data.barcode;
            this.produk.produk_nama = data.produk_nama;
            this.produk.jenis_produk = data.jenis_produk;
            this.produk.satuan = data.satuan;
            this.produk.satuan_beli = data.satuan_beli;
            this.produk.konversi = data.konversi;
            this.produk.harga_beli = data.harga_beli; 
            this.produk.hna = data.hna;
            this.produk.het = data.het;
            this.produk.is_hasil_produksi = data.is_hasil_produksi;
            this.produk.is_jual = data.is_jual;
            this.produk.is_pengadaan = data.is_pengadaan;
            this.produk.is_sterilisasi = data.is_sterilisasi;
            this.produk.is_kontrol_kadaluarsa = data.is_kontrol_kadaluarsa;
            this.produk.is_kontrol_stok = data.is_kontrol_stok;
            this.produk.is_laundry_item = data.is_laundry_item;
            this.produk.cara_pakai = data.cara_pakai;
            this.produk.signa = data.signa;
            this.produk.aturan_pakai = data.aturan_pakai;
            this.produk.label_obat = data.label_obat;
            this.produk.klasifikasi = data.klasifikasi;
            this.produk.sediaan = data.sediaan;
            this.produk.golongan = data.golongan;
            this.produk.komposisi = data.komposisi;
            this.produk.kontra_indikasi = data.kontra_indikasi;
            this.produk.vendor_id = data.vendor_id;
            this.produk.vendor = data.vendor;
            this.produk.pabrikan_id = data.pabrikan_id;
            this.produk.pabrikan = data.pabrikan;
            this.produk.meta_data = data.meta_data;
            this.produk.produk_akun = data.produk_akun;
            this.produk.produk_akun_id = data.produk_akun_id;
            this.produk.kelompok_vclaim_id = data.kelompok_vclaim_id;
            this.produk.kelompok_vclaim = data.kelompok_vclaim;
            this.produk.kelompok_tagihan_id = data.kelompok_tagihan_id;
            this.produk.kelompok_tagihan = data.kelompok_tagihan;
            this.produk.jenis_etiket = data.jenis_etiket;
            this.produk.is_aktif = true;
        },
    }
    
}
</script>