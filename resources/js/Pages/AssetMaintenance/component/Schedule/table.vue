<template>
    <div style="margin-top:1em;">
        <header-page 
            title="LIST MAINTENANCE SCHEDULE" 
            subTitle="daftar data penjadwalan pemeliharaan aset dengan teknisi">
        </header-page>
        <base-table-asset ref="tableSchedule"
            :update-function = "updateFunction"
            :delete-function = "deleteFunction"
            :columns = "tbColumns" 
            :childColumns = "tbChildColumns"
            :config="configTable" 
            :isCheckboxAksi=false
            :buttons = "buttons"
            :urlSearch="searchUrl">
        </base-table-asset>
    </div>
    <form-schedule 
        ref="formSchedule" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </form-schedule>
    <edit-form-schedule 
        ref="editFormSchedule" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </edit-form-schedule>
    <Dialog 
        ref="dialog"
        :show="showDialog"
        :cancel="cancel"
        :confirm="confirm"
        v-on:cancel="refreshTable"
        v-on:confirm="refreshTable"
        title="Konfirmasi Hapus"
        :description="`Anda akan menghapus permanen data schedule ${selectedRow?.maint_schedule_id} dengan nama teknisi ${selectedRow?.schedule_teknisi}. Lanjutkan ?`" 
    >
    </Dialog>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseRow from '@/Components/BaseTable/BaseRow.vue';
    import BaseTableAsset from '@/Components/BaseTableAsset';
    import FormSchedule from './FormSchedule.vue';
    import EditFormSchedule from './EditFormSchedule.vue';
    import Dialog from '@/Components/DialogBox/index.vue';

export default {
  watch: {
  },
        components : {
            headerPage,
            BaseRow,
            BaseTableAsset,
            FormSchedule,
            EditFormSchedule,
            Dialog,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                showDialog: false,
                isExpanded : true,
                configTable : {                
                    isExpandable : true,
                    filterType : 'REGULER', 
                },
                tbColumns : [             
                    { 
                        name : 'maint_schedule_id', 
                        title : 'ID Schedule', 
                        colType : 'text', 
                    },
                    { 
                        name : 'maint_ticket_id', 
                        title : 'ID Ticket', 
                        colType : 'text', 
                    },
                    { 
                        name : 'teknisi', 
                        title : 'Teknisi', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    {
                        name : 'tgl_rencana',
                        title : 'Tanggal Rencana',
                        colType : 'date',
                        isSearchable : true,
                    },
                    {
                        name : 'tgl_realisasi',
                        title : 'Tanggal Realisasi',
                        colType : 'date',
                        isSearchable : true,
                    },
                    {
                        name: 'created_by',
                        title: 'Dibuat oleh',
                        colType: 'date',
                        isSearchable: false,
                    },

                ], 
                updateFunction: this.updateData,
                deleteFunction: this.deleteData,
                tbChildColumns :[
                    { 
                        name : 'client_id', 
                        title : 'ID Client', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    {
                        name: 'catatan',
                        title: 'Catatan',
                        colType: 'text',
                        isSearchable: true,
                    },
                    {
                        name: 'tindak_lanjut',
                        title: 'Tindak Lanjut',
                        colType: 'text',
                        isSearchable: true,
                    },
                    { 
                        name : 'status', 
                        title : 'Status Schedule', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'nama_vendor', 
                        title : 'Nama Vendor', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'is_vendor', 
                        title : 'Vendor', 
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Ya' },
                            { value:1, text:'Tidak' },
                        ]
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
                    {
                        name: 'updated_by',
                        title: 'Diubah oleh',
                        colType: 'text',
                        isSearchable: 'false'
                    },
                ],
                buttons : [
                    { title : 'Buat Jadwal Baru', icon:'plus-circle', 'callback' : this.addSchedule },
                ],
                dataLists : null,
                isLoading : false,
                datas: [],
                options: [],
                columns: [],
                tahun: '',
                params: [],
                searchUrl: '/mschedule'
             }
        },

        mounted() {
        },
    
        methods : {
            ...mapActions('schedule',['listSchedule','deleteSchedule','searchID', 'searchName']),
            ...mapMutations(['CLR_ERRORS']),

            // Update Schedule
            updateData(mschedule){
                this.CLR_ERRORS();
                    this.$refs.editFormSchedule.editSchedule(mschedule.maint_schedule_id);        
                
            },

            /**Popup Modal untuk membuat List Schedule baru */
            // Popup Tambah schedule
            addSchedule(){        
                this.CLR_ERRORS();
                this.$refs.formSchedule.newSchedule();        
            },
            refreshTable() {
                this.$refs.tableSchedule.retrieveData();
            },

            // PopUp DialogBox delete confirmation
            cancel() {
                this.showDialog = false;
            },
            confirm() {
                this.deleteSchedule(this.selectedRow?.maint_schedule_id)
                .then((response) => {
                        if (response.success == true) {
                                this.$refs.tableSchedule.retrieveData();
                                this.showDialog = false;
                        }
                        else { alert(response.message) }
                    });            
                },
            // END PopUp DialogBox delete confirmation

            // Hapus Schedule
            deleteData(schedule) {
                this.CLR_ERRORS();
                this.showDialog = true
                this.selectedRow = schedule
                // if(confirm(`Anda akan menghapus permanen data schedule ${schedule.maint_schedule_id} dengan nama teknisi ${schedule.teknisi}. Lanjutkan ?`)){
                //     this.deleteSchedule(schedule.maint_schedule_id).then((response) => {
                //         console.log(response);
                //         if (response.success == true) {
                //             alert(response.message);
                //             this.$refs.tableSchedule.retrieveData();
                //         }
                //         else { alert (response.message) }
                //     });
                // };
            }
        }
    }
</script>