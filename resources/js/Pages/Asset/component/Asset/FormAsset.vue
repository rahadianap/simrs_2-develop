<template>
    <!-- Popup Modal tambah data asset -->
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formAsset" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="border-radius:10px;">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitAsset" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" style="border-radius:10px;justify-content:center;text-align:center;" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 1em 0.1em;margin:0;">
                            <h5 class="uk-text-uppercase">TAMBAH DATA ASSET</h5>
                        </div>                                
                    </div>
                    <div style="margin:0 10px;border-top:2px solid #cc0202;">                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small hims-form-subpage-asset" style="margin:30px 0" uk-grid >
                            <div class="uk-width-1-1@s uk-grid-small" style="margin-left:1px;padding-left:1px;" uk-grid>
                                
                                <!-- nomor asset -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nomor Asset*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="assets.nomor_asset" type="text" placeholder="masukkan nomor asset" required>
                                    </div>
                                </div>

                                <!-- nomor seri -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nomor Seri*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="assets.nomor_seri" type="text" placeholder="masukkan nomor seri" required>
                                    </div>
                                </div>

                                <!-- nama asset -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Asset*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="assets.nama_asset" type="text" placeholder="nama asset" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Masa Pakai (bulan)*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="assets.masa_pakai" type="number" placeholder="masa pakai" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Perolehan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="assets.tgl_perolehan" type="date" required>
                                        <!-- <input class="uk-input uk-form-small" v-model="assets.tgl_perolehan" type="text" placeholder="tanggal perolehan" required> -->
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Pemakaian</label>
                                    <div class="uk-form-controls">
                                        <!-- <input class="uk-input uk-form-small" v-model="assets.tgl_pemakaian" type="text" placeholder="pemakaian awal" required> -->
                                        <input class="uk-input uk-form-small" v-model="assets.tgl_pemakaian" type="date" placeholder="tanggal perolehan">
                                
                                    </div>
                                </div>

                                 
                                <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Brand*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="assets.brand" type="text" placeholder="nama brand asset" required>
                                    </div>
                                </div>

                                <!-- lokasi aset -->
                                <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <!-- <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lokasi*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="assets.lokasi" type="text" placeholder="lokasi asset" required>
                                    </div> -->
                                    <search-select
                                    :config = configLokasiSelect
                                    searchUrl = "/assets/locations"
                                    label = "Lokasi*"
                                    placeholder = "pilih lokasi aset"
                                    captionField = "lokasi_nama"
                                    valueField = "lokasi_nama"
                                    :itemListData = lokasiDesc
                                    :value = "assets.lokasi"
                                    v-on:item-select = "lokasiSelected"
                                    ></search-select>
                                </div>

                                <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kategori Asset</label>
                                    <div class="uk-form-controls">
                                        <select class="uk-select uk-form-small" v-model="assets.group_asset" required>
                                            <option v-for="dt in refAsetGroup.ref_data" :value="dt.text">{{ dt.value }}</option>
                                            <!-- <option value="Elektronik">Elektronik</option>
                                            <option value="Properti">Properti</option>
                                            <option value="Alat Medis">Alat Medis</option> -->
                                        </select>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Asset (Rp)*</label>
                                    <div class="uk-form-controls">
                                        <input 
                                            class="uk-input uk-form-small" 
                                            v-model="assets.nilai_asset"
                                            type="text"
                                            v-model.trim="inputRupiah"
                                            @keyup="formatInput"
                                            placeholder="nominal asset dalam rupiah" 
                                        >
                                   </div>
                                </div>

                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status*</label>

                                    <div class="uk-form-controls">
                                        <select class="uk-select uk-form-small" v-model="assets.status" required>
                                            <option v-for="dt in refAsetStatus.ref_data" :value="dt.text">{{ dt.value }}</option>
                                        </select>
                                    </div>
                                </div>    

                                <!-- deskripsi asset -->
                                <div class="uk-width-1-1@m" style="padding-left:1px;padding-right: 10px;">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi Asset*</label>
                                    <div class="uk-form-controls">
                                        <textarea input class="uk-input uk-textarea" v-model="assets.deskripsi" type="text" placeholder="deskripsi asset" required></textarea>
                                    </div>
                                </div>

                                <!-- <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="assets.is_aktif" style="margin-left:5px;"> Data Asset aktif</label>
                                </div> -->

                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-collapse uk-card uk-card-default hims-form-footer uk-column-1-2@m" style="border-radius: 10px;">
                        <div class="uk-width-auto uk-column-1-1@s" style="padding:1em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="assets.is_aktif" style="margin-left:5px;"> Data Asset aktif</label>
                        </div>
                        <div class="uk-grid-match uk-align-center uk-align-right@m uk-column-1-2@s" style="margin-bottom:6px;margin-top:1px">
                            <div class="uk-width-auto" style="padding:0.5em;">
                                <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;border-radius:10px;padding:0 85px;">Tutup</button>                  
                            </div>
                            <div class="uk-width-auto" style="padding:0.5em;">
                                <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;border-radius:10px;padding:0 85px;">Simpan</button>                  
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
import SearchList from '@/Components/SearchList.vue';
import SearchSelect from '@/Components/SearchSelect.vue';


