<template>
    <div class="uk-container uk-container-small1" style="padding:0;">
        <div class="uk-grid uk-grid-small" style="padding:0.5em 1em 1em 1em;">
            <div class="uk-width-auto" style="padding:0;margin:auto;">
                <router-link :to = "{ name:'listTemplateInformed' }" uk-icon="icon:arrow-left;ratio:1.5" style="display:block;width:30px;"></router-link>
            </div>
            <div class="uk-width-expand" style="padding:0;margin:auto;">
                <header-page :title="titleForm" subTitle="template master informed consent"></header-page>
            </div>
        </div>
        <div style="margin-top:1em;background-color:#fff;min-height:80vh;padding:1em;">
            <div class="uk-container hims-form-container" style="background-color:#fff;">
                <form action="" @submit.prevent="submitTemplate" style="padding:0 1em 1em 1em;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA TEMPLATE</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="retrieveData" class="uk-button-close uk-button uk-button-default uk-button-medium" style="font-size:12px;">Refresh</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                        <div uk-spinner="ratio : 2"></div>
                        <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                    </div>
                    
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                    
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                nama dan deskripsi dari template.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                            
                                <div class="uk-width-2-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Template*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="informed.template_nama" type="text" placeholder="nama template" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="informed.deskripsi" type="text" placeholder="deskripsi dan fungsi dari template"></textarea>
                                    </div>
                                </div> 
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0 0.5em 1em 0.5em;">
                            <div class="uk-width-expand@m">
                                <h5 style="color:indigo;padding:0;margin:0;">ITEM PERTANYAAN</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    daftar item pertanyaan dari form template.
                                </p>
                            </div>
                            <div class="uk-width-auto@m">
                                <button style="height:40px;" @click.prevent="showPreview">Preview</button>
                                <button style="height:40px;" @click.prevent="showPertanyaanPicker">Pilih item pertanyaan</button>
                            </div>

                        </div>
                        <div class="uk-width-1-1@s">
                            <table class="uk-table hims-table">
                                <thead>
                                    <th style="font-weight: 500; width:120px;">ID</th>
                                    <th>Item</th>
                                    <th style="text-align:center;">VITAL_SIGN</th>
                                    <th style="text-align:center;">Mandatory</th>
                                    <th>...</th>
                                </thead>
                                <tbody>
                                    <tr v-if="informed.items" v-for="dt in informed.items">
                                        <td style="font-weight: 500; width:120px;">
                                           <p style="margin:0;padding:0;font-size:12px;">{{  dt.inform_detail_id }}</p> 
                                           <p style="margin:0;padding:0;font-size:10px; font-weight:400;font-style:italic;">{{ dt.tipe_jawaban }}</p> 
                                        </td>
                                        <td style="font-weight: 400;">
                                           <p style="margin:0;padding:0;font-size:12px;font-weight:500;">{{ dt.pertanyaan }}</p> 
                                           <p style="margin:0;padding:0;font-size:10px; font-weight:400;font-style:italic;">{{ dt.deskripsi }}</p> 
                                        </td>
                                        <!-- <td>{{ dt.deskripsi }}</td> -->
                                        <td style="text-align:center;">
                                            <input class="uk-checkbox" type="checkbox" v-model="dt.is_tanda_vital" style="margin-left:5px;border:1px solid black;">
                                        </td>
                                        <td style="text-align:center;">
                                            <input class="uk-checkbox" type="checkbox" v-model="dt.is_mandatory" style="margin-left:5px;border:1px solid black;">
                                        </td>
                                        <td>
                                            <button @click.prevent="deleteItemInformed(dt)" style="background:none; border:none; cursor:pointer; "><span uk-icon="icon:trash;ratio:1"></span></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>                          
            </div>            
        </div>
        <modal-picker-pertanyaan 
            ref="modalPickerPertanyaan"
            title="Pilih Pertanyaan" 
            :fieldDatas = pickerColumns 
            :urlSearch = "urlPicker" 
            viewType="table"
            :proceedFunction="saveDataItemInform">
        </modal-picker-pertanyaan>

        <questionaire-form ref="questionaireForm"></questionaire-form>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import FormTemplate from '@/Pages/MasterData/InformedConsent/components/FormTemplate.vue';
import headerPage from '@/Components/HeaderPage.vue';
import ModalPickerPertanyaan from '@/Pages/MasterData/InformedConsent/components/ModalPickerPertanyaan/index.vue';
import QuestionaireForm from '@/Pages/MasterData/InformedConsent/form/QuestionaireForm.vue';


