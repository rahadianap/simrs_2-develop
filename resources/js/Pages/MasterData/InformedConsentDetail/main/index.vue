<template>
    <div class="uk-container uk-container-large" style="padding:0 1em 1em 1em;">
        <!-- <header-page title="MASTER PERTANYAAN" subTitle="pertanyaan untuk informed consent"></header-page> -->
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:0;">
            <base-table ref="tableInformedDetail"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl"
                v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="detailLists">
                        <row-inform-detail v-for="dt in detailLists" :rowData="dt" :rowFunctions = rowFunctions></row-inform-detail>
                    </tbody>
                </template>
            </base-table>
        </div>
        <form-informed-consent-detail ref="formDetail" v-on:closed="refreshTable"></form-informed-consent-detail>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormInformedConsentDetail from '@/Pages/MasterData/InformedConsentDetail/components/FormInformedConsentDetail.vue';
import RowInformDetail from '@/Pages/MasterData/InformedConsentDetail/main/RowInformDetail.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormInformedConsentDetail,
        RowInformDetail,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('informedConsentDetail',['informedDetailLists']),
    },
    data() {
        return {
            isExpanded : false,
            detailLists : [],
            configTable : {
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            tbColumns : [
                { 
                    name : 'inform_detail_id', 
                    title : 'ID', 
                    colType : 'text', 
                },
                { 
                    name : 'pertanyaan', 
                    title : 'Pertanyaan', 
                    colType : 'linkmenus', 
                    isSearchable : true,
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                    ],
                },
                { 
                    name : 'tipe_jawaban', 
                    title : 'Tipe', 
                    colType : 'text', 
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text', 
                },
                { 
                    name : 'is_mandatory', 
                    title : 'Mandatory', 
                    colType : 'text', 
                },
                { 
                    name : 'is_tanda_vital', 
                    title : 'Tanda Vital', 
                    colType : 'text', 
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
            rowFunctions : [
                { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
            ], 
            buttons : [
                { title : 'Tambah', icon:'plus-circle', 'callback' : this.addPertanyaan },
            ],
            searchUrl : '/informed/detail',
        }
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapActions('informedConsentDetail',['listInformedDetail','deleteInformedDetail']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let param = {per_page:'ALL'};
        },

        updateListData(data){
            if(data) {
                this.detailLists = data.data;
            }
        },

        addPertanyaan(){
            this.CLR_ERRORS();
            this.$refs.formDetail.newDetail();
        },

        refreshTable() {
            this.$refs.tableInformedDetail.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formDetail.editDetail(data.inform_detail_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan pertanyaan ${data.inform_detail_id}. Lanjutkan ?`)){
                this.deleteInformedDetail(data.inform_detail_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableInformedDetail.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },
    }
}
</script>