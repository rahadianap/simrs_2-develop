<template>
    <div class="uk-modal-full" uk-modal="bg-close:false;" id="formws" style="height:100vh; padding:0;margin:0;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container uk-container-small" style="min-height:100vh;">
                <form action="" @submit.prevent="submitWorkplace">
                    <div class="uk-grid-small uk-card uk-card-default" uk-grid style="padding:0;margin:0 0.5em 0 0.5em;position:sticky;top:0px;background-color:white;z-index:99;">
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase" style="font-weight:bold;">DATA TEMPAT KERJA</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div>                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small" uk-grid style="padding:1em;margin:0.5em;border-top:1px solid blue;">
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;">INFORMASI UTAMA</h5>
                                <p style="font-size:12px;font-style:italic;">
                                    informasi utama terkait tempat kerja, dan akan digunakan dalam header maupun cetakan.
                                </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                                    <div class="uk-width-2-3@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="workplace.client_nama" type="text" placeholder="nama tempat kerja" required>
                                        </div>
                                    </div>
                                    
                                    <div class="uk-width-1-3@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tipe</label>
                                        <select v-model="workplace.client_tipe" required class="uk-select uk-form-small">
                                            <option v-for="option in WsTipe" v-bind:value="option.value" v-bind:key="option.value">{{option.caption}}</option>
                                        </select>                    
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No.Perijinan</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="workplace.no_ijin" type="text" placeholder="no perijinan">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Perijinan</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="workplace.tgl_terbit_ijin" type="date" data-date-format="DD MMMM YYYY" placeholder="tanggal akhir kontrak" required>
                                        </div>
                                    </div>
                                    
                                    <div class="uk-width-1-1@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alamat</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="workplace.alamat" type="text" required placeholder="alamat tempat kerja"></textarea>
                                        </div>
                                    </div>
                                    <!-- <div class="uk-width-1-1@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kota</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="workplace.kota" type="text" required placeholder="kota lokasi tempat kerja">
                                        </div>
                                    </div> -->
                                    <div class="uk-width-1-2@m">
                                        <search-list
                                            ref = "propSelect"
                                            :config = configSelect
                                            :dataLists = propinsiLists.data
                                            label = "Propinsi"
                                            placeholder = "propinsi"
                                            captionField = "propinsi"
                                            valueField = "propinsi_id"
                                            :detailItems = propDesc
                                            :value = "workplace.propinsi_id"
                                            v-on:item-select = "propSelected"
                                        ></search-list>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <search-list
                                            ref = "kotaSelect" 
                                            :config = configSelect
                                            :dataLists = kota.data
                                            label = "Kabupaten/Kota"
                                            placeholder = "Kota"
                                            captionField = "kota"
                                            valueField = "kota_id"
                                            :detailItems = kotaDesc
                                            :value = "workplace.kota_id"
                                            v-on:item-select = "kotaSelected"
                                        ></search-list>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <search-list
                                            ref = "kecSelect" 
                                            :config = configSelect
                                            :dataLists = kecamatan.data
                                            label = "Kecamatan"
                                            placeholder = "Kecamatan"
                                            captionField = "kecamatan"
                                            valueField = "kecamatan_id"
                                            :detailItems = kecDesc
                                            :value = "workplace.kecamatan"
                                            v-on:item-select = "kecSelected"
                                        ></search-list>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <search-list
                                            ref = "kelSelect" 
                                            :config = configSelect
                                            :dataLists = kelurahan.data
                                            label = "Kelurahan/Desa"
                                            placeholder = "Kelurahan"
                                            captionField = "kelurahan"
                                            valueField = "kelurahan_id"
                                            :detailItems = kelDesc
                                            :value = "workplace.kelurahan"
                                            v-on:item-select = "kelSelected"
                                        ></search-list>
                                    </div>                        
                                    <div class="uk-width-1-1@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kodepos</label>
                                        <div class="uk-inline uk-width-1-1">
                                            <input v-model="workplace.kodepos" class="uk-input uk-form-small" type="text" readonly>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Telepon</label>
                                        <div class="uk-inline uk-width-1-1">
                                            <span class="uk-form-icon" uk-icon="icon: receiver" style="color:#333;"></span>
                                            <input class="uk-input uk-form-small" v-model="workplace.no_telepon" type="text" required placeholder="no telepon">
                                        </div>
                                    </div>      
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Whatsapp</label>
                                        <div class="uk-inline uk-width-1-1">
                                            <span class="uk-form-icon" uk-icon="icon: whatsapp" style="color:#333;"></span>
                                            <input class="uk-input uk-form-small" v-model="workplace.no_whatsapp" type="text" required placeholder="no whatsapp">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Email</label>
                                        <div class="uk-inline uk-width-1-1">
                                            <span class="uk-form-icon" uk-icon="icon: mail" style="color:#333;"></span>
                                            <input class="uk-input uk-form-small" v-model="workplace.email" type="email" placeholder="alamat email">
                                        </div>
                                    </div>                                
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Instagram</label>
                                        <div class="uk-inline uk-width-1-1">
                                            <span class="uk-form-icon" uk-icon="icon: instagram" style="color:#333;"></span>
                                            <input class="uk-input uk-form-small" v-model="workplace.link_instagram" type="text" placeholder="akun instagram">
                                        </div>
                                    </div>                                 
                                    
                                </div>                                           
                                <div class="uk-width-1-4@m" style="padding-top:1em;">
                                    <div>
                                        <div v-if="workplace.url_logo == null || workplace.url_logo == ''" class="uk-width-1-1"  style="overflow:hidden;background-color:whitesmoke;height:140px;width:140px;margin:0;padding:0;">
                                            <img id="avatarWrapper" src="@/Assets/noimage.png" alt="" uk-img @click.prevent="browseLogo">
                                        </div>
                                        <div v-else class="uk-width-1-1" :style=" {'background-image': `url(${workplace.url_logo})` }">
                                            <img id="avatarWrapper" :data-src="workplace.url_logo" alt="" uk-img @click.prevent="browseLogo">
                                        </div>
                                        <span style="font-size:10px; font-style:italic;">Klik gambar untuk mengubah.</span>
                                        <!-- <button type="button" @click.prevent="browseLogo" class="uk-button uk-button-default uk-button-small uk-width-1-1@m" style="width:140px;">Pilih Foto</button> -->
                                        <div class="uk-width-1-1" style="margin:0;padding:0;">
                                            <div uk-form-custom="target: true">
                                                <input type="file" ref="file" @input="selectedFileLogo" >
                                            </div>
                                        </div>
                                    </div>
                                </div>                                           
                                
                            </div>
                        </div>

                        <div class="uk-grid-small" uk-grid style="padding:1em;margin:0.5em; border-top:1px solid blue;">
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;">PROFILE DAN LOKASI</h5>
                                <p style="font-size:12px;font-style:italic;">biografi singkat dan lokasi </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Profil Singkat</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="workplace.deskripsi" type="text" required placeholder="deskripsi singkat terkait tempat kerja"></textarea>
                                    </div>
                                </div>                                
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kode embed google maps</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" @change="getSourceMap" v-model="workplace.loc_embed_code" type="text" placeholder="copy kode embed html google maps">
                                    </div>
                                </div>    
                                <div v-html="workplace.loc_embed_code" class="uk-hidden" style="scrollbar-width: thin; overflow: auto; height:400px;background-color: !!transparent;margin-left:15px;">
                                    
                                </div>    
                                <div class="uk-width-1-1@m" style="scrollbar-width: thin; overflow: auto; width:100%; height:450px;background-color: !!transparent;">
                                    <iframe :src="workplace.loc_embed_src" width="100%" height="445px"></iframe>
                                </div>
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
import SearchList from '@/Pages/MasterData/Pasien/components/SearchList.vue';

