<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <ul id="switcherTindakanLab" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">Item1</a></li>
            <li><a href="#">Item2</a></li>
        </ul>

        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <div>
                    <header-page 
                        title="MASTER PEMERIKSAAN RADIOLOGI" 
                        subTitle="master data pemeriksaan radiologi">
                    </header-page>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:1em;">
                        <base-table ref="tableTindakanLab"
                            :columns = "tbColumns" 
                            :config = "configTable" 
                            :buttons = "buttons"
                            :urlSearch="searchUrl" 
                            v-on:updateListDataTable="updateListData">
                            <template v-slot:tbl-body>
                                <tbody v-if="dataLists">
                                    <row-list-tindakan-rad 
                                        v-for="dt in dataLists" 
                                        :rowData="dt" 
                                        :rowFunctions = rowFunctions>
                                    </row-list-tindakan-rad>
                                </tbody>
                            </template>
                        </base-table>
                    </div>
                </div>
            </li>
            <li style="padding:0;margin:0;">
                <form-tindakan-rad 
                    ref="formTindakanRad" 
                    v-on:formTindakanClosed="formClosed">
                </form-tindakan-rad>
            </li>
        </ul>
        <div>
            <modal-new-pemeriksaan-rad 
                ref="modalNewPemeriksaanRad" 
                v-on:addPemeriksaanRadSuccess="updatePemeriksaan">
            </modal-new-pemeriksaan-rad>
        </div>

    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowListTindakanRad from "@/Pages/Penunjang/Radiologi/MasterPemeriksaan/components/RowListTindakanRad.vue";

import FormTindakanRad from '@/Pages/Penunjang/Radiologi/MasterPemeriksaan/FormTindakanRad';
import ModalNewPemeriksaanRad from '@/Pages/Penunjang/Radiologi/MasterPemeriksaan/components/ModalNewPemeriksaanRad.vue';

export default {
    components : {
        FormTindakanRad,
        ModalNewPemeriksaanRad,
        headerPage,
        BaseTable,
        RowListTindakanRad
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'REGULAR', },
            rowFunctions : [
                { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updatePemeriksaan },
                { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
            ],
            tbColumns : [                
                { name : 'tindakan_id', title : 'ID', colType : 'text', },
                { name : 'tindakan_nama', title : 'Nama', colType : 'text', },
                { name : 'deskripsi', title : 'Deskripsi', colType : 'text', isSearchable : true, },
                { name : 'klasifikasi', title : 'Klasifikasi', colType : 'text', isSearchable : true, },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addTindakan },
            ],
            searchUrl : '/radiology/inspections',
            dataLists : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapActions('tindakan',['listTindakan','deleteTindakan']),
        ...mapMutations(['CLR_ERRORS']),
        
        //refresh data row table
        updateListData(data){
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        addTindakan(){        
            this.CLR_ERRORS();
            this.$refs.modalNewPemeriksaanRad.showModal();     
        },
        updatePemeriksaan(data){        
            this.CLR_ERRORS();
            this.$refs.formTindakanRad.editTindakan(data.tindakan_id); 
            UIkit.switcher('#switcherTindakanLab').show(1);       
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan pemeriksaan ${data.tindakan_nama}. Lanjutkan ?`)){
                this.deleteTindakan(data.tindakan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableTindakanLab.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        formClosed(){
            this.$refs.tableTindakanLab.retrieveData();
            UIkit.switcher('#switcherTindakanLab').show(0);
        }
    }
}
</script>