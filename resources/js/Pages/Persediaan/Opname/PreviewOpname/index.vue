<template>
    <div class="uk-container">        
        <div class="uk-container" style="padding:0;">
            <div class="uk-grid uk-grid-small uk-width-1-1" style="padding:0.5em 1em 1em 1em;">
                <div class="uk-width-auto" style="padding:0;">
                    <a href="#" @click.prevent="closeFormOpname" uk-icon="icon:arrow-left;ratio:1.5" style="display:block;width:30px;"></a>
                </div>
                <div class="uk-width-expand" style="padding:0;margin:0;">
                    <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                        <li>
                            <a href="#" @click.prevent="closeFormOpname" class="uk-text-uppercase">
                                <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">DATA OPNAME</h4>
                            </a>
                        </li>
                        <li v-if="opname.depo_nama !== null">
                            <span style="font-weight:400;color:#333;font-size:14px;"  class="uk-text-uppercase">{{opname.depo_nama}}</span>
                        </li>
                    </ul>
                </div>                
            </div>    
        </div>

        <div class="uk-width-1-1" style="padding:0 0.5em 0 0.5em;">
            <tr>
                <td style="color:black;font-size:12px;font-weight:500;padding:0 0.2em 0 0.2em;">DEPO </td>
                <td style="color:black;font-size:12px;padding:0 0.2em 0 0.2em;"> : </td>
                <td style="color:black;font-size:12px;padding:0 0.2em 0 0.2em;">{{opname.depo_nama}}</td>
            </tr>
            <tr>
                <td style="color:black;font-size:12px;font-weight:500;padding:0 0.2em 0 0.2em;">ID </td>
                <td style="color:black;font-size:12px;padding:0 0.2em 0 0.2em;"> : </td>
                <td style="color:black;font-size:12px;padding:0 0.2em 0 0.2em;">{{opname.so_id}}</td>
            </tr>
            <tr>
                <td style="color:black;font-size:12px;font-weight:500;padding:0 0.2em 0 0.2em;">TANGGAL </td>
                <td style="color:black;font-size:12px;padding:0 0.2em 0 0.2em;"> : </td>
                <td style="color:black;font-size:12px;padding:0 0.2em 0 0.2em;">{{opname.tgl_so}} - {{opname.tgl_selesai}}</td>
            </tr>
            <tr>
                <td style="color:black;font-size:12px;font-weight:500;padding:0 0.2em 0 0.2em;">CATATAN </td>
                <td style="color:black;font-size:12px;padding:0 0.2em 0 0.2em;"> : </td>
                <td style="color:black;font-size:12px;padding:0 0.2em 0 0.2em;">{{opname.catatan}}</td>
            </tr>
        </div>

        <div class="uk-width-1-1" style="padding:1em 0.5em 0 0.5em;">
            <table class="uk-table hims-table uk-table-responsive uk-box-shadow-large">
                <thead>
                    <tr>
                        <th style="padding:1em;margin:0;text-align:center;">ID</th>
                        <th style="padding:1em;margin:0;">Nama Item</th>
                        <th style="padding:1em;margin:0;text-align:center;">Jenis</th>
                        <th style="padding:1em;margin:0;text-align:center;">Awal</th>
                        <th style="padding:1em;margin:0;text-align:center;">Opname</th>
                        <th style="padding:1em;margin:0;text-align:center;">Selisih</th>
                    </tr>
                </thead>
                <tbody v-if="opname.produk && opname.produk.length > 0">
                    <tr v-for = "dt in opname.produk">
                        <td style="padding:1em;margin:0;font-weight:500;text-align:center;width:150px;">{{dt.produk_id}}</td>
                        <td style="padding:1em;margin:0;">{{dt.produk_nama}}</td>
                        <td style="padding:1em;margin:0;text-align:center;">{{dt.jenis_produk}}</td>
                        <td style="padding:1em;margin:0;text-align:center;width:80px;">{{dt.total_awal}}</td>
                        <td style="padding:1em;margin:0;text-align:center;width:80px;">{{dt.total_so}}</td>
                        <td style="padding:1em;margin:0;text-align:center;width:80px;">{{dt.selisih}}</td>
                    </tr>                    
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="6">
                            <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">tidak ada data untuk ditampilkan</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    name : 'preview-opname',
    props : {
        opnameId : { type:String, required:true }, 
    },
    data() {
        return {
            opname : {
                so_id : null,                
                depo_id : null,
                depo_nama : null,
                tgl_rencana : null,
                tgl_so : null,
                status : null,
                catatan : null,
                produk : [],
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('stockOpname',['dataOpname']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.clearOpname();            
            this.dataOpname(this.opnameId).then((response)=>{
                if (response.success == true) {
                    this.fillOpname(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                }
            })
        },

        closeFormOpname() {
            // this.$emit('closed',true);
            this.$router.push({ name:'listOpname' });
        },
        refreshData(data){
            this.clearOpname();            
            this.dataOpname(data.so_id).then((response)=>{
                if (response.success == true) {
                    this.fillOpname(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                }
            })

        },

        clearOpname(){
            this.opname.client_id = null;
            this.opname.so_id = null;
            this.opname.tgl_rencana = null;
            this.opname.tgl_so = null;
            this.opname.tgl_selesai = null;
            this.opname.unit_id = null;
            this.opname.depo_id = null;
            this.opname.unit_nama = null;
            this.opname.depo_nama = null;
            this.opname.catatan = null;
            this.opname.status = 'RENCANA';
            this.opname.nilai_awal = 'KOSONG';
            this.opname.is_aktif = true;
            this.opname.produk = [];
        },

        fillOpname(data) {
            this.opname.client_id = null;
            this.opname.so_id = data.so_id;
            this.opname.tgl_rencana = data.tgl_rencana;
            this.opname.tgl_so = data.tgl_so;
            this.opname.tgl_selesai = data.tgl_selesai;            
            this.opname.unit_id = data.unit_id;
            this.opname.depo_id = data.depo_id;
            this.opname.unit_nama = data.unit_nama;
            this.opname.depo_nama = data.depo_nama;
            this.opname.nilai_awal = data.nilai_awal;
            this.opname.catatan = data.catatan;
            this.opname.produk = data.produk;
            this.opname.is_aktif = true;
        }
    }
}

</script>