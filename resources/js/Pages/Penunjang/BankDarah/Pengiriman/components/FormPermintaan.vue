<template>
    <div style="margin:0;padding:0;">
        <form action="" @submit.prevent="submitPermintaan" style="margin:0;padding:0;">
            <div class="uk-card-default uk-grid-small" uk-grid style="padding:0;margin:0; border-top:0px solid #cc0202; height: 70px;">
                <div class="uk-width-expand" style="padding:1em;">
                    <h3 style="color:black; font-weight: 400;">{{formTitle}}</h3>
                </div>
                <div class="uk-width-auto" style="padding:0.8em;">
                    <button class="uk-button" @click.prevent="modalClosed" type="button" style="color:#333; font-weight: 500;">TUTUP</button>
                    <button class="uk-button" type="submit" style="color:#fff; font-weight: 500;background-color: #cc0202;margin-left:5px;">SIMPAN</button>
                </div>
            </div>
            <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">     
                <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">                        
                    <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            detail data pasien yang membutuhkan.
                        </p>
                    </div>
                    <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                        <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid> 
                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>No.Registrasi</dt>
                                    <dd>{{permintaan.jns_registrasi}} - {{permintaan.reg_id}}</dd>
                                    <dt>Pasien</dt>
                                    <dd>{{permintaan.no_rm}} - {{permintaan.pasien_nama}}</dd>
                                    <dt>Tempat | Tgl Lahir </dt>
                                    <dd>{{permintaan.tempat_lahir}} {{permintaan.tgl_lahir}}</dd>
                                </dl>
                            </div>
                            <div class="uk-width-1-2@m">
                                <dl class="uk-description-list hims-description-list">
                                    <dt>Dokter </dt>
                                    <dd>{{permintaan.dokter_nama}}</dd>
                                    <dt>Unit</dt>
                                    <dd>{{permintaan.unit_nama}}</dd>
                                    <dt>Ruang - Bed</dt>
                                    <dd>{{permintaan.ruang_nama}} - {{permintaan.bed_no}}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                                        
                <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                    <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA PERMINTAAN</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            detail data permintaan darah.
                        </p>
                    </div>
                    <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                        <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid> 
                            <div class="uk-width-1-1">
                                <h5 style="font-weight:500;">{{permintaan.darah_dist_id}}</h5>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Dibutuhkan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="date" v-model="permintaan.tgl_dibutuhkan" style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Dibutuhkan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="time" v-model="permintaan.jam_dibutuhkan" style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Golongan Darah*</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select uk-form-small" v-model="permintaan.gol_darah" style="color:black;">
                                        <option v-for="dt in golDarahRefs.json_data" :value="dt">{{dt}}</option>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Rhesus*</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select uk-form-small" v-model="permintaan.rhesus" style="color:black;">
                                        <option v-for="dt in rhesusRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis (Group)*</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select uk-form-small" v-model="permintaan.group_darah" style="color:black;">
                                        <!-- <option v-for="dt in groupDarahRefs.json_data" :value="dt.value">{{dt.value}}</option> -->
                                        <option v-for="dt in groupDarahLists" :value="dt.jenis_produk_darah">{{dt.jenis_produk_darah}} ({{dt.inisial}})</option>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jml Kantung*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="number" v-model="permintaan.jml_permintaan" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Volume/Kantung(cc)*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="number" v-model="permintaan.volume_per_labu" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Hasil Hb(gr/dL)*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="text" v-model="permintaan.haemoglobin" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diagnosa*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="text" v-model="permintaan.diagnosa" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diajukan Oleh*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="permintaan.peminta" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea uk-form-small" type="text" v-model="permintaan.catatan" style="color:black;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-permintaan',
    components : { SearchSelect,SearchList },
    data() {
        return {
            formTitle : 'PENGIRIMAN DARAH',
            isUpdate : true,
            isLoading : true,
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
            cssdDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],
            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],
            satuanDesc : [
                { colName : 'value', labelData : '', isTitle : true },
                { colName : 'text', labelData : '', isTitle : false },
            ],   
            permintaan : {
                darah_dist_id : null,
                reg_id : null,
                jns_registrasi : null,
                dokter_id : null,
                dokter_nama : null,
                pasien_id : null,
                pasien_nama : null,
                no_rm : null,
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                bed_no : null,
                penjamin_id : null,
                penjamin_nama : null,
                is_bpjs : false,
                usia : null,
                tempat_lahir : null,
                tgl_lahir : null,
                jenis_kelamin : null,
                tgl_permintaan : null,
                jam_permintaan : null,
                peminta : null,
                tgl_dibutuhkan : null,
                jam_dibutuhkan : null,

                tgl_distribusi : null,
                jam_distribusi : null,
                pengirim : null,
                penerima : null,
                gol_darah : null,
                group_darah: null,
                rhesus : null,
                haemoglobin : null,
                jml_permintaan : null,
                volume_per_labu : null,
                diagnosa : null,
                is_kirim : false,
                status : null,
                catatan : null,
                is_aktif : true,
                client_id : null,
                items : [],
            },
            itemUrl : '',
            unitUrl : '',
            groupDarahLists : null,
            asalDarahLists : null,
        }
    },

    computed : {
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
        ...mapGetters('referensi',['isRefExist','golDarahRefs','rhesusRefs','groupDarahRefs','asalDarahRefs',]),
    },

    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapActions('bankDarah',[
            'getDarahLists',
            'createPenerimaanDarah',
            'updatePenerimaanDarah',
            'deletePenerimaanDarah',
            'dataPenerimaanDarah',
            'createPermintaanDarah',
            'updatePermintaanDarah',
            'dataPermintaanDarah',
        
        ]),
        ...mapActions('produkDarah',['listProdukDarah']),
        ...mapActions('asalDarah',['listAsalDarah']),

        ...mapActions('referensi',['listReferensi']),
        ...mapActions('refProduk',['listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();
            this.listRefProduk({per_page:'ALL'});
            this.clearPermintaan();

            this.listProdukDarah({per_page:'ALL'}).then((response) => {
                if (response.success == true) {
                    this.groupDarahLists = response.data.data;
                }
            });

            this.listAsalDarah({per_page:'ALL'}).then((response) => {
                if (response.success == true) {
                    this.asalDarahLists = response.data.data;
                }
            });
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

        /** function called before modal closed */
        modalClosed(){
            this.clearPermintaan();
            this.$emit('formPermintaanDarahClosed',true);      
        },

        /**modal call from other component for new data entry */
        newData(){            
            this.CLR_ERRORS();
            this.clearPermintaan();
            this.isUpdate = false;
            this.formTitle = "DATA BARU PERMINTAAN DARAH";
        },
        
        /**modal call from other component for update data entry */
        editData(refData) {
            this.CLR_ERRORS();
            this.clearPermintaan();
            this.formTitle = "UBAH DATA PERMINTAAN DARAH";
            this.isUpdate = true;
            this.isLoading = true;

            if(refData) {
                this.dataPermintaanDarah(refData.darah_dist_id).then((response) => {
                    if (response.success == true) {
                        this.fillPermintaan(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        this.$emit('retrievePermintaanSuccess',true);
                    }
                    else { alert('data permintaan tidak ditemukan'); }
                })
            }
        },

        clearPermintaan() {
            this.permintaan.darah_dist_id = null;
            this.permintaan.reg_id = 'REG20220201.REG0001';//null;
            this.permintaan.jns_registrasi = 'RAWAT INAP';//null;
            this.permintaan.dokter_id = 'DOK001'; //null;
            this.permintaan.dokter_nama = 'Dr.JOHN DOE, Sp.A';//null;
            this.permintaan.pasien_id = 'PSN000211';//null;
            this.permintaan.pasien_nama = 'JANE DOE';//null;
            this.permintaan.no_rm = '000001';//null;
            this.permintaan.unit_id = 'UNIT0020';//null;
            this.permintaan.unit_nama = 'KEBIDANAN DAN KANDUNGAN'; //null;
            this.permintaan.ruang_id = 'RU002'; //null;
            this.permintaan.ruang_nama = 'EDELWEIS'; //null;
            this.permintaan.bed_no = 'BED001';//null;
            this.permintaan.penjamin_id = 'INS202210.0001';//null;
            this.permintaan.penjamin_nama = 'BADAN PENYELENGGARA JAMINAN SOSIAL';//null;
            this.permintaan.is_bpjs = true;//false;
            this.permintaan.usia = '21 THN 3 BLN';//null;
            this.permintaan.tempat_lahir = 'JAKARTA';//null;
            this.permintaan.tgl_lahir = '2000-01-11';//null;
            this.permintaan.jenis_kelamin = 'P';//null;
            
            this.permintaan.tgl_permintaan = this.getTodayDate();
            this.permintaan.jam_permintaan = null;
            
            this.permintaan.tgl_dibutuhkan = this.getTodayDate();
            this.permintaan.jam_dibutuhkan = null;

            this.permintaan.tgl_distribusi = this.getTodayDate();
            this.permintaan.jam_distribusi = null;
            
            this.permintaan.peminta = null;
            this.permintaan.pengirim = null;
            this.permintaan.penerima = null;
            this.permintaan.gol_darah = null;
            this.permintaan.group_darah = null;
            
            this.permintaan.rhesus = null;
            this.permintaan.haemoglobin = null;
            this.permintaan.jml_permintaan = null;
            this.permintaan.volume_per_labu = null;
            this.permintaan.diagnosa = null;
            this.permintaan.is_kirim = false;
            this.permintaan.status = 'PERMINTAAN';
            this.permintaan.catatan = null;
            this.permintaan.is_aktif = true;
            this.permintaan.client_id = null;
            this.permintaan.items = [];
        },

        fillPermintaan(data) {
            if(data) {
                this.permintaan.darah_dist_id = data.darah_dist_id;
                this.permintaan.reg_id = data.reg_id;
                this.permintaan.jns_registrasi = data.jns_registrasi;
                this.permintaan.dokter_id = data.dokter_id;
                this.permintaan.dokter_nama = data.dokter_nama;
                this.permintaan.pasien_id = data.pasien_id;
                this.permintaan.pasien_nama = data.pasien_nama;
                this.permintaan.no_rm = data.no_rm;
                this.permintaan.unit_id = data.unit_id;
                this.permintaan.unit_nama = data.unit_nama;
                this.permintaan.ruang_id = data.ruang_id;
                this.permintaan.ruang_nama = data.ruang_nama;
                this.permintaan.bed_no = data.bed_no;
                this.permintaan.penjamin_id = data.penjamin_id;
                this.permintaan.penjamin_nama = data.penjamin_nama;
                this.permintaan.is_bpjs = false;
                this.permintaan.usia = data.usia;
                this.permintaan.tempat_lahir = data.tempat_lahir;
                this.permintaan.tgl_lahir = data.tgl_lahir;
                this.permintaan.jenis_kelamin = data.jenis_kelamin;
                
                this.permintaan.tgl_permintaan = data.tgl_permintaan;
                this.permintaan.jam_permintaan = data.jam_permintaan;
                this.permintaan.tgl_dibutuhkan = data.tgl_dibutuhkan;
                this.permintaan.jam_dibutuhkan = data.jam_dibutuhkan;
                this.permintaan.tgl_distribusi = data.tgl_distribusi;
                this.permintaan.jam_distribusi = data.jam_distribusi;
                
                this.permintaan.peminta = data.peminta;
                this.permintaan.pengirim = data.pengirim;
                this.permintaan.penerima = data.penerima;
                this.permintaan.gol_darah = data.gol_darah;
                this.permintaan.group_darah = data.group_darah;
            
                this.permintaan.rhesus = data.rhesus;
                this.permintaan.haemoglobin = data.haemoglobin;
                this.permintaan.jml_permintaan = data.jml_permintaan;
                this.permintaan.volume_per_labu = data.volume_per_labu;
                this.permintaan.diagnosa = data.diagnosa;
                this.permintaan.is_kirim = data.is_kirim;
                this.permintaan.status = data.status;
                this.permintaan.catatan = data.catatan;
                this.permintaan.is_aktif = data.is_aktif;
                this.permintaan.client_id = null;
                this.permintaan.items = data.items;
            }
        },

        submitPermintaan(){
            this.CLR_ERRORS();
            this.isLoading = true;      
            if(this.isUpdate) {
                //update data
                this.updatePermintaanDarah(this.permintaan).then((response) => {
                    if (response.success == true) {
                        this.fillPermintaan(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        alert('Data berhasil disimpan.');
                        this.modalClosed();
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
            else {
                //data baru
                this.createPermintaanDarah(this.permintaan).then((response) => {
                    if (response.success == true) {
                        this.fillPermintaan(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        alert('Data berhasil disimpan.');
                        this.modalClosed();
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
        }
    }
}
</script>
<style>

dl.hims-description-list dt {
    margin:0;
    padding:0;
    font-size:11px;
    font-weight: bold;
    color:#333;
}

dl.hims-description-list dd {
    margin:0;
    padding:0;
    font-size:14px;
    font-weight: 400;
    color:#000;
}

</style>