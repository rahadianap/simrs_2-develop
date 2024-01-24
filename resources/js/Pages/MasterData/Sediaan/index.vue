<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER SEDIAAN PRODUK" subTitle="master data bentuk sediaan produk"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableSediaan"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-sediaan ref="formSediaan" v-on:closed="refreshTable"></form-sediaan>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormSediaan from '@/Pages/MasterData/Sediaan/components/FormSediaan.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormSediaan,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'SIMPLE', 
            },
            tbColumns : [                
                { 
                    name : 'sediaan', 
                    title : 'Sediaan', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData }, 
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addSediaan },
            ],
            searchUrl : '/forms'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('sediaan',['deleteSediaan']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addSediaan(){        
            this.CLR_ERRORS();
            this.$refs.formSediaan.newSediaan();
        },

        refreshTable() {
            this.$refs.tableSediaan.retrieveData();
        },
        
        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formSediaan.editSediaan(data.sediaan_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus sediaan ${data.sediaan}. Lanjutkan ?`)){
                this.deleteSediaan(data.sediaan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableSediaan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>