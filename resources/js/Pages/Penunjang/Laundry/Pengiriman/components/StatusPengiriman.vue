<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalStatusLinen" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="padding:0;">
            <form action="">
                <div class="uk-card-default uk-grid-small" uk-grid style="padding:0;margin:0; border-top:3px solid #cc0202; height: 70px;">
                    <div class="uk-width-expand" style="padding:1em;">
                        <h3 style="color:black; font-weight: 400;">PERUBAHAN STATUS</h3>
                    </div>
                    <div class="uk-width-auto" style="padding:0.8em;">
                        <button class="uk-button" @click.prevent="modalClosed" type="button" style="color:#333; font-weight: 500;">TUTUP</button>
                        <button class="uk-button" type="submit" style="color:#fff; font-weight: 500;background-color: #cc0202;margin-left:5px;">SIMPAN</button>
                    </div>
                </div>
                <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">                    
                    <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">
                        <div class="uk-width-1-1@m" style="padding:0 0.5em 0.5em 0.5em;">
                            <h5 style="color:black;font-weight: 500; padding:0;margin:0;">{{addStatus.trx_linen_id}}</h5>
                        </div>
                        <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status terbaru*</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select uk-form-small" v-model="addStatus.status" style="color:black;">
                                        <option value="TERIMA">TERIMA</option>
                                        <option value="PROSES">PROSES</option>
                                        <option value="SIMPAN">SIMPAN</option>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal diubah*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="date" v-model="addStatus.tgl_status" style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam diubah*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="time" v-model="addStatus.jam_status" style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea uk-form-small" type="text" v-model="addStatus.catatan" style="color:black;"></textarea>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
export default {
    name:'status-pengiriman',
    components : {},
    data() {
        return {
            penerimaan : {
                linen_terima_id : null,
                unit_id : null,
                unit_nama : null,
                tgl_terima : null,
                jam_terima : null,
                statusLog : [],
            },

            addStatus : {
                linen_status_id : null,
                trx_linen_id : null,
                tgl_status : null,
                jam_status : null,
                status : null,
                catatan : null,
                is_aktif : true,
                client_id : null,
            }
        }
    },

    methods : {
        modalClosed(){

        },

        showStatus(data) {
            this.addStatus.trx_linen_id = data.linen_terima_id;
            UIkit.modal('#modalStatusLinen').show();
        },
    }
}
</script>