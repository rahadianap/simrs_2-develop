<template>
    <tr v-if="rowData" style="border-bottom:1px solid #cecece;">
        <td style="width:20px;">
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu"><span uk-icon="icon:menu;ratio:0.8"></span></a>
                <div uk-dropdown="mode: click">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="rowFunctions" :key="rf" v-for="rf in rowFunctions">
                            <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                <span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>    
        </td>       
        <td style="font-weight: 500;width:120px;">
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">{{rowData.reg_id}}</a>
                <div uk-dropdown="mode: click">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="rowFunctions" :key="rf" v-for="rf in rowFunctions">
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
</template>
<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null }
    },
    name : 'row-registrasi',
    data() {
        return {
            isExpanded : false,
            buffer : [],
        }
    },
    methods : {
    }
}
</script>