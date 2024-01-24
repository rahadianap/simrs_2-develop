<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="UNIT PELAYANAN" subTitle="master data unit kerja dan pelayanan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableUnitPelayanan"
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
                    name : 'unit_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'unit_nama', 
                    title : 'Nama', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'inisial', 
                    title : 'Inisial', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'group_unit', 
                    title : 'Group', 
                    colType : 'text', 
                },
                { 
                    name : 'kepala_unit', 
                    title : 'Kepala Unit', 
                    colType : 'text', 
                    isSearchable : true,
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
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/units'
         }
    },
    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('unitPelayanan',['listUnitPelayanan','deleteUnitPelayanan']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            //this.$refs.tableUnitPelayanan.retrieveData();
        },

        /**tampikan modal untuk membuat unit pelayanan baru */
        addData(){        
            this.CLR_ERRORS();
            //this.$refs.formUnitPelayanan.newUnitPelayanan();
            this.$router.push(`/master/unit/pelayanan/data/baru`);
        },

        refreshTable() {
            this.$refs.tableUnitPelayanan.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            //this.$refs.formUnitPelayanan.editUnitPelayanan(data.unit_id);
            this.$router.push(`/master/unit/pelayanan/data/${data.unit_id}`);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan unit pelayanan ${data.unit_nama}. Lanjutkan ?`)){
                this.deleteUnitPelayanan(data.unit_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableUnitPelayanan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>