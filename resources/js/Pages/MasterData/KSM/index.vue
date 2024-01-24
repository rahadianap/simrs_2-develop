<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="KELOMPOK STAFF MEDIS" subTitle="master kelompok staff medis"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKsm"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-ksm ref="formKsm" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-ksm>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormKsm from '@/Pages/MasterData/KSM/components/FormKsm.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormKsm,
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
                    name : 'smf_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'smf_nama', 
                    title : 'Nama', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ], 
            buttons : [
                { title : 'SMF Baru', icon:'plus-circle', 'callback' : this.addSMF },
            ],
            searchUrl : '/smf'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('ksm',['listKsm','deleteKsm']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat dokter baru */
        addSMF(){        
            this.CLR_ERRORS();
            this.$refs.formKsm.newKsm();        
        },

        refreshTable() {
            this.$refs.tableKsm.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formKsm.editKsm(data.smf_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan KSM ${data.smf_nama}. Lanjutkan ?`)){
                this.deleteKsm(data.smf_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKsm.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>