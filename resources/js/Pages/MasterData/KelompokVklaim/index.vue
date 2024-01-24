<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="KELOMPOK V-CLAIM BPJS" subTitle="master kelompok vclaim BPJS"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKelompokVclaim"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-kelompok-vclaim ref="formKelompokVclaim" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-kelompok-vclaim>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import formKelompokVclaim from '@/Pages/MasterData/KelompokVklaim/components/FormKelompokVclaim.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        formKelompokVclaim,
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
                    name : 'kelompok_vclaim_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData }, 
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },            
                { 
                    name : 'kelompok_vclaim', 
                    title : 'Nama', 
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
                },   
                { 
                    name : 'vclaim_label', 
                    title : 'Label', 
                    colType : 'text', 
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
            searchUrl : '/vclaim/groups'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('kelompokVclaim',['deleteKelompokVclaim']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$refs.formKelompokVclaim.newKelompokVclaim();
        },

        refreshTable() {
            this.$refs.tableKelompokVclaim.retrieveData();
        },
        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKelompokVclaim.editKelompokVclaim(data.kelompok_vclaim_id);
        },
        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus kelompok tindakan ${data.kelompok_vclaim}. Lanjutkan ?`)){
                this.deleteKelompokVclaim(data.kelompok_vclaim_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKelompokVclaim.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>