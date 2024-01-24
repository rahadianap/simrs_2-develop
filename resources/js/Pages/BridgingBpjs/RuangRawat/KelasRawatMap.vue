<template>
    <div class="uk-container uk-container-large" style="padding:0.5em;background-color: #fff;">
        <!-- <header-page title="KELAS PELAYANAN" subTitle="master kelas pelayanan dan harga"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div> -->
        <div style="margin-top:0;">
            <base-table ref="tableKelas"
                :columns = "tbColumns" 
                :config="configTable" 
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <wilayah-picker ref="kelasPicker" 
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
    name : 'kelas-rawat-map',
    components : {
        headerPage,
        BaseTable,
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
                    name : 'kelas_id', 
                    title : 'ID', 
                    colType : 'linkmenus',
                    functions: [
                        { 'title':'Pemetaan BPJS', 'icon':'file-edit','callback':this.updateData }, 
                    ],
                },   
                { 
                    name : 'kelas_nama', 
                    title : 'Nama', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Pemetaan BPJS', 'icon':'file-edit','callback':this.updateData },
                    ],
                },
                { 
                    name : 'kode_bpjs', 
                    title : 'Kode BPJS', 
                    colType : 'text',
                },
                { 
                    name : 'nama_bpjs', 
                    title : 'Nama BPJS', 
                    colType : 'text', 
                },
                { 
                    name : 'is_kelas_kamar', 
                    title : 'Kamar', 
                    colType : 'boolean', 
                    isSearchable : false,
                },
                { 
                    name : 'is_kelas_harga', 
                    title : 'Tarif', 
                    colType : 'boolean', 
                    isSearchable : false,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],

            selKelas : {
                kode_lokal : null,
                nama_lokal : null,
                bridging_group : 'BPJS',
                kode_bridging : null,
                nama_bridging : null,
            },

            searchUrl : '/bpjs/classes',
            kelasLists : null,
         }
    },
    mounted() {
        this.getInitData();
    },

    methods : {
        ...mapActions('kelas',['deleteKelas']),
        ...mapActions('bpjsReferensi',['refBpjsKelas','updateBpjsMapping']),
        ...mapMutations(['CLR_ERRORS']),

        getInitData(){
            this.CLR_ERRORS();
            this.refBpjsKelas().then((response) => {
                if (response.success == true) {
                    this.kelasLists = response.data;
                }
            });
        },

        refreshTable() {
            this.$refs.tableKelas.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.selKelas.nama_lokal = data.kelas_nama;
            this.selKelas.kode_lokal = data.kelas_id;
            this.selKelas.kode_bridging = null;
            this.selKelas.nama_bridging = null;

            this.$refs.kelasPicker.showModalPicker(this.kelasLists);
        },

        updateBpjsKode(data) {
            this.selKelas.kode_bridging = data.kode;
            this.selKelas.nama_bridging = data.nama;
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengubah kode bridging kelas rawat. Lanjutkan ?`)){
                this.updateBpjsMapping(this.selKelas).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKelas.retrieveData();
                        this.$refs.kelasPicker.closeModalPicker();
                    }
                    else { alert (response.message) }
                });
            };
        }

    }
}
</script>