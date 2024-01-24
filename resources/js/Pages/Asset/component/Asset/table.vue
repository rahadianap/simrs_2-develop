<template>
    <div style="margin-top:1em;">
        <header-page 
            title="LIST ASSET" 
            subTitle="daftar data asset">
        </header-page>
        <base-table-asset ref="tableAsset"
            :update-function = "updateFunction"
            :delete-function = "deleteFunction"
            :columns = "tbColumns" 
            :childColumns = "tbChildColumns"
            :config="configTable" 
            :buttons = "buttons"
            :urlSearch="searchUrl"
            :isCheckboxAksi=true
            :isAksiDelete=true
            :isExpandable=true
        >
        </base-table-asset>
    </div>
    <form-asset 
        ref="formAsset" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </form-asset>
    <edit-form-asset 
        ref="editformAsset" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </edit-form-asset>
    <Dialog 
        ref="dialog"
        :show="showDialog"
        :cancel="cancel"
        :confirm="confirm"
        v-on:cancel="refreshTable"
        v-on:confirm="refreshTable"
        title="Konfirmasi Hapus"
        :description="`Anda akan menghapus permanen data asset nomor ${selectedRow?.nomor_asset} dengan nama asset ${selectedRow?.nama_asset}. Lanjutkan ?`" 
    >
    </Dialog>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseRow from '@/Components/BaseTableAsset/BaseRow.vue';
    import BaseTableAsset from '@/Components/BaseTableAsset';
    import FormAsset from './FormAsset.vue';
    import EditFormAsset from './EditFormAsset.vue';
    import Dialog from '@/Components/DialogBox/index.vue';


