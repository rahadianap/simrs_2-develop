<template>
    <!-- <div style="padding:0;margin:0;">         -->
        <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">            
            <!-- <div class="uk-width-auto@m">
                <button type="button" style="margin-top:0.2em; padding:0.5em;" @click.prevent="fnAddCallback">Tambah Asesmen</button>
            </div> -->
            <div class="uk-width-auto@m">
                <div class="uk-inline">
                    <button type="button" style="margin-top:0.2em;">General Consent</button>
                    <div uk-dropdown>
                        <ul class="uk-nav uk-dropdown-nav">
                            <li v-if="informedListUnit" v-for="item in informedListUnit">
                                <a href="#" style="color:#333;" @click.prevent="showInformedTemplate(item.template_id)">{{ item.template_nama }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding:0 0.5em 0.2em 0.5em;">
            <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                <thead>
                    <th class="tb-header-reg" style="width:20px;text-align:center;color:white;"></th>
                    <th class="tb-header-reg" style="width:200px;text-align:left;color:white;">ID</th>
                    <th class="tb-header-reg" style="width:120px;text-align:left;color:white;">Tanggal</th>
                    <th class="tb-header-reg" style="text-align:center;color:white;">Inform</th>
                    <th class="tb-header-reg" style="text-align:left;color:white;">Dokter</th>
                    <th class="tb-header-reg" style="width:120px;text-align:center;color:white;">...</th>
                </thead>
                <tbody>
                    <row-list-inform 
                        v-if="poliInforms" 
                        v-for="dt in poliInforms"
                        :rowData = "dt"
                        :linkCallback = "editInform">
                    </row-list-inform>
                    <tr v-else>
                        <td colspan="7" style="font-style:italic;font-size:12px;color:black;">data tidak ditemukan</td>
                    </tr>
                </tbody>
            </table>            
        </div>
        <questionaire-form ref="questionaireForm" v-on:formQuestionaireClosed="fnRefresh"></questionaire-form>
    <!-- </div> -->
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowListInform from '@/Pages/RawatJalan/Pemeriksaan/ListPemeriksaan/RowListInform.vue';
import QuestionaireForm from '@/Pages/RawatJalan/Pemeriksaan/components/QuestionaireForm.vue';

export default {
    name : 'sub-list-inform',
    props : {
        fnAddCallback : { type:Function, required:true },
        fnEditCallback : { type:Function, required:true },
        fnRefresh  : { type:Function, required:false },
    },
    components : { 
        RowListInform,
        QuestionaireForm, 
    },
    computed : {
        ...mapGetters('rawatJalan',['poliTransaction','poliExamination','poliAssesments','poliInforms']),       
        ...mapGetters('informedConsent',['informedListUnit']),
    },
    methods : {
        showInformedTemplate(data){
            this.$refs.questionaireForm.newData(data, this.poliTransaction);
        },

        editInform(data) {
            this.$refs.questionaireForm.editData(data);
        }
    }
}
</script>