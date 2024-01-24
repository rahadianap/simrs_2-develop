<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="KELAS PELAYANAN" subTitle="master kelas pelayanan dan harga"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKelas"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-kelas ref="formKelas" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-kelas>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormKelas from '@/Pages/MasterData/Kelas/components/FormKelas.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormKelas,
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
                    name : 'kelas_id', 
                    title : 'ID', 
                    colType : 'linkmenus',
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData }, 
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'kelas_nama', 
                    title : 'Nama', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData }, 
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'rl_kode', 
                    title : 'Kode RL', 
                    colType : 'text', 
                    isSearchable : false,
                },
                { 
                    name : 'is_kelas_kamar', 
                    title : 'Kamar', 
                    colType : 'boolean', 
                    isSearchable : false,
                },
                { 
                    name : 'is_kelas_harga', 
                    title : 'Tarif', 
                    colType : 'boolean', 
                    isSearchable : false,
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
            searchUrl : '/services/classes'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('kelas',['deleteKelas']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$refs.formKelas.newKelas();
        },

        refreshTable() {
            this.$refs.tableKelas.retrieveData();
        },
        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKelas.editKelas(data.kelas_id);
        },
        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus kelas ${data.kelas_nama}. Lanjutkan ?`)){
                this.deleteKelas(data.kelas_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKelas.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>