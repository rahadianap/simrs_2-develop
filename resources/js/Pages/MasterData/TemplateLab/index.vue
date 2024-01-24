<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <ul id="switcherTemplate" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">Item1</a></li>
            <li><a href="#">Item2</a></li>
        </ul>

        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <div>
                    <header-page title="TEMPLATE HASIL LABORATORIUM" subTitle="master data pemeriksaan dan template hasil laboratorium"></header-page>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:1em;">
                        <base-table ref="tableTindakanLab"
                            :columns = "tbColumns" 
                            :config="configTable" 
                            :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                            <template v-slot:tbl-body>
                                <tbody v-if="dataLists" style="min-weight:50vh;">
                                    <row-template-lab
                                        v-for="dt in dataLists" 
                                        :rowData="dt"
                                        :linkCallback = editTemplate >
                                    </row-template-lab>
                                </tbody>
                            </template>
                        </base-table>
                    </div>
                </div>
            </li>
            <li style="padding:0;margin:0;">
                <form-template-lab ref="formTemplateLab" v-on:formLabClosed="formLabClosed"></form-template-lab>
            </li>
        </ul>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormTemplateLab from '@/Pages/MasterData/TemplateLab/components/FormTemplateLab.vue';
import RowTemplateLab from '@/Pages/MasterData/TemplateLab/components/RowTemplateLab.vue';

export default {
    components : {
        headerPage,
        FormTemplateLab,
        RowTemplateLab,
        BaseTable,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            tbColumns : [                
                { 
                    name : 'tindakan_id', 
                    title : 'ID', 
                    colType : 'link',
                    linkCallback : this.editTemplate,
                },
                { 
                    name : 'tindakan_nama', 
                    title : 'Nama', 
                    colType : 'link',
                    linkCallback : this.editTemplate,
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'klasifikasi', 
                    title : 'Klasifikasi', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean',
                },
                
            ],
            searchUrl : '/acts/LAB',
            dataLists : null,
         }
    },
    methods : {
        ...mapActions('tindakan',['listTindakan','deleteTindakan']),
        ...mapMutations(['CLR_ERRORS']),
        
        updateListData(data) {
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        formLabClosed(){
            UIkit.switcher('#switcherTemplate').show(0);
        },

        editTemplate(data){
            if(data){
                this.$refs.formTemplateLab.editTemplateLab(data);
                UIkit.switcher('#switcherTemplate').show(1);
            }
        }
    }
}
</script>