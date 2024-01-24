<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER JABATAN" subTitle="master jabatan dan tanggung jawab"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableJabatan"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
                <!-- v-on:updateListDataTable="updateListData"> -->
                <template v-slot:tbl-body>
                    <tbody v-if="jabatanLists" style="min-weight:50vh;">
                        <row-jabatan
                            v-for="dt in jabatanLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-jabatan>
                    </tbody>
                </template>
            </base-table>
        </div>
        <!-- <form-jabatan ref="formJabatan" v-on:formJabatanClosed="refreshTable"></form-jabatan> -->
        <form-jabatan ref="formJabatan" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-jabatan>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowJabatan from '@/Pages/HumanResource/JobRole/component/RowJabatan.vue';
import FormJabatan from '@/Pages/HumanResource/JobRole/component/FormJabatan.vue'

export default {
    components : {
        headerPage,
        BaseTable,
        RowJabatan,
        FormJabatan,
    },
    
    // props : {
    //     addFunction : { type : Function, required:false, default: null },
    //     editFunction : { type : Function, required:false, default: null },
    //     showFunction : { type : Function, required:false, default: null },
    // },
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
            // rowFunctions : [
            //     { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
            //     { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
            // ],
            tbColumns : [  
                { 
                    name : 'jabatan_id', 
                    title : 'ID', 
                    colType : 'linkmenus',
                    functions: [
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData }, 
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'jabatan_nama', 
                    title : 'Jabatan', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'tanggung_jawab', 
                    title : 'Tanggung Jawab', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'tgl_berlaku', 
                    title : 'Tanggal Berlaku', 
                    colType : 'text', 
                    isSearchable : true,
                },
                // { 
                //     name : 'status', 
                //     title : 'Status', 
                //     colType : 'text', 
                //     isSearchable : true,
                // },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    options : [
                        { value:0, text:'Aktif' },
                        { value:1, text:'Nonaktif' },
                    ]
                },
            ], 
            // updateFunction: this.updateData,
            // deleteFunction: this.deleteData,
            buttons : [
                { title : 'Jabatan Baru', icon:'plus-circle', 'callback' : this.addJabatan },
            ],
            searchUrl : '/jabatan/master',
            jabatanLists : null,
         }
    },
    mounted() {
        // this.initialize();
    },

    methods : {
        ...mapActions('jabatan',['listJabatan','deleteJabatan']),
        // ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        // initialize(){
        //     if(!this.isRefExist) { this.listReferensi(); };
        // },

        // updateListData(data){
        //     this.jabatanLists = null;
        //     if(data) { this.jabatanLists = data.data; }
        // },

        /**tampilkan modal untuk membuat jabatan baru */
        addJabatan(){        
            this.CLR_ERRORS();
            this.$refs.formJabatan.newJabatan();        
        },

        refreshTable() {
            this.$refs.tableJabatan.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formJabatan.editJabatan(data.jabatan_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan jabatan ${data.jabatan_nama}. Lanjutkan ?`)){
                this.deleteJabatan(data.jabatan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableJabatan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>