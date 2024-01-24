<template>
    <div id="modal-preview-issue" class="uk-modal-container" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <h2 class="uk-modal-title">PENGELUARAN PERSEDIAAN</h2>
        <div>
            <table>
                <tr>
                    <td colspan="3" style="font-size:16px; color:black;font-weight: 500;padding-bottom:1em;">{{issue.sa_id}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">DEPO PERSEDIAAN</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{issue.depo_nama}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">TANGGAL</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{issue.tgl_selesai}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">STATUS</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{issue.status}}</td>
                </tr>
                <tr>
                    <td style="font-size:12px; color:black;font-weight: 500;">CATATAN</td>
                    <td style="font-size:12px;padding-left:1em;padding-right:1em;"> : </td>
                    <td style="font-size:12px;">{{issue.catatan}}</td>
                </tr>
            </table>
        </div>
        <div style="margin-top:1.5em;">
            <table class="uk-table hims-table uk-table-responsive uk-box-shadow-small">
                <thead>
                    <tr>
                        <th style="width:180px;">ID</th>
                        <th>ITEM</th>
                        <th style="width:140px;text-align:center;">JML.KELUAR</th>
                        <th style="text-align:center;">SATUAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="itm in issue.items" style="border-top:1px solid #eee;">
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
    name : 'preview-issue', 
    data() {
        return {
            issue : {
                client_id : null,
                sa_id : null,
                jenis_penyesuaian : null,
                depo_id : null,
                depo_nama : null,
                catatan : null,
                tgl_penyesuaian : null,
                tgl_selesai : null,
                biaya_unit_id : null,
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
        ...mapActions('inventoryIssue',['dataInventoryIssue']),
        ...mapMutations(['CLR_ERRORS']),

        viewData(Id) {
            this.clearInventoryIssue();
            this.dataInventoryIssue(Id).then((response)=>{
                if (response.success == true) {
                    this.fillInventoryIssue(response.data);
                    UIkit.modal('#modal-preview-issue').show();
                } 
                else { alert(response.message); }
            })
        },

        clearInventoryIssue(){
            this.issue.client_id = null;
            this.issue.sa_id = null;
            this.issue.depo_id = null;
            this.issue.depo_nama = null;
                
            this.issue.tgl_penyesuaian = null;
            this.issue.tgl_selesai = null;                
            this.issue.status = null;
            this.issue.catatan = null;
            this.issue.is_aktif = true;
            this.issue.items = [];
        },

        fillInventoryIssue(data){
            this.issue.client_id = null;
            this.issue.sa_id = data.sa_id;
            this.issue.depo_id = data.depo_id;
            this.issue.depo_nama = data.depo_nama;
                
            this.issue.tgl_penyesuaian = data.tgl_penyesuaian;
            this.issue.tgl_selesai = data.tgl_selesai;                
            this.issue.status = data.status;
            this.issue.catatan = data.catatan;
            this.issue.is_aktif = true;
            this.issue.items = data.items;
        },
    }
}

</script>

