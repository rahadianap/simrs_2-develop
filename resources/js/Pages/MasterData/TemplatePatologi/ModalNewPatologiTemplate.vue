<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalNewPatologiTemplate" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close @click.prevent=""></button>
            <div class="hims-form-container" style="padding:0; ">
                <form action="" @submit.prevent="submitTemplate">                        
                    <h3 class="uk-text-uppercase" style="font-weight:bold;">TEMPLATE HASIL PATOLOGI</h3>
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-1" style="border-top:1px solid #ccc;margin-top: 0.5em;margin-bottom: 0.5em;"></div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Template*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" type="text" v-model="patologiTemplate.pa_template_nama" style="color:black;" required>
                            </div>
                        </div>

                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="patologiTemplate.deskripsi" style="color:black;" required></textarea>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Hasil*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="patologiTemplate.hasil" style="color:black;" required></textarea>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="patologiTemplate.catatan" style="color:black;" required></textarea>
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
    name : 'modal-new-patologi-template',
    components : { SearchList, },
    data() {
        return {
            isUpdate : false,

            patologiTemplate : {
                pa_template_id : null,
                pa_template_nama : null,
                deskripsi : null,
                hasil : null,
                catatan : null,
                is_aktif : true,
            },

            subKlasifikasi : null,            
        }
    },

    computed : {
        ...mapGetters(['errors']),
        // ...mapGetters('kelompokTagihan',['groupBillingLists']),
        // ...mapGetters('kelompokVclaim',['groupVclaimLists']),
        // ...mapGetters('kelompokTindakan',['groupActLists']),
        // ...mapGetters('refPenunjang',['groupLabNormalRefs','satuanLabNormalRefs','klasifikasiItemLabRefs']),
    },
    mounted() {
        this.initialize();
    },

    methods : {        
        // ...mapActions('refPenunjang',['listRefPenunjang']),
        // ...mapActions('tindakan',['createTindakan','updateTindakan','dataTindakan']),
        // ...mapActions('tindakanLab',['createTindakanLab']),
        // ...mapActions('kelompokTagihan',['listKelompokTagihan']),
        // ...mapActions('kelompokVclaim',['listKelompokVclaim']),
        // ...mapActions('kelompokTindakan',['listKelompokTindakan']),

        ...mapActions('patologiTemplate',['dataPatologiTemplate','createPatologiTemplate','updatePatologiTemplate']),
        
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            let params = { is_aktif:true, per_page:'ALL' };
            //this.listRefPenunjang(params);
        },

        closeModal(){
            this.clearPatologiTemplate();
            this.$emit('modalNewPatologiTemplateClosed',true);
            UIkit.modal('#modalNewPatologiTemplate').hide();
        },
        
        newData(){
            this.clearPatologiTemplate();
            this.isUpdate = false;
            UIkit.modal('#modalNewPatologiTemplate').show(); 
        },

        editData(data){
            this.clearPatologiTemplate();
            if(data){
                this.dataPatologiTemplate(data.pa_template_id).then((response) => {
                    if (response.success == true) {
                        this.fillDataTemplate(response.data);
                        this.isUpdate = true;
                        UIkit.modal('#modalNewPatologiTemplate').show();
                    }
                    else {  alert (response.message);  }
                });
            }
        },

        fillDataTemplate(data){
            this.patologiTemplate.pa_template_id = data.pa_template_id;
            this.patologiTemplate.pa_template_nama = data.pa_template_nama;
            this.patologiTemplate.deskripsi = data.deskripsi;
            this.patologiTemplate.hasil = data.hasil;
            this.patologiTemplate.catatan = data.catatan;
            this.patologiTemplate.is_aktif = data.is_aktif;
        },

        showModal() { 
            this.clearPemeriksaan();
            UIkit.modal('#modalNewTindakan').show(); 
        },

        clearPatologiTemplate(){
            this.patologiTemplate.pa_template_id = null;
            this.patologiTemplate.pa_template_nama = null;
            this.patologiTemplate.deskripsi = null;
            this.patologiTemplate.hasil = null;
            this.patologiTemplate.catatan = null;
            this.patologiTemplate.is_aktif = true;
        },


        submitTemplate(){ 
            this.CLR_ERRORS();
            if(this.isUpdate) {
                this.updatePatologiTemplate(this.patologiTemplate).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.clearPatologiTemplate();
                        this.closeModal();
                        UIkit.modal('#modalNewPatologiTemplate').hide();
                    }
                    else {  alert (response.message);  }
                });
            }
            else {
                this.createPatologiTemplate(this.patologiTemplate).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.clearPatologiTemplate();
                        this.closeModal();
                        UIkit.modal('#modalNewPatologiTemplate').hide();
                    }
                    else {  alert (response.message);  }
                });
            }
        }
    }
}
</script>