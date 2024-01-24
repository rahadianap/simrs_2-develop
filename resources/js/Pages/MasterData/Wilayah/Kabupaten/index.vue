<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <div>
            <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                <li>
                    <router-link :to = "{ name:'propinsi' }" class="uk-text-uppercase">
                        <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">WILAYAH</h4>
                    </router-link>                    
                </li>
                <li v-if="propinsi.propinsi !== null"><span style="font-weight:400;color:#333;font-size:14px;"  class="uk-text-uppercase">KABUPATEN (KOTA) - PROP. {{propinsi.propinsi}}</span></li>           
            </ul>
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
        <form-kabupaten ref="formKabupaten" v-on:closed="refreshTable" v-on:saveSucceed="refreshTable"></form-kabupaten>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import FormKabupaten from '@/Pages/MasterData/Wilayah/components/FormKabupaten.vue';

export default {
    props : {
        propinsiId : {type:String, required:true },
    },
    components : {
        BaseTable,
        FormKabupaten,
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
                    name : 'kota_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Lihat Daftar Kecamatan', 'icon':'plus-circle','callback':this.listKecamatan },
                        { 'title':'Ubah Kabupaten', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus Kabupaten', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'propinsi.propinsi', 
                    title : 'Propinsi', 
                    colType : 'text',
                },   
                { 
                    name : 'kota', 
                    title : 'Kabupaten / Kota', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Lihat Daftar Kecamatan', 'icon':'plus-circle','callback':this.listKecamatan },
                        { 'title':'Ubah Kabupaten', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Hapus Kabupaten', 'icon':'trash','callback':this.deleteData },   
                    ],
                },   
                { 
                    name : 'jenis', 
                    title : 'Jenis', 
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
            propinsi : {
                propinsi : null,
                propinsi_id : null,
            },
            headerTitle : '',
            headerDetail : '',
            searchUrl : `/cities?propinsi=${this.propinsiId}`
         }
    },

    created() {
        this.CLR_ERRORS();
        this.getPropinsi();
    },

    methods : {
        ...mapActions('kabupaten',['deleteKabupaten','dataKabupaten']),
        ...mapActions('propinsi',['dataPropinsi']),
        ...mapMutations(['CLR_ERRORS']),

        getPropinsi() {
            this.CLR_ERRORS();
            this.dataPropinsi(this.propinsiId).then((response) => {
                if (response.success == true) {
                    this.propinsi = response.data;
                    this.headerTitle = `KABUPATEN (KOTA) - ${this.propinsi.propinsi}`;
                    this.headerDetail = `daftar kabupaten kota dari propinsi ${this.propinsi.propinsi}`;
                }
                else { alert (response.message) }
            });
        },

        /**tampikan daftar kode RL */
        listKecamatan(data){        
            this.CLR_ERRORS();
            this.$router.push(`/master/wilayah/kecamatan/${data.kota_id}`);
        },

        addData(){
            this.CLR_ERRORS();
            this.$refs.formKabupaten.newKabupaten(this.propinsi);
        },

        refreshTable() {
            this.$refs.tableKabupaten.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formKabupaten.editKabupaten(this.propinsi,data.kota_id);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan kabupaten / kota ${data.kota}. Lanjutkan ?`)){
                this.deleteKabupaten(data.kota_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableKabupaten.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>