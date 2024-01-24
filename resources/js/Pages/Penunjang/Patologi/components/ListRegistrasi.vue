<template>
    <div>
        <header-page title="Patologi Anatomi" subTitle="daftar pasien patologi anatomi"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;" class="uk-responsive">
            <base-table ref="tablePatologi"
                :columns = "tbColumns" 
                :config = "configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl"
                v-on:updateListDataTable="updateListData">
            </base-table>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormRegistrasi from '@/Pages/Penunjang/Patologi/components/FormRegistrasi.vue';

export default {
    name : 'list-registrasi',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
    },

    components : {
        headerPage,
        BaseTable,
        FormRegistrasi,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'REGULAR', },
            rowFunctions: [
                { 'title':'Ubah Data', 'icon':'file-edit','callback':this.editFunction },
                { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData }, 
            ],
            tbColumns : [
                { name : 'reg_id', title : 'ID', colType : 'linkmenus', functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.editFunction },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                    ], width : '120px', },
                { name : 'tgl_periksa', title : 'JADWAL PERIKSA', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text',},
                { name : 'nama_pasien', title : 'Pasien', width : '180px', colType : 'text', },
                { name : 'diagnosa', title : 'Diagnosa', width : '200px', colType : 'text', },
                { name : 'tgl_registrasi', title : 'Tgl.Daftar', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addFunction },
            ],
            searchUrl : '/patologi/results',
            dataLists : null,
         }
    },
    mounted() {
        
    },

    methods : {
        ...mapActions('patologi',['createPatologi','deletePatologi','updatePatologi','dataPatologi','listsRegPA','dataRegPA']),
        ...mapMutations(['CLR_ERRORS']),
        
        updateListData(data){
            this.dataLists = null;
            if(data) { this.dataLists = data.data; }
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus data patologi ini. Lanjutkan ?`)){
                this.deletePatologi(data.reg_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePatologi.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        refreshTable(){
            this.$refs.tablePatologi.retrieveData();
        },

    }
}
</script>