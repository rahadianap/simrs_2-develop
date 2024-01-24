<template>
    <tr v-if="rowData" style="border-bottom:1px solid #eee;">
        <td style="background-color:#fafafa;border:none;width:5px;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;"></p>
        </td> 
        <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">
                    {{rowData.karyawan_id}}
                </a>
                <div uk-dropdown="mode: click">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="rowFunctions" v-for="rf in rowFunctions">
                            <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                <span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>    
        </td>
        <td style="max-width: 220px;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.nama}}</p>
            <p style="margin:0;padding:0;font-weight:350;">NIP. {{rowData.nip}}</p>
        </td> 
        <td style="max-width: 220px;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.tempat_lahir}}, {{dateFormatting(rowData.tanggal_lahir)}}</p>
            <p v-if="rowData.jns_kelamin == 'L'" style="margin:0;padding:0;font-weight:350;"><span>Jk. </span>Laki-laki</p>
            <p v-else-if="rowData.jns_kelamin == 'P'" style="margin:0;padding:0;font-weight:350;"><span>Jk. </span>Perempuan</p>
            <p v-else style="margin:0;padding:0;font-weight:350;"><span>Jk. </span>Tidak tahu</p>
        </td> 
        <td style="max-width: 220px;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.jabatan_nama}}</p>
        </td> 
        <td style="max-width: 220px;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.unit_nama}}</p>
        </td>
        <td style="max-width: 220px;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.tgl_masuk}}</p>
        </td>  
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
</template>
<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null },
    },
    name : 'row-karyawan',
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