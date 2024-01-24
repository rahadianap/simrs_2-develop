<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalPersalinan" style="min-height:50vh;padding:0;margin:0;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitPersalinan" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA PASIEN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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

                        <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;padding-bottom:0;">                        
                            <div class="uk-width-1-4@m" style="padding:0 0.5em 0.5em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA BAYI</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    detail data bayi.
                                </p>
                            </div>
                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt style="color:#000;">No.RM</dt>
                                        <dd style="color:#000; font-size:14px;">{{dataBayi.no_rm}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt style="color:#000;">Nama Pasien</dt>
                                        <dd style="color:#000; font-size:14px;">{{dataBayi.nama_pasien}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt style="color:#000;">Pasien ID</dt>
                                        <dd style="color:#000; font-size:14px;">{{dataBayi.pasien_id}}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        

                        <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">                        
                            <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA KELAHIRAN</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    detail data kelahiran.
                                </p>
                            </div>
                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                                <div class="uk-width-1-1" style="margin:0;padding:0;"></div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                    <div class="uk-inline uk-width-1-1">
                                        <select v-model="dataBayi.jk_bayi" class="uk-select uk-form-small" required style="color:black;">
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Berat Badan Bayi (Kg)</label>
                                    <div class="uk-inline uk-width-1-1">
                                        <input class="uk-input uk-form-small" v-model="dataBayi.bb_bayi" type="number" style="color:black;">
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Panjang Badan Bayi (Cm)</label>
                                    <div class="uk-inline uk-width-1-1">
                                        <input class="uk-input uk-form-small" v-model="dataBayi.tb_bayi" type="number" style="color:black;">
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lingkar Kepala (Cm)</label>
                                    <div class="uk-inline uk-width-1-1">
                                        <input class="uk-input uk-form-small" v-model="dataBayi.lingkar_kepala" type="number" style="color:black;">
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lingkar Dada (Cm)</label>
                                    <div class="uk-inline uk-width-1-1">
                                        <input class="uk-input uk-form-small" v-model="dataBayi.lingkar_dada" type="number" style="color:black;">
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Frek. Pernapasan (x / Menit)</label>
                                    <div class="uk-inline uk-width-1-1">
                                        <input class="uk-input uk-form-small" v-model="dataBayi.frekuensi_napas" type="number" style="color:black;">
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Detak Jantung (x / Menit)</label>
                                    <div class="uk-inline uk-width-1-1">
                                        <input class="uk-input uk-form-small" v-model="dataBayi.detak_jantung" type="number" style="color:black;">
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kondisi Lahir</label>
                                    <div class="uk-inline uk-width-1-1">
                                        <select v-model="dataBayi.kondisi_lahir" class="uk-select uk-form-small" required style="color:black;">
                                            <option value="Sehat">Sehat</option>
                                            <option value="Kritis">Kritis</option>
                                            <option value="Meninggal">Meninggal</option>
                                        </select>
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
import FormPersalinan from '@/Pages/Penunjang/Operasi/components/FormPersalinan.vue';
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Pages/MasterData/Pasien/components/SearchList.vue';

export default {
    name : 'modal-persalinan',
    components : { 
        FormPersalinan,
        SearchSelect,
        SearchList, 
    },

    data() {
        return {            
            //urlBack : null,
            isLoading : true,

            dataBayi : {
                pasien_id : null,
                pasien_nama : null,
                no_rm : null,
                jam_kelahiran : null,
                tgl_kelahiran : null,
                umur_kehamilan : null,
                jenis_persalinan : null,
                kelahiran_ke : null,
                kondisi_ibu : null,
                jk_bayi : null,
                bb_bayi : null,
                tb_bayi : null,
                lingkar_kepala : null,
                lingkar_dada : null,
                frekuensi_napas : null,
                detak_jantung : null,
                kondisi_lahir : null,
                is_aktif : null,
            },

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

    methods : {
        showModal(data){
            if(data) {
                this.dataBayi = data;
                this.isLoading = false;
                UIkit.modal('#modalPersalinan').show();
            }
        },

        closeModal(){
            this.isLoading = false;
            UIkit.modal('#modalPersalinan').hide();
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

        newData(data){
            console.log(data);
            // this.dataBayi = data;
            // this.isUpdate = false;
            if(data) {
                alert('new data');
                this.isLoading = false;
                UIkit.modal('#modalPersalinan').show();
            }
        },

        editData(data){
            // this.dataBayi = data;
            // this.isLoading = false;
            // this.isUpdate = true;
            UIkit.modal('#modalPersalinan').show();
        },

        editDataPasien(pasienId){
            // this.$refs.formPersalinan.editPasien(pasienId);
            UIkit.modal('#modalPersalinan').show();
        },

        modalPersalinanClosed(data){
            this.$emit('modalPersalinanClosed',data);
            UIkit.modal('#modalPersalinan').hide();
        },

        submitPersalinan(){
            this.$emit('dataCreate',this.dataBayi);
            UIkit.modal('#modalPersalinan').hide();
        }
        
    }
}
</script>