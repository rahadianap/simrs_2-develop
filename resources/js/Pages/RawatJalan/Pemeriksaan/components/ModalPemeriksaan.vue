<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:true;" :id="modalId" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Tindakan</h2>
            <div style="padding:0;margin:0;">
                <form action="" @submit.prevent="simpanPemeriksaan">
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 0 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">Tindakan dan BHP Pemeriksaan</h5>
                            <!-- <p style="font-size:12px;font-style:italic;padding:0;margin:0;">catatan ICD Diagnosa.</p> -->
                        </div>
                        <div class="uk-width-1-1@s uk-grid-small" uk-grid> 
                            <div class="uk-width-1-1@m">
                                <div style="padding:0 0 0.2em 0;">
                                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-reg" style="text-align:center;color:white;">Tindakan</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">Dokter</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">Jumlah</th>
                                            <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Harga</th>
                                            <th class="tb-header-reg" style="text-align:center;color:white;">Diskpn</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>
                                            <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
                                                    <search-select  ref="searchObat"
                                                        :config = configSelect
                                                        :searchUrl = urlTindakan
                                                        placeholder = "pilih tindakan"
                                                        captionField = "tindakan_nama"
                                                        valueField = "tindakan_nama"
                                                        :itemListData = tindakanDesc
                                                        :value = "tindakan.tindakan_nama"
                                                        v-on:item-select = "tindakanSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; color:black;">
                                                    <search-select  ref="searchDokter"
                                                        :config = configSelect
                                                        :searchUrl = urlDokter
                                                        placeholder = "pilih dokter"
                                                        captionField = "dokter_nama"
                                                        valueField = "dokter_nama"
                                                        :itemListData = dokterDesc
                                                        :value = "tindakan.dokter_nama"
                                                        v-on:item-select = "dokterSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <input id="jumlah" v-model="tindakan.jumlah" class="uk-input uk-form-small" type="number" style="text-align: center;" @change="onChangeJumlah">
                                                </td>
                                                <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(tindakan.nilai)}}</td>
                                                <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(tindakan.diskon)}}</td>
                                                <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(tindakan.subtotal)}}</td>                            
                                                <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                    <button type="button" @click.prevent="insertTindakan">Tambah</button>
                                                </td>
                                            </tr>
                                            
                                            <template v-for="dt in examinationItems">
                                                <tr v-if="dt.is_aktif">
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dt.tindakan_nama}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black;">
                                                        <p style="margin:0;padding:0;font-weight: 400;">{{ dt.dokter_nama }}</p>
                                                    </td>        
                                                    <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.jumlah)}}</td>
                                                    <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.harga)}}</td>
                                                    <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.diskon)}}</td>
                                                    <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.subtotal)}}</td>

                                                    <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" style="text-align: center;" @change="activationChange">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="padding:0 0.5em 0.2em 0;">
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
                                            <tr v-if="!examination.is_realisasi">
                                                <td style="padding:0.4em;">
                                                    <select v-model="bhp.depo_id" class="uk-select uk-form-small" @change="depoSelected">
                                                        <option v-for="dt in depoLists" :value="dt.depo_id">{{ dt.depo_nama }}</option>
                                                    </select>
                                                </td>
                                                <td style="padding:0.4em;">
                                                    <search-select
                                                        :config = configSelect
                                                        :searchUrl = urlItemDepo
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
                                                        <option v-for="act in tindakanLists" :value="act.tindakan_id">{{ act.tindakan_nama }}</option>
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
                                            <template v-for="dt in bhpItems">
                                                <tr v-if="dt.is_aktif">
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dt.depo_id}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black;">
                                                        <p style="margin:0;padding:0;font-weight: 400;">
                                                            {{ dt.item_nama }}
                                                        </p>
                                                    </td>
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dt.tindakan_id}}</p>
                                                    </td>        
                                                    <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.jumlah)}}</td>
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dt.satuan}}</p>
                                                    </td>
                                                    <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.nilai)}}</td>
                                                    <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(dt.subtotal)}}</td>
                                                    <td style="padding:1em;text-align:center;font-size:12px;color:black;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.bhp_tindakan" style="text-align: center;">
                                                    </td>
                                                    <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" style="text-align: center;" @change="activationChange">
                                                    </td>
                                                </tr>
                                            </template>
                                            <tr>
                                                <td colspan="6" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL PEMERIKSAAN DAN BHP</td>
                                                <td style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(examination.total)}}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div> 
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid>
                        <div class="uk-width-1-1" style="text-align:right;padding:0 1em 0.5em 1em;">
                            <button type="button" class="uk-button uk-modal-close" style="border:none; background-color: whitesmoke; color:#000;">TUTUP</button>
                            <button type="submit" class="uk-button" style="border:none; background-color: #cc0202; color:#fff;margin-left:0.5em;">SIMPAN</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
