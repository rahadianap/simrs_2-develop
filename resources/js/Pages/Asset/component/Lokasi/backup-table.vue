<!-- <template>
    <div style="margin-top:1em;">
        <header-page 
            title="RUANG DAN BED" 
            subTitle="master data lokasi ruang unit dan bed inap">
        </header-page>
        <base-table-asset ref="tableDataLokasi"
            :columns = "tbColumns" 
            :config="configTable" 
            :buttons = "buttons"
            :urlSearch="searchUrl"
            :update-function = "updateFunction"
            :delete-function = "deleteFunction"
            :childColumns = "tbChildColumns"
            :childColumns2 = "tbChildColumns2"

        >
        </base-table-asset>
    </div>
    <form-lokasi 
        ref="formLokasi" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </form-lokasi>
    <edit-form-lokasi 
        ref="editformLokasi" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </edit-form-lokasi>
    
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
// import BaseRow from '@/Components/BaseTable/BaseRow.vue';
import BaseRow from '@/Components/BaseTableAsset/BaseRow.vue';
import BaseTableAsset from '@/Components/BaseTableAsset';
import FormLokasi from './FormLokasi.vue';
import EditFormLokasi from './EditFormLokasi.vue';

export default {
        components : {
            headerPage,
            BaseRow,
            BaseTableAsset,
            FormLokasi,
            EditFormLokasi,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                // isExpanded2 : true,
                configTable : {                
                    // isExpandable2 : true,
                    filterType : 'REGULAR', 
                },
                tbColumns : [             
                    { 
                        name : 'ruang_id', 
                        title : 'ID Lokasi', 
                        colType : 'text', 
                        // functions: [
                        //     { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        //     { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                        // ],
                    },
                    {
                        name: 'unit_nama',
                        title: 'Nama Lokasi',
                        colType: 'text',
                        isSearchable: 'true'
                    },   
                    { 
                        name : 'ruang_nama', 
                        title : 'Nama Ruang', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'lokasi', 
                        title : 'Lokasi', 
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
                        name : 'is_aktif', 
                        title : 'Aktif', 
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Ditampilkan' },
                            { value:1, text:'Nonaktif' },
                        ]
                    },

                ], 

                updateFunction: this.updateData,
                deleteFunction: this.deleteData,

                // data expandable parameter ruang
                tbChildColumns :[
                    { 
                        name : 'is_utama', 
                        title : 'Ruang Utama', 
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Aktif' },
                            { value:1, text:'Nonaktif' },
                        ]                    
                    },
                    {
                        name: 'is_ventilator',
                        title: 'Ventilator',
                        isSearchable: 'false',
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Aktif' },
                            { value:1, text:'Nonaktif' },
                        ]
                    },   
                    { 
                        name : 'is_ruang_operasi', 
                        title : 'Ruang Operasi', 
                        isSearchable : true,
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Aktif' },
                            { value:1, text:'Nonaktif' },
                        ]
                    },
                    { 
                        name : 'is_negative_pressure', 
                        title : 'Negative Pressure', 
                        isSearchable : true,
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Aktif' },
                            { value:1, text:'Nonaktif' },
                        ]
                    },
                    {
                        name : 'is_ruang_operasi',
                        title : 'Ruang Operasi',
                        isSearchable : true,
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Aktif' },
                            { value:1, text:'Nonaktif' },
                        ]
                    },
                    {
                        name : 'is_pandemi',
                        title : 'Isolasi Pandemi',
                        isSearchable : true,
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Aktif' },
                            { value:1, text:'Nonaktif' },
                        ]
                    },
                    { 
                        name : 'is_aktif', 
                        title : 'Visibilitas', 
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Aktif' },
                            { value:1, text:'Nonaktif' },
                        ]
                    }
                ],

                // data expandable bed ruang
                tbChildColumns2 :[
                    { 
                        name : 'bed_id', 
                        title : 'ID Bed', 
                        colType : 'text', 
                    },
                    { 
                        name : 'ruang_id', 
                        title : 'ID Ruang', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    {
                        name : 'no_bed',
                        title : 'No Bed',
                        colType : 'text',
                        isSearchable : true,
                    },
                    {
                        name : 'jns_kelamin_bed',
                        title : 'Jenis Kelamin Pasien',
                        colType: 'boolean',
                        isSearchable: true,

                    }
                ],
                buttons : [
                    { title : 'Data Ruang Baru', icon:'plus-circle', 'callback' : this.addRuang },
                ],
                searchUrl : '/rooms',
                dataLists : null,

                isLoading : false,
                datas: [],
                options: [],
                columns: [],
                tahun: '',
                params: [],
             }
        },
        mounted() {
        },
    
        
        methods : {
            ...mapMutations(['CLR_ERRORS']),
            ...mapActions('ruang',['deleteRuang']),

            addRuang() {
                this.CLR_ERRORS();
                this.$router.push(`/master/ruang/baru`);
            },

            updateData(data) {
                this.CLR_ERRORS();
                this.$router.push(`/master/ruang/${data.ruang_id}`);
            },
            deleteData(data) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menghapus ruang ${data.ruang_nama}. Lanjutkan ?`)){
                    this.deleteRuang(data.ruang_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableRuang.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }
        }
    }
</script> -->