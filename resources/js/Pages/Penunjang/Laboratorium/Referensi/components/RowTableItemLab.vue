<template>
    <tr v-if="rowData">
        <td style="background-color:#fefefe;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td style="width:130px;font-weight: 500;">
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">{{rowData.lab_hasil_id}}</a>
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
        <td style="width:100px;">{{rowData.klasifikasi}}</td>
        <td style="width:150px;">{{rowData.sub_klasifikasi}}</td>
        <td>{{rowData.hasil_nama}}</td>
        <td style="text-align:center;">{{rowData.no_urut}}</td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>

    <tr>
        <Transition>
            <td colspan="7" v-if="isExpanded" style="background-color:#fefefe;border-top:1px solid #efefef;">
                <div style="padding:0;margin:0;">
                    <h6 style="font-weight:500;padding:0;margin:0;">NILAI NORMAL</h6>
                    <!-- <p style="font-weight:400;padding:0;margin:0 0 0.5em 0;">nilai normal berdasar kelompok nilai normal lab</p> -->
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                        <thead>
                            <th class="child-table-header" style="text-align:left;">KELOMPOK</th>
                            <th class="child-table-header" style="text-align:left;">JENIS</th>
                            <th class="child-table-header" style="text-align:left;">LAKI-LAKI</th>
                            <th class="child-table-header" style="text-align:left;">PEREMPUAN</th>
                            <th class="child-table-header" style="text-align:left;">SATUAN</th>
                        </thead>
                        <tbody>
                            <tr v-for="dt in rowData.normal">                                                
                                <td>{{ dt.normal_group }}</td> 
                                <td>{{ dt.jenis_hasil }}</td>
                                <td>{{ dt.lk_normal }}</td>
                                <td>{{ dt.pr_normal }}</td>
                                <td>{{ dt.satuan }}</td>                                    
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
    name : 'row-table-item-lab',
    data() {
        return {
            isExpanded : false,
            buffer : [],
        }
    },
    methods : {
        getNormalValue(datas) {
            // let val = '';
            // if(datas.length > 0) { 
            //     datas.forEach(el => {
            //         console.log(el);
            //     });          
            // } 
            // else {  data = row.col; }
            // return data;
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