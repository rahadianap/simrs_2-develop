<template>
    <div class="uk-container" style="min-height:50vh; background-color:#fff;">
        <form action="" @submit.prevent="submitPersalinan" >
            <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                    <h5 class="uk-text-uppercase">DATA PASIEN</h5>
                </div>                                
                <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                    <button type="button" @click.prevent="closeFormPasien" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                </div>
                <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                    <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                </div>
            </div>
            <div> 
                <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                    <div uk-spinner="ratio : 2"></div>
                    <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                </div>
                
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>              
                <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">                        
                    <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA BAYI</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            detail data bayi baru lahir.
                        </p>
                    </div>
                    <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                        <div class="uk-width-1-1" style="margin:0;padding:0;"></div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                            <div class="uk-inline uk-width-1-1">
                                <select v-model="persalinan.jk_bayi" class="uk-select uk-form-small" required style="color:black;">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Berat Badan Bayi (Kg)</label>
                            <div class="uk-inline uk-width-1-1">
                                <input class="uk-input uk-form-small" v-model="persalinan.bb_bayi" type="number" style="color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Panjang Badan Bayi (Cm)</label>
                            <div class="uk-inline uk-width-1-1">
                                <input class="uk-input uk-form-small" v-model="persalinan.tb_bayi" type="number" style="color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lingkar Kepala (Cm)</label>
                            <div class="uk-inline uk-width-1-1">
                                <input class="uk-input uk-form-small" v-model="persalinan.lkr_kepala" type="number" style="color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lingkar Dada (Cm)</label>
                            <div class="uk-inline uk-width-1-1">
                                <input class="uk-input uk-form-small" v-model="persalinan.lkr_dada" type="number" style="color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Frekuensi Pernapasan (x / Menit)</label>
                            <div class="uk-inline uk-width-1-1">
                                <input class="uk-input uk-form-small" v-model="persalinan.pernapasan" type="number" style="color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Detak Jantung (x / Menit)</label>
                            <div class="uk-inline uk-width-1-1">
                                <input class="uk-input uk-form-small" v-model="persalinan.detak_jantung" type="number" style="color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kondisi Lahir</label>
                            <div class="uk-inline uk-width-1-1">
                                <input class="uk-input uk-form-small" v-model="persalinan.kondisi_lahir" type="number" style="color:black;">
                            </div>
                        </div>
                    </div>
                </div>                     
            </div>
        </form>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Pages/MasterData/Pasien/components/SearchList.vue';

