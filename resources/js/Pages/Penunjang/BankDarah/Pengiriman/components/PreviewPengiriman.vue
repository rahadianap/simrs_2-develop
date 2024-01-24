<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="previewPengirimanDarah" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="padding:0;">
            
            <div class="uk-card-default uk-grid-small" uk-grid style="padding:0;margin:0; border-top:3px solid #cc0202; height: 70px;">
                <div class="uk-width-expand" style="padding:1em;">
                    <h3 style="color:black; font-weight: 400;">DATA PERMINTAAN DARAH DAN REALISASI</h3>
                </div>
                <div class="uk-width-auto" style="padding:0.8em;">
                    <button class="uk-button" @click.prevent="previewClosed" type="button" style="color:#333; font-weight: 500;">TUTUP</button>
                </div>
            </div>
            <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">
                <!-- <div>
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="3" style="font-weight:500; font-size:16px; padding:1em 0 1em 0.5em; color:black;">{{permintaan.darah_dist_id}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">TANGGAL</td>
                                <td>:</td>
                                <td>{{pengiriman.tgl_kirim}} {{pengiriman.jam_kirim}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">UNIT PENERIMA</td>
                                <td>:</td>
                                <td>{{pengiriman.unit_penerima}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">PENERIMA</td>
                                <td>:</td>
                                <td>{{pengiriman.penerima}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">CATATAN</td>
                                <td>:</td>
                                <td>{{pengiriman.catatan}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:1em;">
                    <div class="uk-width-1-1@m" style="padding:1em 0.5em 0.5em 0.5em;">
                        <h5 style="color:#000;padding:0;margin:0;font-weight: 500;">ITEM PENGIRIMAN</h5>
                    </div>
                    <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                        <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;margin:0;">
                            <table class="uk-table hims-table">
                                <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                                    <tr>
                                        <th style="padding:1em;width:150px;">Produk ID</th>
                                        <th style="padding:1em;">Nama Produk</th>
                                        <th style="text-align:center;width:70px;padding:1em;">Jumlah</th>
                                        <th style="text-align:center;width:120px;padding:1em;">Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom:1px solid #ccc; font-size:12px;" v-if="pengiriman.items.length > 0" v-for="dt in pengiriman.items">
                                        <td style="font-weight:500;width:150px;">{{dt.produk_id}}</td>
                                        <td style="font-weight:400;">{{dt.produk_nama}}</td>
                                        <td style="text-align:center;">{{dt.jml_keluar}}</td>
                                        <td style="text-align:center;">{{dt.satuan}}</td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="4">
                                            <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>        
                    </div>
                </div> -->

                    <!-- <div class="uk-card-default uk-grid-small" uk-grid style="position:sticky; top:-50px; padding:0;margin:0; border-top:0px solid #cc0202; height: 70px;">
                        <div class="uk-width-expand" style="padding:1em;">
                            <h3 style="color:black; font-weight: 400;">REALISASI PERMINTAAN DARAH</h3>
                        </div>
                        <div class="uk-width-auto" style="padding:0.8em;">
                            <button class="uk-button" @click.prevent="modalClosed" type="button" style="color:#333; font-weight: 500;">TUTUP</button>
                            <button class="uk-button" type="submit" style="color:#fff; font-weight: 500;background-color: #cc0202;margin-left:5px;">SIMPAN</button>
                        </div>
                    </div> -->
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
                                            <dt>Jumlah Dibutuhkan</dt>
                                            <dd>{{permintaan.jml_permintaan}} Kantung @{{permintaan.volume_per_labu}} cc</dd>
                                        </dl>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">                                            
                                            <dt>Diagnosa</dt>
                                            <dd>{{permintaan.diagnosa}}</dd>
                                            <dt>Hasil Haemoglobin</dt>
                                            <dd>{{permintaan.haemoglobin}}</dd>
                                            <dt>Catatan</dt>
                                            <dd>{{permintaan.catatan}}</dd>
                                        </dl>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>Tgl Pengiriman</dt>
                                            <dd>{{permintaan.tgl_distribusi}} {{permintaan.jam_distribusi}}</dd>
                                            <dt>Penerima</dt>
                                            <dd>{{permintaan.penerima}}</dd>
                                            <dt>Pengirim</dt>
                                            <dd>{{permintaan.pengirim}}</dd>
                                        </dl>
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
                                                <th style="text-align:center;width:120px;padding:0.5em;">Sesuai</th>
                                                <th style="text-align:center;width:80px;padding:0.5em;">Mayor</th>
                                                <th style="text-align:center;width:80px;padding:0.5em;">Minor</th>
                                                <th style="text-align:center;width:80px;padding:0.5em;">AC</th>
                                                <th style="text-align:center;width:80px;padding:0.5em;">DCT</th>
                                                <th style="text-align:center;width:50px;padding:0.5em;">...</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr :class=" !dt.is_terpakai ? 'data-inactive' : ''" style="border-top:1px solid #ccc;" v-if="permintaan.items.length > 0" v-for="dt in permintaan.items">
                                                <td style="font-weight: 500;font-size: 12px;">{{dt.no_labu}}</td>
                                                <td style="padding:0.3em 0.5em 0 0.5em; margin:0;">
                                                    <p style="padding:0;margin:0;font-weight: 500;">{{dt.group_darah}} ( Gol. {{dt.gol_darah}}, Rhesus. {{dt.rhesus}} )</p>
                                                    <p style="padding:0;margin:0;">vol. {{dt.volume}} cc, Kadaluarsa. {{dt.tgl_kadaluarsa}}</p>
                                                </td>
                                                <td style="text-align:center;width:120px;padding:1em 0.5em 0 0.5em; margin:0;">
                                                    {{dt.sesuai}}
                                                </td>
                                                <td style="text-align:center;font-size:14px; width:80px;padding:0.8em 0.5em 0 0.5em; margin:0;">
                                                    {{dt.mayor}}
                                                </td>
                                                <td style="text-align:center;font-size:14px; padding:0.8em 0.5em 0 0.5em; margin:0;width:80px;">
                                                    {{dt.minor}}
                                                </td>
                                                <td style="text-align:center;font-size:14px; padding:0.8em 0.5em 0 0.5em; margin:0;width:80px;">
                                                    {{dt.ac}}
                                                </td>
                                                <td style="text-align:center;font-size:14px; padding:0.8em 0.5em 0 0.5em; margin:0;width:50px;">
                                                    {{dt.dct}}
                                                </td>
                                                
                                                <td style="padding:1.2em 0.5em 0 0.5em; text-align:center;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="dt.is_terpakai" style="text-align: center;" disabled>
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
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'preview-pengiriman',
    data() {
        return {
            formTitle : 'PENGIRIMAN LINEN BARU',
            isUpdate : true,
            isLoading : true,
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
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
        }
    },
    computed : {},
    mounted() {},
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
        ...mapMutations(['CLR_ERRORS']),

        /** function called before modal closed */
        previewClosed(){
            this.clearPengiriman();
            this.$emit('modalLinenClosed',true);
            UIkit.modal('#previewPengirimanDarah').hide();            
        },

        /**modal call from other component for update data entry */
        previewData(refData) {
            this.CLR_ERRORS();
            this.clearPengiriman();
            this.isUpdate = true;
            this.isLoading = true;

            if(refData) {
                this.dataPermintaanDarah(refData.darah_dist_id).then((response) => {
                    if (response.success == true) {
                        this.fillPengiriman(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal('#previewPengirimanDarah').show();
                    }
                    else { alert('data pengiriman tidak ditemukan'); }
                })
            }
        },

        clearPengiriman() {
            this.permintaan.darah_dist_id = null;
            this.permintaan.reg_id = null;
            this.permintaan.jns_registrasi = null;
            this.permintaan.dokter_id = null;
            this.permintaan.dokter_nama = null;
            this.permintaan.pasien_id = null;
            this.permintaan.pasien_nama = null;
            this.permintaan.no_rm = null;
            this.permintaan.unit_id = null;
            this.permintaan.unit_nama = null;
            this.permintaan.ruang_id = null;
            this.permintaan.ruang_nama = null;
            this.permintaan.bed_no = null;
            this.permintaan.penjamin_id = null;
            this.permintaan.penjamin_nama = null;
            this.permintaan.is_bpjs = false;
            this.permintaan.usia = null;
            this.permintaan.tempat_lahir = null;
            this.permintaan.tgl_lahir = null;
            this.permintaan.jenis_kelamin = null;
            
            this.permintaan.tgl_permintaan = null;
            this.permintaan.jam_permintaan = null;
            
            this.permintaan.tgl_dibutuhkan = null;
            this.permintaan.jam_dibutuhkan = null;

            this.permintaan.tgl_distribusi = null;
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


        fillPengiriman(data) {
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
                //this.getListDarah(data);
            }
        },
    }
}

</script>