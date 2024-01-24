<template>
    <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
        <form action="" @submit.prevent="submitTemplate" >
            <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                    <h5 class="uk-text-uppercase">TEMPLATE INFORMED CONSENT</h5>
                </div>
                <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                    <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Selesai</button>                  
                </div>
                <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                    <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                </div>
            </div>
            <div>
                <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                    <div uk-spinner="ratio : 2"></div>
                    <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                </div>
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>

                <!-- DATA UTAMA -->
                <div class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            informasi singkat informed consent.
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Template*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="informed.nama_template" type="text" placeholder="nama template" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" rows="2" v-model="informed.deskripsi" type="text" placeholder="deskripsi"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DATA DETAIL -->
                <div class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA DETAIL</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            informasi detail informed consent.
                        </p>
                        <br>
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:11px;"><i>*gunakan titik-koma ( <b style="font-size:15px;">;</b> ) sebagai pemisah jawaban</i></label>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                        <div class="uk-width-5-6@s uk-grid-small" uk-grid id="det_temp" v-for="input in arr_Pertanyaan">
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pertanyaan</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" ref="idPertanyaan" :id="input.idPertanyaan" v-model="input.valuePertanyaan" type="text" placeholder="">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Jawaban</label>
                                <div class="uk-form-controls">
                                    <select :id="input.idJenis" v-model="input.valueJenis" class="uk-select uk-form-small">
                                        <option v-if="isRefExist" v-for="dt in jenisJawabanRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pilihan Jawaban</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea uk-form-small" rows="2" :id="input.idJawaban" v-model="input.valueJawaban" type="text" placeholder=""></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-1-6@s uk-grid-small" uk-grid id="det_tambah" style="margin-top:80px;">
                            <div class="uk-width-1-4@m" id="btnDupe">
                                <div class="uk-form-controls">
                                    <button type="button" @click="addInput" class="uk-button uk-button-default uk-button-small hims-table-btn">Tambah</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
                nama_template : '',
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
            if(!this.isRefExist) { 
                this.listReferensi();
            };
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
        },

        closeModal(){
            this.$router.back('-1');
        },

        submitTemplate(){
            this.CLR_ERRORS();
            alert('submit template');
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

        editTemplate(dokterId){
            this.isLoading = true;
            this.dataDokter(dokterId).then((response)=>{
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