export default {
    props : {
        templateId : { type:String, required:true, default:'baru' },
    },
    components : {
        FormTemplate,headerPage,ModalPickerPertanyaan,QuestionaireForm
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('informedConsent',['informedLists']),
        ...mapGetters('referensi',['jenisJawabanRefs','isRefExist']),
    },
    data(){
        return {
            isUpdate : true,
            isLoading : false,
            titleForm : 'INFORMED CONSENT BARU',
            informed : {
                template_id : null,
                template_nama : null,
                deskripsi : null,
                items : [],
            },
            urlPicker : '/informed/detail',
            pickerColumns : [ 
                { 
                    name : 'inform_detail_id', 
                    title : 'ID', 
                    colType : 'text',
                    isCaption : false,
                },
                { 
                    name : 'pertanyaan', 
                    title : 'Pertanyaan',
                    colType : 'text',
                    isCaption : false,
                },            
                { 
                    name : 'tipe_jawaban', 
                    title : 'Tipe Jawaban', 
                    colType : 'text',
                    isCaption : true,
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text',
                },                
            ],
        }
    },
    mounted(){
        this.initialize();
    },
    methods : {
        ...mapActions('informedConsent',['listInformed','createInformed','deleteInformed','dataInformed','updateInformed','deleteInformedItem']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            if(this.templateId == '' || this.templateId == 'baru' || this.templateId == null) {
                this.titleForm = 'INFORMED CONSENT BARU';
                this.isUpdate = false;
            }
            else {
                this.titleForm = 'UBAH INFORMED CONSENT';
                this.isUpdate = true;
                this.retrieveData();
            }
        },

        showPreview(){
            this.$refs.questionaireForm.retrieveData(this.templateId);
        },

        showPertanyaanPicker(){
            this.$refs.modalPickerPertanyaan.showModalPicker('/informed/detail');
        },

        saveDataItemInform(data){
            if(data) {
                // console.log(data);
                let exist = this.informed.items.find( item => item.inform_detail_id == data.inform_detail_id );
                if(!exist) {
                    let dt = JSON.parse(JSON.stringify(data));
                    dt.is_mandatory = false;
                    dt.inform_map_id = null;
                    this.informed.items.push(dt);
                    this.$refs.modalPickerPertanyaan.closeModalPicker();
                }
                else {
                    alert('item sudah ada dalam template.');
                    return false;
                }
            }
        },

        deleteItemInformed(data){
            this.isLoading = true;
            this.CLR_ERRORS();

            if(data) {
                if(data.inform_map_id) { 
                    if(confirm(`Proses ini akan menghapus satuan ${data.satuan_id}. Lanjutkan ?`)){
                        this.deleteInformedItem(data.inform_map_id).then((response) => {
                            if (response.success == true) {
                                this.isLoading = false;
                                this.retrieveData();
                                this.isUpdate = true;
                            }
                        })
                    };
                }
                else { 
                    let dt = this.informed.items.filter(item => { return item.inform_detail_id !== data.inform_detail_id; });
                    this.informed.items = JSON.parse(JSON.stringify(dt));
                    this.isLoading = false;
                }
            }
        },

        submitTemplate(){
            this.CLR_ERRORS();
            this.isLoading = true;

            if(!this.isUpdate) {
                this.createInformed(this.informed).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        alert("Informed Consent baru berhasil dibuat.");
                        this.fillInformed(response.data);
                        this.isUpdate = true;
                        this.closeModal();
                    }
                })
            }
            else {
                this.updateInformed(this.informed).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        this.fillInformed(response.data);
                        alert("Informed Consent berhasil diubah.");
                        this.isUpdate = true;
                    }
                })
            }
        },
        retrieveData(){
            this.isLoading = true;
            this.dataInformed(this.templateId).then((response)=>{
                this.isLoading = false;
                if (response.success == true) {
                    this.fillInformed(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                    this.$router.back('-1');
                }
            })
        },

        // editTemplate(template_id){
        //     this.isLoading = true;
        //     this.dataDokter(template_id).then((response)=>{
        //         if (response.success == true) {
        //             this.fillInformed(response.data);
        //             this.isUpdate = true;
        //         } else {
        //             alert(response.message);
        //             this.$router.back('-1');
        //         }
        //         this.isLoading = false;
        //     })
        // },

        fillInformed(data){
            this.informed.template_id = data.template_id;
            this.informed.template_nama = data.template_nama;
            this.informed.deskripsi = data.deskripsi;
            this.informed.items = data.items;
        },

        clearInformed(){
            this.informed.template_id = null;
            this.informed.template_nama = null;
            this.informed.deskripsi = null;
            this.informed.items = [];
        }

    }
}
</script>