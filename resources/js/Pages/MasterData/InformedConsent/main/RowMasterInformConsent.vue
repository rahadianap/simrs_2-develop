<template>
    <tr v-if="rowData" style="border-top:1px solid #ccc;">
        <!-- <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{ rowData.template_nama }}</p>
            <p style="margin:0;padding:0;font-weight: 400;">{{ rowData.deskripsi }}</p>
            <p style="margin:0;padding:0;font-weight: 350;">{{ rowData.template_id }}</p>
        </td> -->
        <td style="background-color:#fafafa;border:none;width:20px;">
            <!-- <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a> -->
        </td> 
        <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">
                    <p style="margin:0;padding:0;font-weight: 500;">{{ rowData.template_id }}</p>
                </a>
                <div uk-dropdown="mode: click">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="rowFunctions" v-for="rf in rowFunctions">
                            <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:350;">
                                <span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>    
        </td>
        <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">
                    <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{ rowData.template_nama }}</p>
                </a>
                <div uk-dropdown="mode: click">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="rowFunctions" v-for="rf in rowFunctions">
                            <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:350;">
                                <span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>    
        </td>
        <td class="">
            <p style="margin:0;padding:0;font-weight: 400;">{{rowData.deskripsi}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
</template>
<script>
//import detailPasien from '@/Pages/MasterData/Pasien/components/DetailPasien.vue';

export default {
    //components : { detailPasien },
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null }
    },
    name : 'row-master-inform-consent',
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