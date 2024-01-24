<template>
    <div class="uk-container" style="margin-top:15px;">

        <form action="" @submit.prevent="insertDiagnosa">
            <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0;margin:0;">
                <div class="uk-width-1-3@m">
                    <label style="padding:0;margin:0;font-size:12px;">Tipe Diagnosa</label>
                    <div>
                        <select class="uk-select uk-form-small" v-model="diagData.tipe" style="font-weight:400; background-color: white;color:black;border:1px solid #aaa; border-radius:7px;" required>
                            <option></option>
                            <option value="PRIMER">PRIMER</option>
                            <option value="SEKUNDER">SEKUNDER</option>
                        </select>
                    </div>
                </div>
                <div class="uk-width-2-3@m">
                    <search-select
                        label = "ICD Diagnosa"
                        :config = configSelect
                        searchUrl = '/icd10'
                        placeholder = "icd diagnosa"
                        captionField = "nama_icd"
                        valueField = "kode_icd"
                        :itemListData = icdDesc
                        :value = "diagData.kode_icd"
                        v-on:item-select = "icdSelected"
                    ></search-select>
                </div>
                <div class="uk-width-1-3@m">
                    <label style="color:black; font-size: 12px; font-weight: 400;">
                        <input type="checkbox" class="uk-checkbox" v-model="diagData.kasus_lama"  style="border:1px solid #aaa; background-color:white;color:black;margin-right:10px;">
                        Kasus Lama
                    </label>
                </div>
                <div class="uk-width-2-3@m" style="text-align: right;">
                    <a href="#" @click.prevent="addNewDiagnosa" style="margin-right:10px; border-radius: 7px; border:none; font-size: 12px; font-style: italic; color:blue;">(buat) master ICD diagnosa</a>                                        
                </div>
                

                <div class="uk-width-1-1">
                    <button class="uk-button uk-button-primary uk-width-1-1" type="submit" style="border-radius: 7px;border:none;font-size: 14px; text-transform: none;background: rgba(30, 135, 240, 1);color:white">Tambah</button>                                        
                </div>
            </div>
        </form>
        
        <div class="uk-grid-small" uk-grid style="padding:0;margin:1em 0 0 0;">
            <div class="uk-width-1-1">
                <div class="uk-card card-table-bg uk-card-default uk-padding-remove-top uk-margin-remove-top">
                <!-- Tabel dalam card -->
                    <table class="uk-table" style="padding: 0 0.5em 0.5em 0.5em;">
                        <thead>
                            <tr class="table-th-text">
                                <th style="width:60px;font-size:14px;font-weight: 600;">TIPE</th>
                                <th style="font-size:14px;font-weight: 600;">DIAGNOSA</th>
                                <th></th>
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

                        <tr v-else-if="soapData.diagnosa && soapData.diagnosa.length > 0" v-for="item in soapData.diagnosa"  class="uk-card uk-card-default" style="border-radius: 7px; background-color: #FFFFFF;" :key="item in soapData">
                            <td style="width:60px;">
                                <p style="padding:0;margin:0;font-weight: 500; font-size: 14px;">{{ item.tipe }}</p>
                                <p style="padding:0;margin:0;font-weight: 700;">
                                    <span v-if="item.kasus_lama" class="uk-label" style="background-color:blue; color:white;padding:2px;font-size:10px;border-radius:5px;">Kasus Lama</span>
                                    <span v-else class="uk-label uk-label-warning" style="background-color:yellow; color:black;padding:2px;font-size:10px;border-radius:5px;">Kasus Baru</span>
                                </p>
                            </td>
                            <td>
                                <p style="padding:0;margin:0;font-weight: 700;">
                                    {{ item.kode_icd }}
                                </p>
                                <p style="padding:0;margin:0;font-weight: 500; font-size: 14px;">{{ item.nama_icd }}</p>
                            </td>
                            <td style="width:15px">
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
                            <td :colspan="columns.length" class="uk-card uk-card-default uk-text-center" style="border-radius: 7px; background-color: #FFFFFF;text=align:center">
                                <img src="../../../../Assets/icon-data-not-found.png" style="height:135px" />
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
        </div>
        <!-- Alert hapus -->
        <div class="uk-margin-top">
            <AlertInput
                ref="alertInput"
                :showComponentAlertInput="showAlertInput"
                :showInput="showInput"
                titleAlert="Hapus Data"
                :subtitleAlert="`Data '${selectedData ? selectedData.nama_icd : ''}' dengan tipe '${selectedData ? selectedData.tipe : ''} (${selectedData ? selectedData.kasus_lama ? 'Kasus Lama' : 'Kasus Baru' : ''})' akan dihapus. Anda yakin?`"
                confirm="Ya"
                @confirm="submitHapus"
                cancel="Tidak"
                @tutup-alert="showAlertInput = false"
                v-on:confirm="submitHapus"
            />

        </div> 

        <!-- alert success -->
        <div class="uk-margin-top">
            <AlertSuccess
                ref="alertSuccess"
                :showComponentAlertSuccess="showAlertSuccess"
                titleSuccess="Success"
                :subtitleSuccess="`Data '${selectedData ? selectedData.nama_icd : ''}' dengan tipe '${selectedData ? selectedData.tipe : ''} (${selectedData ? selectedData.kasus_lama ? 'Kasus Lama' : 'Kasus Baru' : ''})' telah dihapus.`"
                confirmSuccess="Tutup"
                @tutup-alert="showAlertSuccess = false"
            />
        </div>

        <modal-icd ref="modalIcd"></modal-icd>

    </div>

