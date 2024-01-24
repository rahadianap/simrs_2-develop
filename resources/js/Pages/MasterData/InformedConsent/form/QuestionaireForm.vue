<template>
    <div class="uk-modal uk-modal-container" :id="idModal">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Preview Question</h2>
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            <div>
                <p style="font-size:12px; font-weight:400;margin:0;padding:0;color:black;">{{ informed.template_id }}</p>
                <h3 style="font-size:18px; font-weight:500;margin:0;padding:0;">{{ informed.template_nama }}</h3>
                <p style="font-size:12px; font-weight:400;margin:0;padding:0;">{{ informed.deskripsi }}</p>
            </div>
            <div class="uk-grid-small" uk-grid style="margin:0;padding:0.5em;">
                <table class="uk-table">
                    <thead>
                        <tr style="background-color: #cc0202;padding:0.5em;">
                            <th colspan="2" style="font-weight:500;font-size:14px;color:white;">Item Pertanyaan</th>
                            <th style="font-weight:500;font-size:14px;color:white;">Jawaban</th>      
                            <th style="font-weight:500;font-size:14px;color:white;">Jenis</th>
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
                                    <!-- <p v-for="itm in dt.pilihan_jawaban" style="font-size:12px;padding:0.4em; margin:0; color:black; border-bottom:1px solid #efefef;">
                                        <label><input class="uk-radio" type="radio" :name="dt.inform_map_id" :value="itm.variable" :checked="itm.is_default" style="color:black;margin:0 0 0 5px;"> {{ itm.variable }}</label>                                    
                                    </p> -->

                                    <!-- <template v-for="itm in dt.pilihan_jawaban" style="font-size:12px;padding:0.4em; margin:0; color:black; border-bottom:1px solid #efefef;"> -->
                                        <!-- <p  style="font-size:12px; padding:0.4em; margin:0.2em 0 0.2em 0; color:black; border-bottom:1px solid #efefef; font-weight:500;">{{ itm.variable }}</p> -->
                                        <div class="uk-column-1-2@m" style="font-size:12px; padding:0 0.4em 0.4em 0.4em; margin:0.2em 0 0 0; color:black; border-bottom:0px solid #efefef; font-weight:500;">
                                            <p v-for="itm in dt.pilihan_jawaban" style="font-weight:400; font-size:12px; padding:0.4em; margin:0; color:black; ">
                                                <label class="uk-grid-small" uk-grid>
                                                    <input class="uk-radio uk-width-auto" type="radio" :name="dt.inform_map_id" :value="itm.variable" :checked="itm.is_default" style="color:black;margin:0 0 0 3px;"> 
                                                    <span class="uk-width-expand" style="display:inline-block;">{{ itm.variable }}</span>
                                                </label>                                    
                                            </p>
                                        </div>                                    
                                    <!-- </template> -->
                                </td>
                                <td style="font-size:12px; font-weight:500;color:black;">{{ dt.tipe_jawaban }}</td>
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
                                                    <input class="uk-radio uk-width-auto" type="radio" :name="itm.variable" :value="score.score" :checked="itm.is_default" style="color:black;margin:0 0 0 3px;"> 
                                                    <span class="uk-width-expand" style="display:inline-block;">{{ score.item_score }} <strong>({{ score.score }})</strong></span>
                                                </label>                                    
                                            </p>
                                        </div>                                    
                                    </template>
                                </td>
                                <!-- <td> -->
                                    <!-- <p v-for="itm in dt.pilihan_jawaban" style="font-size:12px;padding:0.4em; margin:0; color:black; border-bottom:1px solid #efefef;">
                                        <label><input class="uk-radio" type="radio" :name="dt.inform_map_id" :value="itm.variable" :checked="itm.is_default" style="color:black;margin:0 0 0 5px;"> {{ itm.variable }}</label>                                    
                                    </p> -->
                                <!-- </td> -->
                                <td style="font-size:12px; font-weight:500;color:black;">{{ dt.tipe_jawaban }}</td>
                            </template>

                            <template v-else-if="dt.tipe_jawaban == 'Number'">
                                <td colspan="2" style="font-size:12px; font-weight:500;color:black;">
                                    <p style="padding:0;margin:0;">{{ dt.pertanyaan }} <span v-if="dt.satuan">({{ dt.satuan }})</span></p>
                                    <p style="padding:0;margin:0;font-weight:400">{{ dt.deskripsi }}</p>
                                </td>
                                <td>
                                    <input type="number" class="uk-input uk-form-small">
                                </td>
                                <td style="font-size:12px; font-weight:500;color:black;">{{ dt.tipe_jawaban }}</td>
                            </template>
                            
                            <template v-else-if="dt.tipe_jawaban == 'FreeText'">
                                <td colspan="2" style="font-size:12px; font-weight:500;color:black;">
                                    <p style="padding:0;margin:0;">{{ dt.pertanyaan }}</p>
                                    <p style="padding:0;margin:0;font-weight:400">{{ dt.deskripsi }}</p>
                                </td>
                                <td>
                                    <textarea type="text" class="uk-textarea uk-form-small"></textarea>
                                </td>
                                <td style="font-size:12px; font-weight:500;color:black;">{{ dt.tipe_jawaban }}</td>
                            </template>
                            

                            <template v-else>
                                <td colspan="2" style="font-size:12px; font-weight:500;color:black;">
                                    <p style="padding:0;margin:0;">{{ dt.pertanyaan }}</p>
                                    <p style="padding:0;margin:0;font-weight:400">{{ dt.deskripsi }}</p>
                                </td>
                                <td></td>
                                <td style="font-size:12px; font-weight:500;color:black;">{{ dt.tipe_jawaban }}</td>
                            </template>


                            <!-- <td style="font-size:12px; font-weight:500;color:black;">
                                <p style="padding:0;margin:0;">{{ dt.pertanyaan }}</p>
                                <p style="padding:0;margin:0;font-weight:400">{{ dt.deskripsi }}</p>
                            </td>
                            <td></td>
                            <td v-if="dt.tipe_jawaban == 'Radio'">
                                <p v-for="itm in dt.pilihan_jawaban" style="font-size:12px;padding:0.4em; margin:0; color:black; border-bottom:1px solid #efefef;">
                                    <label><input class="uk-radio" type="radio" :name="dt.inform_map_id" :value="itm.variable" :checked="itm.is_default" style="color:black;margin:0 0 0 5px;"> {{ itm.variable }}</label>                                    
                                </p>
                            </td>
                            <td v-else-if="dt.tipe_jawaban == 'Number'">
                                Number
                            </td>
                            <td v-else-if="dt.tipe_jawaban == 'FreeText'">
                                Free text
                            </td>
                            <td v-else>
                                {{ dt.tipe_jawaban }}
                            </td>

                            <td style="font-size:12px; font-weight:500;color:black;">{{ dt.tipe_jawaban }}</td> -->
                            
                        </tr>
                    </tbody>
                </table>
            </div>            
        </div>
    </div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'questionaire-form',
    data(){
        return {
            idModal : 'questionaireForm',
            isUpdate : true,
            isLoading : false,
            titleForm : 'INFORMED CONSENT BARU',
            informed : {
                template_id : null,
                template_nama : null,
                deskripsi : null,
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
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        showModal() {
            UIkit.modal(`#${this.idModal}`).show();
        },

        closeModal() {
            UIkit.modal(`#${this.idModal}`).hide();
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