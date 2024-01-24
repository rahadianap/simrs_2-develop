<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td>
            <div style="margin:0;padding:0;">
                <a href="#" @click.prevent="linkCallback(rowData)" class="row-link-menu">
                    {{rowData.trx_id}}
                    <p style="margin:0;padding:0;font-weight:350;">{{rowData.reg_id}}</p>
                </a>
            </div>      
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.nama_pasien}}</p>
            <p style="font-size:11px; margin:0;padding:0;">ID:{{rowData.pasien_id}} RM:{{ rowData.no_rm }}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.dokter_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.unit_nama}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.no_spesimen}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.status}}</p>
        </td>
        <td class="uk-text-uppercase" style="text-align:right;">
            <p style="margin:0;padding:0;font-weight: 500;">{{formatCurrency(rowData.transaksi.total)}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="9" v-if="isExpanded" style="background-color:#fefefe;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                    <div>
                        <ul class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0.2em 0 0 0;margin:0;">
                            <li><div><a href="#"><h5>Registrasi</h5></a></div></li>
                            <li style="padding:0;"><div><a href="#"><h5>Pemeriksaan</h5></a></div></li>
                        </ul>
                        <ul class="uk-switcher">
                            <li>
                                <div style="padding: 0.2em 0.5em 0.5em 0.5em;margin:0;">
                                    <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid style="padding:0;margin:0;"> 
                                        <div class="uk-width-1-1@m" style="margin-top:0.2em;">
                                            <h5 style="margin:0;padding:0; font-weight: 500;">{{rowData.sub_trx_id}}</h5>
                                            <p  style="font-size:11px; margin:0;padding:0;">
                                                <span style="font-weight: 500;">Trx. </span> {{rowData.trx_id}} | 
                                                <span style="font-weight: 500;"> Reg. </span> {{rowData.reg_id}}  
                                            </p>
                                        </div>
                                        <div class="uk-width-1-3@m">
                                            <dl class="uk-description-list pasien-description-list">
                                                <dt>ID Pasien</dt>
                                                <dd>{{rowData.pasien_id}}</dd>
                                                <dt>Nama</dt>
                                                <dd>{{rowData.nama_pasien}}</dd>
                                                <dt>Dokter</dt>
                                                <dd>{{rowData.dokter_nama}}</dd>
                                            </dl>
                                        </div>
                                        <div class="uk-width-1-3@m">
                                            <dl class="uk-description-list pasien-description-list">
                                                <dt>Tgl.Permintaan</dt>
                                                <dd>{{rowData.tgl_permintaan}}</dd>
                                                <dt>Tgl.Dibutuhkan</dt>
                                                <dd>{{rowData.tgl_dibutuhkan}}</dd>
                                                <dt>Tgl.Diperiksa</dt>
                                                <dd>{{rowData.tgl_diperiksa}}</dd>
                                            </dl>
                                        </div>
                                        <div class="uk-width-1-3@m">
                                            <dl class="uk-description-list pasien-description-list">
                                                <dt>Status</dt>
                                                <dd>{{rowData.status}}</dd>
                                                <dt>No.Spesimen</dt>
                                                <dd>{{rowData.no_spesimen}}</dd>
                                                <!-- <dt>Ruang Asal (Bed)</dt>
                                                <dd>{{rowData.ruang_asal}} {{rowData.no_asal_bed}}</dd> -->
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li style="padding:0;margin:0;">                       
                                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                    <thead>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">ID Pemeriksaan</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Nama Pemeriksaan</th>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">Jumlah</th>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">Satuan</th>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">Harga</th>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">Subtotal</th>                                     
                                    </thead>
                                    <tbody>
                                        <tr v-for="dt in rowData.detail">
                                            <td style="width: 130px;padding:1em; text-align:left;">{{dt.item_id}}</td>
                                            <td style="padding:1em; text-align:left;">{{dt.item_nama}}</td>
                                            <td style="width: 50px;padding:1em; text-align:center;">{{dt.jumlah}}</td>
                                            <td style="width: 50px;padding:1em; text-align:center;">{{dt.satuan}}</td>
                                            <td style="width: 50px;padding:1em; text-align:center;">{{formatCurrency(dt.nilai)}}</td>
                                            <td style="width: 50px;padding:1em; text-align:center;">{{formatCurrency(dt.subtotal)}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="font-weight: 500;">Total</td>
                                            <td  style="font-weight: 500;">{{formatCurrency(getTotal(rowData.detail))}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </li>
                        </ul>    
                    </div>
                </div>
            </td>
        </Transition>
    </tr>
</template>
<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        selectAllCallback : { type:Function, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null }
    },
    name : 'row-pemeriksaan-pa',
    data() {
        return {
            isExpanded : false,
            selectAll : true,
            buffer : [],
        }
    },
    methods : {
        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        changeRowExpand() {
            this.isExpanded = !this.isExpanded;
            return false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        },

        getTotal(data){
            let total = 0;
            data.forEach(function(dt){
                total = (total*1) + (dt.nilai*1);
            });
            return total;
        }
    }
}
</script>