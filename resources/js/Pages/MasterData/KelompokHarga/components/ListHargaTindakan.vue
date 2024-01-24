<template>
    <div class="uk-container-xlarge hims-form-container" style="padding:0;min-height:70vh;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="closeListHarga" style="background-color:white;padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:2"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0;padding:0;" class="uk-text-uppercase">
                    {{bukuHarga.buku_nama}} 
                </h4>
                <p style="font-weight:300;margin:0;padding:0;font-size:12px;font-style:italic;">
                    detail harga pemeriksaan dan pelayanan
                </p>
            </div>
        </div>

        <div>
            <div style="margin-top:0.5em;">
                <base-table ref="tableListHarga"
                    :columns = "tbColumns" 
                    :config="configTable" 
                    :urlSearch="searchUrl"   
                    :buttons = "buttons"                  
                    v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="dataLists" style="min-weight:50vh;">
                            <row-harga-tindakan
                                v-for="dt in dataLists"
                                :rowFunctions="showModalHarga" 
                                :childApproveFunctions = "setujuiHarga"
                                :childEditFunctions = "editHarga"
                                :childDeleteFunctions = "deleteHarga"
                                :rowData="dt">
                            </row-harga-tindakan>
                        </tbody>
                    </template>
                </base-table>
            </div>
        </div>
        <modal-harga ref="modalHarga" v-on:modalHargaClosed="refreshDataListHarga"></modal-harga>
    </div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import ModalHarga from '@/Pages/MasterData/KelompokHarga/components/ModalHarga.vue';
import RowHargaTindakan from '@/Pages/MasterData/KelompokHarga/components/RowHargaTindakan.vue';

export default {
    name  : 'list-harga-tindakan',
    components : {
        BaseTable, ModalHarga, RowHargaTindakan
    },
    data() {
        return {
            isUpdate : true,
            bukuHarga : {
                client_id : null,
                buku_harga_id : null,
                buku_nama : null,
                deskripsi : null,
                is_standar_sistem : false,
                tgl_berlaku : null,
                jam_berlaku : null,
                is_aktif : true,
            },
            
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            rowFunctions : this.showModalHarga,

            tbColumns : [             
                { 
                    name : 'tindakan_id', 
                    title : 'ID', 
                    width : '150px',
                    colType : 'link',
                    linkCallback : this.showModalHarga,
                },   
                { 
                    name : 'tindakan_nama', 
                    title : 'Nama', 
                    colType : 'link', 
                    linkCallback : this.showModalHarga,
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text', 
                    isSearchable : false,
                },       
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    textAlign: ' center',
                    isSearchable : false,
                }
            ], 
            buttons : [
                { title : 'Setujui Semua', icon:'plus-circle', 'callback' : this.approveAllHarga },
            ],
            searchUrl : '',
            dataLists : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },   
    methods : {
        ...mapActions('bukuHarga',['dataBukuHarga','approveBukuHarga']),
        ...mapActions('harga',['approveHarga','deleteHarga']),
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data){
            this.dataLists = null;
            if(data) {
                this.dataLists = data.data;
            }
        },

        showListHarga(data) {
            if(data) {
                this.CLR_ERRORS();
                this.clearBukuHarga();            
                this.dataBukuHarga(data.buku_harga_id).then((response)=>{
                    if (response.success == true) {
                        this.fillBukuHarga(response.data);
                        this.isUpdate = true;
                    } else {
                        alert(response.message);
                    }
                })
            }
        },

        clearBukuHarga(){
            this.bukuHarga.client_id = null;
            this.bukuHarga.buku_harga_id = null;
            this.bukuHarga.buku_nama = null;
            this.bukuHarga.deskripsi = null;
            this.bukuHarga.jam_berlaku = null;
            this.bukuHarga.tgl_berlaku = null;
            this.bukuHarga.is_standar_sistem = false;
            this.bukuHarga.is_aktif = true;
        },

        fillBukuHarga(data){
            this.bukuHarga.client_id = data.client_id;
            this.bukuHarga.buku_harga_id = data.buku_harga_id;
            this.bukuHarga.buku_nama = data.buku_nama;
            this.bukuHarga.deskripsi = data.deskripsi;
            this.bukuHarga.jam_berlaku = data.jam_berlaku;
            this.bukuHarga.tgl_berlaku = data.tgl_berlaku;
            this.bukuHarga.is_standar_sistem = data.is_standar_sistem;
            this.bukuHarga.is_aktif = data.is_aktif;
            this.searchUrl = `/tariffs/items/lists/${data.buku_harga_id}`;
        },

        closeListHarga(){
            this.$emit('closeListHarga',true);
        },

        showModalHarga(data){
            if(data) {
                data['buku_harga_id'] = this.bukuHarga.buku_harga_id;
                data['buku_nama'] = this.bukuHarga.buku_nama;
                this.$refs.modalHarga.newHarga(data);
            }
        },

        editHarga(data){
            if(!data) { return false; }
            this.CLR_ERRORS();
            this.$refs.modalHarga.editHarga(data.harga_id);
        },

        deleteHarga(data){
            if(!data) { return false; }
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus item harga di ${data.tindakan_nama} kelas ${data.kelas_nama}. Lanjutkan ?`)){
                this.deleteHarga(data.harga_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableListHarga.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        setujuiHarga(data) {
            if(!data) { return false; }
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mennyetujui harga di ${data.buku_nama} dan mengubah harga lama. Lanjutkan ?`)){
                this.approveHarga(data.harga_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableListHarga.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        approveAllHarga(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mennyetujui seluruh harga di ${this.bukuHarga.buku_nama} dan mengubah harga lama. Lanjutkan ?`)){
                this.approveBukuHarga(this.bukuHarga.buku_harga_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableListHarga.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        refreshDataListHarga(){
            this.$refs.tableListHarga.retrieveData();
        }

    }
}
</script>