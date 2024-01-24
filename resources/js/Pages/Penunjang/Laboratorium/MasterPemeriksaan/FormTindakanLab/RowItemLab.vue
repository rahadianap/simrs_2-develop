<template>
    <tr v-if="rowData" style="border-top:1px solid #eee;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td style="width:130px;"><p style="margin:0;padding:0;font-weight:500;">{{rowData.lab_hasil_id}}</p></td>
        <td class="uk-text-uppercase"><p style="margin:0;padding:0;">{{rowData.hasil_nama}}</p></td>
        <td class="uk-text-uppercase" style="width:100px;text-align:center;"><p style="margin:0;padding:0;">{{rowData.klasifikasi}}</p></td>
        <td class="uk-text-uppercase" style="width:120px;text-align:center;"><p style="margin:0;padding:0;">{{rowData.sub_klasifikasi}}</p></td>
        <td class="uk-text-uppercase" style="width:80px;text-align:center;"><p style="margin:0;padding:0;">{{rowData.no_urut}}</p></td>
        <td style="width:60px;padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
            <a href="#" uk-icon="icon:trash;ratio:0.8" @click.prevent="deleteCallback(rowData)"></a>
        </td>
        <td style="width:130px;text-align:center;">
            <button href="#" @click.prevent="updateCallback(rowData)" style="font-style:italic;">ubah data</button>
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="8" v-if="isExpanded" style="background-color:#fefefe;border-bottom:1px solid #fff;">
                <div style="padding:0;margin:0;">
                    <h5 style="font-weight:500;padding:0.2em;margin:0;">NILAI NORMAL PEMERIKSAAN</h5>
                    <div v-if="rowData.normal">                                          
                        <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                            <thead>
                                <th class="child-table-header" style="text-align:left;padding:1em;">ITEM</th>
                                <th class="child-table-header" style="text-align:left;padding:1em;">GROUP</th>
                                <th class="child-table-header" style="text-align:left;padding:1em;">JENIS HASIL</th>
                                <th class="child-table-header" style="text-align:center;padding:1em;">LAKI-LAKI</th>
                                <th class="child-table-header" style="text-align:center;padding:1em;">PEREMPUAN</th>                        
                            </thead>
                            <tbody>
                                <tr v-if="rowData.normal" v-for="dt in rowData.normal">
                                    <td style="width: 130px;padding:1em; text-align:left; font-weight:500;">{{dt.hasil_nama}}</td>
                                    <td style="width: 130px;padding:1em; text-align:left; font-weight:500;">{{dt.normal_group}}</td>
                                    <td style="padding:1em; text-align:left;">{{dt.jenis_hasil}}</td>
                                    <td style="width: 120px;padding:1em; text-align:center;">{{dt.lk_normal}}</td>
                                    <td style="width: 120px;padding:1em; text-align:center;">{{dt.pr_normal}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>
        </Transition>
    </tr>
</template>
<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        updateCallback : { type:Function, required:false, default:null },
        deleteCallback: { type:Function, required:false, default:null },
    },
    name : 'row-item-lab',
    data() {
        return {

            isExpanded : false,
            selectAll : true,
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