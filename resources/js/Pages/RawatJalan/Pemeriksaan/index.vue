<template>
    <div class="uk-container uk-container-large" style="padding:1em;"> 
        <router-view></router-view>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import FormPemeriksaan from '@/Pages/RawatJalan/Pemeriksaan/FormPemeriksaan';
import ListPasienPoli from '@/Pages/RawatJalan/Pemeriksaan/ListPasienPoli';
import ListPemeriksaan from '@/Pages/RawatJalan/Pemeriksaan/ListPemeriksaan';

export default {
    components : {
        FormPemeriksaan,
        ListPasienPoli,
        ListPemeriksaan,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransactionLists','poliFilterTable']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {
                isExpandable : false,
                filterType : 'ADVANCED', 
                qtyPerPage : 2,
            },
            rowFunctions : [
                { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                { 'title':'Hapus', 'icon':'ban','callback':this.deleteData },
            ],

            tbColumns : [
                { 
                    name : 'reg_id', 
                    title : 'Registrasi', 
                    colType : 'linkmenus',
                },   
                { 
                    name : 'nama_pasien', 
                    title : 'PASIEN', 
                    colType : 'linkmenus', 
                },
                { 
                    name : 'dokter_nama', 
                    title : 'Dokter Unit', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'penjamin_nama', 
                    title : 'Penjamin', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'no_antrian', 
                    title : 'Antrian', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'status', 
                    title : 'STATUS', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }                
            ], 
            buttons : [
                //{ title : 'Data Baru', icon:'plus-circle', 'callback' : this.addData },
            ],
            searchUrl : '/outpatient/transactions',
            regPoliLists : null,
        }
    },
    mounted() {
    },

    methods : {
        ...mapActions('pendaftaran',['createRegistrasi','dataRegistrasi','updateRegistrasi','deleteRegistrasi']),
        ...mapActions('kelompokTagihan',['deleteKelompokTagihan']),

        ...mapActions('rawatJalan',['listTransaksiPoli']),
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',[
            'CLR_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION_LISTS',
            'CLR_POLI_TRANSACTION_LISTS',
            'SET_FILTER_TABLE_POLI'
        ]),

        formClosed(){
            UIkit.switcher('#switcherPasienPoli').show(0);
        },

        // updateListData(data){
        //     this.regPoliLists = null;
        //     if(data){ this.regPoliLists = data.data; }
        // },

        editPelayananPoli(data) {
            if(data) {
                if(data.status_reg == 'DAFTAR') { 
                    this.$refs.modalRegPoli.showModal(data);
                } 
                else { 
                    UIkit.switcher('#switcherPasienPoli').show(1); 
                    this.$refs.listPemeriksaan.retrieveData(data);
                    ////this.$refs.formPemeriksaanPoli.retrieveData(data);
                }
            }
        },

        /**tampikan modal untuk membuat vendor baru */
        addData(){
            this.CLR_ERRORS();
            this.$refs.formPendaftaran.newPendaftaran();
        },

        refreshTable() {
            this.$refs.tableRawatJalan.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            this.$refs.formPendaftaran.editRegistrasi(data);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus registrasi ${data.nama_pasien}. Lanjutkan ?`)){
                this.deleteRegistrasi(data.reg_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableRawatJalan.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>
<style>

ul.hims-poli-tab li {
    margin:0;
    padding:0;
}

ul.hims-poli-tab li h5{
    padding:0 1em 0 1em;
    margin:0;
}

ul.hims-poli-tab li div a {
    padding:0;
    margin:0;
    color:#000; 
    display:block; 
    padding:0.5em;
    text-decoration: none;
    font-weight: 500;
}

ul.hims-poli-tab li div {
    margin:0; 
    background-color: #eee; 
    color:#000;
    padding:0;
}

ul.hims-poli-tab li.uk-active div {
    margin:0; 
    background-color: #fff; 
    color:#000;
    padding:0;
}

ul.hims-poli-tab li div a h5 {
    color:#666;
    font-weight: 500;
}

ul.hims-poli-tab li.uk-active div a h5 {
    color:#000;
    font-weight: 500;
}
</style>