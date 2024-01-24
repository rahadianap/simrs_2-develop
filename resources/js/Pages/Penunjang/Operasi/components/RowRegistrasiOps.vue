<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td>
            <div style="margin:0;padding:0;">
                <a href="#" @click.prevent="linkCallback(rowData)" class="row-link-menu">
                    {{rowData.tgl_operasi}} {{rowData.jam_operasi}}
                    <p style="margin:0;padding:0;font-weight:350;font-size: 11px;">{{rowData.trx_id}}</p>
                </a>
            </div>      
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.nama_pasien}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.no_rm}} - {{rowData.pasien_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.dokter_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.dokter_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.unit_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.ruang_nama}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.tindakan_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.jenis_operasi}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.no_antrian}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.status}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="9" v-if="isExpanded" style="background-color:#eee;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                    <div>
                        <ul class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0.2em 0 0 0;margin:0;">
                            <li><div><a href="#"><h5>Registrasi</h5></a></div></li>
                            <li style="padding:0;"><div><a href="#"><h5>Tim Operasi</h5></a></div></li>
                            <li><div><a href="#"><h5>Kelahiran</h5></a></div></li>
                        </ul>
                        <ul class="uk-switcher">
                            <li>
                                <div style="padding: 1em;margin:0;background-color: #fefefe;">
                                    <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid style="padding:0;margin:0;"> 
                                        <div class="uk-width-1-1@m" style="margin-top:0.2em;">
                                            <h5 style="margin:0;padding:0; font-weight: 500;">{{rowData.trx_id}}</h5>
                                            <p  style="font-size:11px; margin:0;padding:0;">
                                                <span style="font-weight: 500;"> Reg. </span> {{rowData.reg_id}}  
                                            </p>
                                        </div>
                                        <div class="uk-width-1-3@m">
                                            <dl class="uk-description-list hims-description-list">
                                                <dt>No.RM | ID</dt>
                                                <dd>{{rowData.no_rm}} | {{rowData.pasien_id}}</dd>
                                                <dt>Nama</dt>
                                                <dd>{{rowData.nama_pasien}}</dd>
                                                <dt>Penjamin</dt>
                                                <dd>{{rowData.penjamin_nama}}</dd>
                                            </dl>
                                        </div>
                                        <div class="uk-width-1-3@m">
                                            <dl class="uk-description-list hims-description-list">
                                                <dt>Dokter Pengirim</dt>
                                                <dd>{{rowData.dokter_pengirim}}</dd>
                                                <dt>Unit Asal</dt>
                                                <dd>{{rowData.unit_asal}}</dd>
                                                <dt>Ruang Asal (Bed)</dt>
                                                <dd>{{rowData.ruang_asal}} {{rowData.no_asal_bed}}</dd>
                                            </dl>
                                        </div>
                                        <div class="uk-width-1-3@m">
                                            <dl class="uk-description-list hims-description-list">
                                                <dt>Tindakan</dt>
                                                <dd class="uk-text-uppercase">{{rowData.tindakan_nama}}</dd>
                                                <dt>Jenis dan Skala</dt>
                                                <dd>{{rowData.jenis_operasi}} {{rowData.skala_operasi}}</dd>
                                                <dt>Dokter Operator</dt>
                                                <dd>{{rowData.dokter_nama}}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li style="padding:0;margin:0;"> 
                                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                    <thead>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Posisi</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">ID Dokter</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Nama Dokter</th>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">OPR</th>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">...</th>                                     
                                    </thead>
                                    <tbody>
                                        <tr v-for="dt in rowData.tim_operasi">
                                            <td style="width: 150px;padding:1em; text-align:left;font-weight: 500;">{{dt.posisi}}</td>
                                            <td style="width: 130px;padding:1em; text-align:left;">{{dt.dokter_id}}</td>
                                            <td style="padding:1em; text-align:left;">{{dt.dokter_nama}}</td>
                                            <td style="width: 50px;padding:1em; text-align:center;">
                                                <input class="uk-checkbox" type="checkbox" v-model="dt.is_operator" disabled style="border:1px solid black;">
                                            </td>
                                            <td style="width: 50px;padding:1em; text-align:center;">
                                                <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" disabled style="border:1px solid black;">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </li>
                            <li style="padding:0;margin:0;"> 
                                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                    <thead>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Persalinan ID</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">No. RM Bayi</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Nama Bayi</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Tanggal Lahir</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Jam Lahir</th>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">...</th>                                     
                                    </thead>
                                    <tbody>
                                        <tr v-for="dt in rowData.persalinan">
                                            <td style="width: 150px;padding:1em; text-align:left;font-weight: 500;">{{dt.persalinan_id}}</td>
                                            <td style="width: 130px;padding:1em; text-align:left;">{{dt.no_rm}}</td>
                                            <td style="width: 130px;padding:1em; text-align:left;">{{dt.nama_pasien}}</td>
                                            <td style="width: 130px;padding:1em; text-align:left;">{{dt.tgl_kelahiran}}</td>
                                            <td style="width: 130px;padding:1em; text-align:left;">{{dt.jam_kelahiran}}</td>
                                            <td style="width: 50px;padding:1em; text-align:center;">
                                                <button class="uk-button uk-button-primary uk-button-small" type="button" style="width: 100px;">Add RM</button>
                                            </td>
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
    emits : ['itemRequestSelected'],
    name : 'row-registrasi-ops',
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
<style>
dl.hims-description-list dt {
    margin:0;
    padding:0;
    font-size:11px;
    font-weight: bold;
    color:#333;
}

dl.hims-description-list dd {
    margin:0;
    padding:0;
    font-size:12px;
    font-weight: 400;
    color:#000;
}
</style>