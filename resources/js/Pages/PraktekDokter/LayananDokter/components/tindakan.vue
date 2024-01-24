<template>
    <div class="uk-container" style="margin-top:15px">
        <form v-if="registrasiData && !registrasiData.is_bayar == true" action="" @submit.prevent="submitTindakan">
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-2-5@m">
                    <search-select ref="tindakanPicker"
                        label = "Tindakan"
                        :config = configSelect
                        :searchUrl = urlTindakan
                        placeholder = "pilih tindakan"
                        captionField = "tindakan_nama"
                        valueField = "tindakan_nama"
                        :itemListData = actDesc
                        :value = "actData.item_nama"
                        v-on:item-select = "actSelected"
                    ></search-select>
                    <a href="#" @click.prevent="addPemeriksaanBaru" style="margin-right:10px; border-radius: 7px; border:none; font-size: 12px; font-style: italic; color:blue;">(buat) master pemeriksaan</a>                                        
                </div>
                <div class="uk-width-1-6@m">
                    <label style="padding:0;margin:0;font-size:12px;">Qty</label>
                    <div>
                        <input type="number" class="uk-input uk-form-small" v-model="actData.jumlah" style="font-weight:400; background-color: white;color:black;border:1px solid #aaa; border-radius:7px;" required>
                    </div>
                </div>
                <div class="uk-width-1-5@m">
                    <label style="padding:0;margin:0;font-size:12px;">Harga</label>
                    <div>
                        <input type="number" class="uk-input uk-form-small" v-model="actData.nilai" style="font-weight:400; background-color: white;color:black;border:1px solid #aaa; border-radius:7px;" required>
                    </div>
                </div>
                <div class="uk-width-1-5@m">
                    <label style="padding:0;margin:0;font-size:12px;">Subtotal</label>
                    <div>
                        <input 
                            type="number" 
                            class="uk-input uk-form-small" 
                            :value="calculateSubtotal()"
                            disabled 
                            style="font-weight:400; background-color: #e8e5e5;color:black;border:1px solid #aaa; border-radius:7px; cursor: not-allowed;" 
                            required
                        >
                    </div>
                </div>
            </div>
            <div style="padding:0.5em; text-align:right;">
                <button class="uk-button uk-button-primary uk-width-1-1" type="submit" style="border-radius: 7px;border:none;font-size: 14px; text-transform: none;background: rgba(30, 135, 240, 1);color:white">Tambah</button>                                        
            </div>
        </form>

        <!-- Table -->
        <div>
            <div class="uk-card card-table-bg uk-card-default uk-padding-remove-top uk-margin-remove-top">
            <!-- Tabel dalam card -->
                <table class="uk-table" style="padding: 0 1em;">
                <thead>
                    <tr class="table-th-text" style="font-size:var(--lato-md-14)">
                        <th style="font-weight: 700;font-size: 14px;">Pemeriksaan</th>
                        <th style="font-weight: 700;font-size: 14px;text-align: center;">Jml</th>
                        <th :style="{ 'font-weight': '700', 'font-size': '14px', 'text-align': 'right', 'width': registrasiData && !registrasiData.is_bayar ? '' : '150px' }">Harga Satuan</th>
                        <th style="font-weight: 700;font-size: 14px;text-align: right;">Subtotal</th>
                        <th  v-if="registrasiData && !registrasiData.is_bayar == true" style="width:50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="isLoading">
                        <td style="text-align: center; background-color: #fff;padding:1em;" :colspan="columns.length + 1"> 
                            <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                                <span uk-spinner></span>
                                sedang mengambil data
                            </p>
                        </td>
                    </tr>                         

                    <!-- <tr v-else-if="dataPemeriksaan.items && dataPemeriksaan.items.length > 0" v-for="item in filteredItems" :key="item in dataPemeriksaan" class="uk-card uk-card-default" style="border-radius: 7px; background-color: #FFFFFF;"> -->
                    <tr v-else-if="dataPemeriksaan && dataPemeriksaan.items && dataPemeriksaan.items.length > 0" v-for="item in filteredItems" :key="item in dataPemeriksaan" class="uk-card uk-card-default" style="border-radius: 7px; background-color: #FFFFFF;">

                        <td>
                            <p style="margin:0;padding:0;font-size:14px;font-weight: 700;color:black;">{{ item.item_nama }}</p>
                            <p style="margin:0;padding:0;font-size:10px;font-style:italic;font-weight: 400;">{{ item.item_id }}</p>
                            <!-- <p style="margin:0;padding:0;font-size:12px;font-weight: 400;">{{ item.dokter_nama }}</p> -->
                        </td>
                        <td style="text-align:center;">{{ item.jumlah }}</td>
                        <td style="text-align:right;">{{ formatCurrency(item.nilai) }}</td>
                        <td style="text-align:right;">{{ formatCurrency(item.subtotal) }}</td>
                        <!-- <td>{{ item.dokter_nama }}</td> -->

                        <td v-if="registrasiData && !registrasiData.is_bayar == true" style="width:50px;" class="uk-text-right">
                            <a href="#" 
                                style="color:#F1595F" 
                                @click.prevent="hapusData(item)">
                                <span class="uk-margin-small-right" uk-tooltip="Hapus">
                                    <font-awesome-icon :icon="['fas', 'trash']" size="lg" style="color: #F1595F;"/>
                                </span>
                            </a>                                        
                        </td>
                        
                    </tr>

                    <!-- START Empty state -->
                    <tr v-if="this.dataPemeriksaan.items.filter(item => item.is_aktif).length === 0">
                        <td :colspan="5" class="uk-card uk-card-default  uk-text-center" style="border-radius: 7px; background-color: #FFFFFF">
                            <img src="../../../../Assets/icon-data-not-found.png" style="height:135px"/>
                            <h3 style="font-size: 16px; font-weight: 700; color: black;">
                            Maaf, data kosong.
                            </h3>
                            <p style="font-size: 14px; font-weight: 500; font-style: italic; color: black;margin-bottom:10px">
                            Silahkan masukkan data terlebih dahulu.
                            </p>
                        </td>
                    </tr>
                    <!-- END Empty state -->
                    </tbody>
                    
                    <div v-if="!this.isLoading && this.dataPemeriksaan.items.filter(item => item.is_aktif).length > 0" class="uk-grid" style="width:100%;position:absolute;">
                        <div :class="{ 'uk-width-1-1': true, 'uk-text-right': true, 'uk-margin-large-right': registrasiData && !registrasiData.is_bayar }" style="font-size: 16px; font-weight: bold;">
                            <span>Grand Total : </span> {{ formatCurrency(dataPemeriksaan.total) }}
                        </div>
                    </div>
                </table>
            </div>
        </div>

        <!-- Alert hapus -->
        <div class="uk-margin-top">
            <AlertInput
            ref="alertInput"
            :showComponentAlertInput="showAlertInput"
            :showInput="showInput"
            titleAlert="Hapus Data"
            :subtitleAlert="`Data '${selectedData ? selectedData.tindakan_id : ''}' akan dihapus. Anda yakin?`"
            confirm="Lanjutkan"
            @confirm="submitHapus"
            cancel="Tutup"
            @tutup-alert="showAlertInput = false"
            v-on:confirm = "submitHapus"
            />
        </div>

        <!-- alert success -->
        <div class="uk-margin-top">
        <AlertSuccess
            ref="alertSuccess"
            :showComponentAlertSuccess="showAlertSuccess"
            titleSuccess="Success"
            :subtitleSuccess="`Data '${selectedData ? selectedData.tindakan_id : ''}' telah dihapus.`"
            confirmSuccess="Tutup"
            @tutup-alert="showAlertSuccess = false"
        />
        </div>

        <modal-master-tindakan ref="modalMasterTindakan"></modal-master-tindakan>
    </div>

