<template>
    <div style="padding:0;margin:0;">    
        <template v-if="!showEditBhpForm">
            <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">
                <div class="uk-width-expand@m">
                    <h5 style="color:indigo;padding:0;margin:0;">PEMAKAIAN BHP</h5>
                </div>
                <div class="uk-width-auto@m">
                    <button @click.prevent="addBhpBaru">Tambah Bhp</button>
                </div>                                        
            </div>
            <div style="padding:0 0.5em 0.2em 0.5em;">
                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-reg" style="width:20px;text-align:center;color:white;"></th>
                        <th class="tb-header-reg" style="width:200px;text-align:left;color:white;">ID</th>
                        <th class="tb-header-reg" style="width:120px;text-align:left;color:white;">Tanggal</th>
                        <th class="tb-header-reg" style="text-align:left;color:white;">Dokter</th>
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Total</th>                                                
                        <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Status</th>
                        <th class="tb-header-reg" style="width:120px;text-align:center;color:white;">...</th>
                    </thead>
                    <tbody>
                        <row-list-bhp 
                            v-if="poliBhpLists" 
                            v-for="dt in poliBhpLists"
                            :rowData = "dt"
                            :linkCallback = "editBhp">
                        </row-list-bhp>
                    </tbody>
                </table>
            </div>
        </template>
        
        <template v-else>
            <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">
                <div class="uk-width-expand@m">
                    <h5 style="color:indigo;padding:0;margin:0;">BAHAN HABIS PAKAI (BHP)</h5>
                </div>
                <div class="uk-width-expand@m">
                    <select v-model="depo.depo_id" class="uk-select uk-form-small">
                        <option v-for="dt in depoLists" :value="dt.depo_id">{{ dt.depo_nama }}</option>
                    </select>
                </div>
                <div class="uk-width-auto@m">
                    <button @click.prevent="searchBhp">Pilih Item</button>
                </div>                                        
            </div>
            <div style="padding:0 0.5em 0.2em 0.5em;">
                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-reg" style="text-align:left;color:white;">Depo</th>
                        <th class="tb-header-reg" style="text-align:left;color:white;">Item BHP</th>
                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">Satuan</th>
                        <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Jumlah</th>
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Harga</th>
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>                                                
                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                    </thead>
                    <tbody>
                        <row-form-bhp 
                            v-if="consumableItems" 
                            v-for="dt in consumableItems"
                            :rowData = "dt"
                            :dataChange = "calculateTotal"
                            :activationChange = "changeActivationActItem">
                        </row-form-bhp>
                        <tr>
                            <td colspan="5" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL</td>
                            <td style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(consumables.total)}}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>            
            <div style="padding:1em;text-align:right;">
                <button type="button" class="uk-button" @click.prevent="closeEditForm" style="margin-right:1em;border:none;">Batal</button>
                <button type="button" class="uk-button" @click.prevent="simpanBhp" style="border:none; background-color: #cc0202;color:white;">Simpan</button>
            </div>
        </template>
        <modal-bhp ref="bhpModalPicker"
            modalId = "bhpModalPicker"
            :fnSelected = "addBhp">
        </modal-bhp>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowFormBhp from '@/Pages/RawatJalan/Pemeriksaan/SubFormBhp/RowFormBhp.vue';
