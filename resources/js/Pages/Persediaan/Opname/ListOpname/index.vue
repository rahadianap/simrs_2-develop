<template>
    <div>
        <header-page title="STOCK OPNAME" subTitle="daftar pelaksanaan stock opname"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableOpname"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <create-opname ref="formCreateOpname" v-on:createClosed="refreshTable"></create-opname>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import CreateOpname from '@/Pages/Persediaan/Opname/components/CreateOpname.vue'

export default {
    name : 'list-opname',
    props : {
        addFunction : { type : Function, required:false, default: null },
        editFunction : { type : Function, required:false, default: null },
        showFunction : { type : Function, required:false, default: null },
    },

    components : {
        headerPage,
        BaseTable,
        CreateOpname
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
                    name : 'so_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Proses/Update', 'icon':'file-edit','callback':this.prosesData },   
                        { 'title':'Ubah Opname', 'icon':'ban','callback':this.updateData },   
                        { 'title':'Data Opname', 'icon':'file','callback':this.previewData },   
                        { 'title':'Hapus Opname', 'icon':'ban','callback':this.deleteData },
                        // { 'title':'Persetujuan', 'icon':'ban','callback':this.approveData },
                    ],
                },
                { 
                    name : 'depo_nama', 
                    title : 'Depo', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Proses/Update', 'icon':'file-edit','callback':this.prosesData },   
                        { 'title':'Ubah Opname', 'icon':'ban','callback':this.updateData },   
                        { 'title':'Data Opname', 'icon':'file','callback':this.previewData },   
                        { 'title':'Hapus Opname', 'icon':'ban','callback':this.deleteData },
                        // { 'title':'Persetujuan', 'icon':'ban','callback':this.approveData },
                    ],
                },
                { 
                    name : 'catatan', 
                    title : 'Catatan', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'tgl_rencana', 
                    title : 'Rencana', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'tgl_so', 
                    title : 'Realisasi', 
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
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addOpname },
            ],
            searchUrl : '/stock/opname'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('stockOpname',['listOpname','deleteOpname','proceedOpname','approveOpname']),
        ...mapMutations(['CLR_ERRORS']),

        /**tampikan modal untuk membuat opname baru */
        addOpname(){
            this.CLR_ERRORS();
            this.$refs.formCreateOpname.newOpname();        
        },

        refreshTable() {
            this.$refs.tableOpname.retrieveData();
        },

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
            if(data.status !== 'PROSES' && data.status !== 'RENCANA' ) {
                alert('stock opname yang sudah/sedang berjalan tidak dapat diproses.');
                return false;
            }

            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus rencana / data sementara opname depo ${data.depo_nama}. Lanjutkan ?`)){
                this.deleteOpname(data.so_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableOpname.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        prosesData(data) {
            let opname_id = data.so_id;
            if(data.status == 'RENCANA') {
                if(confirm(`Proses ini akan mengunci depo dan memulai proses data ${data.depo_nama}. Status tidak dapat diubah kembali, kecuali dibatalkan. Lanjutkan ?`)){
                    this.proceedOpname(data.so_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$router.push({ name: 'formOpname', params: { opnameId: opname_id}});
                            // this.$refs.tableOpname.retrieveData();
                            // this.updateData(data);
                        }
                        else { alert (response.message) }
                    });
                };
            }

            else if (data.status == 'PROSES'){
                //this.editFunction(data);
                this.$router.push({ name: 'formOpname', params: { opnameId: data.so_id}});
            }

            else {
                alert('data sudah tidak dapat diubah.');
            }
        },

        approveData(data) {
            if(confirm(`Proses ini akan mengakhiri proses Stock Opname dan membuka kunci depo. Status tidak dapat diubah kembali. Lanjutkan ?`)){
                this.approveOpname(data.so_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableOpname.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        previewData(data) {
            if(data.status == 'SELESAI') {
                //this.showFunction(data);
                this.$router.push({ name: 'viewOpname', params: { opnameId: data.so_id}});
            }
            else {
                alert('Stock Opname masih dalam proses. data akhir belum bisa ditampilkan.')
            }
        }
    }
}
</script>