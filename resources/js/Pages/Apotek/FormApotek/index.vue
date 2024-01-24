<template>
    <div class="uk-container uk-container-large" style="margin-top:1em; padding:1em 1em 2em 1em;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="formClosed" style="background-color:white;padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:1"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">PEMBELIAN APOTEK</h4>
            </div>
        </div>

        <div  style=" background-color:#fff; margin-top:2em; padding:2em 1em 1em 1em;">
            <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                <div uk-spinner="ratio : 2"></div>
                <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
            </div>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-1">
                    <h4 style="font-weight:500;padding:0;margin:0;">Data Transaksi</h4>
                    <p style="font-weight:400;padding:0;margin:0;font-size:12px;">{{ transaksi.trx_id }}</p>
                </div>
                <!-- <div class="uk-width-1-3@m" style="border-right:1px solid #ccc;">
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
                </div> -->
                <div class="uk-width-1-3@m">
                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien</label>
                    <div class="uk-form-controls">
                        <input type="text" name="keyword" v-model="transaksi.nama_pasien" class="uk-input uk-form-small" style="color:black;">
                    </div>
                </div>
                <div class="uk-width-1-3@m">
                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Dokter (Peresep)</label>
                    <div class="uk-form-controls">
                        <input type="text" name="keyword" v-model="transaksi.dokter_nama" class="uk-input uk-form-small" style="color:black;">
                    </div>
                </div>
                
                <div class="uk-width-2-3@m">
                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Depo Obat</label>
                    <select class="uk-select uk-form-small" v-model="selectedDepo" @change="depoSelected" :disabled="isUpdate">
                        <option value = null>pilih depo resep</option>
                        <option v-for="dt in depoFarmasiList" :value="dt.depo_id">{{ dt.depo_id }} - {{ dt.depo_nama }}</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:1em;">
                <div class="uk-width-1-1">
                    <h4 style="font-weight:500;">Item Resep</h4>
                </div>
            </div>

            <div v-if="selectedResepId !== null" style="margin-top:1em;">
                <div style="padding:0;">
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                        <thead>
                            <th class="tb-header-reg" style="text-align:center;color:white;background-color: #cc0202;">Tipe</th>
                            <th colspan="2" class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Item Resep</th>
                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">Jumlah</th>
                            <th class="tb-header-reg" style="width:80px;text-align:center;color:white;background-color: #cc0202;">Satuan</th>
                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Cara Pakai</th>
                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Harga</th>
                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Subtotal</th>                                                
                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">...</th>
                        </thead>
                        <!-- <thead>
                            <th class="tb-header-reg" style="text-align:center;color:white;background-color: #cc0202;">Tipe</th>
                            <th colspan="2" class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Item Resep</th>
                            <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Cara Pakai</th>
                            <th class="tb-header-reg" style="width:80px;text-align:center;color:white;background-color: #cc0202;">Satuan</th>
                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Harga</th>
                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">Jml.Resep</th>
                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">Jml.Ambil</th>
                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Subtotal</th>                                                
                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">...</th>
                        </thead> -->
                        <tbody>
                            <row-input-apotek ref="rowInputApotek"
                                :groupRacikanLists = "groupLists"
                                :resepItems = "transaksi.items"
                                :fnAddCallback = "addItemResep">
                            </row-input-apotek>                        
                            <row-form-apotek
                                v-if="transaksi.items" 
                                v-for="dt in transaksi.items"
                                :rowData = "dt"
                                :dataChange = "calculateTotal"
                                :activationChange = "changeActivationActItem"
                                :signaCallback = "ubahSigna">
                            </row-form-apotek>
                            <tr>
                                <td colspan="7" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL</td>
                                <td style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(transaksi.total)}}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="padding:1em;text-align:right;">
                    <button type="button" class="uk-button" @click.prevent="formClosed" style="margin-right:1em;border:none;">Batal</button>
                    <button type="button" class="uk-button" @click.prevent="simpanResep" style="border:none; background-color: #cc0202;color:white;margin-right:1em;">Simpan</button>
                    <!-- <button v-if="isUpdate" type="button" class="uk-button" @click.prevent="simpanResep" style="border:none; background-color: #cc0202;color:white;">Simpan dan Kunci</button> -->
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
import RowFormApotek from '@/Pages/Apotek/FormApotek/RowFormApotek.vue';
import RowInputApotek from '@/Pages/Apotek/FormApotek/RowInputApotek.vue';
import ModalResep from '@/Pages/RawatJalan/components/ModalResep';
import ModalSigna from '@/Pages/RawatJalan/components/ModalSigna';

// import FormRealisasiResep from '@/Pages/Farmasi/components/FormRealisasiResep.vue';
// import FormResep from '@/Pages/Farmasi/components/FormResep.vue';
// import SubFarmasiForm from '@/Pages/Farmasi/FarmasiForm/SubFarmasiForm/index.vue';

