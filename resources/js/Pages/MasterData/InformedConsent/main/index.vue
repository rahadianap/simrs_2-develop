<template>
    <div>
        <header-page title="INFORMED CONSENT" subTitle="template master informed consent"></header-page>
        <div style="padding:0;margin:1em 0 0 0;background-color:#fff;">
            <ul id="switcherPasienPoli" class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0;margin:0;">
                <li style="padding:0;"  :class=" tabName.name == 'listTemplateInformed' ? 'uk-active' : ''">
                    <div>
                        <router-link :to = "{ name:'listTemplateInformed' }"><h5>Template</h5></router-link>
                    </div>
                </li>
                <li style="padding:0;"  :class=" tabName.name == 'listTemplateInformedItem' ? 'uk-active' : ''">
                    <div>
                        <router-link :to = "{ name:'listTemplateInformedItem' }"><h5>Item Pertanyaan</h5></router-link>
                    </div>
                </li>       
                <li style="padding:0;"  :class=" tabName.name == 'listTemplateInformedUnit' ? 'uk-active' : ''">
                    <div>
                        <router-link :to = "{ name:'listTemplateInformedUnit' }"><h5>Pemetaan Unit Pengguna</h5></router-link>
                    </div>
                </li>       
                         
            </ul>
            <div style="padding:1em;min-height:40vh;">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowMasterInformConsent from '@/Pages/MasterData/InformedConsent/main/RowMasterInformConsent.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        RowMasterInformConsent
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('informedConsent',['informedLists']),
    },
    data() {
        return {
            isExpanded : false,
            templateLists : null,
            tabName : 'listTemplateInformed',
            
            configTable : {
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            tbColumns : [
                { 
                    name : 'template_nama', 
                    title : 'Nama', 
                    colType : 'text', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                    ],
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Status', 
                    colType : 'boolean', 
                    options : [
                        { value:0, text:'Aktif' },
                        { value:1, text:'Nonaktif' },
                    ]
                },
                
            ], 
            buttons : [
                { title : 'Buat Baru', icon:'plus-circle', 'callback' : this.addTemplate },
            ],

            rowFunctions : [
                { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
            ],
            searchUrl : '/informed',
        }
    },
    watch:{
        'tabName': function(newVal, oldVal) {
            this.tabName = newVal;
        },
    },
    created() {
        this.tabName = this.$router.currentRoute;
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapActions('informedConsent',['listInformed','deleteInformed']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let param = {per_page:'ALL'};
        },
        updateListData(dt) {
            if(dt) { this.templateLists = dt.data; }
        },
        /**tampikan modal untuk membuat template informed consent baru */
        addTemplate(){
            this.CLR_ERRORS();
            this.$router.push(`/master/informed/baru`);
        },

        masterPertanyaan(){
            this.CLR_ERRORS();
            this.$router.push(`/master/informedDetail`);
        },

        refreshTable() {
            this.$refs.tableInformed.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$router.push(`/master/informed/${data.template_id}`);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan template ${data.template_nama}. Lanjutkan ?`)){
                this.deleteInformed(data.template_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableInformed.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },
    }
}
</script>