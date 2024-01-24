<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="KELOMPOK LAPORAN RL" subTitle="master kelompok laporan RL"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableGroupRL"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-group-rl ref="formGroupRL" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-group-rl>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormGroupRl from '@/Pages/MasterData/LaporanRL/components/FormGroupRL.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormGroupRl,
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
                    name : 'rl_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Daftar Kode RL', 'icon':'plus-circle','callback':this.listRLKode },
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'kode_rl', 
                    title : 'KODE', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Daftar Kode RL', 'icon':'plus-circle','callback':this.listRLKode },
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'nama_laporan', 
                    title : 'Nama RL', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'no_urut', 
                    title : 'No Urut', 
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
                { title : 'Group RL Baru', icon:'plus-circle', 'callback' : this.addGroupData },
            ],
            searchUrl : '/rl/groups'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('kodeRL',['deleteGroupRL']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan daftar kode RL */
        listRLKode(data){        
            this.CLR_ERRORS();
            this.$router.push(`/master/rl/kode/${data.rl_id}`);
        },

        addGroupData(){
            this.CLR_ERRORS();
            this.$refs.formGroupRL.newGroupRL();
        },

        refreshTable() {
            this.$refs.tableGroupRL.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formGroupRL.editGroupRL(data.rl_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan kelompok laporan RL ${data.kode_rl} - ${data.nama_laporan}. Lanjutkan ?`)){
                this.deleteGroupRL(data.rl_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableGroupRL.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>