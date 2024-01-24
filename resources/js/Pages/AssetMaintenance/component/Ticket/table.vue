<template>
    <div style="margin-top:1em;">
        <header-page 
            title="LIST MAINTENANCE TICKET" 
            subTitle="daftar data pengaduan pemeliharaan">
        </header-page>
        <base-table-asset ref="tableTicket"
            :update-function = "updateFunction"
            :delete-function = "deleteFunction"
            :columns = "tbColumns" 
            :childColumns = "tbChildColumns"
            :config="configTable" 
            :buttons = "buttons"
            :urlSearch="searchUrl"
            :isCheckboxAksi=false
            :isExpandable=true
            >
        </base-table-asset>
    </div>
    <form-ticket 
        ref="formTicket" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </form-ticket>
    <edit-form-ticket 
        ref="editformTicket" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </edit-form-ticket>
    <Dialog 
        ref="dialog"
        :show="showDialog"
        :cancel="cancel"
        :confirm="confirm"
        v-on:cancel="refreshTable"
        v-on:confirm="refreshTable"
        title="Konfirmasi Hapus"
        :description="`Anda akan menghapus permanen data tiket nomor ${selectedRow?.maint_ticket_id} dengan nama teknisi ${selectedRow?.nama_teknisi}. Lanjutkan ?`" 
    >
    </Dialog>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseRow from '@/Components/BaseTableAsset/BaseRow.vue';
    // import BaseTable from '@/Components/BaseTable';
    import BaseTableAsset from '@/Components/BaseTableAsset';
    import FormTicket from './FormTicket.vue';
    import EditFormTicket from './EditFormTicket.vue';
    import Dialog from '@/Components/DialogBox/index.vue';

export default {
        components : {
            headerPage,
            BaseRow,
            BaseTableAsset,
            FormTicket,
            EditFormTicket,
            Dialog,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isChecked: false,
                showDialog: false,
                configTable : {           
                    isExpandable : true,
                    filterType : 'REGULER', 
                },
                tbColumns : [             
                    { 
                        name : 'maint_ticket_id', 
                        title : 'ID Ticket', 
                        colType : 'text', 
                        // functions: [
                        //     { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        //     { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                        // ],
                    },
                    { 
                        name : 'asset_id', 
                        title : 'nama Asset', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'tgl_tiket', 
                        title : 'Tgl Dibuat', 
                        colType : 'date', 
                        isSearchable : true,
                    },
                    { 
                        name : 'jenis_maintenance', 
                        title : 'Jenis Maintenance', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'nama_teknisi', 
                        title : 'Nama Teknisi', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    {
                        name: 'created_by',
                        title: 'Dibuat oleh',
                        colType: 'text',
                        isSearchable: 'false'
                    },
                ], 
                updateFunction: this.updateData,
                deleteFunction: this.deleteData,
                tbChildColumns :[
                    { 
                        name : 'client_id', 
                        title : 'ID Klien', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    {
                        name: 'prioritas',
                        title: 'Prioritas',
                        colType: 'text',
                        isSearchable: 'false'
                    }, 
                    { 
                        name : 'keterangan', 
                        title : 'Keterangan', 
                        colType : 'text',
                    },
   
                    { 
                        name : 'tindakan', 
                        title : 'Tindakan', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'catatan_tindakan', 
                        title : 'Catatan Tindakan', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'tgl_selesai', 
                        title : 'Tanggal Selesai Tiket', 
                        colType : 'date', 
                        isSearchable : true,
                    },
                    {
                        name : 'status',
                        title : 'Status Maintenance Tiket',
                        colType : 'text',
                    },
                    { 
                        name : 'lokasi_asset', 
                        title : 'Lokasi Asset', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'is_aktif', 
                        title : 'Aktif', 
                        colType : 'boolean', 
                    },
                    {
                        name: 'updated_by',
                        title: 'Diubah oleh',
                        colType: 'text',
                        isSearchable: 'false'
                    },
                ],
                buttons : [
                    { title : 'Buat Tiket Baru', icon:'plus-circle', 'callback' : this.addTicket },
                ],
                searchUrl: '/mtickets',
                dataLists : null,

                isLoading : false,
                datas: [],
                options: [],
                columns: [],
                tahun: '',
                params: [],
                searchUrl: '/mtickets',
                selectedRow: null
             }
        },

        mounted() {
        },
    
        methods : {
            ...mapActions('assetMaintenance',['listTicket','deleteTicket','searchID', 'searchName']),
            ...mapMutations(['CLR_ERRORS']),

            // Update ticket
            updateData(mtickets){
                this.CLR_ERRORS();
                    this.$refs.editformTicket.editTicket(mtickets.maint_ticket_id);        
                
            },

            /**Popup Modal untuk membuat List ticket baru */
            // Popup Tambah ticket
            addTicket(){        
                this.CLR_ERRORS();
                this.$refs.formTicket.newTicket();        
            },
            refreshTable() {
                this.$refs.tableTicket.retrieveData();
            },

             // PopUp DialogBox delete confirmation
            cancel() {
                this.showDialog = false;
            },
            confirm() {
                this.deleteTicket(this.selectedRow?.maint_ticket_id)
                .then((response) => {
                        if (response.success == true) {
                                this.$refs.tableTicket.retrieveData();
                                this.showDialog = false;
                        }
                        else { alert(response.message) }
                    });            },
            // END PopUp DialogBox delete confirmation

            // Hapus ticket
            deleteData(mtickets) {
                this.CLR_ERRORS();
                this.showDialog = true
                this.selectedRow = mtickets
                // if(confirm(`Anda akan menghapus permanen data tiket nomor ${mtickets.maint_ticket_id} dengan nama teknisi ${mtickets.nama_teknisi}. Lanjutkan ?`)){
                //     this.deleteTicket(mtickets.maint_ticket_id).then((response) => {
                //         console.log(response);
                //         if (response.success == true) {
                //             alert(response.message);
                //             this.$refs.tableTicket.retrieveData();
                //         }
                //         else { alert (response.message) }
                //     });
                // };
            }
        }
    }
</script>