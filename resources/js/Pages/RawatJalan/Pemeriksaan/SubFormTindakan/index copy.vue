<template>
    <div style="padding:0 0 1em 0;margin:0;">   
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="formClosed" style="padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:1"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">PEMERIKSAAN DAN BHP</h4>
            </div>
        </div>

        <div class="hims-form-container" style="padding:2em 1em 0 1em; margin:1em 0 0 0;background-color:#fff;"> 
            <div class="uk-grid-small hims-form-subpage" uk-grid style="border:none;padding:0;background-color:#fff;">
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
                        <dt>No.Registrasi</dt>
                        <dd>{{poliTransaction.trx_id}}</dd>
                    </dl>
                </div>
                <!-- <div class="uk-width-1-1">
                    <button type="button" @click.prevent="RefreshPemeriksaan">Refresh Pemeriksaan</button>
                </div>                         -->
            </div> 
        
            <!-- <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">
                <div class="uk-width-expand@m">
                    <h5 style="color:indigo;padding:0;margin:0;">PEMERIKSAAN DAN BHP</h5>
                </div>
            </div> -->
            <div style="padding:0 0.5em 0.2em 0.5em;">
                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-reg" style="width:20px;text-align:center;color:white;"></th>
                        <th class="tb-header-reg" style="text-align:left;color:white;">Tindakan</th>
                        <th class="tb-header-reg" style="text-align:left;color:white;">Dokter</th>
                        <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Jumlah</th>
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Harga</th>
                        <th class="tb-header-reg" style="width:60px;text-align:right;color:white;">Diskon(Rp)</th>
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>                                                
                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                    </thead>
                    <tbody>                    
                        <row-input-tindakan ref="rowInputTindakan"
                            :rowData = "tindakan"                             
                            :urlTindakan="urlTindakanPicker" 
                            :urlDokter="urlDokterPicker"                             
                            :actSelected="tindakanSelected"
                            :docSelected="dokterSelected"
                            :addFunction="addPemeriksaan">
                        </row-input-tindakan>

                        <row-form-tindakan 
                            v-if="poliActs && poliActs.length > 0" 
                            v-for="dt in poliActs"
                            :rowData = "dt"
                            :dataChange = "calculateTotal"
                            :activationChange = "changeActivationActItem">
                        </row-form-tindakan>
                        <tr>
                            <td colspan="6" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL</td>
                            <td v-if="poliTransaction.tindakan" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(poliTransaction.tindakan.total)}}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- <div style="padding:0 0.5em 0.2em 0;">
            <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">
                <div class="uk-width-expand@m">
                    <h5 style="color:indigo;padding:0;margin:0;">BAHAN HABIS PAKAI (BHP)</h5>
                </div>
            </div>
            <div style="padding:0 0 0.2em 0.5em;">
                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-reg" style="text-align:left;color:white;">Depo</th>
                        <th class="tb-header-reg" style="text-align:left;color:white;">Item BHP</th>
                        <th class="tb-header-reg" style="text-align:left;color:white;">Tindakan</th>
                        <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Jumlah</th>
                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">Satuan</th>
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Harga</th>
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>  
                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Bhp Tind.</th>  
                        <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:0.4em;">
                                <select v-model="bhp.depo_id" class="uk-select uk-form-small" @change="depoSelected">
                                    <option v-for="dt in depoLists" :value="dt.depo_id">{{ dt.depo_nama }}</option>
                                </select>
                            </td>
                            <td style="padding:0.4em;">
                                <search-select
                                    :config = configSelect
                                    :searchUrl = urlItemDepoPicker
                                    placeholder = "pilih item bhp"
                                    captionField = "produk_nama"
                                    valueField = "produk_nama"
                                    :itemListData = produkDesc
                                    :value = "bhp.item_nama"
                                    v-on:item-select = "bhpSelected"
                                ></search-select>
                            </td>
                            <td  style="padding:0.4em;">
                                <select v-model="bhp.tindakan_id" class="uk-select uk-form-small" @change="tindakanBhpSelected">
                                    <option></option>
                                    <option v-for="act in poliActs" :value="act.item_id">{{ act.item_nama }}</option>
                                </select>
                            </td>
                            <td style="padding:0.4em;">
                                <input v-model="bhp.jumlah" type="number" class="uk-input uk-form-small" style="text-align:center;" @change="jmlBhpChange">
                            </td>
                            <td style="padding:1em;text-align:center;font-size:12px;color:black;">{{ bhp.satuan }}</td>
                            <td style="padding:1em;text-align:right;font-size:12px;color:black;">{{formatCurrency(bhp.nilai)}}</td>
                            <td style="padding:1em;text-align:right;font-size:12px;color:black;">{{formatCurrency(bhp.subtotal)}}</td>
                            <td style="padding:1em;text-align:center;font-size:12px;color:black;">
                                <input class="uk-checkbox" type="checkbox" v-model="bhp.bhp_tindakan" style="text-align: center;">
                            </td>
                            <td style="padding:1em;text-align:center;font-size:12px;color:black;">
                                <button @click.prevent="addBhpManual">Tambah</button>
                            </td>
                        </tr>
                        <row-form-bhp 
                            v-if="poliItemUsages" 
                            v-for="dt in poliItemUsages"
                            :rowData = "dt"
                            :dataChange = "calculateTotal"
                            :depoLists = "depoLists"
                            :activationChange = "changeActivationBhpItem">
                        </row-form-bhp>
                    </tbody>
                </table>
            </div> 
        </div> -->

        <!-- <modal-bhp ref="bhpModalPicker"
            modalId = "bhpModalPicker"
            :fnSelected = "addBhp">
        </modal-bhp>

        <modal-tindakan ref="actModalPicker"
            modalId = "actModalPicker"
            :fnSelected = "addPemeriksaan">
        </modal-tindakan> -->
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowFormTindakan from '@/Pages/RawatJalan/Pemeriksaan/SubFormTindakan/RowFormTindakan.vue';
import RowInputTindakan from '@/Pages/RawatJalan/Pemeriksaan/SubFormTindakan/RowInputTindakan.vue';
import RowFormBhp from '@/Pages/RawatJalan/Pemeriksaan/SubFormBhp/RowFormBhp.vue';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'sub-form-tindakan',
    props : { 
        trxId : { type:String, required:true },
        actId : { type:String, required:true },
     },    
    components : {
        RowFormTindakan,
        RowInputTindakan,
        RowFormBhp,
        SearchSelect,
    },
    data() {
        return {
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m uk-text-uppercase",
                compStyle : "padding:0;margin:0;",
                retrieveAll : false,
            },
            produkDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],
            tindakanDesc : [
                { colName : 'tindakan_nama', labelData : '', isTitle : true },
                { colName : 'tindakan_id', labelData : '', isTitle : false },
            ],
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],

            urlItemDepoPicker : '',
            urlTindakanPicker : '',
            urlDokterPicker : '',
            urlSearch : null,
            // tindakan :[],
            depoLists : [],
            defaultDepo : null,
            depo: { depo_id: null,  depo_nama: null, },
            bhp : { 
                detail_id : null,
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                item_id : null,
                item_nama : null,
                jumlah : 1,
                satuan : null,
                klasifikasi :  null,
                unit_id : null,
                unit_nama : null,
                depo_id : null,
                depo_nama : null,
                tindakan_id : null,
                tindakan_nama : null,
                bhp_tindakan : false,
                kelas_harga_id : '-',
                buku_harga_id : '-',
                nilai : 0,
                diskon : 0,
                diskon_persen : 0,
                harga_diskon : 0,
                subtotal : 0,
                dokter_nama : null,
                dokter_id : null,
                dokter_pengirim_id : null,
                is_racikan : false,
                is_aktif : true,
                signa : null,
                signa_deskripsi : null,
                komponen : [],
            },
            
            tindakan : { 
                detail_id : null,
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                item_id : null,
                item_nama : null,
                jumlah : 1,
                satuan : null,
                klasifikasi :  null,
                unit_id : null,
                unit_nama : null,
                depo_id : null,
                depo_nama : null,
                tindakan_id : null,
                tindakan_nama : null,
                bhp_tindakan : false,
                kelas_harga_id : '-',
                buku_harga_id : '-',
                nilai : 0,
                diskon : 0,
                diskon_persen : 0,
                harga_diskon : 0,
                subtotal : 0,
                dokter_nama : null,
                dokter_id : null,
                dokter_pengirim_id : null,
                is_racikan : false,
                is_aktif : true,
                signa : null,
                signa_deskripsi : null,
                komponen : [],
                bhp : [],
            },

            dokter : {
                dokter_id : null, 
                dokter_nama : null, 
            },
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliActs','poliItemUsages']),
    },
    watch:{
        'poliTransaction' : function(oldVal,newVal) {
            console.log(newVal);
            this.dokter.dokter_id = newVal.dokter_id;
            this.dokter.dokter_nama = newVal.dokter_nama;
        }
    },
    mounted(){
        this.retrieveData();
    },
    methods : {          
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',['CLR_POLI_TRANSACTION','SET_POLI_TRANSACTION','SET_ACTS_POLI','SET_ITEM_USAGE']),   
        ...mapActions('rawatJalan',['dataTransaksiPoli']),     
        ...mapActions('unitPelayanan',['dataUnitPelayanan']),

        retrieveData() {
            alert('disini');
                
            this.CLR_ERRORS();
            this.isUpdate = true;
            this.isLoading = true;
            if(this.trxId) {
                this.dataTransaksiPoli(this.trxId).then((response) => {
                    if (response.success == true) {          
                        console.log(response.data);
                        this.SET_POLI_TRANSACTION(response.data);
                        this.isLoading = false;                        
                        this.initialize();
                        console.log(this.poliActs);
                    }
                    else { 
                        this.CLR_POLI_TRANSACTION();
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
        },

        initialize(){
            this.dataUnitPelayanan(this.poliTransaction.unit_id).then((response) => {
                if (response.success == true) { 
                    this.depoLists = JSON.parse(JSON.stringify(response.data.depo)); 
                    let depo = this.depoLists.find(data => data.is_default == true);
                    if(depo) { console.log(depo); this.defaultDepo = JSON.parse(JSON.stringify(depo)); }
                    else { this.defaultDepo = JSON.parse(JSON.stringify(this.depoLists[0])); }
                    this.bhp.depo_id = this.defaultDepo.depo_id;
                    this.bhp.depo_nama = this.defaultDepo.depo_nama;                    
                }
            });
            this.urlTindakanPicker = `/units/${this.poliTransaction.unit_id}/acts`;
            this.urlDokterPicker = `/units/${this.poliTransaction.unit_id}/doctors`;
            if(this.actId.toUpperCase() == 'BARU') {
                let pemeriksaan = {
                    total : 0,
                    detail : [],
                }
                this.SET_ACTS_POLI = [];
            }
        },

        // clearDokter() {
        //     console.log(this.poliTransaction);
        //     this.dokter.dokter_id = this.poliTransaction.dokter_id;
        //     this.dokter.dokter_nama = this.poliTransaction.dokter_nama;
        // },

        setValue(data) {
            this.tindakan = data;
        },   
        
        searchTindakan(){
            console.log(this.poliTransaction);
            if(this.poliTransaction.unit_id == null || 
                this.poliTransaction.unit_id == '' || 
                this.poliTransaction.kelas_harga_id == null ||
                this.poliTransaction.kelas_harga_id == '') 
            {
                alert('Pilih unit pelayanan dan kelas terlebih dahulu.');
            }

            else {
                this.urlPicker = `units/${this.poliTransaction.unit_id}/acts`;
                this.$refs.actModalPicker.showModal(this.urlPicker);
            }
        },

        tindakanSelected(dataSelected){
            // console.log(dataSelected);

            if(dataSelected) {
                let data = JSON.parse(JSON.stringify(dataSelected));
                let trxData = this.poliTransaction;
                let hargaRes = data.harga.find(item => item.buku_harga_id == trxData.buku_harga_id && item.kelas_id == trxData.kelas_harga_id);

                console.log('harga res:');
                console.log(hargaRes);

                if(!hargaRes) {
                    alert('harga pemeriksaan tidak ditemukan. Item tidak dapat ditambahkan.'); 
                    return false;
                }
                else {
                    if(!hargaRes.komponen) {
                        alert('komponen harga pemeriksaan tidak ditemukan. Item tidak dapat ditambahkan.'); 
                        return false;
                    }
                }

                let exist = false;
                if(this.poliActs) {
                    exist = this.poliActs.find(item => item.item_id == data.tindakan_id);
                    if(exist) {
                        alert('Tindakan / pemeriksaan sudah ada.') ; return false;
                    }
                    else {
                        this.insertTindakanToBuffer(data,trxData,hargaRes);
                    }
                }
                else {
                    //alert('poli acts not exist.') ; return false;
                    this.insertTindakanToBuffer(data,trxData,hargaRes);
                }
                // if(this.poliActs.length > 0) {
                //     exist = this.poliActs.find(item => item.item_id == data.tindakan_id);
                //     if(exist) {
                //         alert('Tindakan / pemeriksaan sudah ada.') ; return false;
                //     }
                //     else {
                //         console.log('harga res:');
                //         console.log(hargaRes);
                //         // let komp = [];
                //         // hargaRes.komponen.forEach(dt => {
                //         //     komp.push({
                //         //         client_id : null,
                //         //         komp_detail_id : null,
                //         //         detail_id : null,
                //         //         reg_id : null,
                //         //         trx_id : null,
                //         //         sub_trx_id : null,
                //         //         jumlah : 1,
                //         //         harga_id : dt.harga_id,
                //         //         komp_harga_id : dt.komp_harga_id,
                //         //         komp_harga : dt.komp_harga,
                //         //         nilai : dt.nilai,
                //         //         diskon : 0,
                //         //         nilai_diskon : 0,
                //         //         subtotal : dt.nilai,
                //         //         is_diskon : dt.is_diskon,
                //         //         is_ubah_manual : dt.is_ubah_manual,
                //         //         is_realisasi : false,
                //         //         is_bayar : false,
                //         //         is_aktif : dt.is_aktif,
                //         //     });
                //         // });

                //         this.tindakan.detail_id = null;
                //         this.tindakan.reg_id = null;
                //         this.tindakan.trx_id = null;
                //         this.tindakan.sub_trx_id = null;
                //         this.tindakan.item_id = data.tindakan_id;
                //         this.tindakan.item_nama = data.tindakan_nama;
                //         this.tindakan.jumlah = 1;
                //         this.tindakan.satuan = 'X';
                //         this.tindakan.kelas_harga_id = trxData.kelas_harga_id;
                //         this.tindakan.buku_harga_id = trxData.buku_harga_id;
                //         this.tindakan.nilai = hargaRes.nilai;
                //         this.tindakan.diskon = 0;
                //         this.tindakan.diskon_persen = 0;
                //         this.tindakan.harga_diskon = 0;
                //         this.tindakan.subtotal = hargaRes.nilai;
                //         this.tindakan.dokter_pengirim_id = trxData.dokter_pengirim_id;
                //         this.tindakan.is_aktif = true;
                //         this.tindakan.komponen = komp;
                //         this.tindakan.bhp = data.bhp;
                //     }
                //}
                
            }
            else {
                this.tindakan.item_nama = null;
                this.tindakan.item_id = null;
            }
            // console.log(this.tindakan);
        },

        insertTindakanToBuffer(data,trxData,hargaRes){
            console.log('harga res:');
            console.log(hargaRes);
            let komp = [];
            hargaRes.komponen.forEach(dt => {
                komp.push({
                    client_id : null,
                    komp_detail_id : null,
                    detail_id : null,
                    reg_id : null,
                    trx_id : null,
                    sub_trx_id : null,
                    jumlah : 1,
                    harga_id : dt.harga_id,
                    komp_harga_id : dt.komp_harga_id,
                    komp_harga : dt.komp_harga,
                    nilai : dt.nilai,
                    diskon : 0,
                    nilai_diskon : 0,
                    subtotal : dt.nilai,
                    is_diskon : dt.is_diskon,
                    is_ubah_manual : dt.is_ubah_manual,
                    is_realisasi : false,
                    is_bayar : false,
                    is_aktif : dt.is_aktif,
                });
            });

            this.tindakan.detail_id = null;
            this.tindakan.reg_id = null;
            this.tindakan.trx_id = null;
            this.tindakan.sub_trx_id = null;
            this.tindakan.item_id = data.tindakan_id;
            this.tindakan.item_nama = data.tindakan_nama;
            this.tindakan.jumlah = 1;
            this.tindakan.satuan = 'X';
            this.tindakan.kelas_harga_id = trxData.kelas_harga_id;
            this.tindakan.buku_harga_id = trxData.buku_harga_id;
            this.tindakan.nilai = hargaRes.nilai;
            this.tindakan.diskon = 0;
            this.tindakan.diskon_persen = 0;
            this.tindakan.harga_diskon = 0;
            this.tindakan.subtotal = hargaRes.nilai;
            this.tindakan.dokter_pengirim_id = trxData.dokter_pengirim_id;
            this.tindakan.is_aktif = true;
            this.tindakan.komponen = komp;
            this.tindakan.bhp = data.bhp;
        },

        dokterSelected(data) {
            if(data) {
                this.tindakan.dokter_id = data.dokter_id;
                this.tindakan.dokter_nama = data.dokter_nama;
            }
            else {
                this.tindakan.dokter_id = null;
                this.tindakan.dokter_nama = null;
            }
        },

        addPemeriksaan(dataVal){

            this.CLR_ERRORS();
            if(dataVal) {
                let data = JSON.parse(JSON.stringify(dataVal));
                /**kroscek data pemeriksaan */
                if(data.item_id == null ||  data.item_id == '' ||  data.item_nama == null ||  data.item_nama == '') { alert('tindakan tidak boleh kosong.'); return false; }
                if(data.dokter_id == null ||  data.dokter_id == '' ||  data.dokter_nama == null ||  data.dokter_nama == '') { alert('dokter tidak boleh kosong.'); return false; }
                
                let trxData = this.poliTransaction;
                let exist = false;
                // if(this.poliActs.length > 0) {  exist = this.poliActs.find(item => item.item_id == data.item_id); }
                if(this.poliActs){
                    if(this.poliActs.length > 0){
                        exist = this.poliActs.find(item => item.item_id == data.item_id);
                    }
                }
                else {

                }
                console.log(`is exist: ${exist}`);

                if(!exist) {
                    this.poliActs.push({
                        detail_id : null,
                        reg_id : null,
                        trx_id : null,
                        sub_trx_id : null,
                        item_id : data.item_id,
                        item_nama : data.item_nama,
                        jumlah : data.jumlah,
                        satuan : data.satuan,
                        kelas_harga_id : trxData.kelas_harga_id,
                        buku_harga_id : trxData.buku_harga_id,
                        nilai : data.nilai,
                        diskon : data.diskon,
                        diskon_persen : data.diskon_persen,
                        harga_diskon : data.harga_diskon,
                        subtotal : data.nilai,
                        dokter_nama : data.dokter_nama,
                        dokter_id : data.dokter_id,
                        dokter_pengirim_id : trxData.dokter_pengirim_id,
                        is_aktif : true,
                        komponen : data.komponen,
                    });
                    console.log(this.poliActs);
                    // data.bhp.forEach(dt => { 
                    //     dt['bhp_tindakan'] = true; 
                    //     dt['hna'] = 0;
                    //     dt['subtotal'] = 0;
                    //     console.log(dt);
                    //     this.addBhp(dt); 
                    // });                    
                    // this.calculateTotal();
                    // this.clearTindakan();
                
                }
                else { 
                    exist.is_aktif = true; 
                    this.calculateTotal();
                }
            }
        },

        clearTindakan(){
            this.tindakan.detail_id = null;
            this.tindakan.reg_id = null;
            this.tindakan.trx_id = null;
            this.tindakan.sub_trx_id = null;
            this.tindakan.item_id = null;
            this.tindakan.item_nama = null;
            this.tindakan.jumlah = 1;
            this.tindakan.satuan = null;
            this.tindakan.klasifikasi =  null;
            this.tindakan.unit_id = this.poliTransaction.unit_id;
            this.tindakan.unit_nama = this.poliTransaction.unit_nama;
            this.tindakan.depo_id = null;
            this.tindakan.depo_nama = null;
            this.tindakan.tindakan_id = null;
            this.tindakan.tindakan_nama = null;
            this.tindakan.bhp_tindakan = false;
            this.tindakan.kelas_harga_id = this.poliTransaction.kelas_harga_id;
            this.tindakan.buku_harga_id = this.poliTransaction.buku_harga_id;
            this.tindakan.nilai = 0;
            this.tindakan.diskon = 0;
            this.tindakan.diskon_persen = 0;
            this.tindakan.harga_diskon = 0;
            this.tindakan.subtotal = 0;            
            this.tindakan.dokter_pengirim_id = this.poliTransaction.dokter_pengirim_id;
            this.tindakan.is_racikan = false;
            this.tindakan.is_aktif = true;
            this.tindakan.signa = null;
            this.tindakan.signa_deskripsi = null;
            this.tindakan.komponen = [];
            this.tindakan.bhp = [];
        },

        depoSelected(){
            this.urlItemDepoPicker = `/depos/${this.bhp.depo_id}/products`;
        },

        tindakanBhpSelected(){
            let val = this.poliActs.find(data => data.tindakan_id == this.bhp.tindakan_id);
            if(val) { this.bhp.tindakan_nama = val.item_nama; }
        },

        searchBhp(){
            this.urlPicker = `/depos/${this.bhp.depo_id}/products`;
            this.$refs.bhpModalPicker.showModal(this.urlPicker);
        },

        addBhp(dataVal){
            this.CLR_ERRORS();
            if(dataVal) {
                let trxData = this.poliTransaction;
                let data = JSON.parse(JSON.stringify(dataVal));
                this.poliItemUsages.push({
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
                        depo_id : this.defaultDepo.depo_id,//data.depo_id,
                        depo_nama : this.defaultDepo.depo_nama,//data.depo_nama,
                        tindakan_id : data.tindakan_id,
                        tindakan_nama : data.tindakan_nama,
                        bhp_tindakan : data.bhp_tindakan,
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

                this.calculateTotalBhp();
            }
        },

        calculateTotalBhp(){
            let total = 0;          
            this.poliItemUsages.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                //console.log(total);
                //this.bhp.total = total; 
            })
        },

        jmlBhpChange(){
            this.bhp.subtotal = this.bhp.jumlah * this.bhp.nilai;
        },

        calculateTotal(){
            let total = 0;          
            this.poliTransaction.tindakan.detail.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.poliTransaction.tindakan.total = total; 
            })
        },

        changeActivationActItem(){
            let acts = this.poliActs.filter(
                item => { return item.detail_id !== null || item.is_aktif == true }
            );
            this.SET_ACTS_POLI(JSON.parse(JSON.stringify(acts)));
            this.calculateTotal();
        },

        changeActivationBhpItem(){
            let items = this.poliItemUsages.filter(
                item => { return item.detail_id !== null || item.is_aktif == true }
            );
            this.SET_ITEM_USAGE(JSON.parse(JSON.stringify(items)));
            //this.calculateTotal();
        },

        calculateTotalBhp(){
            let total = 0;          
            this.poliTransaction.bhp.detail.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.poliTransaction.bhp.total = total; 
            })
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        bhpSelected(data){
            if(data) {
                this.bhp.item_id = data.produk_id;
                this.bhp.item_nama = data.produk_nama;
                this.bhp.satuan = data.satuan;
                this.bhp.jumlah = 1;
                this.bhp.nilai = data.hna;
                this.bhp.diskon = 0;
                this.bhp.diskon_persen = 0;
                this.bhp.harga_diskon = 0;
                this.bhp.subtotal = data.hna;
                this.bhp.dokter_nama = this.poliTransaction.dokter_nama;
                this.bhp.dokter_id = this.poliTransaction.dokter_id;
             }
        },

        addBhpManual(){
            if(this.bhp.depo_id == null || this.bhp.depo_id == '') {
                alert('depo tidak boleh kosong'); return false;
            }

            if(this.bhp.item_id == null || this.bhp.item_id == '') {
                alert('item bhp tidak boleh kosong'); return false;
            }

            if(this.bhp.tindakan_id !== null && this.bhp.tindakan_id !== '') {
                this.bhp.bhp_tindakan = true;
            }
            let val = this.poliActs.find(data => data.item_id == this.bhp.tindakan_id);
            console.log(val);
            if(val) {
                this.bhp.tindakan_nama = val.item_nama;
            }

            this.poliItemUsages.push(JSON.parse(JSON.stringify(this.bhp)));
            this.clearBhpData();
        },

        clearBhpData(){
            this.bhp.detail_id = null;
            this.bhp.reg_id = null;
            this.bhp.trx_id = null;
            this.bhp.sub_trx_id = null;
            this.bhp.item_id = null;
            this.bhp.item_nama = null;
            this.bhp.jumlah = 1;
            this.bhp.satuan = null;
            this.bhp.klasifikasi =  null;
            this.bhp.unit_id = null;
            this.bhp.unit_nama = null;
            this.bhp.depo_id = this.defaultDepo.depo_id;
            this.bhp.depo_nama = this.defaultDepo.depo_nama;
            this.bhp.tindakan_id = null;
            this.bhp.tindakan_nama = null;
            this.bhp.bhp_tindakan = false;
            this.bhp.kelas_harga_id = '-';
            this.bhp.buku_harga_id = '-';
            this.bhp.nilai = 0;
            this.bhp.diskon = 0;
            this.bhp.diskon_persen = 0;
            this.bhp.harga_diskon = 0;
            this.bhp.subtotal = 0;
            this.bhp.dokter_nama = null;
            this.bhp.dokter_id = null;
            this.bhp.dokter_pengirim_id = null;
            this.bhp.is_racikan = false;
            this.bhp.is_aktif = true;
            this.bhp.signa = null;
            this.bhp.signa_deskripsi = null;
            this.bhp.komponen = [];
        }
    }
}
</script>