<template>
    <div class="" style="margin:0;padding:0;">        
        <div style="margin:0;padding:0;">
            <form action="" @submit.prevent="submitHasilLab">
                <div style="margin:0;padding:0;">
                    <div style="padding:0.5em 0.5em 0.5em 1em;position: sticky; position: -webkit-sticky;top: 0; z-index:99;">
                        <div class="uk-grid-small uk-card1 uk-card-default1 hims-form-header" uk-grid style="padding:0;margin:0;">                        
                            <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                                <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                                    <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="modalClosed" style="padding:0.5em;"><span uk-icon="arrow-left"></span> Tutup</button>
                                    <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="refreshData" style="padding:0.5em;"><span uk-icon="refresh"></span> Refresh</button>
                                    <button :disabled="isLoading" type="button" @click.prevent="batalKonfirmasiReg" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-edit"></span> Ubah Pemeriksaan</button>
                                    <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                                    <button type="button" class="hims-table-btn uk-width-auto" @click="cetakHasil" :disabled="!isUpdate" style="padding:0.5em;"><span uk-icon="file-pdf"></span> Cetak</button>
                                    <button :disabled="isLoading" type="submit" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-text"></span> Simpan</button>
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
                                        <dt>Tanggal</dt>
                                        <dd>{{registrasi.tgl_transaksi}}</dd>
                                    </dl>
                                </div>                        
                            </div> 

                            <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                        detail data pasien.
                                    </p>
                                </div>
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0 0.5em;"> 
                                    <div class="uk-width-1-1" style="margin:0;padding:0;"></div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.nama_pasien" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
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
                                    </div>
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
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Penjamin*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.jns_penjamin" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penjamin / Asuransi</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.penjamin_nama" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
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
                                    </div>
                                </div>
                            </div>
                                    
                            <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">DATA REGISTRASI</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                        detail data pendaftaran laboratorium.
                                    </p>
                                </div>
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Periksa*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_periksa" style="color:black;" :min="minDate" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Periksa*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="time" v-model="registrasi.jam_periksa" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.unit_nama" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Ruang</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.ruang_nama" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Pemeriksa</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.dokter_nama" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Pengirim</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.dokter_pengirim" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Layanan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="registrasi.kelas_harga" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>                                        
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Asal Pasien</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.asal_pasien" style="color:black;"  disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                                <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0.5em 0.5em 0 0.5em;">
                                    <div class="uk-width-expand@m">
                                        <h5 style="color:indigo;padding:0;margin:0;">PEMERIKSAAN LAB
                                            <span style="font-size:12px;font-style:italic;padding:0;margin:0 0 0 0.2em;">detail pemeriksaan lab.</span>
                                        </h5>
                                    </div> 
                                    <div class="uk-width-1-2@m uk-grid-small" uk-grid>
                                        <div class="uk-width-auto">
                                            <span style="font-size:12px; font-weight:500;font-style:italic; color:black;margin-right:5px;">Group Nilai Normal</span>
                                        </div>
                                        <div class="uk-width-expand">
                                            <select v-model="registrasi.normal_group" class="uk-form-small uk-select" style="width:100%;" @change="fillItemHasil" required>
                                                <option>pilih salah satu</option>
                                                <option v-for="dt in groupLabNormalRefs.json_data"  :value="dt.value">{{dt.value}}</option>
                                            </select>
                                        </div>
                                    </div>                                  
                                </div>
                                <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                    <table class="uk-table uk-table-striped child-table tb-hasil-lab" style="padding:0;margin:0;">
                                        <thead>
                                            <!-- <th class="tb-header-reg" style="width:100px;text-align:left;color:white;">ID</th> -->
                                            <th class="tb-header-reg" style="width:100px;text-align:left;color:white;">KLASIFIKASI</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">PEMERIKSAAN</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:left;color:white;">JNS HASIL</th>
                                            <th class="tb-header-reg" style="width:100px;text-align:center;color:white;">HASIL</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:left;color:white;">SATUAN</th>
                                            <th class="tb-header-reg" style="width:140px;text-align:left;color:white;">ACUAN</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:center;color:white;">TANDAI</th>
                                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">No URUT</th>
                                        </thead>
                                        <tbody> 
                                            <tr v-for="res in registrasi.hasil" v-bind:class="res.is_tandai == true ? 'mark-result':'' ">
                                                <td style="width:100px;text-align:left;">
                                                    <p style="margin:0;padding:0;font-weight: 500;">{{res.klasifikasi}}</p>
                                                    <p style="margin:0;padding:0;font-style: italic;font-size: 10px;">{{res.sub_klasifikasi}}</p>
                                                </td>
                                                <td >
                                                    <p style="margin:0;padding:0;font-weight: 500;">{{res.hasil_nama}}</p>
                                                    <p style="margin:0;padding:0;font-style: italic;font-size: 10px;">{{res.lab_hasil_id}}</p>
                                                </td>
                                                <td style="width:80px;text-align:left;">
                                                    <p style="margin:0;padding:0;font-weight: 500;">{{res.jenis_hasil}}</p>
                                                    <p style="margin:0;padding:0;font-style: italic;font-size: 10px;">{{res.normal_group}}</p>
                                                </td>

                                                <td style="width:100px;text-align:center;">
                                                    <input v-if = "res.jenis_hasil == 'NILAI'" v-model="res.hasil_nilai" type="number" step="0.001" class="uk-input uk-form-small" @change="resultDataChange(res)">
                                                    <select v-else-if = "res.jenis_hasil == 'PILIHAN'"  v-model="res.hasil_nilai" class="uk-select uk-form-small"  @change="resultDataChange(res)">
                                                        <option></option>
                                                        <option v-for="opt in labOptionValues" :value="opt">{{opt}}</option>
                                                    </select>
                                                    <input v-else v-model="res.hasil_nilai" type="text" class="uk-input uk-form-small">
                                                </td>
                                                <td style="width:80px;text-align:left;">{{res.satuan}}</td>
                                                <td style="width:140px; text-align:left;width:auto;background-color: #eee;">
                                                    <p style="margin:0;padding:0;font-size: 10px;width:auto; font-style: italic;"><span style="font-weight: 500;">L : </span>{{res.hasil_normal_lk}}</p>
                                                    <p style="margin:0;padding:0;font-size: 10px;width:auto; font-style: italic;"><span style="font-weight: 500;">P : </span>{{res.hasil_normal_pr}}</p>
                                                </td>
                                                
                                                <td style="width:80px; text-align:center;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="res.is_tandai" style="border:1px solid black;">
                                                </td>
                                                <td style="width:60px;text-align:center;">
                                                    <input v-model="res.no_urut" type="text" class="uk-input uk-form-small" style="text-align:center;">
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
        <cetakan-hasil-lab ref="cetakanHasilLab"></cetakan-hasil-lab>
    </div>
    
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';
//import ModalPickerRuang from '@/Pages/Pendaftaran/RawatInap/components/ModalPickerRuang';
import ListPasien from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/ListPasien';
import RowFormRegistrasiLab from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/RowFormRegistrasiLab.vue';
import CetakanHasilLab from '@/Pages/Penunjang/Laboratorium/Cetakan/CetakanHasilLab.vue';

