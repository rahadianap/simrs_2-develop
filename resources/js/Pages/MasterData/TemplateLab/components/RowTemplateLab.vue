<template>
    <tr v-if="rowData">
        <td style="background-color:#fefefe;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td style="width:130px;font-weight: 500;">
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu" @click.prevent="linkCallback(rowData)">{{rowData.tindakan_id}}</a>
            </div>    
        </td>
        <td>{{rowData.tindakan_nama}}</td>
        <td>{{rowData.deskripsi}}</td>
        <td style="text-align:center;width:100px;">{{rowData.klasifikasi}}</td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>

    <tr>
        <Transition>
            <td colspan="6" v-if="isExpanded" style="background-color:#fefefe;border-top:1px solid #efefef;">
                <div style="padding:0;margin:0;">
                    <h6 style="font-weight:500;padding:0;margin:0;">ITEM HASIL LABORATORIUM</h6>
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                        <thead>
                            <th class="child-table-header" style="text-align:left;padding:1em;">ID</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">KLASIFIKASI</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">SUB KLASIFIKASI</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">NAMA</th>
                            <th class="child-table-header" style="text-align:center;padding:1em;">NO URUT</th>
                        </thead>
                        <tbody>
                            <tr v-for="dt in rowData.labItems">             
                                <td style="font-weight:500;width:150px;">{{dt.lab_hasil_id}}</td>                                   
                                <td style="width:120px;" class="uk-text-uppercase">{{dt.klasifikasi}}</td>
                                <td style="width:120px;" class="uk-text-uppercase">{{dt.sub_klasifikasi}}</td>
                                <td>{{dt.hasil_nama}}</td>
                                <td style="width:80px;text-align: center;">{{dt.no_urut}}</td>                                  
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
        rowFunctions : { type:Object, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null },
    },
    name : 'row-template-lab',
    data() {
        return {
            isExpanded : false,
            buffer : [],
        }
    },
    methods : {
        getNormalValue(datas) {
        },
        changeRowExpand() {
            this.isExpanded = !this.isExpanded;
            return false;
        },
    }
}
</script>
<style>
.v-enter-active,
.v-leave-active {
  transition: opacity 0.2s ease-in-out;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>