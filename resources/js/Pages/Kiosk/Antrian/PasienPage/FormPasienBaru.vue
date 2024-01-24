<template>
    <div>
        <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
            <div uk-spinner="ratio : 2"></div>
            <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
        </div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div> 
        <form action="" @submit.prevent="submitPasien">
            <div class="uk-grid" uk-grid>
                <div class="uk-width-1-2@m" style="padding:1em;margin:0;">
                    <div style="padding:1em 0 1em 0;border-bottom:2px solid #cc0202;">
                        <h3 style="color:black;font-weight: 500; padding:0;margin:0;">DATA PRIBADI</h3>
                    </div>
                    <div style="margin-top:1em;">
                        <div class="uk-grid-small" uk-grid>                                         
                            <div class="uk-width-2-3@s">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.nama_pasien" type="text" placeholder="nama pasien" required>
                                </div>
                            </div>
                            <div class="uk-width-1-3@s">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Salut*</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.salut" class="uk-select uk-form-small" required>
                                        <option v-if="isRefExist" v-for="dt in salutRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-3@s">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin*</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.jns_kelamin" class="uk-select uk-form-small" required>
                                        <option value="L">LAKI-LAKI</option>
                                        <option value="P">PEREMPUAN</option>
                                        <option value="-">-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-3@s">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small uk-text-uppercase" v-model="pasien.tempat_lahir" type="text" placeholder="tempat lahir" required>
                                </div>
                            </div>
                            <div class="uk-width-1-3@s">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.tgl_lahir" type="date" placeholder="tanggal lahir" required>
                                </div>
                            </div>
                            <div class="uk-width-1-3@s">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No.KTP / No. NIK*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.nik" type="text" placeholder="no kependudukan" required>
                                </div>
                            </div>
                            <div class="uk-width-1-3@s">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Kartu Keluarga</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.no_kk" type="text" placeholder="no kartu keluarga">
                                </div>
                            </div>
                            <div class="uk-width-1-3@s">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Penjamin*</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.jns_penjamin" class="uk-select uk-form-small" required @change="ambilPenjaminList">
                                        <option v-if="isRefExist" v-for="dt in jenisPenjaminRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-2-3@s">
                                <search-list
                                    :config = configSelect
                                    :dataLists = asuransiLists.data
                                    label = "Penjamin / Asuransi"
                                    placeholder = "pilih penjamin (asuransi)"
                                    captionField = "penjamin_nama"
                                    valueField = "penjamin_id"
                                    :detailItems = penjaminDesc
                                    :value = "pasien.penjamin_id"
                                    v-on:item-select = "penjaminSelected"
                                ></search-list>
                            </div>

                            <div class="uk-width-1-3@s">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Kepesertaan</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.no_kepesertaan" type="text" placeholder="no kepesertaan asuransi">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-1-2@m" style="padding:1em;margin:0;">
                    <div style="padding:1em 0 1em 0;border-bottom:2px solid #cc0202;">
                        <h3 style="color:black;font-weight: 500; padding:0;margin:0;">ALAMAT</h3>
                    </div>
                    <div style="margin-top:1em;"> 
                        <div class="uk-grid-small" uk-grid>   
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alamat</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.alamat" type="text" placeholder="alamat sesuai identitas">
                                </div>
                            </div>   
                            <div class="uk-width-1-2@s uk-grid uk-grid-small" style="padding:15px 0 0 0;margin:0;">
                                <div class="uk-width-1-2@s">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">RT</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pasien.rt" type="text" placeholder="rt">
                                    </div>
                                </div>        
                                <div class="uk-width-1-2@s">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">RW</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pasien.rw" type="text" placeholder="rw">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="uk-width-1-2@s">
                                <search-list
                                    ref = "propSelect"
                                    :config = configSelect
                                    :dataLists = propinsiLists.data
                                    label = "Propinsi"
                                    placeholder = "propinsi sesuai identitas"
                                    captionField = "propinsi"
                                    valueField = "propinsi_id"
                                    :detailItems = propDesc
                                    :value = "pasien.propinsi_id"
                                    v-on:item-select = "propSelected"
                                ></search-list>
                            </div>
                            <div class="uk-width-1-2@s">
                                <search-list
                                    ref = "kotaSelect" 
                                    :config = configSelect
                                    :dataLists = kota.data
                                    label = "Kabupaten/Kota"
                                    placeholder = "kota sesuai identitas"
                                    captionField = "kota"
                                    valueField = "kota_id"
                                    :detailItems = kabDesc
                                    :value = "pasien.kota_id"
                                    v-on:item-select = "kabSelected"
                                ></search-list>
                            </div>
                            <div class="uk-width-1-2@s">
                                <search-list
                                    ref = "kecSelect" 
                                    :config = configSelect
                                    :dataLists = kecamatan.data
                                    label = "Kecamatan"
                                    placeholder = "kecamatan sesuai identitas"
                                    captionField = "kecamatan"
                                    valueField = "kecamatan_id"
                                    :detailItems = kecDesc
                                    :value = "pasien.kecamatan_id"
                                    v-on:item-select = "kecSelected"
                                ></search-list>
                            </div>
                            <div class="uk-width-1-2@s">
                                <search-list
                                    ref = "kelSelect" 
                                    :config = configSelect
                                    :dataLists = kelurahan.data
                                    label = "Kelurahan/Desa"
                                    placeholder = "kelurahan sesuai identitas"
                                    captionField = "kelurahan"
                                    valueField = "kelurahan_id"
                                    :detailItems = kelDesc
                                    :value = "pasien.kelurahan_id"
                                    v-on:item-select = "kelSelected"
                                ></search-list>
                            </div>
                            <div class="uk-width-1-2@s">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kodepos</label>
                                <div class="uk-inline uk-width-1-1">
                                    <input v-model="pasien.kodepos" class="uk-input uk-form-small" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div class="uk-modal-footer uk-text-center" style="padding:2em 1em 1em 1em;">
                <button class="uk-button modal-jadwal-button" type="button" style="border:2px solid #666; margin-right:10px;" @click.prevent="closeModalVerif">BATAL</button>
                <button class="uk-button modal-jadwal-button-primary" style="border:2px solid #cc0202;" type="submit">DAFTARKAN</button>
            </div>
        </form>

        <modal-pasien-baru ref="modalPasienBaru" v-on:modalPasienBaruClosed="formModalPasienClosed"></modal-pasien-baru>
    </div>
