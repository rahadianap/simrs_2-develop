<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <ul id="mainTabGroupHarga" uk-tab style="padding:0;" class="uk-hidden">
            <li><a href="#" style="font-size:14px;font-weight:500;">ITEM 1</a></li>
            <li><a href="#" style="font-size:14px;font-weight:500;">ITEM 2</a></li>           
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li>
                <div class="uk-container uk-container-xlarge" style="padding:0;">
                    <header-page title="HARGA DAN KELOMPOK" subTitle="kelompok harga dan detail harga pelayanan"></header-page>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:1em;">
                        <base-table ref="tableKelompokHarga"
                            :columns = "tbColumns" 
                            :config="configTable" 
                            :buttons = "buttons"
                            :urlSearch="searchUrl">
                        </base-table>
                    </div>
                </div>
            </li>
            <li>
                <list-harga-tindakan 
                    ref="listHargaTindakan" 
                    v-on:saveSucceed="refreshTable" 
                    v-on:closeListHarga="refreshTable">
            </list-harga-tindakan>
            </li>
        </ul>
        <modal-kelompok-harga ref="modalKelompokHarga" v-on:GroupTariffClosed="refreshTable"></modal-kelompok-harga>
    </div>
    
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import ModalKelompokHarga from '@/Pages/MasterData/KelompokHarga/components/ModalKelompokHarga.vue';
import ListHargaTindakan from '@/Pages/MasterData/KelompokHarga/components/ListHargaTindakan.vue';
import FormPurchaseReceive from '@/Pages/Persediaan/PurchaseReceive/components/FormPurchaseReceive.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        ModalKelompokHarga,
        FormPurchaseReceive,
        ListHargaTindakan,
    },
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
            
            tbColumns : [             
                { 
                    name : 'buku_harga_id', 
                    title : 'ID', 
                    width : '150px',
                    colType : 'linkmenus',
                    functions: [
                        { 'title':'Daftar Harga', 'icon':'list','callback':this.detailHarga }, 
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData }, 
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'buku_nama', 
                    title : 'Nama', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Daftar Harga', 'icon':'list','callback':this.detailHarga }, 
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData }, 
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text', 
                    isSearchable : false,
                },
                { 
                    name : 'tgl_berlaku', 
                    title : 'Tgl. Berlaku', 
                    textAlign: ' center',
                    colType : 'text', 
                },
                { 
                    name : 'jam_berlaku', 
                    title : 'Jam Berlaku', 
                    textAlign: ' center',
                    colType : 'text', 
                },
                { 
                    name : 'is_standar_sistem', 
                    title : 'Standar', 
                    colType : 'boolean', 
                    textAlign: ' center',
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
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/tariffs/groups',
            dataLists : null,
            }
    },
    methods: {
        ...mapActions('purchaseReceive',['listPOR','deletePOR', 'updateApprovePOR', 'updateCancelPOR', 'updateProsesPOR']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            this.$refs.modalKelompokHarga.newBukuHarga();
        },

        refreshTable() {
            this.$refs.tableKelompokHarga.retrieveData();
            UIkit.switcher('#mainTabGroupHarga').show(0);

        },
        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.modalKelompokHarga.editBukuHarga(data.buku_harga_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus kelas ${data.buku_nama}. Lanjutkan ?`)){
                this.deleteKelas(data.kelas_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKelompokHarga.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        detailHarga(data){
            if(data) {
                this.$refs.listHargaTindakan.showListHarga(data);
                UIkit.switcher('#mainTabGroupHarga').show(1);
            }
        }
    }
}
</script>