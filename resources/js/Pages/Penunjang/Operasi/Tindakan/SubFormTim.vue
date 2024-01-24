<template>
    <div>
        <div class="uk-grid-small hims-form-subpage" uk-grid style="margin-top:0;padding-top:0.2em;">            
            <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0.5em 0.5em 0.2em 0.5em;">
                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-reg" style="width:100px;text-align:left;color:white;">ID</th>
                        <th class="tb-header-reg" style="width:180px;text-align:left;color:white;">Dokter</th>
                        <th class="tb-header-reg" style="text-align:center;color:white;">Posisi</th>  
                        <th class="tb-header-reg" style="text-align:center;color:white;">Opr</th>      
                        <!-- <th class="tb-header-reg" style="text-align:center;color:white;">Tindakan</th>  -->
                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                    </thead>
                    <tbody>   
                        <tr>
                            <td colspan="2" style="padding:0.5em;margin:0;">
                                <search-list
                                    :config = configItemSelect
                                    :dataLists = dokterLists
                                    placeholder = "pilih dokter unit..."
                                    captionField = "dokter_nama"
                                    valueField = "dokter_nama"
                                    :detailItems = dokterDesc
                                    :value = "addedTim.dokter_nama"
                                    v-on:item-select = "addedDokterSelected"
                                ></search-list>
                            </td>
                            <td style="padding:0.5em 0.2em 0.5em 0.2em;margin:0;">
                                <select v-model="addedTim.posisi" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="posisiChange(addedTim)"> 
                                    <option v-for="pos in timOperasiRefs.json_data" v-bind:value="pos.value" v-bind:key="pos.value">{{pos.text}}</option>
                                    <option value=""></option>
                                </select>
                            </td>
                            <td style="width:50px;text-align: center;padding:0.5em 0.3em 0.3em 0.3em;margin:0;">
                                <input class="uk-checkbox" type="checkbox" v-model="addedTim.is_operator" disabled style="border:1px solid black;">
                            </td>
                            <!-- <td style="text-align: center;padding:0.5em 0.3em 0.3em 0.3em;margin:0;">
                                <search-list
                                    :config = configItemSelect
                                    :dataLists = subTindakanLists
                                    placeholder = "pilih sub tindakan paket..."
                                    captionField = "sub_tindakan_nama"
                                    valueField = "sub_tindakan_nama"
                                    :detailItems = actDesc
                                    :value = "addedTim.tindakan_nama"
                                    v-on:item-select = "addedActSelected"
                                ></search-list>
                            </td> -->
                            
                            <td style="padding:0.5em;margin:0;text-align:center;">
                                <button @click.prevent="addTimOperasi" :disabled="isLoading" style="height:30px;">Tambah</button>
                            </td>
                        </tr>                                            
                        <tr v-for="row in dataOperasi.tim_operasi" v-bind:class="row.is_aktif != true ? 'inaktif':'' ">
                            <td style="width:150px;font-size:12px;color:black; padding:0.5em;margin:0;font-weight: 500;">{{row.dokter_id}}</td>
                            <td style="font-size:12px;color:black; padding:0.5em;margin:0;">{{row.dokter_nama}}</td>
                            <td style="width:200px;font-size:12px;color:black;padding:0.2em;margin:0;">
                                <select v-model="row.posisi" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="posisiChange(row)"> 
                                    <option v-for="pos in timOperasiRefs.json_data" v-bind:value="pos.value" v-bind:key="pos.value">{{pos.text}}</option>
                                    <option value=""></option>
                                </select>
                            </td>
                            <td style="width:50px;text-align: center;padding:0.3em 0.2em 0.3em 0.2em;margin:0;">
                                <input class="uk-checkbox" type="checkbox" v-model="row.is_operator" disabled style="border:1px solid black;">
                            </td>
                            <!-- <td style="font-size:12px;color:black; padding:0.5em;margin:0;">
                                <search-list
                                    :config = configItemSelect
                                    :dataLists = subTindakanLists
                                    placeholder = "pilih sub tindakan paket..."
                                    captionField = "sub_tindakan_nama"
                                    valueField = "sub_tindakan_nama"
                                    :detailItems = actDesc
                                    :value = "row.tindakan_nama"
                                    v-on:item-select = "rowActSelected"
                                ></search-list>    
                            </td> -->
                            <td style="width:50px;text-align: center;padding:0.3em 0.2em 0.3em 0.2em;margin:0;">
                                <input class="uk-checkbox" type="checkbox" v-model="row.is_aktif" style="border:1px solid black;" @change="timRowAktifChange(row)">
                            </td>
                        </tr>

                        <!-- <sub-row-tim v-for="row in dataOperasi.tim_operasi" 
                            :rowData="row" 
                            :subTindakanLists = "subTindakanLists" 
                            :activeChange="actRowAktifChange">
                        </sub-row-tim> -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';