export default {
        components : {
            headerPage,
            BaseRow,
            BaseTableAsset,
            FormAsset,
            EditFormAsset,
            Dialog
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isChecked: true,
                showDialog: false,
                isExpanded : false,
                configTable : {   
                    isExpandable : true,
                    filterType : 'REGULER', 
                },
                tbColumns : [             
                    { 
                        name : 'asset_id', 
                        title : 'ID', 
                        colType : 'text', 
                        // functions: [
                        //     { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        //     { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                        // ],
                    },
                    // {
                    //     name: 'client_id',
                    //     title: 'ID Client',
                    //     colType: 'date',
                    //     isSearchable: 'false'
                    // },   
                    { 
                        name : 'nomor_asset', 
                        title : 'Nomor', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'nama_asset', 
                        title : 'Nama', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    // { 
                    //     name : 'group_asset', 
                    //     title : 'Group Asset', 
                    //     colType : 'text', 
                    //     isSearchable : true,
                    // },
                    {
                        name : 'brand',
                        title : 'Brand',
                        colType : 'text',
                        isSearchable : true
                    },
                    { 
                        name : 'lokasi', 
                        title : 'Lokasi', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    {
                        name: 'status',
                        title: 'Status',
                        colType: 'text',
                        isSearchable: 'false'
                    },
                    // {
                    //     name: 'created_by',
                    //     title: 'Dibuat oleh',
                    //     colType: 'text',
                    //     isSearchable: 'false'
                    // },
                    // {
                    //     name: 'created_at',
                    //     title: 'Dibuat pada',
                    //     colType: 'text',
                    //     isSearchable: 'false'
                    // },
                ], 
                updateFunction: this.updateData,
                deleteFunction: this.deleteData,
                tbChildColumns :[
                    // { 
                    //     name : 'asset_id', 
                    //     title : 'ID Asset', 
                    //     colType : 'text'
                    // },
                    // {
                    //     name: 'client_id',
                    //     title: 'ID Client',
                    //     colType: 'date',
                    //     isSearchable: 'false'
                    // },   
                    {
                        name : 'nomor_seri', 
                        title : 'No. Seri', 
                        colType : 'text', 
                        isSearchable : true,                   
                    },
                    { 
                        name : 'nilai_asset', 
                        title : 'Nilai Asset', 
                        colType : 'text', 
                        isSearchable : true,
                        template: `
                            <div>
                            <span v-if="column.name === 'nilai_asset'">{{ separatorRupiah(value) }}</span>
                            <span v-else>{{ value }}</span>
                            </div>
                        `,
                    },
                    { 
                        name : 'masa_pakai', 
                        title : 'Masa Pakai', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    {
                        name: 'tgl_perolehan',
                        title: 'Tgl Perolehan',
                        colType: 'date',
                        isSearchable: 'false'
                    },
                    {
                        name: 'tgl_pemakaian',
                        title: 'Tgl Pemakaian',
                        colType: 'date',
                        isSearchable: 'false'
                    },
                    { 
                        name : 'nama_asset', 
                        title : 'Nama', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'group_asset', 
                        title : 'Kategori', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    // {
                    //     name : 'brand',
                    //     title : 'Nama Brand',
                    //     colType : 'text',
                    //     isSearchable : true
                    // },
                    // { 
                    //     name : 'lokasi', 
                    //     title : 'Lokasi Asset', 
                    //     colType : 'text', 
                    //     isSearchable : true,
                    // },
                   
                    // {
                    //     name: 'created_by',
                    //     title: 'Dibuat oleh',
                    //     colType: 'text',
                    //     isSearchable: 'false'
                    // },
                    // {
                    //     name: 'updated_by',
                    //     title: 'Diubah oleh',
                    //     colType: 'text',
                    //     isSearchable: 'false'
                    // },
                    // {
                    //     name: 'created_at',
                    //     title: 'Dibuat pada',
                    //     colType: 'text',
                    //     isSearchable: 'false'
                    // },
                    // {
                    //     name: 'updated_at',
                    //     title: 'Diubah pada',
                    //     colType: 'text',
                    //     isSearchable: 'false'
                    // },
                    // {
                    //     name: 'status',
                    //     title: 'Status Asset',
                    //     colType: 'text',
                    //     isSearchable: 'false'
                    // },
                    {
                        name: 'deskripsi',
                        title: 'Deskripsi',
                        colType: 'text',
                        isSearchable: 'false'
                    },
                    { 
                        name : 'is_aktif', 
                        title : 'Aktif', 
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Aktif' },
                            { value:1, text:'Nonaktif' },
                        ]
                    }
                ],
                buttons : [
                    { title : 'Data Aset Baru', icon:'plus-circle', 'callback' : this.addAsset },
                ],
                searchUrl : '/assets',
                dataLists : null,

                isLoading : false,
                datas: [],
                options: [],
                columns: [],
                tahun: '',
                params: [],
                searchUrl: '/assets',
                selectedRow: null,
             }
        },

        mounted() {
        },
    
        methods : {
            ...mapActions('asset',['listAsset','deleteAsset','searchID', 'searchName']),
            ...mapMutations(['CLR_ERRORS']),

            // Update Asset
            updateData(assets){
                this.CLR_ERRORS();
                    this.$refs.editformAsset.editAsset(assets.asset_id);        
                
            },

            /**Popup Modal untuk membuat List Asset baru */
            // Popup Tambah Asset
            addAsset(){        
                this.CLR_ERRORS();
                this.$refs.formAsset.newAsset();        
            },
            refreshTable() {
                this.$refs.tableAsset.retrieveData();
            },

            // PopUp DialogBox delete confirmation
            cancel() {
                this.showDialog = false;
            },
            confirm() {
                this.deleteAsset(this.selectedRow?.asset_id)
                .then((response) => {
                        if (response.success == true) {
                                this.$refs.tableAsset.retrieveData();
                                this.showDialog = false;
                        }
                        else { alert(response.message) }
                    });            },
            // END PopUp DialogBox delete confirmation

            // Hapus Asset
            deleteData(assets) {
                this.CLR_ERRORS();
                this.showDialog = true
                this.selectedRow = assets
                // this.CLR_ERRORS();
                // if(confirm(`Anda akan menghapus permanen data asset nomor ${assets.nomor_asset} dengan nama asset ${assets.nama_asset}. Lanjutkan ?`)){
                //     this.show.deleteAsset(assets.asset_id).then((response) => {
                //         if (response.success == true) {
                //             alert(response.message);
                //             this.$refs.tableAsset.retrieveData();
                //         }
                //         else { alert (response.message) }
                //     });
                // };
            },

            separatorRupiah(value) {
                if (value === null || isNaN(value)) {
                    return '';
                }

                const parts = [];
                let formattedValue = value.toString();

                while (formattedValue.length > 3) {
                    parts.unshift(formattedValue.slice(-3));
                    formattedValue = formattedValue.slice(0, -3);
                }

                if (formattedValue.length > 0) {
                    parts.unshift(formattedValue);
                }

                return `Rp ${parts.join(',')}`;
                },

            // prototype popup delete dialogbox
            // deleteData(assets) {
            //     this.CLR_ERRORS();
            //     if(confirm(`Anda akan menghapus permanen data asset nomor ${assets.nomor_asset} dengan nama asset ${assets.nama_asset}. Lanjutkan ?`)){
            //         this.deleteAsset(assets.asset_id).then((confirm) => {
            //             console.log('confirm');
            //             if (this.confirm.success == true) {
            //                 showDialog(confirm.message);
            //                 this.$refs.tableAsset.retrieveData();
            //             }
            //             else { showDialog (confirm.message) }
            //         });
            //     };
            // }

            
        }
    }
</script>