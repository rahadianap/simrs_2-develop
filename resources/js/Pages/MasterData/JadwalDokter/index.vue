<!-- <template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="JADWAL PRAKTEK DOKTER" subTitle="master jadwal praktek dokter"></header-page>
        <div>
            <div class="uk-card uk-card-default" style="padding:1em;margin-top:1em;border-radius:10px;min-height:400px;">
                
            </div>
        </div>
    </div>
</template>
<script>
import headerPage from '@/Components/HeaderPage.vue';
import TableJadwalDokter from '@/Pages/MasterData/JadwalDokter/components/TableJadwalDokter.vue';

export default {
    components : {
        headerPage,
    }
}
</script> -->
<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <div class="uk-width-1-1 uk-grid-small" uk-grid>
            <div class="uk-width-expand@m" style="margin:0;padding:0 0 0 0.5em;">
                <header-page title="JADWAL PRAKTEK DOKTER" subTitle="master jadwal praktek dokter"></header-page>
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>
            </div>
            <div class="uk-width-auto@m">
                <div class="uk-button-group uk-box-shadow-large" style="padding:0;margin:0;border-radius:5px;">                
                    <button
                        v-for="(button, index) in switchButtons"
                        :key="index"
                        :class="{ 'hims-table-btn': true, 'hims-btn-active': activeButton === button.title }"
                        @click.prevent="button.callback"
                    >
                        <span v-if="button.icon" :uk-icon="button.icon"></span>
                        {{ button.title }}
                    </button>
                </div>
            </div>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableJadwalDokter"
                :columns = "tbColumns" 
                :childColumns = "tbChildColumns"
                childNameIndex = "jadwal"
                :config="configTable" 
                :urlSearch="searchUrl">
            </base-table>
        </div>

        <form-jadwal-dokter ref="formJadwalDokter" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-jadwal-dokter>
        <form-jadwal-unit ref="formJadwalUnit" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-jadwal-unit>
        <form-jadwal-edit ref="formJadwalEdit" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-jadwal-edit>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable/index.vue';
import FormJadwalDokter from '@/Pages/MasterData/JadwalDokter/components/FormJadwalDokter.vue';
import FormJadwalUnit from '@/Pages/MasterData/JadwalDokter/components/FormJadwalUnit.vue';
import FormJadwalEdit from '@/Pages/MasterData/JadwalDokter/components/FormJadwalEdit.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormJadwalDokter,
        FormJadwalUnit,
        FormJadwalEdit,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : true,
                filterType : 'SIMPLE', 
            },
            tbColumns : [],

            tbColumnsUnit : [
                { 
                    name : 'unit_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Tambah Jadwal', 'icon':'plus-circle','callback':this.addJadwalUnit },
                    ],
                },   
                { 
                    name : 'unit_nama', 
                    title : 'NAMA', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Tambah Jadwal', 'icon':'plus-circle','callback':this.addJadwalUnit },
                    ],
                },
                { 
                    name : 'inisial', 
                    title : 'Inisial', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'kepala_unit', 
                    title : 'Kepala Unit', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'jenis_registrasi', 
                    title : 'Registrasi', 
                    colType : 'text', 
                    isSearchable : true,
                }
            ],

            tbColumnsDokter : [
                { 
                    name : 'dokter_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Tambah Jadwal', 'icon':'plus-circle','callback':this.addJadwal },
                    ],
                },   
                { 
                    name : 'dokter_nama', 
                    title : 'NAMA DOKTER', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Tambah Jadwal', 'icon':'plus-circle','callback':this.addJadwal },
                    ],
                },   
                { 
                    name : 'smf.smf_nama', 
                    title : 'SMF', 
                    colType : 'text',
                },   
                { 
                    name : 'no_sip', 
                    title : 'SIP', 
                    colType : 'text',
                }
            ],

            tbChildColumns :[],
            tbChildUnitColumns :[
                { 
                    name : 'jadwal_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    textAlign : 'left',
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },
                    ],
                },   
                { 
                    name : 'dokter_nama', 
                    title : 'NAMA', 
                    colType : 'linkmenus', 
                    textAlign : 'left',
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },
                    ],
                },   
                { 
                    name : 'nama_hari', 
                    title : 'Hari', 
                    textAlign : 'center',
                    colType : 'text',
                },
                { 
                    name : 'mulai', 
                    title : 'Mulai', 
                    colType : 'text',
                    textAlign : 'center',                    
                },
                { 
                    name : 'selesai', 
                    title : 'Selesai', 
                    colType : 'text',
                    textAlign : 'center',
                },
                { 
                    name : 'kuota', 
                    title : 'Kuota', 
                    colType : 'text',
                    textAlign : 'center',
                }
            ],            
            tbChildDokterColumns :[
                { 
                    name : 'jadwal_id', 
                    title : 'ID', 
                    colType : 'linkmenus',
                    textAlign : 'left',                     
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },
                    ],
                },   
                { 
                    name : 'unit_nama', 
                    title : 'NAMA UNIT', 
                    colType : 'linkmenus', 
                    textAlign : 'left',     
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },
                    ],
                },   
                { 
                    name : 'nama_hari', 
                    title : 'Hari', 
                    colType : 'text',
                    textAlign : 'center',     
                },
                { 
                    name : 'mulai', 
                    title : 'Mulai', 
                    colType : 'text',
                    textAlign : 'center',     
                },
                { 
                    name : 'selesai', 
                    title : 'Selesai', 
                    colType : 'text',
                    textAlign : 'center',     
                },
                { 
                    name : 'kuota', 
                    title : 'Kuota', 
                    colType : 'text',
                    textAlign : 'center',     
                }
            ],        
            activeButton : 'Group Unit',
            switchButtons : [
                { title : 'Group Unit', icon:'list', 'callback' : this.groupByUnit },
                { title : 'Group Dokter', icon:'list', 'callback' : this.groupByDokter },
            ],
            searchUrl : '/schedule/units'
         }
    },
    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('kelompokMakanan',['deleteKelompokMakanan']),
        ...mapActions('jadwalDokter',['deleteJadwalDokter']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.tbColumns = this.tbColumnsUnit;
            this.tbChildColumns = this.tbChildUnitColumns;
        },

        groupByUnit(){
            this.tbColumns = this.tbColumnsUnit;
            this.tbChildColumns = this.tbChildUnitColumns;
            this.searchUrl = '/schedule/units';
            this.activeButton = 'Group Unit';
        },

        groupByDokter(){
            this.tbColumns = this.tbColumnsDokter;
            this.tbChildColumns = this.tbChildDokterColumns;
            this.searchUrl = '/schedule/doctors';
            this.activeButton = 'Group Dokter';
        },

        /**tampikan modal untuk membuat vendor baru */
        addJadwal(data){        
            this.CLR_ERRORS();
            this.$refs.formJadwalDokter.newJadwalDokter(data);
        },

        addJadwalUnit(data){        
            this.CLR_ERRORS();
            this.$refs.formJadwalUnit.newJadwalUnit(data);
        },

        refreshTable() {
            this.$refs.tableJadwalDokter.retrieveData();
        },

        updateData(data){        
            this.CLR_ERRORS();
            this.$refs.formJadwalEdit.editJadwal(data.jadwal_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus jadwal dokter ${data.dokter_nama}. Lanjutkan ?`)){
                this.deleteJadwalDokter(data.jadwal_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableJadwalDokter.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>
<style scoped>
.hims-btn-active {
    background-color: #cc0202 !important;
    color: white !important;
}
</style>