<template>
    <tr v-if="rowData" style="border-top:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu" @click.prevent="linkCallback(rowData)">
                    {{rowData.kode_booking}}
                    <p style="margin:0;padding:0;font-weight:350;">{{rowData.tgl_registrasi}}</p>
                </a>
            </div>    
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.no_antrian}}</p>
            <p style="margin:0;padding:0;font-weight:350;">{{rowData.tgl_periksa}} {{rowData.jam_periksa}}</p>
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.nama_pasien}} ({{rowData.jns_kelamin}})</p>
            <p style="margin:0;padding:0;font-weight:350;">{{rowData.no_rm}} - {{rowData.pasien_id}}</p>
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.dokter_nama}}</p>
            <p style="margin:0;padding:0;font-weight:350;">{{rowData.unit_nama}}</p>
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.penjamin_nama}}</p>
            <p style="margin:0;padding:0;font-weight:350;">{{rowData.jns_penjamin}}</p>
        </td>
        
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.status_reg}}</p>
            <!-- <p style="margin:0;padding:0;font-weight:350;">{{rowData.tgl_periksa}} {{rowData.jam_periksa}}</p> -->
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.cara_registrasi}}</p>
            <p style="margin:0;padding:0;font-weight:350;">{{rowData.jns_registrasi}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="9" v-if="isExpanded" style="background-color:#fff;border-top:0px solid #000;padding:0;margin:0;">
                <div style="padding:1em;margin:0;background-color: whitesmoke;">
                    <detail-pasien-poli :detailData="rowData"></detail-pasien-poli>
                </div>
            </td>
        </Transition>
    </tr>
</template>

<script>
import detailPasienPoli from '@/Pages/RawatJalan/components/DetailPasienPoli.vue';

export default {
    components : { detailPasienPoli },
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null },
        linkCallback : {type:Function, required:true}
    },
    name : 'row-booking',
    data() {
        return {
            isExpanded : false,
            buffer : [],
        }
    },
    methods : {
        changeRowExpand() {
            this.isExpanded = !this.isExpanded;
            return false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString(); 
            return val;
        }
    }
}
</script>