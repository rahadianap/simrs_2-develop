<template>
    <div style="padding:0;margin:0;"> 
        <template v-if="!showEditForm">
            <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">
                <div class="uk-width-auto@m">
                    <button @click.prevent="addResepBaru">Tambah Resep</button>
                </div>                                        
            </div>
            <div style="padding:0 0.5em 0.2em 0.5em;">
                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-reg" style="width:20px;text-align:center;color:white;background-color: #cc0202;"></th>
                        <th class="tb-header-reg" style="width:200px;text-align:left;color:white;background-color: #cc0202;">ID</th>
                        <th class="tb-header-reg" style="width:120px;text-align:left;color:white;background-color: #cc0202;">Tanggal</th>
                        <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Dokter</th>
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Total</th>                                                
                        <th class="tb-header-reg" style="width:40px;text-align:center;color:white;background-color: #cc0202;">Status</th>
                        <th class="tb-header-reg" style="width:120px;text-align:center;color:white;background-color: #cc0202;">...</th>
                    </thead>
                    <tbody>
                        <row-list-resep-inap
                            v-if="inpatientResepLists" 
                            v-for="dt in inpatientResepLists"
                            :rowData = "dt"
                            :linkCallback = "editResep">
                        </row-list-resep-inap>
                    </tbody>
                </table>
            </div>
        </template>  
        <template v-else>
            <div style="padding:0 0.5em 0.2em 0.5em;">
                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-reg" style="text-align:center;color:white;background-color: #cc0202;">Tipe</th>
                        <th colspan="2" class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Item Resep</th>
                        <th class="tb-header-reg" style="width:40px;text-align:center;color:white;background-color: #cc0202;">Jumlah</th>
                        <th class="tb-header-reg" style="width:80px;text-align:center;color:white;background-color: #cc0202;">Satuan</th>
                        <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Cara Pakai</th>
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Harga</th>
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;background-color: #cc0202;">Subtotal</th>                                                
                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;background-color: #cc0202;">...</th>
                    </thead>
                    <tbody>
                        <row-input-resep-inap 
                            :groupRacikanLists = "groupLists"
                            :resepItems = "prescriptionItems"
                            :fnAddCallback = "calculateTotal">
                        </row-input-resep-inap>                        
                        <row-form-resep-inap
                            v-if="prescriptionItems" 
                            v-for="dt in prescriptionItems"
                            :rowData = "dt"
                            :dataChange = "calculateTotal"
                            :activationChange = "changeActivationActItem"
                            :signaCallback = "ubahSigna">
                        </row-form-resep-inap>
                        <tr>
                            <td colspan="7" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL</td>
                            <td style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(prescription.total)}}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="padding:1em;text-align:right;">
                <button type="button" class="uk-button" @click.prevent="closeEditForm" style="margin-right:1em;border:none;">Batal</button>
                <button type="button" class="uk-button" @click.prevent="simpanResep" style="border:none; background-color: #cc0202;color:white;">Simpan</button>
            </div>
        </template>
        
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
import RowFormResepInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListResepInap/RowFormResepInap.vue';
import RowListResepInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListResepInap/RowListResepInap.vue';
import RowInputResepInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListResepInap/RowInputResepInap.vue';


import ModalResep from '@/Pages/RawatJalan/components/ModalResep';
import ModalSigna from '@/Pages/RawatJalan/components/ModalSigna';

