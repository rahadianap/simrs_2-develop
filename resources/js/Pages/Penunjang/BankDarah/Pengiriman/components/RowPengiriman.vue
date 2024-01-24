<template>
    <tr v-if="rowData" style="border-bottom:1px solid #999;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td>
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">
                    {{rowData.darah_dist_id}}
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
                <p style="margin:0;padding:0;font-weight: 400;">{{rowData.tgl_permintaan}} {{rowData.jam_permintaan}}</p>
            </div>    
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.no_rm}} - {{rowData.pasien_nama}}</p>
            <p style="margin:0;padding:0;">{{rowData.reg_id}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.unit_nama}}</p>
            <p style="margin:0;padding:0;">{{rowData.ruang_nama}} - {{rowData.bed_no}}</p>
        </td>
        <td>
            <p style="margin:0;padding:0;"><span style="font-weight: 500;">Tgl. </span>{{rowData.tgl_dibutuhkan}} {{rowData.jam_dibutuhkan}}</p>
            <p style="margin:0;padding:0;"><span style="font-weight: 500;">Gol. </span>{{rowData.gol_darah}}, <span style="font-weight: 500;">Rhe. </span>{{rowData.rhesus}}</p>
        </td>
        
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.catatan}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="8" v-if="isExpanded" style="background-color:#f0f0f0;border-top:1px solid #efefef;">
                <div style="padding:0;margin:0;">
                    <h5 style="font-weight:500;padding:0;margin:0;">ITEM PENGIRIMAN</h5>
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:1em 0 0 0;">
                        <thead>
                            <th class="child-table-header" style="width:150px;text-align:left;padding:1em;">Item</th>
                            <th class="child-table-header" style="text-align:center;padding:1em;">Gol/Rhesus</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Jenis</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Volume</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">Kadaluarsa</th> 
                            
                        </thead>
                        <tbody>
                            <tr v-for="child in rowData.items">
                                <td style="width: 150px;padding:1em;">{{child.no_labu}}</td>
                                <td style="width: auto;text-align:center;">{{child.gol_darah}} / {{child.rhesus}}</td>
                                <td style="width: auto;">{{child.group_darah}}</td>
                                <td style="width: 80px; text-align:center;">{{child.volume}} CC</td>
                                <td style="width: 80px; text-align:center;">{{child.tgl_kadaluarsa}}</td>
                            </tr>
                        </tbody>
                    </table>
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
    name : 'row-pengiriman',
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