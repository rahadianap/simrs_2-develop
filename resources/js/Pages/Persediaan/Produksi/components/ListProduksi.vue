<template>
    <div>
        <header-page title="PRODUKSI PERSEDIAAN" subTitle="daftar pelaksanaan produksi item persediaan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableProduksi"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <preview-produksi ref="previewProduksi"></preview-produksi>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import PreviewProduksi from '@/Pages/Persediaan/Produksi/components/PreviewProduksi.vue';


export default {
    name : 'list-produksi',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
        showFunction : { type : Function, required:false, default: null },
    },

    components : {
        headerPage,
        BaseTable,
        PreviewProduksi
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
            rowFunctions : [
                { 'title':'Ubah Data', 'icon':'ban','callback':this.editFunction },   
                { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                { 'title':'Persetujuan', 'icon':'ban','callback':this.approveData },
                { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
            ],
            tbColumns : [                
                { 
                    name : 'produksi_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'ban','callback':this.editFunction },   
                        { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                        { 'title':'Persetujuan', 'icon':'ban','callback':this.approveData },
                        { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
                    ],
                },
                { 
                    name : 'depo_nama', 
                    title : 'Depo', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'ban','callback':this.editFunction },   
                        { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                        { 'title':'Persetujuan', 'icon':'ban','callback':this.approveData },
                        { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
                    ],
                },
                { 
                    name : 'produk_hasil', 
                    title : 'Produk Hasil', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'catatan', 
                    title : 'Catatan', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'tgl_produksi', 
                    title : 'Tgl Dibuat', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'tgl_selesai', 
                    title : 'Tgl Selesai', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'status', 
                    title : 'Status', 
                    colType : 'text', 
                    isSearchable : true,
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
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addFunction },
            ],
            searchUrl : '/items/productions'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('produksi',['listProduksi','deleteProduksi','dataProduksi','approveProduksi']),
        ...mapMutations(['CLR_ERRORS']),

        updateData(data){
            if(data.status == 'RENCANA') {
                this.CLR_ERRORS();
                this.$refs.formCreateOpname.editOpname(data.so_id);
            }
            else {
                alert('Proses opname sedang berjalan/ sudah dihapus / sudah selesai. Data sudah tidak dapat diubah.');
            }
        },

        deleteData(data) {
            if(data.status !== 'PRODUKSI' && data.status !== 'RENCANA' ) {
                alert('produksi yang sudah/sedang berjalan tidak dapat diproses.');
                return false;
            }

            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus rencana / data sementara produksi ${data.produk_hasil}. Lanjutkan ?`)){
                this.deleteProduksi(data.produksi_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableProduksi.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        approveData(data) {
            if(data.status == 'RENCANA'){
                if(confirm(`Proses ini akan mengakhiri proses produksi. Data sudah tidak dapat diubah. Lanjutkan ?`)){
                    this.approveProduksi(data.produksi_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$refs.tableProduksi.retrieveData();
                        }
                        else { alert (response.message) }
                    });
                };
            }
            else {
                alert('Hanya data dengan status RENCANA yang bisa diubah / disetujui.');
            }
        },

        previewData(data) {
            this.$refs.previewProduksi.viewData(data.produksi_id);
        },

        refreshTable(){
            this.$refs.tableProduksi.retrieveData();
        }
    }
}
</script>