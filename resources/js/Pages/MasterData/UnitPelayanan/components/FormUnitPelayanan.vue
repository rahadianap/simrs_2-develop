<template>
    <div class="uk-container hims-form-container" style="background-color:#fff;">
        <form action="" @submit.prevent="submitUnitPelayanan" style="padding:0 1em 1em 1em;" >
            <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                    <h5 class="uk-text-uppercase">FORM UNIT PELAYANAN</h5>
                </div>                                
                <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                    <button type="button" @click.prevent="refreshData" class="uk-button-close uk-button uk-button-default uk-button-medium" style="font-size:12px;">Refresh</button>                  
                </div>
                <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                    <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                </div>
            </div>
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
                        informasi dan data terkait unit pelayanan.
                    </p>
                </div>
                <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                    <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="unit.unit_nama" type="text" placeholder="nama unit pelayanan" required>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Inisial</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="unit.inisial" type="text" placeholder="inisial">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Kepala Unit</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="unit.kepala_unit" type="text" placeholder="nama kepala unit">
                            </div>
                        </div>                        
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Group Unit</label>
                            <div class="uk-form-controls">
                                <select v-model="unit.group_unit" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                    <option v-if="jenisPelayananRefs" v-for="option in jenisPelayananRefs.json_data" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                </select>
                            </div>
                        </div>     
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Pendaftaran</label>
                            <div class="uk-form-controls">
                                <select v-model="unit.jenis_registrasi" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                    <option v-if="jenisRegistrasiRefs" v-for="option in jenisRegistrasiRefs.json_data" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                </select>
                            </div>
                        </div>     
                        <div class="uk-width-1-1@m" style="padding:0.2em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="unit.is_aktif" style="margin-left:15px;"> Unit pelayanan aktif.</label>
                        </div>                       
                    </div>

                    <div class="uk-width-1-4@m">
                        <div class="uk-width-1-1@m" style="padding-top:1em;">
                            <div v-if="unit.icon_url == null || unit.icon_url == ''" class="uk-width-1-1"  style="overflow:hidden;background-color:whitesmoke;height:auto;width:140px;margin:0;padding:0;">
                                <img id="avatarWrapper" src="@/Assets/profile_pic.png" alt="" uk-img @click.prevent="browseIcon">
                            </div>
                            <div v-else class="uk-width-1-1" style="overflow:hidden;background-color:whitesmoke;height:auto;width:140px;margin:0;padding:0;">
                                <img id="avatarWrapper" :data-src="unit.icon_url" alt="" uk-img @click.prevent="browseIcon">
                            </div>
                            <span style="font-size:10px; font-style:italic;">Klik gambar untuk mengubah.</span>
                            <div class="uk-width-1-1" style="margin:0;padding:0;">
                                <div uk-form-custom="target: true">
                                    <input type="file" ref="file" @change="selectedFileIcon" >
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div>
                <ul uk-tab style="padding:0;margin:0;">
                    <li><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">PARAMETER UNIT</h5></a></li>
                    <li v-if="isUpdate"><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">DOKTER UNIT</h5></a></li>
                    <li v-if="isUpdate"><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">DEPO UNIT</h5></a></li>                            
                    <li v-if="isUpdate"><a href="#" style="font-size:14px;font-weight:500;"><h5 style="padding:0;margin:0;">TINDAKAN UNIT</h5></a></li>
                </ul>
                <ul class="uk-switcher" style="padding:0;margin:0;">
                    <li>
                        <div class="uk-grid-small hims-form-subpage" uk-grid >                                    
                            <div class="uk-width-1-1@s uk-grid-small" uk-grid>
                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="unit.is_unit_kiosk" > Kiosk Pendaftaran
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">tampilkan unit di kiosk pendaftaran mandiri.</p>
                                </div>
                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="unit.is_unit_external" > Aplikasi eksternal
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">tampilkan unit di aplikasi lain (mobile app, bridging, dsb).</p>
                                </div>

                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="unit.is_registrasi" > Pendaftaran Pasien
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat melakukan pendaftaran pasien.</p>
                                </div>

                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="unit.is_rm_baru" > Pasien Baru
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat mendaftarkan pasien baru.</p>
                                </div>

                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="unit.is_unit_rujukan" > Unit Rujukan
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat menjadi rujukan unit lain.</p>
                                </div>

                                <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="unit.is_bedah" > Jadwal Operasi
                                        </label>
                                    </div>
                                    <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Masukkan di pilihan unit untuk jadwal operasi.</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li v-if="isUpdate">
                        <sub-form-dokter ref="subFormDokter" :unit="unit" :doctors="unit.dokter" v-on:updated="refreshData"></sub-form-dokter>
                    </li>
                    <li v-if="isUpdate">
                        <sub-form-depo ref="subFormDepo" :unit="unit" :depos="unit.depo" v-on:updated="refreshData"></sub-form-depo>
                    </li>
                    <li v-if="isUpdate">
                        <sub-form-tindakan ref="subFormTindakan" :unit="unit" v-on:updated="refreshData"></sub-form-tindakan>
                    </li>
                </ul>
            </div>
            
        </form>                          
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SubFormOtorisasi from '@/Pages/MasterData/UnitPelayanan/components/SubFormOtorisasi.vue';
import SubFormDokter from '@/Pages/MasterData/UnitPelayanan/components/SubFormDokter.vue';
import SubFormDepo from '@/Pages/MasterData/UnitPelayanan/components/SubFormDepo.vue';
import SubFormTindakan from '@/Pages/MasterData/UnitPelayanan/components/SubFormTindakan.vue';

