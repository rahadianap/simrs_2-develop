<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="previewPengirimanLinen" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="padding:0;">
            
            <div class="uk-card-default uk-grid-small" uk-grid style="padding:0;margin:0; border-top:3px solid #cc0202; height: 70px;">
                <div class="uk-width-expand" style="padding:1em;">
                    <h3 style="color:black; font-weight: 400;">DATA PENGIRIMAN</h3>
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
                                <td colspan="3" style="font-weight:500; font-size:16px; padding:1em 0 1em 0.5em; color:black;">{{pengiriman.linen_dist_id}}</td>
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
            pengiriman : {
                linen_dist_id : null,
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
        ...mapActions('linen',['dataPengirimanLinen']),
        ...mapMutations(['CLR_ERRORS']),

        /** function called before modal closed */
        previewClosed(){
            this.clearPengiriman();
            this.$emit('modalLinenClosed',true);
            UIkit.modal('#previewPengirimanLinen').hide();            
        },

        /**modal call from other component for update data entry */
        previewData(refData) {
            this.CLR_ERRORS();
            this.clearPengiriman();
            this.isUpdate = true;
            this.isLoading = true;

            if(refData) {
                this.dataPengirimanLinen(refData.linen_dist_id).then((response) => {
                    if (response.success == true) {
                        this.fillPengiriman(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal('#previewPengirimanLinen').show();
                    }
                    else { alert('data pengiriman linen tidak ditemukan'); }
                })
            }
        },

        clearPengiriman() {
            this.pengiriman.linen_dist_id = null;
            this.pengiriman.unit_pengirim_id = null;
            this.pengiriman.unit_pengirim = null;
            this.pengiriman.unit_penerima_id = null;
            this.pengiriman.unit_penerima = null;
            this.pengiriman.pengirim = null;
            this.pengiriman.penerima = null;
            
            this.pengiriman.tgl_terima = null;
            this.pengiriman.jam_terima = null;
            this.pengiriman.tgl_kirim = null;
            this.pengiriman.jam_kirim = null;

            this.pengiriman.catatan = null;            
            this.pengiriman.is_infeksius = false;
            this.pengiriman.status = 'TERIMA';
            this.pengiriman.is_aktif = true;
            this.pengiriman.client_id = null;
            this.pengiriman.items = [];        
        },

        fillPengiriman(data) {
            if(data) {
                this.pengiriman.linen_dist_id = data.linen_dist_id;
                this.pengiriman.unit_pengirim_id = data.unit_pengirim_id;
                this.pengiriman.unit_pengirim = data.unit_pengirim;
                this.pengiriman.unit_penerima_id = data.unit_penerima_id;
                this.pengiriman.unit_penerima = data.unit_penerima;
                this.pengiriman.pengirim = data.pengirim;
                this.pengiriman.penerima = data.penerima;
                
                this.pengiriman.tgl_terima = data.tgl_terima;
                this.pengiriman.jam_terima = data.jam_terima;
                this.pengiriman.tgl_kirim = data.tgl_kirim;
                this.pengiriman.jam_kirim = data.jam_kirim;

                this.pengiriman.catatan = data.catatan;
                this.pengiriman.status = data.status;
                this.pengiriman.is_aktif = data.is_aktif;
                this.pengiriman.client_id = data.client_id;
                this.pengiriman.items = data.items;        
            }
        },

        submitPenerimaan(){
            this.CLR_ERRORS();
            this.isLoading = true;
            
            if(this.isUpdate) {
                //update data
                this.updatePenerimaanLinen(this.pengiriman).then((response) => {
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
                this.createPenerimaanLinen(this.pengiriman).then((response) => {
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