<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <div>
            <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                <li>
                    <router-link :to = "{ name:'propinsi' }" class="uk-text-uppercase">
                        <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">WILAYAH</h4>
                    </router-link>                    
                </li>
                <li  v-if="kabupaten.propinsi_id !== null">
                    <router-link :to = "{ path:kotaPath }" class="uk-text-uppercase" style="font-weight:400;color:#333;">{{kabupaten.propinsi}}</router-link>
                </li>   
                <li v-if="kabupaten.kota !== null"><span style="font-weight:400;color:#333;font-size:14px;" class="uk-text-uppercase">KECAMATAN - KAB(KOTA) {{kabupaten.kota}}</span></li>           
            </ul>
        </div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKecamatan"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <wilayah-picker ref="kecPicker" 
            v-on:closed="refreshTable" 
            v-on:saveSucceed="refreshTable" 
            :fieldDatas = pickerColumns
            :proceedFunction = "updateBpjsKode">
        </wilayah-picker>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import FormKecamatan from '@/Pages/MasterData/Wilayah/components/FormKecamatan.vue';

import WilayahPicker from '@/Pages/BridgingBpjs/Wilayah/components/WilayahPicker';

export default {
    props : {
        kabupatenId : {type:String, required:true },
    },
    components : {
        BaseTable,
        FormKecamatan,
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
                    name : 'kecamatan_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Pemetaan', 'icon':'file-edit','callback':this.updateData },
                    ],
                },   
                { 
                    name : 'kecamatan', 
                    title : 'Kecamatan', 
                    colType : 'linkmenus',
                },   
                { 
                    name : 'kota', 
                    title : 'Kabupaten/kota', 
                    colType : 'text',
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
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            kabupaten : {
                kota : null,
                kota_id : null,
                propinsi_id : null,
                propinsi : null,
                kode_bpjs : null,
            },

            kecamatan : {
                kode_lokal : null,
                nama_lokal : null,
                bridging_group : 'BPJS',
                kode_bridging : null,
                nama_bridging : null,
            },

            kecamatanLists : null,

            headerTitle : '',
            headerDetail : '',
            searchUrl : `/bpjs/counties/${this.kabupatenId}`,
            kotaPath : null,
         }
    },

    created() {
        this.CLR_ERRORS();
        this.getKabupaten();
    },

    methods : {
        ...mapActions('kecamatan',['deleteKecamatan','dataKecamatan']),
        ...mapActions('bpjsReferensi',['refBpjsKecamatan','updateBpjsMapping']),
        ...mapActions('kabupaten',['dataKabupaten']),
        ...mapMutations(['CLR_ERRORS']),

        getKabupaten() {
            this.CLR_ERRORS();
            this.dataKabupaten(this.kabupatenId).then((response) => {
                if (response.success == true) {
                    this.fillKabupaten(response.data);
                }
                else { alert (response.message) }
            });
        },

        fillKabupaten(data) {
            console.log(data);
            this.kotaPath = `/bridging/wilayah/kabupaten/${data.propinsi_id}`;
            this.headerTitle = `KECAMATAN - KAB (KOTA) ${data.kota}`;
            this.headerDetail = `daftar kecamatan dari kabupaten (kota) ${data.kota}`;

            this.kabupaten.kota = data.kota;
            this.kabupaten.kota_id = data.kota_id;
            this.kabupaten.propinsi_id = data.propinsi_id;
            this.kabupaten.propinsi = data.propinsi;
            
            let bridgeData = data.bridging.find(item => item.bridging_group == 'BPJS');
            console.log(bridgeData);
            this.kabupaten.kode_bpjs = bridgeData.bridging_resource_id;
            
            this.refBpjsKecamatan(this.kabupaten.kode_bpjs).then((response) => {
                if (response.success == true) {
                    this.kecamatanLists = response.data;
                }
            });
        },

        refreshTable() {
            this.$refs.tableKecamatan.retrieveData();
        },

        updateData(data) {
            console.log(data);
            this.CLR_ERRORS();
            this.kecamatan.nama_lokal = data.kecamatan;
            this.kecamatan.kode_lokal = data.kecamatan_id;
            this.kecamatan.kode_bridging = null;
            this.kecamatan.nama_bridging = null;   

            this.refBpjsKecamatan(this.kabupaten.kode_bpjs).then((response) => {
                if (response.success == true) {
                    this.kecamatanLists = response.data;
                }
            });         
            
            this.$refs.kecPicker.showModalPicker(this.kecamatanLists);
        },

        updateBpjsKode(data) {
            this.kecamatan.kode_bridging = data.kode;
            this.kecamatan.nama_bridging = data.nama;
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengubah kode bridging kecamatan. Lanjutkan ?`)){
                this.updateBpjsMapping(this.kecamatan).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKecamatan.retrieveData();
                        this.$refs.kecPicker.closeModalPicker();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>