import SubRowTim from './SubRowTim.vue';

export default {
    name : 'sub-form-tim',
    props : {
        dataOperasi : { type:Object, default:null, required : false },
        dokterLists : { type:Object, default:null, required : false },
        isLoading : { type:Boolean, default:false, required : false },
    },
    components : { 
        SearchList, 
        SubRowTim,
    },
    data() {
        return {            
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            actDesc : [
                { colName : 'sub_tindakan_nama', labelData : '', isTitle : true },
                { colName : 'sub_tindakan_id', labelData : '', isTitle : false },
            ],
            addedTim : {
                dokter_id : null,
                dokter_nama : null,
                posisi : null,
                is_operator : false,
                tindakan_id : null,
                tindakan_nama : null,                
                paket : false,
            },

            subTindakanLists :[],
        }

    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',['isRefExist','jenisOperasiRefs','skalaOperasiRefs','timOperasiRefs',]), 
        ...mapGetters('operasi',['operasiData']),  
    }, 
    watch : {
        'operasiData.tindakan_operasi_id' : function(newVal, oldVal) {
            if(newVal) { this.retrieveSubTindakan(newVal); }
        },
    },

    mounted() {
        this.initialize();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']), 
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('operasi',['updateOperasi']),        
        ...mapActions('tindakan',['dataTindakan']),

        initialize(){
            this.$emit('ready',true);
            if(!this.isRefExist) { this.listReferensi({per_page:'ALL'}); }
        },

        retrieveSubTindakan(newData){
            if(newData){
                this.dataTindakan(newData).then((response) => {
                    if (response.success == true) { 
                        this.subTindakanLists = response.data.subTindakan;
                    }
                    else { 
                        alert(response.message); 
                    }
                })
            }
        },

        addTimOperasi(){
            //if(confirm(`Tim operasi akan ditambahkan. Lanjutkan ?`)){
                this.CLR_ERRORS();
                let exist = this.dataOperasi.tim_operasi.find(item => item.dokter_id == this.addedTim.dokter_id);
                if(!exist) {
                    this.dataOperasi.tim_operasi.push({
                        operasi_tim_id : null,
                        reg_id : null,
                        trx_id : null,
                        sub_trx_id : null,
                        dokter_id : this.addedTim.dokter_id,
                        dokter_nama : this.addedTim.dokter_nama,
                        posisi : this.addedTim.posisi,
                        is_operator : this.addedTim.is_operator,
                        is_aktif : true,
                    });

                    if(this.addedTim.is_operator) {
                        this.dataOperasi.dokter_id = this.addedTim.dokter_id;
                        this.dataOperasi.dokter_nama = this.addedTim.dokter_nama;
                    }

                    this.addedTim.dokter_id = null;
                    this.addedTim.dokter_nama = null;     
                    this.addedTim.posisi = null;
                    this.addedTim.is_operator = false;               
                }
                else { 
                    exist.is_aktif = true;
                    alert('dokter sudah didalam list tim operasi.');
                    this.addedTim.dokter_id = null;
                    this.addedTim.dokter_nama = null;
                    this.addedTim.posisi = null;
                    this.addedTim.is_operator = false;
                    
                }
            //}            
        },

        addedDokterSelected(data) {
            if(data) {
                this.addedTim.dokter_id = data.dokter_id;
                this.addedTim.dokter_nama = data.dokter_nama;                   
            }
        },

        addedActSelected(data) {
            if(data) {
                this.addedTim.tindakan_id = data.sub_tindakan_id;
                this.addedTim.tindakan_nama = data.sub_tindakan_nama;                   
            }
        },

        // rowActSelected(data){
        //     if(data) {
        //         this.addedTim.tindakan_id = data.sub_tindakan_id;
        //         this.addedTim.tindakan_nama = data.sub_tindakan_nama;                   
        //     }
        // },

        posisiChange(data) {
            let val = this.timOperasiRefs.json_data.find(item=> item.value == data.posisi);
            if(val) {
                data.is_operator = val.is_operator;
                // if(val.is_operator) {
                //     this.registrasi.dokter_id = data.dokter_id;
                //     this.registrasi.dokter_nama = data.dokter_nama;
                // }
            }
        },

        actRowAktifChange(data){
            if(data) {
                let items = this.dataOperasi.detail.filter(item => { return item.detail_id !== null || item.is_aktif == true });
                this.dataOperasi.detail = JSON.parse(JSON.stringify(items));
            }
        }
    }
}
</script>