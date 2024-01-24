<template>
    <div>
        <header-page title="PASIEN POLI & IGD" subTitle="daftar pasien poli dan IGD"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <store-table ref="tableRawatJalan"
                :columns = "tbColumns" 
                :config = "configTable"
                :storeData = "poliTransactionLists"
                :searchCallback = "listTransaksiPoli"
                :paramFilter = "poliFilterTable">
                <template v-slot:tbl-body>
                    <tbody style="min-weight:50vh;">
                        <row-pelayanan-poli  v-if="poliTransactionLists"
                            v-for="dt in poliTransactionLists.data" 
                            :rowData="dt"
                            :linkCallback="editData">
                        </row-pelayanan-poli>
                    </tbody>
                </template>
                <template v-slot:form-filter>                    
                    <filter-pasien-poli></filter-pasien-poli>
                </template>
            </store-table>
        </div>
        <div>
            <modal-registrasi-poli ref="modalRegistrasiPoli" modalId="modalConfirmPeriksaPoli" v-on:confirmPoliSucceed="confirmSucceedCallback"></modal-registrasi-poli>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import StoreTable from '@/Components/StoreTable';
import FilterPasienPoli from '@/Pages/RawatJalan/Pemeriksaan/ListPasienPoli/FilterPasienPoli.vue';
import RowPelayananPoli from '@/Pages/RawatJalan/Pemeriksaan/ListPasienPoli/RowPelayananPoli.vue';
import ModalRegistrasiPoli from '@/Pages/RawatJalan/Pemeriksaan/ModalRegistrasiPoli';

export default {
    name : 'list-pasien-poli',
    props : {
        fnEditCallback : { type:Function, required:false },
    },
    components : {
        headerPage,
        StoreTable,
        RowPelayananPoli,
        FilterPasienPoli,
        ModalRegistrasiPoli,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransactionLists','poliFilterTable']),
    },
    data() {
        return {
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
                { name : 'reg_id', title : 'Registrasi', colType : 'linkmenus', },   
                { name : 'no_antrian', title : 'Antrian', colType : 'text', isSearchable : true, },
                { name : 'nama_pasien', title : 'PASIEN', colType : 'linkmenus', },
                { name : 'dokter_nama', title : 'Dokter Unit', colType : 'text', isSearchable : true, },
                { name : 'penjamin_nama', title : 'Penjamin', colType : 'text', isSearchable : true, },
                { name : 'status', title : 'STATUS', colType : 'text', isSearchable : true, },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }                
            ], 
        }
    },
    methods : {
        ...mapActions('pendaftaran',['createRegistrasi','dataRegistrasi','updateRegistrasi','deleteRegistrasi']),
        ...mapActions('rawatJalan',['listTransaksiPoli']),
        ...mapMutations(['CLR_ERRORS']),

        editData(data) {
            if(data) { 
                if(this.fnEditCallback) { 
                    this.fnEditCallback(data); 
                }
                else {
                    // if(data.status.toUpperCase() == 'ANTRI') {
                    //     alert('Ubah status menjadi PERIKSA untuk melanjutkan proses input data pemeriksaan');
                    // }
                    // else {
                        this.editPelayananPoli(data);
                    // }
                } 
            }
        },

        editPelayananPoli(data) {
            // console.log(data);
            if(data) {
                if(data.status.toUpperCase() == 'DAFTAR' || data.status.toUpperCase() == 'ANTRI') { 
                    this.$refs.modalRegistrasiPoli.editData(data);
                } 
                else { 
                    this.$router.push({ name: 'dataPasienPoli', params: { trxId: data.trx_id } });
                }
            }
        },

        confirmSucceedCallback(data){
            this.$refs.tableRawatJalan.retrieveData(); 
            if(this.fnEditCallback){
                this.fnEditCallback(data);
            }
        },
    }
}
</script>