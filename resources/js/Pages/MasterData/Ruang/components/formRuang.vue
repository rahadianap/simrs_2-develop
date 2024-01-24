<template>
    <div class="uk-container hims-form-container" style="background-color:#fff;padding:0 1em 1em 1em;">
        <form action="" @submit.prevent="submitRuang" >
            <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                    <h5 class="uk-text-uppercase">DATA RUANG DAN BED</h5>
                </div>                                
                <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                    <button type="button" @click.prevent="refreshData" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Refresh</button>                  
                </div>
                <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                    <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                </div>
            </div>
            <div> 
                <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                    <div uk-spinner="ratio : 2"></div>
                    <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                </div>
                         
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>              
                <div class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            data ruang unit pelayanan.
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                        
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Ruang*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="ruang.ruang_nama" type="text" placeholder="nama ruang" required>
                            </div>
                        </div>                        
                        
                        <div class="uk-width-1-2@m">
                            <search-select
                                :config = configSelect
                                searchUrl = "/units" 
                                label = "Unit"
                                placeholder = "unit pelayanan"
                                captionField = "unit_nama"
                                valueField = "unit_id"
                                :itemListData = unitDesc
                                :value = "ruang.unit_id"
                                v-on:item-select = "unitSelected"
                            ></search-select>
                        </div>                        
                        
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lokasi</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="ruang.lokasi" type="text" placeholder="lokasi ruang">
                            </div>
                        </div>

                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Kamar</label>
                            <div class="uk-form-controls">
                                <select v-model="ruang.kelas_kamar" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                    <option v-for="option in kelasKamar" v-bind:value="option.kelas_id">{{option.kelas_nama}}</option>
                                </select>
                            </div>
                        </div>    

                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Biaya Sewa Kamar</label>
                            <div class="uk-form-controls">
                                <select v-model="ruang.harga_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                    <option v-for="option in hargaKamarLists" v-bind:value="option.tindakan_id">{{option.tindakan_nama}}</option>
                                    <option></option>
                                </select>
                            </div>
                        </div>    
                        
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" rows="2" v-model="ruang.deskripsi" type="text" placeholder="deskripsi singkat">{{ruang.deskripsi}}</textarea>
                            </div>
                        </div> 

                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="ruang.is_aktif" style="margin-left:5px;"> Ruang rawat aktif.</label>
                        </div>
                    </div>
                </div>
                <div>
                    <ul uk-tab style="padding:0;margin:0;">
                        <li><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">PARAMETER RUANG</h5></a></li>
                        <li v-if="isUpdate"><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">BED RUANG</h5></a></li>
                    </ul>
                    <ul class="uk-switcher" style="padding:0;margin:0;">
                        <li>
                            <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">
                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="ruang.is_utama"> Ruang Utama
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Ruang utama pelayanan unit terkait</p>
                                </div>

                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="ruang.is_ruang_operasi"> Ruang Operasi
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Ruang rawat adalah ruang operasi</p>
                                </div>

                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="ruang.is_ruang_isolasi"> Ruang Isolasi
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Ruang rawat adalah ruang isolasi</p>
                                </div>
                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="ruang.is_ventilator"> Ventilator
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Ruang dilengkapi dengan ventilator</p>
                                </div>
                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="ruang.is_negative_pressure"> Negative Pressure
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Ruang isolasi negative pressure</p>
                                </div>

                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="ruang.is_pandemi"> Isolasi Pandemi
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Ruang isolasi pandemi</p>
                                </div>
                            </div>
                        </li>

                        <li v-if="isUpdate">
                            <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">
                                <div class="uk-width-1-1 uk-grid-small hims-table-filter-container" uk-grid style="margin:0;padding:0;"> 
                                    <form action="" @submit.prevent="submitBed" class="uk-grid uk-grid-small uk-width-1-1">
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Bed*</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="bed.no_bed" type="text" required placeholder="no pengenal bed">
                                            </div>
                                        </div>
                                        <div class="uk-width-1-4@m uk-form-controls">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">J.Kelamin Pasien*</label>
                                            <select v-model="bed.jenis_kelamin_bed" class="uk-select uk-form-small">
                                                <option value="L/P">SEMUA</option>
                                                <option value="L">LAKI-LAKI</option>
                                                <option value="P">PEREMPUAN</option>
                                            </select>
                                        </div>

                                        <div class="uk-width-auto@m" style="padding:1.5em 1em 0 1em;margin:0;">
                                            <button type="submit" class="hims-ruang-btn" style="background-color: #cc0202;color:#fff;"> Simpan </button>
                                            <button type="button" class="hims-ruang-btn" style="background-color: #fefefe;color:#666;margin-left:5px;"> Batal </button>
                                        </div>      
                                    </form>
                                    <div class="uk-width-1-1" style="padding:0.2em 0.5em 0 0.8em;margin:0;">
                                        <p style="color:#333; font-size:12px; padding:0;margin:0; font-style: italic; ">*Penambahan dan pengurangan bed akan langsung disimpan.</p>
                                    </div>
                                              
                                </div>
                                <div class="uk-width-1-1">
                                    <div class="uk-card" style="padding:0.5em 0 1em 0;margin:0;min-height:200px;">
                                        <table class="uk-table uk-table-striped">
                                            <thead class="uk-card uk-card-default uk-box-shadow-medium" style="border-top:2px solid #cc0202;">
                                                <tr>
                                                    <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; width:180px;">ID</th>
                                                    <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; ">No.Bed</th>
                                                    <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; ">JK</th>
                                                    <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px;">No.Reg Pasien</th>                                                    
                                                    <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; width:120px;">Status</th>
                                                    <th style="padding:1em 0.5em 1em 0.5em; color:#000; font-weight:bold; font-size:14px; width:100px;text-align:center;">...</th>  
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="data in ruang.beds">
                                                    <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;font-weight:500;width:180px;">{{data.bed_id}}</td>
                                                    <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;">{{data.no_bed}}</td>
                                                    <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;">{{data.jns_kelamin_bed}}</td>
                                                    <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;">{{data.no_reg}}</td>
                                                    <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;color:#333;">
                                                        <span class="uk-label uk-label-success" v-if="data.is_tersedia" style="font-size:11px;color:#fff;">{{data.status}}</span>
                                                        <span class="uk-label uk-label-danger" v-else  style="font-size:11px;color:#fff;">{{data.status}}</span>
                                                    </td>
                                                    <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
                                                        <a href="#" uk-icon="icon:trash;" @click.prevent="removeBed(data)"></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'form-ruang', 
    components : { SearchSelect, },
    data() {
        return {
            isLoading : true,
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],
            isUpdate : true,
            ruang : {
                client_id:null,
                ruang_id:null,
                ruang_nama : null,
                unit_id : null,
                is_utama : false,
                kelas_kamar : null,
                kelas_harga : null,
                harga_id : null,
                lokasi : null,
                deskripsi : null,
                is_ruang_isolasi : false,
                is_ventilator : false,
                is_negative_pressure : false,
                is_ruang_operasi : false,
                is_pandemi : false,
                is_aktif : true,
                beds : null,
            },
            kelasKamar : null,
            kelasHarga : null,
            hargaKamarLists : null,
            bed:{
                bed_id : null,
                ruang_id : null,
                no_bed : null,
                jns_kelamin_bed : 'L/P',
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelas',['classLists']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('kelas',['listKelas']),
        ...mapActions('tindakan',['listTindakanByKlasifikasi']),
        ...mapActions('ruang',['dataRuang','createRuang','updateRuang']),
        ...mapActions('bed',['createBed','updateBed','deleteBed']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let param = {per_page:'ALL'};
            if(this.classLists.length < 1){
                this.listKelas(param).then((response)=>{
                    if (response.success == true) {
                        this.separateKelas(response.data.data);
                    } else {
                        alert(response.message);
                    }
                })
            }
            else {
                this.separateKelas(this.classLists.data);
            }

            this.getHargaKamarLists();
            
        },

        getHargaKamarLists() {
            this.hargaKamarLists = null;
            this.CLR_ERRORS();
            let param = {per_page:'ALL', 'klasifikasi':'KAMAR' };
            this.listTindakanByKlasifikasi(param).then((response)=>{
                if (response.success == true) {
                    console.log(response);
                    this.hargaKamarLists = response.data.data;
                    // console.log(this.hargaKamarLists);
                } else {
                    alert(response.message);
                }
            })
        },

        separateKelas(data){
            this.kelasHarga = data.filter(function (el) { return el.is_kelas_harga == true; });
            this.kelasKamar = data.filter(function (el) { return el.is_kelas_kamar == true; });
        },

        unitSelected(data) {
            if(data) {
                this.ruang.unit_id = data.unit_id;
            }
        },

        submitRuang(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createRuang(this.ruang).then((response) => {
                    if (response.success == true) {
                        alert("Data ruang baru berhasil dibuat.");
                        this.fillRuang(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.redirectRoute();
                    }
                })
            }
            else {
                this.updateRuang(this.ruang).then((response) => {
                    if (response.success == true) {
                        this.fillRuang(response.data);
                        alert("Data ruang berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.refreshData();
                    }
                })
            }            
        },

        redirectRoute(){
            this.$router.replace(`/master/ruang/${this.ruang.ruang_id}`);                    
        },

        newRuang(){
            this.clearRuang();
            this.isUpdate = false;
            this.isLoading = false;
        },

        refreshData(){
            this.editRuang(this.ruang.ruang_id);
        },

        editRuang(ruangId){
            if(ruangId == null || ruangId == '') {
                return false;
            }
            this.isLoading = true;
            this.dataRuang(ruangId).then((response)=>{
                if (response.success == true) {
                    this.fillRuang(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                    this.$router.back('-1');
                }
                this.isLoading = false;
            })
        },   

        clearRuang(){
            this.ruang.client_id = null;
            this.ruang.ruang_id = null;
            this.ruang.ruang_nama = null;
            this.ruang.unit_id = null;
            this.ruang.is_utama = false;
            this.ruang.kelas_harga = null;
            this.ruang.kelas_kamar = null;
            this.ruang.harga_id = null;
            this.ruang.lokasi = null;
            this.ruang.deskripsi = null;
            this.ruang.is_ruang_isolasi = false;
            this.ruang.is_ventilator = false;
            this.ruang.is_negative_pressure = false;
            this.ruang.is_ruang_operasi = false;
            this.ruang.is_pandemi = false;
                
            this.ruang.is_aktif = true;
            this.ruang.beds = null;
        },

        fillRuang(data){
            this.ruang.client_id = null;
            this.ruang.ruang_id = data.ruang_id;
            this.ruang.ruang_nama = data.ruang_nama;
            this.ruang.unit_id = data.unit_id;
            this.ruang.is_utama = data.is_utama;
            this.ruang.kelas_harga = data.kelas_harga;
            this.ruang.kelas_kamar = data.kelas_kamar;
            this.ruang.harga_id = data.harga_id;
            this.ruang.lokasi = data.lokasi;
            this.ruang.deskripsi = data.deskripsi;
            this.ruang.is_ruang_isolasi = data.is_ruang_isolasi;
            this.ruang.is_ventilator = data.is_ventilator;
            this.ruang.is_negative_pressure = data.is_negative_pressure;
            this.ruang.is_ruang_operasi = data.is_ruang_operasi;
            this.ruang.is_pandemi = data.is_pandemi;
            this.ruang.is_aktif = data.is_aktif;
            this.ruang.beds = data.beds;
        },

        clearBed(){
            this.bed.bed_id = null;
            this.bed.ruang_id = this.ruang.ruang_id;
            this.bed.no_bed = null;
            this.bed.jns_kelamin_bed = 'L/P';
        },

        submitBed(){
            this.CLR_ERRORS();
            this.bed.ruang_id = this.ruang.ruang_id;
            this.createBed(this.bed).then((response) => {
                if (response.success == true) {
                    alert("Data bed baru berhasil dibuat.");
                    this.clearBed();
                    this.ruang.beds = response.data;
                }
            })          
        },

        removeBed(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus bed no. ${data.no_bed}. Lanjutkan ?`)){
                this.deleteBed(data.bed_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.refreshData();
                    }
                    else { alert (response.message) }
                });
            };
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

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
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
}

.hims-ruang-btn {
    border:none;
    padding:0.5em 1em 0.5em 1em;
    min-width:20px;        
    border-radius: 5px;
    cursor : pointer;
} */
</style>