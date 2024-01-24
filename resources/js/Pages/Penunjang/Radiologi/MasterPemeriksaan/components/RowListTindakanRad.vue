<template>
    <tr v-if="rowData" style="border-top:1px solid #eee;">
        <td style="background-color:#fafafa;border:none;width:5px;">
            <!-- <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a> -->
        </td> 
        <td style="width:130px;">
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">{{rowData.tindakan_id}}</a>
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
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.tindakan_nama}}</p>
        </td>
        <td class="uk-text-uppercase1">
            <p style="margin:0;padding:0;">{{rowData.deskripsi}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.klasifikasi}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    <!-- <tr>
        <Transition>
            <td colspan="7" v-if="isExpanded" style="background-color:#fcfcfc;border-bottom:1px solid #fff;">
                <div style="padding:0;margin:0;">
                    <h5 style="font-weight:500;padding:0.2em;margin:0;">DETAIL PEMERIKSAAN</h5>
                    <div>                                          
                        <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                            <thead>
                                <th class="child-table-header" style="text-align:left;padding:1em;">ID</th>
                                <th class="child-table-header" style="text-align:left;padding:1em;">Nama</th>
                                <th class="child-table-header" style="text-align:center;padding:1em;">Klasifikasi</th>
                                <th class="child-table-header" style="text-align:center;padding:1em;">Sub Klasifikasi</th>
                                <th class="child-table-header" style="text-align:center;padding:1em;">No. Urut</th>                                     
                            </thead>
                            <tbody>
                                <tr v-for="dt in rowData.labItems">
                                    <td style="width: 130px;padding:1em; text-align:left; font-weight:500;">{{dt.lab_hasil_id}}</td>
                                    <td style="padding:1em; text-align:left;">{{dt.hasil_nama}}</td>
                                    <td style="width: 120px;padding:1em; text-align:center;">{{dt.klasifikasi}}</td>
                                    <td style="width: 120px;padding:1em; text-align:center;">{{dt.sub_klasifikasi}}</td>
                                    <td style="width: 70px;padding:1em; text-align:center;">{{dt.no_urut}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>
        </Transition>
    </tr> -->
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
    name : 'row-list-tindakan-rad',
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

        getTotal(data){
            let total = 0;
            data.forEach(function(dt){
                total = (total*1) + (dt.nilai*1);
            });
            return total;
        }
    }
}
</script>
<style>

</style>