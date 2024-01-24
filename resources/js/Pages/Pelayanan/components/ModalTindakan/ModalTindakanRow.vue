<template>
    <tr v-if="rowData" style="border-top:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:10px;">
            <!-- <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a> -->
        </td> 
        <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu" @click.prevent="fnActSelected(rowData)">
                    {{rowData.tindakan_id}}
                    <!-- <p style="margin:0;padding:0;font-weight:350;">ID. {{rowData.tindakan_id}}</p> -->
                </a>
            </div>    
        </td>
        <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu" @click.prevent="fnActSelected(rowData)">
                    {{rowData.tindakan_nama}}
                    <!-- <p style="margin:0;padding:0;font-weight:350;">ID. {{rowData.tindakan_id}}</p> -->
                </a>
            </div>    
        </td>
        <td>
            <p style="margin:0;padding:0;">{{rowData.deskripsi}}</p>
        </td>
        <!-- 
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
        </td> -->
        <td style="width:50px;text-align: center;">
            <button @click.prevent="fnActSelected(rowData)">Pilih</button>
        </td>
    </tr>
    <!-- <tr>
        <Transition>
            <td colspan="8" v-if="isExpanded" style="background-color:#f0f0f0;border-top:0px solid #000;padding:0;margin:0;">
                <div style="padding:1em;margin:0;background-color: whitesmoke;">
                    <detail-pasien :detailData="rowData"></detail-pasien>                    
                </div>
            </td>
        </Transition>
    </tr> -->
</template>

<script>

export default {
    name : 'modal-tindakan-row',
    props : {
        rowData : { type:Object, required:false, default:null },
       fnActSelected : {type:Function, required:true },
    },
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