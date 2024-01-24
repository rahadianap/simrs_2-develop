<template>
    <div style="padding:1em;margin:0;background-color: #fff;">
        <form action="" @submit.prevent="submitBridging">
            <div class="uk-container-small">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-1">
                        <h5  style="font-weight:500;margin:0;padding:0;">{{ bridging.nama_lokal }}</h5>
                        <p style="font-size:11px;margin:0;padding:0;">{{ bridging.kode_lokal }}</p>
                    </div>
                    <div class="uk-width-1-4@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Bridging*</label>
                        <div class="uk-form-controls">
                            <select class="uk-select uk-form-small" v-model="bridging.bridging_group" required style="color:black;">
                                <option value="BPJS">BPJS</option>
                                <option value="JKN">JKN ANTRIAN</option>
                            </select>
                        </div>
                    </div>
                    <div class="uk-width-1-2@m" style="padding-top:1.5em;">
                        <button type="button" @click.prevent="showPickerPoli" style="height:2.2em;">Pilih Kode Bridging</button>
                    </div>
                    <div class="uk-width-1-2@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kode Bridging*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" v-model="bridging.kode_bridging" type="text" required style="color:black;">
                        </div>
                    </div> 
                    <div class="uk-width-1-2@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Bridging*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" v-model="bridging.nama_bridging" type="text" required style="color:black;">
                        </div>
                    </div> 
                    <div class="uk-width-1-2@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Sub Kode Bridging</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" v-model="bridging.sub_kode_bridging" type="text" required style="color:black;">
                        </div>
                    </div> 
                    <div class="uk-width-1-2@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Sub Nama Bridging</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" v-model="bridging.sub_nama_bridging" type="text" required style="color:black;">
                        </div>
                    </div> 
                    <div class="uk-width-1-2@m">
                        <button type="submit">SIMPAN</button>
                    </div>
                </div>
            </div>
        </form>
        <poli-picker ref="poliPicker" 
            v-on:closed="refreshTable" 
            v-on:saveSucceed="refreshTable" 
            :fieldDatas = pickerColumns
            :proceedFunction = "selectedItem">
        </poli-picker>
    </div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';
import PoliPicker from '@/Pages/BridgingBpjs/UnitPelayanan/PoliPicker.vue';

export default {
    name : 'form-map-poli',
    props : {
        refData :{ type:Object, required:true },
    },
    components : {
        SearchList, PoliPicker,
    },
    watch : {
        'data' : function(newVal, oldVal){
            this.initialize();
        },
    },
    data() {
        return {
            isLoading : true,
            listItems : null,
            pickerJknColumns : [
                { name : 'nmpoli', title : 'Nama', colType : 'text', isCaption : false, },
                { name : 'kdpoli', title : 'Kode', colType : 'text', isCaption : true, },
                { name : 'nmsubspesialis', title : 'Nama Subspesialis', colType : 'text', isCaption : false, },
                { name : 'kdsubspesialis', title : 'Kode Subspesialis', colType : 'text', isCaption : false, },
            ],
            pickerBpjsColumns : [
                { name : 'kode', title : 'Kode', colType : 'text', isCaption : false, },
                { name : 'nama', title : 'Nama', colType : 'text', isCaption : true, },
            ],

            bridging : {
                bridging_id : null,
                bridging_group : null,
                kode_lokal : null,
                nama_lokal : null,
                kode_bridging : null,
                nama_bridging : null,
                sub_kode_bridging : null,
                sub_nama_bridging : null,
            },

            pickerColumns : [
                { name : 'kode', title : 'Kode', colType : 'text', isCaption : false, },
                { name : 'nama', title : 'Nama', colType : 'text', isCaption : true, },
            ],
            listItems : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
        this.initialize();
    },
    methods : {
        ...mapActions('bpjsReferensi',['refBpjsPoli','updateBpjsMapping','removeBpjsMapping']),
        ...mapActions('jknAntrian',['jknRefPoli']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            this.bridging.kode_lokal = this.refData.unit_id;
            this.bridging.nama_lokal = this.refData.unit_nama;            
        },

        refreshTable(){

        },

        clearData(){
            this.bridging.kode_lokal = this.refData.unit_id;
            this.bridging.nama_lokal = this.refData.unit_nama;
            
            this.bridging.bridging_id = null;
            this.bridging.kode_bridging = null;
            this.bridging.nama_bridging = null;
            this.bridging.sub_kode_bridging = null;
            this.bridging.sub_nama_bridging = null;
        },

        showPickerPoli(){
            this.CLR_ERRORS();
            this.clearData();
            if(this.bridging.bridging_group == 'BPJS') {
                this.pickerColumns = this.pickerBpjsColumns;
                this.$refs.poliPicker.showModalPicker(this.bridging.bridging_group);
            }
            if(this.bridging.bridging_group == 'JKN') {
                this.pickerColumns = this.pickerJknColumns;
                this.$refs.poliPicker.showModalPicker(this.bridging.bridging_group);
            }            
        },

        selectedItem(data) {
            if(this.bridging.bridging_group == 'BPJS') {
                this.bridging.kode_bridging = data.kode;
                this.bridging.nama_bridging = data.nama;
                this.$refs.poliPicker.closeModalPicker();
            }   
            else if(this.bridging.bridging_group == 'JKN'){
                this.bridging.kode_bridging = data.kdpoli;
                this.bridging.nama_bridging = data.nmpoli;
                this.bridging.sub_kode_bridging = data.kdsubspesialis;
                this.bridging.sub_nama_bridging = data.nmsubspesialis;
                this.$refs.poliPicker.closeModalPicker();
            }
        },

        submitBridging(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengubah kode bridging poli (unit pelayanan). Lanjutkan ?`)){
                this.updateBpjsMapping(this.bridging).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$emit('proceed',true);
                        this.clearData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>