</template>
<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Pages/MasterData/Pasien/components/SearchList.vue';
import ModalPasienBaru from '@/Pages/Kiosk/Antrian/PasienPage/ModalPasienBaru.vue';

export default {
    name : 'form-pasien-baru',
    components : { SearchSelect, SearchList,ModalPasienBaru },
    data() {
        return {
            isLoading : false,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : false,
            },
            penjaminDesc : [
                { colName : 'penjamin_nama', labelData : '', isTitle : true },
                { colName : 'penjamin_id', labelData : '', isTitle : false },
            ],
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

            pasien : {
                pasien_id : null,
                client_id : null,
                is_pasien_luar : false,
                no_rm : null,
                nama_pasien : null,
                salut : null,
                nama_lengkap : null,
                nik : null,
                no_kk : null,
                jns_kelamin : null,
                penjamin_id : null,
                penjamin_nama : null,
                no_kepesertaan : null,
                is_aktif : null,
                tgl_daftar : null,
                alamat : null,
                propinsi_id : null,
                kota_id : null,
                kecamatan_id : null,
                kelurahan_id : null,
                propinsi : null,
                kota : null,
                kecamatan : null,
                kelurahan : null,
                kodepos : null,
                rt : null,
                rw : null,
                is_ktp_sama_dgn_tinggal : true,

                tgl_terakhir_periksa : null,
                is_hamil : false,
                is_meninggal : false,
                tgl_meninggal : null,
                penyebab_kematian : null,
                tgl_daftar : null,
            },

            kota :[],
            kecamatan: [],
            kelurahan: [],
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
        ...mapActions('kiosk',['createPasienBaru']),
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

        formModalPasienClosed(){
            this.$emit('formPasienClosed',true);
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

        propSelected(data) {
            this.pasien.kota_id = null;
            this.pasien.kecamatan_id = null;
            this.pasien.kelurahan_id = null;
            this.pasien.kodepos = null;
            this.kecamatan = [];
            this.kelurahan = [];
            this.kota = [];  
            if(data){
                this.pasien.propinsi_id = data.propinsi_id;
                
                let prm = {per_page:'ALL',propinsi: data.propinsi_id };                            
                this.listKabupaten(prm).then((response) => {
                    if (response.success == true) {
                        this.kota = JSON.parse(JSON.stringify(response.data)); 
                    }
                })
            }      
        },

        kabSelected(data) {            
            this.pasien.kecamatan_id = null;
            this.pasien.kelurahan_id = null;
            this.pasien.kodepos = null;
            this.kecamatan = [];
            this.kelurahan = [];

            if(data) {                 
                this.pasien.kota_id = data.kota_id; 
                let prm = {per_page:'ALL',kota: data.kota_id };
                this.listKecamatan(prm).then((response) => {
                    if (response.success == true) {
                        this.kecamatan = JSON.parse(JSON.stringify(response.data)); 
                    }
                })
            }
        },

        kecSelected(data) {
            this.pasien.kelurahan_id = null;
            this.pasien.kodepos = null;
            this.kelurahan = [];                
            if(data) {                 
                this.pasien.kecamatan_id = data.kecamatan_id;
                let prm = {per_page:'ALL',kecamatan: data.kecamatan_id };
                this.listKelurahan(prm).then((response) => {
                    if (response.success == true) {
                        this.kelurahan = JSON.parse(JSON.stringify(response.data)); 
                    }
                })       
            }
        },

        kelSelected(data) {
            this.pasien.kelurahan_id = null;
            this.pasien.kodepos = null;
            if(data){ 
                this.pasien.kelurahan_id = data.kelurahan_id; 
                this.pasien.kelurahan = data.kelurahan; 
                this.pasien.kodepos = data.kodepos;
            }
        },

        submitPasien(){
            this.CLR_ERRORS();            
            this.createPasienBaru(this.pasien).then((response) => {
                if (response.success == true) {
                    alert("Data pasien baru berhasil dibuat.");
                    this.fillPasien(response.data);
                    // this.$emit('saveSucceed',true);
                    // this.isUpdate = true;
                    // this.closeFormPasien();
                }
            })
        },

        fillPasien(data) {
            if(data) {
                let almtTinggal = `${data.alamat.alamat} RT.${data.alamat.rt}/ RW.${data.alamat.rw} Kel.${data.alamat.kelurahan} Kec.${data.alamat.kecamatan} Kota/Kab.${data.alamat.kota} Prop.${data.alamat.propinsi}`;
                let almtKtp = `${data.alamat.alamat_tinggal} RT.${data.alamat.rt_tinggal}/ RW.${data.alamat.rw_tinggal} Kel.${data.alamat.kelurahan_tinggal} Kec.${data.alamat.kecamatan_tinggal} Kota/Kab.${data.alamat.kota_tinggal} Prop.${data.alamat.propinsi_tinggal}`;
                
                let dtPasien = {
                    pasien_id : data.pasien_id,
                    no_rm : data.no_rm,
                    nama_pasien : data.nama_pasien,
                    salut : data.salut,
                    nama_lengkap : data.nama_lengkap,
                    nik : data.nik,
                    no_kk : data.no_kk,
                    no_telepon : '-',
                    jns_kelamin : data.jns_kelamin,
                    tgl_lahir : data.tgl_lahir,
                    tempat_lahir : data.tempat_lahir,
                    alamat_tinggal : almtTinggal,
                    alamat_ktp : almtKtp,
                };

                this.$refs.modalPasienBaru.showModal(dtPasien);
            }
        },

        newPasien(){
            this.clearPasien();
            this.isLoading = false;
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
            this.pasien.tgl_daftar = null;
            this.pasien.url_foto = null;
            this.pasien.is_aktif = true;
            
            this.pasien.alamat = null;
            this.pasien.propinsi_id = null;
            this.pasien.kota_id = null;
            this.pasien.kecamatan_id = null;
            this.pasien.kelurahan_id = null;
            this.pasien.propinsi = null;
            this.pasien.kota = null;
            this.pasien.kecamatan = null;
            this.pasien.kelurahan = null;
            this.pasien.kodepos = null;
            this.pasien.rt = null;
            this.pasien.rw = null;
        },
    }
}
</script>