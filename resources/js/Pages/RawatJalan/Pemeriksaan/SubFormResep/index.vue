<template>
    <div style="padding:0 0 1em 0;margin:0;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="closeEditForm" style="padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:1"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">RESEP</h4>
            </div>
        </div>
        <div style="background-color:white;margin-top:2em;">
            <div class="uk-grid-small hims-form-subpage" uk-grid style="border:none;padding:1em 0.5em 0.5em 0.5em;background-color:#fff;">
                <div class="uk-width-1-1@m">
                    <p style="font-size:12px;padding:0;margin:0;">ID.{{ poliTransaction.pasien_id }}</p>
                    <h5 style="color:black;padding:0;margin:0;font-weight: 500;">{{ poliTransaction.nama_pasien }} ({{ poliTransaction.no_rm }})</h5>                                        
                </div>
                <div class="uk-width-1-3@m">
                    <dl class="uk-description-list hims-description-list">
                        <dt>Dokter</dt>
                        <dd>{{poliTransaction.dokter_nama}}</dd>
                    </dl>                                        
                </div>
                <div class="uk-width-1-3@m">                                
                    <dl class="uk-description-list hims-description-list">
                        <dt>Unit</dt>
                        <dd>{{poliTransaction.unit_nama}}</dd>
                    </dl>
                </div>
                <div class="uk-width-1-3@m">                                        
                    <dl class="uk-description-list hims-description-list">
                        <dt>No.Resep</dt>
                        <dd>{{prescription.trx_id}} <strong>{{ prescription.status }}</strong></dd>
                    </dl>
                </div>                
            </div> 
            <div style="background-color: #fff;padding:0 1em 1em 1em;">
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
                <div style="padding:1em;text-align:right;">
                    <button type="button" class="uk-button" @click.prevent="closeEditForm" style="margin-right:1em;border:none;">Batal</button>
                    <button type="button" class="uk-button" @click.prevent="simpanResep" style="border:none; background-color: #cc0202;color:white;">Simpan</button>
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
import RowFormResepInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListResepInap/RowFormResepInap.vue';
import RowListResepInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListResepInap/RowListResepInap.vue';
import RowInputResepInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListResepInap/RowInputResepInap.vue';

import ModalResep from '@/Pages/RawatJalan/components/ModalResep';
import ModalSigna from '@/Pages/RawatJalan/components/ModalSigna';

export default {
    name : 'sub-form-resep',
    props : { 
        trxId : { type:String, required:true },
        resepId : { type:String, required:true },
    },   

    components : {
        RowFormResepInap,
        RowInputResepInap,
        RowListResepInap,
        ModalResep,
        ModalSigna
    },

    data() {
        return {
            urlPicker : '',
            tindakan :[],
            isUpdateResep : true,
            signaSelectedRow : null,
            groupLists : [],
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliActs','poliItemUsages']),
        ...mapGetters('pemeriksaan',['examinationItems','tindakanLists','bhpLists','examination']),
        ...mapGetters('resep',['prescriptionItems','prescription']),
    },
    watch:{
        'poliTransaction' : function(oldVal,newVal) {
        }
    },
    mounted(){
        this.initialize();
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',['CLR_POLI_TRANSACTION','SET_POLI_TRANSACTION','SET_ACTS_POLI','SET_ITEM_USAGE']),   
        
        ...mapMutations('pemeriksaan',['CLR_EXAMINATION','NEW_EXAMINATION','SET_EXAMINATION','SET_EXAMINATION_ITEMS']),
        ...mapActions('rawatJalan',['dataTransaksiPoli','updateTransaksiPoli','createTransaksiPoli']),
        
        ...mapMutations('resep',['CLR_PRESCRIPTION','NEW_PRESCRIPTION','SET_PRESCRIPTION','SET_PRESCRIPTION_ITEMS']),
        ...mapActions('resep',['createPrescription','updatePrescription','dataPrescription']),
        

        initialize(){
            //this.clearTindakan();
            this.retrieveData();
        },

        closeEditForm(){
            this.$router.push({ name: 'dataPasienPoli', params: { trxId: this.trxId } });
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

        calculateTotal(){
            let total = 0;
            this.prescriptionItems.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.prescription.total = total; 
            })
        },
        
        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        retrieveData() {            
            this.CLR_ERRORS();
            if(this.trxId) {
                this.isLoading = true;
                this.dataTransaksiPoli(this.trxId).then((response) => {
                    if (response.success == true) {  
                        this.isLoading = false; 
                        this.initData();
                    }
                    else { 
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
        },

        addResep(){

        },
        initData(){
            if(this.resepId.toUpperCase() == 'BARU') { this.addResepBaru(); }
            else { this.editResep(); }
        },

        addResepBaru() { 
            this.isUpdateResep = false;
            let dt = JSON.parse(JSON.stringify(this.poliTransaction));
            dt.jns_transaksi = 'RESEP';
            this.NEW_PRESCRIPTION(dt);
        },

        editResep(){
            this.isUpdateResep = true;
            this.CLR_ERRORS();
            this.dataPrescription(this.resepId).then((response) => {
                if (response.success == true) { 
                    this.isUpdateResep = true; 
                    this.calculateTotal();
                }
                else { alert (response.message) }
            });
        },

        changeActivationActItem(){
            let items = this.prescriptionItems.filter(
                item => { return item.detail_id !== null || item.is_aktif == true }
            );
            this.SET_PRESCRIPTION_ITEMS(JSON.parse(JSON.stringify(items)));
            this.calculateTotal();
        },

        calculateTotal(){
            let total = 0;
            this.prescriptionItems.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.prescription.total = total;
            })
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

        simpanResep(){
            if(this.prescriptionItems.length <= 0) {
                alert('item resep tidak boleh kosong.');
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
                                this.retrieveDataTransaksiPoli(response.data);
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
                                this.retrieveDataTransaksiPoli(response.data);
                                this.closeEditForm();
                            }
                            else { alert (response.message) }
                        });
                    };
                }
            }
            
        },

        retrieveDataTransaksiPoli(){
            this.dataTransaksiPoli(this.poliTransaction.trx_id);
        },
    }
}
</script>

