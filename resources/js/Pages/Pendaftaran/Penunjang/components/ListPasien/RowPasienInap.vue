<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td>
            <div style="margin:0;padding:0;">
                <a href="#" @click.prevent="linkCallback(rowData)" class="row-link-menu">
                    {{rowData.reg_id}}
                    <p style="margin:0;padding:0;font-weight:350;">{{rowData.trx_id}}</p>
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
            <p style="font-size:11px; margin:0;padding:0;">Ruang. {{rowData.ruang_nama}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.no_bed}}</p>
            <!-- <p style="font-size:11px; margin:0;padding:0;">{{rowData.bed_id}}</p> -->
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.penjamin_nama}}</p>
            <p style="font-size:11px; margin:0;padding:0;">Ruang. {{rowData.penjamin_id}}</p>
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
            <td colspan="9" v-if="isExpanded" style="background-color:#fefefe;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                    <div uk-grid>
                        <div class="uk-width-expand">
                            <h5 style="font-weight:500;padding:0 0 1em 0;margin:0;" class="uk-width-expand">DETAIL INFORMASI</h5>
                        </div>
                    </div>
                    <div>                       
                        <div style="padding:0.5em;margin:0;">
                            <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid style="padding:0;margin:0;"> 
                                <div class="uk-width-1-4@m">
                                    <dl class="uk-description-list pasien-description-list">
                                        <dt>Nama</dt>
                                        <dd>{{rowData.nama_pasien}} ({{rowData.usia}} Thn)</dd>
                                        <dt>No.RM | ID</dt>
                                        <dd>{{rowData.no_rm}} | {{rowData.pasien_id}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <dl class="uk-description-list pasien-description-list">
                                        <dt>Penjamin</dt>
                                        <dd>{{rowData.jns_penjamin}} - {{rowData.penjamin_nama}}</dd>
                                        <dt>No. Kepesertaan</dt>
                                        <dd>{{rowData.no_kepesertaan}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <dl class="uk-description-list pasien-description-list">                                                
                                        <dt>Unit</dt>
                                        <dd>{{rowData.unit_nama}}</dd>
                                        <dt>Ruang (Bed)</dt>
                                        <dd>{{rowData.ruang_nama}} ({{rowData.no_bed}})</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <dl class="uk-description-list pasien-description-list">                                                
                                        <dt>Tgl. Masuk</dt>
                                        <dd>{{rowData.tgl_masuk}} {{rowData.jam_masuk}}</dd>
                                        <dt>Jenis Kelamin</dt>
                                        <dd v-if="rowData.jns_kelamin == 'P'">Perempuan</dd>
                                        <dd v-else-if="rowData.jns_kelamin == 'L'">Laki-laki</dd>
                                        <dd v-else>Tidak Tahu</dd>
                                    
                                    </dl>
                                </div>
                            </div>
                        </div>
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