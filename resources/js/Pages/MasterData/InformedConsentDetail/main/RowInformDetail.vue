<template>
    <tr v-if="rowData" style="border-top:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:10px;">
        </td> 
        <td style="width:180px;">
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">
                    <p style="margin:0;padding:0;font-weight: 500;">{{ rowData.inform_detail_id }}</p>
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
                    <p style="margin:0;padding:0;font-weight: 400;">
                        {{ rowData.pertanyaan }}
                    </p>
                    <p v-if="rowData.satuan " style="margin:0;padding:0;font-weight: 400;">
                        <span style="font-weight: 500;font-style: italic;">Satuan  : </span>{{rowData.satuan}}
                    </p>
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
                <p style="margin:0;padding:0;font-weight: 500;">{{rowData.tipe_jawaban}}</p>
            </div>    
        </td>
        <!-- <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <p v-if="rowData.tipe_jawaban == 'radio' || rowData.tipe_jawaban == 'ScoreItem' " style="margin:0;padding:0;font-weight: 400;">
                    <span style="font-weight: 500;font-style: italic;">pilihan  : </span>{{rowData.pilihan_jawaban}}
                </p>
            </div>    
        </td> -->
        <td style="max-width:150px;">
            <p style="margin:0;padding:0;font-weight: 400;">{{rowData.deskripsi}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_mandatory" disabled style="margin-left:5px;border:1px solid black;">
        </td>
        <td style="width:100px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_tanda_vital" disabled style="margin-left:5px;border:1px solid black;">
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
</template>

<script>

export default {
    //components : { detailPasien },
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null }
    },
    name : 'row-inform-detail',
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