</template>
<script>
    import { mapGetters, mapMutations, mapActions } from 'vuex';
    import "../../Dashboard/tabs/dashboardStyle.css";
    import AlertInput from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertInput.vue';
    import AlertSuccess from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertSuccess.vue';

    import SearchSelect from '@/Pages/PraktekDokter/components/SearchSelect.vue';

    import ModalMasterTindakan from '@/Pages/PraktekDokter/LayananDokter/components/ModalMasterTindakan/index.vue';

    import { far } from '@fortawesome/free-regular-svg-icons';
    import { fas } from '@fortawesome/free-solid-svg-icons';
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
    library.add(far, fas);


    export default {
        name : 'tindakan',
        components : {
            AlertInput,
            AlertSuccess,
            SearchSelect,
            ModalMasterTindakan,
            FontAwesomeIcon
        },
        data(){
            return {
                urlTindakan : '/praktek/dokter/mastertindakan',
                configSelect : {
                    required : true,
                    compClass : "uk-width-1-1@m",
                    compStyle : "padding:0;margin:0;",
                },

                actDesc: [
                    { colName : 'tindakan_id', labelData : '', isTitle : false },
                    { colName : 'tindakan_nama', labelData : '', isTitle : true },
                ],

                actData : {
                    detail_id : null,
                    item_id : null,
                    item_nama :null,
                    unit_id : 'PDM',
                    unit_nama : 'Praktek Dokter Mandiri',
                    dokter_id : null,
                    dokter_nama : null,
                    jenis : 'TINDAKAN',
                    satuan : 'X',
                    jumlah : 1,
                    is_bhp : false,
                    nilai : 0,
                    diskon_persen : 0,
                    diskon : 0,
                    harga_diskon : 0,
                    subtotal : 0,
                    catatan : null,
                    status : 'SELESAI',
                    is_realisasi : true,
                    is_aktif: true,
                },
                // is_bayar:true,


            selectedData : null,
            showAlertInput: false,
            showInput: false,
            showAlertSuccess : false,
            
            formData: "",

            sortKey: '',
            
            search: '',
            
            reverse: false,
            
            columns: ['Pemeriksaan','Qty', 'Harga Satuan', 'Subtotal'],


            // emrTindakan: [
            //   {namaTindakan: 'Konsultasi Dokter', qty: '1', harga: 'Rp.500.000', namaDokter: 'DR. MUHAMMAD AL KAHFI SP. PD'},
            //   {namaTindakan: 'Konsultasi Gigi', qty: '2', harga: 'Rp.720.000', namaDokter: 'DR. MUHAMMAD AL KAHFI SP. PD'},
            //   {namaTindakan: 'Konsultasi riwayat kesehatan', qty: '3', harga: 'Rp.350.000', namaDokter: 'DR. MUHAMMAD AL KAHFI SP. PD'},
            // ],
            isLoading : false,

            }
        },

        computed: {
            ...mapGetters(['errors']),
            ...mapGetters('praktekDokter',['dataPemeriksaan','registrasiData']),

            filteredItems() {
                return this.dataPemeriksaan.items.filter(item => item.is_aktif);
            },

            // calculateSubtotal() {
            //     return this.actData.jumlah * this.actData.nilai;
            // },
            // calculateSubtotal() {
            //     return this.actData.jumlah * this.actData.nilai;
            // },
        },

        mounted() {
            this.initialize();

        },

        methods : {
            ...mapMutations(['CLR_ERRORS']),

            ...mapActions('praktekDokter',['dataPemeriksaanPasien','updatePemeriksaanPasien','listAntrian']),

            formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            },

            initialize(){
                this.isLoading = false;        
                this.submitFilter(); 
                //console.log(this.dataPemeriksaan)   
            },
            submitFilter(){
                this.selectedData = null;
                this.isLoading = false;
            },

        
            hapusData(data) {
                this.selectedData = data;
                this.showAlertInput = true;
            },

            calculateSubtotal() {
                return this.actData.jumlah * this.actData.nilai;
            },

            // pembayaran(data){
            //     if(data) {
            //         if(data.is_bayar) {
            //         }
            //     }
            // },

            // AlertInput sebagai konfirmasi hapus data
            // AlertSuccess sebagai tanda sukses hapus data
            submitHapus() {
                this.CLR_ERRORS();

                if (this.selectedData && this.selectedData.item_id) {
                    const indexToRemove = this.dataPemeriksaan.items.findIndex(item => item.item_id === this.selectedData.item_id);

                    if (indexToRemove !== -1) {
                    this.dataPemeriksaan.items[indexToRemove].is_aktif = false;
                    this.calculateSubtotal();


                    this.updatePemeriksaanPasien(this.dataPemeriksaan).then(response => {
                        if (response.success) {
                        this.showAlertInput = false;
                        this.showAlertSuccess = true;

                        // this.dataPemeriksaan.items.is_aktif.splice(indexToRemove, 1);
                        this.dataPemeriksaan.items = this.dataPemeriksaan.items.filter(item => item.is_aktif);
                        this.calculateSubtotal();
                        } else {
                        alert(response.message);
                        }
                    });

                    this.isLoading = true;
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 1000);
                    } else {
                    alert("Error: Data tidak ditemukan");
                    }
                } else {
                    alert("Error: Ada kesalahan memilih data");
                }
            },



            // refreshData(regId) {
            //     this.dataPemeriksaanPasien(regId).then((response) => {
            //         if (response.success == true) {
            //             this.isUpdate = true;
            //             console.log(this.dataPemeriksaan);
            //         }
            //         else {
            //             alert(response.message);
            //         }
            //     })
            // },-
            refreshData(regId) {
                this.CLR_ERRORS();

                if(regId) {
                    this.dataPemeriksaanPasien(regId, { is_aktif: true }).then(response => {
                        if (response.success) {
                            this.isUpdate = true;
                            //console.log(this.dataPemeriksaan);
                            console.log(this.soapData);
                        } else {
                            alert(response.message);
                        }
                    });
                }
            },

            actSelected(data){
                if(data)  {
                    this.actData.item_nama = data.tindakan_nama;
                    this.actData.item_id = data.tindakan_id;
                    this.actData.nilai = data.harga;
                    this.actData.subtotal = data.harga;
                }
            },

            calculateTotal() {
                let total = 0;
                this.dataPemeriksaan.items.forEach(item => {
                    if(item.is_aktif) {
                        total = total + (item.subtotal * 1);
                    }
                });
                this.dataPemeriksaan.total = total;
            },

            submitTindakan(){
                this.isLoading = true;
                
                // this.actData.subtotal = this.actData.jumlah * this.actData.nilai;
                this.actData.subtotal = this.calculateSubtotal();

                let exist = this.dataPemeriksaan.items.find(item => item.item_id == this.actData.item_id);
                if(exist) {
                    alert('Item pemeriksaan sudah ada.');
                    this.isLoading = false;
                    return false;
                }
                else {
                    this.savePemeriksaan();
                }
                
                
            },

            savePemeriksaan(){
                this.CLR_ERRORS();
                // this.actData.subtotal = this.actData.jumlah * this.actData.nilai * 1;
                // this.actData.subtotal = this.calculateSubtotal;

                this.dataPemeriksaan.items.push(JSON.parse(JSON.stringify(this.actData)));
                this.calculateTotal();
                
                //console.log(this.dataPemeriksaan);
                this.updatePemeriksaanPasien(this.dataPemeriksaan).then((response) => {
                    if (response.success == true) {
                        this.isUpdate = true;
                        this.refreshData(response.data.reg_id);
                        this.clrDataPemeriksaan();
                    }
                    else {
                        alert(response.message);
                        this.refreshData(this.dataPemeriksaan.reg_id);
                    }
                    this.isLoading = false;

                })
            },

            clrDataPemeriksaan(){
                this.actData.detail_id  = null;
                this.actData.item_id  = null;
                this.actData.item_nama  =null;
                this.actData.unit_id  = 'PDM';
                this.actData.unit_nama  = 'Praktek Dokter Mandiri';
                this.actData.dokter_id  = null;
                this.actData.dokter_nama  = null;
                this.actData.jenis  = 'TINDAKAN';
                this.actData.satuan  = 'X';
                this.actData.jumlah  = 1;
                this.actData.is_bhp  = false;
                this.actData.nilai  = 0;
                this.actData.diskon_persen  = 0;
                this.actData.diskon  = 0;
                this.actData.harga_diskon  = 0;
                this.actData.subtotal  = 0;
                this.actData.catatan  = null;
                this.actData.status  = 'SELESAI';
                this.actData.is_realisasi  = true;
                this.actData.is_aktif = true;
            },

            addPemeriksaanBaru(){
                this.$refs.modalMasterTindakan.showModal();
            }

            
        }
    }
