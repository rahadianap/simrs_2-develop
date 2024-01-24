<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="previewPenerimaanDarah" style="min-height:50vh;">
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
                                <td colspan="3" style="font-weight:500; font-size:16px; padding:1em 0 1em 0.5em; color:black;">{{penerimaan.darah_terima_id}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">TANGGAL</td>
                                <td>:</td>
                                <td>{{penerimaan.tgl_terima}}</td>
                            </tr>
                            <tr style="font-size:12px;color:black;">
                                <td style="font-weight:500;padding-left:1em;">ASAL</td>
                                <td>:</td>
                                <td>{{penerimaan.asal_darah}} {{penerimaan.nama_donor}}</td>
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
                                        <th style="padding:1em;width:150px;">No Labu</th>
                                        <th style="text-align:center;padding:1em;width:80px;">Gol.Darah</th>
                                        <th style="text-align:center;width:80px;padding:1em;">Rhesus</th>
                                        <th style="text-align:left;padding:1em;">Jenis</th>
                                        <th style="text-align:center;width:120px;padding:1em;">Kadaluarsa</th>
                                        <th style="text-align:center;width:120px;padding:1em;">Tgl.Terima</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom:1px solid #ccc; font-size:12px;" v-if="penerimaan.items.length > 0" v-for="dt in penerimaan.items">
                                        <td style="font-weight:500;width:150px;">{{dt.no_labu}}</td>
                                        <td style="text-align:center;font-weight:400;">{{dt.gol_darah}}</td>
                                        <td style="text-align:center;">{{dt.rhesus}}</td>
                                        <td style="text-align:left;">{{dt.group_darah}}</td>                                        
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
    name : 'preview-penerimaan',
    data() {
        return {
            formTitle : 'PENERIMAAN PERSEDIAAN DARAH',
            isUpdate : true,
            isLoading : true,
            penerimaan : {
                darah_terima_id : null,
                tgl_terima : null,
                
                penerima : null,
                asal_darah : null,
                nama_donor : null,
                
                catatan : null,  
                status : null,
                is_aktif : null,
                client_id : null,
                items : [],      
            },
        }
    },
    methods : {
        ...mapActions('bankDarah',['dataPenerimaanDarah']),
        ...mapMutations(['CLR_ERRORS']),

        /** function called before modal closed */
        previewClosed(){
            this.clearPenerimaan();
            this.$emit('modalPenerimaanClosed',true);
            UIkit.modal('#previewPenerimaanDarah').hide();            
        },

        /**modal call from other component for update data entry */
        previewData(refData) {
            this.CLR_ERRORS();
            this.clearPenerimaan();
            this.isUpdate = true;
            this.isLoading = true;

            if(refData) {
                this.dataPenerimaanDarah(refData.darah_terima_id).then((response) => {
                    if (response.success == true) {
                        this.fillPenerimaan(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal('#previewPenerimaanDarah').show();
                    }
                    else { alert('data tidak ditemukan'); }
                })
            }
        },

        clearPenerimaan() {
            this.penerimaan.darah_terima_id = null;
            this.penerimaan.tgl_terima = null;
            
            this.penerimaan.penerima = null;
            this.penerimaan.asal_darah = null;
            this.penerimaan.nama_donor = null;
            
            this.penerimaan.catatan = null;  
            this.penerimaan.status = 'TERIMA';
            this.penerimaan.is_aktif = true;
            this.penerimaan.client_id = null;
            this.penerimaan.items = [];        
        },

        fillPenerimaan(data) {
            if(data) {
                this.penerimaan.darah_terima_id = data.darah_terima_id;
                this.penerimaan.tgl_terima = data.tgl_terima;
                
                this.penerimaan.penerima = data.penerima;
                this.penerimaan.asal_darah = data.asal_darah;
                this.penerimaan.nama_donor = data.nama_donor;
                
                this.penerimaan.catatan = data.catatan;  
                this.penerimaan.status = data.status;
                this.penerimaan.is_aktif = data.is_aktif;
                this.penerimaan.client_id = data.client_id;
                this.penerimaan.items = data.items;             
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