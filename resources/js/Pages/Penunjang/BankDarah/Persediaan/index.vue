<template>
    <div class="uk-container" style="padding:1em;margin:0;">    
        <header-page title="PERSEDIAAN DARAH" subTitle="daftar persediaan darah"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:0;" class="uk-responsive">
            <base-table ref="tablePersediaan"
                :columns = "tbColumns" 
                :config = "configTable" 
                :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
            </base-table>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowPenerimaan from '@/Pages/Penunjang/BankDarah/Penerimaan/components/RowPenerimaan.vue';

export default {
    name : 'persediaan',
    components : {
        BaseTable,
        RowPenerimaan,
        headerPage,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : { isExpandable : false, filterType : 'SIMPLE', },
            tbColumns : [
                { name : 'no_labu', title : 'No Labu', colType : 'link', linkCallback : this.showPenerimaan},
                { name : 'gol_darah', title : 'GOL DARAH', colType : 'text',  width : '80px', textAlign:'center'},
                { name : 'rhesus', title : 'RHESUS', colType : 'text',  width : '50px', textAlign:'center'},
                { name : 'group_darah', title : 'GROUP', colType : 'text',},
                { name : 'volume', title : 'VOL', colType : 'text', },
                { name : 'tgl_kadaluarsa', title : 'Kadaluarsa', colType : 'text', },
                { name : 'darah_dist_id', title : 'DISTRIBUSI', colType : 'link', },
                { name : 'is_terpakai', title : 'terpakai', colType : 'boolean', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
                
            ], 
            searchUrl : '/bloods/distributions/product?terpakai=ALL',
            dataLists : null,
         }
    },
    mounted() {
        this.preparedData();
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),

        preparedData(){
            if(this.searchUrl == null || this.searchUrl == '' ) {
                this.searchUrl = '/bloods/distributions/product?terpakai=ALL';
            } 
        },

        updateListData(data) {
            if(data) {
                if(data.data) { this.dataLists = data.data; }
                else { this.dataLists = null; }
            }
            else { this.dataLists = null; }
        },

        refreshTable(){ this.$refs.tablePersediaan.retrieveData(); },

        showPenerimaan(data){
            alert('tampilkan penerimaan');
        }
    }
}
</script>