<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:true;" :id="modalId" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Asesmen</h2>
            <div style="padding:0;margin:0;">
                <form action="" @submit.prevent="submitResep">
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 0 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">Resep Dokter</h5>
                            <!-- <p style="font-size:12px;font-style:italic;padding:0;margin:0;">catatan ICD Diagnosa.</p> -->
                        </div>
                        <div class="uk-width-1-1@s uk-grid-small" uk-grid> 
                            <div class="uk-width-1-1@m">
                                <div style="padding:0 0 0.2em 0;">
                                    <table class="uk-table uk-table-striped" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-reg" style="text-align:center;color:black;">Racik</th>
                                            <th class="tb-header-reg" style="text-align:left;color:black;">Item Resep</th>
                                            <th class="tb-header-reg" style="text-align:left;color:black;">Cara Pakai</th>
                                            <th class="tb-header-reg" style="width:40px;text-align:center;color:black;">Jumlah</th>
                                            <th class="tb-header-reg" style="text-align:center;color:black;">Satuan</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:black;">Harga</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:black;">Subtotal</th>
                                        </thead>
                                        <tbody>
                                            <!-- <tr>
                                                <td style="padding:1em; font-size: 12px; text-align:center; color:black;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="itemResep.is_racikan" style="text-align: center;" @change="racikanChange">
                                                </td>
                                                <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
                                                    <search-select  ref="searchObat"
                                                        :config = configSelect
                                                        searchUrl = '/products/medical/items'
                                                        placeholder = "pilih item obat"
                                                        captionField = "produk_nama"
                                                        valueField = "produk_nama"
                                                        :itemListData = produkDesc
                                                        :value = "itemResep.produk_nama"
                                                        v-on:item-select = "itemSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; color:black;">
                                                    <search-select  ref="searchSigna"
                                                        :config = configSelect
                                                        searchUrl = '/signas'
                                                        placeholder = "pilih signa"
                                                        captionField = "signa"
                                                        valueField = "signa"
                                                        :itemListData = signaDesc
                                                        :value = "itemResep.signa"
                                                        v-on:item-select = "signaSelected"
                                                    ></search-select>
                                                </td>

                                                <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <input id="jumlah" v-model="itemResep.jumlah" class="uk-input uk-form-small" type="number" style="text-align: center;" @change="onChangeJumlah">
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <search-select  ref="searchSatuan"
                                                        :config = configSelect
                                                        searchUrl = '/uoms'
                                                        placeholder = "pilih satuan"
                                                        captionField = "satuan_id"
                                                        valueField = "satuan_id"
                                                        :itemListData = satuanDesc
                                                        :value = "itemResep.satuan"
                                                        v-on:item-select = "satuanSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemResep.harga)}}</td>
                                                <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemResep.subtotal)}}</td>
                                                
                                                <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                    <button type="button" @click.prevent="insertResep">Tambah</button>
                                                </td>
                                            </tr> -->
                                            <template v-for="dt in prescriptionItems">
                                                <tr v-if="dt.is_aktif">
                                                    <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_racikan" style="text-align: center;" disabled>
                                                    </td>
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;" readonly>{{dt.item_nama}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black;">
                                                        <p style="margin:0;padding:0;font-weight: 400;">
                                                            <strong>{{ dt.signa }}</strong> {{ dt.signa_deskripsi }}
                                                        </p>
                                                    </td>        
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;" readonly>{{dt.jumlah}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black; text-align:center;">
                                                        <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;" readonly>{{dt.satuan}}</p>
                                                    </td>
                                                    <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;" readonly>{{dt.nilai}}</td>
                                                    <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;" readonly>{{dt.subtotal}}</td>
                                                </tr>
                                            </template>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0 0.5em 0 0.5em;">
                            <div class="uk-width-expand@m">
                                <h5 style="color:indigo;padding:0;margin:0;">RESEP REALISASI
                                    <span style="font-size:12px;font-style:italic;padding:0;margin:0;"> data resep realisasi.</span>
                                </h5>
                            </div>
                            <div class="uk-width-auto@m">
                                <button @click.prevent="pilihItemPermintaan">Copy Resep</button>
                            </div>
                        </div>
                        <div class="uk-width-1-1@s uk-grid-small" uk-grid> 
                            <div class="uk-width-1-1@m">
                                <div style="padding:0 0 0.2em 0;">
                                    <table class="uk-table uk-table-striped" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-reg" style="text-align:center;color:black;">Racik</th>
                                            <th class="tb-header-reg" style="text-align:left;color:black;">Item Resep</th>
                                            <th class="tb-header-reg" style="text-align:left;color:black;">Cara Pakai</th>
                                            <th class="tb-header-reg" style="width:40px;text-align:center;color:black;">Jumlah</th>
                                            <th class="tb-header-reg" style="text-align:center;color:black;">Satuan</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:black;">Harga</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:right;color:black;">Subtotal</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding:1em; font-size: 12px; text-align:center; color:black;">
                                                    <input class="uk-checkbox" type="checkbox" v-model="itemRealisasi.is_racikan" style="text-align: center;" @change="racikanChange">
                                                </td>
                                                <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
                                                    <search-select  ref="searchObat"
                                                        :config = configSelect
                                                        searchUrl = '/products/medical/items'
                                                        placeholder = "pilih item obat"
                                                        captionField = "produk_nama"
                                                        valueField = "produk_nama"
                                                        :itemListData = produkDesc
                                                        :value = "itemRealisasi.produk_nama"
                                                        v-on:item-select = "itemSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; color:black;">
                                                    <search-select  ref="searchSigna"
                                                        :config = configSelect
                                                        searchUrl = '/signas'
                                                        placeholder = "pilih signa"
                                                        captionField = "signa"
                                                        valueField = "signa"
                                                        :itemListData = signaDesc
                                                        :value = "itemRealisasi.signa"
                                                        v-on:item-select = "signaSelected"
                                                    ></search-select>
                                                </td>

                                                <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <input id="jumlah" v-model="itemRealisasi.jumlah" class="uk-input uk-form-small" type="number" style="text-align: center;" @change="onChangeJumlah">
                                                </td>
                                                <td style="padding:0.4em; font-size: 12px; text-align:center; color:black;">
                                                    <search-select  ref="searchSatuan"
                                                        :config = configSelect
                                                        searchUrl = '/uoms'
                                                        placeholder = "pilih satuan"
                                                        captionField = "satuan_id"
                                                        valueField = "satuan_id"
                                                        :itemListData = satuanDesc
                                                        :value = "itemRealisasi.satuan"
                                                        v-on:item-select = "satuanSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemRealisasi.harga)}}</td>
                                                <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(itemRealisasi.subtotal)}}</td>
                                                
                                                <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                    <button type="button" @click.prevent="insertResep">Tambah</button>
                                                </td>
                                            </tr>
                                            <template v-for="dt in this.itemResep.realization">
                                                <tr v-if="dt.is_aktif">
                                                    <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_racikan" style="text-align: center;" disabled>
                                                    </td>
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;" readonly>{{dt.item_nama}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black;">
                                                        <p style="margin:0;padding:0;font-weight: 400;">
                                                            <strong>{{ dt.signa }}</strong> {{ dt.signa_deskripsi }}
                                                        </p>
                                                    </td>        
                                                    <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;" readonly>{{dt.jumlah}}</p>
                                                    </td>
                                                    <td style="padding:1em; font-size: 12px; color:black; text-align:center;">
                                                        <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;" readonly>{{dt.satuan}}</p>
                                                    </td>
                                                    <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;" readonly>{{dt.nilai}}</td>
                                                    <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;" readonly>{{dt.subtotal}}</td>
                                                    <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" style="text-align: center;" @change="activationChange">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
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
    name : 'form-realisasi-resep',
    props : { 
        modalId : { type:String, required:false, default:'formRealisasiResep' }, 
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
            produkDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],
            signaDesc : [
                { colName : 'signa', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
            ],
            satuanDesc : [
                { colName : 'satuan_id', labelData : '', isTitle : true },
                { colName : 'satuan_nama', labelData : '', isTitle : false },
            ],
            itemResep : {
                detail_id : null,
                produk_id : null,
                produk_nama : null,
                signa : null,
                signa_deskripsi : null,
                satuan : null,
                jenis_produk : null,
                jumlah : 0,
                harga : 0,
                subtotal : 0,
                is_aktif : true,
                is_racikan : false,
                client_id : null,
                realization : []
            },
            itemRealisasi : {
                detail_id : null,
                produk_id : null,
                produk_nama : null,
                signa : null,
                signa_deskripsi : null,
                satuan : null,
                jenis_produk : null,
                jumlah : 0,
                harga : 0,
                subtotal : 0,
                is_aktif : true,
                is_racikan : false,
                client_id : null,
            },
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliItemPrescripts','poliResepLists']),
        ...mapGetters('resep',['prescriptionItems','prescription']),
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',['CLR_POLI_TRANSACTION','SET_POLI_TRANSACTION','SET_ITEM_PRESCRIPTS']),
        ...mapMutations('resep',['CLR_PRESCRIPTION','NEW_PRESCRIPTION','SET_PRESCRIPTION','SET_PRESCRIPTION_ITEMS']),
        ...mapActions('resep',['createPrescription','updatePrescription','dataPrescription', 'realisasiResep']),
        ...mapActions('rawatJalan',['dataTransaksiPoli']),
        ...mapActions('signa',['createSigna','dataSigna','updateSigna']),

        approveResep(resep_id){
            // this.clearSigna();            
            this.dataPrescription(resep_id).then((response)=>{
                if (response.success == true) {
                    this.fillSigna(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formRealisasiResep').show();
                } else {
                    alert(response.message)
                }
            })
        },

        fillSigna(data){
            // console.log(data);
            this.itemResep.client_id = data.client_id;
            data.items.forEach(data => {
                this.itemResep.produk_nama = data.item_nama;
            });
            // this.signa.signa_id = data.signa_id;
            // this.signa.signa = data.signa;
            // this.signa.deskripsi = data.deskripsi;
            // this.signa.is_aktif = data.is_aktif;
        },

        itemSelected(data) {
            if(data) {
                // console.log(data);
                // this.itemResep.detail_id = data.detail_id;
                this.itemRealisasi.produk_nama = data.produk_nama;
                this.itemRealisasi.produk_id = data.produk_id;
                this.itemRealisasi.signa = data.signa;
                this.itemRealisasi.satuan = data.satuan;
                this.itemRealisasi.harga = data.hna;
                this.itemRealisasi.subtotal = data.hna;
            }
        }, 
        signaSelected(data) {
            if(data) {
                this.itemRealisasi.signa = data.signa;
                this.itemRealisasi.signa_deskripsi = data.deskripsi;
            }
        },

        satuanSelected(data) {
            if(data) { this.itemRealisasi.satuan = data.satuan_id; }
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
        
        submitResep(){
            if(confirm(`Proses ini akan mengupdate data resep. Lanjutkan ?`)){
                this.realisasiResep(this.prescription.trx_id).then((response) => {
                    if (response.success == true) {
                        this.isUpdate = true;
                        alert(response.message);
                        this.RefreshPemeriksaan();
                        this.closeModal();
                    }
                    else { 
                        alert (response.message); 
                    }
                });
            };
            // if(this.isUpdate) {
            //     if(confirm(`Proses ini akan menambahkan data resep. Lanjutkan ?`)){
            //         this.updatePrescription(this.prescription).then((response) => {
            //             if (response.success == true) {
            //                 this.isUpdate = true;
            //                 alert(response.message);
            //                 this.RefreshPemeriksaan();
            //                 this.closeModal();
            //             }
            //             else { 
            //                 alert (response.message); 
            //             }
            //         });
            //     };
            // }
            // else {
            //     if(confirm(`Proses ini akan membuat data baru resep. Lanjutkan ?`)){
            //         this.createPrescription(this.prescription).then((response) => {
            //             if (response.success == true) {
            //                 this.isUpdate = true;
            //                 alert(response.message);
            //                 this.RefreshPemeriksaan();
            //                 this.closeModal();
            //             }
            //             else { 
            //                 alert (response.message); 
            //             }
            //         });
            //     };
            // }
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
            this.itemRealisasi.subtotal = jumlah * this.itemRealisasi.harga;
        },

        insertResep(){
            let val = JSON.parse(JSON.stringify(this.itemRealisasi));
            this.itemResep.realization.push(val);
            // console.log(this.itemResep.realization);
            this.calculateTotal();
            this.itemRealisasi.produk_id = null;
            this.itemRealisasi.produk_nama = null;
            this.itemRealisasi.signa = null;
            this.itemRealisasi.signa_deskripsi = null;
            this.itemRealisasi.satuan = null;
            this.itemRealisasi.jenis_produk = null;
            this.itemRealisasi.jumlah = null;
            this.itemRealisasi.harga = null;
            this.itemRealisasi.subtotal = null;
            this.itemRealisasi.is_racikan = false;
            this.itemRealisasi.is_aktif = true;
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

        pilihItemPermintaan() {
            this.prescriptionItems.forEach(data => {
                this.itemResep.realization.push(data);
            })
            // let val = JSON.parse(JSON.stringify(this.prescriptionItems[0]));
            // this.itemResep.realization.push(val);
            // console.log(this.itemResep.realization);
        },

        activationChange() {
            this.itemResep.realization = [];
        }
    }
}
</script>