<template>
    <div class="uk-container uk-container-large" style="padding:0;">
        <div>
            <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                <li>
                    <a href="#" @click.prevent="closeTab" class="uk-text-uppercase">                        
                        <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">
                            <span uk-icon="icon:arrow-left;ratio:1.5"></span>
                            REGISTRASI PENDAFTARAN
                        </h4>
                    </a>                    
                </li>
                <li><span style="font-weight:400;color:#333;font-size:16px;"  class="uk-text-uppercase">Pasien</span></li>           
            </ul>
        </div>
        <div style="margin-top:0.5em;">
            <p style="font-size:12px; font-style: italic; font-weight: 300;margin:0;padding:0;">Pilih pasien yang akan di-registrasi untuk pelayanan</p>
        </div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin:0;">
            <base-table ref="tablePasien"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <modal-form-pasien ref="modalPasien" v-on:modalPasienClosed="refreshPasien"></modal-form-pasien>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import ModalFormPasien from '@/Pages/Pendaftaran/Penunjang/components/ModalFormPasien.vue';
export default {
    name : 'list-pasien',
    components : {
        headerPage,
        BaseTable,
        ModalFormPasien,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',['isRefExist']),
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
                    name : 'pasien_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Daftarkan', 'icon':'file-edit','callback':this.registrasiData },
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                    ],
                },   
                { 
                    name : 'no_rm', 
                    title : 'RM', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Daftarkan', 'icon':'file-edit','callback':this.registrasiData },
                        { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.deleteData },
                    ],
                },   
                { 
                    name : 'nama_lengkap', 
                    title : 'Nama Pasien', 
                    colType : 'text',
                },   
                { 
                    name : 'tempat_lahir', 
                    title : 'Tempat Lahir', 
                    colType : 'text',
                },   
                { 
                    name : 'tgl_lahir', 
                    title : 'Tgl. Lahir', 
                    colType : 'text',
                },   
                { 
                    name : 'jns_kelamin', 
                    title : 'JK', 
                    colType : 'text',
                },   
                { 
                    name : 'jns_penjamin', 
                    title : 'Jenis Penjamin', 
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
                { title : 'Pasien Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/patients'
         }
    },
    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('pasien',['deletePasien']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
        },

        closeTab(){
            this.$emit('redirectBack',true);
        },

        /**tampikan modal untuk membuat vendor baru */
        addData(){        
            this.CLR_ERRORS();
            //this.$router.push(`/master/pasien/baru`);
            this.$refs.modalPasien.createNewPasien();
        },

        refreshTable() {
            this.$refs.tablePasien.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.modalPasien.editDataPasien(data.pasien_id);
            //this.$router.push(`/master/pasien/${data.pasien_id}`);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan data pasien ${data.no_rm} - ${data.nama_lengkap}. Lanjutkan ?`)){
                this.deletePasien(data.pasien_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePasien.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        refreshPasien(data){
            console.log(data);
        },

        registrasiData(data){
            this.$emit('pasienSelected',data);
        }
    }
}
</script>