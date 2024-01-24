<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <div>
            <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                <li>
                    <router-link :to = "{ name:'propinsi' }" class="uk-text-uppercase">
                        <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">WILAYAH</h4>
                    </router-link>                    
                </li>
                <li  v-if="kecamatan.propinsi !== null">
                    <router-link :to = "{ path:kotaPath }" class="uk-text-uppercase" style="font-weight:400;color:#333;">{{kecamatan.propinsi}}</router-link>
                </li>   
                <li  v-if="kecamatan.kota !== null">
                    <router-link :to = "{ path:kecamatanPath }" class="uk-text-uppercase" style="font-weight:400;color:#333;">{{kecamatan.kota}}</router-link>
                </li>
                <li v-if="kecamatan.kota !== null"><span style="font-weight:400;color:#666;font-size:14px;" class="uk-text-uppercase">LIST KELURAHAN - KEC. {{kecamatan.kecamatan}}</span></li>           
            </ul>
        </div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableKelurahan"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <form-kelurahan ref="formKelurahan" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-kelurahan>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import FormKelurahan from '@/Pages/MasterData/Wilayah/components/FormKelurahan.vue';

export default {
    props : {
        kecamatanId : {type:String, required:true },
    },
    components : {
        BaseTable,
        FormKelurahan,
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
                    name : 'kelurahan_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus Data', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'kelurahan', 
                    title : 'Kelurahan', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus Data', 'icon':'trash','callback':this.deleteData },   
                    ],
                },  
                { 
                    name : 'kodepos', 
                    title : 'Kodepos', 
                    colType : 'text', 
                    isSearchable : false,
                },   
                { 
                    name : 'kecamatan.kecamatan', 
                    title : 'Kecamatan', 
                    colType : 'text',
                },   
                { 
                    name : 'kota.kota', 
                    title : 'Kabupaten / Kota', 
                    colType : 'text',
                },   
                { 
                    name : 'propinsi.propinsi', 
                    title : 'Propinsi', 
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

            kecamatan : {
                kecamatan : null,
                kecamatan_id : null,
                kota : null,
                kota_id : null,
                propinsi_id : null,
                propinsi : null,
            },

            headerTitle : '',
            headerDetail : '',
            searchUrl : `/districts?kecamatan=${this.kecamatanId}`,
            kotaPath : '',
            kecamatanPath : '',
         }
    },

    created() {
        this.CLR_ERRORS();
        this.getKecamatan();
    },

    methods : {
        ...mapActions('kelurahan',['deleteKelurahan','dataKelurahan']),
        ...mapActions('kecamatan',['dataKecamatan']),
        ...mapMutations(['CLR_ERRORS']),

        getKecamatan() {
            this.CLR_ERRORS();
            this.dataKecamatan(this.kecamatanId).then((response) => {
                if (response.success == true) {
                    this.kecamatan = response.data;
                    this.kotaPath = `/master/wilayah/kabupaten/${this.kecamatan.propinsi_id}`;
                    this.kecamatanPath = `/master/wilayah/kecamatan/${this.kecamatan.kota_id}`;
                }
                else { alert (response.message) }
            });
        },

        addData(){
            this.CLR_ERRORS();
            this.$refs.formKelurahan.newKelurahan(this.kecamatan);
        },

        refreshTable() {
            this.$refs.tableKelurahan.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKelurahan.editKelurahan(this.kecamatan,data.kelurahan_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan kelurahan ${data.kelurahan}. Lanjutkan ?`)){
                this.deleteKelurahan(data.kelurahan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKelurahan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>