<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="KOMPONEN HARGA" subTitle="master data komponen harga"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKomponenHarga"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-komponen-harga ref="formKomponenHarga" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-komponen-harga>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormKomponenHarga from '@/Pages/MasterData/KomponenTariff/components/FormKomponenHarga.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormKomponenHarga,
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
                    name : 'komp_harga_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'komp_harga', 
                    title : 'Komponen', 
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
            searchUrl : '/tariffs/components'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('komponenHarga',['deleteKomponenHarga']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$refs.formKomponenHarga.newKomponenHarga();
        },

        refreshTable() {
            this.$refs.tableKomponenHarga.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKomponenHarga.editKomponenHarga(data.komp_harga_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus komponen harga ${data.komp_harga}. Lanjutkan ?`)){
                this.deleteKomponenHarga(data.komp_harga_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKomponenHarga.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>