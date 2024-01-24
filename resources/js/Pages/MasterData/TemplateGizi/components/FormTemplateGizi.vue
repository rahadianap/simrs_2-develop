<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formTemplateGizi" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitTemplateGizi" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA TEMPLATE GIZI</h5>
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
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Template*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="gizi.nama_template" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Dibuat*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="gizi.tgl_dibuat" type="date" required :disabled="isUpdate">
                            </div>
                        </div>
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Berlaku*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="gizi.tgl_berlaku" required type="date">
                            </div>
                        </div>
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jumlah Hari</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="gizi.jml_hari" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea" v-model="gizi.catatan" type="text" required>{{gizi.catatan}}</textarea>
                            </div>
                        </div>   
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="gizi.is_aktif" style="margin-left:5px;"> Data template gizi aktif</label>
                        </div>                     
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-gizi', 
    components : { SearchList },
    data() {
        return {
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            mealsDesc : [
                { colName : 'kelompok', labelData : '', isTitle : true },
                { colName : 'kelompok_makanan_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            gizi : {
                client_id:null,
                temp_gizi_id:null,
                nama_template : null,
                tgl_dibuat: null,
                tgl_berlaku: null,
                catatan:null,
                jml_hari: null,
                is_aktif : true,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        
    },
    
    methods : {
        ...mapActions('templateGizi',['createTGizi','dataTGizi','updateTGizi']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formTemplateGizi').hide();
            return false;
        },

        editTemplateGizi(id){
            this.clearGizi();            
            this.dataTGizi(id).then((response)=>{
                if (response.success == true) {
                    this.fillGizi(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formTemplateGizi').show();
                } else {
                    alert(response.message);
                }
            })
        },

        submitTemplateGizi(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createTGizi(this.gizi).then((response) => {
                    if (response.success == true) {
                        alert(" data gizi baru berhasil dibuat.");
                        this.clearGizi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }   
            else {
                this.updateTGizi(this.gizi).then((response) => {
                    if (response.success == true) {
                        alert(" data gizi berhasil diubah.");
                        this.clearGizi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }         
        },

        newTemplateGizi(){
            this.clearGizi();
            this.isUpdate = false;
            UIkit.modal('#formTemplateGizi').show();
        },  

        clearGizi(){
            this.gizi.client_id = null;
            this.gizi.temp_gizi_id = null;
            this.gizi.nama_template = null;
            this.gizi.tgl_dibuat = null;
            this.gizi.tgl_berlaku = null;
            this.gizi.jml_hari = null;
            this.gizi.catatan = null;
            this.gizi.is_aktif = true;
        },

        fillGizi(data){
            this.gizi.client_id = null;
            this.gizi.temp_gizi_id = data.temp_gizi_id;
            this.gizi.nama_template = data.nama_template;
            this.gizi.tgl_dibuat = data.tgl_dibuat;
            this.gizi.tgl_berlaku = data.tgl_berlaku;
            this.gizi.jml_hari = data.jml_hari;
            this.gizi.catatan = data.catatan;
            this.gizi.is_aktif = data.is_aktif;
        },
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
}

.hims-form-container label {
    color:#333;
    font-size:12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border:2px solid #cc0202;
}

.uk-button {
    border-radius:50px;
    border:2px solid #aaa;
    font-weight:400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    border-radius:5px;
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
}

.hims-form-header {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    top:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-header h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}


.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */

</style>