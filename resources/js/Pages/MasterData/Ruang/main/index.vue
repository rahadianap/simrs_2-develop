<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="RUANG DAN BED" subTitle="master data ruang unit dan bed inap"></header-page>
        <div>
            <base-table ref="tableRuang"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';

export default {
    components : {
        headerPage,
        BaseTable,
    },
    data() {
        return {
            configTable : {                
                isExpandable : false,
                filterType : 'SIMPLE', 
            },
            tbColumns : [                
                { 
                    name : 'ruang_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'unit_nama', 
                    title : 'Nama Unit', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'ruang_nama', 
                    title : 'Nama Ruang', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'lokasi', 
                    title : 'Lokasi', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'deskripsi', 
                    title : 'Deskripsi', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ], 
            buttons : [
                { title : 'Ruang Baru', icon:'plus-circle', 'callback' : this.addRuang },
            ],
            searchUrl : '/rooms'
        }
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('ruang',['deleteRuang']),

        addRuang() {
            this.CLR_ERRORS();
            this.$router.push(`/master/ruang/baru`);
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$router.push(`/master/ruang/${data.ruang_id}`);
        },
        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus ruang ${data.ruang_nama}. Lanjutkan ?`)){
                this.deleteRuang(data.ruang_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableRuang.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>