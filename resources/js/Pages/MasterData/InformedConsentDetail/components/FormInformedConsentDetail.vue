<template>
    <div uk-modal="bg-close:false;" id="formDetail" style="min-height:50vh;">
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h4 class="uk-modal-title1" style="font-weight: 500;">Pertanyaan / Pernyataan</h4>
            </div>
            <div class="uk-modal-body" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitPertanyaan" style="padding:0;" >
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                    
                    <!-- DATA DETAIL -->
                    <div class="uk-grid-small hims-form-subpage" uk-grid >                        
                        <div class="uk-width-1-1@s uk-grid-small" uk-grid>
                            <!-- <div class="uk-width-1-1@s uk-grid-small" uk-grid id="det_temp"> -->
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pertanyaan / Pernyataan</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" ref="idPertanyaan" v-model="detail.pertanyaan" type="text" placeholder=""></textarea>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Jawaban</label>
                                    <div class="uk-form-controls">
                                        <select v-model="detail.tipe_jawaban" class="uk-select uk-form-small" @change="tipeJawabanChange">
                                            <option v-if="isRefExist" v-for="dt in jenisJawabanRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Satuan</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="detail.satuan" type="text" placeholder="">
                                    </div>
                                </div>
                                
                                <div v-if="detail.tipe_jawaban !== 'ScoreItem'" class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" rows="2" v-model="detail.deskripsi" type="text" placeholder=""></textarea>
                                    </div>
                                </div>

                                <!-- <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Batas Min.</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="detail.satuan" type="text" placeholder="">
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Batas Maks.</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="detail.satuan" type="text" placeholder="">
                                    </div>
                                </div> -->

                                <div v-if="detail.tipe_jawaban == 'Radio'" class="uk-width-1-1@m">
                                    <table class="uk-table uk-width-full">
                                        <thead>
                                            <tr style="margin:0;padding:0.2em;border-bottom:1px solid #ccc;">
                                                <th style="margin:0;padding:0.5em;color:black;font-weight:500;">VARIABLE</th>
                                                <th v-if="detail.tipe_jawaban == 'ScoreItem'" style="margin:0;padding:0.5em;color:black;font-weight:500;width:50px;text-align:center;">VALUE</th>
                                                <th style="margin:0;padding:0.5em;color:black;font-weight:500;width:50px;text-align:center;">DEFAULT</th>
                                                <th style="margin:0;padding:0.5em;color:black;font-weight:500;width:50px;text-align:center;">NOTE</th>
                                                <th style="margin:0;padding:0.5em;color:black;font-weight:500;width:40px;text-align:center;">...</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="border-bottom:1px solid #ccc;">
                                                <td style="color:black;padding:0.2em;margin:0;">
                                                    <input class="uk-input uk-form-small" type="text" v-model="variableItem.variable" placeholder="item">
                                                </td>
                                                <td v-if="detail.tipe_jawaban == 'ScoreItem'" style="color:black;width:50px;text-align:center;padding:0.2em;margin:0;">
                                                    <input class="uk-input uk-form-small" type="text" placeholder="nilai" v-model="variableItem.value" style="text-align: center;">
                                                </td>
                                                <td style="color:black;width:50px;text-align:center;padding:0.4em 0.2em 0.5em 0.2em;margin:0;">                                                    
                                                    <input class="uk-checkbox" type="checkbox" v-model="variableItem.is_default">
                                                </td>
                                                <td style="color:black;width:50px;text-align:center;padding:0.4em 0.2em 0.5em 0.2em;margin:0;">                                                    
                                                    <input class="uk-checkbox" type="checkbox" v-model="variableItem.is_add_note">
                                                </td>
                                                <td style="color:black;width:40px;text-align:center;padding:0.4em;margin:0;">
                                                    <button type="button" @click.prevent="addItemJawaban" style="padding:0.2em; border:none; background-color: #fff;"><span uk-icon="icon:plus-circle"></span></button>
                                                </td>
                                            </tr>
                                            <template v-for="dt in detail.pilihan_jawaban">
                                                <tr style="border-bottom:1px solid #eee;">
                                                    <td style="color:black;padding:0.2em;margin:0;">
                                                        <input class="uk-input uk-form-small uk-form-blank" type="text" v-model="dt.variable" placeholder="item" style="border:none;font-weight: 400; font-style:italic; ">
                                                    </td>
                                                    <!-- <td style="color:black;width:50px;text-align:center;padding:0.2em;margin:0;">
                                                        <input class="uk-input uk-form-small uk-form-blank" type="text" placeholder="nilai" v-model="dt.value" style="text-align: center;border:none;">
                                                    </td> -->
                                                    <td style="color:black;width:50px;text-align:center;padding:0.4em 0.2em 0.5em 0.2em;margin:0;">                                                    
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_default">
                                                    </td>
                                                    <td style="color:black;width:50px;text-align:center;padding:0.4em 0.2em 0.5em 0.2em;margin:0;">                                                    
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_add_note">
                                                    </td>
                                                    <td style="color:black;width:40px;text-align:center;padding:0.4em;margin:0;">
                                                        <button type="button" @click.prevent="removeItemJawaban(dt)" style="padding:0.2em; border:none; background-color: #fff;"><span uk-icon="icon:minus-circle"></span></button>
                                                    </td>
                                                </tr>                                                
                                            </template>
                                            <!-- <tr style="border-bottom:1px solid #ccc;">
                                                <td style="color:black;padding:0.2em;margin:0;">
                                                    <input class="uk-input uk-form-small" type="text" placeholder="item">
                                                </td>
                                                <td style="color:black;width:70px;text-align:center;padding:0.2em;margin:0;">                                                    
                                                    <input class="uk-checkbox" type="checkbox">
                                                </td>
                                                <td style="color:black;width:70px;text-align:center;padding:0.2em;margin:0;">                                                    
                                                    <input class="uk-checkbox" type="checkbox">
                                                </td>
                                                <td style="color:black;width:70px;text-align:center;padding:0.2em;margin:0;">
                                                    <button><span uk-icon="icon:minus-circle"></span></button>
                                                </td>
                                            </tr> -->
                                        </tbody>
                                    </table>
                                </div>

                                <div v-else-if="detail.tipe_jawaban == 'ScoreResult'" class="uk-width-1-1@m">
                                    <table class="uk-table uk-width-full">
                                        <thead>
                                            <tr style="margin:0;padding:0.2em;border-bottom:1px solid #ccc;">      
                                                <th colspan="3" style="margin:0;padding:0.5em;color:black;font-weight:500;">VARIABLE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <row-score-summary v-for = "data in detail.pilihan_jawaban" :dt="data" :removeItemCallback = "removeItemJawaban"></row-score-summary>
                                            <tr style="padding-top:1em;background-color:#fefefe;">
                                                <td colspan="2" style="color:black;padding:1em 0.2em 1em 0.2em;margin:0;">
                                                    <input class="uk-input uk-form-small" type="text" v-model="variableItem.variable" placeholder="variable baru">
                                                </td> 
                                                <td style="color:black;width:40px;text-align:center;padding:1em 0.2em 1em 0.2em;margin:0;">
                                                    <button type="button" @click.prevent="addItemJawaban" style="padding:0.2em; border:none; background-color: #fff;"><span uk-icon="icon:plus-circle"></span></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- <div class="uk-width-1-1@m">
                                    <table class="uk-table uk-width-full">
                                        <thead>
                                            <tr style="margin:0;padding:0.2em;border-bottom:1px solid #ccc;">
                                                <th style="margin:0;padding:0.5em;color:black;font-weight:500;">Variable</th>
                                                <th style="margin:0;padding:0.5em;color:black;font-weight:500;width:70px;text-align:center;">VALUE</th>
                                                <th style="margin:0;padding:0.5em;color:black;font-weight:500;width:40px;text-align:center;">...</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="border-bottom:1px solid #ccc;">
                                                <td style="color:black;padding:0.2em;margin:0;">
                                                    <input class="uk-input uk-form-small" type="text" placeholder="item"  v-model="variableItem.variable">
                                                </td>
                                                <td style="color:black;width:70px;text-align:center;padding:0.2em;margin:0;">
                                                    <input class="uk-input uk-form-small" type="number" placeholder="nilai"  v-model="variableItem.value">
                                                </td>
                                                <td style="color:black;width:70px;text-align:center;padding:0.2em;margin:0;">
                                                    <button><span uk-icon="icon:plus-circle"></span></button>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom:1px solid #ccc;">
                                                <td style="color:black;padding:0.2em;margin:0;">
                                                    <input class="uk-input uk-form-small" type="text" placeholder="variable">
                                                </td>
                                                <td style="color:black;width:70px;text-align:center;padding:0.2em;margin:0;">
                                                    <input class="uk-input uk-form-small" type="text" placeholder="skala">
                                                </td>
                                                <td style="color:black;width:70px;text-align:center;padding:0.2em;margin:0;">
                                                    <button><span uk-icon="icon:minus-circle"></span></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div> -->

                                <div class="uk-width-1-1@m">
                                    <div>
                                        <label><input class="uk-checkbox" type="checkbox" v-model="detail.is_mandatory" style="color:black;margin:0 0 0 5px;"> Mandatory (wajib isi)</label>
                                    </div>    
                                </div>
                                <div class="uk-width-1-1@m">
                                    <div>
                                        <label><input class="uk-checkbox" type="checkbox" v-model="detail.is_tanda_vital" style="color:black;margin:0 0 0 5px;"> Tanda vital pasien</label>
                                    </div>    
                                </div>
                            <!-- </div> -->
                        </div>

                        <div class="uk-width-1-1 uk-grid-small uk-card hims-form-header" uk-grid >
                            <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                                <!-- <h5 class="uk-text-uppercase">DETAIL INFORMED CONSENT</h5> -->
                            </div>
                            <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                                <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                            </div>
                            <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                                <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
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
import RowScoreSummary from '@/Pages/MasterData/InformedConsentDetail/components/RowScoreSummary.vue';