export default {
    name : 'modal-pemeriksaan',
    props : { 
        modalId : { type:String, required:false, default:'modalAsesmen' }, 
    },
    components : {
        SearchSelect
    },
    data() {
        return {
            isUpdate : false,

            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            tindakanDesc : [
                { colName : 'tindakan_nama', labelData : '', isTitle : true },
                { colName : 'tindakan_id', labelData : '', isTitle : false },
            ],
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],
            produkDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],
            depoLists : [],
            bhpItems : [],
            defaultDepo : null,
            depo: { depo_id: null,  depo_nama: null, },
            bhp : { 
                detail_id : null,
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                item_id : null,
                item_nama : null,
                jenis : 'BHP',
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
                tindakan_id : null,
                tindakan_nama : null,
                jml_formula : 1,
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
            urlTindakan : '',
            urlDokter : '',
            urlItemDepo : ''
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliActs','poliItemUsages']),
        ...mapGetters('pemeriksaan',['examinationItems','tindakanLists','bhpLists','examination']),
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',['CLR_POLI_TRANSACTION','SET_POLI_TRANSACTION','SET_ACTS_POLI','SET_ITEM_USAGE']),
        ...mapMutations('pemeriksaan',['CLR_EXAMINATION','NEW_EXAMINATION','SET_EXAMINATION','SET_EXAMINATION_ITEMS']),
        ...mapActions('rawatJalan',['dataTransaksiPoli','updateTransaksiPoli','createTransaksiPoli']),
        ...mapActions('pemeriksaan',['createPemeriksaan','updatePemeriksaan','dataPemeriksaan','confirmPemeriksaan']),
        ...mapActions('unitPelayanan',['dataUnitPelayanan']),

        pemeriksaanBaru(){
            this.urlTindakan = `/units/${this.poliTransaction.unit_id}/acts`;
            this.urlDokter = `/units/${this.poliTransaction.unit_id}/doctors`;
            // this.urlItemDepo = `/depos/${this.bhp.depo_id}/products`;
            this.isUpdate = false;
            let dt = JSON.parse(JSON.stringify(this.poliTransaction));
            dt.jns_transaksi = 'RAWAT_JALAN';
            this.dataUnitPelayanan(this.poliTransaction.unit_id).then((response) => {
                if (response.success == true) {
                    this.depoLists = JSON.parse(JSON.stringify(response.data.depo));
                    // console.log(this.depoLists); 

                    let depo = this.depoLists.find(data => data.is_default == true);
                    if(depo) { this.defaultDepo = JSON.parse(JSON.stringify(depo)); }
                    else { this.defaultDepo = JSON.parse(JSON.stringify(this.depoLists[0])); }
                    this.bhp.depo_id = this.defaultDepo.depo_id;
                    this.bhp.depo_nama = this.defaultDepo.depo_nama;      
                    this.urlItemDepoPicker = `/depos/${this.defaultDepo.depo_id}/products`;
                    this.urlPicker = `units/${this.poliTransaction.unit_id}/acts`;
                }
            });
            this.NEW_EXAMINATION(dt);
            this.showModal();
        },

        tindakanSelected(data) {
            if(data) {
                this.tindakan.tindakan_id = data.tindakan_id;
                this.tindakan.tindakan_nama = data.tindakan_nama;
                let trxData = this.poliTransaction;
                let hargaRes = data.harga.find(item => item.buku_harga_id == trxData.buku_harga_id && item.kelas_id == trxData.kelas_harga_id);
                
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
                if(this.examinationItems.length > 0) {
                    exist = this.examinationItems.find(item => item.item_id == data.tindakan_id);
                    if(exist) {  alert('Tindakan / pemeriksaan sudah ada.') ; return false; }
                    else { this.insertItemTindakan(hargaRes,data); }
                }
                else { this.insertItemTindakan(hargaRes,data); }
            } 
            else {
                this.tindakan.tindakan_nama = null;
                this.tindakan.tindakan_id = null;
            }
        },

        depoSelected() {
            this.urlItemDepo = `/depos/${this.bhp.depo_id}/products`;
        },

        bhpSelected(data){
            if(data) {
                this.bhp.item_id = data.produk_id;
                this.bhp.item_nama = data.produk_nama;
                this.bhp.jenis = 'BHP';
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

        insertItemTindakan(hargaRes,data) {
            let trxData = this.poliTransaction;
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
            
            this.tindakan.unit_id = trxData.unit_id;
            this.tindakan.unit_nama = trxData.unit_nama;
            this.tindakan.depo_id = null;
            this.tindakan.depo_nama = null;
            this.tindakan.jenis = 'TINDAKAN';
            
            this.tindakan.tindakan_id = data.tindakan_id;
            this.tindakan.tindakan_nama = data.tindakan_nama;
            this.tindakan.jml_formula = 1;
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
            // let val = this.bhpItems.find(data => data.item_id == this.bhp.tindakan_id);
            // console.log(val);
            // if(val) {
            //     this.bhp.tindakan_nama = val.item_nama;
            // }
            this.bhpItems.push(JSON.parse(JSON.stringify(this.bhp)));
            this.calculateTotalBhp();
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
            this.bhp.depo_id = null;
            this.bhp.depo_nama = null;
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
        },

        calculateTotalBhp(){
            let total = 0;          
            this.examinationItems.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.examination.total = total; 
            });
            this.bhpItems.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.examination.total = total; 
            })
        },

        jmlBhpChange(){            
            this.bhp.subtotal = this.bhp.jumlah * this.bhp.nilai;
        },

        calculateTotal(){
            let total = 0; this.examination.total = 0;

            this.examinationItems.forEach(data => {
                if(data.is_aktif){
                    total = (total*1) + (data.subtotal*1);
                    this.examination.total = total; 
                }
            })
        },

        dokterSelected(data) {
            if(data) {
                this.tindakan.dokter_id = data.dokter_id;
                this.tindakan.dokter_nama = data.dokter_nama;
            }
        },

        satuanSelected(data) {
            if(data) { this.itemResep.satuan = data.satuan_id; }
        },

        showModal(){
            UIkit.modal(`#${this.modalId}`).show();
        },

        closeModal(){
            UIkit.modal(`#${this.modalId}`).hide();
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        calculateTotal(){
            let total = 0;
            this.prescriptionItems.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.prescription.total = total; 
            })
        },

        changeRowRegExpand() {
            this.isExpandedReg = !this.isExpandedReg;
            return false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        },
        
        simpanPemeriksaan(){
            this.CLR_ERRORS();
            if(this.isUpdate) {
                if(confirm(`Proses ini akan mengubah data pemeriksaan dan pemakaian BHP. Lanjutkan ?`)){
                    this.updatePemeriksaan(this.examination).then((response) => {
                        if (response.success == true) {
                            this.isUpdate = true;
                            alert(response.message);
                            this.retrieveDataPemeriksaan(response.data);
                        }
                        else { alert (response.message) }
                    });
                };
            }
            else {
                if(confirm(`Proses ini akan membuat data baru pemeriksaan dan pemakaian BHP. Lanjutkan ?`)){
                    this.createPemeriksaan(this.examination).then((response) => {
                        if (response.success == true) {
                            this.isUpdate = true;
                            alert(response.message);
                            this.retrieveDataPemeriksaan(response.data);
                        }
                        else { alert (response.message) }
                    });
                };
            }
        },

        RefreshPemeriksaan(){
            let trxId = this.poliTransaction.trx_id;
            this.CLR_ERRORS();
            this.isLoading = true;
            if(trxId) {
                this.dataTransaksiPoli(trxId).then((response) => {
                    if (response.success == true) {
                        this.SET_POLI_TRANSACTION(response.data);
                        this.isLoading = false;
                    }
                    else { 
                        this.CLR_POLI_TRANSACTION();
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
        },

        onChangeJumlah(){
            let jumlah = document.getElementById("jumlah").value;
            this.itemResep.subtotal = jumlah * this.itemResep.harga;
        },

        insertTindakan(){
            console.log('examination item:');
            console.log(this.examinationItems);
            console.log('tindakan:');
            console.log(JSON.parse(JSON.stringify(this.tindakan)));
            
            let exist = this.examinationItems.find(item => item.tindakan_id ==  this.tindakan.item_id );
            
            let val = JSON.parse(JSON.stringify(this.tindakan));
            // console.log(val);

            this.examinationItems.push(val);
            this.calculateTotalBhp();
            this.clearTindakan();
            // this.tindakan.tindakan_id = null;
            // this.tindakan.tindakan_nama = null;
            // this.tindakan.jumlah = 1;
            // this.tindakan.harga = null;
            
            // console.log(this.examinationItems);
            // this.calculateTotal();
            // this.itemResep.produk_id = null;
            // this.itemResep.produk_nama = null;
            // this.itemResep.signa = null;
            // this.itemResep.signa_deskripsi = null;
            // this.itemResep.satuan = null;
            // this.itemResep.jenis_produk = null;
            // this.itemResep.jumlah = null;
            // this.itemResep.harga = null;
            // this.itemResep.subtotal = null;
            // this.itemResep.is_racikan = false;
            // this.itemResep.is_aktif = true;
        },

        clearTindakan(){             
            this.tindakan.detail_id = null;
            this.tindakan.reg_id = null;
            this.tindakan.trx_id = null;
            this.tindakan.sub_trx_id = null;
            this.tindakan.tindakan_id = null;
            this.tindakan.tindakan_nama = null;
            this.tindakan.jml_formula = 1;
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
            this.tindakan.kelas_harga_id = '-';
            this.tindakan.buku_harga_id = '-';
            this.tindakan.nilai = 0;
            this.tindakan.diskon = 0;
            this.tindakan.diskon_persen = 0;
            this.tindakan.harga_diskon = 0;
            this.tindakan.subtotal = 0;
            this.tindakan.dokter_nama = null;
            this.tindakan.dokter_id = null;
            this.tindakan.dokter_pengirim_id = null;
            this.tindakan.is_racikan = false;
            this.tindakan.is_aktif = true;
            this.tindakan.signa = null;
            this.tindakan.signa_deskripsi = null;
            this.tindakan.komponen = [];
            this.tindakan.bhp = [];
        },

        resepBaru(data) {
            if(data) {
                // console.log(data);
                let val = {
                    reg_id : data.reg_id,
                    trx_id : data.trx_id,
                    reff_trx_id : data.reff_trx_id,
                    dokter_id : data.dokter_id,
                    dokter_nama : data.dokter_nama,
                    unit_id : data.unit_id,
                    unit_nama : data.unit_nama,
                    pasien_id : data.pasien_id,
                };
                // console.log(val);
                this.NEW_PRESCRIPTION(val);
                this.isUpdate = false;
                this.showModal();
            }
        },
    }
}
</script>