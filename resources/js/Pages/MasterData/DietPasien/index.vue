<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER DIET" subTitle="master data diet"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableDietPasien"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-diet-pasien ref="formDietPasien" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-diet-pasien>
        <form-diet-menu ref="formDietMenu" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-diet-menu>
    </div>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormDietPasien from '@/Pages/MasterData/DietPasien/components/FormDietPasien.vue';
    import FormDietMenu from '@/Pages/MasterData/DietPasien/components/FormDietMenu.vue';
    
    export default {
        components : {
            headerPage,
            BaseTable,
            FormDietPasien,
            FormDietMenu
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isExpanded : false,
                configTable : {                
                    isExpandable : false,
                    filterType : 'ADVANCED', 
                },
                tbColumns : [                
                    { 
                        name : 'diet_id', 
                        title : 'ID', 
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Mapping Menu', 'icon':'file-edit','callback':this.mappingMenu },
                            { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                            { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                        ],
                    },
                    { 
                        name : 'nama_diet', 
                        title : 'Nama', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'catatan', 
                        title : 'Catatan', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'komplikasi', 
                        title : 'Komplikasi', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'is_semua_kelas', 
                        title : 'is_semua_kelas', 
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Semua Kelas' },
                            { value:1, text:'Kelas Tertentu' },
                        ],
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
                    { title : 'Diet Baru', icon:'plus-circle', 'callback' : this.addDiet },
                ],
                searchUrl : '/diet'
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('dietPasien',['listDiet','deleteDiet']),
            ...mapMutations(['CLR_ERRORS']),
    
            /**tampikan modal untuk membuat diet baru */
            addDiet(){        
                this.CLR_ERRORS();
                this.$refs.formDietPasien.newDiet();        
            },
    
            refreshTable() {
                this.$refs.tableDietPasien.retrieveData();
            },
    
            updateData(data){
                this.CLR_ERRORS();
                this.$refs.formDietPasien.editDiet(data.diet_id);
            },

            mappingMenu(data) {
                this.CLR_ERRORS();
                this.$refs.formDietMenu.newDietMenu(data.diet_id);
            },
    
            deleteData(data) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menonaktifkan data ${data.nama_diet}. Lanjutkan ?`)){
                    this.deleteDiet(data.diet_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableDietPasien.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }
        }
    }
</script>