<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <header-page title="PRODUKSI PERSEDIAAN" subTitle="daftar pelaksanaan produksi item persediaan"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableProduksi"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl"  v-on:updateListDataTable="updateListData">

                <template v-slot:tbl-body>
                    <tbody v-if="productionLists" style="min-weight:50vh;">
                        <row-produksi 
                            v-for="dt in productionLists" 
                            :rowData="dt"
                            :rowFunctions = rowFunctions>
                        </row-produksi>
                    </tbody>
                </template>

            </base-table>
        </div>

        <preview-produksi ref="previewProduksi"></preview-produksi>
        <form-produksi ref="formProduksi" v-on:formProduksiClosed="formClosed"></form-produksi>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import PreviewProduksi from '@/Pages/Persediaan/Produksi/components/PreviewProduksi.vue';
import FormProduksi from '@/Pages/Persediaan/Produksi/components/FormProduksi.vue';
import RowProduksi from '@/Pages/Persediaan/Produksi/components/RowProduksi.vue';

export default {
    components : {
        headerPage,
        FormProduksi,
        BaseTable,
        PreviewProduksi,
        RowProduksi,
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
            rowFunctions : [
                { 'title':'Ubah Data', 'icon':'ban','callback':this.updateProduksi },   
                { 'title':'Lihat Data', 'icon':'file','callback':this.dataProduksi },   
                { 'title':'Persetujuan', 'icon':'ban','callback':this.setujuiProduksi },
                { 'title':'Hapus Data', 'icon':'ban','callback':this.hapusProduksi },
            ],
            tbColumns : [                
                { 
                    name : 'produksi_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'ban','callback':this.updateProduksi },   
                        { 'title':'Lihat Data', 'icon':'file','callback':this.dataProduksi },   
                        { 'title':'Persetujuan', 'icon':'ban','callback':this.setujuiProduksi },
                        { 'title':'Hapus Data', 'icon':'ban','callback':this.hapusProduksi },
                    ],
                },
                { 
                    name : 'depo_nama', 
                    title : 'Depo', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'ban','callback':this.updateProduksi },   
                        { 'title':'Lihat Data', 'icon':'file','callback':this.dataProduksi },   
                        { 'title':'Persetujuan', 'icon':'ban','callback':this.setujuiProduksi },
                        { 'title':'Hapus Data', 'icon':'ban','callback':this.hapusProduksi },
                    ],
                },
                { 
                    name : 'produk_hasil', 
                    title : 'Produk Hasil', 
                    colType : 'text', 
                },
                { 
                    name : 'catatan', 
                    title : 'Catatan', 
                    colType : 'text',
                },
                { 
                    name : 'tgl_selesai', 
                    title : 'Tgl Selesai', 
                    colType : 'text', 
                },
                { 
                    name : 'status', 
                    title : 'Status', 
                    colType : 'text', 
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                },
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addProduksi },
            ],
            searchUrl : '/items/productions',
            productionLists : null,
         }
    },

    methods : {
        ...mapActions('produksi',['listProduksi','deleteProduksi','dataProduksi','approveProduksi']),
        ...mapActions('users',['isUserDepoAuthorized']),
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data) {
            this.productionLists = null;
            if(data) { this.productionLists = data.data; }
        },

        addProduksi(){
            this.CLR_ERRORS();
            this.$refs.formProduksi.newProduksi();  
        },

        updateProduksi(data){
            if(data.status == 'RENCANA') {
                this.CLR_ERRORS();
                this.isUserDepoAuthorized(data.depo_id).then((response)=> {
                    if (response.success == true) {
                        this.$refs.formProduksi.editProduksi(data);    
                    }
                    else { 
                        alert (response.message); 
                        this.$refs.tableProduksi.retrieveData();
                    }
                });
            }
            else {
                alert('Data dengan status bukan RENCANA sudah tidak dapat diubah.');
            }
        },



        formClosed(){
            this.$refs.tableProduksi.retrieveData();
        },

        dataProduksi(data){
            this.$refs.previewProduksi.viewData(data.produksi_id);
        },

        hapusProduksi(data) {
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

        setujuiProduksi(data) {
            
            if(data.status !== 'RENCANA') {
                alert('Hanya data dengan status RENCANA yang bisa diubah / disetujui.');
            }
            else {
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
        },
    }

}
</script>