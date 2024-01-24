<template>
    <tr v-if="rowData" style="border-top:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:chevron-up;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:chevron-down;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">
                    {{rowData.no_rm}}
                    <p style="margin:0;padding:0;font-weight:350;">ID. {{rowData.pasien_id}}</p>
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
        <td class="">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.nama_lengkap}}</p>
            <p v-if="rowData.is_pasien_luar" style="margin:0;padding:0;font-weight: 350;">Pasien Luar</p>
            <p v-else="rowData.is_pasien_luar" style="margin:0;padding:0;font-weight: 350;">Pasien Internal</p>
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.tempat_lahir}} , {{dateFormatting(rowData.tgl_lahir)}}</p>
            <p v-if="rowData.jns_kelamin == 'L'" style="margin:0;padding:0;font-weight:350;"><span>Jk. </span>Laki-laki</p>
            <p v-else-if="rowData.jns_kelamin == 'P'" style="margin:0;padding:0;font-weight:350;"><span>Jk. </span>Perempuan</p>
            <p v-else style="margin:0;padding:0;font-weight:350;"><span>Jk. </span>Tidak tahu</p>
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.nik}}</p>
            <p style="margin:0;padding:0;font-weight:350;">{{rowData.no_kk}}</p>
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.penjamin_nama}}</p>
            <p style="margin:0;padding:0;font-weight:350;">{{rowData.jns_penjamin}} No.{{rowData.no_kepesertaan}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <button class="uk-button" type="button" @click.prevent="fnPasienSelected(rowData)">Pilih</button>
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="8" v-if="isExpanded" style="background-color:#f0f0f0;border-top:0px solid #000;padding:0;margin:0;">
                <div style="padding:1em;margin:0;background-color: whitesmoke;">
                    <detail-pasien :detailData="rowData"></detail-pasien>                    
                </div>
            </td>
        </Transition>
    </tr>
</template>
<script>
import detailPasien from '@/Pages/MasterData/Pasien/components/DetailPasien.vue';

export default {
    components : { detailPasien },
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null },
        fnPasienSelected : {type:Function, required:false },
    },
    name : 'row-modal-pasien',
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
        },
    }
}
</script>