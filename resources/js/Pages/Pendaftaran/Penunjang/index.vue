<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">        
        <ul id="switcherRegistrasi" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List Registrasi</a></li>
            <li><a href="#">Form Registrasi</a></li>
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <!-- <list-registrasi ref="listRegistrasi" 
                    :addFunction="addRegistrasi" 
                    :editFunction="updateDistribusi"
                    :approveFunction="approveDistribusi"
                ></list-registrasi> -->
                <div>
                    <header-page title="PENDAFTARAN PENUNJANG" subTitle="daftar pasien pelayanan penunjang"></header-page>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:1em;" class="uk-responsive">
                        <base-table ref="tablePenunjang"
                            :columns = "tbColumns" 
                            :config = "configTable" 
                            :buttons = "buttons"
                            :urlSearch="searchUrl" v-on:updateListDataTable="updateListData">
                            <template v-slot:tbl-body>
                                <tbody v-if="dataLists">
                                    <row-registrasi v-for="dt in dataLists" :rowData="dt" :rowFunctions = rowFunctions></row-registrasi>
                                </tbody>
                            </template>
                        </base-table>
                    </div>
                </div>
            </li>
            <li style="padding:0;margin:0;">
                <div style="padding:0;min-height:70vh;">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-auto">
                            <a href="#" @click.prevent="formRegistrasiClosed" style="background-color:white;padding:0.3em;display:inline-block;">
                                <span uk-icon="icon:chevron-left;ratio:1"></span>
                            </a>
                        </div>
                        <div class="uk-width-expand">
                            <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">REGISTRASI PENUNJANG</h4>
                        </div>
                    </div>
                    <div style="background-color:#fff;margin-top:1em;">
                        <form-registrasi-penunjang ref="formRegistrasiPenunjang" v-on:registrasiInapClosed="formRegistrasiClosed"></form-registrasi-penunjang>
                    </div>
                </div>
                        
                
            </li>
        </ul>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import RowRegistrasi from '@/Pages/Pendaftaran/Penunjang/components/RowRegistrasi.vue';
import ListRegistrasi from '@/Pages/Pendaftaran/Penunjang/components/ListRegistrasi.vue';
import FormRegistrasi from '@/Pages/Pendaftaran/Penunjang/components/FormRegistrasi.vue';
import FormRegistrasiPenunjang from '@/Pages/Pendaftaran/Penunjang/components/FormRegistrasiPenunjang.vue';

export default {
    components : {
        headerPage,
        ListRegistrasi,
        FormRegistrasi,
        FormRegistrasiPenunjang,
        BaseTable,
        RowRegistrasi,
    },
    data() {
        return {
            isExpanded : true,
            configTable : { isExpandable : true, filterType : 'REGULAR', },
            rowFunctions: [
                { 'title':'Ganti Dokter', 'icon':'ban','callback':this.updateDistribusi },   
                { 'title':'Konfirmasi', 'icon':'ban','callback':this.approveDistribusi },   
                { 'title':'Lihat Data', 'icon':'file','callback':this.previewData },   
                { 'title':'Hapus Data', 'icon':'ban','callback':this.deleteData },
            ],
            tbColumns : [
                { name : 'reg_id', title : 'ID', colType : 'text', width : '120px', },
                { name : 'tgl_periksa', title : 'JADWAL PERIKSA', colType : 'text', },
                { name : 'no_antrian', title : 'No', colType : 'text',},
                { name : 'pasien', title : 'Pasien', width : '180px', colType : 'text', },
                { name : 'unit_nama', title : 'Dokter Unit', width : '200px', colType : 'text', },
                { name : 'tgl_registrasi', title : 'Tgl.Daftar', colType : 'text', },
                { name : 'status_reg', title : 'Status', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
                
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addRegistrasi },
            ],
            searchUrl : '/registrations',
            dataLists : null,
         }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        updateListData(data) {
            if(data) {
                if(data.data) { this.dataLists = data.data; }
                else { this.dataLists = null; }
            }
            else { this.dataLists = null; }
        },
        
        addRegistrasi(){
            this.CLR_ERRORS();
            this.$refs.formRegistrasiPenunjang.newData(); 
            UIkit.switcher('#switcherRegistrasi').show(1);
        },

        updateDistribusi(data){
            if(data.status == 'PERMINTAAN') {
                this.CLR_ERRORS();
                this.$refs.formRegistrasi.editDistribusi(data); 
                UIkit.switcher('#switcherRegistrasi').show(1);       
            }
            else {
                alert('Data dengan status bukan PERMINTAAN sudah tidak dapat diubah.');
            }
        },

        redirectMainTab(){
            this.$refs.listRegistrasi.refreshTable();
            UIkit.switcher('#switcherRegistrasi').show(0);
        },

        pasienListShow(data){
            UIkit.switcher('#switcherRegistrasi').show(1);
        },

        registrationFormShow(data){
            this.$refs.formRegistrasi.refreshPasien(data.pasien_id);
            UIkit.switcher('#switcherRegistrasi').show(1);
        },
        
        formClosed(){
            this.$refs.listRegistrasi.refreshTable();
            UIkit.switcher('#switcherRegistrasi').show(0);
        },
        formRegistrasiClosed(){
            this.$refs.tablePenunjang.refreshTable();
            UIkit.switcher('#switcherRegistrasi').show(0);
        },

        approveDistribusi(data) {
            this.$refs.approveDistribusi.refreshData(data);
            UIkit.switcher('#switcherRegistrasi').show(2);
        }
    }

}
</script>
<style>

ul.hims-penunjang-tab li {
    margin:0;
    padding:0;
}

ul.hims-penunjang-tab li h5{
    padding:0 1em 0 1em;
    margin:0;
}

ul.hims-penunjang-tab li div a {
    padding:0;
    margin:0;
    color:#000; 
    display:block; 
    padding:0.5em;
    text-decoration: none;
    font-weight: 500;
}

ul.hims-penunjang-tab li div {
    margin:0; 
    background-color: #eee; 
    color:#000;
    padding:0;
}

ul.hims-penunjang-tab li.uk-active div {
    margin:0; 
    background-color: #fff; 
    color:#000;
    padding:0;
}

ul.hims-penunjang-tab li div a h5 {
    color:#666;
    font-weight: 500;
}

ul.hims-penunjang-tab li.uk-active div a h5 {
    color:#000;
    font-weight: 500;
}
</style>