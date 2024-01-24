<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formIcdDiagnosa" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitIcdDiagnosa" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">KODE ICD10 DIAGNOSA</h5>
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
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kode ICD*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="icd10.kode_icd" type="text" required :disabled="isUpdate" placeholder="kode icd">
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama ICD</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="icd10.nama_icd" type="text" placeholder="nama icd">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kata Pencarian</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="icd10.kata_kunci" type="text" placeholder="kata pencarian"></textarea>
                            </div>
                        </div>
                        <div class="uk-width-1-2@m">
                            <search-select
                                :config = configSelect
                                searchUrl = "/dtd"
                                label = "No.DTD"
                                placeholder = "nomor dtd"
                                captionField = "no_dtd"
                                valueField = "no_dtd"
                                :itemListData = dtdDesc
                                :value = "icd10.no_dtd"
                                v-on:item-select = "dtdSelected"
                            ></search-select>
                        </div>
                        <div class="uk-width-1-1" style="padding:1em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="icd10.is_valid_icd" style="margin-left:5px;"> Valid Kode ICD</label>
                        </div>
                        
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
export default {
    name : 'form-icd-diagnosa', 
    components : { SearchSelect },
    data() {
        return {
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            isUpdate : true,
            icd10 : {
                client_id:null,
                kode_icd:null,
                nama_icd:null,
                kata_kunci : '',
                no_dtd : null,
                is_valid_icd : true,
                is_aktif : true,
            },
            dtdDesc : [
                { colName : 'no_dtd', labelData : '', isTitle : true },
                { colName : 'nama_dtd', labelData : '', isTitle : false },
                { colName : 'label_dtd', labelData : '', isTitle : false },
            ],
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('icd10',['createIcdDiagnosa','updateIcdDiagnosa','dataIcdDiagnosa']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formIcdDiagnosa').hide();
            return false;
        },
        dtdSelected(data){
            this.icd10.no_dtd = null;
            if(data) {
                this.icd10.no_dtd = data.no_dtd;
            }
        },
        submitIcdDiagnosa(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createIcdDiagnosa(this.icd10).then((response) => {
                    if (response.success == true) {
                        alert("ICD 10 baru berhasil dibuat.");
                        this.clearIcdDiagnosa();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }
            else {
                this.updateIcdDiagnosa(this.icd10).then((response) => {
                    if (response.success == true) {
                        this.fillIcdDiagnosa(response.data);
                        alert("ICD 10 berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }            
        },

        newIcdDiagnosa(){
            this.clearIcdDiagnosa();
            this.isUpdate = false;
            UIkit.modal('#formIcdDiagnosa').show();
        },

        editIcdDiagnosa(icdId){
            this.clearIcdDiagnosa();            
            this.dataIcdDiagnosa(icdId).then((response)=>{
                if (response.success == true) {
                    this.fillIcdDiagnosa(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formIcdDiagnosa').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearIcdDiagnosa(){
            this.icd10.client_id = null;
            this.icd10.kode_icd = null;
            this.icd10.nama_icd = null;
            this.icd10.no_dtd = null;
            this.icd10.kata_kunci = '';
            this.icd10.is_valid_icd = true;
            this.icd10.is_aktif = true;
        },

        fillIcdDiagnosa(data){
            this.icd10.client_id = null;
            this.icd10.kode_icd = data.kode_icd;
            this.icd10.nama_icd = data.nama_icd;
            this.icd10.no_dtd = data.no_dtd;
            this.icd10.kata_kunci = data.kata_kunci;
            this.icd10.is_valid_icd = data.is_valid_icd;
            this.icd10.is_aktif = data.is_aktif;
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