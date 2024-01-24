<template>
    <div>
        <header-page title="ITEM PEMERIKSAAN DAN PELAYANAN" subTitle="master data item tindakan, pemeriksaan, dan kamar"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableTindakan"
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
    name : 'list-tindakan',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
    },

    components : {
        headerPage,
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
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.editFunction },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'tindakan_nama', 
                    title : 'Nama', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.editFunction },
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
                    name : 'klasifikasi', 
                    title : 'Klasifikasi', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'is_paket', 
                    title : 'Paket', 
                    colType : 'boolean', 
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
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addFunction },
            ],
            searchUrl : '/acts'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('tindakan',['listTindakan','deleteTindakan']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addTindakan(){        
            this.CLR_ERRORS();
            this.$refs.formTindakan.newTindakan();        
        },

        refreshTable() {
            this.$refs.tableTindakan.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formTindakan.editTindakan(data.tindakan_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan tindakan/pemeriksaan ${data.tindakan_nama}. Lanjutkan ?`)){
                this.deleteTindakan(data.tindakan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableTindakan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>