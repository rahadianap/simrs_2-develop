<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER SIGNA MEDIS" subTitle="master signa pemakaian item medis"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableSigna"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-signa ref="formSigna" v-on:closed="refreshTable"></form-signa>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormSigna from '@/Pages/MasterData/Signa/components/FormSigna.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormSigna,
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
                    name : 'signa', 
                    title : 'Signa', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },   
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
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addSigna },
            ],
            searchUrl : '/signas'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('signa',['deleteSigna']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addSigna(){        
            this.CLR_ERRORS();
            this.$refs.formSigna.newSigna();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formSigna.editSigna(data.signa_id);
        },

        refreshTable() {
            this.$refs.tableSigna.retrieveData();
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus signa ${data.signa}. Lanjutkan ?`)){
                this.deleteSigna(data.signa_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableSigna.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>