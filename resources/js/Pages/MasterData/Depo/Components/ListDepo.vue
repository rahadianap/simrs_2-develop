<template>
    <div class="uk-container-large" style="padding:1em;">
        <header-page title="DEPO PERSEDIAAN" subTitle="master data lokasi persediaan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableDepo"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';

export default {
    name : 'list-depo',
    props : {
        depoMapFunction : { type: Function, required:false, default:null, },
        addFunction : { type: Function, required:false, default:null, },
        updateFunction : { type: Function, required:false, default:null, },
    },
    components : {
        headerPage,
        BaseTable,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('depo',['depoLists']),
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
                    name : 'depo_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data Depo', 'icon':'file-edit','callback':this.updateFunction },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'depo_nama', 
                    title : 'Nama', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text',
                    isSearchable  : true,
                },
                { 
                    name : 'lokasi', 
                    title : 'Lokasi', 
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
            buttons : [
                { title : 'Depo Baru', icon:'plus-circle', 'callback' : this.addFunction },
            ],
            searchUrl : '/depos'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('depo',['listDepo','deleteDepo']),
        ...mapMutations(['CLR_ERRORS']),

        refreshTable() {
            this.$refs.tableDepo.retrieveData();
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan depo persediaan ${data.depo_nama}. Lanjutkan ?`)){
                this.deleteDepo(data.depo_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableDepo.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>