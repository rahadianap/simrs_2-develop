<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalNewRadTemplate" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close @click.prevent=""></button>
            <div class="hims-form-container" style="padding:0; ">
                <form action="" @submit.prevent="submitRadTemplate">                        
                    <h3 class="uk-text-uppercase" style="font-weight:bold;">TEMPLATE HASIL RADIOLOGI</h3>
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-1" style="border-top:1px solid #ccc;margin-top: 0.5em;margin-bottom: 0.5em;"></div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Template*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" type="text" v-model="radTemplate.rad_template_nama" style="color:black;" required>
                            </div>
                        </div>

                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="radTemplate.deskripsi" style="color:black;" required></textarea>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kesan*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="radTemplate.kesan" style="color:black;" required></textarea>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Uraian*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="radTemplate.uraian" style="color:black;" required></textarea>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="radTemplate.catatan" style="color:black;" required></textarea>
                            </div>
                        </div>                  
                        <div class="uk-width-1-1" style="text-align:right;margin-top:1.5em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>
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

export default {
    name : 'modal-new-rad-template',
    components : { SearchList, },
    data() {
        return {
            isUpdate : false,
            isLab : false,
            isKamar : false,
            
            configSelect2 : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            billingDesc : [
                { colName : 'kelompok_tagihan', labelData : '', isTitle : true },
                { colName : 'kelompok_tagihan_id', labelData : '', isTitle : false },
            ],

            vclaimDesc : [
                { colName : 'vclaim_label', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
                { colName : 'kelompok_vclaim_id', labelData : '', isTitle : false },
            ],

            radTemplate : {
                rad_template_id : null,
                rad_template_nama : null,
                deskripsi : null,
                kesan : null,
                uraian : null,
                catatan : null,
                is_aktif : true,
            },

            subKlasifikasi : null,            
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelompokTagihan',['groupBillingLists']),
        ...mapGetters('kelompokVclaim',['groupVclaimLists']),
        ...mapGetters('kelompokTindakan',['groupActLists']),
        ...mapGetters('refPenunjang',['groupLabNormalRefs','satuanLabNormalRefs','klasifikasiItemLabRefs']),
    },
    mounted() {
        this.initialize();
    },

    methods : {        
        ...mapActions('refPenunjang',['listRefPenunjang']),
        ...mapActions('tindakan',['createTindakan','updateTindakan','dataTindakan']),
        ...mapActions('tindakanLab',['createTindakanLab']),
        ...mapActions('kelompokTagihan',['listKelompokTagihan']),
        ...mapActions('kelompokVclaim',['listKelompokVclaim']),
        ...mapActions('kelompokTindakan',['listKelompokTindakan']),

        ...mapActions('radTemplate',['dataRadTemplate','createRadTemplate','updateRadTemplate']),
        
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            let params = { is_aktif:true, per_page:'ALL' };
            this.listRefPenunjang(params);
        },

        closeModal(){
            this.clearRadTemplate();
            this.$emit('modalNewRadTemplateClosed',true);
            UIkit.modal('#modalNewRadTemplate').hide();
        },
        
        newData(){
            this.clearRadTemplate();
            this.isUpdate = false;
            UIkit.modal('#modalNewRadTemplate').show(); 
        },

        editData(data){
            this.clearRadTemplate();
            if(data){
                this.dataRadTemplate(data.rad_template_id).then((response) => {
                    if (response.success == true) {
                        this.fillDataTemplate(response.data);
                        this.isUpdate = true;
                        UIkit.modal('#modalNewRadTemplate').show();
                    }
                    else {  alert (response.message);  }
                });
            }
        },

        fillDataTemplate(data){
            this.radTemplate.rad_template_id = data.rad_template_id;
            this.radTemplate.rad_template_nama = data.rad_template_nama;
            this.radTemplate.deskripsi = data.deskripsi;
            this.radTemplate.uraian = data.uraian;
            this.radTemplate.kesan = data.kesan;
            this.radTemplate.catatan = data.catatan;
            this.radTemplate.is_aktif = data.is_aktif;
        },

        showModal() { 
            this.clearPemeriksaan();
            UIkit.modal('#modalNewTindakan').show(); 
        },

        clearRadTemplate(){
            this.radTemplate.rad_template_id = null;
            this.radTemplate.rad_template_nama = null;
            this.radTemplate.deskripsi = null;
            this.radTemplate.uraian = null;
            this.radTemplate.kesan = null;
            this.radTemplate.catatan = null;
            this.radTemplate.is_aktif = true;
        },


        submitRadTemplate(){ 
            this.CLR_ERRORS();
            if(this.isUpdate) {
                this.updateRadTemplate(this.radTemplate).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.clearRadTemplate();
                        this.closeModal();
                        UIkit.modal('#modalNewRadTemplate').hide();
                    }
                    else {  alert (response.message);  }
                });
            }
            else {
                this.createRadTemplate(this.radTemplate).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.clearRadTemplate();
                        this.closeModal();
                        UIkit.modal('#modalNewRadTemplate').hide();
                    }
                    else {  alert (response.message);  }
                });
            }
        }
    }
}
</script>