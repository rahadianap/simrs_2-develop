<template>
    <div class="hims-form-subpage">
        <div class="uk-card uk-card-default" style="padding:1em;margin:0;">
            <p style="color:#333; font-size:12px; padding:0;margin:0; font-style: italic; ">*Penambahan dan pengurangan data dokter akan langsung disimpan.</p>
            <form action="" @submit.prevent="saveData" class="uk-grid-small" uk-grid>                
                <div class="uk-width-expand@m" style="margin-top:2px;">
                    <search-list
                        :config = configSelect
                        :dataLists = dokterLists
                        label = ""
                        placeholder = "pilih dokter dan spesialis"
                        captionField = "dokter_nama"
                        valueField = "dokter_id"
                        :detailItems = dokDesc
                        :value = "dokter.dokter_id"
                        v-on:item-select = "dokterSelected"
                    ></search-list>
                </div>
                <div class="uk-width-auto@m" style="margin-top:2px;">
                    <div class="uk-form-controls">
                        <input class="uk-input uk-form-small" style="text-align:center;" required v-model="dokter.prefix_no_antrian" type="text" placeholder="kode antrian">
                    </div>
                </div>
                <div class="uk-width-auto@m">
                    <button type="submit" class="uk-button uk-button-small" style="border:2px solid #cc0202; color:white; background-color:#cc0202;">Simpan</button>
                    <button type="button" @click.prevent="clearDokter" class="uk-button uk-button-small" style="border:none; background-color:#ffffff;">Batal</button>
                </div>                
            </form>
        </div>
        <div class="uk-card" style="padding:1em 0 1em 0;margin:0.5em 0 0 0;min-height:200px;">
            <table class="uk-table uk-table-striped">
                <thead class="uk-card uk-card-default uk-box-shadow-medium" style="border-top:2px solid #cc0202;">
                    <tr>
                        <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; width:150px;">ID</th>
                        <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; ">Nama Dokter</th>
                        <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; width:120px;">Kode Antrian</th>
                        <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; width:100px;text-align:center;">...</th>  
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="doctors" v-for="dok in doctors">
                        <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;font-weight:500;width:150px;">{{dok.dokter_id}}</td>
                        <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;">{{dok.dokter_nama}}</td>
                        <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;text-align:center;">{{dok.prefix_no_antrian}}</td>
                        <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
                            <a href="#" uk-icon="icon:trash;" @click.prevent="deleteData(dok)"></a>
                            <a href="#" uk-icon="icon:pencil;" style="margin-left:1em;" @click.prevent="editData(dok)"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'sub-form-dokter',
    props : {
        unit : { type:Object , required:true },
        doctors : { type:Object, required: false, default:[] }
    },
    components : {
        SearchList,   
    },
    data() {
        return {
            columns : [                
                { 
                    name : 'dokter_nama', 
                    title : 'Dokter', 
                    colType : 'text',
                    isCaption : true,
                },
                { 
                    name : 'smf.smf_nama', 
                    title : 'SMF', 
                    colType : 'text',
                    isCaption : false
                },
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            dokDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],

            dokter : {
                dokter_unit_id : null,
                dokter_id : null,
                dokter_nama : null,
                prefix_no_antrian : '',
                unit_id : this.unit.unit_id,    
            },
            isUpdate : true,
            dokterLists : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('dokter',['doctorLists']),
        ...mapGetters('unitPelayanan',['doctorUnitLists']),
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapActions('dokter',['listDokter']),
        ...mapActions('unitPelayanan',['addUnitDokter','deleteUnitDokter','listUnitDokter','updateUnitDokter']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            let param = { per_page:'ALL' };
            this.listDokter(param).then((response) => {
                if (response.success == true) {
                    console.log(response.data.data);
                    this.dokterLists = response.data.data;
                }
            });
            this.isUpdate = false;
        },

        dokterSelected(data){
            this.dokter.dokter_id = null;
            this.dokter.dokter_nama = null;
            if(data){
                this.dokter.dokter_id = data.dokter_id;
                this.dokter.dokter_nama = data.dokter_nama;
            }
        },

        editData(data){
            this.dokter.dokter_unit_id = data.dokter_unit_id;
            this.dokter.dokter_id = data.dokter_id;
            this.dokter.dokter_nama = data.dokter_nama;
            this.dokter.prefix_no_antrian = data.prefix_no_antrian; 
            this.dokter.unit_id = this.unit.unit_id;
            this.isUpdate = true;   
            this.configSelect.disabled = true;
        },

        saveData(){
            this.CLR_ERRORS();
            this.dokter.unit_id = this.unit.unit_id;
            if(this.isUpdate) {
                this.updateUnitDokter(this.dokter).then((response) => {
                    if (response.success == true) {
                        alert("Dokter unit berhasil diubah.");
                        this.$emit('updated',true);
                        this.clearDokter();
                        this.isUpdate = false;
                    }
                })
            }
            else {
                this.addUnitDokter(this.dokter).then((response) => {
                    if (response.success == true) {
                        alert("Dokter unit berhasil ditambahkan.");
                        this.$emit('updated',true);
                        this.clearDokter();
                        this.isUpdate = false;
                        this.configSelect.disabled = false;
                    }
                })
            }            
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus dokter ${data.dokter_nama} dari unit ${this.unit.unit_nama}. Lanjutkan ?`)){
                this.deleteUnitDokter(data.dokter_unit_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$emit('updated',true);
                        this.clearDokter();
                    }
                    else { alert (response.message) }
                });
            };
        },

        clearDokter(){
            this.dokter.dokter_unit_id = null;
            this.dokter.dokter_id = null;
            this.dokter.unit_id = this.unit.unit_id;
            this.dokter.dokter_nama = null;
            this.dokter.prefix_no_antrian = null;
            this.isUpdate = false;
            this.configSelect.disabled = false;
        }

    }
}

</script>
<style>

</style>