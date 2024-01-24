<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <div>
            <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                <li>
                    <router-link :to = "{ name:'propinsi' }" class="uk-text-uppercase">
                        <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">WILAYAH</h4>
                    </router-link>                    
                </li>
                <li  v-if="kabupaten.propinsi !== null">
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
        <form-kecamatan ref="formKecamatan" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-kecamatan>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import FormKecamatan from '@/Pages/MasterData/Wilayah/components/FormKecamatan.vue';

export default {
    props : {
        kabupatenId : {type:String, required:true },
    },
    components : {
        BaseTable,
        FormKecamatan,
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
                    name : 'kecamatan_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Lihat Daftar Kelurahan', 'icon':'plus-circle','callback':this.listKelurahan },
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus Data', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'kecamatan', 
                    title : 'Kecamatan', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Lihat Daftar Kelurahan', 'icon':'file','callback':this.listKelurahan },
                        { 'title':'Ubah Data', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus Data', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'kota.kota', 
                    title : 'Kabupaten/kota', 
                    colType : 'text',
                },   
                { 
                    name : 'propinsi.propinsi', 
                    title : 'Propinsi', 
                    colType : 'text',
                },  
                { 
                    name : 'is_prioritas', 
                    title : 'Prioritas', 
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
            kabupaten : {
                kota : null,
                kota_id : null,
                propinsi_id : null,
                propinsi : null,
            },
            headerTitle : '',
            headerDetail : '',
            searchUrl : `/counties?kota=${this.kabupatenId}`,
            kotaPath : `/master/wilayah/kabupaten`,
         }
    },

    created() {
        this.CLR_ERRORS();
        this.getKabupaten();
    },

    methods : {
        ...mapActions('kecamatan',['deleteKecamatan','dataKecamatan']),
        ...mapActions('kabupaten',['dataKabupaten']),
        ...mapMutations(['CLR_ERRORS']),

        getKabupaten() {
            this.CLR_ERRORS();
            this.dataKabupaten(this.kabupatenId).then((response) => {
                if (response.success == true) {
                    this.kabupaten = response.data;
                    this.kotaPath = `/master/wilayah/kabupaten/${this.kabupaten.propinsi_id}`;
                    this.headerTitle = `KECAMATAN - KAB (KOTA) ${this.kabupaten.kota}`;
                    this.headerDetail = `daftar kecamatan dari kabupaten (kota) ${this.kabupaten.kota}`;
                }
                else { alert (response.message) }
            });
        },

        /**tampikan daftar kode RL */
        listKelurahan(data){        
            this.CLR_ERRORS();
            this.$router.push(`/master/wilayah/kelurahan/${data.kecamatan_id}`);
        },

        addData(){
            this.CLR_ERRORS();
            this.$refs.formKecamatan.newKecamatan(this.kabupaten);
        },

        refreshTable() {
            this.$refs.tableKecamatan.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKecamatan.editKecamatan(this.kabupaten,data.kecamatan_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan kecamatan ${data.kecamatan}. Lanjutkan ?`)){
                this.deleteKecamatan(data.kecamatan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKecamatan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>