<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="SPESIALISASI" subTitle="master data spesialisasi dokter"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableSpesialisasi"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-spesialisasi ref="formSpesialis" v-on:closed="refreshTable"></form-spesialisasi>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormSpesialisasi from '@/Pages/MasterData/Spesialisasi/components/FormSpesialisasi.vue'

export default {
    components : {
        headerPage,
        BaseTable,
        FormSpesialisasi,
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
                    name : 'spesialisasi_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'spesialisasi', 
                    title : 'Spesialisasi', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addSpesialisasi },
            ],
            searchUrl : '/specializations'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('spesialisasi',['deleteSpesialisasi']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addSpesialisasi(){        
            this.CLR_ERRORS();
            this.$refs.formSpesialis.newSpesialisasi();
        },

        refreshTable() {
            this.$refs.tableSpesialisasi.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formSpesialis.editSpesialisasi(data.spesialisasi_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus spesialisasi ${data.spesialisasi}. Lanjutkan ?`)){
                this.deleteSpesialisasi(data.spesialisasi_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableSpesialisasi.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>