<template>
    <div style="margin-top:1em;">
        <header-page 
            title="Master Data Lokasi" 
            subTitle="master data lokasi asset">
        </header-page>
        <base-table-asset ref="tableLokasi"
            :update-function = "updateFunction"
            :delete-function = "deleteFunction"
            :columns = "tbColumns" 
            :config="configTable" 
            :isAksiDelete=true
            :buttons = "buttons"
            :urlSearch="searchUrl"
            :isExpandable=false
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
    <Dialog 
        ref="dialog"
        :show="showDialog"
        :cancel="cancel"
        :confirm="confirm"
        v-on:cancel="refreshTable"
        v-on:confirm="refreshTable"
        title="Konfirmasi Hapus"
        :description="`Anda akan menghapus permanen data lokasi ${selectedRow?.lokasi_nama} dengan deskripsi lokasi ${selectedRow?.deskripsi}. Lanjutkan ?`" 
    >
    </Dialog>

</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseRow from '@/Components/BaseTableAsset/BaseRow.vue';
import BaseTableAsset from '@/Components/BaseTableAsset';
import FormLokasi from './FormLokasi.vue';
import EditFormLokasi from './EditFormLokasi.vue';
import Dialog from '@/Components/DialogBox/index.vue';

export default {
        components : {
            headerPage,
            BaseRow,
            BaseTableAsset,
            FormLokasi,
            EditFormLokasi,
            Dialog,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                showDialog: false,
                isExpanded : false,
                configTable : {                
                    isExpandable : false,
                    filterType : 'REGULER', 
                },
                tbColumns : [             
                    { 
                        name : 'lokasi_aset_id', 
                        title : 'ID', 
                        colType : 'text',
                    },
                    {
                        name: 'lokasi_nama',
                        title: 'Nama Lokasi',
                        colType: 'text',
                        isSearchable: 'true',
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
                // tbChildColumns :[
                //     { 
                //         name : 'client_id', 
                //         title : 'ID Klien', 
                //         colType : 'text',
                //     },
                //     { 
                //         name : 'is_aktif', 
                //         title : 'Aktif', 
                //         colType : 'boolean', 
                //         options : [
                //             { value:0, text:'Ditampilkan' },
                //             { value:1, text:'Nonaktif' },
                //         ]
                //     },
                    
                //     {
                //         name: 'updated_by',
                //         title:'Diperbarui oleh',
                //         colType : 'text'
                //     },
                // ],
                buttons : [
                    { title : 'Data Lokasi Baru', icon:'plus-circle', 'callback' : this.addLokasi },
                ],
                searchUrl : '/assets/locations',
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
            ...mapActions('lokasi',['listLokasi','deleteLokasi','searchID', 'searchName']),
            ...mapMutations(['CLR_ERRORS']),

            updateData(locations) {
                this.CLR_ERRORS();
                this.$refs.editformLokasi.editLokasi(locations.lokasi_aset_id);        
            },

            addLokasi() {
                this.CLR_ERRORS();
                this.$refs.formLokasi.newLokasi();        
            },
            
            refreshTable() {
                this.$refs.tableLokasi.retrieveData();
            },
             // PopUp DialogBox delete confirmation
             cancel() {
                this.showDialog = false;
            },
            confirm() {
                this.deleteLokasi(this.selectedRow?.lokasi_aset_id)
                .then((response) => {
                        if (response.success == true) {
                                this.$refs.tableLokasi.retrieveData();
                                this.showDialog = false;
                        }
                        else { alert(response.message) }
                    });            },
            // END PopUp DialogBox delete confirmation

            deleteData(locations) {
                this.CLR_ERRORS();
                this.showDialog = true
                this.selectedRow = locations
                // if(confirm(`Proses ini akan menghapus lokasi ${locations.lokasi_nama}. Lanjutkan ?`)){
                //     this.deleteLokasi(locations.lokasi_aset_id).then((response) => {
                //         if (response.success == true) {
                //             alert(response.message);
                //             this.$refs.tableLokasi.retrieveData();
                //         }
                //         else { alert (response.message) }
                //     });
                // };
            }
        }
    }
</script>