<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formAsuransi" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitAsuransi" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA ASURANSI / PENJAMIN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeAsuransiModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div>                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small hims-form-subpage" uk-grid >
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    informasi utama terkait data asuransi.
                                </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                                <div class="uk-width-3-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" v-model="penjamin.penjamin_nama" type="text" placeholder="nama asuransi" required>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Inisial*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" v-model="penjamin.inisial" type="text" placeholder="inisial" required>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Penjamin*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="penjamin.jns_penjamin" class="uk-select uk-form-small" @change="jenisPenjaminChange">
                                            <option v-if="isRefExist" v-for="dt in jenisPenjaminRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NPWP*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="penjamin.npwp" type="text" placeholder="nomor pajak" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Narahubung*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" v-model="penjamin.narahubung" type="text" placeholder="contact person" required>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Telepon*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="penjamin.no_telp" type="text" placeholder="nomor telepon" required>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Email*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="penjamin.email" type="email" placeholder="alamat email" required>
                                    </div>
                                </div>                                

                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alamat</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" rows="2" v-model="penjamin.alamat" type="text" required placeholder="alamat asuransi"></textarea>
                                    </div>
                                </div>   
                                                                    
                                <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;font-weight: 400;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="penjamin.is_bpjs" > Asuransi BPJS
                                        </label>
                                        <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.4em; margin:0; font-style: italic;">asuransi adalah BPJS atau turunannya.</span>
                                    </div>
                                </div>

                                <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;font-weight: 400;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="penjamin.is_aktif" > Data Aktif
                                        </label>
                                        <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.4em; margin:0; font-style: italic;">data asuransi (penjamin) aktif.</span>                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-grid-small hims-form-subpage" uk-grid>
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">BIAYA ADMINISTRASI</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">biaya administrasi pasien</p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                                <div class="uk-width-1-1@m" style="padding:0.2em 1em 0 0.8em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:14px;font-weight:400;">Jenis Pelayanan yang ditanggung :</label>
                                </div>

                                <div class="uk-width-auto@m" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="penjamin.is_coverage_poli" style="margin-left:5px;"> Rawat Jalan dan IGD</label>
                                </div>
                                <div class="uk-width-auto@m" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="penjamin.is_coverage_inap" style="margin-left:5px;"> Rawat Inap</label>
                                </div>
                                <div class="uk-width-auto@m" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="penjamin.is_coverage_penunjang" style="margin-left:5px;"> Pemeriksaan Penunjang</label>
                                </div>
                                <div class="uk-width-1-1" style="padding:0;margin:0;"></div>

                                <div class="uk-width-1-2@m">
                                    <search-list
                                    :config = configKelompokSelect
                                    :dataLists = bukuHarga
                                    label = "Kelompok Harga*"
                                    placeholder = "pilih kelompok harga"
                                    captionField = "buku_nama"
                                    valueField = "buku_nama"
                                    :detailItems = bukuDesc
                                    :value = "penjamin.buku_nama"
                                    v-on:item-select = "bukuSelected"
                                ></search-list>
                                </div>
                                

                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Biaya Admin*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="penjamin.jenis_biaya_admin" class="uk-select uk-form-small" @change="jenisBiayaChange" required>
                                            <option value="TETAP">TETAP</option>
                                            <option value="PERSEN">PERSENTASE</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Administrasi</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="penjamin.nilai_admin" type="number" placeholder="nilai administrasi" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Maks. Administrasi (Rp.)</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="penjamin.nilai_maks_admin" type="number" placeholder="nilai maksimum admin" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Margin Resep Poli(%)</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="penjamin.margin_resep_poli" type="number" placeholder="margin resep rawat jalan" max="100" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Margin Resep Inap(%)</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="penjamin.margin_resep_inap" type="number" placeholder="margin resep inap" max="100" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Margin BHP(%)</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="penjamin.margin_bhp" type="number" placeholder="margin bhp" max="100" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-grid-small hims-form-subpage" uk-grid>
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">PEMETAAN AKUN</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">pemetaan akun (coa)</p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                                <div class="uk-width-1-1@m">
                                    <search-select
                                        :config = configSelect
                                        searchUrl = "/coa/accounts"
                                        label = "A/R Invoice"
                                        placeholder = "akun invoice"
                                        captionField = "text_coa"
                                        valueField = "text_coa"
                                        :itemListData = coaDesc
                                        :value = "penjamin.coa_invoice"
                                        v-on:item-select = "akunInvoiceSelected"
                                    ></search-select>
                                </div>

                                <div class="uk-width-1-1@m">
                                    <search-select
                                        :config = configSelect
                                        searchUrl = "/coa/accounts"
                                        label = "A/R Proses"
                                        placeholder = "akun proses"
                                        captionField = "text_coa"
                                        valueField = "text_coa"
                                        :itemListData = coaDesc
                                        :value = "penjamin.coa_proses"
                                        v-on:item-select = "akunProsesSelected"
                                    ></search-select>
                                </div>

                                <div class="uk-width-1-1@m">
                                    <search-select
                                        :config = configSelect
                                        searchUrl = "/coa/accounts"
                                        label = "Deposit"
                                        placeholder = "akun deposit"
                                        captionField = "text_coa"
                                        valueField = "text_coa"
                                        :itemListData = coaDesc
                                        :value = "penjamin.coa_deposit"
                                        v-on:item-select = "akunDepositSelected"
                                    ></search-select>
                                </div>

                                <!-- <div class="uk-width-1-1@m">
                                    <search-option
                                        compClass = "uk-width-1-1@m"
                                        compStyle = "padding:0;margin:0;"
                                        searchUrl = "/coa/accounts"
                                        label = "Deposit"
                                        placeholder = "test"
                                        captionField = "text_coa"
                                        valueField = "text_coa"
                                        :itemListData = coaDesc
                                        :value = "penjamin.coa_deposit"
                                        v-on:item-select = "akunDepositSelected"
                                    ></search-option>
                                </div> -->
                            </div>
                        </div>
                                
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchOption from '@/Components/SearchOption';

