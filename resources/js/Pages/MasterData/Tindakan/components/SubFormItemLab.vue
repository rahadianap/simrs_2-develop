<!-- <template>    
    <div class="hims-form-subpage">
        <div class="uk-card">
            <div class="uk-width-1-1" style="padding:0.5em;">
                <p style="color:#333; font-size:12px; padding:0;margin:0; font-style: italic; ">*item hasil pemeriksaan laboratorium akan langsung disimpan.</p>
            </div>
            <form class="uk-width-1-1" action="" @submit.prevent="saveData" style="padding:0;margin:0 0 1em 0;">
                <div class="uk-width-2-3@m uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;margin:0;">         
                    <div class="uk-grid-small uk-width-full" uk-grid style="margin:0;padding:0;">
                        <search-select
                            :config = configSelect
                            :searchUrl = urlPicker
                            placeholder = "pilih produk"
                            captionField = "hasil_nama"
                            valueField = "lab_hasil_id"
                            :itemListData = labDesc
                            :value = "addedItem.lab_hasil_id"
                            v-on:item-select = "itemSelected"
                        ></search-select>
                        <input class="uk-input uk-form-small uk-width-auto" type="text" placeholder="no urut" v-model="addedItem.no_urut" style="border:none;border-left:1px solid #ccc;margin:0;padding:0.2em 0 0 0;text-align:center;">
                        <button type="submit" class="uk-width-auto hims-table-btn-submit" style="border:none;border-top-right-radius: 5px;border-bottom-right-radius: 5px;">
                            <span uk-icon="plus-circle"></span> Tambah Item
                        </button>                        
                    </div>
                </div>
            </form>
            <div style="margin:0;padding:0;" class="uk-overflow-auto">
                <table class="uk-table hims-table uk-table-responsive">
                    <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                        <tr>
                            <th style="width:150px;">ID</th>
                            <th>NAMA</th>
                            <th>KLASIFIKASI</th>
                            <th>NO URUT</th>
                            <th style="text-align:center;">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="tindakan.labItems" v-for="dt in tindakan.labItems">
                            <td style="font-weight:500;width:150px;">{{dt.lab_hasil_id}}</td>
                            <td>{{dt.hasil_nama}}</td>
                            <td style="width:120px;" class="uk-text-uppercase">{{dt.klasifikasi}}</td>
                            <td style="width:80px;">{{dt.no_urut}}</td>
                            <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
                                <a href="#" uk-icon="icon:trash;ratio:0.8" @click.prevent="deleteData(dt)"></a>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="5">
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
    name : 'sub-form-item-lab',
    components : {
        SearchSelect,
    },
    data() {
        return {
            tindakan : {
                tindakan_id : null,
                tindakan_nama : null,
                unitTindakan : [],
                bhpTindakan : [],
                labItems : [],
            },
            addedItem : {
                lab_hasil_id : null,
                hasil_nama : null,
                klasifikasi : null,
                sub_klasifikasi : null,
                no_urut : null,
            },
            labDesc : [
                { colName : 'hasil_nama', labelData : '', isTitle : true },
                { colName : 'klasifikasi', labelData : '', isTitle : false },
                { colName : 'lab_hasil_id', labelData : '', isTitle : false },
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-expand",
                compStyle : "padding:0 0 0 0.2em;margin:0;border:none;",
                retrieveAll : false,
                qtyPerPage : 20,
            },
            urlPicker : '/labs/items/results'
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },  
    mounted() {
        this.initialize();
    },
    methods :{
        ...mapMutations(['CLR_ERRORS']), 
        ...mapActions('tindakan',['addLabTemplate','deleteLabTemplate']),
        
        initialize() {
            this.$emit('ready',true);
        },

        saveData(data){
            this.CLR_ERRORS();            
            let dt = { 
                tindakan_id : this.tindakan.tindakan_id,
                tindakan_nama : this.tindakan.tindakan_nama,  
                lab_hasil_id : this.addedItem.lab_hasil_id,
                hasil_nama : this.addedItem.hasil_nama,
                klasifikasi : this.addedItem.klasifikasi,
                sub_klasifikasi : this.addedItem.sub_klasifikasi,
                no_urut : this.addedItem.no_urut,                
            }
            this.addLabTemplate(dt).then((response) => {
                if (response.success == true) {
                    this.addedItem.lab_hasil_id = null;
                    this.addedItem.hasil_nama = null;                    
                    this.addedItem.klasifikasi = null;
                    this.addedItem.sub_klasifikasi = null;
                    this.addedItem.no_urut = null;                    
                    alert("Item hasil berhasil dipetakan.");
                    this.$emit('updated',this.tindakan);
                }
            }) 
        },

        refreshData(data){
            this.tindakan.tindakan_id = data.tindakan_id;
            this.tindakan.tindakan_nama = data.tindakan_nama;
            this.tindakan.labItems = data.labItems;
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus item hasil ${data.hasil_nama} dari mappping tindakan ${this.tindakan.tindakan_nama}. Lanjutkan ?`)){
                this.deleteLabTemplate(data.lab_template_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$emit('updated',data);
                    }
                    else { alert (response.message) }
                });
            };
        },

        itemSelected(data) {
            this.addedItem.lab_hasil_id = null;
            this.addedItem.hasil_nama = null;
            this.addedItem.klasifikasi = null;
            this.addedItem.sub_klasifikasi = null;
            this.addedItem.no_urut = null;
            if(data) {
                this.addedItem.lab_hasil_id = data.lab_hasil_id;
                this.addedItem.hasil_nama = data.hasil_nama;
                this.addedItem.klasifikasi = data.klasifikasi;
                this.addedItem.sub_klasifikasi = data.sub_klasifikasi;
                this.addedItem.no_urut = data.no_urut;
            }
        }
    }
}
</script> -->

<template>    
    <div class="hims-form-subpage">
        <div class="uk-card">
            <div class="uk-width-1-1" style="padding:0.5em;">
                <p style="color:#333; font-size:12px; padding:0;margin:0; font-style: italic; ">*item hasil pemeriksaan laboratorium akan langsung disimpan.</p>
            </div>
            <form class="uk-width-1-1" action="" @submit.prevent="saveData" style="padding:0;margin:0 0 1em 0;">
                <div class="uk-width-3-4@m uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;margin:0;">         
                    <div class="uk-grid-small uk-width-1-1@m" uk-grid style="margin:0;padding:0;">
                        <search-select
                            :config = configSelect
                            :searchUrl = urlPicker
                            placeholder = "pilih hasil pemeriksaan"
                            captionField = "hasil_nama"
                            valueField = "lab_hasil_id"
                            :itemListData = labDesc
                            :value = "addedItem.lab_hasil_id"
                            v-on:item-select = "itemSelected"
                        ></search-select>
                        <input class="uk-input uk-form-small uk-width-auto" type="text" placeholder="no urut" v-model="addedItem.no_urut" style="border:none;border-left:1px solid #ccc;margin:0;padding:0.2em 0 0 0;text-align:center;">
                        <button type="submit" class="uk-width-auto hims-table-btn-submit" style="border:none;border-top-right-radius: 5px;border-bottom-right-radius: 5px;">
                            <span uk-icon="plus-circle"></span> Tambah Item
                        </button>                        
                    </div>
                </div>
            </form>
            <div style="margin:0;padding:0;" class="uk-overflow-auto">
                <table class="uk-table hims-table uk-table-responsive">
                    <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                        <tr>
                            <th style="width:20px;"></th>
                            <th style="width:150px;">ID</th>
                            <th>NAMA</th>
                            <th>KLASIFIKASI</th>
                            <th>SUB KLASIFIKASI</th>
                            <th style="text-align:center;">NO URUT</th>
                            <th style="text-align:center;">...</th>
                            <th style="text-align:center;">NILAI NORMAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <row-item-lab  
                            v-if="tindakan.labItems" 
                            v-for="dt in tindakan.labItems"
                            :deleteCallback = "deleteHasil"
                            :updateCallback = "updateHasil"
                            :rowData = dt>
                        </row-item-lab>
                        <tr v-else>
                            <td colspan="5">
                                <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <edit-hasil-lab ref="modalEditHasilLab" v-on:modalEditHasilLabClosed="editHasilClosed"></edit-hasil-lab>
    </div>
</template> 

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import RowItemLab from '@/Pages/MasterData/Tindakan/components/RowItemLab.vue';
import EditHasilLab from '@/Pages/Penunjang/Laboratorium/MasterPemeriksaan/FormTindakanLab/EditHasilLab.vue';

export default {
    name : 'sub-form-item-lab',
    props : { 
        tindakan : { type : Object, required: false, default: null }, 
    },    
    components : { SearchSelect, RowItemLab, EditHasilLab,  },
    data() {
        return {
            addedItem : {
                lab_hasil_id : null,
                hasil_nama : null,
                klasifikasi : null,
                sub_klasifikasi : null,
                no_urut : null,
            },
            labDesc : [
                { colName : 'hasil_nama', labelData : '', isTitle : true },
                { colName : 'klasifikasi', labelData : '', isTitle : false },
                { colName : 'lab_hasil_id', labelData : '', isTitle : false },
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-expand",
                compStyle : "padding:0 0 0 0.2em;margin:0;border:none;",
                retrieveAll : false,
                qtyPerPage : 20,
            },
            urlPicker : '/labs/items/results'
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },  
    mounted() {
        this.initialize();
    },
    methods :{
        ...mapMutations(['CLR_ERRORS']), 
        ...mapActions('tindakan',['addLabTemplate','deleteLabTemplate']),
        
        initialize() {
            //this.$emit('ready',true);
        },

        saveData(data){
            this.CLR_ERRORS();            
            let dt = { 
                tindakan_id : this.tindakan.tindakan_id,
                tindakan_nama : this.tindakan.tindakan_nama,  
                lab_hasil_id : this.addedItem.lab_hasil_id,
                hasil_nama : this.addedItem.hasil_nama,
                klasifikasi : this.addedItem.klasifikasi,
                sub_klasifikasi : this.addedItem.sub_klasifikasi,
                no_urut : this.addedItem.no_urut,                
            }
            this.addLabTemplate(dt).then((response) => {
                if (response.success == true) {
                    this.addedItem.lab_hasil_id = null;
                    this.addedItem.hasil_nama = null;                    
                    this.addedItem.klasifikasi = null;
                    this.addedItem.sub_klasifikasi = null;
                    this.addedItem.no_urut = null;                    
                    alert("Item hasil berhasil dipetakan.");
                    this.$emit('updateItemLab',this.tindakan);
                }
            }) 
        },

        refreshData(data){
            this.tindakan.tindakan_id = data.tindakan_id;
            this.tindakan.tindakan_nama = data.tindakan_nama;
            this.tindakan.labItems = data.labItems;
        },

        deleteHasil(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus item hasil ${data.hasil_nama} dari mappping tindakan ${this.tindakan.tindakan_nama}. Lanjutkan ?`)){
                this.deleteLabTemplate(data.lab_template_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$emit('updateItemLab',this.tindakan);
                    }
                    else { alert (response.message) }
                });
            };
        },

        updateHasil(data) {            
            console.log(data);
            if(data) {
                this.$refs.modalEditHasilLab.editItemLab(data);
            }
        },

        itemSelected(data) {
            this.addedItem.lab_hasil_id = null;
            this.addedItem.hasil_nama = null;
            this.addedItem.klasifikasi = null;
            this.addedItem.sub_klasifikasi = null;
            this.addedItem.no_urut = null;
            if(data) {
                this.addedItem.lab_hasil_id = data.lab_hasil_id;
                this.addedItem.hasil_nama = data.hasil_nama;
                this.addedItem.klasifikasi = data.klasifikasi;
                this.addedItem.sub_klasifikasi = data.sub_klasifikasi;
                this.addedItem.no_urut = data.no_urut;
            }
        },

        editHasilClosed(){
            this.$emit('updateItemLab',true);
        }
    }
}
</script>
