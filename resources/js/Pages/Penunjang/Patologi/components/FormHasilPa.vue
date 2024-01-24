<template>
    <div class="" style="margin:0;padding:0;">        
        <div style="margin:0;padding:0;">
            <form action="" @submit.prevent="submitHasilPa">
                <div style="margin:0;padding:0;">
                    <div style="padding:0.5em 0.5em 0.5em 1em;position: sticky; position: -webkit-sticky;top: 0; z-index:99;">
                        <div class="uk-grid-small hims-form-header" uk-grid style="padding:0;margin:0;">                        
                            <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                                <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0.5em;">
                                    <button type="button" class="hims-form-header-btn uk-width-auto" @click.prevent="modalClosed" style="padding:0.5em;"><span uk-icon="arrow-left"></span> Tutup</button>
                                    <button type="button" class="hims-form-header-btn uk-width-auto" @click.prevent="refreshData" style="padding:0.5em;"><span uk-icon="refresh"></span> Refresh</button>
                                    <button :disabled="isLoading" type="button" @click.prevent="batalKonfirmasiReg" class="hims-form-header-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-edit"></span> Ubah Pemeriksaan</button>
                                    <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                                    <button :disabled="isLoading" type="submit" class="hims-form-header-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-text"></span> Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-container uk-container-large">                
                        <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                            <div uk-spinner="ratio : 2"></div>
                            <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                        </div>
                        
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>
                                        
                        <div class="hims-form-container view-lab-container" style="padding:0 1.5em 2em 1.5em; margin:0;"> 
                            <div class="hims-form-subpage" style="padding:0.5em;">
                                <h4 style="padding:0;margin:0;">{{ registrasi.nama_pasien }}</h4>
                                <p style="padding:0;margin:0;font-size:12px;">{{ registrasi.pasien_id }}</p>     
                            </div>
                            <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;padding:0.5em;background-color:#eee;">
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>No.Registrasi</dt>
                                        <dd>{{registrasi.reg_id}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>No.Transaksi</dt>
                                        <dd>{{registrasi.trx_id}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Reff Transaksi</dt>
                                        <dd>{{registrasi.reff_trx_id}}</dd>
                                    </dl>
                                </div>
                            </div> 

                            <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                                <div class="uk-width-1-4@m" style="padding:0.5em 0.5em 1em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">DATA TRANSAKSI</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                        detail data pasien dan transaksi.
                                    </p>
                                </div>
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0.5em 0.5em 0 0.5em;">
                                    <!-- <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.nama_pasien" style="color:black;" disabled>
                                        </div>
                                    </div> -->
                                    <!-- <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No RM</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.no_rm" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Pasien*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.pasien_id" style="color:black;" disabled>
                                        </div>
                                    </div> -->
                                    
                                    <!-- <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Penjamin*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.jns_penjamin" style="color:black;" disabled>
                                        </div>
                                    </div> -->
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penjamin / Asuransi</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.penjamin_nama" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <!-- <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Kepesertaan</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.no_kepesertaan" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK (KTP / ID Card)*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.nik" type="text" style="color:black;" disabled>
                                        </div>
                                    </div> -->
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.unit_nama" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <!-- <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Ruang</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.ruang_nama" type="text" style="color:black;" disabled>
                                        </div>
                                    </div> -->
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.jns_kelamin" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.tempat_lahir" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Lahir*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_lahir" style="color:black;" :max="minDate" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Layanan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.kelas_harga" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>                                        
                                </div>
                            </div>
                            
                            <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                                <div class="uk-width-1-4@m" style="padding:0.5em 0.5em 1em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">DATA PEMERIKSAAN</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                        detail data pemeriksaan patologi.
                                    </p>
                                </div>
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0.5em 0.5em 0.2em 0.5em;">
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Patologi*</label>
                                        <div class="uk-form-controls">
                                            <select v-model="registrasi.dokter_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="dokterSelected"> 
                                                <option v-for="option in dokterLists" v-bind:value="option.dokter_id" v-bind:key="option.dokter_id">{{option.dokter_nama}}</option>
                                            </select>
                                        </div>
                                    </div> -->
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Patologi*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.dokter_nama" type="text" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Periksa*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_periksa" style="color:black;" :min="minDate">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Periksa*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="time" v-model="registrasi.jam_periksa" style="color:black;">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Cito*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.jenis_cito" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Spesimen*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.no_spesimen" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Diserahkan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.tgl_diserahkan" type="date" :min="minDate">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Media Hasil</label>
                                        <div class="uk-form-controls">
                                            <select v-model="registrasi.media_hasil" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;"> 
                                                <option v-for="option in mediaHasilRefs.json_data" v-bind:value="option.value">{{option.value}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Hub. Penerima</label>
                                        <div class="uk-form-controls">
                                            <select v-model="registrasi.hub_penerima" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;"> 
                                                <option v-for="option in hubKeluargaRefs.json_data" v-bind:value="option.value">{{option.value}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-width-3-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Penerima</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="registrasi.penerima_hasil">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-else class="uk-grid-small hims-form-subpage" uk-grid>
                                <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0.5em 0.5em 0 0.5em;">
                                    <div class="uk-width-expand@m">
                                        <h5 style="color:indigo;padding:0;margin:0;">HASIL PATOLOGI
                                            <span style="font-size:12px;font-style:italic;padding:0;margin:0 0 0 0.2em;">hasil pemeriksaan patologi.</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                    <table class="uk-table uk-table-striped child-table tb-hasil-lab" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">PEMERIKSAAN</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">FILM</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">HASIL</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">CATATAN</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">DOKTER</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">EXPERTISE</th>
                                            <th colspan="2" class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>                                                                                        
                                        </thead>
                                        <tbody> 
                                            <tr v-for="res in registrasi.hasil">
                                                <td style="width:130px;text-align:left;">
                                                    <p style="margin:0;padding:0;font-weight: 500;">{{res.tindakan_nama}}</p>
                                                    <p style="margin:0;padding:0;font-size: 10px;">{{res.tindakan_id}}</p>
                                                </td>
                                                <td >
                                                    <p style="margin:0;padding:0;font-weight: 500;"><span>Jenis : </span>{{res.jenis_foto}}</p>
                                                    <p style="margin:0;padding:0;font-size: 10px;"><span>No : </span>{{res.no_foto}}</p>
                                                </td>
                                                <td style="text-align:left;">
                                                    <p style="margin:0;padding:0;font-weight: 500;"><span>Uraian : </span>{{res.uraian_hasil}}</p>
                                                    <p style="margin:0;padding:0;font-size: 10px;"><span>Kesan : </span>{{res.kesan}}</p>
                                                </td>
                                                <td style="text-align:left;">{{res.catatan}}</td>                                       
                                                <td style="text-align:left;">
                                                    <p style="margin:0;padding:0;font-weight: 500;">{{res.dokter_nama}}</p>
                                                    <p style="margin:0;padding:0;font-size: 10px;">{{res.dokter_id}}</p>
                                                </td>  
                                                <td style="width:30px; padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
                                                    <a href="#" uk-icon="icon:file-edit;ratio:0.8" @click.prevent="updateData(res)"></a>
                                                </td>                                                
                                                <td style="width:30px; padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
                                                    <a href="#" uk-icon="icon:trash;ratio:0.8" @click.prevent="deleteData(res)"></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
            </form>
        </div>

        <div>
            <modal-hasil-Pa ref="modalHasilPa" 
                v-on:hasilpaUpdated = "ubahDataHasilPa">
            </modal-hasil-Pa>
        </div>
    </div>
    
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

import RowFormRegistrasiPa from '@/Pages/Penunjang/Patologi/Pendaftaran/components/RowFormRegistrasiPa.vue';
import ModalHasilPa from '@/Pages/Penunjang/Patologi/Pemeriksaan/components/ModalHasilPa.vue';

export default {
    name : 'form-hasil-pa',
    components : { 
        SearchSelect,
        SearchList,
        ModalHasilPa,
        RowFormRegistrasiPa,
     },
    
    data() {
        return {
            isUpdate : false,
            isLoading : false,
            dokterUrl : '/doctors',
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],
            
            registrasi : {
                reg_id : null,
                trx_id : null,
                reff_trx_id : null,
                client_id : null,
                is_rujukan_int : false,
                jns_registrasi : 'PATOLOGI',
                tgl_transaksi : this.getTodayDate(),
                tgl_periksa : this.getTodayDate(),
                jam_periksa : null,
                
                dokter_unit_id : null,
                dokter_id : null,
                dokter_nama : null,
                
                dokter_pengirim_id: null,
                dokter_pengirim: null,
                
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                
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

                kelas_harga_id : null,
                kelas_harga : null,
                buku_harga_id : null,
                buku_harga : null,
                total : 0,

                is_bpjs : false,
                status : null,
                is_aktif : true,
                client_id : null,
                is_realisasi : false,     
                
                diagnosa : null,
                ket_klinis : null,
                is_multi_hasil : false,
                jenis_cito : null, 
                is_cito : null,
                no_spesimen : null,
                media_hasil : null,
                expertise_id : null,
                expertise_nama : null,
                tgl_hasil : this.getTodayDate(),
                tgl_diserahkan : null,
                diserahkan_oleh : null,
                penerima_hasil : null,
                hub_penerima : null,

                detail : [],
                normal_group : null,
                hasil : [],
            },

            unitLists : [],
            dokterLists : [],
            roomLists : [],
            kelasHargaLists : [],
            minDate : this.getTodayDate(),
            labOptionValues : [],
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelas',['classLists']),        
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
        ...mapGetters('asuransi',['asuransiLists']),        
        ...mapGetters('refPenunjang',['groupLabNormalRefs','satuanLabNormalRefs','klasifikasiItemLabRefs','mediaHasilRefs']),
        ...mapGetters('referensi',['isRefExist','jenisPelayananRefs','jenisRegistrasiRefs','asalPasienRefs',
            'caraRegistrasiRefs','salutRefs','jenisPenjaminRefs','hubKeluargaRefs',]),    
    },

    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('patologi',[
                'dataPatologi', 'createPatologi', 'updatePatologi', 'deletePatologi',
                'confirmPatologi', 'unConfirmPatologi', 'dataHasilPatologi', 'updateHasilPatologi',
            ]),            
        ...mapActions('refPenunjang',['listRefPenunjang']),
        ...mapActions('kelas',['listKelas']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();  
            this.listKelas({per_page:'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.kelasHargaLists = response.data.data.filter(function (el) { return el.is_kelas_harga == true; });
                    let params = { is_aktif:true, per_page:'ALL' };
                    this.listRefPenunjang(params);
                } 
                else { alert(response.message); }
            })  

            this.listUnitPelayanan({'jenis_reg':this.registrasi.jns_registrasi, 'per_page':'ALL'}).then((response) => {
                if (response.success == true) { 
                    this.unitLists = response.data.data;
                }
            })
        },

        expertiseSelected(data) {
            if(data) {
                this.registrasi.expertise_id = data.dokter_id;
                this.registrasi.expertise_nama = data.dokter_nama;
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

        getTimeNow(){
            let currentDate = new Date();
            let hours = currentDate.getHours();
            if(hours < 10){ hours = `0${hours}` };
            let minutes = currentDate.getMinutes();
            if(minutes < 10){ minutes = `0${minutes}` };
            
            let time = hours + ":" + minutes;
            return time;
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        /** function called before modal closed */
        modalClosed(){
            this.clearRegistrasi();
            this.isUpdate = false;
            this.$emit('resultPaClosed',true);
        },

        refreshData(){
            let data = JSON.parse(JSON.stringify(this.registrasi));
            this.viewData(data);
        },

        viewData(refData) {
            if(refData) {
                this.CLR_ERRORS();
                this.clearRegistrasi();
                this.isUpdate = true;
                this.isLoading = true;
                if(refData) {
                    this.dataHasilPatologi(refData.trx_id).then((response) => {
                        if (response.success == true) {
                            this.fillRegistrasi(response.data);
                            this.isUpdate = true;
                            this.isLoading = false;
                        }
                        else { alert('data hasil patologi tidak ditemukan'); this.isLoading = false; }
                    })
                }
            }
        },

        clearRegistrasi() {
            this.registrasi.reg_id = null;
            this.registrasi.trx_id = null;
            this.registrasi.reff_trx_id = null;
            this.registrasi.client_id = null;
            this.registrasi.is_rujukan_int = false;
            this.registrasi.jns_registrasi = 'LABORATORIUM';
            this.registrasi.tgl_transaksi = this.getTodayDate();
            this.registrasi.tgl_periksa = this.getTodayDate();
            this.registrasi.jam_periksa = this.getTimeNow();
                
            this.registrasi.dokter_unit_id = null;
            this.registrasi.dokter_id = null;
            this.registrasi.dokter_nama = null;
                
            this.registrasi.dokter_pengirim_id= null;
            this.registrasi.dokter_pengirim= null;
                
            this.registrasi.unit_id = null;
            this.registrasi.unit_nama = null;
            this.registrasi.ruang_id = null;
            this.registrasi.ruang_nama = null;
                
            this.registrasi.unit_asal_id = null;
            this.registrasi.unit_asal = null;
            this.registrasi.ruang_asal_id = null;
            this.registrasi.ruang_asal = null;
            this.registrasi.bed_asal_id = null;
            this.registrasi.no_asal_bed = null;

            this.registrasi.cara_registrasi = null;
            this.registrasi.asal_pasien = null;
            this.registrasi.ket_asal_pasien = null;
                
            this.registrasi.pasien_id = null;
            this.registrasi.no_rm = null;
            this.registrasi.nama_pasien = null;
            this.registrasi.tempat_lahir = null;
            this.registrasi.tgl_lahir = null;
            this.registrasi.nik = null;
            this.registrasi.jns_kelamin = null;

            this.registrasi.is_pasien_baru = null;
            this.registrasi.is_pasien_luar = false;
                
            this.registrasi.jns_penjamin = null;
            this.registrasi.penjamin_id = null;      
            this.registrasi.buku_harga_id = null;    
            this.registrasi.total = 0;      
                      
            this.registrasi.penjamin_nama = null;
            this.registrasi.is_bpjs = false;
            this.registrasi.status = null;
            this.registrasi.is_aktif = true;
            this.registrasi.client_id = null;
            this.registrasi.is_realisasi = false;

            this.registrasi.hub_penerima = null;
            this.registrasi.penerima_hasil = null;

            this.registrasi.diagnosa = null;
            this.registrasi.ket_klinis = null;
            this.registrasi.is_multi_hasil = true;
            this.registrasi.jenis_cito = null; 
            this.registrasi.is_cito = null;
            this.registrasi.media_hasil = null;
            this.registrasi.expertise_id = null;
            this.registrasi.expertise_nama = null;

            this.registrasi.hasil = [];
            
            this.registrasi.detail = [];
            this.minDate = this.getTodayDate();   

        },

        fillRegistrasi(data){
            let unit = this.unitLists.find( item => item.unit_id == data.unit_id );
            if(unit) { 
                this.roomLists = unit.ruang; 
                this.dokterLists = unit.dokter; 
            }

            this.registrasi.reg_id = data.reg_id;
            this.registrasi.trx_id = data.trx_id;
            this.registrasi.reff_trx_id = data.reff_trx_id;
            this.registrasi.is_rujukan_int = data.is_rujukan_int;
            
            this.registrasi.client_id = data.client_id;
            this.registrasi.jns_registrasi = data.jns_registrasi;
            this.registrasi.cara_registrasi = data.cara_registrasi;
            this.registrasi.tgl_transaksi = data.tgl_transaksi;
                
            this.registrasi.tgl_periksa = data.tgl_periksa;
            this.registrasi.jam_periksa = data.jam_periksa;
                
            this.registrasi.dokter_unit_id = data.dokter_unit_id;
            this.registrasi.dokter_id = data.dokter_id;
            this.registrasi.dokter_nama = data.dokter_nama;
            this.registrasi.dokter_pengirim_id = data.dokter_pengirim_id;
            this.registrasi.dokter_pengirim = data.dokter_pengirim;
                
            this.registrasi.unit_id = data.unit_id;
            this.registrasi.unit_nama = data.unit_nama;
            this.registrasi.ruang_id = data.ruang_id;
            this.registrasi.ruang_nama = data.ruang_nama;
            
            this.registrasi.unit_asal_id = data.unit_asal_id;
            this.registrasi.unit_asal = data.unit_asal;
            this.registrasi.ruang_asal_id = data.ruang_asal_id;
            this.registrasi.ruang_asal = data.ruang_asal;
            this.registrasi.bed_asal_id = data.bed_asal_id;
            this.registrasi.no_asal_bed = data.no_asal_bed;
            
            this.registrasi.kelas_harga_id = data.kelas_harga_id;
            this.registrasi.kelas_harga = data.kelas_harga;
            
            this.registrasi.total = 0; //data.transaksi.total;
            
            this.registrasi.asal_pasien = data.asal_pasien;
            this.registrasi.ket_asal_pasien = data.ket_asal_pasien;

            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.nama_pasien = data.nama_pasien;
            this.registrasi.no_rm = data.no_rm;
            this.registrasi.jns_kelamin = data.jns_kelamin;
            this.registrasi.nik = data.nik;
            
            this.registrasi.tempat_lahir = data.tempat_lahir;
            this.registrasi.tgl_lahir = data.tgl_lahir;
            
            this.registrasi.is_pasien_baru = data.is_pasien_baru;
            this.registrasi.is_pasien_luar = data.is_pasien_luar;
                
            this.registrasi.jns_penjamin = data.jns_penjamin;
            this.registrasi.penjamin_id = data.penjamin_id;                
            this.registrasi.penjamin_nama = data.penjamin_nama;
            this.registrasi.no_kepesertaan = data.no_kepesertaan;
            this.registrasi.is_bpjs = data.is_bpjs;

            this.registrasi.buku_harga = data.buku_harga;
            this.registrasi.buku_harga_id = data.buku_harga_id;
            
            this.registrasi.no_spesimen = data.no_spesimen;

            this.registrasi.status = data.status;
            this.registrasi.is_aktif = data.is_aktif;
            this.registrasi.client_id = data.client_id;
            this.registrasi.is_realisasi = data.is_realisasi;
            this.registrasi.detail = data.detail;
            this.registrasi.hasil = data.hasil;
            
            this.registrasi.hub_penerima = data.hub_penerima;
            this.registrasi.penerima_hasil = data.penerima_hasil;
            
            this.registrasi.diagnosa = data.diagnosa;
            this.registrasi.ket_klinis = data.ket_klinis;
            this.registrasi.is_multi_hasil = data.is_multi_hasil;
            this.registrasi.jenis_cito = data.jenis_cito; 
            this.registrasi.is_cito = data.is_cito;
            this.registrasi.media_hasil = data.media_hasil;
            this.registrasi.expertise_id = data.expertise_id;
            this.registrasi.expertise_nama = data.expertise_nama;

            this.minDate = this.registrasi.tgl_periksa;
            this.isUpdate = true;

            if(this.registrasi.jns_kelamin == "L") { this.registrasi.jns_kelamin = "LAKI-LAKI"; }
            else if(this.registrasi.jns_kelamin == "P") { this.registrasi.jns_kelamin = "PEREMPUAN"; }
            else { this.registrasi.jns_kelamin = "TIDAK TAHU"; }

            let klsVal = this.kelasHargaLists.find(item => item.kelas_id == this.registrasi.kelas_harga_id );
            this.registrasi.kelas_harga = klsVal.kelas_nama;
            this.fillItemHasil();
        },

        batalKonfirmasiReg(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan Mengubah status data registrasi ${this.registrasi.trx_id} - ${this.registrasi.nama_pasien} kembali ke DAFTAR dan menghapus pencatatan jurnal. Lanjutkan ?`)){
                this.isLoading = true;
                this.unConfirmPatologi(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; this.isUpdate = false;
                        alert('konfirmasi registrasi patologi BERHASIL dibatalkan.');
                        this.$emit('confirmPaCancel',response.data);
                    }
                    else { 
                        this.isLoading = false;
                        alert('konfirmasi registrasi patologi tidak berhasil dibatalkan.'); 
                    }
                })
            }
        },

        fillItemHasil(){
            if(this.registrasi.normal_group == null) {
                return false;
            }

            this.registrasi.hasil.forEach(data => {
                let normalVal = data.normal.find(item=>item.normal_group == this.registrasi.normal_group);
                console.log(this.registrasi.jns_kelamin);
                if(normalVal) {
                    data.satuan = normalVal.satuan;
                    data.jns_kelamin = this.registrasi.jns_kelamin;
                    data.normal_group = normalVal.normal_group;
                    data.jenis_hasil = normalVal.jenis_hasil;
                    data.hasil_normal_lk = normalVal.lk_normal; 
                    data.hasil_normal_pr = normalVal.pr_normal;
                            
                    if(this.registrasi.jns_kelamin == 'LAKI-LAKI') {
                        
                        data.hasil_operator = normalVal.lk_operator;
                        data.hasil_pilihan = normalVal.lk_nilai_pilihan;
                        data.hasil_min = normalVal.lk_nilai_min;
                        data.hasil_maks = normalVal.lk_nilai_maks;
                        if(data.jenis_hasil == 'PILIHAN') {
                            this.labOptionValues = data.hasil_pilihan.split(";");
                            console.log(this.labOptionValues);
                        }
                    }
                    else if(this.registrasi.jns_kelamin == 'PEREMPUAN'){
                        data.hasil_operator = normalVal.pr_operator;
                        data.hasil_pilihan = normalVal.pr_nilai_pilihan;
                        data.hasil_min = normalVal.pr_nilai_min;
                        data.hasil_maks = normalVal.pr_nilai_maks;
                        if(data.jenis_hasil == 'PILIHAN') {
                            this.labOptionValues = data.hasil_pilihan.split(";");
                            console.log(this.labOptionValues);
                        }
                    }
                }
            })
        },
        
        submitHasilPa(){
            this.CLR_ERRORS();
            if(confirm(`Simpan hasil Patologi ${this.registrasi.trx_id} - ${this.registrasi.nama_pasien} ini ?`)){
                this.isLoading = true;
                this.updateHasilPatologi(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = true;
                        alert('hasil Patologi berhasil diubah.');                    
                    }
                    else { 
                        this.isLoading = false;
                        alert("hasil Patologi tidak berhasil disimpan."); 
                    }
                })
            }
        },

        updateData(data){
            if(data){
                this.$refs.modalHasilPa.showModal(data);
            }
        },

        ubahDataHasilPa(data) {            
            if(this.registrasi.is_multi_hasil) {
                let item = this.registrasi.hasil.find(item => item.tindakan_id == data.tindakan_id );
                if(item) {
                    item.hasil = data.hasil;
                    item.catatan = data.catatan;
                    item.dokter_id = data.dokter_id;
                    item.dokter_nama = data.dokter_nama;
                }
            }
            // else {
            //     this.registrasi.hasil.forEach(function(item){
            //         item.uraian_hasil = data.uraian_hasil;
            //         item.kesan =  data.kesan;
            //         item.catatan = data.catatan;
            //         item.expertise_id = data.expertise_id;
            //         item.expertise_nama = data.expertise_nama;
            //         item.jenis_foto = data.jenis_foto;
            //         item.no_foto = data.no_foto;
            //     });
            // }
        },

        deleteData(data){
            this.CLR_ERRORS();
            if(confirm(`kosongkan hasil patologi ${this.registrasi.trx_id} - ${this.registrasi.nama_pasien}. Lanjutkan ?`)){
                if(this.registrasi.is_multi_hasil) {
                    let item = this.registrasi.hasil.find(item => item.tindakan_id == data.tindakan_id );
                    if(item) {
                        item.uraian_hasil = null;
                        item.kesan =  null;
                        item.catatan = null;
                        item.expertise_id = null;
                        item.expertise_nama = null;
                        item.jenis_foto = null;
                        item.no_foto = null;
                    }
                }
                else {
                    this.registrasi.hasil.forEach(function(item){
                        item.uraian_hasil = null;
                        item.kesan =  null;
                        item.catatan = null;
                        item.expertise_id = null;
                        item.expertise_nama = null;
                        item.jenis_foto = null;
                        item.no_foto = null;
                    });
                }
            }
        }
    }
}
</script>
<style>
th.tb-header-reg {
    padding:1em; 
    background-color:#cc0202; 
    color:white;
}

.view-lab-container div {
    padding:0 2px 0 2px;margin:0;
}

div.view-lab-container label {
    font-weight:500;
}

.tb-hasil-lab tr td {
    padding:1em; 
    font-size: 12px; 
    text-align:left; 
    color:black;
    background-color:#fff;
}

tr.mark-result td {
    background-color: yellow;
}

</style>