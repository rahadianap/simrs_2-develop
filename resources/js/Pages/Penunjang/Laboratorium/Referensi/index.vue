<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <ul id="mainTabItemLab" uk-tab style="padding:0;" class="uk-hidden">
            <li><a href="#" style="font-size:14px;font-weight:500;">ITEM 1</a></li>
            <li><a href="#" style="font-size:14px;font-weight:500;">ITEM 2</a></li>           
        </ul>
        <ul class="uk-switcher">
            <li>
                <div>
                    <header-page title="ITEM PEMERIKSAAN LABORATORIUM" subTitle="master data item pemeriksaan laboratorium"></header-page>
                    <div style="margin-top:1em;">
                        <div class="uk-card" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                            <ul uk-tab style="padding:0;">
                                <li><a href="#" style="font-size:14px;font-weight:500;">ITEM PEMERIKSAAN</a></li>
                                <li><a href="#" style="font-size:14px;font-weight:500;">KLASIFIKASI</a></li>
                                <li><a href="#" style="font-size:14px;font-weight:500;">GROUP NILAI NORMAL</a></li>
                                <li><a href="#" style="font-size:14px;font-weight:500;">SATUAN HASIL</a></li>                    
                            </ul>
                            <ul class="uk-switcher" style="padding:0;margin:0;">
                                <li>
                                    <div>
                                        <!-- <header-page title="TINDAKAN MEDIS" subTitle="master data tindakan dan pemeriksaan di poli, IGD dan rawat inap"></header-page> -->
                                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                                            <p>{{ errors.invalid }}</p>
                                        </div>
                                        <div style="margin-top:1em;">
                                            <base-table ref="tableItemLab"
                                                :columns = "tbColumns" 
                                                :config = "configTable"
                                                :buttons = "buttons"
                                                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                                                <template v-slot:tbl-body>
                                                    <tbody v-if="tableDatas">
                                                        <row-table-item-lab v-for="dt in tableDatas" :rowData="dt" :rowFunctions = rowFunctions></row-table-item-lab>
                                                    </tbody>
                                                </template>
                                            </base-table>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <klasifikasi-item-lab ref="klasifikasiItemLab"></klasifikasi-item-lab>                        
                                </li>
                                <li>
                                    <group-nilai-normal ref="groupLabNormal"></group-nilai-normal>                        
                                </li>
                                <li>                        
                                    <satuan-nilai-normal ref="satuanLabNormal"></satuan-nilai-normal>
                                </li>
                            </ul>
                        </div>
                    </div>        
                </div>
            </li>
            <li>
                <form-item-lab ref="formItemLab" v-on:closed="formItemClosed" v-on:succeed="formItemClosed"></form-item-lab>
            </li>
        </ul>
        
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormItemLab from '@/Pages/Penunjang/Laboratorium/Referensi/components/FormItemLab.vue';

import GroupNilaiNormal from '@/Pages/Penunjang/Laboratorium/Referensi/components/GroupNilaiNormal.vue';
import SatuanNilaiNormal from '@/Pages/Penunjang/Laboratorium/Referensi/components/SatuanNilaiNormal.vue';
import KlasifikasiItemLab from '@/Pages/Penunjang/Laboratorium/Referensi/components/KlasifikasiItemLab.vue';
import ItemLaboratorium from '@/Pages/Penunjang/Laboratorium/Referensi/components/ItemLaboratorium.vue';
import RowTableItemLab from '@/Pages/Penunjang/Laboratorium/Referensi/components/RowTableItemLab.vue';

export default {
    components : {
        headerPage,
        GroupNilaiNormal,
        SatuanNilaiNormal,
        ItemLaboratorium,
        BaseTable,
        FormItemLab,
        KlasifikasiItemLab,
        RowTableItemLab,
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : true,
                filterType : 'REGULAR', 
            },            
            rowFunctions : [
                    { 'title':'Ubah Data', 'icon':'file-edit','callback':this.editData },
                    { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                ],
            tbColumns : [                
                { 
                    name : 'lab_hasil_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    width : "120px",
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.editData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'klasifikasi', 
                    title : 'Klasifikasi', 
                    colType : 'text', 
                    width : "150px",
                    isSearchable : true,
                },
                { 
                    name : 'sub_klasifikasi', 
                    title : 'Sub Klasifikasi', 
                    colType : 'text',
                    isSearchable : true,
                },
                { 
                    name : 'hasil_nama', 
                    title : 'Nama', 
                    colType : 'text', 
                },
                { 
                    name : 'no_urut', 
                    title : 'No Urut', 
                    colType : 'text', 
                    textAlign : 'center',
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    width : "50px",
                    textAlign : "center",
                    options : [
                        { value:0, text:'Aktif' },
                        { value:1, text:'Nonaktif' },
                    ]
                },
                
            ], 
            buttons : [
                { title : 'Item Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/labs/items/results',
            tableDatas : null,
         }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refPenunjang',['groupLabNormalRefs','satuanLabNormalRefs','klasifikasiItemLabRefs']),
    },    
    mounted() {
        this.initialize();
    },
    methods :{
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('refPenunjang',['listRefPenunjang']),
        ...mapActions('itemLab',['deleteItemLab']),

        updateListData(data){
            if(data) {
                if(data.data) { this.tableDatas = data.data; }
                else { this.tableDatas = null; }
            }
            else { this.tableDatas = null; }
        },

        initialize(){
            this.listRefPenunjang().then((response) => {
                if (response.success == true) { this.checkData(); }
                else { alert (response.message) }
            });
        },

        checkData(){
            this.$refs.groupLabNormal.refreshData(this.groupLabNormalRefs);
            this.$refs.satuanLabNormal.refreshData(this.satuanLabNormalRefs);
            this.$refs.klasifikasiItemLab.refreshData(this.klasifikasiItemLabRefs);
        },

        addData(){
            this.$refs.formItemLab.newItemLab();
            UIkit.switcher('#mainTabItemLab').show(1);
        },

        editData(data){
            this.$refs.formItemLab.editItemLab(data.lab_hasil_id);
            UIkit.switcher('#mainTabItemLab').show(1);
        },
        
        formItemClosed(){
            UIkit.switcher('#mainTabItemLab').show(0);
            this.$refs.tableItemLab.retrieveData();
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan item hasil ${data.hasil_nama}. Lanjutkan ?`)){
                this.deleteItemLab(data.lab_hasil_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableItemLab.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }

    }
}
</script>