</script>
<style scoped>
.uk-margin-large-right {
    margin-right: 75px!important;
}
.card-table-bg {
  margin-top: 1em; 
  border-radius: 12px; 
  height: auto;
  padding: 0 0 2em 0;
}
.uk-table {
    border-collapse: inherit;
    border-spacing: 0px 12px;
    width: 100%;
}
.uk-table td {
  font-family: var(--lato);
  color: #4D4D4D;
  font-size: var(--font-sm-12);
  font-weight: var(--bold);
  line-height:20px;
  letter-spacing:0.1px;
}
.uk-table th {
  font-family: var(--inter);
  color: #000000;
  font-size: var(--font-md-14);
  font-weight: var(--semibold);
  line-height:24px;
  letter-spacing:0.1px;
  padding: 0;
  text-align: left;
  vertical-align: center;
}
td:first-child,
th:first-child {
  border-radius: 10px 0 0 10px;
}
td:last-child,
th:last-child {
  border-radius: 0 10px 10px 0;
}
.uk-card-default {
    background-color: #e6dfff40;
    border: 1px solid #D1E8FF;
    color: #666;
    box-shadow: 0 5px 15px rgba(0,0,0,.08);
}
.blur-box {
  background-color: rgb(255 255 255 / 0.3);
  backdrop-filter: blur(3px);
  filter:blur(3px);
  -webkit-filter: blur(3px);
  border-radius: 5px;
  /* max-width: 50%; */
  /* max-height: 50%; */
  /* padding: 20px 40px; */
  cursor:not-allowed;

}
</style>