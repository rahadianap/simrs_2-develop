<template>
    <div class="uk-container uk-container-large" style="padding:0;">
        <div class="uk-card1 uk-card-default1" style="padding:1em;border-radius:10px;min-height:400px;">
            <header-page title="TEMPLATE HASIL PATOLOGI ANATOMI" subTitle="master template hasil patologi anatomi"></header-page>
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            <div style="margin-top:1em;">
                <base-table ref="tableTemplatePatologi"
                    :columns = "tbColumns" 
                    :config = "configTable" 
                    :buttons = "buttons"
                    :urlSearch="searchUrl" 
                    v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="dataLists">
                            <row-list-patologi-template
                                v-for="dt in dataLists" 
                                :rowData="dt" 
                                :rowFunctions = rowFunctions>
                            </row-list-patologi-template>
                        </tbody>
                    </template>
                </base-table>
            </div>
        </div>
        <modal-new-patologi-template ref="modalNewPatologiTemplate" v-on:modalNewPatologiTemplateClosed="refreshData"></modal-new-patologi-template>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';

import ModalNewPatologiTemplate from '@/Pages/MasterData/TemplatePatologi/ModalNewPatologiTemplate.vue';
import RowListPatologiTemplate from '@/Pages/MasterData/TemplatePatologi/RowListPatologiTemplate.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        RowListPatologiTemplate,
        ModalNewPatologiTemplate,
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'REGULAR', },
            rowFunctions : [
                { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updatePatologiTemplate },
                { 'title':'Non-aktifkan', 'icon':'ban','callback':this.removePatologiTemplate },   
            ],
            tbColumns : [                                
                { name : 'pa_template_nama', title : 'Nama', colType : 'text', },
                { name : 'deskripsi', title : 'Deskripsi', colType : 'text', isSearchable : true, },
                { name : 'hasil', title : 'Hasil', colType : 'text', isSearchable : true, },
                { name : 'catatan', title : 'Catatan', colType : 'text', isSearchable : true, },                
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addPatologiTemplate },
            ],
            searchUrl : '/patologi/templates',
            dataLists : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapActions('patologiTemplate',['listPatologiTemplate','deletePatologiTemplate']),
        ...mapMutations(['CLR_ERRORS']),
        
        refreshData(){
            this.$refs.tableTemplatePatologi.retrieveData();
        },
        //refresh data row table
        updateListData(data){
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        addPatologiTemplate(){        
            this.CLR_ERRORS();
            this.$refs.modalNewPatologiTemplate.newData();
        },
        updatePatologiTemplate(data){        
            this.CLR_ERRORS();
            this.$refs.modalNewPatologiTemplate.editData(data);    
        },

        removePatologiTemplate(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus template hasil pemeriksaan ${data.pa_template_nama}. Lanjutkan ?`)){
                this.deletePatologiTemplate(data.pa_template_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableTemplatePatologi.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

    }
}
</script>