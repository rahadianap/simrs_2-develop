<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalHasilPa" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close @click.prevent=""></button>
            <div class="hims-form-container" style="padding:0; ">
                <form action="" @submit.prevent="submitHasilRad">                        
                    <h3 class="uk-text-uppercase" style="font-weight:bold;">Hasil Radiologi</h3>
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-1" style="border-top:1px solid #ccc;margin-top: 0.5em;margin-bottom: 0.5em;"></div>

                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pemeriksaan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="pemeriksaan.tindakan_nama" style="color:black;" required disabled>
                            </div>
                        </div>

                        
                        <div class="uk-width-1-1@m">
                            <div class="uk-form-controls">
                                <search-select
                                    :config = configSelect
                                    :searchUrl = dokterUrl
                                    label = "Dokter Patologi"
                                    placeholder = "pilih dokter patologi"
                                    captionField = "dokter_nama"
                                    valueField = "dokter_nama"
                                    :itemListData = dokterDesc
                                    :value = "pemeriksaan.dokter_nama"
                                    v-on:item-select = "dokterSelected"
                                ></search-select>
                            </div>
                        </div>

                        <div class="uk-width-1-1@m">
                            <div class="uk-form-controls">
                                <search-select
                                    :config = configSelect
                                    searchUrl = '/patologi/templates'
                                    label = "template hasil"
                                    placeholder = "pilih template hasil"
                                    captionField = "pa_template_nama"
                                    valueField = "pa_template_nama"
                                    :itemListData = paTemplateDesc
                                    :value = "paTemplateSelected"
                                    v-on:item-select = "templateSelected"
                                ></search-select>
                            </div>
                        </div>

                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Uraian Hasil*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="pemeriksaan.hasil" type="text" placeholder="uraian hasil" required></textarea>
                            </div>
                        </div> 
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="pemeriksaan.catatan" type="text" placeholder="catatan"></textarea>
                            </div>
                        </div> 

                        <div class="uk-width-1-1" style="text-align:right;margin-top:1.5em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">OK</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchList from '@/Components/SearchList.vue';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'modal-hasil-rad',
    components : { SearchList, SearchSelect },
    data() {
        return {
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],
            paTemplateDesc : [
                { colName : 'pa_template_nama', labelData : '', isTitle : true },
                { colName : 'pa_template_id', labelData : '', isTitle : false },
            ],

            paTemplateSelected : null,
            pemeriksaan : {
                detail_id : null,
                reg_id : null,
                trx_id : null,
                reff_trx_id : null,
                tindakan_id : null,
                tindakan_nama : null,
                hasil : null,
                catatan : null,
                dokter_id : null,
                dokter_nama : null,
                is_aktif : true,    
            },           
            dokterUrl : '/doctors',
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refPenunjang',['isRefExist','jenisFotoRefs']),
    },
    mounted() {
        this.initialize();
    },

    methods : {        
        ...mapActions('refPenunjang',['listRefPenunjang']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            let params = { is_aktif:true, per_page:'ALL' };
            this.listRefPenunjang(params);
        },

        modalHasilPaClosed(){
            this.clearPemeriksaan();
            this.$emit('modalHasilPaClosed',true);
            UIkit.modal('#modalHasilPa').hide();
        },

        showModal(data) { 
            if(data){
                this.clearPemeriksaan();
                this.fillPemeriksaan(data);
                UIkit.modal('#modalHasilPa').show(); 
            }
        },

        templateSelected(data) {
            if(data) {
                this.paTemplateSelected = data.pa_template_nama;
                this.pemeriksaan.hasil  = data.hasil;
                this.pemeriksaan.catatan = data.catatan;
            }  
        },

        dokterSelected(data){
            if(data) { 
                this.pemeriksaan.dokter_id = data.dokter_id; 
                this.pemeriksaan.dokter_nama = data.dokter_nama; 
            }
        },

        clearPemeriksaan(){
            this.pemeriksaan.detail_id = null;
            this.pemeriksaan.reg_id = null;
            this.pemeriksaan.trx_id = null;
            this.pemeriksaan.reff_trx_id = null;
            this.pemeriksaan.tindakan_id = null;
            this.pemeriksaan.tindakan_nama = null;
            this.pemeriksaan.hasil = null;
            this.pemeriksaan.catatan = null;
            this.pemeriksaan.dokter_id = null;
            this.pemeriksaan.dokter_nama = null;
            this.pemeriksaan.is_aktif = true; 
        },

        fillPemeriksaan(data) {
            if(data) {
                this.pemeriksaan.detail_id = data.detail_id;
                this.pemeriksaan.reg_id = data.reg_id;
                this.pemeriksaan.trx_id = data.trx_id;
                this.pemeriksaan.reff_trx_id = data.reff_trx_id;
                this.pemeriksaan.tindakan_id = data.tindakan_id;
                this.pemeriksaan.tindakan_nama = data.tindakan_nama;
                this.pemeriksaan.hasil = data.hasil;
                this.pemeriksaan.catatan = data.catatan;
                this.pemeriksaan.dokter_id = data.dokter_id;
                this.pemeriksaan.dokter_nama = data.dokter_nama;
                this.pemeriksaan.is_aktif = data.is_aktif; 
            }
        },

        submitHasilRad(){            
            this.CLR_ERRORS();
            this.$emit('hasilPaUpdated',this.pemeriksaan);
            UIkit.modal('#modalHasilPa').hide();
        }
    }
}
</script>