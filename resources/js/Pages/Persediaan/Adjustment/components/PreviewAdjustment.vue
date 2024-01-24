<template>
    <div id="modal-preview-adjustment" class="uk-modal-container" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">STOCK ADJUSTMENT</h2>
        <div>
            <table>
                <tr>
                    <td colspan="3" style="font-size:16px; color:black;font-weight: 500;padding-bottom:1em;">{{penyesuaian.sa_id}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">DEPO PERSEDIAAN</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{penyesuaian.depo_nama}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">TANGGAL</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{penyesuaian.tgl_selesai}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">STATUS</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{penyesuaian.status}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">CATATAN</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{penyesuaian.catatan}}</td>
                </tr>
            </table>
        </div>
        <div style="margin-top:1.5em;">
            <table class="uk-table hims-table uk-table-responsive uk-box-shadow-small">
                <thead>
                    <tr>
                        <th style="width:180px;">ID</th>
                        <th>ITEM</th>
                        <th style="width:140px;text-align:center;">JML.PENYESUAIAN</th>
                        <th style="text-align:center;">SATUAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="itm in penyesuaian.items" style="border-top:1px solid #eee;">
                        <td style="font-weight: 500;">{{itm.produk_id}}</td>
                        <td>{{itm.produk_nama}}</td>
                        <td style="width:140px;text-align:center;">{{itm.jml_penyesuaian}}</td>
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
    name : 'preview-penyesuaian', 
    data() {
        return {
            penyesuaian : {
                client_id : null,
                sa_id : null,
                jenis_penyesuaian : null,
                depo_id : null,
                depo_nama : null,
                catatan : null,
                tgl_sa : null,
                tgl_selesai : null,
                unit_id : null,
                status : null,
                is_aktif : true,
                items : [],
            },
        }
    },
    computed : {
        ...mapGetters(['errors'])
    },
    methods : {
        ...mapActions('adjustment',['dataAdjustment']),
        ...mapMutations(['CLR_ERRORS']),

        viewData(Id) {
            this.clearAdjustment();
            this.dataAdjustment(Id).then((response)=>{
                if (response.success == true) {
                    this.fillAdjustment(response.data);
                    UIkit.modal('#modal-preview-adjustment').show();
                } 
                else { alert(response.message); }
            })
        },

        clearAdjustment(){
            this.penyesuaian.client_id = null;
            this.penyesuaian.sa_id = null;
            this.penyesuaian.depo_id = null;
            this.penyesuaian.depo_nama = null;
                
            this.penyesuaian.tgl_sa = null;
            this.penyesuaian.tgl_selesai = null;                
            this.penyesuaian.status = null;
            this.penyesuaian.catatan = null;
            this.penyesuaian.is_aktif = true;
            this.penyesuaian.items = [];
        },

        fillAdjustment(data){
            this.penyesuaian.client_id = null;
            this.penyesuaian.sa_id = data.sa_id;
            this.penyesuaian.depo_id = data.depo_id;
            this.penyesuaian.depo_nama = data.depo_nama;
                
            this.penyesuaian.tgl_sa = data.tgl_sa;
            this.penyesuaian.tgl_selesai = data.tgl_selesai;                
            this.penyesuaian.status = data.status;
            this.penyesuaian.catatan = data.catatan;
            this.penyesuaian.is_aktif = true;
            this.penyesuaian.items = data.items;
        },
    }
}

</script>