export default {
    name: "form-informed-consent-detail",
    components : {
        RowScoreSummary,
    },
    data() {
        return {
            isUpdate: true,
            detail: {
                inform_detail_id : null,
                pertanyaan : null,
                deskripsi: null,
                tipe_jawaban : null,
                pilihan_jawaban: [],
                satuan: null,
                is_tanda_vital: false,
                is_mandatory : false,
                image_item: null,
            },

            variableItem : {
                is_default : false,
                is_add_note : false,
                value : null,
                variable : [],
                item_score : [],
            },

            itemScore : {
                item_score : null,
                is_default : false,
                is_add_note : false,
                value : null,
                score : 0,
            },

            informedData: [],
        };
    },
    computed: {
        ...mapGetters(["errors"]),
        ...mapGetters("referensi", ["jenisJawabanRefs", "isRefExist"]),
    },
    mounted() {
        this.initialize();
    },
    methods: {
        ...mapActions("dtd", ["createDtd", "updateDtd", "dataDtd"]),
        ...mapActions("informedConsentDetail", ["createInformedDetail","dataInformedDetail","updateInformedDetail","deleteInformedDetail",]),
        ...mapActions("referensi", ["listReferensi"]),
        ...mapMutations(["CLR_ERRORS"]),

        initialize() {
            let param = { per_page: "ALL" };
            if (!this.isRefExist) {
                this.listReferensi();
            }
        },
        closeModal() {
            this.$emit("closed", true);
            UIkit.modal("#formDetail").hide();
        },
        
        submitPertanyaan() {
            this.CLR_ERRORS();
            if (!this.isUpdate) {
                this.createInformedDetail(this.detail).then((response) => {
                    if (response.success == true) {
                        alert("Pertanyaan baru berhasil dibuat.");
                        this.clearForm();
                        this.isUpdate = false;
                    }
                });
            }
            else {
                this.updateInformedDetail(this.detail).then((response) => {
                    if (response.success == true) {
                        alert("Pertanyaan berhasil diubah.");
                        this.isUpdate = true;
                        this.closeModal();
                    }
                });
            }
        },
        newDetail() {
            this.clearForm();
            this.isUpdate = false;
            UIkit.modal("#formDetail").show();
        },

        editDetail(detailId) {
            this.clearForm();
            this.dataInformedDetail(detailId).then((response) => {
                if (response.success == true) {
                    this.fillForm(response.data);
                    this.isUpdate = true;
                    UIkit.modal("#formDetail").show();
                }
                else {
                    alert(response.message);
                }
            });
        },

        clearForm() {
            this.detail.inform_detail_id  = null;
            this.detail.pertanyaan  = null;
            this.detail.tipe_jawaban  = null;
            this.detail.pilihan_jawaban = [];
            this.detail.deskripsi = null;
            this.detail.satuan = null;
            this.detail.is_tanda_vital = false;
            this.detail.is_mandatory = false;
            this.detail.image_item = null;
        },

        fillForm(data) {
            this.detail.inform_detail_id  = data.inform_detail_id;
            this.detail.pertanyaan  = data.pertanyaan;
            this.detail.tipe_jawaban  = data.tipe_jawaban;
            this.detail.pilihan_jawaban = JSON.parse(JSON.stringify(data.pilihan_jawaban));
            this.detail.satuan = data.satuan;
            this.detail.deskripsi = data.deskripsi;
            this.detail.is_tanda_vital = data.is_tanda_vital;
            this.detail.is_mandatory = data.is_mandatory;
            this.detail.image_item = data.image_item;
        },

        tipeJawabanChange(){
            this.detail.pilihan_jawaban = [];
            this.detail.satuan = null;
            this.detail.is_tanda_vital = false;
            this.detail.is_mandatory = false;
            this.detail.image_item = null;
        },

        addItemJawaban(){
            let val = JSON.parse(JSON.stringify(this.variableItem));
            this.detail.pilihan_jawaban.push(val);
            this.clearVariableItem();
        },

        clearVariableItem(){
            this.variableItem.is_default = false;
            this.variableItem.is_add_note = false;
            this.variableItem.value = null;
            this.variableItem.variable = null;
            this.variableItem.item_score = [];
        },

        removeItemJawaban(data){
            if(data) {
                if(confirm(`Proses ini akan menghapus variable jawaban ${data.variable}. Lanjutkan ?`)){
                    let filteredData = this.detail.pilihan_jawaban.filter(item => { return item.variable !== data.variable });
                    if(filteredData) {
                        this.detail.pilihan_jawaban = JSON.parse(JSON.stringify(filteredData));
                    }
                }
            }
        }
    },
}
</script>