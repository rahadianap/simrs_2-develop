<template>
    <div id="modal-preview-produksi" class="uk-modal-container" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">PRODUKSI PERSEDIAAN</h2>
        <div>
            <table>
                <tr>
                    <td colspan="3" style="font-size:16px; color:black;font-weight: 500;padding-bottom:1em;">{{produksi.produksi_id}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">DEPO</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{produksi.depo_nama}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">PRODUK HASIL</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{produksi.produk_hasil}} {{produksi.jml_hasil}} {{produksi.satuan}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">TGL PRODUKSI</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{produksi.tgl_produksi}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">STATUS</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{produksi.status}}</td>
                </tr>
            </table>
        </div>
        <div style="margin-top:1.5em;">
            <table class="uk-table hims-table uk-table-responsive uk-box-shadow-small">
                <thead>
                    <tr>
                        <th style="width:180px;">ID</th>
                        <th>ITEM BAHAN</th>
                        <th style="width:80px;text-align:center;">JML</th>
                        <th style="text-align:center;">SATUAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="itm in produksi.items" style="border-top:1px solid #eee;">
                        <td style="font-weight: 500;">{{itm.produk_id}}</td>
                        <td>{{itm.produk_nama}}</td>
                        <td style="width:80px;text-align:center;padding:0.5em 0.2em 0.5em 0.2em;">{{itm.jml_bahan}}</td>
                        <td style="width:180px;text-align:center;">{{itm.satuan}}</td>
                    </tr>
                </tbody>
            </table>
            
        </div>
    </div>
</div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
export default {
    name : 'preview-produksi', 
    data() {
        return {
            produksi : {
                client_id : null,
                produksi_id : null,
                depo_id : null,
                depo_nama : null,
                produk_hasil_id : null,
                produk_hasil : null,
                jml_hasil : null,
                satuan : null,
                tgl_produksi : null,
                tgl_selesai : null,
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
        ...mapActions('produksi',['dataProduksi']),
        ...mapMutations(['CLR_ERRORS']),

        viewData(produksiId) {
            this.clearProduksi();
            this.dataProduksi(produksiId).then((response)=>{
                if (response.success == true) {
                    this.fillProduksi(response.data);
                    UIkit.modal('#modal-preview-produksi').show();
                } 
                else { alert(response.message); }
            })
        },
        clearProduksi(){
            this.produksi.client_id = null;
            this.produksi.produksi_id = null;
            this.produksi.produk_hasil_id = null;
            this.produksi.produk_hasil = null;
            this.produksi.satuan = null;
            this.produksi.jml_hasil = null;            
            this.produksi.depo_id = null;
            this.produksi.depo_nama = null;
            this.produksi.tgl_produksi = null;
            this.produksi.tgl_selesai = null;
            this.produksi.status = null;
            this.produksi.catatan = null;
            this.produksi.is_aktif = true;
            this.produksi.items = [];
        },

        fillProduksi(data){
            this.produksi.client_id = null;
            this.produksi.produksi_id = data.produksi_id;
            this.produksi.produk_hasil_id = data.produk_hasil_id;
            this.produksi.produk_hasil = data.produk_hasil;
            this.produksi.satuan = data.satuan;
            this.produksi.jml_hasil = data.jml_hasil;            
            this.produksi.depo_id = data.depo_id;
            this.produksi.depo_nama = data.depo_nama;
            this.produksi.tgl_produksi = data.tgl_produksi;
            this.produksi.tgl_selesai = data.tgl_selesai;
            this.produksi.status = data.status;
            this.produksi.catatan = data.catatan;
            this.produksi.is_aktif = data.is_aktif;
            this.produksi.items = data.items;
        },
    }
}

</script>

