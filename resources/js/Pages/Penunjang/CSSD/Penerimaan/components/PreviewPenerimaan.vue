<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="previewPenerimaanCssd" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="padding:0;">
            
            <div class="uk-card-default uk-grid-small" uk-grid style="padding:0;margin:0; border-top:3px solid #cc0202; height: 70px;">
                <div class="uk-width-expand" style="padding:1em;">
                    <h3 style="color:black; font-weight: 400;">DATA PENERIMAAN</h3>
                </div>
                <div class="uk-width-auto" style="padding:0.8em;">
                    <button class="uk-button" @click.prevent="previewClosed" type="button" style="color:#333; font-weight: 500;">TUTUP</button>
                </div>
            </div>
            <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">
                <div>
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="3" style="font-weight:500; font-size:16px; padding:1em 0 1em 0.5em; color:black;">{{penerimaan.linen_terima_id}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">TANGGAL</td>
                                <td>:</td>
                                <td>{{penerimaan.tgl_terima}} {{penerimaan.jam_terima}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">UNIT PENGIRIM</td>
                                <td>:</td>
                                <td>{{penerimaan.unit_pengirim}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">PENGIRIM</td>
                                <td>:</td>
                                <td>{{penerimaan.pengirim}}</td>
                            </tr>                            
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">JUMLAH TERIMA</td>
                                <td>:</td>
                                <td>{{penerimaan.berat}} {{penerimaan.satuan}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">CATATAN</td>
                                <td>:</td>
                                <td>{{penerimaan.catatan}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:1em;">
                    <div class="uk-width-1-1@m" style="padding:1em 0.5em 0.5em 0.5em;">
                        <h5 style="color:#000;padding:0;margin:0;font-weight: 500;">ITEM PENERIMAAN </h5>
                    </div>
                    <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                        <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;margin:0;">
                            <table class="uk-table hims-table">
                                <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                                    <tr>
                                        <th style="padding:1em;width:150px;">Produk ID</th>
                                        <th style="padding:1em;">Nama Produk</th>
                                        <th style="text-align:center;width:120px;padding:1em;">Satuan</th>
                                        <th style="text-align:center;width:70px;padding:1em;">Jumlah</th>
                                        <th style="text-align:center;width:120px;padding:1em;">Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom:1px solid #ccc; font-size:12px;" v-if="penerimaan.items.length > 0" v-for="dt in penerimaan.items">
                                        <td style="font-weight:500;width:150px;">{{dt.produk_id}}</td>
                                        <td style="font-weight:400;">{{dt.produk_nama}}</td>
                                        <td style="text-align:center;">{{dt.satuan}}</td>
                                        <td style="text-align:center;">{{dt.jml_terima}}</td>                                        
                                        <td style="text-align:center;">{{dt.kondisi}}</td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="5">
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
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'preview-penerimaan',
    data() {
        return {
            formTitle : 'PENERIMAAN LINEN BARU',
            isUpdate : true,
            isLoading : true,
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            penerimaan : {
                linen_terima_id : null,
                unit_pengirim_id : null,
                unit_pengirim : null,
                unit_penerima_id : null,
                unit_penerima : null,
                pengirim : null,
                penerima : null,
                tgl_terima : null,
                jam_terima : null,
                tgl_selesai : null,
                catatan : null,
                berat : 0,
                satuan : null,
                is_infeksius : false,
                kondisi : null,
                status : 'TERIMA',
                is_aktif : true,
                client_id : null,
                items : [],
            },
        }
    },
    computed : {},
    mounted() {},
    methods : {
        ...mapActions('cssd',['dataPenerimaanCssd']),
        ...mapMutations(['CLR_ERRORS']),

        /** function called before modal closed */
        previewClosed(){
            this.clearPenerimaan();
            this.$emit('modalCssdClosed',true);
            UIkit.modal('#previewPenerimaanCssd').hide();            
        },

        /**modal call from other component for update data entry */
        previewData(refData) {
            this.CLR_ERRORS();
            this.clearPenerimaan();
            this.isUpdate = true;
            this.isLoading = true;

            if(refData) {
                this.dataPenerimaanCssd(refData.cssd_terima_id).then((response) => {
                    if (response.success == true) {
                        this.fillPenerimaan(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal('#previewPenerimaanCssd').show();
                    }
                    else { alert('data penerimaan tidak ditemukan'); }
                })
            }
        },

        clearPenerimaan() {
            this.penerimaan.cssd_terima_id = null;
            this.penerimaan.unit_pengirim_id = null;
            this.penerimaan.unit_pengirim = null;
            this.penerimaan.unit_penerima_id = null;
            this.penerimaan.unit_penerima = null;
            this.penerimaan.pengirim = null;
            this.penerimaan.penerima = null;
            this.penerimaan.tgl_terima = null;
            this.penerimaan.jam_terima = null;
            this.penerimaan.tgl_selesai = null;
            
            this.penerimaan.catatan = null;  
            this.penerimaan.status = 'TERIMA';
            this.penerimaan.is_aktif = true;
            this.penerimaan.client_id = null;
            this.penerimaan.items = [];        
        },

        fillPenerimaan(data) {
            if(data) {
                this.penerimaan.cssd_terima_id = data.cssd_terima_id;
                this.penerimaan.unit_pengirim_id = data.unit_pengirim_id;
                this.penerimaan.unit_pengirim = data.unit_pengirim;
                this.penerimaan.unit_penerima_id = data.unit_penerima_id;
                this.penerimaan.unit_penerima = data.unit_penerima;
                this.penerimaan.pengirim = data.pengirim;
                this.penerimaan.penerima = data.penerima;
                this.penerimaan.tgl_terima = data.tgl_terima;
                this.penerimaan.jam_terima = data.jam_terima;
                this.penerimaan.tgl_selesai = data.tgl_selesai;
                
                this.penerimaan.catatan = data.catatan;
                this.penerimaan.status = data.status;
                this.penerimaan.is_aktif = data.is_aktif;
                this.penerimaan.client_id = data.client_id;
                this.penerimaan.items = data.items;        
            }
        },

        submitPenerimaan(){
            this.CLR_ERRORS();
            this.isLoading = true;
            
            if(this.isUpdate) {
                //update data
                this.updatePenerimaanCssd(this.penerimaan).then((response) => {
                    if (response.success == true) {
                        this.fillPenerimaan(response.data);
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
                this.createPenerimaanCssd(this.penerimaan).then((response) => {
                    if (response.success == true) {
                        this.fillPenerimaan(response.data);
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
/* .uk-input, .uk-textarea, .uk-select, .uk-checkbox, .uk-radio {
    border:1px solid #333;
}

.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */
</style>