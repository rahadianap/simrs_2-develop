<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER KARYAWAN" subTitle="master data karyawan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKaryawan"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="karyawanLists" style="min-weight:50vh;">
                        <row-karyawan
                            v-for="dt in karyawanLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-karyawan>
                    </tbody>
                </template>
            </base-table>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowKaryawan from '@/Pages/HumanResource/Karyawan/ListKaryawan/component/RowKaryawan.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        RowKaryawan,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',['isRefExist']),
        ...mapGetters('jabatan',['jabatanLists']),
        ...mapGetters('unitPelayanan',['unitLists']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            rowFunctions : [
                { 'title':'Detail Data', 'icon':'eye','callback':this.detailData },
                { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
            ],
            tbColumns : [  
                { 
                    name : 'karyawan_id', 
                    title : 'ID', 
                    colType : 'linkmenus',
                    functions: [
                        // { 'title':'Detail Data', 'icon':'file-edit','callback':this.detailData }, 
                        { 'title':'Detail Data', 'icon':'eye','callback':this.detailData },
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'nama', 
                    title : 'Nama Karyawan', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'tempat_lahir', 
                    title : 'Kelahiran', 
                    colType : 'text',
                    isSearchable  : true,
                },
				{ 
                    name : 'jabatan_nama', 
                    title : 'Jabatan', 
                    colType : 'text',
                    isSearchable  : true,
                },
                { 
                    name : 'unit_nama', 
                    title : 'Unit', 
                    colType : 'text',
                    isSearchable  : true,
                },
				{ 
                    name : 'tgl_masuk', 
                    title : 'Mulai Bekerja', 
                    colType : 'text',
                    isSearchable  : true,
                },
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
            buttons : [
                { title : 'Karyawan Baru', icon:'plus-circle', 'callback' : this.addKaryawan },
            ],
            searchUrl : '/karyawan/master',
            karyawanLists : null,
         }
    },

    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('karyawan',['deleteKaryawan']),
        ...mapActions('jabatan',['listJabatan']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
        },

        initialize(){
            let param = {per_page:'ALL'};
            if(this.jabatanLists == null || this.jabatanLists.length == 0){
                this.listJabatan(param);
            }
            if(this.unitLists == null || this.unitLists.length == 0) {
                this.listUnitPelayanan(param);
            }
            if(!this.isRefExist) { this.listReferensi(); };
        },

        updateListData(data){
            this.karyawanLists = null;
            if(data) { this.karyawanLists = data.data; }
        },

        /**tampilkan halaman untuk membuat karyawan baru */
        addKaryawan(){        
            this.CLR_ERRORS();   
            this.$router.push({ name: 'formKaryawan', params: { karyawanId: 'baru' } });
        },

        refreshTable() {
            this.$refs.tableKaryawan.retrieveData();
        },

        detailData(data){
            this.CLR_ERRORS();
            this.$router.push(`/karyawan/detail/${data.karyawan_id}`);
        },

        updateData(data) {
            this.CLR_ERRORS();
            // this.$router.push(`/master/pasien/form/${data.pasien_id}`);
            this.$router.push(`/karyawan/${data.karyawan_id}`);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan karyawan ${data.karyawan_nama}. Lanjutkan ?`)){
                this.deleteKaryawan(data.karyawan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKaryawan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },
    }
}
</script>