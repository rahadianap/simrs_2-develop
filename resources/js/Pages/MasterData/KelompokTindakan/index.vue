<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="KELOMPOK TINDAKAN" subTitle="master kelompok tindakan dan pemeriksaan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKelompokTindakan"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-kelompok-tindakan ref="formKelompokTindakan" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-kelompok-tindakan>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormKelompokTindakan from '@/Pages/MasterData/KelompokTindakan/components/FormKelompokTindakan.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormKelompokTindakan,
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
                    name : 'kelompok_tindakan_id', 
                    title : 'ID', 
                    colType : 'linkmenus',
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData }, 
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'kelompok_tindakan', 
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
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/acts/groups'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('kelompokTindakan',['deleteKelompokTindakan']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$refs.formKelompokTindakan.newKelompokTindakan();
        },

        refreshTable() {
            this.$refs.tableKelompokTindakan.retrieveData();
        },
        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKelompokTindakan.editKelompokTindakan(data.kelompok_tindakan_id);
        },
        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus kelompok tindakan ${data.kelompok_tindakan}. Lanjutkan ?`)){
                this.deleteKelompokTindakan(data.kelompok_tindakan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKelompokTindakan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>