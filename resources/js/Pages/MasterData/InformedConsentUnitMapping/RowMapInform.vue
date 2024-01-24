<template>
    <tr v-if="rowData" style="border-top:1px solid #eee;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td style="font-weight: 500;">
            <div style="margin:0;padding:0;">
                <!-- <a href="#" class="row-link-menu">{{rowData.unit_id}}</a>
                <div uk-dropdown="mode: click">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="rowFunctions" v-for="rf in rowFunctions">
                            <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                <span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span>
                            </a>
                        </li>
                    </ul>
                </div> -->
                <p style="margin:0;padding:0;font-weight: 700;">{{rowData.unit_id}}</p>
            </div>    
        </td>
        <td style="font-weight: 500;">
            <div style="margin:0;padding:0;">
                <p style="margin:0;padding:0;font-weight: 700;">{{rowData.unit_nama}}</p>
            </div>    
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.inisial}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;">{{rowData.kepala_unit}}</p>
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="6" v-if="isExpanded" style="background-color:#eee;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                    <div>
                        <button type="button" style="padding:0.5em 1em 0.5em 1em;" @click.prevent="addCallback(rowData)">Tambahkan inform consent</button>
                    </div>
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0.5em 0 0 0;">
                        <thead>
                            <th class="child-table-header" style="text-align:left;padding:1em;width:180px;">ID</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">INFORM TEMPLATE</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">DESKRIPSI</th>                                                                         
                            <th class="child-table-header" style="text-align:center;padding:1em;width:40px;">...</th>
                        </thead>
                        <tbody>
                            <tr v-if="rowData.inform" v-for="dt in rowData.inform">
                                <td style="padding:1em; text-align:left;font-weight: 500;width:100px;">{{dt.template_id}}</td>
                                <td style="padding:1em; text-align:left;">{{dt.template_nama}}</td>
                                <td style="padding:1em; text-align:left;">{{dt.deskripsi}}</td>
                                <td style="padding:1em; text-align:center;width:40px;">
                                    <a href="#" @click.prevent="removeCallback(dt)"><span uk-icon="trash"></span></a>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="4">
                                    <p>tidak ada data untuk ditampilkan</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </Transition>
    </tr>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
// import FormMapDokter from '@/Pages/BridgingBpjs/Dokter/FormMapDokter.vue';

export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        selectAllCallback : { type:Function, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null },
        addCallback : { type:Function, required:false, default:null },
        removeCallback : { type:Function, required:false, default:null },
        listDokterJkn : { type:Object, required:false, default:null },
    },
    components : {
        //FormMapDokter,
    },
    emits : ['itemSelected','itemUpdated'],
    name : 'row-map-inform',
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
            if(confirm(`Proses ini akan menghapus pemetaan bridging dokter. Lanjutkan ?`)){
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