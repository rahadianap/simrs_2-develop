<template>
    <div ref="modalFormPasien"
        class="uk-offcanvas-overlay uk-open"
        :style="formStyles"
    >
        <div 
            class="uk-modal1 uk-width-1-3@m" 
            uk-modal1="bg-close:false;" 
            :id="modalId" 
            :style="modalStyles"
        >
            <div class="uk-modal-dialog1 uk-modal-body1">
                    <div style="border-bottom: 1px solid rgb(212, 212, 212); width: auto;">
                        <h2 class="uk-modal-title1" style="padding:0;margin:0;font-weight:700; color:black;">Data Pasien</h2>
                    </div>
                    <form action="" @submit.prevent="submitPasien">
                    <div>
                        <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                            <div uk-spinner="ratio : 2"></div>
                            <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                        </div>
                        
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        
                        <div class="uk-grid-small" style="padding:1em 1em 0em 0em;" uk-grid>
                            <div class="uk-width-2-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien</label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input" style="color:black;border-radius:7px;" v-model="pasien.nama_pasien" required>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jns Kelamin</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.jns_kelamin" class="uk-select"  style="border-radius:7px;" required>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                        <option value="-">-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input" style="color:black;border-radius:7px;" v-model="pasien.tempat_lahir" required>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir</label>
                                <div class="uk-form-controls">
                                    <input type="date" class="uk-input" style="color:black;border-radius:7px;" v-model="pasien.tgl_lahir" required>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK</label>
                                <div class="uk-form-controls">
                                    <input type="text" class="uk-input" style="color:black;border-radius:7px;" v-model="pasien.nik" required>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Agama</label>
                                <div class="uk-form-controls">
                                    <!-- <input type="text" class="uk-input" style="color:black;border-radius:7px;" v-model="pasien.agama"> -->
                                    <select v-if="isRefExist" v-model="pasien.detail.agama" class="uk-select" style="border-radius:7px;">
                                        <option v-for="dt in agamaRefs.json_data" :key="dt in agamaRefs" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pekerjaan</label>
                                <div class="uk-form-controls">
                                    <!-- <input type="text" class="uk-input" style="color:black;border-radius:7px;" v-model="pasien.pekerjaan"> -->
                                    <select v-if="isRefExist" v-model="pasien.detail.pekerjaan" class="uk-select"  style="border-radius:7px;">
                                        <option v-for="dt in pekerjaanRefs.json_data" :key="dt in pekerjaanRefs" :value="dt.value">{{dt.value}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pendidikan</label>
                                <div class="uk-form-controls">
                                    <!-- <input type="text" class="uk-input" style="color:black;border-radius:7px;" v-model="pasien.pendidikan"> -->
                                    <select v-if="isRefExist" v-model="pasien.detail.pendidikan" class="uk-select" style="border-radius:7px;">
                                        <option v-for="dt in pendidikanRefs.json_data" :key="dt in pendidikanRefs" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-1@m uk-text-left" style="margin-top:2em;">
                                <p style="font-size:12px; font-style:italic; color:blue; padding:0; margin:0;"><a href="#" @click.prevent="editMasterData">tambah/ubah master agama, pendidikan dan pekerjaan</a></p>
                            </div> 
                            <div class="uk-width-1-1@m" style="text-align:right;margin-top:2em;">
                                <button class="uk-button uk-button-default uk-modal-close" type="button" @click.prevent="closeFormPasien" style="margin-right:10px; border-radius: 7px; font-size: 14px; text-transform: none;">Tutup</button>
                                <button class="uk-button uk-button-primary" type="submit" style="border-radius: 7px; font-size: 14px; text-transform: none;">Simpan</button>                                        
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <modal-master-praktek ref="modalMasterPraktek"></modal-master-praktek>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import ModalMasterPraktek from '@/Pages/PraktekDokter/LayananDokter/tabs/MasterPasien/components/ModalMaster.vue';

export default {
    name : 'form-pasien-praktek',
    components : {
        ModalMasterPraktek,
    },
    data(){
        return {
            modalId : 'formPasien',
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
            },
            isUpdate : false,
            isLoading : true,
            isModalOpen: false,

            formStyles: {
                visibility: 'visible',
                opacity: '1',
                transition: '', 
            },

            modalStyles : {
                height:'100vh', 
                top:'0px',
                right:'-550px', 
                zIndex:'999', 
                position:'fixed', 
                backgroundColor:'white',
                paddingTop: '1em',
                paddingLeft: '2em',
                animation: 'slide 0.5s forwards', 
                animationDelay: '0.3s',
                display: 'block',
            }
        }
    },
    computed : {        
        ...mapGetters(['errors']),
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
        ...mapActions('praktekDokter',['createPasien','updatePasien','dataPasien','uploadDokterAvatar']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            // this.$refs.modalFormPasien.style.display = "none";
            this.$refs.modalFormPasien.style.display = this.isModalOpen ? 'block' : 'none';
            this.isLoading = false;            
        },

        openModal() {
            this.isModalOpen = true;
        },

        newPasien(){
            this.CLR_ERRORS();
            if(!this.isRefExist) { 
                this.listReferensi(); 
            };
            this.isUpdate = false;
            this.clearPasien();
            this.isLoading = false;
            //UIkit.modal(`#${this.modalId}`).show();
            this.$refs.modalFormPasien.style.display = "block";
        },

        ubahDataPasien(data) {
            this.CLR_ERRORS();
            if(!this.isRefExist) { 
                this.listReferensi(); 
            };
            this.isUpdate = true;
            this.clearPasien();
            this.fillPasien(data);
            this.isLoading = false;
            this.$refs.modalFormPasien.style.display = "block";
        },


        closeFormPasien(){
            this.isUpdate = false;
            this.clearPasien();
            this.$emit('formClosed',true);
            //UIkit.modal(`#${this.modalId}`).hide();
            // this.$refs.modalFormPasien.style.display = "none";
            this.formStyles.visibility = 'hidden';
            this.formStyles.opacity = '0';
            this.formStyles.transition = 'opacity 0.3s, visibility 0.5s';
            this.modalStyles.right = '0px';
            this.modalStyles.animation = 'slideOut 0.5s forwards';
            this.modalStyles.animationDelay = '0.1s';
        },

        submitPasien(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.isLoading = true;
                this.createPasien(this.pasien).then((response) => {
                    this.isLoading = false;
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
                this.isLoading = true;
                this.updatePasien(this.pasien).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        this.fillPasien(response.data);
                        alert("Data pasien berhasil diubah.");
                        this.$emit('saveSucceed',response.data);
                        this.isUpdate = true;
                        this.closeFormPasien();
                    }
                })
            }
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
            //this.pasien.alergi = data.alergi;

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
                        
            // if(data.alamat){

            //     this.pasien.alamat.alamat = data.alamat.alamat;
            //     this.pasien.alamat.propinsi_id = data.alamat.propinsi_id;
            //     this.pasien.alamat.propinsi = data.alamat.propinsi;
                
            //     this.pasien.alamat.kota_id = data.alamat.kota_id;
            //     this.pasien.alamat.kota = data.alamat.kota;
                
            //     this.pasien.alamat.kecamatan_id = data.alamat.kecamatan_id;
            //     this.pasien.alamat.kecamatan = data.alamat.kecamatan;
                
            //     this.pasien.alamat.kelurahan_id = data.alamat.kelurahan_id;
            //     this.pasien.alamat.kelurahan = data.alamat.kelurahan;
                
            //     this.pasien.alamat.kodepos = data.alamat.kodepos;
            //     this.pasien.alamat.rt = data.alamat.rt;
            //     this.pasien.alamat.rw = data.alamat.rw;
                
            //     this.pasien.alamat.is_ktp_sama_dgn_tinggal = data.alamat.is_ktp_sama_dgn_tinggal;
            //     if(!data.alamat.is_ktp_sama_dgn_tinggal) {
            //         this.pasien.alamat.alamat_tinggal = data.alamat.alamat_tinggal;
            //         this.pasien.alamat.propinsi_tinggal_id = data.alamat.propinsi_tinggal_id;
            //         this.pasien.alamat.propinsi_tinggal = data.alamat.propinsi_tinggal;
            //         this.pasien.alamat.kota_tinggal_id = data.alamat.kota_tinggal_id;
            //         this.pasien.alamat.kota_tinggal = data.alamat.kota_tinggal;
            //         this.pasien.alamat.kecamatan_tinggal_id = data.alamat.kecamatan_tinggal_id;
            //         this.pasien.alamat.kecamatan_tinggal = data.alamat.kecamatan_tinggal;
            //         this.pasien.alamat.kelurahan_tinggal_id = data.alamat.kelurahan_tinggal_id;
            //         this.pasien.alamat.kelurahan_tinggal = data.alamat.kelurahan_tinggal;
            //         this.pasien.alamat.kodepos_tinggal = data.alamat.kodepos_tinggal;
            //         this.pasien.alamat.rt_tinggal = data.alamat.rt_tinggal;
            //         this.pasien.alamat.rw_tinggal = data.alamat.rw_tinggal;
            //     }

            //     this.configTinggalSelect.disabled = data.alamat.is_ktp_sama_dgn_tinggal; 

            //     if(data.alamat.propinsi_id) {
            //         this.listKabupaten({ per_page:'ALL',propinsi:data.alamat.propinsi_id }).then((response) => {
            //             if (response.success == true) {
            //                 this.kota = JSON.parse(JSON.stringify(response.data)); 
            //                 this.$refs.kotaSelect.refreshList(this.kota.data);
            //             }
            //         })  
            //     }
            //     if(data.alamat.kota_id) {
            //         this.listKecamatan({ per_page:'ALL',kota:data.alamat.kota_id }).then((response) => {
            //             if (response.success == true) {
            //                 this.kecamatan = JSON.parse(JSON.stringify(response.data)); 
            //                 this.$refs.kecSelect.refreshList(this.kecamatan.data);
            //             }
            //         })  
            //     }   
            //     if(data.alamat.kecamatan_id) {
            //         this.listKelurahan({ per_page:'ALL',kecamatan:data.alamat.kecamatan_id }).then((response) => {
            //             if (response.success == true) {
            //                 this.kelurahan = JSON.parse(JSON.stringify(response.data)); 
            //                 this.$refs.kelSelect.refreshList(this.kelurahan.data);
            //             }
            //         })  
            //     }            

            //     if(!data.alamat.is_ktp_sama_dgn_tinggal) {
            //         if(data.alamat.propinsi_tinggal_id) {
            //             this.listKabupaten({ per_page:'ALL',propinsi:data.alamat.propinsi_tinggal_id }).then((response) => {
            //                 if (response.success == true) {
            //                     this.kotaTinggal = JSON.parse(JSON.stringify(response.data)); 
            //                     this.$refs.kotaTinggalSelect.refreshList(this.kota.data);
            //                 }
            //             })
            //         }
                    
            //         if(data.alamat.kota_tinggal_id) {
            //             this.listKecamatan({ per_page:'ALL',kota:data.alamat.kota_tinggal_id }).then((response) => {
            //                 if (response.success == true) {
            //                     this.kecamatanTinggal = JSON.parse(JSON.stringify(response.data)); 
            //                     this.$refs.kecTinggalSelect.refreshList(this.kecamatan.data);
            //                 }
            //             })
            //         }
                    
            //         if(data.alamat.kecamatan_tinggal_id) {
            //             this.listKelurahan({ per_page:'ALL',kecamatan:data.alamat.kecamatan_tinggal_id }).then((response) => {
            //                 if (response.success == true) {
            //                     this.kelurahanTinggal = JSON.parse(JSON.stringify(response.data)); 
            //                     this.$refs.kelTinggalSelect.refreshList(this.kelurahan.data);
            //                 }
            //             })
            //         }
            //     }   
            // } 
        },

        editMasterData(){
            this.$refs.modalMasterPraktek.showModalMaster();
        }
    }
}
</script>
<style>
.uk-offcanvas-overlay {
  position: fixed;
  z-index:998;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  -webkit-backdrop-filter: blur(5px);
  backdrop-filter: blur(5px);
}
.uk-offcanvas-overlay::before {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity .15s linear;
}

@-webkit-keyframes slide {
    100% { right: 0; }
}
@-webkit-keyframes slideOut {
    100% {
        right: -550px;
    }
}
@keyframes slide {
    100% { right: 0; }
}
@keyframes slideOut {
    100% {
        right: -550px;
    }
}
</style>