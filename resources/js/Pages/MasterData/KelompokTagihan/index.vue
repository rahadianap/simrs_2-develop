<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="KELOMPOK TAGIHAN PASIEN" subTitle="master kelompok tagihan (billing) pasien"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKelompokTagihan"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-kelompok-tagihan ref="formKelompokTagihan" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-kelompok-tagihan>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormKelompokTagihan from '@/Pages/MasterData/KelompokTagihan/components/FormKelompokTagihan.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormKelompokTagihan,
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
                    name : 'kelompok_tagihan_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'kelompok_tagihan', 
                    title : 'NAMA', 
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
            searchUrl : '/billings/groups'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('kelompokTagihan',['deleteKelompokTagihan']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$refs.formKelompokTagihan.newKelompokTagihan();
        },

        refreshTable() {
            this.$refs.tableKelompokTagihan.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKelompokTagihan.editKelompokTagihan(data.kelompok_tagihan_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus kelompok tagihan ${data.kelompok_tagihan}. Lanjutkan ?`)){
                this.deleteKelompokTagihan(data.kelompok_tagihan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKelompokTagihan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>