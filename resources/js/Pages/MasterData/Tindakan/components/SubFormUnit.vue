<template>    
    <div class="hims-form-subpage">
        <div class="uk-card">
            <div class="uk-width-1-1" style="padding:0.5em;">
                <p style="color:#333; font-size:12px; padding:0;margin:0; font-style: italic; ">*Penambahan dan pengurangan unit pelayanan langsung disimpan.</p>
            </div>
            <form class="uk-width-1-1" action="" @submit.prevent="saveDataUnit" style="padding:0;margin:0 0 1em 0;">
                <div class="uk-width-large uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;margin:0;">         
                    <div class="uk-grid-small uk-width-full" uk-grid style="margin:0;padding:0;">
                        <search-list
                            :config = configSelect
                            :dataLists = unitLists.data
                            placeholder = "unit pelayanan"
                            captionField = "unit_nama"
                            valueField = "unit_id"
                            :detailItems = unitDesc
                            :value = "addedUnit.unit_id"
                            v-on:item-select = "unitSelected"
                        ></search-list>
                    
                        <button type="submit" class="uk-width-auto hims-table-btn-submit" style="border:none;border-top-right-radius: 5px;border-bottom-right-radius: 5px;">
                            <span uk-icon="plus-circle"></span> Tambahkan
                        </button>                        
                    </div>
                </div>
            </form>
            <div style="margin:0;padding:0;" class="uk-overflow-auto">
                <table class="uk-table hims-table uk-table-responsive">
                    <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                        <tr>
                            <th style="width:150px;">ID</th>
                            <th>Unit</th>
                            <th>Kepala Unit</th>
                            <th style="text-align:center;">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="tindakan.unitTindakan" v-for="dt in tindakan.unitTindakan">
                            <td style="font-weight:500;width:150px;">{{dt.unit_id}}</td>
                            <td class="uk-text-uppercase">{{dt.unit_nama}}</td>
                            <td class="uk-text-uppercase">{{dt.kepala_unit}}</td>
                            <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
                                <a href="#" uk-icon="icon:trash;ratio:0.8" @click.prevent="deleteData(dt)"></a>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="4">
                                <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template> 

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'sub-form-unit',
    props : {
        tindakan : { type:Object, default:null, required : false },
    },
    components : { SearchList },
    data() {
        return {
            configSelect : {
                compClass : "uk-width-expand",
                compStyle : "padding:0 0 0 0.2em;margin:0;border:none;",
            },

            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],
            // tindakan : {
            //     tindakan_id : null,
            //     tindakan_nama : null,
            //     unitTindakan : [],
            // },
            addedUnit : {
                unit_id : null,
                unit_nama : null,
            },
            urlPicker : ''
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('unitPelayanan',['unitLists']),
    },  
    mounted() {
        this.initialize();
    },
    methods :{
        ...mapMutations(['CLR_ERRORS']), 
        ...mapActions('tindakan',['addTindakanUnit','deleteTindakanUnit']),    
        ...mapActions('unitPelayanan',['listUnitPelayanan']),

        initialize(){
            this.listUnitPelayanan({per_page:'ALL'});
            this.$emit('ready',true);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus unit ${data.unit_nama} dari mappping tindakan ${this.tindakan.tindakan_nama}. Lanjutkan ?`)){
                this.deleteTindakanUnit(data.unit_tindakan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$emit('updated',data);
                    }
                    else { alert (response.message) }
                });
            };
        },

        refreshData(data){
            this.tindakan.tindakan_id = data.tindakan_id;
            this.tindakan.tindakan_nama = data.tindakan_nama;
            this.tindakan.unitTindakan = data.unitTindakan;
            this.listUnitPelayanan({per_page:'ALL'});
        },

        unitSelected(data) {
            this.addedUnit.unit_id = null;
            this.addedUnit.unit_nama = null;
            if(data) {
                this.addedUnit.unit_id = data.unit_id;
                this.addedUnit.unit_nama = data.unit_nama;
            }
        },

        saveDataUnit(){
            this.CLR_ERRORS();            
            let unit = {
                tindakan_id : this.tindakan.tindakan_id,
                unitTindakan :[],
            }
            unit.unitTindakan.push(this.addedUnit);
            this.addTindakanUnit(unit).then((response) => {
                if (response.success == true) {
                    this.addedUnit.unit_id = null;
                    this.addedUnit.unit_nama = null;                    
                    alert("Unit berhasil dipetakan.");
                    this.$emit('updated',true);
                }
            })           
        },
    }
}
</script>