export default {
    name : 'form-workplace',
    components : {
        SearchList
    }, 
    data() {
        return {
            isUpdate : true,
            selectedFiles : null,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : false,
            },
            WsTipe :[
                {value:'RUMAH SAKIT', caption:'Rumah Sakit' },
                {value:'KLINIK', caption:'Klinik' },
                {value:'APOTEK', caption:'Apotek' },                
            ],
            workplace : {
                client_id:null,
                client_nama : null,
                client_tipe : null,
                deskripsi : '',
                propinsi_id : '',
                propinsi : '',
                kota_id : '',
                kota : '',
                kecamatan_id : '',
                kecamatan : '',
                kelurahan_id : '',
                kelurahan : '',
                kodepos : '',
                alamat : '',
                no_ijin : '',
                tgl_terbit_ijin:null,
                no_telepon : null,
                email: null,
                no_whatsapp:null,
                link_instagram : null,
                admin_id : null,
                admin_email : null,
                path_logo : null,
                url_logo : null,
                loc_embed_src : '',
                loc_embed_code : '',
                is_admin : false,
            },
            propDesc : [
                { colName : 'propinsi', labelData : '', isTitle : true },
                { colName : 'propinsi_id', labelData : '', isTitle : false },
            ],
            kotaDesc : [
                { colName : 'kota', labelData : '', isTitle : true },
                { colName : 'kota_id', labelData : '', isTitle : false },
            ],
            kecDesc : [
                { colName : 'kecamatan', labelData : '', isTitle : true },
                { colName : 'kecamatan_id', labelData : '', isTitle : false },
            ],
            kelDesc : [
                { colName : 'kelurahan', labelData : '', isTitle : true },
                { colName : 'kelurahan_id', labelData : '', isTitle : false },
            ],
            kota :[],
            kecamatan: [],
            kelurahan: [],
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('propinsi',['propinsiLists']),
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapActions(['createClient','updateClient','dataClient','uploadLogo',]),
        ...mapActions('propinsi',['listPropinsi']),
        ...mapActions('kabupaten',['listKabupaten']),
        ...mapActions('kecamatan',['listKecamatan']),
        ...mapActions('kelurahan',['listKelurahan']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let param = {per_page:'ALL'};
            this.listPropinsi(param);
        },

        propSelected(data) {
            this.workplace.kota_id = null;
            this.workplace.kecamatan_id = null;
            this.workplace.kelurahan_id = null;
            this.workplace.kodepos = null;
            this.kecamatan = [];
            this.kelurahan = [];
            this.kota = [];  
            if(data){
                this.workplace.propinsi_id = data.propinsi_id;
                this.workplace.propinsi = data.propinsi;
                
                let prm = {per_page:'ALL',propinsi: data.propinsi_id };                            
                this.listKabupaten(prm).then((response) => {
                    if (response.success == true) {
                        this.kota = JSON.parse(JSON.stringify(response.data)); 
                    }
                })
            }      
        },

        kotaSelected(data) {            
            this.workplace.kecamatan_id = null;
            this.workplace.kelurahan_id = null;
            this.workplace.kodepos = null;
            this.kecamatan = [];
            this.kelurahan = [];

            if(data) {                 
                this.workplace.kota_id = data.kota_id;
                this.workplace.kota = data.kota; 
                let prm = {per_page:'ALL',kota: data.kota_id };
                this.listKecamatan(prm).then((response) => {
                    if (response.success == true) {
                        this.kecamatan = JSON.parse(JSON.stringify(response.data)); 
                    }
                })
            }
        },

        kecSelected(data) {
            this.workplace.kelurahan_id = null;
            this.workplace.kodepos = null;
            this.kelurahan = [];
                
            if(data) {                 
                this.workplace.kecamatan_id = data.kecamatan_id;
                this.workplace.kecamatan = data.kecamatan;   

                let prm = {per_page:'ALL',kecamatan: data.kecamatan_id };
                this.listKelurahan(prm).then((response) => {
                    if (response.success == true) {
                        this.kelurahan = JSON.parse(JSON.stringify(response.data)); 
                    }
                })       
            }
        },

        kelSelected(data) {
            this.workplace.kelurahan_id = null;
            this.workplace.kodepos = null;
            if(data){ 
                this.workplace.kelurahan_id = data.kelurahan_id; 
                this.workplace.kelurahan = data.kelurahan; 
                this.workplace.kodepos = data.kodepos;
            }
        },

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formws').hide();
            return false;
        },

        submitWorkplace(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createClient(this.workplace).then((response) => {
                    if (response.success == true) {
                        alert("Tempat Kerja baru berhasil dibuat.");
                        this.isUpdate = true;
                    }
                })
            }
            else {
                this.updateClient(this.workplace).then((response) => {
                    if (response.success == true) {
                        alert("Tempat Kerja berhasil diubah.");
                        this.isUpdate = true;
                    }
                })
            }            
        },

        newWorkplace(){
            this.clearWorkplace();
            this.isUpdate = false;
            UIkit.modal('#formws').show();
        },

        updateWorkplace(clientId){
            this.clearWorkplace();            
            this.dataClient(clientId).then((response)=>{
                if (response.success == true) {
                    this.fillWorkplace(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formws').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        browseLogo() {
            this.$refs.file.click();
        },
        selectedFileLogo(){
            let input = this.$refs.file
            let file = input.files
            if(file && file[0]){
                let reader = new FileReader
                reader.onload = e => {
                    this.workplace.url_logo = e.target.result
                }
                reader.readAsDataURL(file[0])
                this.$emit('input', file[0])
            }
            // this.selectedFiles = this.$refs.file.files[0];
            // this.CLR_ERRORS();
            // let formData = new FormData();
            // formData.append("image", this.selectedFiles);
            // if(this.isUpdate == false) { formData.append("client_id", ""); } 
            // else { formData.append("client_id", this.workplace.client_id); }

            // this.uploadLogo(formData).then((response)=>{
            //     if (response.success == true) {
            //         this.workplace.path_logo = response.data.path;
            //         this.workplace.url_logo = response.data.path_url;                    
            //     } else {
            //         alert(response.message)
            //     }
            // })
        },

        getSourceMap(){
            let comp = document.getElementsByTagName('iframe')[0];
            let embedSource = comp.getAttribute("src");
            this.workplace.loc_embed_src = embedSource;
        },

        clearWorkplace(){
            this.workplace.client_id = null;
            this.workplace.client_nama = null;
            this.workplace.client_tipe = null;
            this.workplace.deskripsi = '';
            this.workplace.kota = '';
            this.workplace.alamat = '';
            this.workplace.no_ijin = '';
            this.workplace.tgl_terbit_ijin = null;
            this.workplace.no_telepon = null;
            this.workplace.email = null;
            this.workplace.no_whatsapp = null;
            this.workplace.link_instagram = null;
            this.workplace.admin_id = null;
            this.workplace.admin_email = null;
            this.workplace.path_logo = null;
            this.workplace.url_logo = null;
            this.workplace.loc_embed_src = '';
            this.workplace.loc_embed_code = '';
            this.selectedFiles = null;
        },

        fillWorkplace(data){
            console.log(data);
            this.workplace.client_id = data.client_id;
            this.workplace.client_nama = data.client_nama;
            this.workplace.client_tipe = data.client_tipe;
            this.workplace.deskripsi = data.deskripsi;
            this.workplace.propinsi = data.propinsi;
            this.workplace.kota = data.kota;
            this.workplace.kecamatan = data.kecamatan;
            this.workplace.kelurahan = data.kelurahan;
            this.workplace.kodepos = data.kodepos;
            this.workplace.alamat = data.alamat;
            this.workplace.no_ijin = data.no_ijin;
            this.workplace.tgl_terbit_ijin = data.tgl_terbit_ijin;
            this.workplace.no_telepon = data.no_telepon;
            this.workplace.email = data.email;
            this.workplace.no_whatsapp = data.no_whatsapp;
            this.workplace.link_instagram = data.link_instagram;
            this.workplace.admin_id = data.admin_id;
            this.workplace.admin_email = data.admin_email;
            this.workplace.path_logo = data.path_logo;
            this.workplace.url_logo = data.url_logo;
            this.workplace.loc_embed_src = data.loc_embed_src;
            this.workplace.loc_embed_code = data.loc_embed_code;
        }


    }
}
</script>

<style>
</style>