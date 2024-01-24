<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="previewPemusnahanDarah" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="padding:0;">
            
            <div class="uk-card-default uk-grid-small" uk-grid style="padding:0;margin:0; border-top:3px solid #cc0202; height: 70px;">
                <div class="uk-width-expand" style="padding:1em;">
                    <h3 style="color:black; font-weight: 400;">DATA PEMUSNAHAN</h3>
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
                                <td colspan="3" style="font-weight:500; font-size:16px; padding:1em 0 1em 0.5em; color:black;">{{pemusnahan.darah_musnah_id}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">TANGGAL</td>
                                <td>:</td>
                                <td>{{pemusnahan.tgl_pemusnahan}} {{pemusnahan.jam_pemusnahan}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">CATATAN</td>
                                <td>:</td>
                                <td>{{pemusnahan.catatan}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:1em;">
                    <div class="uk-width-1-1@m" style="padding:1em 0.5em 0.5em 0.5em;">
                        <h5 style="color:#000;padding:0;margin:0;font-weight: 500;">ITEM PEMUSNAHAN </h5>
                    </div>
                    <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                        <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;margin:0;">
                            <table class="uk-table hims-table">
                                <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                                    <tr>
                                        <th style="padding:1em;width:150px;">No Labu</th>
                                        <th style="padding:1em;width:80px;">Gol.Darah</th>
                                        <th style="text-align:center;width:80px;padding:1em;">Rhesus</th>
                                        <th style="text-align:center;padding:1em;">Jenis</th>
                                        <th style="text-align:center;width:120px;padding:1em;">Kadaluarsa</th>
                                        <th style="text-align:center;width:120px;padding:1em;">Tgl.Terima</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom:1px solid #ccc; font-size:12px;" v-if="pemusnahan.items.length > 0" v-for="dt in pemusnahan.items">
                                        <td style="font-weight:500;width:150px;">{{dt.no_labu}}</td>
                                        <td style="font-weight:400;">{{dt.gol_darah}}</td>
                                        <td style="text-align:center;">{{dt.rhesus}}</td>
                                        <td style="text-align:center;">{{dt.group_darah}}</td>                                        
                                        <td style="text-align:center;">{{dt.tgl_kadaluarsa}}</td>
                                        <td style="text-align:center;">{{dt.tgl_terima}}</td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="6">
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
    name : 'preview-pemusnahan',
    data() {
        return {
            formTitle : 'PEMUSNAHAN PERSEDIAAN DARAH',
            isUpdate : true,
            isLoading : true,
            pemusnahan : {
                darah_musnah_id : null,
                pembuat : null,
                tgl_pemusnahan : null,
                jam_pemusnahan : null,
                catatan : null,
                status : null,
                is_aktif : true,
                client_id : null,
                items : [],
            },
        }
    },
    methods : {
        ...mapActions('bankDarah',['dataPemusnahanDarah']),
        ...mapMutations(['CLR_ERRORS']),

        /** function called before modal closed */
        previewClosed(){
            this.clearPemusnahan();
            this.$emit('modalPemusnahanClosed',true);
            UIkit.modal('#previewPemusnahanDarah').hide();            
        },

        /**modal call from other component for update data entry */
        previewData(refData) {
            this.CLR_ERRORS();
            this.clearPemusnahan();
            this.isUpdate = true;
            this.isLoading = true;

            if(refData) {
                this.dataPemusnahanDarah(refData.darah_musnah_id).then((response) => {
                    if (response.success == true) {
                        this.fillPemusnahan(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal('#previewPemusnahanDarah').show();
                    }
                    else { alert('data tidak ditemukan'); }
                })
            }
        },

        clearPemusnahan() {
            this.pemusnahan.darah_musnah_id = null;
            this.pemusnahan.tgl_pemusnahan = null;
            this.pemusnahan.jam_pemusnahan = null;
            this.pemusnahan.pembuat = null;
            this.pemusnahan.catatan = null;  
            this.pemusnahan.status = 'TERIMA';
            this.pemusnahan.is_aktif = true;
            this.pemusnahan.client_id = null;
            this.pemusnahan.items = [];        
        },

        fillPemusnahan(data) {
            if(data) {
                this.pemusnahan.darah_musnah_id = data.darah_musnah_id;
                this.pemusnahan.tgl_pemusnahan = data.tgl_pemusnahan;
                this.pemusnahan.jam_pemusnahan = data.jam_pemusnahan;
                this.pemusnahan.pembuat = data.pembuat;
                this.pemusnahan.catatan = data.catatan;  
                this.pemusnahan.status = data.status;
                this.pemusnahan.is_aktif = data.is_aktif;
                this.pemusnahan.client_id = data.client_id;
                this.pemusnahan.items = data.items;        
            }
        },
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