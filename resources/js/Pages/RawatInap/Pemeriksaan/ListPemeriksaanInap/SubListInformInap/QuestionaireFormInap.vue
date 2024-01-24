<template>
    <div class="uk-modal-container" :id="idModal" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-body" uk-overflow-auto>
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>
                <div style="padding:1em;">
                    <h2 style="margin:0;padding:0;" class="uk-modal-title">{{informed.template_nama}}</h2>
                    <p style="font-size:12px; font-weight:400;margin:0;padding:0;color:black;">{{ informed.template_id }} - {{ informed.deskripsi }}</p>
                </div>

                <div style="padding:1em;">
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-3@m" style="border-right:1px solid #ccc;">
                            <dl class="uk-description-list resep-description-list">
                                <dt style="font-weight: 700;font-size:14px;color:black;">Dokter</dt>
                                <dd style="font-weight: 500;font-size:12px;color:black;">{{informed.dokter_nama}}</dd>
                                <dd style="font-weight: 500;font-size:12px;color:black;">{{informed.dokter_id}}</dd>
                            </dl>
                        </div>
                        <div class="uk-width-1-3@m" style="border-right:1px solid #ccc;">
                            <dl class="uk-description-list resep-description-list">
                                <dt style="font-weight: 700;font-size:14px;color:black;">Pasien</dt>
                                <dd style="font-weight: 500;font-size:12px;color:black;">{{informed.nama_pasien}}</dd>
                                <dd style="font-weight: 500;font-size:12px;color:black;">{{informed.no_rm}} - {{informed.pasien_id}}</dd>
                            </dl>
                        </div>
                        <div class="uk-width-1-3@m" style="border-right:0px solid #ccc;">
                            <dl class="uk-description-list resep-description-list">
                                <dt style="font-weight: 700;font-size:14px;color:black;">Unit</dt>
                                <dd style="font-weight: 500;font-size:12px;color:black;">{{informed.unit_nama}}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="uk-grid-small" uk-grid style="margin:0;padding:0.5em;">
                    <table class="uk-table">
                        <thead>
                            <tr style="background-color: #cc0202;padding:0.5em;">
                                <th colspan="2" style="font-weight:500;font-size:14px;color:white;">Item Pertanyaan</th>
                                <th style="font-weight:500;font-size:14px;color:white;">Jawaban</th>      
                                <th style="font-weight:500;font-size:14px;color:white;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for = "dt in informed.items" style="border-bottom:1px solid #ccc;">
                                <template v-if="dt.tipe_jawaban == 'Radio'">
                                    <td colspan="2" style="font-size:12px; font-weight:500;color:black;">
                                        <p style="padding:0;margin:0;">{{ dt.pertanyaan }}</p>
                                        <p style="padding:0;margin:0;font-weight:400">{{ dt.deskripsi }}</p>
                                    </td>
                                    <td>                                    
                                        <div class="uk-column-1-2@m" style="font-size:12px; padding:0 0.4em 0.4em 0.4em; margin:0.2em 0 0 0; color:black; border-bottom:0px solid #efefef; font-weight:500;">
                                            <p v-for="itm in dt.pilihan_jawaban" style="font-weight:400; font-size:12px; padding:0.4em; margin:0; color:black; ">
                                                <label class="uk-grid-small" uk-grid>
                                                    <input class="uk-radio uk-width-auto" type="radio" v-model="dt.value" :name="dt.inform_map_id" :value="itm.variable" :checked="itm.is_default" style="color:black;margin:0 0 0 3px;"> 
                                                    <span class="uk-width-expand" style="display:inline-block;">{{ itm.variable }}</span>
                                                </label>                                    
                                            </p>
                                        </div>
                                    </td>
                                    <td style="font-size:12px; font-weight:500;color:black;">
                                        {{ dt.value }}
                                    </td>
                                </template>
                                
                                <template v-else-if="dt.tipe_jawaban == 'ScoreResult'">
                                    <td colspan="2" style="font-size:12px; font-weight:500;color:black;">
                                        <p style="padding:0;margin:0;">{{ dt.pertanyaan }}</p>
                                        <p style="padding:0;margin:0;font-weight:400">{{ dt.deskripsi }}</p>
                                    </td>
                                    <td>
                                        <template v-for="itm in dt.pilihan_jawaban" style="font-size:12px; padding:0.4em; margin:0; color:black; border-bottom: 1px solid #efefef;">
                                            <p  style="font-size:12px; padding:0.4em; margin:0.2em 0 0.2em 0; color:black; border-bottom: 0px solid #efefef; font-weight:500;">{{ itm.variable }}</p>
                                            <div class="uk-column-1-2@m" style="font-size:12px; padding:0.4em; margin:0.2em 0 0 0; color:black; border-bottom:1px solid #efefef; font-weight:500;">
                                                <p v-for="score in itm.item_score" style="font-weight:400; font-size:12px; padding:0.4em; margin:0; color:black; ">
                                                    <label class="uk-grid-small" uk-grid>
                                                        <input class="uk-radio uk-width-auto" type="radio" :name="itm.variable" v-model="itm.value" :value="score.score" :checked="itm.is_default" @change="scoreChecked(itm,dt)" style="color:black;margin:0 0 0 3px;"> 
                                                        <span class="uk-width-expand" style="display:inline-block;">{{ score.item_score }} <strong>({{ score.score }})</strong></span>
                                                    </label>                                    
                                                </p>
                                            </div>                                    
                                        </template>
                                    </td>
                                    <td style="font-size:12px; font-weight:500;color:black;">
                                        <p style="padding:0;margin:0;">{{ dt.tipe_jawaban }}</p>
                                        <p style="padding:0;margin:0;font-size:16px;">{{ dt.value }}</p>
                                    </td>
                                </template>

                                <template v-else-if="dt.tipe_jawaban == 'Number'">
                                    <td colspan="2" style="font-size:12px; font-weight:500;color:black;">
                                        <p style="padding:0;margin:0;">{{ dt.pertanyaan }} <span v-if="dt.satuan">({{ dt.satuan }})</span></p>
                                        <p style="padding:0;margin:0;font-weight:400;">{{ dt.deskripsi }}</p>
                                    </td>
                                    <td>
                                        <input type="number" class="uk-input uk-form-small" v-model="dt.value">
                                    </td>
                                    <td style="font-size:12px; font-weight:500;color:black;">
                                        <!-- {{ dt.tipe_jawaban }} -->
                                    </td>
                                </template>
                                
                                <template v-else-if="dt.tipe_jawaban == 'FreeText'">
                                    <td colspan="2" style="font-size:12px; font-weight:500;color:black;">
                                        <p style="padding:0;margin:0;">{{ dt.pertanyaan }}</p>
                                        <p style="padding:0;margin:0;font-weight:400">{{ dt.deskripsi }}</p>
                                    </td>
                                    <td>
                                        <textarea type="text" class="uk-textarea uk-form-small" v-model="dt.value"></textarea>
                                    </td>
                                    <td style="font-size:12px; font-weight:500;color:black;">
                                        <!-- {{ dt.tipe_jawaban }} -->
                                    </td>
                                </template>
                                

                                <template v-else>
                                    <td colspan="2" style="font-size:12px; font-weight:500;color:black;">
                                        <p style="padding:0;margin:0;">{{ dt.pertanyaan }}</p>
                                        <p style="padding:0;margin:0;font-weight:400">{{ dt.deskripsi }}</p>
                                    </td>
                                    <td></td>
                                    <td style="font-size:12px; font-weight:500;color:black;">
                                        <!-- {{ dt.tipe_jawaban }} -->
                                    </td>
                                </template>
                            </tr>
                        </tbody>
                    </table>
                </div>                  
            </div>    
            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-modal-close" type="button" style="background-color:#fff; color:black;margin-right:5px;border:none;">Tutup</button>
                <button class="uk-button" type="button" style="background-color:#cc0202; color:white;border:none" @click.prevent="submitConsent">Simpan</button>
            </div>        
        </div>
    </div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'questionaire-form-inap',
    data(){
        return {
            idModal : 'questionaireFormInap',
            isUpdate : true,
            isLoading : false,
            titleForm : 'INFORMED CONSENT BARU',
            informed : {
                asesmen_id : null,
                template_id : null,
                template_nama : null,
                deskripsi : null,
                dokter_id : null,
                dokter_nama : null,
                pasien_id : null,
                nama_pasien : null,
                no_rm : null,
                unit_id : null,
                unit_nama : null,
                reg_id : null,
                reff_trx_id : null,
                items : [],
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('informedConsent',['informedLists']),
    },
    methods : {
        ...mapActions('informedConsent',['listInformed','createInformed','deleteInformed','dataInformed','updateInformed','deleteInformedItem']),
        ...mapActions('medicalrecord',['createInformConsent', 'updateInformConsent', 'dataInformConsent']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        showModal() {
            UIkit.modal(`#${this.idModal}`).show();
        },

        closeModal() {
            UIkit.modal(`#${this.idModal}`).hide();
        },

        newData(templateId, transaction){
            this.isLoading = true;

            this.informed.dokter_id = transaction.dokter_id;
            this.informed.dokter_nama = transaction.dokter_nama;
            this.informed.pasien_id = transaction.pasien_id;
            this.informed.nama_pasien = transaction.nama_pasien;
            this.informed.no_rm = transaction.no_rm;
            this.informed.unit_id = transaction.unit_id;
            this.informed.unit_nama = transaction.unit_nama;
            this.informed.reg_id = transaction.reg_id;
            this.informed.reff_trx_id = transaction.trx_id;

            this.dataInformed(templateId).then((response)=>{
                this.isLoading = false;
                if (response.success == true) {
                    this.fillInformed(response.data);
                    this.isUpdate = false;
                    this.showModal();
                } 
            })
        },

        editData(data) {
            this.isLoading = true;
            this.CLR_ERRORS();
            this.dataInformConsent(data.asesmen_id).then((response)=>{
                this.isLoading = false;
                if (response.success == true) {
                    this.fillEditedInformed(response.data);
                    this.showModal();
                } 
            })
        },

        retrieveData(templateId){
            this.isLoading = true;
            this.dataInformed(templateId).then((response)=>{
                this.isLoading = false;
                if (response.success == true) {
                    this.fillInformed(response.data);
                    this.showModal();
                } 
            })
        },

        fillInformed(data){
            this.informed.template_id = data.template_id;
            this.informed.template_nama = data.template_nama;
            this.informed.deskripsi = data.deskripsi;
            this.informed.items = data.items;

            this.informed.items.forEach(function(dt){
                if(dt.tipe_jawaban.toUpperCase() == 'SCORERESULT') {
                    dt['value'] = 0;
                }
            })
        },

        fillEditedInformed(data) {
            this.informed.asesmen_id = data.asesmen_id;
            this.informed.dokter_id = data.dokter_id;
            this.informed.dokter_nama = data.dokter_nama;
            this.informed.pasien_id = data.pasien_id;
            this.informed.nama_pasien = data.nama_pasien;
            this.informed.no_rm = data.no_rm;
            this.informed.unit_id = data.unit_id;
            this.informed.unit_nama = data.unit_nama;
            this.informed.reg_id = data.reg_id;
            this.informed.reff_trx_id = data.reff_trx_id;
            this.informed.template_id = data.template_id;
            this.informed.template_nama = data.template_nama;
            this.informed.deskripsi = data.deskripsi;
            this.informed.items = data.items;

            this.isUpdate = true;

        },

        clearInformed(){
            this.informed.template_id = null;
            this.informed.template_nama = null;
            this.informed.deskripsi = null;
            this.informed.items = [];
        },

        scoreChecked(dt, group){
            let totalScore = 0;
            group.pilihan_jawaban.forEach(function(val){
                totalScore = totalScore + (val.value * 1);
                group.value = totalScore;
            });
        },

        submitConsent() {
            this.isLoading = true;
            console.log(JSON.parse(JSON.stringify(this.informed)));
            if(this.isUpdate) {
                this.updateInformConsent(this.informed).then((response)=>{
                    this.isLoading = false;
                    if (response.success == true) {
                        console.log(response.data);
                        this.closeModal();
                    } 
                    else {
                        alert(response.message);
                    }
                });
            }
            else {
                this.createInformConsent(this.informed).then((response)=>{
                    this.isLoading = false;
                    if (response.success == true) {
                        console.log(response.data);
                        this.closeModal();
                    } 
                    else {
                        alert(response.message);
                    }
                });
            }
        }

    }
}
</script>