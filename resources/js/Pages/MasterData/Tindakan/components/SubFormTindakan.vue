<template>    
    <div class="hims-form-subpage">
        <div class="uk-card">
            <div class="uk-width-1-1" style="padding:0.5em;">
                <p style="color:#333; font-size:12px; padding:0;margin:0; font-style: italic; ">*Penambahan dan pengurangan sub item pelayanan langsung disimpan.</p>
            </div>
            <form class="uk-width-1-1" action="" @submit.prevent="saveDataUnit" style="padding:0;margin:0 0 1em 0;">
                <div class="uk-width-1-1 uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;margin:0;">         
                    <div class="uk-grid-small uk-width-full" uk-grid style="margin:0;padding:0;">
                        <search-select
                            :config = configSelect
                            :searchUrl = urlAct
                            placeholder = "pilih pelayanan"
                            captionField = "tindakan_nama"
                            valueField = "tindakan_nama"
                            :itemListData = tindakanDesc
                            :value = "addedTindakan.sub_tindakan_nama"
                            v-on:item-select = "tindakanSelected"
                        ></search-select>
                        <input class="uk-input uk-form-small uk-width-auto" type="number" placeholder="jumlah" v-model="addedTindakan.jumlah" style="border:none;border-left:1px solid #ccc;margin:0;padding:0.2em 0 0 0;text-align:center;">
                        
                        <button type="button" @click.prevent="addSubTindakan" class="uk-width-auto hims-table-btn-submit" style="border:none;border-top-right-radius: 5px;border-bottom-right-radius: 5px;">
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
                            <th>Item Sub Tindakan</th>
                            <th>Jumlah</th>
                            <th style="text-align:center;">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="tindakan.subTindakan" v-for="dt in tindakan.subTindakan" v-bind:class="dt.is_aktif != true ? 'inaktif':'' ">
                            <td style="font-weight:500;width:150px;">{{dt.sub_tindakan_id}}</td>
                            <td class="uk-text-uppercase">{{dt.sub_tindakan_nama}}</td>
                            <td class="uk-text-uppercase" style="text-align:center;">{{dt.jumlah}}</td>
                            <td style="padding:1.2em 0.5em 1em 0.5em; margin:0; color:black; font-size:12px; text-align:center;">
                                <div class="uk-form-controls"><input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" @change="updateData(dt)"></div>
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
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'sub-form-tindakan',
    props : {
        tindakan : { type:Object, default:null, required : false },
    },
    components : { SearchSelect },
    data() {
        return {
            configSelect : {
                compClass : "uk-width-expand",
                compStyle : "padding:0 0 0 0.2em;margin:0;border:none;",
            },

            tindakanDesc : [
                { colName : 'tindakan_nama', labelData : '', isTitle : true },
                { colName : 'tindakan_id', labelData : '', isTitle : false },
            ],
            // tindakan : {
            //     tindakan_id : null,
            //     tindakan_nama : null,
            //     subTindakan : [],
            // },

            addedTindakan : {
                detail_id : null,
                sub_tindakan_id : null,
                tindakan_id : null,
                sub_tindakan_nama : null,
                tindakan_nama : null,
                jumlah : 1,
                satuan : null,
                is_aktif : true,
            },
            urlPicker : '',
            urlAct : '',
            subTindakan : null,
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
        ...mapActions('tindakan',['addTindakanUnit','deleteTindakanUnit','listTindakan']),    
        ...mapActions('unitPelayanan',['listUnitPelayanan']),

        initialize(){
            //this.listUnitPelayanan({per_page:'ALL'});
            this.urlAct = '/acts';
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
            this.tindakan.subTindakan = data.subTindakan;
        },

        tindakanSelected(data) {
            this.addedTindakan.sub_tindakan_id = null;
            this.addedTindakan.sub_tindakan_nama = null;
            if(data) {
                this.addedTindakan.sub_tindakan_id = data.tindakan_id;
                this.addedTindakan.sub_tindakan_nama = data.tindakan_nama;
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

        addSubTindakan() {
            if(this.tindakan.subTindakan == null) { this.tindakan.subTindakan = []; }
            let isExist = this.tindakan.subTindakan.find(item => item.sub_tindakan_id == this.addedTindakan.sub_tindakan_id );
            if(!isExist) {
                this.tindakan.subTindakan.push(JSON.parse(JSON.stringify(this.addedTindakan)));
                this.clearAddedTindakan();
            }
            else {
                alert('Sub tindakan (item pelayanan) sudah ada dalam daftar.');
            }
        },

        clearAddedTindakan(){
            this.addedTindakan.tindakan_id = this.tindakan.tindakan_id;
            this.addedTindakan.tindakan_nama = this.tindakan.tindakan_nama;
            this.addedTindakan.detail_id = null;
            this.addedTindakan.sub_tindakan_id = null;
            this.addedTindakan.sub_tindakan_nama = null;
            this.addedTindakan.jumlah = 1;
            this.addedTindakan.satuan = null;
            this.addedTindakan.is_aktif = true;            
        },

        updateData(data) {
            this.tindakan.subTindakan = this.tindakan.subTindakan.filter(item => { return item.is_aktif == true || item.detail_id !== null })
            console.log(this.tindakan.subTindakan);
            this.$emit('updateSubTindakan',this.tindakan.subTindakan);
        }
    }
}
</script>

