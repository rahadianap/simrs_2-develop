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
                        title="MASTER PEMERIKSAAN LABORATORIUM" 
                        subTitle="master data pemeriksaan laboratorium dan hasil">
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
                                    <row-list-tindakan-lab 
                                        v-for="dt in dataLists" 
                                        :rowData="dt" 
                                        :rowFunctions = rowFunctions>
                                    </row-list-tindakan-lab>
                                </tbody>
                            </template>
                        </base-table>
                    </div>
                </div>
            </li>
            <li style="padding:0;margin:0;">
                <form-tindakan-lab 
                    ref="formTindakanLab" 
                    v-on:formTindakanClosed="formClosed">
                </form-tindakan-lab>
            </li>
        </ul>
        <div>
            <modal-new-pemeriksaan-lab 
                ref="modalNewPemeriksaanLab" 
                v-on:addPemeriksaanLabSuccess="updatePemeriksaan">
            </modal-new-pemeriksaan-lab>
        </div>

    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowListTindakanLab from "@/Pages/Penunjang/Laboratorium/MasterPemeriksaan/components/RowListTindakanLab.vue";

import FormTindakanLab from '@/Pages/Penunjang/Laboratorium/MasterPemeriksaan/FormTindakanLab';
import ModalNewPemeriksaanLab from '@/Pages/Penunjang/Laboratorium/MasterPemeriksaan/components/ModalNewPemeriksaanLab.vue';

export default {
    components : {
        FormTindakanLab,
        ModalNewPemeriksaanLab,        
        headerPage,
        BaseTable,
        RowListTindakanLab
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : true, filterType : 'REGULAR', },
            rowFunctions : [
                { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updatePemeriksaan },
                { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
            ],
            tbColumns : [                
                { name : 'tindakan_id', title : 'ID', colType : 'text', },
                { name : 'tindakan_nama', title : 'Nama', colType : 'text', },
                { name : 'deskripsi', title : 'Deskripsi', colType : 'text', isSearchable : true, },
                { name : 'klasifikasi', title : 'Klasifikasi', colType : 'text', isSearchable : true, },
                { name : 'is_paket', title : 'Paket', colType : 'boolean', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
                { name : 'created_at', title : 'Tgl Dibuat', colType : 'date', },
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addTindakan },
            ],
            searchUrl : '/labs/inspections',
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
            this.$refs.modalNewPemeriksaanLab.showModal();     
        },
        updatePemeriksaan(data){        
            this.CLR_ERRORS();
            this.$refs.formTindakanLab.editTindakan(data.tindakan_id); 
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