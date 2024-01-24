<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="KELOMPOK MAKANAN" subTitle="master kelompok makanan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKelompokMakanan"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-kelompok-makanan ref="formKelompokMakanan" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-kelompok-makanan>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormKelompokMakanan from '@/Pages/MasterData/KelompokMakanan/components/FormKelompokMakanan.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormKelompokMakanan,
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
                    name : 'kelompok_makanan_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'kelompok', 
                    title : 'NAMA', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'catatan', 
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
            searchUrl : '/meals/groups'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('kelompokMakanan',['deleteKelompokMakanan']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$refs.formKelompokMakanan.newKelompokMakanan();
        },

        refreshTable() {
            this.$refs.tableKelompokMakanan.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKelompokMakanan.editKelompokMakanan(data.kelompok_makanan_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus kelompok tagihan ${data.kelompok}. Lanjutkan ?`)){
                this.deleteKelompokMakanan(data.kelompok_makanan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKelompokMakanan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>