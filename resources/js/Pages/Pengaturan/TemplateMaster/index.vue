<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="TEMPLATE MASTER DATA" subTitle="template input master data"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table 
                ref="tableTemplate"
                :columns = "tbColumns" 
                :config = "configTable"
                :urlSearch = "searchUrl">
                <template v-slot:form-filter="slotProps" >
                    <template-form-filter></template-form-filter>
                </template>
            </base-table>
        </div>
        <form-template ref="formTemplate" v-on:saveSucceed="refreshTable"></form-template>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import TemplateFormFilter from '@/Pages/Pengaturan/TemplateMaster/components/TemplateFormFilter.vue';
import axios from '@/Stores/httpReq';
import FormTemplate from '@/Pages/Pengaturan/TemplateMaster/components/FormTemplate.vue'

export default {
    components : {
        headerPage,
        BaseTable,
        TemplateFormFilter,
        FormTemplate,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('role',['roleLists']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'ADVANCED', 
            },
            tbColumns : [                
                { 
                    name : 'template_id', 
                    title : 'ID', 
                    colType : 'text',
                },
                { 
                    name : 'template_nama', 
                    title : 'Nama', 
                    colType : 'link',
                    linkCallback: this.exportExcel,
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text',
                    isSearchable  : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    options : [
                        { value:0, text:'Aktif' },
                        { value:1, text:'Nonaktif' },
                    ]
                },
            ],
            buttons : [
                { title : 'Template Baru', icon:'plus-circle', 'callback' : this.addTemplate },
            ],
            searchUrl : '/templatemaster'
        }
    },
    mounted() {
    },

    methods : {
        ...mapActions('templateMaster',['exportCoa','listTemplate']),
        ...mapMutations(['CLR_ERRORS']),

        refreshTable() {
            this.$refs.tableTemplate.retrieveData();
        },

        addRole(){        
            this.CLR_ERRORS();
            this.$refs.formRole.newRole();        
        },

        exportExcel(data) {
            this.CLR_ERRORS();
            let url = data.template_url;
            axios.get(url, {responseType: 'blob'})
            .then(response => {
                const blob = new Blob([response.data], { type: 'application/pdf' })
                const link = document.createElement('a')
                link.href = URL.createObjectURL(blob)
                link.download = data.template_nama + '.xlsx'
                link.click()
                URL.revokeObjectURL(link.href)
            })
            .catch((error) => {
                this.$store.commit('SET_ERRORS', { invalid: error }, { root: true });
            })
        }, 
    }
}
</script>