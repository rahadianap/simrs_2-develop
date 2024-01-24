<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="REFERENSI WILAYAH BPJS" subTitle="master data wilayah propinsi"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableRefPropinsi"
                :columns = "tbColumns" 
                :config="configTable"
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>            
        </div>
        <wilayah-picker ref="propPicker" 
            v-on:closed="refreshTable" 
            v-on:saveSucceed="refreshTable" 
            :fieldDatas = pickerColumns
            :proceedFunction = "updateBpjsKode">
        </wilayah-picker>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
// import FormPropinsi from '@/Pages/MasterData/Wilayah/components/FormPropinsi.vue';
// import RowPropinsi from '@/Pages/BridgingBpjs/Wilayah/Propinsi/RowPropinsi.vue';
import WilayahPicker from '@/Pages/BridgingBpjs/Wilayah/components/WilayahPicker';

export default {
    components : {
        headerPage,
        BaseTable,
        // FormPropinsi,
        // RowPropinsi,
        WilayahPicker,
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
            tbColumns : [             
                { 
                    name : 'propinsi_id', 
                    title : 'ID Prop', 
                    colType : 'linkmenus', 
                    colWidth : '100px',
                    functions: [
                        { 'title':'Daftar Kabupaten', 'icon':'file','callback':this.listKabupaten },
                        { 'title':'Ubah Pemetaan', 'icon':'file-edit','callback':this.updateData },
                    ],
                },   
                { 
                    name : 'propinsi', 
                    title : 'Propinsi', 
                    colType : 'text', 
                },

                { 
                    name : 'kode_bpjs', 
                    title : 'Kode BPJS', 
                    colType : 'text', 
                    colWidth : '100px',
                },   
                { 
                    name : 'nama_bpjs', 
                    title : 'Nama BPJS', 
                    colType : 'text',
                },                
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }         
            ], 
            buttons : [
                { title : 'Refresh Data', icon:'plus-circle', 'callback' : this.refreshData },
            ],
            selectedProp : {
                bridging_group : 'BPJS',
                propinsi_id : null,
                propinsi : null,
                kode_bridging : null,
                nama_bridging : null,
            },
            searchUrl : '/bpjs/provinces',
            dataLists : null,
            propinsiLists : null,
         }
    },
    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('propinsi',['deletePropinsi','updateBpjsPropinsi']),
        ...mapActions('bpjsReferensi',['refBpjsPropinsi']),

        ...mapMutations(['CLR_ERRORS']),

        updateListData(data) {
            if(data) {
                if(data.data) { this.dataLists = data.data; }
                else { this.dataLists = null; }
            }
            else { this.dataLists = null; }
        },

        initialize(){
            this.refBpjsPropinsi().then((response)=> {
                if (response.success == true) {
                    this.propinsiLists = response.data;
                }
            })
        },

        refreshData(){
            this.refBpjsPropinsi().then((response)=> {
                if (response.success == true) {
                    this.propinsiLists = response.data;
                    this.isLoading = false;
                    this.$refs.tableRefPropinsi.refreshData(data);
                } else {
                    alert(response.message);
                    this.isLoading = false;
                }
            })
        },

        /**tampikan daftar kode RL */
        listKabupaten(data){        
            this.CLR_ERRORS();
            this.$router.push(`/bridging/wilayah/kabupaten/${data.propinsi_id}`);
        },

        refreshTable() {
            this.$refs.tablePropinsi.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.selectedProp.propinsi_id  = data.propinsi_id;
            this.selectedProp.propinsi = data.propinsi;
            this.selectedProp.kode_bridging = null;
            this.selectedProp.nama_bridging = null;
            
            this.$refs.propPicker.showModalPicker(this.propinsiLists);
        },

        updateBpjsKode(data){
            this.selectedProp.kode_bridging = data.kode;
            this.selectedProp.nama_bridging = data.nama;
            
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengubah kode bridging propinsi. Lanjutkan ?`)){
                this.updateBpjsPropinsi(this.selectedProp).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableRefPropinsi.retrieveData();
                        this.$refs.propPicker.closeModalPicker();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>