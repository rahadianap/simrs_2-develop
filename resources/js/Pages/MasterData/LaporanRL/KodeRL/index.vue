<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <div>
            <h4 style="font-weight:500;margin:0;padding:0;" class="uk-text-uppercase">
                <router-link :to = "{ name:'masterRL' }" style="color:black;">
                    <span uk-icon="icon: arrow-left ; ratio: 1.5"></span>{{groupInfo}}
                </router-link>
            </h4>
            <p style="font-weight:300;margin:0;padding:0;font-size:12px;font-style:italic;">
                {{this.detailInfo}}
            </p>
        </div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKodeRL"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-kode-rl ref="formKodeRL" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-kode-rl>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormKodeRl from '@/Pages/MasterData/LaporanRL/components/FormKodeRL.vue';

export default {
    props : {
        groupId : {type:String, required:true },
    },  
    components : {
        headerPage,
        BaseTable,
        FormKodeRl,
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
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'kode_rl', 
                    title : 'KODE', 
                    colType : 'linkmenus', 
                    functions: [
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
                    name : 'is_valid_rl', 
                    title : 'VALID RL', 
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
                { title : 'Kode Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : `/rl/groups/codes/${this.groupId}`,
            groupRL : [],
            groupInfo : '',
            detailInfo : '',
         }
    },
    created() {
        this.CLR_ERRORS();
        this.getGroupRL();
    },
    mounted() {
    },

    methods : {
        ...mapActions('kodeRL',['deleteKodeRL','dataGroupRL']),
        ...mapMutations(['CLR_ERRORS']),

        getGroupRL() {
            this.CLR_ERRORS();
            this.dataGroupRL(this.groupId).then((response) => {
                if (response.success == true) {
                    this.groupRL = response.data;
                    this.groupInfo = `${this.groupRL.kode_rl} - ${this.groupRL.nama_laporan}`;
                    this.detailInfo = `sub laporan RL ${this.groupRL.nama_laporan}`;
                }
                else { alert (response.message) }
            });
        },

        /**tampikan daftar kode RL */
        listRLKode(data){        
            this.CLR_ERRORS();
            this.$router.push(`/master/rl/kode/${data.rl_id}`);
        },

        addData(){
            this.CLR_ERRORS();
            this.$refs.formKodeRL.newKodeRL(this.groupRL);
        },

        refreshTable() {
            this.$refs.tableKodeRL.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKodeRL.editKodeRL(this.groupRL,data.rl_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan kode RL ${data.kode_rl} - ${data.nama_laporan}. Lanjutkan ?`)){
                this.deleteKodeRL(data.rl_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKodeRL.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>