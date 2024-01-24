<template>
    <div class="uk-container uk-container-large" style="padding:0;">
        <div class="uk-card1 uk-card-default1" style="padding:1em;border-radius:10px;min-height:400px;">
            <header-page title="TEMPLATE HASIL RADIOLOGI" subTitle="master template hasil radiologi"></header-page>
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            <div style="margin-top:1em;">
                <base-table ref="tableTemplateRad"
                    :columns = "tbColumns" 
                    :config = "configTable" 
                    :buttons = "buttons"
                    :urlSearch="searchUrl" 
                    v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="dataLists">
                            <row-list-rad-template
                                v-for="dt in dataLists" 
                                :rowData="dt" 
                                :rowFunctions = rowFunctions>
                            </row-list-rad-template>
                        </tbody>
                    </template>
                </base-table>
            </div>
        </div>
        <modal-new-rad-template ref="modalNewRadTemplate" v-on:modalNewRadTemplateClosed="refreshData"></modal-new-rad-template>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';

import ModalNewRadTemplate from '@/Pages/MasterData/TemplateRadiologi/ModalNewRadTemplate.vue';
import RowListRadTemplate from '@/Pages/MasterData/TemplateRadiologi/RowListRadTemplate.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        RowListRadTemplate,
        ModalNewRadTemplate,
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'REGULAR', },
            rowFunctions : [
                { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateRadTemplate },
                { 'title':'Non-aktifkan', 'icon':'ban','callback':this.removeRadTemplate },   
            ],
            tbColumns : [                                
                { name : 'rad_template_nama', title : 'Nama', colType : 'text', },
                { name : 'deskripsi', title : 'Deskripsi', colType : 'text', isSearchable : true, },
                { name : 'kesan', title : 'Kesan', colType : 'text', isSearchable : true, },
                { name : 'uraian', title : 'Uraian', colType : 'text', isSearchable : true, },
                { name : 'catatan', title : 'Catatan', colType : 'text', isSearchable : true, },                
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addRadTemplate },
            ],
            searchUrl : '/radiology/templates',
            dataLists : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapActions('radTemplate',['listRadTemplate','deleteRadTemplate']),
        ...mapMutations(['CLR_ERRORS']),
        
        refreshData(){
            this.$refs.tableTemplateRad.retrieveData();
        },
        //refresh data row table
        updateListData(data){
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        addRadTemplate(){        
            this.CLR_ERRORS();
            this.$refs.modalNewRadTemplate.newData();
        },
        updateRadTemplate(data){        
            this.CLR_ERRORS();
            this.$refs.modalNewRadTemplate.editData(data);    
        },

        removeRadTemplate(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus template hasilpemeriksaan ${data.rad_template_nama}. Lanjutkan ?`)){
                this.deleteRadTemplate(data.rad_template_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableTemplateRad.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

    }
}
</script>