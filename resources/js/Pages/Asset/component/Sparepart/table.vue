<template>
    <div style="margin-top:1em;">
        <header-page 
            title="Sparepart" 
            subTitle="master data sparepart">
        </header-page>
        <base-table-asset ref="tableSparepart"
            :update-function = "updateFunction"
            :delete-function = "deleteFunction"
            :isAksiDelete=true
            :columns = "tbColumns" 
            :childColumns = "tbChildColumns"
            :config="configTable" 
            :buttons = "buttons"
            :urlSearch="searchUrl">
        </base-table-asset>
    </div>
    <form-sparepart 
        ref="formSparepart" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </form-sparepart>
    <edit-form-sparepart 
        ref="editformSparepart" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </edit-form-sparepart>
    <Dialog 
        ref="dialog"
        :show="showDialog"
        :cancel="cancel"
        :confirm="confirm"
        v-on:cancel="refreshTable"
        v-on:confirm="refreshTable"
        title="Konfirmasi Hapus"
        :description="`Anda akan menghapus permanen data asset nomor ${selectedRow?.sparepart_id} dengan nama asset ${selectedRow?.sparepart_nama}. Lanjutkan ?`" 
    >
    </Dialog>

</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseRow from '@/Components/BaseTable/BaseRow.vue';
import BaseTableAsset from '@/Components/BaseTableAsset';
import FormSparepart from './FormSparepart.vue';
import EditFormSparepart from './EditFormSparepart.vue';
import Dialog from '@/Components/DialogBox/index.vue';


// import EditFormLokasi from './EditFormLokasi.vue';

export default {
        components : {
            headerPage,
            BaseRow,
            BaseTableAsset,
            FormSparepart,
            EditFormSparepart,
            Dialog
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isExpanded : true,
                configTable : {                
                    isExpandable : true,
                    filterType : 'REGULER', 
                },
                tbColumns : [             
                    { 
                        name : 'sparepart_id', 
                        title : 'ID Sparepart', 
                        colType : 'text', 
                        // functions: [
                        //     { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        //     { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                        // ],
                    },
                    // { 
                    //     name : 'client_id', 
                    //     title : 'ID Klien', 
                    //     colType : 'text', 
                    //     isSearchable : true,
                    // },
                    {
                        name: 'sparepart_nama',
                        title: 'Nama Sparepart',
                        colType: 'text',
                        isSearchable: 'true',
                    },   
                    { 
                        name : 'serial_no', 
                        title : 'Nomer Seri', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'brand_nama', 
                        title : 'Brand', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    // { 
                    //     name : 'tgl_kadaluarsa', 
                    //     title : 'Tanggal Kadaluarsa', 
                    //     colType : 'date', 
                    //     isSearchable : true,
                    // },
                    { 
                        name : 'status', 
                        title : 'Status', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    // {
                    //     name: 'created_by',
                    //     title:'Dibuat oleh',
                    //     colType : 'text',
                    // },
                    // { 
                    //     name : 'created_by', 
                    //     title : 'Dibuat oleh', 
                    //     colType : 'text', 
                    //     isSearchable : true,
                    // },
                    // { 
                    //     name : 'updated_by', 
                    //     title : 'Diperbarui oleh', 
                    //     colType : 'text', 
                    //     isSearchable : true,
                    // },
                   
                ], 
                updateFunction: this.updateData,
                deleteFunction: this.deleteData,
                tbChildColumns :[
                    // { 
                    //     name : 'client_id', 
                    //     title : 'ID Klien', 
                    //     colType : 'text',
                    // },
                    {
                        name: 'tgl_kadaluarsa',
                        title:'Tgl kadaluarsa',
                        colType : 'text',
                    },
                    { 
                        name : 'deskripsi', 
                        title : 'Deskripsi', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    // {
                    //     name: 'updated_by',
                    //     title:'Diperbarui oleh',
                    //     colType : 'text'
                    // },
                ],
                buttons : [
                    { title : 'Data Sparepart Baru', icon:'plus-circle', 'callback' : this.addSparepart },
                ],
                searchUrl : '/spareparts',
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
            ...mapActions('spareparts',['listSpareparts','deleteSparepart','searchID', 'searchName']),
            ...mapMutations(['CLR_ERRORS']),


            addSparepart() {
                this.CLR_ERRORS();
                this.$refs.formSparepart.newSparepart();        
            },

            refreshTable() {
                this.$refs.tableSparepart.retrieveData();
            },

            updateData(spareparts) {
                this.CLR_ERRORS();
                this.$refs.editformSparepart.editSparepart(spareparts.sparepart_id);        
            },
            deleteData(spareparts) {
                this.CLR_ERRORS();
                if(confirm(`Proses ini akan menghapus sparepart ${spareparts.sparepart_nama}. Lanjutkan ?`)){
                    this.deleteSparepart(spareparts.sparepart_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableSparepart.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }
        }
    }
</script>