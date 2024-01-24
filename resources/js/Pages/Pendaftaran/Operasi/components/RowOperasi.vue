<template>
    <tr v-if="rowData" style="border-bottom:1px solid #cecece;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td>         
        <td style="font-weight: 500;width:120px;">
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">{{rowData.reg_id}}</a>
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
        <td style="width:150px;" class="">
            <p style="margin:0;padding:0;font-weight:500;">{{rowData.tgl_periksa}}</p>
            <p style="margin:0;padding:0;font-size:12px;">{{rowData.jam_praktek}}</p>            
        </td>
        <td class="" style="font-size: 14px;">{{rowData.no_antrian}}</td>
        <td class="">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.no_rm}} - {{rowData.nama_pasien}}</p>
            <p style="margin:0;padding:0;font-size:12px;">{{rowData.pasien_id}}</p>
        </td>
        <td class="">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.dokter_nama}}</p>
            <p style="margin:0;padding:0;font-size:12px;">{{rowData.unit_nama}}</p>
        </td>
        <td class="">{{rowData.tgl_registrasi}}</td>
        <td class="">{{rowData.status_reg}}</td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="9" v-if="isExpanded" style="background-color:#fefefe;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                </div>
            </td>
        </Transition>
    </tr>
</template>
<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null }
    },
    name : 'row-operasi',
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
    }
}
</script>