export default {
    name : 'form-unit-pelayanan',
    components : { 
        SubFormOtorisasi,
        SubFormDokter,
        SubFormDepo,
        SubFormTindakan
    },
    data() {
        return {
            isLoading : true,
            isUpdate : true,
            jenisRegistrasi : [
                { value : 'RAJAL', text : 'RAWAT JALAN' },
                { value : 'IGD', text : 'GAWAT DARURAT' },
                { value : 'RANAP', text : 'RAWAT INAP' },
                { value : 'MCU', text : 'MEDICAL CHECK UP' },
                { value : 'LAB', text : 'LABORATORIUM' },
                { value : 'RAD', text : 'RADIOLOGI' },
                { value : 'PENJ', text : 'PENUNJANG LAIN' },
                { value : 'FARMASI', text : 'FARMASI' },
                { value : 'LAIN', text : 'LAIN-LAIN' },
            ],

            unit : {
                client_id:null,
                unit_id:null,
                unit_nama : null,
                inisial : null,
                kepala_unit : null,
                jenis_registrasi : null,                
                group_unit : null,
                lokasi : null,

                is_pilih_dokter : true,
                is_unit_kiosk : true,
                is_unit_external : true,
                is_registrasi : true,
                is_rm_baru : true,
                is_bedah : false,
                is_unit_rujukan : true,
                
                icon_dir : null,
                icon_url : null,
                is_aktif : true,                
                dokter : [],
                depo : [],
            },
            selectedFiles : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',['jenisPelayananRefs','jenisRegistrasiRefs']),        
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('unitPelayanan',['createUnitPelayanan','updateUnitPelayanan','dataUnitPelayanan','uploadUnitIcon']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            alert('disini');
            let params = { is_aktif:true, per_page:'ALL' };
            this.listReferensi();
        },

        updateOtorisasi(data){
            if(data) {
                let dt = JSON.parse(JSON.stringify(data));
                this.unit.is_pilih_dokter = dt.is_pilih_dokter;
                this.unit.is_unit_kiosk = dt.is_unit_kiosk;
                this.unit.is_unit_external = dt.is_unit_external;
                this.unit.is_registrasi = dt.is_registrasi;
                this.unit.is_rm_baru = dt.is_rm_baru;
                this.unit.is_bedah = dt.is_bedah;
                this.unit.is_unit_rujukan = dt.is_unit_rujukan;
            }
        },

        browseIcon() {
            this.$refs.file.click();
        },

        selectedFileIcon(){
            this.selectedFiles = this.$refs.file.files[0];
            this.CLR_ERRORS();
            let formData = new FormData();
            formData.append("image", this.selectedFiles);
            if(this.isUpdate == false) { formData.append("unit_id", ""); } 
            else { formData.append("unit_id", this.unit.unit_id); }

            this.uploadUnitIcon(formData).then((response)=>{
                if (response.success == true) {
                    this.unit.icon_url = response.data.path_url;                    
                } else {
                    alert(response.message)
                }
            })
        },

        closeModalUnitPelayanan(){
            this.CLR_ERRORS();
            this.$router.push('/master/unit/pelayanan');
        },

        submitUnitPelayanan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createUnitPelayanan(this.unit).then((response) => {
                    if (response.success == true) {
                        alert("Unit pelayanan baru berhasil dibuat.");
                        this.isUpdate = true;
                        this.fillUnitPelayanan(response.data);
                        this.editUnitPelayanan(response.data.unit_id);
                    }
                    else {
                        alert(response.message);
                        this.isLoading = false;
                    }
                })
            }
            else {
                this.updateUnitPelayanan(this.unit).then((response) => {
                    if (response.success == true) {
                        alert("Unit pelayanan berhasil diubah.");
                        this.fillUnitPelayanan(response.data);
                        this.isUpdate = true;
                    }
                    else {
                        alert(response.message);
                        this.isLoading = false;
                    }
                })
            }            
        },

        newUnitPelayanan(){
            this.clearUnitPelayanan();
            this.isUpdate = false;
            this.isLoading = false;
        },

        refreshData(){
            let id = this.unit.unit_id;
            this.editUnitPelayanan(id);
        },

        editUnitPelayanan(id){
            this.isLoading = true;
            this.clearUnitPelayanan();            
            this.dataUnitPelayanan(id).then((response)=>{
                if (response.success == true) {
                    this.fillUnitPelayanan(response.data);
                    this.isUpdate = true;
                    this.isLoading = false;
                } else {
                    alert(response.message);
                    this.isLoading = false;
                }
            })
        },   

        clearUnitPelayanan(){
            this.unit.client_id = null;
            this.unit.unit_id = null;
            this.unit.unit_nama = null;
            this.unit.inisial = null;
            this.unit.kepala_unit = null;
            this.unit.jenis_registrasi = null;
            this.unit.group_unit = null;
            this.unit.icon_dir = null;
            this.unit.icon_url = null;
            this.unit.is_aktif = true;
            
            this.unit.is_pilih_dokter = true;
            this.unit.is_unit_kiosk = true;
            this.unit.is_unit_external = true;
            this.unit.is_registrasi = true;
            this.unit.is_rm_baru = true;
            this.unit.is_bedah = false;
            this.unit.is_unit_rujukan = true;
            this.unit.dokter = [];
        },

        fillUnitPelayanan(data){
            this.unit.client_id = null;
            this.unit.unit_id = data.unit_id;
            this.unit.unit_nama = data.unit_nama;
            this.unit.inisial = data.inisial;
            this.unit.kepala_unit = data.kepala_unit;
            this.unit.jenis_registrasi = data.jenis_registrasi;
            this.unit.group_unit = data.group_unit;
            this.unit.icon_dir = data.icon_dir;
            this.unit.icon_url = data.icon_url;
            this.unit.is_aktif = data.is_aktif;

            this.unit.is_registrasi = data.is_registrasi;
            this.unit.is_rm_baru = data.is_rm_baru;
            this.unit.is_unit_rujukan = data.is_unit_rujukan;            
            this.unit.is_bedah = data.is_bedah;
            this.unit.is_pilih_dokter = data.is_pilih_dokter;
            this.unit.is_unit_external = data.is_unit_external;
            this.unit.is_unit_kiosk = data.is_unit_kiosk;

            this.unit.dokter = data.dokter;
            this.unit.depo = data.depo;
            this.$refs.subFormTindakan.refreshData(this.unit);
        },
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
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