export default {
    name : 'form-hasil-lab',
    components : { 
        SearchSelect,
        SearchList,
        //ModalPickerRuang, 
        ListPasien,
        RowFormRegistrasiLab,
        CetakanHasilLab
     },
    
    data() {
        return {
            isUpdate : false,
            isLoading : false,
            urlPicker : '',
            dokterUrl : '/doctors',
            isLocked : false,
            isExpandedReg : false,
            pickerColumns : [ 
                { name : 'tindakan_id', title : 'ID', colType : 'text', isCaption : false, },
                { name : 'tindakan_nama', title : 'Tindakan', colType : 'text', isCaption : true, },
                { name : 'deskripsi',title : 'Kelompok', colType : 'text', isCaption : false, },            
                { name : 'klasifikasi',title : 'Kelompok', colType : 'text', isCaption : false, },            
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            penjaminDesc: [
                { colName : 'penjamin_nama', labelData : '', isTitle : true },
                { colName : 'penjamin_id', labelData : '', isTitle : false },
            ],
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],

            asalPasienList : [
                { value : 'DATANG SENDIRI', title : 'DATANG SENDIRI', isInternal : false, isPasienLuar:false, },
                { value : 'RAWAT JALAN', title : 'RAWAT JALAN', isInternal : true, isPasienLuar:false, },
                { value : 'RAWAT INAP', title : 'RAWAT INAP', isInternal : true, isPasienLuar:false, },
                { value : 'PASIEN LUAR', title : 'PASIEN LUAR', isInternal : false, isPasienLuar: true, },
            ],
            
            registrasi : {
                reg_id : null,
                trx_id : null,
                reff_trx_id : null,
                client_id : null,
                is_rujukan_int : false,
                jns_registrasi : 'LABORATORIUM',
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
                reff_trx_id : null,
                is_realisasi : false,                
                detail : [],
                normal_group : null,
                hasil : [],
            },

            unitLists : [],
            dokterLists : [],
            roomLists : [],
            kelasHargaLists : [],
            minDate : this.getTodayDate(),
            groupNormalLab : [],
            labOptionValues : [],
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelas',['classLists']),
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
        ...mapGetters('asuransi',['asuransiLists']),        
        ...mapGetters('refPenunjang',['groupLabNormalRefs','satuanLabNormalRefs','klasifikasiItemLabRefs']),
        ...mapGetters('referensi',['isRefExist','jenisPelayananRefs','jenisRegistrasiRefs','asalPasienRefs',
            'caraRegistrasiRefs','salutRefs','jenisPenjaminRefs','hubKeluargaRefs',]),    
    },

    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapActions('hasilLab',['dataLabResult','updateLabResult']),    
        ...mapActions('labRegistrasi',['dataRegistrasi','createRegistrasi','updateRegistrasi','deleteRegistrasi','confirmRegistrasi','unConfirmRegistrasi']),
            
        ...mapActions('refPenunjang',['listRefPenunjang']),
        ...mapActions('kelas',['listKelas']),
        ...mapActions('cetakan',['dataCetakanHasilLab']),
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
            this.$emit('resultLabClosed',true);
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
                    this.dataLabResult(refData.trx_id).then((response) => {
                        if (response.success == true) {
                            this.fillRegistrasi(response.data);
                            this.isUpdate = true;
                            this.isLoading = false;
                        }
                        else { alert(response.message); this.isLoading = false; }
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
            this.registrasi.reff_trx_id = null;
            this.registrasi.is_realisasi = false;
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
                
            this.registrasi.status = data.status;
            this.registrasi.is_aktif = data.is_aktif;
            this.registrasi.client_id = data.client_id;
            this.registrasi.is_realisasi = data.is_realisasi;
            this.registrasi.detail = data.detail;
            this.registrasi.hasil = data.hasil;
            this.registrasi.normal_group = data.normal_group;
            
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
                this.unConfirmRegistrasi(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; this.isUpdate = false;
                        alert('konfirmasi registrasi lab BERHASIL dibatalkan.');
                        this.$emit('confirmLabCancel',response.data);
                    }
                    else { 
                        this.isLoading = false;
                        alert('konfirmasi registrasi lab tidak berhasil dibatalkan.'); 
                    }
                })
            }
        },

        calculateTotalLab(){  
            let total = 0;          
            this.registrasi.detail.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.registrasi.total = total; 
            })
        },

        fillItemHasil(){
            if(this.registrasi.normal_group == null) {
                return false;
            }

            this.registrasi.hasil.forEach(data => {
                let normalVal = data.normal.find(item=>item.normal_group == this.registrasi.normal_group);
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
                        }
                    }
                    else if(this.registrasi.jns_kelamin == 'PEREMPUAN'){
                        data.hasil_operator = normalVal.pr_operator;
                        data.hasil_pilihan = normalVal.pr_nilai_pilihan;
                        data.hasil_min = normalVal.pr_nilai_min;
                        data.hasil_maks = normalVal.pr_nilai_maks;
                        if(data.jenis_hasil == 'PILIHAN') {
                            this.labOptionValues = data.hasil_pilihan.split(";");
                        }
                    }
                }
            })
        },

        resultDataChange(data) {
            if(data) {
                if(data.jenis_hasil == 'NILAI') {
                    if(data.hasil_operator == '-') {
                        let val = data.hasil_nilai * 1;
                        if(val > data.hasil_maks || val < data.hasil_min) {
                            data.is_tandai = true;
                        }
                        else {
                            data.is_tandai = false;
                        }
                    }
                }
                else if(data.jenis_hasil == 'PILIHAN') {
                    if(data.jns_kelamin == 'LAKI-LAKI') {
                        if(data.hasil_nilai !== data.hasil_normal_lk) {
                            data.is_tandai = true;
                        }
                        else {
                            data.is_tandai = false;
                        }
                    }
                    else if(data.jns_kelamin == 'PEREMPUAN') {
                        if(data.hasil_nilai !== data.hasil_normal_pr) {
                            data.is_tandai = true;
                        }
                        else {
                            data.is_tandai = false;
                        }
                    }
                }
            }
        },

        submitHasilLab(){
            this.CLR_ERRORS();
            if(confirm(`Simpan hasil lab ${this.registrasi.trx_id} - ${this.registrasi.nama_pasien} ini ?`)){
                this.isLoading = true;
                this.updateLabResult(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = true;
                        alert('hasil lab berhasil diubah.');                    
                    }
                    else { 
                        this.isLoading = false;
                        alert("hasil lab tidak berhasil disimpan."); 
                    }
                })
            }
        },

        cetakHasil(){
            this.dataCetakanHasilLab(this.registrasi.trx_id).then((response) => {
                // if (response.success == true) {
                    // this.$refs.cetakanHasilLab.generateReport(response.data);
                    var newNode = document.createElement('p');
                    newNode.appendChild(document.createTextNode('html string'));
                    this.printDiv(response,"8.3in 11.7in");
                // }
                // else { alert (response.message) }
            });
        },

        getDateFormated(curDate){
            const today = new Date(curDate);
            const yyyy = today.getFullYear();
            let mm = today.getMonth() + 1; // Months start at 0!
            let dd = today.getDate();

            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;

            const formattedToday = dd + '/' + mm + '/' + yyyy;
            return formattedToday;
        },

        printDiv(pdf_body, paperSize) {
            const customPaperSize = paperSize;
            let frame1 = document.createElement('iframe');
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            let frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();

            // Set custom paper size using CSS @page rule
            const customStyles = `
                <style>
                    @page {
                        size: ${customPaperSize};
                        margin: 0;
                    }
                    .page-number {
                        font-size:9px;
                        position: absolute;
                        bottom: 10px;
                        right: 10px;
                    }
                    .left-text {
                        font-size:9px;
                        position: absolute;
                        bottom: 10px;
                        left: 10px;
                    }
                </style>
            `;

            // Append page number to each page
            const pages = pdf_body.split('<div class="page-break"></div>'); // Assuming you have a class for page breaks
            let fullHtml = customStyles;
            var Currentdate = this.getDateFormated(new Date());
            for (let i = 0; i < pages.length; i++) {
                const pageNumber = i + 1;
                const pageHtml = pages[i];
                const pageWithNumber = `
                    <div class="page-container">
                        ${pageHtml}
                        <div class="left-text">${Currentdate}</div>
                        <div class="page-number">Page ${pageNumber} of ${pages.length}</div>
                    </div>
                `;
                fullHtml += pageWithNumber;
                if (i < pages.length - 1) {
                    fullHtml += '<div class="page-break"></div>';
                }
            }

            frameDoc.document.write(fullHtml);
            frameDoc.document.close();

            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 1000);
            
            return false;
        },
        

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