export default {
    name : 'form-asset', 
    components :{
        SearchList,
        SearchSelect,   
    },
    // mixins: [Vue2Filters.mixin],

    data() {
        return {
            inputRupiah: '',
            assetData: [],
            // config select lokasi aset
            configLokasiSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            lokasiDesc : [
                { colName : 'lokasi_nama', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
                { colName : 'lokasi_aset_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            assets : {
                asset_id:null,
                nomor_asset:null,
                nomor_seri:null,
                group_asset : null,
                nama_asset : null,
                deskripsi:null,
                brand : null,
                lokasi:null,
                nilai_asset:null,
                masa_pakai:null,
                is_aktif : true,
                tgl_perolehan : null,
                tgl_pemakaian : null,
                client_id : null,
                created_by : null,
                created_at : null,
                updated_by : null,
                updated_at : null,
                status : null,
            },
            errorMessage: '',
            unitData : {
                asset_id:null,
                nomor_asset:null,
                nomor_seri:null,
                group_asset : null,
                nama_asset : null,
                deskripsi:null,
                brand : null,
                lokasi:null,
                nilai_asset:null,
                masa_pakai:null,
                is_aktif : true,
                tgl_perolehan : null,
                tgl_pemakaian : null,
                client_id : null,
                created_by : null,
                created_at : null,
                updated_by : null,
                updated_at : null,
                status : null,
            },
            lokasi : {
                lokasi_aset_id : null,
            },

            // status_asset : [
            //     { id:'Terverifikasi', text:'Terverifikasi' },
            //     { id:'Tidak Terverifikasi', text:'Tidak Terverifikasi' },
            //     { id:'Tidak Tahu', text:'Tidak Tahu' },
            // ]
        }
    },
    computed : {
        ...mapGetters('refAset',['refAsetStatus','refAsetGroup']),
        ...mapGetters(['errors']),
    },

    retrieveData() {
        this.datas = response.data.data;
        this.filterAndCheckDuplicates();
    },

    mounted() {
        // this.createAsset();
        // this['assets/form-asset']();
        if (this.datas && this.datas.data) {
            this.filterAndCheckDuplicates();
        }

        /* Fungsi Input Rupiah */
        // var rupiah = document.getElementById("rupiah");
        //     rupiah.addEventListener("keyup", function(e) {
        //     // tambahkan 'Rp.' saat ketik di form
        //     rupiah.value = formatRupiah(this.value, "Rp. ");
        //     });

        // function formatRupiah(angka, prefix) {
        // var number_string = angka.replace(/[^,\d]/g, "").toString(),
        //     split = number_string.split(","),
        //     sisa = split[0].length % 3,
        //     rupiah = split[0].substr(0, sisa),
        //     ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // if (ribuan) {
        //     // separator = sisa ? "." : "";
        //     // rupiah += separator + ribuan.join(".");
        //     rupiah += ribuan.join(".");

        // }

        // rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        // return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        // }

    }, 
    
    watch: {
        'assets.nomor_asset'(newValue) {
            this.validateAssetFields();
        },
        'assets.nomor_seri'(newValue) {
            this.validateAssetFields();
        }
    },

    methods : {
        ...mapActions('refAset',['listRefAsetStatus','listRefAsetGroup']),
        ...mapActions('asset',['createAsset','updateAsset','dataAsset']),
        ...mapActions('lokasi'['dataLokasi']),
        ...mapMutations(['CLR_ERRORS']),   
        
        // filter separator rupiah untuk field nilai_asset
        formatInput() {
        let angka = this.inputRupiah.replace(/[^,\d,]/g, ''); // buang karakter non-digit
        let angkaNumerik = parseFloat(angka);    //parse angka separator biar support tipe double precision. Saat input ke db, separator hilang

        if (Number.isFinite(angkaNumerik)) {
            let angka = angkaNumerik.toString(); // angka jadi string, utk ngebalikin separator ke frontend
            let split = angka.split('.');
                let rupiah = split[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                rupiah = rupiah !== '' ? 'Rp. ' + rupiah : '';
                // this.inputRupiah = split[1] !== undefined ? rupiah + '.' + split[1] : rupiah;
                this.inputRupiah = angka.toString;
            } else {
                this.inputRupiah = '';
            }
        },

        // filter validasi duplikat untuk field nomor_asset & nomor_seri 
        validateAssetFields() {
            const nomorAsset = this.assets.nomor_asset;
            const nomorSeri = this.assets.nomor_seri;

            // Cek apakah nomor_asset dan nomor_seri memiliki nilai yang sama
            if (nomorAsset && nomorSeri && nomorAsset === nomorSeri) {
            this.errorMessage = 'Nomor asset dan nomor seri tidak boleh sama';
            } else {
            this.errorMessage = '';
            }
        },



        // init(){
        //     this.listRefAsetStatus().then((response) => {
        //         if (response.success == false) {
        //             alert('ada kesalahan dalam mengubah referensi group aset');
        //         }
        //     });
        // },

        lokasiSelected(data){ 
            if(data) {
                this.assets.lokasi = data.lokasi_nama;
                this.lokasi.lokasi_aset_id = data.lokasi_aset_id;
            }
        },
        
        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formAsset').hide();
            return false;
        },

        submitAsset(){
            this.CLR_ERRORS();
            // this.assets.nilai_asset = this.assets.nilai_asset.replace(',', '.');
            if (this.errorMessage) {
                alert(this.errorMessage);
            return;
            }

            if(!this.isUpdate) {
                this.createAsset(this.assets).then((response) => {
                    if (response.success == true) {
                        alert("Data Asset baru berhasil dibuat.");
                        // this.fillAsset(response.data);
                        this.clearAsset();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        UIkit.modal('#formAsset').hide();
                    }
                })
            }
            else {
                this.updateAsset(this.assets).then((response) => {
                    if (response.success == true) {
                        // this.fillAsset(response.data);
                        alert("Data Asset berhasil diubah.");
                        // this.$emit('saveSucceed',true);
                        // this.isUpdate = true;
                        this.clearAsset();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        UIkit.modal('#editFormAsset').hide();
                        this.closeModal();
                    }
                })
            }            
        },

        newAsset(){
            this.clearAsset();
            this.isUpdate = false;
            UIkit.modal('#formAsset').show();
        },

        editAsset(asset_id){
            console.log(444);
            this.clearAsset();  
            this.assets.nilai_asset = this.assets.nilai_asset.replace(',', '.');          
            this.dataAsset(asset_id).then((response)=>{
                if (response.success == true) {
                    this.fillAsset(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#editFormAsset').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearAsset(){
            this.assets.asset_id = null;
            this.assets.nomor_asset = null;
            this.assets.nomor_seri = null;
            this.assets.group_asset = null;
            this.assets.nama_asset = null;
            this.assets.deskripsi = null;
            this.assets.brand = null;
            this.assets.lokasi = null;
            this.assets.nilai_asset = null;
            this.assets.masa_pakai = null;
            this.assets.is_aktif = true;
            this.assets.tgl_perolehan = null;
            this.assets.tgl_pemakaian = null;
            this.assets.client_id = null;
            this.assets.created_by = true;
            this.assets.updated_by = null;
            this.assets.updated_at = true;
            this.assets.created_at = null;
            this.assets.status = null;
        },

        fillAsset(data){
            // this.assets.asset_id = data.asset_id;
            this.assets.nomor_asset = data.nomor_asset;
            this.assets.nomor_seri = data.nomor_seri;
            this.assets.group_asset = data.group_asset;
            this.assets.nama_asset = data.nama_asset;
            this.assets.deskripsi = data.deskripsi;
            this.assets.brand = data.brand;
            this.assets.lokasi = data.lokasi;
            this.assets.nilai_asset = data.nilai_asset;
            this.assets.masa_pakai = data.masa_pakai;
            this.assets.is_aktif = data.is_aktif;
            this.assets.tgl_perolehan = data.tgl_perolehan;
            this.assets.tgl_pemakaian = data.tgl_pemakaian;
            this.assets.client_id = data.client_id;
            // this.assets.created_by = data.created_by;
            // this.assets.updated_by = data.updated_by;
            this.assets.status = data.status;
        },

    }
}
</script>


<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
}

.hims-form-container label {
    color:#333;
    font-size:12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border:2px solid #cc0202;
}

.uk-button {
    border:2px solid #aaa;
    font-weight:400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-accordion-title {
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    border-radius:5px;
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
}

.hims-form-header {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    top:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-header h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}


.hims-form-subpage-asset {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
} */

</style>