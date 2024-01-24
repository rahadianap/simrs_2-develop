<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalPractitioner" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitKomponenHarga" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">IHS PRACTITIONER DATA</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>          
                        
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0.5em;">                                 
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID IHS</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="ihs_data.id" type="text" disabled>
                            </div>
                        </div>
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="ihs_data.nik" type="text" disabled>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Name</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="ihs_data.name" type="text" disabled>
                            </div>
                        </div>  
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Birth Date</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="ihs_data.birth_date" type="text" disabled>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Gender</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="ihs_data.gender" type="text" disabled>
                            </div>
                        </div>        
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'modal-practitioner', 
    data() {
        return {
            isUpdate : true,
            ihs_data : {
                id : null,
                nik : null,
                name : null,
                birth_date : null,
                gender : null
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('komponenHarga',['createKomponenHarga','dataKomponenHarga','updateKomponenHarga']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#modalPractitioner').hide();
        },   

        clearKomponenHarga(){
            this.komponen.client_id = null;
            this.komponen.komp_harga_id = null;
            this.komponen.komp_harga = null;
            this.komponen.deskripsi = null;
            this.komponen.is_jasa_dokter = false;
            this.komponen.is_hitung_pajak = false;
            this.komponen.is_auto_hitung = false;
            this.komponen.jenis_pajak = null;
            this.komponen.is_aktif = true;
        },

        fillKomponenHarga(data){
            console.log(data);
            this.ihs_data.id = data[0].ihs_number;
            this.ihs_data.nik = data[0].nik;
            this.ihs_data.name = data[0].name;
            this.ihs_data.birth_date = data[0].birth_date;
            this.ihs_data.gender = data[0].gender;
        },
    }
}
</script>