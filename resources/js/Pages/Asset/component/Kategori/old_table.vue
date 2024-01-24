<template>
    <div style="margin-top:1em;">
        <header-page 
            title="KATEGORI" 
            subTitle="master data kategori">
        </header-page>
        <base-table-asset ref="tableDataKategori"
            :update-function = "updateFunction"
            :delete-function = "deleteFunction"
            :columns = "tbColumns" 
            :config="configTable" 
            :buttons = "buttons"
            :urlSearch="searchUrl">
        </base-table-asset>
    </div>
    <h5 style="font-weight: 750;"> TAHAP DEVELOPMENT. DATA MASTER KATEGORI MASIH GET DATA LIST ASSETS. </h5>
    <form-kategori 
        ref="formKategori" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </form-kategori>
    <edit-form-kategori 
        ref="editFormKategori" 
        v-on:saveSucceed="refreshTable" 
        v-on:closed="refreshTable">
    </edit-form-kategori>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseRow from '@/Components/BaseTable/BaseRow.vue';
import BaseTableAsset from '@/Components/BaseTableAsset';
import FormKategori from './FormKategori.vue';
import EditFormKategori from './EditFormKategori.vue';

export default {
        components : {
            headerPage,
            BaseRow,
            BaseTableAsset,
            FormKategori,
            EditFormKategori
            // FormCreateOrganization
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isExpanded : false,
                configTable : {                
                    isExpandable : true,
                    filterType : 'REGULAR', 
                },
                // rowFunctions :[
                // { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateAsset },
                // { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteAsset }, 
                // ],
                tbColumns : [             
                    { 
                        name : 'asset_id', 
                        title : 'ID Kategori', 
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                            { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                        ],
                    },
                    {
                        name: 'nama_asset',
                        title: 'Nama Kategori',
                        colType: 'text',
                        isSearchable: 'true'
                    },   
                    { 
                        name : 'created_at', 
                        title : 'Dibuat Pada', 
                        colType : 'text', 
                        isSearchable : false,
                    },
                    { 
                        name : 'created_by', 
                        title : 'Dibuat Oleh', 
                        colType : 'text', 
                        isSearchable : false,
                    },
                    { 
                        name : 'updated_at', 
                        title : 'Diubah Pada', 
                        colType : 'text', 
                        isSearchable : false,
                    },
                    { 
                        name : 'updated_by', 
                        title : 'Diubah Oleh', 
                        colType : 'text', 
                        isSearchable : false,
                    },
                    { 
                        name : 'deskripsi', 
                        title : 'Deskripsi', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'is_aktif', 
                        title : 'Visibilitas', 
                        colType : 'boolean', 
                        options : [
                            { value:0, text:'Aktif' },
                            { value:1, text:'Nonaktif' },
                        ]
                    },

                ], 
                updateFunction: this.updateData,
                deleteFunction: this.deleteData,
                buttons : [
                    { title : 'Data Kategori Baru', icon:'plus-circle', 'callback' : this.addKategori },
                ],
                searchUrl : '/assets',
                dataLists : null,

                isLoading : false,
                datas: [],
                // genderSelected: 1,
                options: [
                //     {text: 'Male', value:'male'},
                //     {text: 'Female', value:'female'},
                ],
                columns: [
                //     { name:"id", title: "IHS ID"},
                //     { name:"name", title: "Name" },
                ],
                tahun: '',
                params: [],
                searchUrl: '/assets'
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('group',['listGroup','deleteGroup','searchID', 'searchName']),
            ...mapMutations(['CLR_ERRORS']),

            // // getYear(params) {
            // //     let res = new Date(params);
            // //     let year = res.getFullYear();
            // //     return `${year}`;
            // // },

            // // submitFilterNama(){
            // //     this.datas = [];
            // //     this.isLoading = true;
            // //     this.params = [
            // //         {
            // //             'name' : document.getElementById('nama').value
            // //         }
            // //     ];
            // //     this.searchName(document.getElementById('nama').value).then((response) => {
            // //         this.isLoading = false;
            // //         for (let i=0;i<=response.entry.length-1;i++) {
            // //             this.datas.push({
            // //                 'ihs_number': response.entry[i]['resource'].id,
            // //                 'name': response.entry[i]['resource']['name'][0].text,
            // //             });
            // //         }
            // //         return this.datas;
            // //     });
            // // },

            // // submitFilterID(){
            // //     this.datas = [];
            // //     this.isLoading = true;
            // //     this.searchID(document.getElementById('ihs').value).then((response) => {
            // //         this.isLoading = false;
            // //         console.log(response);
            // //         this.datas.push({
            // //             'id': response.id,
            // //             'name': response.name,
            // //         });
            // //         return this.datas;
            // //     });
            // // },

            // // updateListData(data) {
            // // this.dataLists = null;
            // // if(data) { this.dataLists = data.data; }
            // // },


            // // Update kategori
            updateData(assets){
                this.CLR_ERRORS();
                this.$refs.editFormKategori.editKategori(assets.asset_id);        
            },

         
            /**Popup Modal untuk membuat Data Group baru */
            // Popup Tambah Data Group
            addKategori() {        
                this.CLR_ERRORS();
                this.$refs.formKategori.newKategori();        
            },

            refreshTable() {
                this.$refs.tableDataKategori.retrieveData();
            },

            // // Hapus Kategori
            deleteData(assets) {
                this.CLR_ERRORS();
                if(confirm(`Anda akan menghapus permanen data kategori ${assets.nama_asset}. Lanjutkan ?`)){
                    this.show.deleteKategori(assets.asset_id).then((response) => {
                        console.log(response);
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableDataKategori.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }
        }
    }
</script>