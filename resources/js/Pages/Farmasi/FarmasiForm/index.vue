<template>
    <div class="uk-container uk-container-large" style="margin-top:0.5em; padding:1em 1em 2em 1em;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="formClosed" style="background-color:white;padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:1"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">RESEP OBAT</h4>
            </div>
        </div>

        <div  style=" background-color:#fff; margin-top:2em; padding:2em 1em 1em 1em;">
            <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                <div uk-spinner="ratio : 2"></div>
                <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
            </div>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-1">
                    <h4 style="font-weight:500;">Data Transaksi</h4>
                </div>
                <div class="uk-width-1-3@m" style="border-right:1px solid #ccc;">
                    <dl class="uk-description-list resep-description-list">
                        <dt>No. Registrasi</dt>
                        <dd>{{transaksi.trx_id}}</dd>
                        <dd>{{transaksi.dokter_nama}}</dd>
                    </dl>
                </div>
                <div class="uk-width-1-3@m" style="border-right:1px solid #ccc;">
                    <dl class="uk-description-list resep-description-list">
                        <dt>Pasien</dt>
                        <dd>{{transaksi.nama_pasien}}</dd>
                        <dd>{{transaksi.no_rm}} - {{transaksi.pasien_id}}</dd>
                    </dl>
                </div>
                <div class="uk-width-1-3@m" style="border-right:0px solid #ccc;">
                    <dl class="uk-description-list resep-description-list">
                        <dt>Status</dt>
                        <dd>{{transaksi.status}}</dd>
                    </dl>
                </div>
                
            </div>

            <div style="margin-top:2em;">
                <div class="uk-width-1-1">
                    <h4 style="font-weight:500;">Item Resep</h4>
                </div>
                <div class="uk-width-1-1" style="margin:0;padding:0;">
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-2@m">
                            <select class="uk-select uk-form-small" v-model="selectedDepo" @change="depoSelected">
                                <option v-for="dt in depoFarmasiList" :value="dt.depo_id">{{ dt.depo_id }} - {{ dt.depo_nama }}</option>
                                <option>pilih depo resep</option>
                            </select>
                        </div>
                    </div>
                </div>            
            </div>

            <div v-if="selectedResepId !== null" style="margin-top:1em;">
                <div style="padding:0;">
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                        <thead>
                            <th class="tb-header-reg" style="text-align:center;color:white;background-color: #cc0202;">Tipe</th>
                            <th colspan="2" class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Item Resep</th>
                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Cara Pakai</th>
                            <th class="tb-header-reg" style="width:80px;text-align:center;color:white;background-color: #cc0202;">Satuan</th>
                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Harga</th>
                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">Jml.Resep</th>
                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">Jml.Ambil</th>
                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Subtotal</th>                                                
                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">...</th>
                        </thead>
                        <tbody>
                            <row-input-farmasi 
                                :groupRacikanLists = "groupLists"
                                :resepItems = "transaksi.items"
                                :fnAddCallback = "addItemResep">
                            </row-input-farmasi>                        
                            <row-form-farmasi
                                v-if="transaksi.items" 
                                v-for="dt in transaksi.items"
                                :rowData = "dt"
                                :dataChange = "calculateTotal"
                                :activationChange = "changeActivationActItem"
                                :signaCallback = "ubahSigna">
                            </row-form-farmasi>
                            <tr>
                                <td colspan="8" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL</td>
                                <td style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(transaksi.total)}}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="padding:1em;text-align:right;">
                    <button type="button" class="uk-button" @click.prevent="closeEditForm" style="margin-right:1em;border:none;">Batal</button>
                    <button type="button" class="uk-button" @click.prevent="simpanResep" style="border:none; background-color: #cc0202;color:white;margin-right:1em;">Simpan</button>
                    <!-- <button type="button" class="uk-button" @click.prevent="simpanKunciResep" style="border:none; background-color: #cc0202;color:white;">Simpan dan Kunci</button> -->
                </div>
            </div>
        </div>
        <modal-resep ref="resepModalPicker"
            modalId = "resepModalPicker"
            :fnSelected = "addResep">
        </modal-resep>
        <modal-signa ref="signaModalPicker"
            modalId = "signaModalPicker"
            :fnSelected = "changeItemSigna">
        </modal-signa>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormRealisasiResep from '@/Pages/Farmasi/components/FormRealisasiResep.vue';