</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import "../../Dashboard/tabs/dashboardStyle.css";
import AlertInput from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertInput.vue';
import AlertSuccess from '@/Pages/PraktekDokter/PencatatanKas/components/AlertBox/AlertSuccess.vue';
import ModalIcd from '@/Pages/PraktekDokter/LayananDokter/components/ModalIcd/index.vue';

import SearchSelect from '@/Pages/PraktekDokter/components/SearchSelect.vue';

import { far } from '@fortawesome/free-regular-svg-icons';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
library.add(far, fas);

export default {
    name : 'diagnosa',
    components : {
        AlertInput,
        AlertSuccess,
        ModalIcd,
        SearchSelect,
        FontAwesomeIcon
    },
    data(){
        return {
            showAlertSuccess : false,
            showAlertInput : false,
            showInput: false,
            selectedData : null,
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            icdDesc: [
                { colName : 'kode_icd', labelData : '', isTitle : false },
                { colName : 'nama_icd', labelData : '', isTitle : true },
            ],

            diagData : {
                soap_diag_id : null,
                tipe : null,
                kode_icd :null,
                nama_icd : null,
                diagnosa : null,
                kasus_lama : false,
                is_aktif: true,
            },
            
            sortKey: '',            
            search: '',
            reverse: false,
            columns: [
                { col:'kode_icd', text: 'KODE',}, 
                { col:'nama_icd', text: 'ICD',}, 
            ],
            isLoading : false,
        }
    },

    computed: {
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['soapData','diagnosaLists']),
    },

    mounted() {
        this.initialize();
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('praktekDokter'),

        icdSelected(data){
            if(data){
                this.diagData.kode_icd = data.kode_icd;
                this.diagData.nama_icd = data.nama_icd;
            }
        },

        insertDiagnosa(){
            this.soapData.diagnosa = [];
            this.soapData.diagnosa.push(JSON.parse(JSON.stringify(this.diagData)));
            this.$emit('dataChange',true);
            this.initialize();
            this.isLoading = true;
            setTimeout(() => {
                this.isLoading = false;
            }, 375); 

        },

        initialize(){
            this.diagData.soap_diag_id = null;
            this.diagData.tipe = null;
            this.diagData.kode_icd =null;
            this.diagData.nama_icd = null;
            this.diagData.diagnosa = null;
            this.diagData.kasus_lama = false;
            this.diagData.is_aktif= true;
        },

        hapusData(data) {
            this.selectedData = data;
            this.showAlertInput = true;
            
        },


        // AlertInput sebagai konfirmasi hapus data
        // AlertSuccess sebagai tanda sukses hapus data
        submitHapus() {
            this.CLR_ERRORS();

            if (this.selectedData && this.selectedData.kode_icd) {
                const indexToRemove = this.soapData.diagnosa.findIndex(item => item.kode_icd === this.selectedData.kode_icd);

                if (indexToRemove !== -1) {
                    this.soapData.diagnosa[indexToRemove].is_aktif = false;

                    this.$emit('dataChange', true);

                    this.showAlertInput = false;
                    this.showAlertSuccess = true;

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

        addNewDiagnosa(){
            this.$refs.modalIcd.showModal();
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