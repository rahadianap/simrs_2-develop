<template>
    <div class="uk-container uk-container-large" style="padding:0.5em;background-color: #fff;">
        <!-- <header-page title="RUANG DAN BED" subTitle="master data ruang unit dan bed inap"></header-page> -->
        <div>
            <base-table ref="tableRuang"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <wilayah-picker ref="ruangPicker" 
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
import WilayahPicker from '@/Pages/BridgingBpjs/Wilayah/components/WilayahPicker';

export default {
    name : 'ruang-rawat-map',
    components : {
        headerPage,
        BaseTable,
        WilayahPicker,
    },
    data() {
        return {
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
                    name : 'ruang_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Pemetaan', 'icon':'file-edit','callback':this.updateData },
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
                    name : 'kode_bpjs', 
                    title : 'Kode BPJS', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'nama_bpjs', 
                    title : 'Nama BPJS', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ], 

            selData : {
                kode_lokal : null,
                nama_lokal : null,
                bridging_group : 'BPJS',
                kode_bridging : null,
                nama_bridging : null,
            },
            ruangLists : null,

            buttons : [
                //{ title : 'Ruang Baru', icon:'plus-circle', 'callback' : this.addRuang },
            ],
            searchUrl : '/bpjs/rooms'
        }
    },

    mounted() {
        //this.getInitData();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('bpjsReferensi',['refBpjsRuang','updateBpjsMapping']),
        ...mapActions('ruang',['deleteRuang']),

        refreshTable() {
            this.CLR_ERRORS();
            this.$refs.tableRuang.retrieveData();
        },

        getInitData(){
            this.ruangLists = null;
            this.CLR_ERRORS();
            this.refBpjsRuang().then((response) => {
                if (response.success == true) {
                    this.ruangLists = response.data;
                }
            });
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.selData.nama_lokal = data.ruang_nama;
            this.selData.kode_lokal = data.ruang_id;
            this.selData.kode_bridging = null;
            this.selData.nama_bridging = null;
            if(!this.ruangLists) { this.getInitData(); }

            this.$refs.ruangPicker.showModalPicker(this.ruangLists);
        },

        updateBpjsKode(data) {
            this.selData.kode_bridging = data.kode;
            this.selData.nama_bridging = data.nama;
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengubah kode bridging ruang rawat. Lanjutkan ?`)){
                this.updateBpjsMapping(this.selData).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableRuang.retrieveData();
                        this.$refs.ruangPicker.closeModalPicker();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>