<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalNewTindakan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close @click.prevent=""></button>
            <div class="hims-form-container" style="padding:0; ">
                <form action="" @submit.prevent="submitTindakan">                        
                    <h3 class="uk-text-uppercase" style="font-weight:bold;">Item Pelayanan</h3>
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-1" style="border-top:1px solid #ccc;margin-top: 0.5em;margin-bottom: 0.5em;"></div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Pelayanan*</label>
                            <div class="uk-form-controls">
                                <select class="uk-select uk-form-small" v-model="pemeriksaan.klasifikasi" @change="jenisChange" style="border:1px solid #cc0202;color:black;">
                                    <option></option>
                                    <option value="TINDAKAN_MEDIS">TINDAKAN MEDIS</option>
                                    <option value="BEDAH">TINDAKAN OPERASI</option>
                                    <option value="LABORATORIUM">LABORATORIUM</option>
                                    <option value="RADIOLOGI">RADIOLOGI</option>
                                    <option value="KAMAR">KAMAR</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pemeriksaan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" type="text" v-model="pemeriksaan.tindakan_nama" style="color:black;" required>
                            </div>
                        </div>

                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="pemeriksaan.deskripsi" style="color:black;" required></textarea>
                            </div>
                        </div>

                        <div class="uk-width-1-2@m">
                            <search-list
                                :config = configSelect2
                                :dataLists = groupBillingLists.data
                                label = "Kelompok Tagihan"
                                placeholder = "Kelompok Tagihan"
                                captionField = "kelompok_tagihan"
                                valueField = "kelompok_tagihan_id"
                                :detailItems = billingDesc
                                :value = "pemeriksaan.kelompok_tagihan_id"
                                v-on:item-select = "billingSelected"
                            ></search-list>
                        </div>
                        
                        <div class="uk-width-1-2@m">
                            <search-list
                                :config = configSelect2
                                :dataLists = groupVclaimLists.data
                                label = "Kelompok VKlaim"
                                placeholder = "Kelompok vklaim"
                                captionField = "vclaim_label"
                                valueField = "kelompok_vclaim_id"
                                :detailItems = vclaimDesc
                                :value = "pemeriksaan.kelompok_vklaim_id"
                                v-on:item-select = "vklaimGroupSelected"
                            ></search-list>
                        </div>
                        <div class="uk-width-1-1" style="margin:0;padding:0.5em;"></div>
                        <div class="uk-width-1-2@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="pemeriksaan.is_hitung_admin"> Hitung Admin                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 1.6em; margin:0;">
                                    pemeriksaan masuk dalam biaya administrasi
                                </p> 
                            </div>
                        </div>
                        <div class="uk-width-1-2@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="pemeriksaan.is_kamar" :disabled="!isKamar"> 
                                    Harga Kamar                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    item pelayanan untuk harga kamar
                                </p> 
                            </div>
                        </div>
                        <div class="uk-width-1-2@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="pemeriksaan.is_lab_hasil" :disabled="!isLab" @change="isLabHasilChange"> 
                                    Hasil Laboratorium                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    pemeriksaan dengan hasil lab
                                </p>
                            </div>
                        </div>
                        <div class="uk-width-1-2@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="pemeriksaan.is_tampilkan_dokter"> 
                                    Tampil Dokter                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    munculkan nama dokter di tindakan
                                </p> 
                            </div>
                        </div>

                        <div class="uk-width-1-2@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="pemeriksaan.is_paket" :disabled="pemeriksaan.is_lab_hasil"> Paket Pemeriksaan                                    
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 1.6em; margin:0;">
                                    memiliki sub pemeriksaan / hasil lab
                                </p> 
                            </div>
                        </div>

                        <div class="uk-width-1-2@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight: 500;">
                                    <input class="uk-checkbox" type="checkbox" v-model="pemeriksaan.is_aktif" disabled> 
                                    Data Aktif
                                </label>
                                <p style="font-size:12px; font-weight: 200; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0;">
                                    tindakan (pemeriksaan) aktif
                                </p> 
                            </div>
                        </div>

                        <template v-if="pemeriksaan.is_lab_hasil">
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Klasifikasi Hasil Lab*</label>
                                <div class="uk-form-controls">
                                    <select required v-model="pemeriksaan.hasil.klasifikasi" class="uk-form-small uk-select" @change="klasifikasiSelected">
                                        <option v-for="dt in klasifikasiItemLabRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                    </select>
                                </div>
                            </div>                            
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Sub Hasil Lab</label>
                                <div class="uk-form-controls">
                                    <select v-model="pemeriksaan.hasil.sub_klasifikasi" class="uk-form-small uk-select">
                                        <option v-for="dt in subKlasifikasi"  :value="dt">{{dt}}</option>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Urut</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="text" v-model="pemeriksaan.hasil.no_urut" style="color:black;">
                                </div>
                            </div>
                        </template>  
                                                
                        <div class="uk-width-1-1" style="text-align:right;margin-top:1.5em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'modal-new-tindakan',
    components : { SearchList, },
    data() {
        return {
            isLab : false,
            isKamar : false,
            
            configSelect2 : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            billingDesc : [
                { colName : 'kelompok_tagihan', labelData : '', isTitle : true },
                { colName : 'kelompok_tagihan_id', labelData : '', isTitle : false },
            ],

            vclaimDesc : [
                { colName : 'vclaim_label', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
                { colName : 'kelompok_vclaim_id', labelData : '', isTitle : false },
            ],

            pemeriksaan : {
                tindakan_id : null,
                tindakan_nama : null,
                deskripsi : null,
                klasifikasi : null,
                kelompok_tindakan_id : null,
                kelompok_tagihan_id : null,
                kelompok_vklaim_id : null,
                meta_data : null,
                satuan : null,
                is_paket : false,
                is_diskon : false,
                is_cito : false,
                is_hitung_admin : false,
                is_tampilkan_dokter : false,
                is_kamar : false,
                is_lab_hasil : false,
                rl_kode : null,
                modality_id : null,
                is_aktif : true,
                hasil : {
                    lab_hasil_id : null,
                    klasifikasi : null,
                    is_header : false,
                    no_urut : null,
                    sub_klasifikasi : null,
                    kode_rl : null,
                    level : null,
                }                       
            },

            subKlasifikasi : null,            
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelompokTagihan',['groupBillingLists']),
        ...mapGetters('kelompokVclaim',['groupVclaimLists']),
        ...mapGetters('kelompokTindakan',['groupActLists']),
        ...mapGetters('refPenunjang',['groupLabNormalRefs','satuanLabNormalRefs','klasifikasiItemLabRefs']),
    },
    mounted() {
        this.initialize();
    },

    methods : {        
        ...mapActions('refPenunjang',['listRefPenunjang']),
        ...mapActions('tindakan',['createTindakan','updateTindakan','dataTindakan']),
        ...mapActions('tindakanLab',['createTindakanLab']),
        ...mapActions('kelompokTagihan',['listKelompokTagihan']),
        ...mapActions('kelompokVclaim',['listKelompokVclaim']),
        ...mapActions('kelompokTindakan',['listKelompokTindakan']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            let params = { is_aktif:true, per_page:'ALL' };
            this.listRefPenunjang(params);
        },

        modalNewPemeriksaanLabClosed(){
            this.clearPemeriksaan();
            this.$emit('modalNewTindakanClosed',true);
            UIkit.modal('#modalNewTindakan').hide();
        },

        showModal() { 
            this.clearPemeriksaan();
            UIkit.modal('#modalNewTindakan').show(); 
        },

        billingSelected(data){
            if(data) { this.pemeriksaan.kelompok_tagihan_id = data.kelompok_tagihan_id; }
        },
        
        vklaimGroupSelected(data){
            if(data) { this.pemeriksaan.kelompok_vklaim_id = data.kelompok_vclaim_id; }
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

        klasifikasiSelected(){
            let sub = this.klasifikasiItemLabRefs.json_data.find(item => item['value'] == this.pemeriksaan.hasil.klasifikasi);
            if(sub) { this.subKlasifikasi = sub.sub; }
        },

        clearPemeriksaan(){
            this.pemeriksaan.tindakan_id = null;
            this.pemeriksaan.tindakan_nama = null;
            this.pemeriksaan.deskripsi = null;
            this.pemeriksaan.klasifikasi = null;
            this.pemeriksaan.kelompok_tindakan_id = null;
            this.pemeriksaan.kelompok_tagihan_id = null;
            this.pemeriksaan.kelompok_vklaim_id = null;
            this.pemeriksaan.meta_data = null;
            this.pemeriksaan.satuan = null;
            this.pemeriksaan.is_paket = false;
            this.pemeriksaan.is_diskon = false;
            this.pemeriksaan.is_cito = false;
            this.pemeriksaan.is_hitung_admin = false;
            this.pemeriksaan.is_tampilkan_dokter = false;
            this.pemeriksaan.is_kamar = false;
            this.pemeriksaan.is_lab_hasil = false;
            this.pemeriksaan.rl_kode = null;
            this.pemeriksaan.modality_id = null;
            this.pemeriksaan.is_aktif = true;
            this.pemeriksaan.hasil.klasifikasi = null;
            this.pemeriksaan.hasil.sub_klasifikasi = null;
        },


        submitTindakan(){ 
            this.CLR_ERRORS();
            this.pemeriksaan.is_aktif = true;
            if(this.pemeriksaan.klasifikasi == 'LABORATORIUM') {
                if(confirm(`Simpan pemeriksaan laboratorium ini ?`)){
                    this.createTindakanLab(this.pemeriksaan).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.clearPemeriksaan();
                            UIkit.modal('#modalNewTindakan').hide();
                            this.$emit('addTindakanSuccess',response.data);
                        }
                        else {  alert (response.message);  }
                    });
                };
            }   

            else {
                this.createTindakan(this.pemeriksaan).then((response) => {
                    if (response.success == true) {
                        alert("Tindakan baru berhasil dibuat.");
                        this.clearPemeriksaan();
                        UIkit.modal('#modalNewTindakan').hide();
                        this.$emit('addTindakanSuccess',response.data);
                    }
                })
            } 
        }
    }
}
</script>