import ModalBhp from '@/Pages/RawatJalan/components/ModalBhp';
import RowListBhp from '@/Pages/RawatJalan/Pemeriksaan/SubFormBhp/RowListBhp.vue';
export default {
    name : 'sub-form-bhp',    
    components : {
        RowFormBhp,
        ModalBhp,
        RowListBhp,
    },
    data() {
        return {
            showEditBhpForm : false,
            isUpdateBhp : true,
            urlPicker : '',
            urlSearch : null,
            tindakan :[],
            depo: { depo_id: null,  depo_nama: null, },
            depoLists : [],
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliItemPrescripts','poliBhpLists']),
        ...mapGetters('bhp',['consumableItems','consumables']),
        //...mapGetters('rawatJalan',['poliTransaction','poliActs','poliItemUsages']),
    },
    watch: {
        //'poliTransaction.unit_id' : function(oldVal,newVal) {}
    },
    mounted(){
        this.initialize();
    },
    methods : {          
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',['CLR_POLI_TRANSACTION','SET_POLI_TRANSACTION','SET_ITEM_USAGES']),
        ...mapActions('unitPelayanan',['dataUnitPelayanan']),

        ...mapMutations('bhp',['CLR_CONSUMABLES','NEW_CONSUMABLES','SET_CONSUMABLES','SET_CONSUMABLES_ITEMS']),
        ...mapActions('bhp',['createConsumables','updateConsumables','dataConsumables']),
        ...mapActions('rawatJalan',['dataTransaksiPoli']),
        
        initialize(){
            this.dataUnitPelayanan(this.poliTransaction.unit_id).then((response) => {
                if (response.success == true) { 
                    this.depoLists = JSON.parse(JSON.stringify(response.data.depo)); 
                }
            });
        },

        setValue(data) {
            this.tindakan = data;
        },
        
        addBhpBaru() {
            this.isUpdateBhp = false;
            let dt = JSON.parse(JSON.stringify(this.poliTransaction));
            dt.jns_transaksi = 'BHP';
            this.NEW_CONSUMABLES(dt);
            this.showEditBhpForm = true;
        },
        
        searchBhp(){
            this.urlPicker = `/depos/${this.depo.depo_id}/products`;
            this.$refs.bhpModalPicker.showModal(this.urlPicker);
        },

        addBhp(dataVal){
            this.CLR_ERRORS();
            if(dataVal) {
                let trxData = this.poliTransaction;
                let data = JSON.parse(JSON.stringify(dataVal));
                this.consumableItems.push({                        
                        detail_id : null,
                        reg_id : null,
                        trx_id : null,
                        sub_trx_id : null,
                        item_id : data.produk_id,
                        item_nama : data.produk_nama,
                        jumlah : 1,
                        satuan : data.satuan,
                        klasifikasi :  data.jenis_produk,
                        unit_id : trxData.unit_id,
                        unit_nama : trxData.unit_nama,
                        depo_id : data.depo_id,
                        depo_nama : data.depo_nama,
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
            // this.poliTransaction.bhp.detail.forEach(data => {
            //     if(data.is_aktif) {
            //         total = (total*1) + (data.subtotal*1);
            //         this.poliTransaction.bhp.total = total; 
            //     }
            // })
            this.consumableItems.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.consumables.total = total; 
            })
        },

        changeActivationActItem(){
            // let items = this.poliItemUsages.filter(
            //     item => { return item.detail_id !== null || item.is_aktif == true }
            // );
            // this.SET_ITEM_USAGES(items);
            // this.calculateTotal();
            let items = this.consumableItems.filter(
                item => { return item.detail_id !== null || item.is_aktif == true }
            );
            this.SET_CONSUMABLES_ITEMS(JSON.parse(JSON.stringify(items)));
            this.calculateTotal();
        },

        simpanBhp(){
            this.CLR_ERRORS();
            if(this.isUpdateBhp) {
                if(confirm(`Proses ini akan mengubah data pemakaian BHP. Lanjutkan ?`)){
                    this.updateConsumables(this.consumables).then((response) => {
                        if (response.success == true) {
                            this.isUpdateBhp = true;
                            alert(response.message);
                            this.retrieveDataTransaksiPoli(response.data);
                            this.closeEditForm();
                        }
                        else { alert (response.message) }
                    });
                };
            }
            else {
                if(confirm(`Proses ini akan membuat data baru pemakaian BHP. Lanjutkan ?`)){
                    this.createConsumables(this.consumables).then((response) => {
                        if (response.success == true) {
                            this.isUpdateBhp = true;
                            alert(response.message);
                            this.retrieveDataTransaksiPoli(response.data);
                            this.closeEditForm();
                        }
                        else { alert (response.message) }
                    });
                };
            }
        },

        retrieveDataTransaksiPoli(){
            this.dataTransaksiPoli(this.poliTransaction.trx_id);
        },

        editBhp(data){
            this.isUpdateBhp = true;
            this.showEditBhpForm = true;
            this.CLR_ERRORS();
            this.dataConsumables(data.trx_id).then((response) => {
                if (response.success == true) { this.isUpdateBhp = true; }
                else { alert (response.message) }
            });
        },

        closeEditForm(){
            this.isUpdateBhp = true;
            this.CLR_CONSUMABLES();
            this.showEditBhpForm = false;
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
    }
}
</script>