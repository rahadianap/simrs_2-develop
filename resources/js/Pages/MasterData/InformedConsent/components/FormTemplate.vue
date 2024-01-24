<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formTemplate" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitTemplate" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">INFORMED CONSENT</h5>
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Template</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="informed.template_nama" type="text" required :disabled="isUpdate" placeholder="nama template">
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="informed.deskripsi" type="text" placeholder="deskripsi singkat template">
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
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-template', 
    components : {
        SearchSelect,
        SearchList
    },
    data() {
        return {
            isLoading : true,
            isUpdate : true,
            informed : {
                template_nama : '',
                deskripsi : '',
            },
            counter1: 0,
            counter2: 0,
            counter3: 0,
            arr_Pertanyaan: [
                {
                    idPertanyaan: 'pertanyaan0',valuePertanyaan: '',
                    idJenis: 'jenis0',valueJenis: '',
                    idJawaban: 'jawaban0',valueJawaban: '',
                },
            ],
            informedData: [],
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('informedConsent',['informedLists']),
        ...mapGetters('referensi',['jenisJawabanRefs','isRefExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('informedConsent',['listInformed','createInformed','deleteInformed']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let param = {per_page:'ALL'};
            // if(!this.isRefExist) { 
            //     this.listReferensi();
            // };
        },

        addInput() {
            this.arr_Pertanyaan.push(
                {
                    idPertanyaan: `pertanyaan${++this.counter1}`,valuePertanyaan: '',
                    idJenis: `jenis${++this.counter2}`,valueJenis: '',
                    idJawaban: `jawaban${++this.counter3}`,valueJawaban: '',
                },
            );
        },

        newTemplate(){
            this.isUpdate = false;
            this.isLoading = false;
            UIkit.modal('#formTemplate').show();
        },

        closeModal(){
            // this.$router.back('-1');
            this.$router.push(`/master/informed`);
            // this.$emit('closed',true);
            UIkit.modal('#formTemplate').hide();
        },

        submitTemplate(){
            this.CLR_ERRORS();
            // this.informedData.push(
            //     this.informed,
            //     this.arr_Pertanyaan,
            // );
            if(!this.isUpdate) {
                this.createInformed(this.informed).then((response) => {
                    if (response.success == true) {
                        alert("Informed Consent baru berhasil dibuat.");
                        // this.fillTemplate(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeModal();
                    }
                })
            }
            else {
                this.updateInformed(this.informed).then((response) => {
                    if (response.success == true) {
                        this.fillTemplate(response.data);
                        alert("Informed Consent berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }
        },

        editTemplate(template_id){
            this.isLoading = true;
            this.dataDokter(template_id).then((response)=>{
                if (response.success == true) {
                    this.fillTemplate(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                    this.$router.back('-1');
                }
                this.isLoading = false;
            })
        },

        fillInformed(data){

        },
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox, .uk-select {
    border: 1px solid #999;
    color: black;
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

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
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