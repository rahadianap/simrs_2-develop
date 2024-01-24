<template>
    <div class="hims-form-subpage">
        <div class="uk-card uk-card-default" style="padding:1em;margin:0;">
            <p style="color:#333; font-size:12px; padding:0;margin:0; font-style: italic; ">
                *Penambahan dan pengurangan data depo persediaan akan langsung disimpan.
            </p>
            <form action="" @submit.prevent="saveData" class="uk-grid-small" uk-grid>                
                <div class="uk-width-expand@m"  style="margin-top:2px;">
                    <search-list
                        :config = configSelect
                        :dataLists = depoLists.data
                        label = ""
                        placeholder = "pilih depo persediaan"
                        captionField = "depo_nama"
                        valueField = "depo_nama"
                        :detailItems = depoDesc
                        :value = "depo.depo_nama"
                        v-on:item-select = "depoSelected"
                    ></search-list>
                </div>
                <div class="uk-width-auto@m" style="margin-top:2px;">
                    <div class="uk-form-controls">
                        <label style="padding:0;margin:0;font-size:12px;">
                            <input class="uk-checkbox" type="checkbox" v-model="depo.is_depo_utama" style="margin-left:15px;"> Depo utama
                        </label>
                    </div>
                </div>
                <div class="uk-width-auto@m">
                    <button type="submit" class="uk-button uk-button-small" style="border:2px solid #cc0202; color:white; background-color:#cc0202;">Simpan</button>
                    <button type="button" @click.prevent="clearDepo" class="uk-button uk-button-small" style="border:none; background-color:#ffffff;">Batal</button>
                </div>                
            </form>
        </div>
        <div class="uk-card" style="padding:1em 0 1em 0;margin:0.5em 0 0 0;min-height:200px;">
            <table class="uk-table uk-table-striped">
                <thead class="uk-card uk-card-default uk-box-shadow-medium" style="border-top:2px solid #cc0202;">
                    <tr>
                        <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; width:180px;">ID</th>
                        <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; ">Nama Depo</th>
                        <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; width:120px;">Depo Utama</th>
                        <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; width:100px;text-align:center;">...</th>  
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="depo in depos">
                        <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;font-weight:500;width:180px;">{{depo.depo_id}}</td>
                        <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;">{{depo.depo_nama}}</td>
                        <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;text-align:center;">
                            <input class="uk-checkbox" type="checkbox" v-model="depo.is_depo_utama" disabled>
                        </td>
                        <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
                            <a href="#" uk-icon="icon:trash;" @click.prevent="deleteData(depo)"></a>
                            <a href="#" uk-icon="icon:pencil;" style="margin-left:1em;" @click.prevent="editData(depo)"></a>
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
    name : 'sub-form-depo',
    props : {
        unit : { type:Object , required:true },
        depos : { type:Object, required: false, default:[] }
    },
    components : {
        SearchList,  
    },
    data() {
        return {
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            depoDesc : [
                { colName : 'depo_nama', labelData : '', isTitle : true },
                { colName : 'depo_id', labelData : '', isTitle : false },
            ],
            depo : {
                depo_unit_id : null,
                depo_id : null,
                depo_nama : null,
                is_depo_utama : false,
                unit_id : this.unit.unit_id,    
            },
            isUpdate : true,     
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('depo',['depoLists']),
        ...mapGetters('unitPelayanan',['depoUnitLists']),
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapActions('depo',['listDepo']),
        ...mapActions('unitPelayanan',['addUnitDepo','deleteUnitDepo','listUnitDepo','updateUnitDepo']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            let param = { per_page:'ALL' };
            this.listDepo(param);
            this.isUpdate = false;
        },

        depoSelected(data){
            this.depo.depo_id = null;
            this.depo.depo_nama = null;
            if(data){
                this.depo.depo_id = data.depo_id;
                this.depo.depo_nama = data.depo_nama;
            }
        },

        editData(data){
            this.depo.depo_unit_id = data.depo_unit_id;
            this.depo.depo_id = data.depo_id;
            this.depo.depo_nama = data.depo_nama;
            this.depo.is_depo_utama = data.is_depo_utama; 
            this.depo.unit_id = this.unit.unit_id;
            this.isUpdate = true;   
            this.configSelect.disabled = true;
        },

        saveData(){
            this.CLR_ERRORS();
            this.depo.unit_id = this.unit.unit_id;
            if(this.isUpdate) {
                this.updateUnitDepo(this.depo).then((response) => {
                    if (response.success == true) {
                        alert("Depo unit berhasil diubah.");
                        this.$emit('updated',true);
                        this.clearDepo();
                        this.isUpdate = false;
                    }
                    else {
                        alert(response.message);
                    }
                })
            }
            else {
                this.addUnitDepo(this.depo).then((response) => {
                    if (response.success == true) {
                        alert("Depo unit berhasil ditambahkan.");
                        this.$emit('updated',true);
                        this.clearDepo();
                        this.isUpdate = false;
                        this.configSelect.disabled = false;
                    }
                    else {
                        alert(response.message);
                    }
                })
            }            
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus depo ${data.depo_nama} dari unit ${this.unit.unit_nama}. Lanjutkan ?`)){
                this.deleteUnitDepo(data.depo_unit_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$emit('updated',true);
                        this.clearDepo();
                    }
                    else { alert (response.message) }
                });
            };
        },

        clearDepo(){
            this.depo.depo_unit_id = null;
            this.depo.depo_id = null;
            this.depo.unit_id = this.unit.unit_id;
            this.depo.depo_nama = null;
            this.depo.is_depo_utama = false;
            this.isUpdate = false;
            this.configSelect.disabled = false;
        }

    }
}

</script>
<style>

</style>