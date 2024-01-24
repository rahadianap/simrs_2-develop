<template>
    <div class="uk-container" style="margin-top:15px">
        <form action="" @submit.prevent="addItemResep">
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-3-4@m">
                    <search-select
                        label = "Nama Obat"
                        :config = configSelect
                        searchUrl = '/products/medical/items'
                        placeholder = "nama obat"
                        captionField = "produk_nama"
                        valueField = "produk_nama"
                        :itemListData = obatDesc
                        :value = "dataObat.item_id"
                        v-on:item-select = "obatSelected"
                    ></search-select>
                </div>
                <div class="uk-width-1-4@m">
                    <label style="padding:0;margin:0;font-size:12px;">Jumlah</label>
                    <div>
                        <input class="uk-input uk-form-small" type="number" step="0.01" v-model="dataObat.jumlah" placeholder="jumlah" style="font-weight:400; background-color: white;color:black;border:1px solid #aaa; border-radius:7px;">
                    </div>
                </div>
                <div class="uk-width-1-1@m">
                    <label style="padding:0;margin:0;font-size:12px;">Aturan Pakai</label>
                    <div>
                        <input class="uk-input uk-form-small" type="text" v-model="dataObat.signa_deskripsi" placeholder="aturan pakai" style="font-weight:400; background-color: white;color:black;border:1px solid #aaa; border-radius:7px;">
                    </div>
                </div>
                <div class="uk-width-1-2@m">
                    <a href="#" @click.prevent="addNewObat" style="margin-right:10px; border-radius: 7px; border:none; font-size: 12px; font-style: italic; color:blue;">(buat) master Obat</a>                                        
                </div> 
                <div class="uk-width-1-2@m" style="text-align: right;margin-bottom: 10px;">
                    <button class="uk-button uk-button-primary" type="button" title="Cetak" style="border-radius: 7px;border:none;font-size: 14px; text-transform: none;background: rgba(30, 135, 240, 1);color:white;margin-right:5px;" @click.prevent="printResep()"> Cetak </button>
                    <button class="uk-button uk-button-primary" type="submit" style="border-radius: 7px;border:none;font-size: 14px; text-transform: none;background: rgba(30, 135, 240, 1);color:white">Tambah</button>                                        
                </div>
            </div>
        </form>
            
        <!-- Table -->
        <div>
            <div class="uk-card card-table-bg uk-card-default uk-padding-remove-top uk-margin-remove-top">
            <!-- Tabel dalam card -->
                <table class="uk-table" style="padding: 0 1em;">
                <thead>
                    <tr class="table-th-text">
                        <th v-for="column in columns" :key="column" style="text-align:center">
                            <a @click="sortBy(column)" :class="{ active: sortKey === column }">
                                <td>{{ column.toUpperCase() }}</td>
                                <td></td>
                            </a>
                        </th>
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

                    <tr v-else-if="resepData.items && resepData.items.length > 0" v-for="item in resepData.items" :key="item in resepData" class="uk-card uk-card-default" style="border-radius: 7px; background-color: #FFFFFF;" >
                        <td>
                            <p style="padding:0;margin:0;">{{ item.item_nama }}</p>
                            <p style="padding:0;margin:0;font-weight: 500;font-size:12px;">{{ item.signa_deskripsi }}</p>
                        </td>
                        <td style="text-align: center;">{{ item.jumlah }}</td>

                        <td style="width:20px">
                            <li>
                                <a href="#" 
                                    style="color:#F1595F" 
                                    @click.prevent="hapusData(item)">
                                    <span class="uk-margin-small-right" uk-tooltip="Hapus">
                                        <font-awesome-icon :icon="['fas', 'trash']" size="lg" style="color: #F1595F;"/>
                                    </span>
                                </a>
                            </li>     
                        </td>
                    </tr>

                     <!-- START Empty state -->
                     <tr v-else>
                        <td :colspan="3" class="uk-card uk-card-default  uk-text-center" style="border-radius: 7px; background-color: #FFFFFF">
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
                </table>
            </div>
        </div>

        <modal-master-obat ref="modalObat"></modal-master-obat>

        <!-- Alert hapus -->
        <div class="uk-margin-top">
            <AlertInput
            ref="alertInput"
            :showComponentAlertInput="showAlertInput"
            titleAlert="Hapus Data"
            :subtitleAlert="`Data '${selectedData ? selectedData.detail_id : ''}' dengan nama '${selectedData ? selectedData.item_nama : ''}' akan dihapus. Anda yakin?`"
            v-model="formData"
            confirm="Lanjutkan"
            @confirm="submitHapus"
            v-on:confirm="submitHapus"
            cancel="Tutup"
            @tutup-alert="showAlertInput = false"
            />
        </div>

        <!-- alert success -->
        <div class="uk-margin-top">
        <AlertSuccess
            ref="alertSuccess"
            :showComponentAlertSuccess="showAlertSuccess"
            titleSuccess="Success"
            :subtitleSuccess="`Data '${selectedData ? selectedData.detail_id : ''}' dengan nama '${selectedData ? selectedData.item_nama : ''}' berhasil dihapus.`"
            confirmSuccess="Tutup"
            @tutup-alert="showAlertSuccess = false"
        />
        </div>

    </div>

