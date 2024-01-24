<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalDistribusiDarah" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <!-- <button class="uk-modal-close-default" type="button" uk-close></button> -->
            <div style="margin:0;padding:0;">
                <form action="" @submit.prevent="submitPermintaan" style="margin:0;padding:0;">
                    <div class="uk-card-default uk-grid-small" uk-grid style="position:sticky; top:-50px; padding:0;margin:0; border-top:0px solid #cc0202; height: 70px;">
                        <div class="uk-width-expand" style="padding:1em;">
                            <h3 style="color:black; font-weight: 400;">REALISASI PERMINTAAN DARAH</h3>
                        </div>
                        <div class="uk-width-auto" style="padding:0.8em;">
                            <button class="uk-button" @click.prevent="modalClosed" type="button" style="color:#333; font-weight: 500;">TUTUP</button>
                            <button class="uk-button" type="submit" style="color:#fff; font-weight: 500;background-color: #cc0202;margin-left:5px;">SIMPAN</button>
                        </div>
                    </div>
                    <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">    
                        <div class="uk-width-1-1" style="text-align:center;padding:1em;margin:0;">
                            <h5 style="font-weight:500;padding:0;margin:0;">{{permintaan.darah_dist_id}}</h5>
                        </div> 
                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN
                                    <span style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                        detail data pasien yang membutuhkan.
                                    </span>
                                </h5>
                            </div>
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid>                                     
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>Pasien</dt>
                                            <dd>{{permintaan.no_rm}} - {{permintaan.pasien_nama}}</dd>
                                            <dt>Tempat | Tgl Lahir </dt>
                                            <dd>{{permintaan.tempat_lahir}} {{permintaan.tgl_lahir}}</dd>
                                        </dl>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>No.Registrasi</dt>
                                            <dd>{{permintaan.jns_registrasi}} - {{permintaan.reg_id}}</dd>
                                            <dt>Dokter </dt>
                                            <dd>{{permintaan.dokter_nama}}</dd>
                                        </dl>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">                                            
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
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA PERMINTAAN
                                    <span style="font-size:12px;font-style:italic;padding:0;margin:0;">detail permintaan darah. </span>
                                </h5>
                            </div>
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid>                                     
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>Tgl. Dibutuhkan</dt>
                                            <dd>{{permintaan.tgl_dibutuhkan}} - {{permintaan.jam_dibutuhkan}}</dd>
                                            <dt>Kebutuhan Darah</dt>
                                            <dd>{{permintaan.group_darah}} | (<span style="font-weight: 500;">Gol :</span> {{permintaan.gol_darah}}, <span style="font-weight: 500;">Rh.</span> {{permintaan.rhesus}})</dd>
                                        </dl>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">                                            
                                            <dt>Jumlah Dibutuhkan</dt>
                                            <dd>{{permintaan.jml_permintaan}} Kantung @{{permintaan.volume_per_labu}} cc</dd>
                                            <dt>Diagnosa</dt>
                                            <dd>{{permintaan.diagnosa}}</dd>
                                        </dl>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>Hasil Haemoglobin</dt>
                                            <dd>{{permintaan.haemoglobin}}</dd>
                                            <dt>Catatan</dt>
                                            <dd>{{permintaan.catatan}}</dd>
                                        </dl>
                                    </div>                                    
                                </div>
                            </div>
                        </div>

                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                            <div class="uk-width-1-3@m" style="padding:0 0.5em 0.2em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA DISTRIBUSI
                                    <span style="font-size:12px;font-style:italic;padding:0;margin:0;">info distribusi permintaan darah. </span>
                                </h5>
                            </div>
                            <div class="uk-width-2-3@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0;">
                                <div class="uk-width-1-1@m" style="margin:0;padding:0 0.2em 0 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="permintaan.is_kirim" @change="flagPengirimanChange"> Pengiriman persediaan
                                            <span style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 0.5em;margin:0;">tandai bahwa realisasi pengiriman persediaan.</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="uk-width-1-4@m"> 
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Pengiriman*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="permintaan.tgl_distribusi" required style="color:black;" :disabled="!permintaan.is_kirim">
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Pengiriman*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="time" v-model="permintaan.jam_distribusi" required style="color:black;" :disabled="!permintaan.is_kirim">
                                    </div>
                                </div>
                                <div class="uk-width-1-1@m" style="padding:0;margin:0;"></div>
                                <div class="uk-width-1-2@m"> 
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pengirim*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="permintaan.pengirim" required style="color:black;" :disabled="!permintaan.is_kirim">
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penerima*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="permintaan.penerima" required style="color:black;" :disabled="!permintaan.is_kirim">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                                
                        <div class="uk-grid-small hims-form-subpage" uk-grid >
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.5 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">ITEM DARAH 
                                    <span style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                        daftar item yang didistribusikan
                                    </span>
                                </h5>
                            </div>
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                                <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;margin:0;">
                                    <table class="uk-table hims-table">
                                        <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                                            <tr>
                                                <th style="padding:0.5em;">No</th>
                                                <th style="padding:0.5em;">Produk Darah</th>
                                                <!-- <th style="padding:0.5em;">Volume</th>
                                                <th style="text-align:center;width:70px;padding:0.5em;">Kadaluarsa</th>      -->
                                                <th style="text-align:center;width:120px;padding:0.5em;">Sesuai</th>
                                                <th style="text-align:center;width:80px;padding:0.5em;">Mayor</th>
                                                <th style="text-align:center;width:80px;padding:0.5em;">Minor</th>
                                                <th style="text-align:center;width:80px;padding:0.5em;">AC</th>
                                                <th style="text-align:center;width:80px;padding:0.5em;">DCT</th>
                                                <th style="text-align:center;width:50px;padding:0.5em;">...</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                            
                                            <tr>
                                                <td colspan="2" style="padding:0.2em;margin:0;">    
                                                    <search-list
                                                        :config = configSelect
                                                        :dataLists = darahLists
                                                        placeholder = "pilih no kantung darah"
                                                        captionField = "no_labu"
                                                        valueField = "no_labu"
                                                        :detailItems = darahDesc
                                                        :value = "permintaanItem.no_labu"
                                                        v-on:item-select = "noLabuSelected"
                                                    ></search-list>
                                                    <div v-if="permintaanItem.itemLabuSelected">
                                                        <p style="padding:0;margin:0;font-style: italic;">
                                                            <span style="font-weight: 500;">Gol. </span>{{permintaanItem.gol_darah}}, 
                                                            <span style="font-weight: 500;">Rhe. </span>{{permintaanItem.rhesus}},
                                                            <span style="font-weight: 500;">Jenis. </span>{{permintaanItem.group_darah}},
                                                            <span style="font-weight: 500;">Vol. </span>{{permintaanItem.volume}} cc, 
                                                            <span style="font-weight: 500;">Expr. </span>{{permintaanItem.tgl_kadaluarsa}}
                                                        </p>
                                                    </div>
                                                </td>
                                                <td style="text-align:center;width:120px;padding:0.4em;margin:0;">
                                                    <label style="font-size:14px;margin-right: 0.2em;"><input class="uk-radio" type="radio" v-model="permintaanItem.sesuai" value="Ya"> Ya</label>
                                                    <label style="font-size:14px;"><input class="uk-radio" type="radio" v-model="permintaanItem.sesuai" value="Tidak"> Tidak</label>
                                                </td>
                                                <td style="text-align:center;width:80px;padding:0.2em;margin:0;">
                                                    <label style="font-size:18px;margin-right: 0.2em;"><input class="uk-radio" type="radio" v-model="permintaanItem.mayor" value="+"> +</label>
                                                    <label style="font-size:18px;"><input class="uk-radio" type="radio" v-model="permintaanItem.mayor" value="-"> -</label>
                                                </td>
                                                <td style="text-align:center;padding:0.2em;margin:0;width:80px;">
                                                    <label style="font-size:18px;margin-right: 0.2em;"><input class="uk-radio" type="radio" v-model="permintaanItem.minor" value="+"> +</label>
                                                    <label style="font-size:18px;"><input class="uk-radio" type="radio" v-model="permintaanItem.minor" value="-"> -</label>
                                                </td>
                                                <td style="text-align:center;padding:0.2em;margin:0;width:80px;">
                                                    <label style="font-size:18px;margin-right: 0.2em;"><input class="uk-radio" type="radio" v-model="permintaanItem.ac" value="+"> +</label>
                                                    <label style="font-size:18px;"><input class="uk-radio" type="radio" v-model="permintaanItem.ac" value="-"> -</label>
                                                </td>
                                                <td style="text-align:center;padding:0.2em;margin:0;width:50px;">
                                                    <label style="font-size:18px;margin-right: 0.2em;"><input class="uk-radio" type="radio" v-model="permintaanItem.dct" value="+"> +</label>
                                                    <label style="font-size:18px;"><input class="uk-radio" type="radio" v-model="permintaanItem.dct" value="-"> -</label>
                                                </td>
                                                
                                                <td style="padding:0.5em; font-size:12px;text-align:center;">
                                                    <a href="#" uk-icon="icon:plus-circle;ratio:1" @click.prevent="addDarahItem" :disabled="!permintaan.is_kirim"></a>
                                                </td>                               
                                            </tr>
                                            
                                            <tr :class=" !dt.is_terpakai ? 'data-inactive' : ''" style="border-top:1px solid #ccc;" v-if="permintaan.items.length > 0" v-for="dt in permintaan.items">
                                                <td style="font-weight: 500;font-size: 12px;">{{dt.no_labu}}</td>
                                                <td style="padding:0.3em 0.5em 0 0.5em; margin:0;">
                                                    <p style="padding:0;margin:0;font-weight: 500;">{{dt.group_darah}} ( Gol. {{dt.gol_darah}}, Rhesus. {{dt.rhesus}} )</p>
                                                    <p style="padding:0;margin:0;">vol. {{dt.volume}} cc, Kadaluarsa. {{dt.tgl_kadaluarsa}}</p>
                                                </td>
                                                <td style="text-align:center;width:120px;padding:1em 0.5em 0 0.5em; margin:0;">
                                                    <label style="font-size:14px;margin-right: 0.2em;"><input class="uk-radio" type="radio" v-model="dt.sesuai" value="Ya"> Ya</label>
                                                    <label style="font-size:14px;"><input class="uk-radio" type="radio" v-model="dt.sesuai" value="Tidak"> Tidak</label>
                                                </td>
                                                <td style="text-align:center;width:80px;padding:0.8em 0.5em 0 0.5em; margin:0;">
                                                    <label style="font-size:18px;margin-right: 0.2em;"><input class="uk-radio" type="radio" v-model="dt.mayor" value="+"> +</label>
                                                    <label style="font-size:18px;"><input class="uk-radio" type="radio" v-model="dt.mayor" value="-"> -</label>
                                                </td>
                                                <td style="text-align:center;padding:0.8em 0.5em 0 0.5em; margin:0;width:80px;">
                                                    <label style="font-size:18px;margin-right: 0.2em;"><input class="uk-radio" type="radio" v-model="dt.minor" value="+"> +</label>
                                                    <label style="font-size:18px;"><input class="uk-radio" type="radio" v-model="dt.minor" value="-"> -</label>
                                                </td>
                                                <td style="text-align:center;padding:0.8em 0.5em 0 0.5em; margin:0;width:80px;">
                                                    <label style="font-size:18px;margin-right: 0.2em;"><input class="uk-radio" type="radio" v-model="dt.ac" value="+"> +</label>
                                                    <label style="font-size:18px;"><input class="uk-radio" type="radio" v-model="dt.mayor" value="-"> -</label>
                                                </td>
                                                <td style="text-align:center;padding:0.8em 0.5em 0 0.5em; margin:0;width:50px;">
                                                    <label style="font-size:18px;margin-right: 0.2em;"><input class="uk-radio" type="radio" v-model="dt.dct" value="+"> +</label>
                                                    <label style="font-size:18px;"><input class="uk-radio" type="radio" v-model="dt.dct" value="-"> -</label>
                                                </td>
                                                
                                                <td style="padding:1.2em 0.5em 0 0.5em; text-align:center;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="dt.is_terpakai" style="text-align: center;" @change="changeActivationDarahItem">
                                                </td>
                                            </tr>                                        
                                            <tr v-if="permintaan.items.length <= 0">
                                                <td colspan="8">
                                                    <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'modal-pengiriman',
    components : { SearchSelect,SearchList },
    data() {
        return {
            formTitle : 'PENGIRIMAN DARAH',
            isUpdate : true,
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            darahDesc : [
                { colName : 'no_labu', labelData : '', isTitle : true },
                { colName : 'gol_darah', labelData : '', isTitle : false },
                { colName : 'rhesus', labelData : '', isTitle : false },
                { colName : 'group_darah', labelData : '', isTitle : false },
                { colName : 'tgl_kadaluarsa', labelData : '', isTitle : false },
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
            permintaanItem: {
                darah_detail_id : null,
                darah_dist_id : null,
                no_labu : null,
                gol_darah : null,
                rhesus : null,
                group_darah : null,
                volume : null,
                jumlah : 0,
                sesuai : 'Ya',
                minor : '+',
                mayor : '+',
                ac : '+',
                dct : '+',                
                status : 'PERMINTAAN',
                is_aktif : true,
                is_terpakai : false,
                client_id : null,
                itemLabuSelected : false,
            },

            itemUrl : '',
            unitUrl : '',
            darahLists : null,
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
            'getStockDarah',
            'updatePengirimanDarah'        
        ]),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('refProduk',['listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();
            this.listRefProduk({per_page:'ALL'});
            this.clearPermintaan();
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
            this.$emit('modalDistribusiDarahClosed',true);
            UIkit.modal('#modalDistribusiDarah').hide();
        },

        /**modal call from other component for new data entry */
        newData(){            
            this.CLR_ERRORS();
            this.clearPermintaan();
            this.isUpdate = false;
            this.formTitle = "DATA DISTRIBUSI DARAH";
            UIkit.modal('#modalDistribusiDarah').show();
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
                        UIkit.modal('#modalDistribusiDarah').show();
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
                this.getListDarah(data);
            }
        },

        getListDarah(data){
            if(data) {
                let prm = {
                    is_aktif:true,
                    goldarah:data.gol_darah,
                    rhesus:data.rhesus,
                    per_page:'ALL',
                }
                this.getStockDarah(prm).then((response) => {
                    if (response.success == true) {
                        this.darahLists = JSON.parse(JSON.stringify(response.data));
                    }
                    else { alert('data persediaan darah yang diminta tidak ditemukan'); }
                })
            }
        },

        submitPermintaan(){
            this.CLR_ERRORS();
            this.changeActivationDarahItem();
            this.isLoading = true;      
            //if(this.isUpdate) {
                //update data
                this.updatePengirimanDarah(this.permintaan).then((response) => {
                    if (response.success == true) {
                        this.fillPermintaan(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        alert('Data berhasil disimpan.');
                        this.modalClosed();
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            //}
            // else {
            //     //data baru
            //     this.createPermintaanDarah(this.permintaan).then((response) => {
            //         if (response.success == true) {
            //             this.fillPermintaan(response.data);
            //             this.isUpdate = true;
            //             this.isLoading = false;
            //             alert('Data berhasil disimpan.');
            //             this.modalClosed();
            //         }
            //         else { alert(response.message); this.isLoading = false; }
            //     })
            // }
        },

        noLabuSelected(data) {
            console.log(data);
            if(data) {
                this.permintaanItem.darah_detail_id = data.darah_detail_id;
                this.permintaanItem.no_labu = data.no_labu;
                this.permintaanItem.tgl_kadaluarsa = data.tgl_kadaluarsa;
                this.permintaanItem.gol_darah = data.gol_darah;
                this.permintaanItem.rhesus = data.rhesus;
                this.permintaanItem.group_darah = data.group_darah;
                this.permintaanItem.volume = data.volume;
                this.permintaanItem.itemLabuSelected = true;
            }
        },

        clearItemPermintaan() {
            this.permintaanItem.darah_detail_id = null;
            this.permintaanItem.no_labu = null;
            this.permintaanItem.tgl_kadaluarsa = null;
            this.permintaanItem.gol_darah = null;
            this.permintaanItem.rhesus = null;
            this.permintaanItem.group_darah = null;
            this.permintaanItem.volume = null;
            this.permintaanItem.darah_dist_id = null;
            this.permintaanItem.is_terpakai = false;
            this.permintaanItem.is_aktif = true;
            this.permintaanItem.itemLabuSelected = false;
        },

        addDarahItem(){
            if(this.permintaan.is_kirim) {
                this.permintaanItem.is_terpakai = true;
                this.permintaanItem.is_aktif = true;                
                let dt = JSON.parse(JSON.stringify(this.permintaanItem));
                let isExist = this.permintaan.items.find(item => item['darah_detail_id'] == this.permintaanItem.darah_detail_id);
                if(!isExist) {
                    this.permintaan.items.push(dt);
                    this.clearItemPermintaan();
                }
                else {
                    alert('item persediaan sudah ada dalam daftar realisasi permintaan');
                    this.clearItemPermintaan();
                }
            }
            else {
                this.clearItemPermintaan();
                alert('tandai pengiriman persediaan sebelum menambahkan item pengiriman.');
            }
        },

        changeActivationDarahItem(){
            let val = this.permintaan.items.filter( item=>{ return item['is_terpakai'] == true || (item['darah_dist_id'] !== null && item['darah_dist_id'] !== '') })
            this.permintaan.items = JSON.parse(JSON.stringify(val));
        },

        flagPengirimanChange(){
            let val = null;
            if(this.permintaan.is_kirim == false) {
                this.permintaan.items.forEach(el => {
                    try { 
                        el.is_terpakai = false;
                    } 
                    catch (error) { return ''; }
                });
                this.changeActivationDarahItem();
            }
        }
    }
}
</script>
<style>

/* dl.hims-description-list dt {
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

tr.data-inactive td {
    text-decoration: line-through;
    font-style: italic;
} */
</style>