export default {
    name : 'form-apotek',
    props : {
        trxId : { type:String, required:true },
    },

    components : {
        headerPage,
        BaseTable,
        RowFormApotek,
        RowInputApotek,
        ModalResep,
        ModalSigna,
        // FormRealisasiResep,
        // FormResep,
        // SubFarmasiForm,
    },
    computed : {
        ...mapGetters(['errors']),     
        ...mapGetters('farmasi',['depoFarmasiList']),   
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
            searchUrl : '/pharmacy',

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
                peresep : null,
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
        ...mapMutations('farmasi',['SET_PRESCRIPTION_ITEMS']),
        ...mapActions('farmasi',['listDepoFarmasi']),
        ...mapActions('apotek',['createApotek','updateApotek','dataApotek','realisasiApotek']),

        initialize(){
            this.transaksi.items = [];
            this.getDepoList();
            if(this.trxId.toUpperCase() == 'BARU') {
                this.clearTransaksiData();
                this.isLoading = false;
                this.isUpdate = false;
            }
            else {
                this.getResepData(this.trxId);
            }
        },

        getDepoList(){
            this.listDepoFarmasi().then((response) => {
                if (response.success == true) {
                    let dp = response.data;
                    // if(dp.length > 0 ){
                    //     this.selectedDepo = dp[0].depo_id;
                    // }
                }
            });
        },

        getResepData(id){
            this.dataApotek(id).then((response) => {
                if (response.success == true) {
                    let dt = response.data;
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
            this.transaksi.trx_id = dt.trx_id;
            this.transaksi.reg_id = dt.reg_id;
            this.transaksi.reff_trx_id = dt.trx_id;
            
            this.transaksi.dokter_id = dt.dokter_id;
            this.transaksi.dokter_nama = dt.dokter_nama;
            
            this.transaksi.pasien_id = dt.pasien_id;
            this.transaksi.nama_pasien = dt.nama_pasien; //.toUpperCase();
            this.transaksi.no_rm = dt.no_rm;
            this.transaksi.usia = dt.usia;
            this.transaksi.tanggal_lahir = dt.tgl_lahir;
            this.transaksi.tempat_lahir = dt.tempat_lahir;
            
            this.transaksi.unit_id = dt.unit_id;
            this.transaksi.unit_nama = dt.unit_nama;
            this.transaksi.depo_id = dt.depo_id;
            this.selectedDepo = dt.depo_id;
            this.transaksi.depo_nama = dt.depo_nama;

            this.transaksi.total = dt.total;
            this.transaksi.status = dt.status;
            this.transaksi.is_aktif = dt.is_aktif;
            this.transaksi.items = dt.items;
            if(this.trxId.toUpperCase() == 'BARU') {
                this.transaksi.items = [];
            }
            this.$refs.rowInputApotek.updateDepo(this.selectedDepo);
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
            this.$router.push({ name:'apotekIndex' });
        },

        /**tampikan modal untuk membuat vendor baru */
        addResep(){        
            this.CLR_ERRORS();
            this.$refs.formResep.resepBaru(this.poliTransaction);
        },

        updateData(data){
            this.CLR_ERRORS();
            this.$refs.formRealisasiResep.approveResep(data.trx_id);
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
            this.$refs.rowInputApotek.updateDepo(this.selectedDepo);
            if(this.isUpdate == false) {
                this.transaksi.items = [];
            }
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
            this.signaSelectedRow.signa = data.signa;
            this.signaSelectedRow.signa_deskripsi = data.deskripsi;        
            
            if(this.signaSelectedRow.item_tipe == 'HEADER_RACIKAN') {
                this.prescriptionItems.forEach(item => {
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
            this.transaksi.items = JSON.parse(JSON.stringify(items));
            this.calculateTotal();
        },

        addItemResep(data) {
            this.calculateTotal();
        },

        simpanResep(){
            this.transaksi.depo_id =  this.selectedDepo;
            this.calculateTotal();
            
            if(this.isUpdate) {
                if(confirm(`Proses ini akan menyimpan dan mengubah data. Lanjutkan ?`)){
                    this.ubahResep();
                };
            }

            else {
                if(confirm(`Proses ini akan menyimpan data resep sebagai DRAFT resep. Lanjutkan ?`)){
                    this.buatResep();
                };
            }

        },
        
        buatResep(){
            this.isLoading = true;
            this.createApotek(this.transaksi).then((response) => {
                if (response.success == true) {
                    this.isUpdate = true;
                    this.isLoading = false;
                    let dt = response.data;
                    this.fillDataTransaksi(dt);
                    alert('data resep berhasil disimpan.');
                    this.$router.replace({ params:{trxId:dt.trx_id}});
                    this.verifikasiResep();
                }
                else { 
                    this.isLoading = false;    
                    alert (response.message);
                }
            });
        },

        ubahResep(){
            this.isLoading = true;
            this.updateApotek(this.transaksi).then((response) => {
                if (response.success == true) {
                        this.verifikasiResep();
                }
                else { 
                    this.isLoading = false;    
                    alert (response.message);
                }
            });
        },

        verifikasiResep() {

            if(confirm(`Resep berhasil disimpan. Resep perlu direalisasikan dan dikunci. Realisasikan ?`)){
                this.realisasiApotek(this.transaksi.trx_id).then((response) => {
                    if (response.success == true) {
                        alert('data transaksi farmasi berhasil disimpan.');
                        this.isLoading = false;
                        this.$router.push({ name:'apotekIndex'});
                    }
                    else { 
                        this.isLoading = false;    
                        alert (response.message);
                    }
                });
            }
            else {
                this.$router.push({ name:'apotekIndex'});
            }
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