export default {
    name : 'form-persalinan', 
    props : {
        urlBack : { type:String, required:false, default:null }
    },
    components : {
        SearchSelect,
        SearchList,
    },
    data() {
        return {            
            //urlBack : null,
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : true,
            },

            configTinggalSelect : {
                disabled : false, 
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            propDesc : [
                { colName : 'propinsi', labelData : '', isTitle : true },
                { colName : 'propinsi_id', labelData : '', isTitle : false },
            ],
            
            kabDesc : [
                { colName : 'kota', labelData : '', isTitle : true },
                { colName : 'propinsi', labelData : 'prop. ', isTitle : false },
            ],
            kecDesc : [
                { colName : 'kecamatan', labelData : '', isTitle : true },
                { colName : 'kota', labelData : 'kab/kota. ', isTitle : false },
                { colName : 'propinsi', labelData : 'prop. ', isTitle : false },
            ],
            kelDesc : [
                { colName : 'kelurahan', labelData : '', isTitle : true },
                { colName : 'kodepos', labelData : 'kodepos. ', isTitle : false },
                { colName : 'kecamatan', labelData : 'kec. ', isTitle : false },
            ],

            penjaminDesc : [
                { colName : 'penjamin_nama', labelData : '', isTitle : true },
                { colName : 'penjamin_id', labelData : '', isTitle : false },
            ],
            isUpdate : true,

            persalinan : {
                jk_bayi : null,
                bb_bayi : null,
                tb_bayi : null,
                lkr_kepala : null,
                lkr_dada : null,
                pernapasan : null,
                detak_jantung : null,
                kondisi_lahir : null
            },

            pasien : {
                client_id:null,
                pasien_id:null,
                no_rm : null,
                is_pasien_luar : false,
                nama_pasien : null,
                salut : null,
                nama_lengkap : null,
                nik : null,
                no_kk : null,
                jns_kelamin : null,
                tgl_lahir : null,
                tempat_lahir : null,
                jns_penjamin : null,
                penjamin_id : null,
                no_kepesertaan : null,
                tgl_terakhir_periksa : null,
                is_hamil : false,
                is_meninggal : false,
                tgl_meninggal : null,
                penyebab_kematian : null,
                tgl_daftar : null,
                url_foto : null,
                is_aktif : true,
                detail : {
                    gol_darah : null,
                    rhesus : null,
                    pendidikan : null,
                    pekerjaan : null,
                    agama : null,
                    kebangsaan : null,
                    suku_bangsa : null,
                    no_telepon : null,
                    alamat_email : null,
                    no_kontak_darurat : null,
                    nama_kontak_darurat : null,
                    hub_kontak_darurat : null,
                    is_aktif : true,
                },
                alamat : {
                    alamat : null,
                    propinsi_id : null,
                    propinsi : null,
                    kota_id : null,
                    kota : null,
                    kecamatan_id : null,
                    kecamatan : null,
                    kelurahan_id : null,
                    kelurahan : null,
                    kodepos : null,
                    rt : null,
                    rw : null,
                    is_ktp_sama_dgn_tinggal : false,
                    alamat_tinggal : null,
                    propinsi_tinggal_id : null,
                    propinsi_tinggal : null,
                    kota_tinggal_id : null,
                    kota_tinggal : null,
                    kecamatan_tinggal_id : null,
                    kecamatan_tinggal : null,
                    kelurahan_tinggal_id : null,
                    kelurahan_tinggal : null,
                    kodepos_tinggal : null,
                    rt_tinggal : null,
                    rw_tinggal : null,
                    is_aktif : true,
                },
                keluarga : {
                    status_perkawinan : null,
                    nama_pasangan : null,
                    nama_ayah : null,
                    nama_ibu : null,
                    pekerjaan_pasangan : null,
                    pekerjaan_ayah : null,
                    pekerjaan_ibu : null,
                    is_aktif : true,
                },
                alergi : [],
                persalinan: []
            },

            registrasi : {
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                client_id : null,
                is_rujukan_int : false,
                jns_registrasi : 'OPERASI',
                tgl_transaksi : this.getTodayDate(),
                
                tgl_operasi : this.getTodayDate(),
                jam_operasi : null,

                tgl_selesai : this.getTodayDate(),
                jam_selesai : null,

                tgl_anestesi : null,
                jam_anestesi : null,
                
                dokter_unit_id : null,
                dokter_id : null,
                dokter_nama : null,
                
                dokter_pengirim_id: null,
                dokter_pengirim: null,
                
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                bed_id : null,
                no_bed : null,
                
                unit_asal_id : null,
                unit_asal : null,
                ruang_asal_id : null,
                ruang_asal : null,
                bed_asal_id : null,
                no_asal_bed : null,

                cara_registrasi : null,
                asal_pasien : null,
                ket_asal_pasien : null,
                
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                tempat_lahir : null,
                tgl_lahir : null,
                nik : null,
                jns_kelamin : null,
                is_pasien_baru : null,
                is_pasien_luar : false,                
                jns_penjamin : null,
                penjamin_id : null,                
                penjamin_nama : null,

                kelas_harga : null,
                buku_harga_id : null,
                buku_harga : null,
                total : 0,

                is_bpjs : false,
                status : null,
                is_aktif : true,
                client_id : null,
                reff_trx_id : null,
                is_realisasi : false,
                
                tindakan_id : null,
                tindakan_nama : null,
                diagnosa_pra : null,
                diagnosa_pasca : null,
                jenis_operasi : null,
                skala_operasi : null,

                nama_ibu : null,
                nik_ibu : null,
                rm_ibu : null,
                pasien_id : null,
                nama_ayah : null,
                nik_ayah : null,
                alamat : null,

                persalinan : [],

                detail : [],
                tim_operasi : [],
            },

            kota :[],
            kecamatan: [],
            kelurahan: [],
            kotaTinggal : [],
            kecamatanTinggal : [],
            kelurahanTinggal : [],

            alergiData : {
                pasien_alergi_id : null,
                pasien_id : null,
                jns_alergi : null,
                tgl_kejadian_awal : null,
                akibat : null,
                catatan_alergi : null,
                is_aktif : true,
            } 
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('propinsi',['propinsiLists']),
        ...mapGetters('asuransi',['asuransiLists']),
        
        ...mapGetters('referensi',[
            'salutRefs',
            'rhesusRefs',
            'golDarahRefs',            
            'agamaRefs',
            'pekerjaanRefs',
            'pendidikanRefs',
            'jenisPenjaminRefs',
            'hubKeluargaRefs',
            'statusKawinRefs',
            'sukuBangsaRefs',
            'kebangsaanRefs',
            'caraPulangRefs',
            'statusPulangRefs',
            'asalPasienRefs',
            'jenisAlergiRefs',
            'statusBedInapRefs',
            'kasusIgdRefs',
            'jenisPelayananRefs',
            'isRefExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('pasien',['createPasien','updatePasien','dataPasien','uploadDokterAvatar']),
        ...mapActions('asuransi',['listAsuransi']),
        ...mapActions('propinsi',['listPropinsi']),
        ...mapActions('kabupaten',['listKabupaten']),
        ...mapActions('kecamatan',['listKecamatan']),
        ...mapActions('kelurahan',['listKelurahan']),
        
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let param = {per_page:'ALL'};
            this.listPropinsi(param);            
            this.listAsuransi({per_page:20});

            if(!this.isRefExist) { 
                this.listReferensi(); 
            };
        },

        submitPersalinan() {
            alert('abc');
            let addedVal = JSON.parse(JSON.stringify(this.persalinan));

            this.registrasi.persalinan.push(addedVal);
            console.log(this.registrasi.persalinan);
            this.modalClosed();
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

        modalClosed(){
            // this.clearRegistrasi();
            this.isUpdate = false;
            this.$emit('modalPersalinanClosed',this.registrasi.persalinan);
            UIkit.modal('#modalPersalinan').hide();
        },

        penjaminSelected(data) {
            this.pasien.penjamin_id = null;
            if(data) {
                this.pasien.penjamin_id = data.penjamin_id;
            }
        },

        ambilPenjaminList(){
            this.pasien.penjamin_id = null;
            this.listAsuransi({per_page:'ALL',jns_penjamin:this.pasien.jns_penjamin, is_aktif:true });
        },

        closeFormPasien(){
            let lastPasienData = JSON.parse(JSON.stringify(this.pasien));
            if(this.urlBack) {
                this.clearPasien();
                this.$router.push(this.urlBack);
            }
            else {
                this.clearPasien();
                this.$emit('formPatientClosed',lastPasienData);
            }
        },

        isAlamatKtpTinggalSama(){
            this.configTinggalSelect.disabled = this.pasien.alamat.is_ktp_sama_dgn_tinggal;
            if(this.pasien.alamat.is_ktp_sama_dgn_tinggal == true) {
                this.pasien.alamat.rt_tinggal = null;
                this.pasien.alamat.rw_tinggal = null;
                this.pasien.alamat.alamat_tinggal = null;
                this.pasien.alamat.propinsi_tinggal_id = null;
                this.pasien.alamat.kota_tinggal_id = null;
                this.pasien.alamat.kecamatan_tinggal_id = null;
                this.pasien.alamat.kelurahan_tinggal_id = null;
                this.pasien.alamat.kodepos_tinggal = null;                
            }
        },

        propSelected(data) {
            this.pasien.alamat.kota_id = null;
            this.pasien.alamat.kecamatan_id = null;
            this.pasien.alamat.kelurahan_id = null;
            this.pasien.alamat.kodepos = null;
            this.kecamatan = [];
            this.kelurahan = [];
            this.kota = [];  
            if(data){
                this.pasien.alamat.propinsi_id = data.propinsi_id;
                
                let prm = {per_page:'ALL',propinsi: data.propinsi_id };                            
                this.listKabupaten(prm).then((response) => {
                    if (response.success == true) {
                        this.kota = JSON.parse(JSON.stringify(response.data)); 
                    }
                })
            }      
        },

        kabSelected(data) {            
            this.pasien.alamat.kecamatan_id = null;
            this.pasien.alamat.kelurahan_id = null;
            this.pasien.alamat.kodepos = null;
            this.kecamatan = [];
            this.kelurahan = [];

            if(data) {                 
                this.pasien.alamat.kota_id = data.kota_id; 
                let prm = {per_page:'ALL',kota: data.kota_id };
                this.listKecamatan(prm).then((response) => {
                    if (response.success == true) {
                        this.kecamatan = JSON.parse(JSON.stringify(response.data)); 
                    }
                })
            }
        },

        kecSelected(data) {
            this.pasien.alamat.kelurahan_id = null;
            this.pasien.alamat.kodepos = null;
            this.kelurahan = [];
                
            if(data) {                 
                this.pasien.alamat.kecamatan_id = data.kecamatan_id;   

                let prm = {per_page:'ALL',kecamatan: data.kecamatan_id };
                this.listKelurahan(prm).then((response) => {
                    if (response.success == true) {
                        this.kelurahan = JSON.parse(JSON.stringify(response.data)); 
                    }
                })       
            }
        },

        kelSelected(data) {
            this.pasien.alamat.kelurahan_id = null;
            this.pasien.alamat.kodepos = null;
            if(data){ 
                this.pasien.alamat.kelurahan_id = data.kelurahan_id; 
                this.pasien.alamat.kelurahan = data.kelurahan; 
                this.pasien.alamat.kodepos = data.kodepos;
                if(this.pasien.alamat.is_ktp_sama_dgn_tinggal) {
                    this.pasien.alamat.kelurahan_tinggal_id = data.kelurahan_id;
                    this.pasien.alamat.kelurahan_tinggal = data.kelurahan;
                    this.pasien.alamat.kodepos_tinggal = data.kodepos;
                }
            }
        },

        propTinggalSelected(data) {  
            this.pasien.alamat.kota_tinggal_id = null;
            this.pasien.alamat.kecamatan_tinggal_id = null;
            this.pasien.alamat.kelurahan_tinggal_id = null;
            this.pasien.alamat.kodepos_tinggal = null;
            this.kecamatanTinggal = [];
            this.kelurahanTinggal = [];
            this.kotaTinggal = [];  
            if(data){
                this.pasien.alamat.propinsi_tinggal_id = data.propinsi_id;
                let prm = {per_page:'ALL',propinsi: data.propinsi_id };                            
                this.listKabupaten(prm).then((response) => {
                    if (response.success == true) {
                        this.kotaTinggal = JSON.parse(JSON.stringify(response.data)); 
                    }
                })
            }  
        },

        kabTinggalSelected(data) {
            this.pasien.alamat.kecamatan_tinggal_id = null;
            this.pasien.alamat.kelurahan_tinggal_id = null;
            this.pasien.alamat.kodepos_tinggal = null;
            this.kecamatanTinggal = [];
            this.kelurahanTinggal = [];

            if(data) {                 
                this.pasien.alamat.kota_tinggal_id = data.kota_id; 
                let prm = {per_page:'ALL',kota: data.kota_id };
                this.listKecamatan(prm).then((response) => {
                    if (response.success == true) {
                        this.kecamatanTinggal = JSON.parse(JSON.stringify(response.data)); 
                    }
                })
            }
        },

        kecTinggalSelected(data) {
            this.pasien.alamat.kelurahan_tinggal_id = null;
            this.pasien.alamat.kodepos_tinggal = null;
            this.kelurahanTinggal = [];   
            if(data) {                 
                this.pasien.alamat.kecamatan_tinggal_id = data.kecamatan_id;   
                let prm = {per_page:'ALL',kecamatan: data.kecamatan_id };
                this.listKelurahan(prm).then((response) => {
                    if (response.success == true) {
                        this.kelurahanTinggal = JSON.parse(JSON.stringify(response.data)); 
                    }
                })       
            }
        },

        kelTinggalSelected(data) {
            this.pasien.alamat.kelurahan_tinggal_id = null;
            this.pasien.alamat.kodepos_tinggal = null;
            if(data){ 
                this.pasien.alamat.kelurahan_tinggal_id = data.kelurahan_id; 
                this.pasien.alamat.kodepos_tinggal = data.kodepos;
            }
        },

        submitPasien(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createPasien(this.pasien).then((response) => {
                    if (response.success == true) {
                        alert("Data pasien baru berhasil dibuat.");
                        this.fillPasien(response.data);
                        this.$emit('saveSucceed',response.data);
                        this.isUpdate = true;
                        this.closeFormPasien();
                    }
                })
            }
            else {
                this.updatePasien(this.pasien).then((response) => {
                    if (response.success == true) {
                        this.fillPasien(response.data);
                        alert("Data pasien berhasil diubah.");
                        this.$emit('saveSucceed',response.data);
                        this.isUpdate = true;
                    }
                })
            }
        },

        redirectEditPasien(pasienId){
            this.$router.push(`/master/pasien/list/${pasienId}`);
        },

        newPasien(){
            this.clearPasien();
            this.isUpdate = false;
            this.isLoading = false;
        },

        editPasien(pasienId){
            this.isLoading = true;
            this.dataPasien(pasienId).then((response)=>{
                if (response.success == true) {
                    this.fillPasien(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                    //this.$router.push('/master/pasien');
                }
                this.isLoading = false;
            })
        },   

        clearPasien(){
            this.pasien.client_id = null;
            this.pasien.pasien_id = null;
            this.pasien.no_rm = null;
            this.pasien.is_pasien_luar = false;
            this.pasien.nama_pasien = null;
            this.pasien.salut = null;
            this.pasien.nama_lengkap = null;
            this.pasien.nik = null;
            this.pasien.no_kk = null;
            this.pasien.jns_kelamin = null;
            this.pasien.tgl_lahir = null;
            this.pasien.tempat_lahir = null;
            this.pasien.jns_penjamin = null;
            this.pasien.penjamin_id = null;
            this.pasien.no_kepesertaan = null;
            this.pasien.tgl_terakhir_periksa = null;
            this.pasien.is_hamil = false;
            this.pasien.is_meninggal = false;
            this.pasien.tgl_meninggal = null;
            this.pasien.penyebab_kematian = null;
            this.pasien.tgl_daftar = null;
            this.pasien.url_foto = null;
            this.pasien.is_aktif = true;

            this.pasien.detail.gol_darah = null;
            this.pasien.detail.rhesus = null;
            this.pasien.detail.pendidikan = null;
            this.pasien.detail.pekerjaan = null;
            this.pasien.detail.agama = null;
            this.pasien.detail.kebangsaan = null;
            this.pasien.detail.suku_bangsa = null;
            this.pasien.detail.no_telepon = null;
            this.pasien.detail.alamat_email = null;
            this.pasien.detail.no_kontak_darurat = null;
            this.pasien.detail.nama_kontak_darurat = null;
            this.pasien.detail.hub_kontak_darurat = null;
            
            this.pasien.alamat.alamat = null;
            this.pasien.alamat.propinsi_id = null;
            this.pasien.alamat.kota_id = null;
            this.pasien.alamat.kecamatan_id = null;
            this.pasien.alamat.kelurahan_id = null;
            this.pasien.alamat.propinsi = null;
            this.pasien.alamat.kota = null;
            this.pasien.alamat.kecamatan = null;
            this.pasien.alamat.kelurahan = null;
            this.pasien.alamat.kodepos = null;
            this.pasien.alamat.rt = null;
            this.pasien.alamat.rw = null;
            
            this.pasien.alamat.is_ktp_sama_dgn_tinggal = false;
            this.pasien.alamat.alamat_tinggal = null;
            this.pasien.alamat.propinsi_tinggal_id = null;
            this.pasien.alamat.kota_tinggal_id = null;
            this.pasien.alamat.kecamatan_tinggal_id = null;
            this.pasien.alamat.kelurahan_tinggal_id = null;

            this.pasien.alamat.propinsi_tinggal = null;
            this.pasien.alamat.kota_tinggal = null;
            this.pasien.alamat.kecamatan_tinggal = null;
            this.pasien.alamat.kelurahan_tinggal = null;

            this.pasien.alamat.kodepos_tinggal = null;
            this.pasien.alamat.rt_tinggal = null;
            this.pasien.alamat.rw_tinggal = null;
            this.pasien.alamat.is_aktif = true;
            
            this.pasien.keluarga.status_perkawinan = null;
            this.pasien.keluarga.nama_pasangan = null;
            this.pasien.keluarga.nama_ayah = null;
            this.pasien.keluarga.nama_ibu = null;
            this.pasien.keluarga.pekerjaan_pasangan = null;
            this.pasien.keluarga.pekerjaan_ayah = null;
            this.pasien.keluarga.pekerjaan_ibu = null;
            
            this.pasien.alergi = [];
        },

        fillPasien(data) {
            this.pasien.client_id = null;
            this.pasien.pasien_id = data.pasien_id;
            this.pasien.no_rm = data.no_rm;
            this.pasien.is_pasien_luar = data.is_pasien_luar;
            this.pasien.nama_pasien = data.nama_pasien;
            this.pasien.salut = data.salut;
            this.pasien.nama_lengkap = data.nama_lengkap;
            this.pasien.nik = data.nik;
            this.pasien.no_kk = data.no_kk;
            this.pasien.jns_kelamin = data.jns_kelamin;
            this.pasien.tgl_lahir = data.tgl_lahir;
            this.pasien.tempat_lahir = data.tempat_lahir;
            this.pasien.jns_penjamin = data.jns_penjamin;
            this.pasien.penjamin_id = data.penjamin_id;
            this.pasien.no_kepesertaan = data.no_kepesertaan;
            this.pasien.tgl_terakhir_periksa = data.tgl_terakhir_periksa;
            this.pasien.is_hamil = data.is_hamil;
            this.pasien.is_meninggal = data.is_meninggal;
            this.pasien.tgl_meninggal = data.tgl_meninggal;
            this.pasien.penyebab_kematian = data.penyebab_kematian;
            this.pasien.tgl_daftar = data.tgl_daftar;
            this.pasien.url_foto = data.url_foto;
            this.pasien.is_aktif = data.is_aktif;
            this.pasien.alergi = data.alergi;

            if(data.detail) {
                this.pasien.detail.gol_darah = data.detail.gol_darah;
                this.pasien.detail.rhesus = data.detail.rhesus;
                this.pasien.detail.pendidikan = data.detail.pendidikan;
                this.pasien.detail.pekerjaan = data.detail.pekerjaan;
                this.pasien.detail.agama = data.detail.agama;
                this.pasien.detail.kebangsaan = data.detail.kebangsaan;
                this.pasien.detail.suku_bangsa = data.detail.suku_bangsa;
                this.pasien.detail.no_telepon = data.detail.no_telepon;
                this.pasien.detail.alamat_email = data.detail.alamat_email;
                this.pasien.detail.no_kontak_darurat = data.detail.no_kontak_darurat;
                this.pasien.detail.nama_kontak_darurat = data.detail.nama_kontak_darurat;
                this.pasien.detail.hub_kontak_darurat = data.detail.hub_kontak_darurat;
            }
            
            if(data.keluarga) {
                this.pasien.keluarga.status_perkawinan = data.keluarga.status_perkawinan;
                this.pasien.keluarga.nama_pasangan = data.keluarga.nama_pasangan;
                this.pasien.keluarga.nama_ayah = data.keluarga.nama_ayah;
                this.pasien.keluarga.nama_ibu = data.keluarga.nama_ibu;
                this.pasien.keluarga.pekerjaan_pasangan = data.keluarga.pekerjaan_pasangan;
                this.pasien.keluarga.pekerjaan_ayah = data.keluarga.pekerjaan_ayah;
                this.pasien.keluarga.pekerjaan_ibu = data.keluarga.pekerjaan_ibu;
            }
                        
            if(data.alamat){

                this.pasien.alamat.alamat = data.alamat.alamat;
                this.pasien.alamat.propinsi_id = data.alamat.propinsi_id;
                this.pasien.alamat.propinsi = data.alamat.propinsi;
                
                this.pasien.alamat.kota_id = data.alamat.kota_id;
                this.pasien.alamat.kota = data.alamat.kota;
                
                this.pasien.alamat.kecamatan_id = data.alamat.kecamatan_id;
                this.pasien.alamat.kecamatan = data.alamat.kecamatan;
                
                this.pasien.alamat.kelurahan_id = data.alamat.kelurahan_id;
                this.pasien.alamat.kelurahan = data.alamat.kelurahan;
                
                this.pasien.alamat.kodepos = data.alamat.kodepos;
                this.pasien.alamat.rt = data.alamat.rt;
                this.pasien.alamat.rw = data.alamat.rw;
                
                this.pasien.alamat.is_ktp_sama_dgn_tinggal = data.alamat.is_ktp_sama_dgn_tinggal;
                if(!data.alamat.is_ktp_sama_dgn_tinggal) {
                    this.pasien.alamat.alamat_tinggal = data.alamat.alamat_tinggal;
                    this.pasien.alamat.propinsi_tinggal_id = data.alamat.propinsi_tinggal_id;
                    this.pasien.alamat.propinsi_tinggal = data.alamat.propinsi_tinggal;
                    this.pasien.alamat.kota_tinggal_id = data.alamat.kota_tinggal_id;
                    this.pasien.alamat.kota_tinggal = data.alamat.kota_tinggal;
                    this.pasien.alamat.kecamatan_tinggal_id = data.alamat.kecamatan_tinggal_id;
                    this.pasien.alamat.kecamatan_tinggal = data.alamat.kecamatan_tinggal;
                    this.pasien.alamat.kelurahan_tinggal_id = data.alamat.kelurahan_tinggal_id;
                    this.pasien.alamat.kelurahan_tinggal = data.alamat.kelurahan_tinggal;
                    this.pasien.alamat.kodepos_tinggal = data.alamat.kodepos_tinggal;
                    this.pasien.alamat.rt_tinggal = data.alamat.rt_tinggal;
                    this.pasien.alamat.rw_tinggal = data.alamat.rw_tinggal;
                }

                this.configTinggalSelect.disabled = data.alamat.is_ktp_sama_dgn_tinggal; 

                if(data.alamat.propinsi_id) {
                    this.listKabupaten({ per_page:'ALL',propinsi:data.alamat.propinsi_id }).then((response) => {
                        if (response.success == true) {
                            this.kota = JSON.parse(JSON.stringify(response.data)); 
                            this.$refs.kotaSelect.refreshList(this.kota.data);
                        }
                    })  
                }
                if(data.alamat.kota_id) {
                    this.listKecamatan({ per_page:'ALL',kota:data.alamat.kota_id }).then((response) => {
                        if (response.success == true) {
                            this.kecamatan = JSON.parse(JSON.stringify(response.data)); 
                            this.$refs.kecSelect.refreshList(this.kecamatan.data);
                        }
                    })  
                }   
                if(data.alamat.kecamatan_id) {
                    this.listKelurahan({ per_page:'ALL',kecamatan:data.alamat.kecamatan_id }).then((response) => {
                        if (response.success == true) {
                            this.kelurahan = JSON.parse(JSON.stringify(response.data)); 
                            this.$refs.kelSelect.refreshList(this.kelurahan.data);
                        }
                    })  
                }            

                if(!data.alamat.is_ktp_sama_dgn_tinggal) {
                    if(data.alamat.propinsi_tinggal_id) {
                        this.listKabupaten({ per_page:'ALL',propinsi:data.alamat.propinsi_tinggal_id }).then((response) => {
                            if (response.success == true) {
                                this.kotaTinggal = JSON.parse(JSON.stringify(response.data)); 
                                this.$refs.kotaTinggalSelect.refreshList(this.kota.data);
                            }
                        })
                    }
                    
                    if(data.alamat.kota_tinggal_id) {
                        this.listKecamatan({ per_page:'ALL',kota:data.alamat.kota_tinggal_id }).then((response) => {
                            if (response.success == true) {
                                this.kecamatanTinggal = JSON.parse(JSON.stringify(response.data)); 
                                this.$refs.kecTinggalSelect.refreshList(this.kecamatan.data);
                            }
                        })
                    }
                    
                    if(data.alamat.kecamatan_tinggal_id) {
                        this.listKelurahan({ per_page:'ALL',kecamatan:data.alamat.kecamatan_tinggal_id }).then((response) => {
                            if (response.success == true) {
                                this.kelurahanTinggal = JSON.parse(JSON.stringify(response.data)); 
                                this.$refs.kelTinggalSelect.refreshList(this.kelurahan.data);
                            }
                        })
                    }
                }   
            } 
        },

        addDataAlergi(){
            if(this.alergiData.jns_alergi == '' || this.alergiData.jns_alergi == null) {
                alert('jenis alergi tidak boleh kosong'); return false;
            }

            if(this.alergiData.catatan_alergi == '' || this.alergiData.catatan_alergi == null) {
                alert('catatan alergi tidak boleh kosong');  return false;
            }

            if(!this.pasien.alergi) { this.pasien.alergi = []; }            
            this.pasien.alergi.push(JSON.parse(JSON.stringify(this.alergiData)));

            this.alergiData.pasien_alergi_id = null;
            this.alergiData.jns_alergi = null;
            this.alergiData.pasien_id = null;
            this.alergiData.catatan_alergi = null;
            this.alergiData.tgl_kejadian_awal = null;
            this.alergiData.is_aktif = true;
            this.alergiData.akibat = null;
            
        },
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox, .uk-select {
    border: 1px solid #999;
    color: black;
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
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    
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


.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */

/* tr.inaktif td {
    text-decoration: line-through;
    font-style: italic;
} */

</style>