export default {
    name : 'form-asuransi', 
    components : { SearchList, SearchSelect,SearchOption},
            
    data() {
        return {
            testVal : null,
            configKelompokSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : false,
            },
            coaDesc : [
                { colName : 'text_coa', labelData : '', isTitle : true },
                { colName : 'nama_coa', labelData : '', isTitle : false },
                { colName : 'kode_coa', labelData : 'ID: ', isTitle : false },
            ],

            bukuDesc : [
                { colName : 'buku_nama', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
                { colName : 'buku_harga_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            penjamin : {
                client_id:null,
                penjamin_id:null,
                penjamin_nama : null,
                inisial : null,
                npwp : null,
                narahubung : null,
                no_telp : null,
                email : null,
                alamat : null,
                is_aktif : true,
                is_bpjs : false,

                is_coverage_poli : true,
                is_coverage_inap : true,
                is_coverage_penunjang : true,

                buku_harga_id : null,
                buku_nama : null,
                
                jns_penjamin : null,
                
                jenis_biaya_admin : null,

                margin_resep_poli : 0,
                margin_resep_inap : 0,
                margin_bhp : 0,
                is_fix_admin : false,
                nilai_admin : 0,
                nilai_maks_admin : 0,
                coa_invoice : null,
                coa_proses : null,
                coa_deposit : null,     
                coa_invoice_id : null,
                coa_proses_id : null,
                coa_deposit_id : null,            
            },
            bukuHarga : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',['jenisPenjaminRefs','isRefExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('asuransi',['createAsuransi','updateAsuransi','dataAsuransi']),
        ...mapActions('bukuHarga',['listBukuHarga']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
            this.getBukuHarga();
        },

        getBukuHarga(){
            if(!this.bukuHarga){
                this.listBukuHarga({'per_page':'ALL'}).then((response) => {
                    if (response.success == true) {
                        this.bukuHarga = response.data.data;
                    }
                })
            }
        },

        bukuSelected(data){
            this.penjamin.buku_harga_id = null;
            if(data) {
                this.penjamin.buku_harga_id = data.buku_harga_id;
                this.penjamin.buku_nama = data.buku_nama;
            }
        },

        closeAsuransiModal(){
            this.$emit('formAsuransiClosed',true);
            UIkit.modal('#formAsuransi').hide();
        },

        jenisPenjaminChange(){
            let dt = this.jenisPenjaminRefs.json_data.find(item => item.value === this.penjamin.jns_penjamin);
            console.log(dt);
            if(dt) { this.penjamin.is_bpjs = dt.is_bpjs; }
        },

        jenisBiayaChange(){

        },

        submitAsuransi(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createAsuransi(this.penjamin).then((response) => {
                    if (response.success == true) {
                        alert("Asuransi / penjamin baru berhasil dibuat.");
                        this.fillAsuransi(response.data);
                        this.isUpdate = true;
                        this.closeAsuransiModal();
                    }
                })
            }
            else {
                this.updateAsuransi(this.penjamin).then((response) => {
                    if (response.success == true) {
                        this.fillAsuransi(response.data);
                        alert("Asuransi / penjamin berhasil diubah.");
                        this.isUpdate = true;
                        this.closeAsuransiModal();
                    }
                })
            }            
        },

        newAsuransi(){
            this.clearAsuransi();
            this.testVal = '1.00.000';
            this.isUpdate = false;
            UIkit.modal('#formAsuransi').show();
        },

        editAsuransi(asuransiId){
            this.clearAsuransi();            
            this.dataAsuransi(asuransiId).then((response)=>{
                if (response.success == true) {
                    this.fillAsuransi(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formAsuransi').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearAsuransi(){
            this.testVal = null;
            this.penjamin.client_id = null;
            this.penjamin.penjamin_id = null;
            this.penjamin.penjamin_nama = null;
            this.penjamin.inisial = null;
            this.penjamin.npwp = null;
            this.penjamin.narahubung = null;
            this.penjamin.no_telp = null;
            this.penjamin.email = null;
            this.penjamin.alamat = null;
            this.penjamin.is_aktif = true;            
            this.penjamin.is_bpjs = false;
            this.penjamin.is_coverage_poli = true;
            this.penjamin.is_coverage_inap = true;
            this.penjamin.is_coverage_penunjang = true;
            this.penjamin.buku_harga_id = null;
            this.penjamin.buku_nama = null;

            this.penjamin.jns_penjamin = null;
            this.penjamin.jenis_biaya_admin = null;

            this.penjamin.margin_resep_poli = 0;
            this.penjamin.margin_resep_inap = 0;
            this.penjamin.margin_bhp = 0;
            this.penjamin.is_fix_admin = false;
            this.penjamin.nilai_admin = 0;
            this.penjamin.nilai_maks_admin = 0;
            
            this.penjamin.coa_invoice = null;
            this.penjamin.coa_invoice_id = null;
            this.penjamin.coa_proses = null;
            this.penjamin.coa_proses_id = null;
            this.penjamin.coa_deposit = null;
            this.penjamin.coa_deposit_id = null;

        },

        fillAsuransi(data){
            this.penjamin.client_id = null;
            this.penjamin.penjamin_id = data.penjamin_id;
            this.penjamin.penjamin_nama = data.penjamin_nama;
            this.penjamin.inisial = data.inisial;
            this.penjamin.npwp = data.npwp;
            this.penjamin.narahubung = data.narahubung;
            this.penjamin.no_telp = data.no_telp;
            this.penjamin.email = data.email;
            this.penjamin.alamat = data.alamat;
            this.penjamin.is_bpjs = data.is_bpjs;
            this.penjamin.is_aktif = data.is_aktif;
            this.penjamin.is_coverage_poli = data.is_coverage_poli;
            this.penjamin.is_coverage_inap = data.is_coverage_inap;
            this.penjamin.is_coverage_penunjang = data.is_coverage_penunjang;
            this.penjamin.buku_harga_id = data.buku_harga_id;
            this.penjamin.buku_nama = data.buku_harga;
            
            this.penjamin.jns_penjamin = data.jns_penjamin;

            this.penjamin.jenis_biaya_admin = data.jenis_biaya_admin;
            this.penjamin.margin_resep_poli = data.margin_resep_poli;
            this.penjamin.margin_resep_inap = data.margin_resep_inap;
            this.penjamin.margin_bhp = data.margin_bhp;
            this.penjamin.is_fix_admin = data.is_fix_admin;
            this.penjamin.nilai_admin = data.nilai_admin;
            this.penjamin.nilai_maks_admin = data.nilai_maks_admin;

            this.penjamin.coa_invoice = data.coa_invoice;
            this.penjamin.coa_invoice_id = data.coa_invoice_id;
            this.penjamin.coa_proses = data.coa_proses;
            this.penjamin.coa_proses_id = data.coa_proses_id;
            this.penjamin.coa_deposit = data.coa_deposit;
            this.penjamin.coa_deposit_id = data.coa_deposit_id;
        },

        akunDepositSelected(data) {
            if(data) {
                this.penjamin.coa_deposit = data.text_coa;
                this.penjamin.coa_deposit_id = data.coa_id;
            }
            else {
                this.penjamin.coa_deposit = null;
                this.penjamin.coa_deposit_id = null;
            }
        },

        akunInvoiceSelected(data) {
            if(data) {
                this.penjamin.coa_invoice = data.text_coa;
                this.penjamin.coa_invoice_id = data.coa_id;
            }
        },

        akunProsesSelected(data){
            if(data) {
                this.penjamin.coa_proses = data.text_coa;
                this.penjamin.coa_proses_id = data.coa_id;
            }
        }
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox, .uk-select {
    border: 1px solid #999;
    color: black;
}

.hims-form-container label {
    color:#000;
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