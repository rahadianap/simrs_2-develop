<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formDietPasien" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitDiet" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">Diet Pasien</h5>
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Diet*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="diet.nama_diet" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea" style="font-size:14px;" v-model="diet.catatan" type="text">{{diet.catatan}}</textarea>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Komplikasi*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="diet.komplikasi" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="diet.is_semua_kelas" style="margin-left:5px;"> Berlaku untuk semua kelas</label>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="diet.is_aktif" style="margin-left:5px;"> Data nama_diet tagihan aktif</label>
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
    name : 'form-diet-pasien', 
    data() {
        return {
            isUpdate : true,
            diet : {
                client_id:null,
                diet_id:null,
                nama_diet : null,
                catatan:null,
                komplikasi:null,
                is_semua_kelas:false,
                is_aktif : true,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('diet',['createDiet','dataDiet','updateDiet']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formDietPasien').hide();
            return false;
        },

        submitDiet(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createDiet(this.diet).then((response) => {
                    if (response.success == true) {
                        alert("Data diet baru berhasil dibuat.");
                        this.clearDiet();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateDiet(this.diet).then((response) => {
                    if (response.success == true) {
                        alert("Data diet berhasil diubah.");
                        this.clearDiet();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }         
        },

        newDiet(){
            this.clearDiet();
            this.isUpdate = false;
            UIkit.modal('#formDietPasien').show();
        },  

        editDiet(id){
            this.clearDiet();            
            this.dataDiet(id).then((response)=>{
                if (response.success == true) {
                    this.fillDiet(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formDietPasien').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearDiet(){
            this.diet.client_id = null;
            this.diet.diet_id = null;
            this.diet.nama_diet = null;
            this.diet.komplikasi = null;
            this.diet.is_semua_kelas = false;
            this.diet.catatan = null;
            this.diet.is_aktif = true;
        },

        fillDiet(data){
            this.diet.client_id = null;
            this.diet.diet_id = data.diet_id;
            this.diet.nama_diet = data.nama_diet;
            this.diet.komplikasi = data.komplikasi;
            this.diet.is_semua_kelas = data.is_semua_kelas;
            this.diet.catatan = data.catatan;
            this.diet.is_aktif = data.is_aktif;
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