import FormResep from '@/Pages/Farmasi/components/FormResep.vue';

import SubFarmasiForm from '@/Pages/Farmasi/FarmasiForm/SubFarmasiForm/index.vue';
import RowFormFarmasi from '@/Pages/Farmasi/FarmasiForm/SubFarmasiForm/RowFormFarmasi.vue';
import RowInputFarmasi from '@/Pages/Farmasi/FarmasiForm/SubFarmasiForm/RowInputFarmasi.vue';

import ModalResep from '@/Pages/RawatJalan/components/ModalResep';
import ModalSigna from '@/Pages/RawatJalan/components/ModalSigna';

export default {
    name : 'farmasi-pasien',
    props : {
        trxId : { type:String, required:true },
        trxType : { type:String, required: true },
    },

    components : {
        headerPage,
        BaseTable,
        FormRealisasiResep,
        FormResep,
        SubFarmasiForm,
        RowFormFarmasi,
        RowInputFarmasi,
        ModalResep,
        ModalSigna,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransactionLists','poliFilterTable','poliTransaction']),
        ...mapGetters('rawatInap',['poliTransactionLists','poliFilterTable','poliTransaction']),
        ...mapGetters('farmasi',['patientPrescriptionItems','patientPrescriptionLists','depoFarmasiList','selectedPrescription','selectedPrescriptionItems']),
        
    },
    data() {
        return {
            isLoading : true,
            isExpanded : false,
            configTable : {                
                isExpandable : true,
                filterType : 'SIMPLE', 
            },
            tbColumns : [                
                { 
                    name : 'trx_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Realisasi Resep', 'icon':'file-edit','callback':this.updateData },   
                        { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },
                    ],
                },
                { 
                    name : 'tgl_transaksi', 
                    title : 'Tanggal', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'jns_resep', 
                    title : 'Jenis', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'status', 
                    title : 'Status', 
                    colType : 'text', 
                    isSearchable : true,
                }
                
            ],
            tbChildColumns :[
                { 
                    name : 'item_id', 
                    title : 'ID', 
                    colType : 'text', 
                    textAlign : 'left',
                },   
                { 
                    name : 'item_nama', 
                    title : 'Resep', 
                    colType : 'text', 
                    textAlign : 'left',
                },   
                { 
                    name : 'satuan', 
                    title : 'Satuan', 
                    textAlign : 'center',
                    colType : 'text',
                },
                { 
                    name : 'jml_resep', 
                    title : 'Jumlah', 
                    colType : 'text',
                    textAlign : 'center',                    
                },
            ], 
            buttons : [
                { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addResep },
            ],
            searchUrl : '/prescriptions',

            transaksi : {
                reg_id : null,
                trx_id : null,
                reff_trx_id : null,

                pasien_id : null,
                nama_pasien : null,
                no_rm : null,
                usia : null,
                
                unit_nama : null,
                unit_id : null,
                dokter_id : null,
                dokter_nama : null,
                
                tanggal_lahir : null,
                tempat_lahir : null,
                penjamin_id : null,
                penjamin_nama : null,
                depo_id : null,
                depo_nama : null,
                total : 0,
                status : null,
                is_aktif : true,
                jns_transaksi : 'RESEP',
                resep_lists : [],
            },

            groupLists : [],
            selectedResepId : 'baru',
            selectedDepo : null,
            isUpdate : false,

         }
    },
    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('signa',['deleteSigna']),
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',[
            'CLR_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION_LISTS',
            'CLR_POLI_TRANSACTION_LISTS',
            'SET_FILTER_TABLE_POLI'
        ]),

        ...mapMutations('farmasi',['SET_PRESCRIPTION_ITEMS']),

        ...mapActions('rawatJalan',['dataTransaksiPoli']),
        ...mapActions('rawatInap',['dataAdmisiInap']),
        ...mapActions('farmasi',['createFarmasi','updatePrescription','dataFarmasi','realisasiFarmasi','updateFarmasi','dataPrescription','listDepoFarmasi','listPatientPrescription']),

        initialize(){
            this.transaksi.items = [];
            this.getDepoList();
            if(this.trxType.toUpperCase() == 'POLI') {
                this.transaksi.jns_transaksi = "RAWAT_JALAN";
                this.getOutpatientData();
            }
            else if(this.trxType.toUpperCase() == 'INAP'){
                this.transaksi.jns_transaksi = "RAWAT_INAP";
                this.getInpatientData();
            }
            else {
                this.selectedResepId = this.trxId;
                this.getResepData(this.trxId);
            }
        },

        getDepoList(){
            this.listDepoFarmasi().then((response) => {
                if (response.success == true) {
                    let dp = response.data;
                    if(dp.length > 0 ){
                        this.selectedDepo = dp[0].depo_id;
                    }
                }
            });
        },

        getInpatientData() {
            this.dataAdmisiInap(this.trxId).then((response) => {
                if (response.success == true) {
                    let dt = response.data;
                    this.transaksi.trx_id = null;
                    this.fillDataTransaksi(dt);
                    this.isLoading = false;
                }
                else { 
                    alert (response.message);
                    this.isLoading = false;
                }
            });
        },

        getOutpatientData() {
            this.dataTransaksiPoli(this.trxId).then((response) => {
                if (response.success == true) {
                    let dt = response.data;
                    this.fillDataTransaksi(dt);
                    this.transaksi.trx_id = null;
                    this.isLoading = false;
                }
                else { 
                    this.isLoading = false;
                    alert (response.message) 
                }
            });
        },

        getResepData(id){
            this.dataFarmasi(id).then((response) => {
                if (response.success == true) {
                    let dt = response.data;
                    this.transaksi.trx_id = dt.trx_id;
                    this.fillDataTransaksi(dt);
                    this.isLoading = false;
                    this.isUpdate = true;
                }
                else { 
                    alert (response.message);
                    this.isLoading = false;
                }
            });
        },

        fillDataTransaksi(dt){
            this.transaksi.reg_id = dt.reg_id;
            this.transaksi.reff_trx_id = this.trxId;
            
            this.transaksi.dokter_id = dt.dokter_id;
            this.transaksi.dokter_nama = dt.dokter_nama;
            
            this.transaksi.pasien_id = dt.pasien_id;
            this.transaksi.nama_pasien = dt.nama_pasien.toUpperCase();
            this.transaksi.no_rm = dt.no_rm;
            this.transaksi.usia = dt.usia;
            this.transaksi.tanggal_lahir = dt.tgl_lahir;
            this.transaksi.tempat_lahir = dt.tempat_lahir;
            
            this.transaksi.unit_id = dt.unit_id;
            this.transaksi.unit_nama = dt.unit_nama;
            this.transaksi.depo_id = dt.depo_id;
            this.transaksi.depo_nama = dt.depo_nama;

            this.transaksi.total = dt.total;
            this.transaksi.status = dt.status;
            this.transaksi.is_aktif = dt.is_aktif;
            this.transaksi.items = dt.items;
            if(this.selectedResepId.toUpperCase() == 'BARU') {
                this.transaksi.items = [];
            }
            this.selectedDepo = dt.depo_id;
        },

        getAllResep(){
            this.listPatientPrescription(this.trxId).then((response) => {
                if (response.success == true) {
                    console.log(response.data);
                    this.transaksi.resep_lists = response.data;
                    this.isLoading = false;
                }
                else { 
                    alert (response.message) 
                    this.isLoading = false;
                }
            });
        },

        clearTransaksiData(){
            this.transaksi.reff_trx_id = null;
            this.transaksi.reg_id = null;
            this.transaksi.trx_id = null;

            this.transaksi.dokter_id = null;
            this.transaksi.dokter_nama = null;
            this.transaksi.pasien_id = null;
            this.transaksi.nama_pasien = null;
            this.transaksi.no_rm = null;
            this.transaksi.unit_id = null;
            this.transaksi.unit_nama = null;
            this.transaksi.usia = null;
            this.transaksi.tanggal_lahir = null;
            this.transaksi.tempat_lahir = null;

            this.transaksi.penjamin_id = null;
            this.transaksi.penjamin_nama = null;
        },

        formClosed(){
            this.clearTransaksiData();
            this.$router.push({ name:'resepIndex' });
        },

        /**tampikan modal untuk membuat vendor baru */
        addResep(){        
            this.CLR_ERRORS();
            this.$refs.formResep.resepBaru(this.poliTransaction);
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formRealisasiResep.approveResep(data.trx_id);
            // console.log(data);
            // UIkit.modal('#formRealisasiResep').show();
        },

        refreshTable() {
            this.$refs.tableFarmasi.retrieveData();
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus signa ${data.signa}. Lanjutkan ?`)){
                this.deleteSigna(data.signa_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableFarmasi.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        resepSelected(){
            if(this.selectedResepId.toUpperCase() == 'BARU') {
                this.isUpdate = false;
            }
            else {
                this.isLoading = true;
                this.dataFarmasi(this.selectedResepId).then((response) => {
                    if (response.success == true) {
                        // console.log(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;                        
                        this.calculateTotal();
                    }
                    else { 
                        this.isLoading = false;    
                        alert (response.message);
                    }
                });
            }
        },

        depoSelected(){
            this.transaksi.depo_id = this.selectedDepo;
        },

        calculateTotal(){
            let total = 0;
            this.transaksi.items.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.transaksi.total = total; 
            })
        },
        
        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        ubahSigna(data) {
            this.signaSelectedRow = data;
            this.$refs.signaModalPicker.showModal();
        },

        changeItemSigna(data) {
            // this.signaSelectedRow.signa = data.signa;
            // this.signaSelectedRow.signa_deskripsi = data.deskripsi;         
            this.signaSelectedRow.signa = data.signa;
            this.signaSelectedRow.signa_deskripsi = data.deskripsi;        
            
            if(this.signaSelectedRow.item_tipe == 'HEADER_RACIKAN') {
                this.transaksi.items.forEach(item => {
                    if(item.group_racikan == this.signaSelectedRow.item_nama) {
                        item.signa = data.signa;
                        item.signa_deskripsi = data.deskripsi;
                    }
                });
            }
            
            this.$refs.signaModalPicker.closeModal();       
        },

        changeActivationActItem(){
            let items = this.transaksi.items.filter(
                item => { return item.detail_id !== null || item.is_aktif == true }
            );
            // this.SET_PRESCRIPTION_ITEMS(JSON.parse(JSON.stringify(items)));
            this.transaksi.items = JSON.parse(JSON.stringify(items));
            this.calculateTotal();
        },

        addItemResep(data) {
            this.calculateTotal();
        },

        simpanResep(){
            if(this.transaksi.items.length <= 0) {
                alert('Item Resep tidak boleh kosong.');
                return false;
            }
            else {
                this.transaksi.depo_id =  this.selectedDepo;
                this.calculateTotal();
                
                if(this.isUpdate) {
                    if(confirm(`Proses ini akan menyimpan data dan mengurangi stock item persediaan. Lanjutkan ?`)){
                        this.ubahResep();
                    };
                }

                else {
                    if(confirm(`Proses ini akan menyimpan data resep sebagai DRAFT resep. Lanjutkan ?`)){
                        this.buatResep();
                    };
                }
            }
        },

        buatResep(){
            this.isLoading = true;
            this.createFarmasi(this.transaksi).then((response) => {
                if (response.success == true) {
                    this.isUpdate = true;
                    this.isLoading = false;
                    let dt = response.data;
                    this.getResepData(dt.trx_id);
                    if(confirm(`Resep berhasil disimpan. Resep perlu direalisasikan dan dikunci. Realisasikan ?`)){
                        this.verifikasiResep(dt.trx_id);
                    }
                    else {
                        this.$router.push({ name:'resepIndex' });
                    }
                }
                else { 
                    this.isLoading = false;    
                    alert (response.message);
                }
            });
        },

        ubahResep(){
            this.isLoading = true;
            this.updateFarmasi(this.transaksi).then((response) => {
                if (response.success == true) {
                    if(confirm(`Resep berhasil disimpan. Resep perlu direalisasikan dan dikunci. Realisasikan ?`)){
                        this.verifikasiResep(this.transaksi.trx_id);
                    }
                    else {
                        this.$router.push({ name:'resepIndex' });
                    }
                }
                else { 
                    this.isLoading = false;    
                    alert (response.message);
                }
            });
        },

        verifikasiResep(trxId) {
            this.realisasiFarmasi(trxId).then((response) => {
                if (response.success == true) {
                    alert('data transaksi farmasi berhasil disimpan.');
                    this.isLoading = false;
                    this.$router.push({ name:'resepIndex'});
                }
                else { 
                    this.isLoading = false;    
                    alert (response.message);
                }
            });
        }
    }
}
</script>
<style>
dl.resep-description-list dt {
    margin:0;
    padding:0;
    font-size:11px;
    font-weight: bold;
    color:#333;
}

dl.resep-description-list dd {
    margin:0;
    padding:0;
    font-size:12px;
    font-weight: 400;
    color:#000;
}
</style>