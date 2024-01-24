<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td>
            <!-- <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu" @click.prevent="rowFunctions(rowData)">{{rowData.reg_id}}</a>
                <p style="font-size:11px; margin:0;padding:0;font-weight: 400;">{{rowData.trx_id}}</p>            
            </div>   -->
            <div style="margin:0;padding:0;">
                <a href="#" @click.prevent="linkCallback(rowData)" class="row-link-menu">
                    {{rowData.reg_id}}
                    <p style="margin:0;padding:0;font-weight:350;">{{rowData.trx_id}}</p>
                </a>
                <!-- <div uk-dropdown="mode: click">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="rowFunctions" v-for="rf in rowFunctions">
                            <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:350;">
                                <span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span>
                            </a>
                        </li>
                    </ul>
                </div> -->
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
            <p style="font-size:11px; margin:0;padding:0;">Ruang. {{rowData.ruang_nama}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.no_bed}}</p>
            <!-- <p style="font-size:11px; margin:0;padding:0;">{{rowData.bed_id}}</p> -->
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.penjamin_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">{{rowData.penjamin_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.tgl_masuk}}</p>
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
            <td colspan="10" v-if="isExpanded" style="background-color:#eee;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                    <div uk-grid>
                        <div class="uk-width-expand">
                            <h5 style="font-weight:500;padding:0 0 1em 0;margin:0;" class="uk-width-expand">DETAIL INFORMASI</h5>
                        </div>
                    </div>
                    <div>
                        <ul class="uk-tab hims-inap-tab" uk-switcher id="switcherDetailInap" style="padding:0;margin:0;">
                            <li><div><a href="#"><h5>Registrasi</h5></a></div></li>
                            <li><div><a href="#"><h5>Pemakaian Bed</h5></a></div></li>
                            <li style="padding:0;"><div><a href="#"><h5>Pemeriksaan</h5></a></div></li>
                        </ul>
                        <ul class="uk-switcher">
                            <li>
                                <div style="padding:0.5em;margin:0;background-color: #fff;">
                                    <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid style="padding:0;margin:0;"> 
                                        <div class="uk-width-1-3@m">
                                            <dl class="uk-description-list pasien-description-list">
                                                <dt>No.RM | ID</dt>
                                                <dd>{{rowData.no_rm}} | {{rowData.pasien_id}}</dd>
                                                <dt>Nama</dt>
                                                <dd>{{rowData.nama_pasien}}</dd>
                                                <dt>Usia</dt>
                                                <dd>{{rowData.usia}}</dd>
                                            </dl>
                                        </div>
                                        <div class="uk-width-1-3@m">
                                            <dl class="uk-description-list pasien-description-list">
                                                <dt>Penjamin</dt>
                                                <dd>{{rowData.jns_penjamin}} - {{rowData.penjamin_nama}}</dd>
                                                <dt>No. Kepesertaan</dt>
                                                <dd>{{rowData.no_kepesertaan}}</dd>
                                                <dt>No. SEP</dt>
                                                <dd>{{rowData.no_sep}}</dd>
                                            </dl>
                                        </div>
                                        <div class="uk-width-1-3@m">
                                            <dl class="uk-description-list pasien-description-list">
                                                <dt>Status</dt>
                                                <dd>{{rowData.status}}</dd>
                                                <dt>Unit</dt>
                                                <dd>{{rowData.unit_nama}}</dd>
                                                <dt>Ruang (Bed)</dt>
                                                <dd>{{rowData.ruang_nama}} ({{rowData.no_bed}})</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li style="padding:0;margin:0;background-color: #fff;">
                                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0 0 0 0;">
                                    <thead>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">Tgl Masuk</th>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">Tgl.Keluar</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Unit</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Ruang</th>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">No.Bed</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="bed in rowData.beds">
                                            <td style="width: 130px;padding:1em; text-align:center;">{{bed.tgl_masuk}} {{bed.jam_masuk}}</td>
                                            <td style="width: 130px;padding:1em; text-align:center;">{{bed.tgl_keluar}} {{bed.jam_keluar}}</td>
                                            <td style="padding:1em; text-align:left;">{{bed.unit_nama}}</td>
                                            <td style="padding:1em; text-align:left;">{{bed.ruang_nama}}</td>
                                            <td style="width: 80px;padding:1em; text-align:center;">{{bed.no_bed}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </li>
                            <li style="padding:0;margin:0;">
                                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0 0 0 0;">
                                    <thead>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">Tanggal</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Jenis</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Unit</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Ruang</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">Dokter</th>
                                        <th class="child-table-header" style="text-align:left;padding:1em;">No.Urut</th>
                                        <th class="child-table-header" style="text-align:center;padding:1em;">Total</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="trx in rowData.transactions">
                                            <td style="width: 130px;padding:1em; text-align:center;">{{trx.tgl_transaksi}}</td>
                                            <td style="width: 130px;padding:1em; text-align:left;">{{trx.jns_transaksi}}</td>
                                            <td style="padding:1em; text-align:left;">{{trx.unit_nama}}</td>
                                            <td style="padding:1em; text-align:left;">{{trx.ruang_nama}}</td>
                                            <td style="padding:1em; text-align:left;">{{trx.dokter_nama}}</td>
                                            <td style="padding:1em; text-align:left;">{{trx.no_antrian}}</td>
                                            <td style="width: 80px;padding:1em; text-align:center;">{{formatCurrency(trx.total)}}</td>
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
    name : 'row-pasien-inap',
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
        
    }
}
</script>