export default {
    name : 'sub-form-resep',    
    components : {
        RowFormResepInap,
        RowInputResepInap,
        RowListResepInap,
        ModalResep,
        ModalSigna,
    },
    data() {
        return {
            showEditForm : false,
            urlPicker : '',
            tindakan :[],
            isUpdateResep : true,
            signaSelectedRow : null,
            groupLists : [],
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatInap',['inpatientTransaction','inpatientItemPrescripts','inpatientResepLists']),
        ...mapGetters('resep',['prescriptionItems','prescription']),
    },
    watch: {
        'inpatientTransaction.unit_id' : function(oldVal,newVal) {
        }
    },
    mounted(){
    },
    methods : {          
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatInap',['CLR_INPATIENT_TRANSACTION','SET_INPATIENT_TRANSACTION','SET_ITEM_PRESCRIPTS']),        
        ...mapMutations('resep',['CLR_PRESCRIPTION','NEW_PRESCRIPTION','SET_PRESCRIPTION','SET_PRESCRIPTION_ITEMS']),
        ...mapActions('resep',['createPrescription','updatePrescription','dataPrescription']),
        ...mapActions('rawatInap',['dataTransaksiInap']),
        
        setValue(data) {
            this.tindakan = data;
        },   
        
        searchObat(){
            this.urlPicker = `/products/medical/items`;
            this.$refs.resepModalPicker.showModal(this.urlPicker);
        },

        addResep(dataVal){
            this.CLR_ERRORS();
            if(dataVal) {
                let trxData = this.prescription;
                let data = JSON.parse(JSON.stringify(dataVal));
                this.prescriptionItems.push({
                    detail_id : null,
                    reg_id : null,
                    trx_id : null,
                    sub_trx_id : null,
                    item_id : data.produk_id,
                    item_nama : data.produk_nama,
                    jumlah : 1,
                    satuan : data.satuan,
                    klasifikasi :  data.jenis_produk,
                    depo_id : null,
                    depo_nama : null,
                    kelas_harga_id : '-',
                    buku_harga_id : '-',
                    nilai : data.hna,
                    diskon : 0,
                    diskon_persen : 0,
                    harga_diskon : 0,
                    subtotal : data.hna,
                    dokter_nama : trxData.dokter_nama,
                    dokter_id : trxData.dokter_id,
                    dokter_pengirim_id : null,
                    is_racikan : false,
                    is_aktif : true,
                    signa : null,
                    signa_deskripsi : null,
                    komponen : [],
                });
                this.calculateTotal();                
            }
        },

        calculateTotal(){
            let total = 0;
            this.prescriptionItems.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.prescription.total = total; 
            })
        },

        changeActivationActItem(){
            let items = this.prescriptionItems.filter(
                item => { return item.detail_id !== null || item.is_aktif == true }
            );
            this.SET_PRESCRIPTION_ITEMS(JSON.parse(JSON.stringify(items)));
            this.calculateTotal();
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        editResep(data){
            this.isUpdateResep = true;
            this.showEditForm = true;
            this.CLR_ERRORS();
            this.dataPrescription(data.trx_id).then((response) => {
                if (response.success == true) { 
                    this.isUpdateResep = true; 
                    this.calculateTotal();
                }
                else { alert (response.message) }
            });
        },

        addResepBaru() { 
            this.isUpdateResep = false;
            let dt = JSON.parse(JSON.stringify(this.inpatientTransaction));
            dt.jns_transaksi = 'RESEP';
            this.NEW_PRESCRIPTION(dt);
            this.showEditForm = true;
        },

        closeEditForm(){
            this.isUpdateResep = true;
            this.CLR_PRESCRIPTION();
            this.showEditForm = false;
        },

        simpanResep(){
            console.log(this.prescriptionItems);
            if(this.prescriptionItems.length <=0 ) {
                alert('item resep tidak boleh kosong');
                return false;
            }

            else {
                this.CLR_ERRORS();
                if(this.isUpdateResep) {
                    if(confirm(`Proses ini akan mengubah data resep. Lanjutkan ?`)){
                        this.updatePrescription(this.prescription).then((response) => {
                            if (response.success == true) {
                                this.isUpdateResep = true;
                                alert(response.message);
                                this.retrieveDataTransaksiInap(response.data);
                                this.closeEditForm();
                            }
                            else { alert (response.message) }
                        });
                    };
                }
                else {
                    if(confirm(`Proses ini akan membuat data resep baru. Lanjutkan ?`)){
                        this.createPrescription(this.prescription).then((response) => {
                            if (response.success == true) {
                                this.isUpdateResep = true;
                                alert(response.message);
                                this.retrieveDataTransaksiInap(response.data);
                                this.closeEditForm();
                            }
                            else { alert (response.message) }
                        });
                    };
                }
            }
            
        },

        retrieveDataTransaksiInap(){
            this.dataTransaksiInap(this.inpatientTransaction.trx_id);
        },

        ubahSigna(data) {
            this.signaSelectedRow = data;
            this.$refs.signaModalPicker.showModal();
        },

        changeItemSigna(data) {
            this.signaSelectedRow.signa = data.signa;
            this.signaSelectedRow.signa_deskripsi = data.deskripsi;            
        }

    }
}
</script>