<template>
    <div id="modal-terima-distribusi" class="uk-modal-container" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">PENERIMAAN DISTRIBUSI</h2>
        <div>
            <table>
                <tr>
                    <td colspan="3" style="font-size:16px; color:black;font-weight: 500;padding-bottom:1em;">{{distribusi.distribusi_id}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">DEPO PENERIMA</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{distribusi.depo_penerima}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">DEPO PENGIRIM</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{distribusi.depo_pengirim}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">TGL DIBUAT</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{distribusi.tgl_dibuat}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">TGL DITERIMA</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{distribusi.tgl_diterima}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">STATUS</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{distribusi.status}}</td>
                </tr>
            </table>
        </div>
        <div style="margin-top:1.5em;">
            <table class="uk-table hims-table uk-table-responsive uk-box-shadow-small">
                <thead>
                    <tr>
                        <th style="width:180px;">ID</th>
                        <th>ITEM</th>
                        <th style="width:80px;text-align:center;">DIMINTA</th>
                        <th style="width:80px;text-align:center;">DIKIRIM</th>
                        <th style="text-align:center;">SATUAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="itm in distribusi.items" style="border-top:1px solid #eee;">
                        <td style="font-weight: 500;">{{itm.produk_id}}</td>
                        <td>{{itm.produk_nama}}</td>
                        <td style="width:80px;text-align:center;padding:0.5em 0.2em 0.5em 0.2em;">{{itm.jml_diminta}}</td>
                        <td style="width:80px;text-align:center;padding:0.5em 0.2em 0.5em 0.2em;">{{itm.jml_dikirim}}</td>
                        <td style="width:180px;text-align:center;">{{itm.satuan}}</td>
                    </tr>
                </tbody>
            </table>            
        </div>
        <div style="text-align:right;margin-top:1em;">
            <button class="uk-button uk-button-primary" style="background-color: #fff;border:2px solid #cc0202;color:black;" @click.prevent="penerimaanDistribusi">SIMPAN PENERIMAAN</button>
        </div>


    </div>
</div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
export default {
    name : 'penerimaan-distribusi', 
    data() {
        return {
            distribusi : {
                client_id : null,
                distribusi_id : null,
                depo_penerima_id : null,
                depo_pengirim_id : null,
                depo_penerima : null,
                depo_pengirim : null,
                
                tgl_dibuat : null,
                tgl_dibutuhkan : null,
                tgl_realisasi : null,
                
                status : null,
                catatan : null,
                is_aktif : true,
                items : [],
            },
        }
    },
    computed : {
        ...mapGetters(['errors'])
    },
    methods : {
        ...mapActions('distribusi',['dataDistribusi','receiveDistribusi']),
        ...mapMutations(['CLR_ERRORS']),

        viewData(distribusiId) {
            this.clearDistribusi();
            this.dataDistribusi(distribusiId).then((response)=>{
                if (response.success == true) {
                    this.fillDistribusi(response.data);
                    UIkit.modal('#modal-terima-distribusi').show();
                } 
                else { alert(response.message); }
            })
        },

        clearDistribusi(){
            this.distribusi.client_id = null;
            this.distribusi.distribusi_id = null;
            this.distribusi.depo_penerima_id = null;
            this.distribusi.depo_pengirim_id = null;
            this.distribusi.depo_penerima = null;
            this.distribusi.depo_pengirim = null;
                
            this.distribusi.tgl_dibuat = null;
            this.distribusi.tgl_dibutuhkan = null;
            this.distribusi.tgl_realisasi = null;
                
            this.distribusi.status = null;
            this.distribusi.catatan = null;
            this.distribusi.is_aktif = true;
            this.distribusi.items = [];
        },

        fillDistribusi(data){
            this.distribusi.client_id = null;
            this.distribusi.distribusi_id = data.distribusi_id;
            this.distribusi.depo_penerima_id = data.depo_penerima_id;
            this.distribusi.depo_pengirim_id = data.depo_pengirim_id;
            this.distribusi.depo_penerima = data.depo_penerima;
            this.distribusi.depo_pengirim = data.depo_pengirim;
                
            this.distribusi.tgl_dibuat = data.tgl_dibuat;
            this.distribusi.tgl_dibutuhkan = data.tgl_dibutuhkan;
            this.distribusi.tgl_realisasi = data.tgl_realisasi;
                
            this.distribusi.status = data.status;
            this.distribusi.catatan = data.catatan;
            this.distribusi.is_aktif = data.is_aktif;
            this.distribusi.items = data.items;
        },

        penerimaanDistribusi(){
            if(confirm(`Proses ini akan mengakhiri proses distribusi. Data sudah tidak dapat diubah. Lanjutkan ?`)){
                this.CLR_ERRORS();
                this.receiveDistribusi(this.distribusi.distribusi_id).then((response) => {
                    if (response.success == true) {
                        alert("Penerimaan distribusi berhasil disimpan.");
                        this.$emit('saveSucceed',true);
                        this.clearDistribusi();
                        UIkit.modal('#modal-terima-distribusi').hide();
                    }
                })
            };
            
        }
    }
}

</script>

