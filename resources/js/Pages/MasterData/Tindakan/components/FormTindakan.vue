<template>
    <div class="uk-container-large">
        <div class="uk-grid uk-grid-small" style="padding:0.5em 1em 1em 1em;">
            <div class="uk-width-auto" style="padding:0;">
                <a href="#" @click.prevent="closeModalTindakan" uk-icon="icon:arrow-left;ratio:1.5" style="display:block;width:30px;"></a>
            </div>
            <div class="uk-width-expand" style="padding:0;margin:0;">
                <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                    <li>
                        <a href="#" @click.prevent="closeModalTindakan" class="uk-text-uppercase">
                            <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">ITEM PELAYANAN</h4>
                        </a>
                    </li>
                    <!-- <li v-if="tindakan.tindakan_nama !== null && isUpdate">
                        <span style="font-weight:400;color:#333;font-size:14px;"  class="uk-text-uppercase">{{tindakan.tindakan_nama}}</span>
                    </li> -->
                </ul>
            </div>
        </div>
        <div class="uk-container hims-form-container" style="min-height:70vh; background-color:#fff;padding:0 0.5em 0.5em 0.5em;">
            <form action="" @submit.prevent="submitTindakan" style="padding:0;" >
                <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                    
                    <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                        <h5 class="uk-text-uppercase"></h5>
                    </div>       
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="button" @click.prevent="refreshTindakan" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Refresh</button>                  
                    </div>                         
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="button" @click.prevent="closeModalTindakan" class="uk-button-close uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                    </div>
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                        <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                    </div>
                </div>
                <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                    <div uk-spinner="ratio : 2"></div>
                    <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang memproses data...</span>
                </div>
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>
                <div class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            informasi utama terkait tindakan / pemeriksaan.
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Klasifikasi*</label>
                            <div class="uk-form-controls">
                                <select class="uk-select uk-form-small" v-model="tindakan.klasifikasi" @change="klasifikasiChange" style="border:1px solid #cc0202;color:black;" disabled>
                                    <option></option>
                                    <option value="TINDAKAN_MEDIS">TINDAKAN MEDIS</option>
                                    <option value="BEDAH">TINDAKAN OPERASI</option>
                                    <option value="RADIOLOGI">RADIOLOGI</option>
                                    <option value="LABORATORIUM">LABORATORIUM</option>
                                    <option value="KAMAR">KAMAR</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" :disabled="isUpdate" v-model="tindakan.tindakan_nama" type="text" required style="color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="tindakan.deskripsi" type="text">
                            </div>
                        </div>
                        <div class="uk-width-1-2@m">
                            <search-list
                                :config = configSelect
                                :dataLists = groupBillingLists.data
                                label = "Kelompok Tagihan"
                                placeholder = "Kelompok Tagihan"
                                captionField = "kelompok_tagihan"
                                valueField = "kelompok_tagihan_id"
                                :detailItems = billingDesc
                                :value = "tindakan.kelompok_tagihan_id"
                                v-on:item-select = "billingSelected"
                            ></search-list>
                        </div>
                        
                        <div class="uk-width-1-2@m">
                            <search-list
                                :config = configSelect
                                :dataLists = groupVclaimLists.data
                                label = "Kelompok VKlaim"
                                placeholder = "Kelompok vklaim"
                                captionField = "vclaim_label"
                                valueField = "kelompok_vclaim_id"
                                :detailItems = vclaimDesc
                                :value = "tindakan.kelompok_vklaim_id"
                                v-on:item-select = "vklaimGroupSelected"
                            ></search-list>
                        </div>
                        
                        <div class="uk-width-1-1" style="height:0.5em;"> </div>
                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_hitung_admin"> Hitung Admin                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 1.6em; margin:0;">
                                    pemeriksaan masuk dalam biaya administrasi
                                </p> 
                            </div>
                        </div>
                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_kamar" :disabled="!isKamar"> 
                                    Harga Kamar                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    item pelayanan untuk harga kamar
                                </p> 
                            </div>
                        </div>
                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_lab_hasil" :disabled="!isLab" @change="isLabHasilChange"> 
                                    Hasil Laboratorium                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    pemeriksaan dengan hasil lab
                                </p>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_tampilkan_dokter"> 
                                    Tampil Dokter                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    munculkan nama dokter di tindakan
                                </p> 
                            </div>
                        </div>

                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_paket" :disabled="tindakan.is_lab_hasil"> Paket Pemeriksaan                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 1.6em; margin:0;">
                                    memiliki sub pemeriksaan / hasil lab
                                </p> 
                            </div>
                        </div>

                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_aktif" disabled> 
                                    Data Aktif
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    tindakan (pemeriksaan) aktif
                                </p> 
                            </div>
                        </div>
                        
                        <!-- <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_kamar"> 
                                    Harga Kamar                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    item pelayanan untuk harga kamar
                                </p> 
                            </div>
                        </div>
                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_lab_hasil"> 
                                    Hasil Laboratorium                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    pemeriksaan dengan hasil lab
                                </p> 
                            </div>
                        </div>
                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_tampilkan_dokter"> 
                                    Tampil Dokter                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    munculkan nama dokter di tindakan
                                </p> 
                            </div>
                        </div>

                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_hitung_admin"> 
                                    Biaya Administrasi                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    tindakan (pemeriksaan) masuk biaya administrasi
                                </p> 
                            </div>
                        </div>

                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_paket" :disabled="isUpdate"> 
                                    Tindakan Paket                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    tindakan (pemeriksaan) paket
                                </p> 
                            </div>
                        </div>
                        
                        <div class="uk-width-1-3@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="tindakan.is_aktif"> 
                                    Data Aktif
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    tindakan (pemeriksaan) aktif
                                </p> 
                            </div>
                        </div> -->
                    </div>
                </div>
                <div v-if="isUpdate">
                    <ul uk-tab style="padding:0;margin:0;">
                        <li v-if="!tindakan.is_kamar">
                            <a href="#" style="font-size:14px;font-weight:500;">
                                <h5 style="padding:0;margin:0;">UNIT PELAYANAN</h5>
                            </a>
                        </li>
                        <li v-if="!tindakan.is_kamar">
                            <a href="#" style="font-size:14px;font-weight:500;">
                                <h5 style="padding:0;margin:0;">ITEM BHP</h5>
                            </a>
                        </li>         
                        <li v-if="tindakan.is_paket">
                            <a href="#" style="font-size:14px;font-weight:500;">
                                <h5 style="padding:0;margin:0;">SUB TINDAKAN PAKET</h5>
                            </a>
                        </li>                 
                        <li v-if="tindakan.klasifikasi == 'LABORATORIUM'">
                            <a href="#" style="font-size:14px;font-weight:500;">
                                <h5 style="padding:0;margin:0;">HASIL LABORATORIUM</h5>
                            </a>
                        </li>
                    </ul>
                    <ul class="uk-switcher" style="padding:0;margin:0;">
                        <li>
                            <sub-form-unit ref="subFormUnit" :tindakan="tindakan" v-on:updated="refreshDataTindakan"></sub-form-unit>
                        </li>
                        <li>
                            <sub-form-bhp ref="subFormBhp" :tindakan="tindakan" v-on:updated="refreshDataTindakan"></sub-form-bhp>
                        </li>                        
                        <li v-if="tindakan.is_paket">
                            <sub-form-tindakan ref="subFormTindakan" :tindakan="tindakan" v-on:updateSubTindakan="updateSubTindakan"></sub-form-tindakan>
                        </li>                        
                        <li>
                            <sub-form-item-lab ref="subFormItemLab" :tindakan="tindakan" v-on:updated="refreshDataTindakan"></sub-form-item-lab>
                        </li>
                    </ul>
                </div>
            </form>                          
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';
import SubFormUnit from '@/Pages/MasterData/Tindakan/components/SubFormUnit.vue';
import SubFormBhp from '@/Pages/MasterData/Tindakan/components/SubFormBhp.vue';
import SubFormItemLab from '@/Pages/MasterData/Tindakan/components/SubFormItemLab.vue';
import SubFormTindakan from '@/Pages/MasterData/Tindakan/components/SubFormTindakan.vue';

