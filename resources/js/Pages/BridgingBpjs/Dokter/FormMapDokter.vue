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
                    <div class="uk-width-1-1"></div>
                    <div class="uk-width-1-4@m">
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
                    <div class="uk-width-1-4@m" style="padding-top:1.5em;">
                        <button type="button" @click.prevent="showPickerDokter" style="height:2.2em;">Pilih Kode Bridging</button>
                    </div>
                    <div class="uk-width-1-2@m">
                        <button type="submit"  style="height:2.2em;">SIMPAN</button>
                    </div>
                </div>
            </div>
        </form>
        <div>
            <table>

            </table>
        </div>
        <dokter-picker ref="dokterPicker" 
            v-on:closed="refreshTable" 
            v-on:saveSucceed="refreshTable" 
            :fieldDatas = pickerColumns
            :proceedFunction = "selectedItem">
        </dokter-picker>
    </div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';
import DokterPicker from '@/Pages/BridgingBpjs/Dokter/DokterPicker.vue';

export default {
    name : 'form-map-dokter',
    props : {
        refData :{ type:Object, required:true },
    },
    components : {
        SearchList, DokterPicker,
    },
    watch : {
        'refData' : function(newVal, oldVal){
            this.initialize();
        },
    },
    data() {
        return {
            isLoading : true,
            listItems : null,
            jknDokterSelected : null,
            pickerJknColumns : [
                { name : 'namadokter', title : 'Nama', colType : 'text', isCaption : false, },
                { name : 'kodedokter', title : 'Kode', colType : 'text', isCaption : true, },
            ],
            pickerBpjsColumns : [
                { name : 'kode', title : 'Kode', colType : 'text', isCaption : false, },
                { name : 'nama', title : 'Nama', colType : 'text', isCaption : true, },
            ],

            bridging : {
                bridging_id : null,
                bridging_group : 'JKN',
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
        ...mapGetters('jknAntrian',['dokterRefs']),
    },

    mounted() {
        this.initialize();
    },
    methods : {
        ...mapActions('bpjsReferensi',['refBpjsPoli','updateBpjsMapping','removeBpjsMapping']),
        ...mapActions('jknAntrian',['jknRefPoli']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            this.bridging.kode_lokal = this.refData.dokter_id;
            this.bridging.nama_lokal = this.refData.dokter_nama; 
        },

        refreshTable(){

        },

        clearData(){
            this.bridging.kode_lokal = this.refData.dokter_id;
            this.bridging.nama_lokal = this.refData.dokter_nama;
            
            this.bridging.bridging_id = null;
            this.bridging.kode_bridging = null;
            this.bridging.nama_bridging = null;
            this.bridging.sub_kode_bridging = null;
            this.bridging.sub_nama_bridging = null;
        },

        retrieveListDokter(){
            if(this.bridging.bridging_group == 'JKN') {
                
            }
        },

        showPickerDokter(){
            this.CLR_ERRORS();
            this.clearData();
            if(this.bridging.bridging_group == 'BPJS') {
                this.pickerColumns = this.pickerBpjsColumns;
                this.$refs.dokterPicker.showModalPicker(this.bridging.bridging_group, this.refData.nama_lokal , null);
            }
            if(this.bridging.bridging_group == 'JKN') {
                this.pickerColumns = this.pickerJknColumns;
                this.$refs.dokterPicker.showModalPicker(this.bridging.bridging_group, this.refData.nama_lokal , this.dokterRefs);
            }            
        },

        selectedItem(data) {
            // this.bridging.kode_bridging = data.kodedokter;
            // this.bridging.nama_bridging = data.namadokter;
            // this.$refs.dokterPicker.closeModalPicker();
            this.bridging.kode_lokal = this.refData.dokter_id;
            this.bridging.nama_lokal = this.refData.dokter_nama; 

            if(this.bridging.bridging_group == 'BPJS') {
                this.bridging.kode_bridging = data.kode;
                this.bridging.nama_bridging = data.nama;
                this.$refs.dokterPicker.closeModalPicker();
            }   
            else if(this.bridging.bridging_group == 'JKN'){
                this.bridging.kode_bridging = data.kodedokter;
                this.bridging.nama_bridging = data.namadokter;
                this.$refs.dokterPicker.closeModalPicker();
            }
        },

        submitBridging(){
            console.log(this.bridging);
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengubah kode bridging dokter. Lanjutkan ?`)){
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