<template>
    <tr v-if="rowData" style="border-top:1px solid #eee;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td style="font-weight: 500;">
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">{{rowData.unit_id}}</a>
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
        <td style="font-weight: 500;">
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">{{rowData.unit_nama}}</a>
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
        <td class="uk-text-uppercase">
            <!-- <p style="margin:0;padding:0;font-weight: 500;">{{rowData.unit_nama}}</p> -->
            <p style="margin:0;padding:0;">{{rowData.inisial}}</p>
        </td>
        <td class="uk-text-uppercase">
            <!-- <p style="margin:0;padding:0;font-weight: 500;">{{rowData.unit_nama}}</p> -->
            <p style="margin:0;padding:0;">{{rowData.jenis_registrasi}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 400;">{{rowData.kepala_unit}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="7" v-if="isExpanded" style="background-color:#eee;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                    <ul class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0.2em 0 0 0;margin:0;">
                        <li><div><a href="#"><h5>Daftar</h5></a></div></li>
                        <li style="padding:0;"><div><a href="#"><h5>Pemetaan</h5></a></div></li>
                    </ul>
                    <ul class="uk-switcher">
                        <li>
                            <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                <thead>
                                    <th class="child-table-header" style="text-align:left;padding:1em;">GROUP</th>
                                    <th class="child-table-header" style="text-align:center;padding:1em;">KODE</th>
                                    <th class="child-table-header" style="text-align:center;padding:1em;">NAMA</th>
                                    <th class="child-table-header" style="text-align:center;padding:1em;">SUBKODE</th>
                                    <th class="child-table-header" style="text-align:center;padding:1em;">SUBNAMA</th>                                     
                                    <th class="child-table-header" style="text-align:left;padding:1em;">ID</th>
                                    <th class="child-table-header" style="text-align:left;padding:1em;">...</th>
                                </thead>
                                <tbody>
                                    <tr v-if="rowData.bridging" v-for="dt in rowData.bridging">
                                        <td style="padding:1em; text-align:left;font-weight: 500;">{{dt.bridging_group}}</td>
                                        <td style="padding:1em; text-align:center;">{{dt.bridging_resource_id}}</td>
                                        <td style="padding:1em; text-align:center;">{{dt.bridging_resource_name}}</td>
                                        <td style="padding:1em; text-align:center;">{{dt.bridging_sub_resource_id}}</td>
                                        <td style="padding:1em; text-align:center;">{{dt.bridging_sub_resource_name}}</td>
                                        <td style="padding:1em; text-align:left;">{{dt.bridging_id}}</td>
                                        <td style="padding:1em; text-align:left;">
                                            <a href="#" @click.prevent="deleteRowData(dt)"><span uk-icon="trash"></span></a>
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <p>tidak ada data untuk ditampilkan</p>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                        <li>
                            <div>
                                <form-map-poli :refData="rowData" v-on:proceed ="refreshRow"></form-map-poli>
                            </div>
                        </li>
                    </ul>
                </div>
            </td>
        </Transition>
    </tr>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import FormMapPoli from '@/Pages/BridgingBpjs/UnitPelayanan/FormMapPoli.vue';
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        selectAllCallback : { type:Function, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null }
    },
    components : {
        FormMapPoli,
    },
    emits : ['itemSelected','itemUpdated'],
    name : 'row-map-poli',
    data() {
        return {
            isExpanded : false,
            selectAll : true,
            buffer : [],
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapActions('bpjsReferensi',['refBpjsPoli','updateBpjsMapping','removeBpjsMapping']),
        ...mapMutations(['CLR_ERRORS']),

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
        },

        refreshRow(){
            this.$emit('itemUpdated',true);
        },

        deleteRowData(data){
            this.CLR_ERRORS();
            let dt = { kode_lokal : this.rowData.unit_id, bridging_group : data.bridging_group };
            if(confirm(`Proses ini akan menghapus pemetaan bridging poli (unit pelayanan). Lanjutkan ?`)){
                this.removeBpjsMapping(dt).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$emit('itemUpdated',true);
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>