export default {
    name : 'form-tindakan', 
    components : { 
        SearchList,
        SubFormUnit,
        SubFormBhp,
        SubFormItemLab,
        SubFormTindakan,
    },
    data() {
        return {
            isUpdate : true,
            isLoading : false,
            isLab : false,
            isKamar : false,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            billingDesc : [
                { colName : 'kelompok_tagihan', labelData : '', isTitle : true },
                { colName : 'kelompok_tagihan_id', labelData : '', isTitle : false },
            ],
            actDesc : [
                { colName : 'kelompok_tindakan', labelData : '', isTitle : true },
                { colName : 'kelompok_tindakan_id', labelData : '', isTitle : false },
            ],
            vclaimDesc : [
                { colName : 'vclaim_label', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
                { colName : 'kelompok_vclaim_id', labelData : '', isTitle : false },
            ],

            tindakan : {
                client_id:null,
                tindakan_id:null,
                tindakan_nama : null,
                deskripsi : null,                
                klasifikasi : 'TINDAKAN MEDIS',
                meta_data : null,
                satuan : null,
                is_paket : false,
                is_diskon : true,
                is_cito : true,
                is_hitung_admin : true,
                is_tampilkan_dokter : true,
                is_lab_hasil : false,
                is_kamar : false,
                rl_kode : null,
                kelompok_tagihan_id : null,
                kelompok_tindakan_id : null,
                kelompok_tindakan : null,
                kelompok_vklaim_id : null,
                is_aktif : true,
                unitTindakan : [],
                bhpTindakan : [],
                labItems :[],
                subTindakan : [],
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelompokTagihan',['groupBillingLists']),
        ...mapGetters('kelompokVclaim',['groupVclaimLists']),
        ...mapGetters('kelompokTindakan',['groupActLists']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('tindakan',['createTindakan','updateTindakan','dataTindakan']),
        ...mapActions('kelompokTagihan',['listKelompokTagihan']),
        ...mapActions('kelompokVclaim',['listKelompokVclaim']),
        ...mapActions('kelompokTindakan',['listKelompokTindakan']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            let params = { is_aktif:true, per_page:'ALL' };
            this.listKelompokTagihan(params).then((response) => {   
                this.listKelompokTindakan(params).then((response) => {
                    this.listKelompokVclaim(params);
                });
            });
        },

        billingSelected(data){
            if(data) {
                this.tindakan.kelompok_tagihan_id = data.kelompok_tagihan_id;
            }
        },

        tindakanGroupSelected(data){
            if(data) {
                this.tindakan.kelompok_tindakan_id = data.kelompok_tindakan_id;
            }
        },

        vklaimGroupSelected(data){
            if(data) {
                this.tindakan.kelompok_vklaim_id = data.kelompok_vclaim_id;
            }
        },

        jenisChange(){
            this.pemeriksaan.is_kamar = false;
            this.pemeriksaan.is_paket = false;
            this.pemeriksaan.is_lab_hasil = false;
            this.isLab = false;
            this.isKamar = false;

            if(this.pemeriksaan.klasifikasi == 'KAMAR') {
                this.pemeriksaan.is_kamar = true;
                this.isKamar = true;
            }
            else if(this.pemeriksaan.klasifikasi == 'LABORATORIUM') {
                this.isLab = true;
                this.pemeriksaan.is_paket = false;
                this.pemeriksaan.is_lab_hasil = false;
            }
        },

        isLabHasilChange(){
            if(this.pemeriksaan.is_lab_hasil == true) {
                this.pemeriksaan.is_paket = false;
            }
        },

        klasifikasiChange(){
            this.tindakan.is_lab_hasil = false;
            this.tindakan.is_kamar = false;
            if(this.tindakan.klasifikasi == 'LAB') { this.tindakan.is_lab_hasil = true; }
            else if(this.tindakan.klasifikasi == 'KAMAR') { this.tindakan.is_kamar = true; }
        },

        closeModalTindakan(){    
            this.clearTindakan();        
            this.$emit('closed',true);
        },

        submitTindakan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createTindakan(this.tindakan).then((response) => {
                    if (response.success == true) {
                        this.isUpdate = true;
                        alert("Tindakan baru berhasil dibuat.");
                        this.fillTindakan(response.data);
                    }
                })
            }
            else {
                this.updateTindakan(this.tindakan).then((response) => {
                    if (response.success == true) {
                        this.isUpdate = true;
                        alert("Tindakan berhasil diubah.");
                        this.fillTindakan(response.data);
                        this.closeModalTindakan();
                    }
                })
            }            
        },

        newTindakan(){
            this.clearTindakan();
            this.isUpdate = false;
        },

        refreshTindakan(){
            let data = JSON.parse(JSON.stringify(this.tindakan));
            this.editTindakan(data.tindakan_id);
        },
        
        editTindakan(id){
            this.clearTindakan();
            this.isUpdate = true;
            this.retrieveTindakan(id);
        },

        retrieveTindakan(id){
            this.isLoading = true;
            this.dataTindakan(id).then((response)=>{
                if (response.success == true) {
                    this.fillTindakan(response.data);
                    this.isLoading = false;
                } else {
                    alert(response.message);
                    this.isLoading = false;
                }
            })
        },

        clearTindakan(){
            this.tindakan.client_id = null;
            this.tindakan.tindakan_id = null;
            this.tindakan.tindakan_nama = null;
            this.tindakan.klasifikasi = 'TINDAKAN_MEDIS';
            this.tindakan.deskripsi = null;
            this.tindakan.kelompok_tagihan_id = null;
            this.tindakan.kelompok_vklaim_id = null;
            this.tindakan.kelompok_tindakan_id = null;
            this.tindakan.is_diskon = true;
            this.tindakan.is_hitung_admin = true;
            this.tindakan.is_cito = false;
            this.tindakan.is_lab_hasil = false;
            this.tindakan.is_kamar = false;
            this.tindakan.is_paket = false;
            this.tindakan.is_tampilkan_dokter = false;
            this.tindakan.rl_kode = null;
            this.tindakan.meta_data = null;
            this.tindakan.satuan = null;
            this.tindakan.is_aktif = true;
            this.tindakan.unitTindakan = [];
            this.tindakan.bhpTindakan = [];
            this.tindakan.labItems = [];
            this.tindakan.subTindakan = [];
            
        },

        fillTindakan(data){
            this.tindakan.client_id = null;
            this.tindakan.tindakan_id = data.tindakan_id;
            this.tindakan.tindakan_nama = data.tindakan_nama;
            this.tindakan.klasifikasi = data.klasifikasi;
            this.tindakan.deskripsi = data.deskripsi;
            this.tindakan.kelompok_tagihan_id = data.kelompok_tagihan_id;
            this.tindakan.kelompok_vklaim_id = data.kelompok_vklaim_id;
            this.tindakan.kelompok_tindakan_id = data.kelompok_tindakan_id;
            this.tindakan.is_diskon = data.is_diskon;
            this.tindakan.is_hitung_admin = data.is_hitung_admin;
            this.tindakan.is_cito = data.is_cito;
            this.tindakan.is_paket = data.is_paket;
            this.tindakan.is_tampilkan_dokter = data.is_tampilkan_dokter;
            this.tindakan.is_lab_hasil = data.is_lab_hasil;
            this.tindakan.is_kamar = data.is_kamar;
            this.tindakan.rl_kode = data.rl_kode;
            this.tindakan.meta_data = data.meta_data;
            this.tindakan.satuan = data.satuan;
            this.tindakan.is_aktif = data.is_aktif;
            this.tindakan.unitTindakan = data.unitTindakan;
            this.tindakan.bhpTindakan = data.bhpTindakan;
            this.tindakan.labItems = data.labItems;
            this.tindakan.subTindakan = data.subTindakan;

            // if(this.$refs.subFormUnit) {
            //     this.$refs.subFormUnit.refreshData(this.tindakan);
            // }
            // if(this.$refs.subFormBhp) {
            //     this.$refs.subFormBhp.refreshData(this.tindakan);
            // }
            // if(this.$refs.subFormItemLab) {
            //     if(data.klasifikasi == 'LAB') { this.$refs.subFormItemLab.refreshData(this.tindakan);}
            // }
            // if(this.$refs.subFormTindakan) {
            //     if(data.is_paket) { this.$refs.subFormTindakan.refreshData(this.tindakan);}
            // }
        },

        refreshDataTindakan(){
            this.retrieveTindakan(this.tindakan.tindakan_id);
        },

        refreshDataSub(tipe) {
            if(tipe == 'lab') { if(this.tindakan.klasifikasi == 'LAB') { this.$refs.subFormItemLab.refreshData(this.tindakan);} }
            else if (tipe == 'unit') { this.$refs.subFormUnit.refreshData(this.tindakan); }
            else if (tipe == 'bhp') { this.$refs.subFormBhp.refreshData(this.tindakan); }
        },

        updateSubTindakan(data) {
            this.tindakan.subTindakan = data;
        }
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
    color:black;
}

.hims-form-container label {
    color:#333;
    font-size:12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border:2px solid #cc0202;
}

.uk-button {
    border:2px solid #aaa;
    font-weight:400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}
.hims-form-header {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    top:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-header h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}


.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */

</style>