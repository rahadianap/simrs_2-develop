<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="WILAYAH" subTitle="master data wilayah propinsi"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tablePropinsi"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-propinsi ref="formPropinsi" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-propinsi>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormPropinsi from '@/Pages/MasterData/Wilayah/components/FormPropinsi.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormPropinsi,
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
                    name : 'propinsi_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Lihat Daftar Kabupaten', 'icon':'file','callback':this.listKabupaten },
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'propinsi', 
                    title : 'Propinsi', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Lihat Daftar Kabupaten', 'icon':'file','callback':this.listKabupaten },
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'is_prioritas', 
                    title : 'Prioritas', 
                    colType : 'boolean', 
                    isSearchable : true,
                },  
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/provinces'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('propinsi',['deletePropinsi']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan daftar kode RL */
        listKabupaten(data){        
            this.CLR_ERRORS();
            this.$router.push(`/master/wilayah/kabupaten/${data.propinsi_id}`);
        },

        addData(){
            this.CLR_ERRORS();
            this.$refs.formPropinsi.newPropinsi();
        },

        refreshTable() {
            this.$refs.tablePropinsi.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formPropinsi.editPropinsi(data.propinsi_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan propinsi ${data.propinsi}. Lanjutkan ?`)){
                this.deletePropinsi(data.propinsi_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePropinsi.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>