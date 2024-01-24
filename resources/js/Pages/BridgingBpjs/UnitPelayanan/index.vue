<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="PEMETAAN POLI" subTitle="pemetaan bridging unit kerja"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tablePoli"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl"
                v-on:updateListDataTable="updateListData">
                <template v-slot:tbl-body>
                    <tbody v-if="dataLists">
                        <row-map-poli v-for="dt in dataLists" 
                            :rowData="dt" 
                            :linkCallback = editDataPoli 
                            v-on:itemUpdated="refreshTable">
                        </row-map-poli>
                    </tbody>
                </template>
            </base-table>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowMapPoli from '@/Pages/BridgingBpjs/UnitPelayanan/RowMapPoli.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        RowMapPoli,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {                
                isExpandable : false,
                filterType : 'SIMPLE', 
            },
            pickerColumns : [
                { name : 'kode', title : 'Kode', colType : 'text', isCaption : false, },
                { name : 'nama', title : 'Nama', colType : 'text', isCaption : true, },
            ],
            rowFunctions : [
                { 'title':'Pemetaan JKN', 'icon':'file-edit','callback':this.updateDataJKN },
                { 'title':'Pemetaan BPJS', 'icon':'file-edit','callback':this.updateData },
                { 'title':'Pemetaan LIS', 'icon':'file-edit','callback':this.updateData },
                { 'title':'Pemetaan RIS', 'icon':'file-edit','callback':this.updateData },
            ],

            tbColumns : [                
                { 
                    name : 'unit_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Pemetaan JKN', 'icon':'file-edit','callback':this.updateDataJKN },
                        { 'title':'Pemetaan BPJS', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus Pemetaan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'unit_nama', 
                    title : 'Nama', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Pemetaan BPJS', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus Pemetaan', 'icon':'ban','callback':this.deleteData },   
                    ],
                },
                { 
                    name : 'inisial', 
                    title : 'Inisial', 
                    colType : 'text', 
                },  
                { 
                    name : 'jenis_registrasi', 
                    title : 'Jns Registrasi', 
                    colType : 'text', 
                },  
                { 
                    name : 'kepala_unit', 
                    title : 'Kepala Unit', 
                    colType : 'text', 
                },                
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean',
                },                
            ], 

            selData : {
                kode_lokal : null,
                nama_lokal : null,
                bridging_group : 'BPJS',
                kode_bridging : null,
                nama_bridging : null,
            },

            buttons : [
                //{ title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/units',
            dataLists : null,
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('unitPelayanan',['listUnitPelayanan','deleteUnitPelayanan']),
        ...mapActions('bpjsReferensi',['refBpjsPoli','updateBpjsMapping','removeBpjsMapping']),
        ...mapActions('jknAntrian',['jknRefPoli']),
        ...mapMutations(['CLR_ERRORS']),


        updateListData(data) {
            if(data) {
                if(data.data) { this.dataLists = data.data; }
                else { this.dataLists = null; }
            }
            else { this.dataLists = null; }
        },

        editDataPoli(data){

        },

        /**tampikan modal untuk membuat unit pelayanan baru */
        addData(){        
            this.CLR_ERRORS();
            //this.$refs.formUnitPelayanan.newUnitPelayanan();
            this.$router.push(`/master/unit/pelayanan/data/baru`);
        },

        refreshTable() {
            this.$refs.tablePoli.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.selData.kode_lokal = data.unit_id;
            this.selData.nama_lokal = data.unit_nama;
            this.selData.kode_bridging = null;
            this.selData.nama_bridging = null;
            
            this.$refs.poliPicker.showModalPicker();
        },

        updateDataJKN(data){
            this.CLR_ERRORS();
            // this.selData.kode_lokal = data.unit_id;
            // this.selData.nama_lokal = data.unit_nama;
            // this.selData.kode_bridging = null;
            // this.selData.nama_bridging = null;
            
            // this.$refs.poliPicker.showModalPicker();
            this.jknRefPoli().then((response) => {
                if (response.success == true) {
                    let items = response.data;
                    this.listItems = items.sort(function(a, b){return a.nmpoli - b.nmpoli});
                    console.log(this.listItems);
                }
            });
        },

        deleteData(data) {
            this.selData.kode_lokal = data.unit_id;
            this.selData.nama_lokal = data.unit_nama;
            this.selData.kode_bridging = data.kode_bpjs;
            this.selData.nama_bridging = data.nama_bpjs;
            
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus kode bridging poli (unit pelayanan). Lanjutkan ?`)){
                this.removeBpjsMapping(this.selData).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePoli.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        updateBpjsKode(data) {
            this.selData.kode_bridging = data.kode;
            this.selData.nama_bridging = data.nama;
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengubah kode bridging poli (unit pelayanan). Lanjutkan ?`)){
                this.updateBpjsMapping(this.selData).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePoli.retrieveData();
                        this.$refs.poliPicker.closeModalPicker();
                    }
                    else { alert (response.message) }
                });
            };
        }

    }
}
</script>