</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import "../../Dashboard/tabs/dashboardStyle.css";
import AlertInput from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertInput.vue';
import AlertSuccess from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertSuccess.vue';
import ModalMasterObat from '@/Pages/PraktekDokter/LayananDokter/components/ModalMasterObat/index.vue';

import SearchSelect from '@/Pages/PraktekDokter/components/SearchSelect.vue';

import { far } from '@fortawesome/free-regular-svg-icons';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
library.add(far, fas);

export default {
    name : 'resep',
    components : {
        AlertInput,
        AlertSuccess,
        ModalMasterObat,
        SearchSelect,
        FontAwesomeIcon
    },
    data(){
        return {
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            obatDesc: [
            { colName : 'produk_id', labelData : '', isTitle : false },
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'aturan_pakai', labelData : '', isTitle : false },
            ],

            selectedData : null,
            showAlertInput: false,
            showAlertSuccess : false,
            
            formData: "",

            sortKey: '',
            
            search: '',
            
            reverse: false,
            
            columns: ['Nama Obat', 'Qty'],


            emrResep: [
            {namaObat: 'Paracetamol', qty: '1', caraPakai: '3 x 1 Tablet Sehari setelah makan'},
            {namaObat: 'Paracetamol', qty: '2', caraPakai: '3 x 1 Tablet Sehari setelah makan'},
            ],
            isLoading : false,

            dataObat : {
                detail_id : null,
                item_id : null,
                item_nama : null,
                jumlah : 1,
                signa : '',
                signa_deskripsi : null,
                is_aktif : true,
            }    
        }
    },

    computed: {
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['resepData']),        
    },

    mounted() {
        if (!this.isLoading && this.resepData.items.length === 0) {
        this.initialize();
        }
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        // ...mapMutations('pemeriksaan',['CLR_EXAMINATION','NEW_EXAMINATION','SET_EXAMINATION','SET_EXAMINATION_ITEMS']),
        // ...mapActions('rawatJalan',['dataTransaksiPoli','updateTransaksiPoli','createTransaksiPoli']),
        
        // ...mapMutations('resep',['CLR_PRESCRIPTION','NEW_PRESCRIPTION','SET_PRESCRIPTION','SET_PRESCRIPTION_ITEMS']),
        // ...mapActions('resep',['createPrescription','updatePrescription','dataPrescription']),

        ...mapActions('praktekDokter',['dataResep','updateResep','cetakResep']),


        initialize() {
            this.isLoading = true;
            this.submitFilter();
            this.clearDataObat();
        },

        submitFilter(){
            this.selectedData = null;
            this.isLoading = false;
        },
 
        hapusData(data) {
            this.selectedData = data;
            this.showAlertInput = true;
            
        },
        
        // AlertInput sebagai konfirmasi hapus data
        // AlertSuccess sebagai tanda sukses hapus data
        submitHapus() {
            this.CLR_ERRORS();

            if (this.selectedData && this.selectedData.item_id) {
                const indexToRemove = this.resepData.items.findIndex(item => item.item_id === this.selectedData.item_id);

                if (indexToRemove !== -1) {
                    this.resepData.items[indexToRemove].is_aktif = false;

                    this.updateResep(this.resepData).then(response => {
                        if (response.success) {
                            this.showAlertInput = false;
                            this.showAlertSuccess = true;
                            this.refreshData(this.resepData.reg_id);
                            //this.resepData.items.splice(indexToRemove, 1);
                        } 
                        else {
                            alert(response.message);
                        }
                    });

                this.isLoading = true;
                setTimeout(() => {
                        this.isLoading = false;
                    }, 1000);
                } 
                else {
                    alert("Error: Data tidak ditemukan");
                }
            } else {
                alert("Error: Ada kesalahan memilih data");
            }
        },

        refreshData(regId) {
            this.clearDataObat();
            this.CLR_ERRORS();
            
            if(regId) {
                this.dataResep(regId, { is_aktif: true }).then(response => {
                    if (response.success) {
                        this.isUpdate = true;
                    } else {
                        alert(response.message);
                    }
                });
            }
        },

        addNewObat(){
            this.$refs.modalObat.showModal();
        },

        clearDataObat(){
            this.dataObat.detail_id = null;
            this.dataObat.item_id = null;
            this.dataObat.item_nama = null;
            this.dataObat.jumlah = 1;
            this.dataObat.signa = '';
            this.dataObat.signa_deskripsi = null;
            this.dataObat.is_aktif = true;
        },

        obatSelected(data){
            if(data) {
                let dt = JSON.parse(JSON.stringify(data));
                this.dataObat.item_id = data.produk_id;
                this.dataObat.item_nama = data.produk_nama;
                this.dataObat.signa_deskripsi = data.aturan_pakai;                
            }
        },

        addItemResep() {

            let exist = this.resepData.items.find(item => item.item_id == this.dataObat.item_id);
            if(exist) {
                alert('item sudah ada di resep');
                return false;
            }

            let dt = JSON.parse(JSON.stringify(this.dataObat));
            this.resepData.items.push(dt);

            this.CLR_ERRORS();
            this.updateResep(this.resepData).then((response) => {
                if (response.success == true) {
                    this.isUpdate = true;
                    console.log(this.resepData);
                    this.refreshData(this.resepData.reg_id);
                }
                else {
                    alert(response.message);
                }
            })
        }, 

        printResep(){
          this.cetakResep(this.resepData.reg_id).then((response) => {
              var newNode = document.createElement('p');
              newNode.appendChild(document.createTextNode('html string'));
              this.printDiv(response,"3.4in 4.5in");
          });
        },
        
        getDateFormated(curDate){
              const today = new Date(curDate);
              const yyyy = today.getFullYear();
              let mm = today.getMonth() + 1; // Months start at 0!
              let dd = today.getDate();

              if (dd < 10) dd = '0' + dd;
              if (mm < 10) mm = '0' + mm;

              const formattedToday = dd + '/' + mm + '/' + yyyy;
              return formattedToday;
        },

        printDiv(pdf_body, paperSize) {
            const customPaperSize = paperSize;
            let frame1 = document.createElement('iframe');
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            let frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();

            // Set custom paper size using CSS @page rule
            const customStyles = `
                <style>
                    @page {
                        size: ${customPaperSize};
                    }
                </style>
            `;

            frameDoc.document.write(customStyles);

            // Use the entire content as one page
            const fullHtml = pdf_body;

            frameDoc.document.write(fullHtml);
            frameDoc.document.close();

            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 1000);

            return false;
        },
    }
}
</script>
<style scoped>

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
</style>