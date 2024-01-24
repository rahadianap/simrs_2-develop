<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <div>
            <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                <li>
                    <router-link :to = "{ name:'refpropinsi' }" class="uk-text-uppercase">
                        <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">WILAYAH</h4>
                    </router-link>                    
                </li>
                <li v-if="propinsi.propinsi !== null"><span style="font-weight:400;color:#333;font-size:14px;"  class="uk-text-uppercase">KABUPATEN (KOTA) - PROP. {{propinsi.propinsi}}</span></li>           
            </ul>
        </div>
        <div style="padding:0;margin:0;">
            <p style="margin:0;padding:0;font-size: 10px;">{{ propinsi.kode_bridging }} - {{ propinsi.nama_bridging }}</p>
        </div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKabupaten"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <wilayah-picker ref="kabPicker" 
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
import FormKabupaten from '@/Pages/MasterData/Wilayah/components/FormKabupaten.vue';
import WilayahPicker from '@/Pages/BridgingBpjs/Wilayah/components/WilayahPicker';

export default {
    props : {
        propinsiId : {type:String, required:true },
    },
    components : {
        BaseTable,
        FormKabupaten,
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
                    name : 'kota_id', 
                    title : 'ID', 
                    colWidth:'120px',
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Lihat Daftar Kecamatan', 'icon':'plus-circle','callback':this.listKecamatan },
                        { 'title':'Ubah Pemetaan', 'icon':'file-edit','callback':this.updateData },
                    ],
                },
                { 
                    name : 'kota', 
                    title : 'Kabupaten / Kota', 
                    colType : 'text', 
                },   
                { 
                    name : 'jenis', 
                    title : 'Jenis', 
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
                    colWidth:'50px',
                    colType : 'boolean', 
                    isSearchable : false,
                }
            ], 
            buttons : [
                { title : 'Refresh', icon:'refresh', 'callback' : this.refreshTable },
            ],
            
            propinsi : {
                propinsi : null,
                propinsi_id : null,
                kode_bridging : null,
                nama_bridging : null,
            },

            kabupaten : {
                kode_lokal : null,
                nama_lokal : null,
                bridging_group : 'BPJS',
                kode_bridging : null,
                nama_bridging : null,
            },
            kabupatenLists : null,

            headerTitle : '',
            headerDetail : '',
            searchUrl : `/bpjs/cities?propinsi=${this.propinsiId}`
         }
    },

    created() {
        this.CLR_ERRORS();
        this.getPropinsi();
    },

    methods : {
        ...mapActions('kabupaten',['deleteKabupaten','dataKabupaten']),
        ...mapActions('bpjsReferensi',['refBpjsKabupaten','updateBpjsMapping']),
        ...mapActions('propinsi',['dataPropinsi']),
        ...mapMutations(['CLR_ERRORS']),

        getPropinsi() {
            this.CLR_ERRORS();
            this.dataPropinsi(this.propinsiId).then((response) => {
                if (response.success == true) {
                    this.propinsi = response.data;
                    this.fillPropinsi(response.data);
                }
                else { alert (response.message) }
            });
        },

        fillPropinsi(data) {
            this.propinsi.propinsi_id = data.propinsi_id;
            this.propinsi.propinsi = data.propinsi;
            let bridgeData = data.bridging.find(item => item.bridging_group == 'BPJS');
            this.propinsi.kode_bridging = bridgeData.bridging_resource_id;
            this.propinsi.nama_bridging = bridgeData.bridging_resource_name;
            this.headerTitle = `KABUPATEN (KOTA) - ${this.propinsi.propinsi}`;
            this.headerDetail = `daftar kabupaten kota dari propinsi ${this.propinsi.propinsi}`;
            this.getRefKabupaten();
        },

        getRefKabupaten(){
            this.refBpjsKabupaten(this.propinsi.kode_bridging).then((response) => {
                if (response.success == true) {
                    this.kabupatenLists = response.data;
                }
            });
        },

        /**tampikan daftar kode RL */
        listKecamatan(data){        
            this.CLR_ERRORS();
            this.$router.push(`/bridging/wilayah/kecamatan/${data.kota_id}`);
        },

        refreshTable() {
            this.$refs.tableKabupaten.retrieveData();
        },

        updateData(data) {
            //console.log(data);
            this.CLR_ERRORS();
            this.kabupaten.nama_lokal = data.kota;
            this.kabupaten.kode_lokal = data.kota_id;
            this.kabupaten.kode_bridging = null;
            this.kabupaten.nama_bridging = null;

            this.$refs.kabPicker.showModalPicker(this.kabupatenLists);
        },

        updateBpjsKode(data) {
            this.kabupaten.kode_bridging = data.kode;
            this.kabupaten.nama_bridging = data.nama;
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengubah kode bridging kabupaten kote. Lanjutkan ?`)){
                this.updateBpjsMapping(this.kabupaten).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKabupaten.retrieveData();
                        this.$refs.kabPicker.closeModalPicker();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>