<template>
    <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
        <form action="" @submit.prevent="submitDokter" >
            <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                    <h5 class="uk-text-uppercase">DATA DOKTER DAN SPESIALIS</h5>
                </div>                                
                <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                    <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Selesai</button>                  
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
                            informasi utama dokter dan spesialis.
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                            <div class="uk-width-2-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="dokter.dokter_nama" type="text" placeholder="nama dokter lengkap dengan gelar" required>
                                </div>
                            </div>
                            
                            <div class="uk-width-1-3@m">
                                <search-list
                                    :config = configSelect
                                    :dataLists = spesialisasiLists.data
                                    label = "Spesialisasi"
                                    placeholder = "Spesialisasi"
                                    captionField = "spesialisasi_id"
                                    valueField = "spesialisasi_id"
                                    :detailItems = spesialisasiDesc
                                    :value = "dokter.spesialis_id"
                                    v-on:item-select = "spesialisSelected"
                                ></search-list>
                            </div>

                            <div class="uk-width-2-3@m">
                                <search-list
                                    :config = configSelect
                                    :dataLists = ksmLists.data
                                    label = "Kelompok Staf Medis"
                                    placeholder = "Kelompok Staf Medis"
                                    captionField = "smf_nama"
                                    valueField = "smf_id"
                                    :detailItems = smfDesc
                                    :value = "dokter.smf_id"
                                    v-on:item-select = "ksmSelected"
                                ></search-list>
                            </div>
                            
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status Kerjasama</label>
                                <div class="uk-form-controls">
                                    <select v-model="dokter.status_kerjasama" class="uk-select uk-form-small">
                                        <option v-for="option in status_kerjasama" v-bind:value="option.id" v-bind:key="option.id">{{option.text}}</option>
                                    </select>
                                </div>
                            </div> 
                            
                            <div class="uk-width-2-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. SIP</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="dokter.no_sip" type="text" placeholder="nomor sip" required>
                                </div>
                            </div>
                            
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl.Akhir SIP</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="dokter.tgl_akhir_sip" type="date">
                                </div>
                            </div>
                            <div class="uk-width-1-1@m">
                                <search-select
                                    :config = configSelect
                                    :searchUrl = memberUrl
                                    label = "Akun Pengguna"
                                    placeholder = "akun pengguna"
                                    captionField = "username"
                                    valueField = "user_id"
                                    :itemListData = memberDesc
                                    :value = "dokter.user_id"
                                    v-on:item-select = "userSelected"
                                ></search-select>
                            </div>

                            <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="dokter.is_aktif" style="margin-left:5px;"> Data Dokter aktif</label>
                            </div>
                        </div>
                        <div class="uk-width-1-4@s">
                            <div class="uk-width-1-1@m" style="padding-top:1em;">
                                <div>
                                    <div v-if="dokter.url_avatar == null || dokter.url_avatar == ''" class="uk-width-1-1"  style="overflow:hidden;background-color:whitesmoke;height:140px;width:140px;margin:0;padding:0;">
                                        <img id="avatarWrapper" src="@/Assets/profile_pic.png" alt="" uk-img @click.prevent="browseAvatar">
                                    </div>
                                    <div v-else class="uk-width-1-1" :style=" {'background-image': `url(${dokter.url_avatar})` }">
                                        <img id="avatarWrapper" :data-src="dokter.url_avatar" alt="" uk-img @click.prevent="browseAvatar">
                                    </div>
                                    <span style="font-size:10px; font-style:italic;">Klik gambar untuk mengubah.</span>
                                    <!-- <button type="button" @click.prevent="browseAvatar" class="uk-button uk-button-default uk-button-small uk-width-1-1@m" style="width:140px;">Pilih Foto</button> -->
                                    <div class="uk-width-1-1" style="margin:0;padding:0;">
                                        <div uk-form-custom="target: true">
                                            <input type="file" ref="file" @input="selectedFileAvatar" >
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>

                        
                    </div>
                </div>

                <div class="uk-grid-small hims-form-subpage" uk-grid>
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">INFO TAMBAHAN</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">informasi tambahan terkait dokter dan spesialis</p>
                    </div>
                    
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>     
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NPWP</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="dokter.npwp" type="text" placeholder="nomor pajak" required>
                            </div>
                        </div>    
                            
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Data rekening</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="dokter.rekening" type="text" placeholder="nama bank dan no.account" required>
                            </div>
                        </div> 
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="dokter.tempat_lahir" type="text" placeholder="tempat kelahiran" required>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl. Lahir</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="dokter.tgl_lahir" type="date">
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                            <div class="uk-form-controls">
                                <select v-model="dokter.jns_kelamin" class="uk-select uk-form-small">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                    <option value="-">-</option>
                                </select>
                            </div>
                        </div>          
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alamat</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" rows="2" v-model="dokter.alamat" type="text" placeholder="alamat tinggal"></textarea>
                            </div>
                        </div> 
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No.Telepon</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="dokter.no_telepon" type="text" required placeholder="no telepon/selular">
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Email</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="dokter.email" type="email" required placeholder = "Alamat email">
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pendidikan Akhir</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="dokter.pend_akhir" type="text" placeholder="pendidikan akhir">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Biografi Singkat</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" rows="2" v-model="dokter.biografi" type="text" required placeholder="biografi singkat"></textarea>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-dokter', 
    components : {
        SearchSelect,
        SearchList
    },
    data() {
        return {
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            spesialisasiDesc : [
                { colName : 'spesialisasi', labelData : '', isTitle : true },
                { colName : 'spesialisasi_id', labelData : '', isTitle : false },
            ],
            smfDesc : [
                { colName : 'smf_nama', labelData : '', isTitle : true },
                { colName : 'smf_id', labelData : '', isTitle : false },
            ],
            memberDesc : [
                { colName : 'username', labelData : '', isTitle : true },
                { colName : 'email', labelData : '', isTitle : false },
            ],
            isUpdate : true,
            selectedFiles : null,
            dokter : {
                client_id:null,
                dokter_id:null,
                dokter_nama : null,
                spesialis_id : null,
                spesialis : null,
                pend_akhir : null,
                smf_id : null,
                biografi : null,
                url_avatar : null,
                no_registrasi : null,
                no_sip : true,
                tgl_akhir_sip : null,
                status_kerjasama : null,
                npwp : null,
                rekening : null,
                tempat_lahir : null,
                tgl_lahir : null,
                no_telepon : null,
                email : null,
                jns_kelamin : null,
                alamat : null,
                user_id : null,
                status : null,
                is_aktif : true,
            },

            status_kerjasama : [
                { id:'Mitra', text:'Mitra' },
                { id:'Karyawan', text:'Karyawan' },
                { id:'Magang', text:'Magang' },
                { id:'Tidak Tahu', text:'Tidak Tahu' },
            ],
            memberUrl : '',
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('ksm',['ksmLists']),
        ...mapGetters('spesialisasi',['spesialisasiLists']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('dokter',['createDokter','updateDokter','dataDokter','uploadDokterAvatar']),
        ...mapActions('ksm',['listKsm']),
        ...mapActions('spesialisasi',['listSpesialisasi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let param = {per_page:'ALL'};
            if(this.ksmLists == null || this.ksmLists.length == 0){
                this.listKsm(param);
            }
            if(this.spesialisasiLists == null || this.spesialisasiLists.length == 0) {
                this.listSpesialisasi(param);
            }
            this.memberUrl = "/workplaces/members";
        },

        browseAvatar() {
            this.$refs.file.click();
        },

        selectedFileAvatar(){
            let input = this.$refs.file
            let file = input.files
            if(file && file[0]){
                let reader = new FileReader
                reader.onload = e => {
                    this.dokter.url_avatar = e.target.result
                }
                reader.readAsDataURL(file[0])
                this.$emit('input', file[0])
            }
            // this.selectedFiles = this.$refs.file.files[0];
            // this.CLR_ERRORS();
            // let formData = new FormData();
            // formData.append("image", this.selectedFiles);
            // if(this.isUpdate == false) { formData.append("dokter_id", ""); } 
            // else { formData.append("dokter_id", this.dokter.dokter_id); }

            // this.uploadDokterAvatar(formData).then((response)=>{
            //     if (response.success == true) {
            //         this.dokter.url_avatar = response.data.path_url;                    
            //     } else {
            //         alert(response.message)
            //     }
            // })
        },

        closeModal(){
            this.$router.back('-1');
        },

        spesialisSelected(data) {
            if(data) {
                this.dokter.spesialis_id = data.spesialisasi_id;
                this.dokter.spesialis = data.spesialisasi;
            }
        },

        userSelected(data) {
            if(data) {
                this.dokter.user_id = data.username;
            }
        },

        ksmSelected(data) {
            if(data) {
                this.dokter.smf_id = data.smf_id;
            }
        },

        submitDokter(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createDokter(this.dokter).then((response) => {
                    if (response.success == true) {
                        alert("Data dokter baru berhasil dibuat.");
                        this.fillDokter(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }
            else {
                this.updateDokter(this.dokter).then((response) => {
                    if (response.success == true) {
                        this.fillDokter(response.data);
                        alert("Data dokter berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }            
        },

        newDokter(){
            this.clearDokter();
            this.isUpdate = false;
            this.isLoading = false;
        },

        editDokter(dokterId){
            this.isLoading = true;
            this.dataDokter(dokterId).then((response)=>{
                if (response.success == true) {
                    this.fillDokter(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                    this.$router.back('-1');
                }
                this.isLoading = false;
            })
        },   

        clearDokter(){
            this.dokter.client_id = null;
            this.dokter.dokter_id = null;
            this.dokter.dokter_nama = null;
            this.dokter.spesialis_id = '';
            this.dokter.spesialis = '';
            this.dokter.pend_akhir = null;
            this.dokter.smf_id = '';
            this.dokter.biografi = null;
            this.dokter.url_avatar = null;
            this.dokter.no_registrasi = null;
            this.dokter.no_sip = null;
            this.dokter.tgl_akhir_sip = null;
            this.dokter.status_kerjasama = null;
            this.dokter.npwp = null;
            this.dokter.rekening = null;
            this.dokter.tempat_lahir = null;
            this.dokter.tgl_lahir = null;
            this.dokter.jns_kelamin = null;
            this.dokter.no_telepon = null;
            this.dokter.email = null;
            this.dokter.alamat = null;
            this.dokter.user_id = null;
            this.dokter.status = null;
            this.dokter.is_aktif = true;
        },

        fillDokter(data){
            this.dokter.client_id = null;
            this.dokter.dokter_id = data.dokter_id;
            this.dokter.dokter_nama = data.dokter_nama;
            this.dokter.spesialis_id = data.spesialis_id;
            this.dokter.spesialis = data.spesialisasi;
            this.dokter.pend_akhir = data.pend_akhir;
            this.dokter.smf_id = data.smf_id;
            this.dokter.biografi = data.biografi;
            this.dokter.url_avatar = data.url_avatar;
            this.dokter.no_registrasi = data.no_registrasi;
            this.dokter.no_sip = data.no_sip;
            this.dokter.tgl_akhir_sip = data.tgl_akhir_sip;
            this.dokter.status_kerjasama = data.status_kerjasama;
            this.dokter.npwp = data.npwp;
            this.dokter.rekening = data.rekening;
            this.dokter.tempat_lahir = data.tempat_lahir;
            this.dokter.tgl_lahir = data.tgl_lahir;
            this.dokter.jns_kelamin = data.jns_kelamin;
            this.dokter.no_telepon = data.no_telepon;
            this.dokter.email = data.email;
            this.dokter.alamat = data.alamat;
            this.dokter.user_id = data.user_id;
            this.dokter.status = data.status;
            this.dokter.is_aktif = data.is_aktif;
        },
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
} */

</style>