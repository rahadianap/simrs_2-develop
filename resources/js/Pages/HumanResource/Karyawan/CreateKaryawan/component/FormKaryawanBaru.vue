<template>
        <div class="uk-container-large" style="margin-top:1em;background-color:#fff;min-height:80vh; ">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitKaryawan">
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA KARYAWAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeKaryawan" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                                    informasi utama terkait data karyawan.
                                </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                                <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                                    <div class="uk-width-2-3@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Lengkap*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="karyawan.nama" type="text" placeholder="nama karyawan" required>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-3@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIP*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="karyawan.nip" type="text" placeholder="nomor induk pegawai" required>
                                        </div>
                                    </div>

                                    <div class="uk-width-2-3@m">
                                        <search-select
                                            :config = configSelect
                                            searchUrl = "/jabatan/master"
                                            label = "Jabatan*"
                                            placeholder = "jabatan"
                                            captionField = "jabatan_nama"
                                            valueField = "jabatan_id"
                                            :itemListData = jabatanDesc
                                            :value = "karyawan.jabatan_id"
                                            v-on:item-select = "jabatanSelected"
                                        ></search-select>
                                    </div> 

                                    <div class="uk-width-1-3@m">
                                        <search-select
                                            :config = configSelect
                                            searchUrl = "/units" 
                                            label = "Unit"
                                            placeholder = "unit pelayanan"
                                            captionField = "unit_nama"
                                            valueField = "unit_id"
                                            :itemListData = unitDesc
                                            :value = "karyawan.unit_id"
                                            v-on:item-select = "unitSelected"
                                        ></search-select>
                                    </div> 

                                    <div class="uk-width-2-3@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status Kerjasama</label>
                                        <div class="uk-form-controls">
                                            <select v-model="karyawan.status" class="uk-select uk-form-small">
                                                <option v-for="option in status" v-bind:value="option.id" v-bind:key="option.id">{{option.text}}</option>
                                            </select>
                                        </div>
                                    </div> 

                                    <div class="uk-width-1-3@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Mulai Bekerja*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="karyawan.tgl_masuk" type="date" placeholder="mulai bekerja" required>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                        <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="karyawan.is_aktif" style="margin-left:5px;"> Data Karyawan aktif</label>
                                    </div>
                                </div>
                            
                            <div class="uk-width-1-4@s">
                                <div class="uk-width-1-1@m" style="padding-top:1em;">
                                    <div>
                                        <div v-if="karyawan.url_foto == null || karyawan.url_foto == ''" class="uk-width-1-1"  style="overflow:hidden;background-color:whitesmoke;height:140px;width:140px;margin:0;padding:0;">
                                            <img id="avatarWrapper" src="@/Assets/profile_pic.png" alt="" uk-img @click.prevent="browseAvatar">
                                        </div>
                                        <div v-else class="uk-width-1-1" :style=" {'background-image': `url(${karyawan.url_foto})` }">
                                            <img id="avatarWrapper" :data-src="karyawan.url_foto" alt="" uk-img @click.prevent="browseAvatar">
                                        </div>
                                        <span style="font-size:10px; font-style:italic;">Klik gambar untuk mengubah.</span>
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
                        <div class="uk-grid-small hims-form-subpage" uk-grid >
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">INFORMASI TAMBAHAN</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    informasi tambahan terkait karyawan
                                </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="karyawan.nik" type="text" placeholder="nomor induk kependudukan" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Data Rekening*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="karyawan.rekening_gaji" type="text" placeholder="nama bank dan no. akun" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="karyawan.tempat_lahir" type="text" placeholder="tempat lahir" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="karyawan.tanggal_lahir" type="date" placeholder="tanggal lahir" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="karyawan.jns_kelamin" class="uk-select uk-form-small" required>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alamat*</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="karyawan.keahlian" type="text" placeholder="alamat sesuai identitas" required></textarea>
                                    </div>
                                </div> 

                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Telepon*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="karyawan.no_telepon" type="text" placeholder="no telepon" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Email*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="karyawan.email" type="email" placeholder="alamat email" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status Perkawinan*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="karyawan.status_perkawinan" class="uk-select uk-form-small" required>
                                            <option v-if="isRefExist" v-for="dt in statusKawinRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>    
            </div>
        </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'form-karyawan-baru',
    props: {
        karyawanId : {type:String, required:true },
    },
    components : {
        // SearchList,
        SearchSelect,
    },
    data() {
        return {
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            jabatanDesc : [
                { colName : 'jabatan_nama', labelData : '', isTitle : true },
                { colName : 'jabatan_id', labelData : '', isTitle : false },
            ],
            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            karyawan : {
                client_id : null,
                karyawan_id : null,
                nama : null,
                nip : null,
                jns_kelamin : null,
                tempat_lahir : null,
                tanggal_lahir : null,
                nik : null,
                no_telepon : null,
                email : null,
                tgl_masuk : null,
                status_perkawinan : null,
                jabatan_id : null,
                jabatan_nama : null,
                unit_id : null,
                unit_nama : null,
                url_foto : null,
                keahlian : null,
                status : null,
                rekening_gaji : null,
                is_aktif : true,
            },

            status : [
                { id:'Mitra', text:'Mitra' },
                { id:'Karyawan', text:'Karyawan' },
                { id:'Magang', text:'Magang' },
                { id:'Tidak Tahu', text:'Tidak Tahu' },
            ],

        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('jabatan',['jabatanLists']),
        ...mapGetters('unitPelayanan',['unitLists']),
        ...mapGetters('referensi',['pendidikanRefs','statusKawinRefs','isRefExist']),
    },

    mounted() {
        this.initialize();
    },

    methods : {
        ...mapActions('karyawan',['createKaryawan','updateKaryawan','dataKaryawan', 'uploadKaryawanFoto']),
        ...mapActions('jabatan',['listJabatan']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            let param = {per_page:'ALL'};
            if(this.jabatanLists == null || this.jabatanLists.length == 0){
                this.listJabatan(param);
            }
            if(this.unitLists == null || this.unitLists.length == 0) {
                this.listUnitPelayanan(param);
            }
            if(!this.isRefExist) { this.listReferensi(); }

            if(this.karyawanId !== 'baru'){
                this.isUpdate = true;
                this.editKaryawan(this.karyawanId);
            }
            else {
                this.isUpdate = false;
                this.isLoading = false;
                this.clearKaryawan();
            }
        },

        closeKaryawan(){
            this.CLR_ERRORS();
            this.$router.back('-1');
        },

        jabatanSelected(data) {
            if(data) {
                this.karyawan.jabatan_id = data.jabatan_id;
                this.karyawan.jabatan_nama = data.jabatan_nama;
            }
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
                    this.karyawan.url_foto = e.target.result
                }
                reader.readAsDataURL(file[0])
                this.$emit('input', file[0])
            }
        },

        unitSelected(data) {
            if(data) {
                this.karyawan.unit_id = data.unit_id;
                this.karyawan.unit_nama = data.unit_nama;
            }
        },

        submitKaryawan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createKaryawan(this.karyawan).then((response) => {
                    if (response.success == true) {
                        alert("Data karyawan baru berhasil dibuat.");
                        this.fillKaryawan(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeKaryawan();
                    }
                })
            }
            else {
                this.updateKaryawan(this.karyawan).then((response) => {
                    if (response.success == true) {
                        this.fillKaryawan(response.data);
                        alert("Data karyawan berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }            
        },

        redirectEditKaryawan(karyawanId){
            this.$router.push(`/karyawan/master/list/${karyawanId}`);
        },

        newKaryawan(){
            this.clearKaryawan();
            this.isUpdate = false;
            this.isLoading = false;
        },

        editKaryawan(karyawanId){
            this.isLoading = true;           
            this.dataKaryawan(karyawanId).then((response)=>{
                if (response.success == true) {
                    this.fillKaryawan(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                    this.$router.back('-1');
                }
                this.isLoading = false;
            })
        },   

        clearKaryawan(){
            this.karyawan.client_id = null;
            this.karyawan.karyawan_id = null;
            this.karyawan.nama = null;
            this.karyawan.nip = null;
            this.karyawan.jns_kelamin = null;
            this.karyawan.tempat_lahir = null;
            this.karyawan.tanggal_lahir = null;
            this.karyawan.nik = null;
            this.karyawan.no_telepon = null;
            this.karyawan.email = null;
            this.karyawan.tgl_masuk = null;
            this.karyawan.status_perkawinan = null;
            this.karyawan.jabatan_id = null;
            this.karyawan.jabatan_nama = null;
            this.karyawan.unit_id = null;
            this.karyawan.unit_nama = null;
            this.karyawan.url_foto = null;
            this.karyawan.keahlian = null;
            this.karyawan.status = null;
            this.karyawan.rekening_gaji = null;
            this.karyawan.is_aktif = null;
        },

        fillKaryawan(data){
            this.karyawan.client_id = null;
            this.karyawan.karyawan_id = data.karyawan_id;
            this.karyawan.nama = data.nama;
            this.karyawan.nip = data.nip;
            this.karyawan.jns_kelamin = data.jns_kelamin;
            this.karyawan.tempat_lahir = data.tempat_lahir;
            this.karyawan.tanggal_lahir = data.tanggal_lahir;
            this.karyawan.nik = data.nik;
            this.karyawan.no_telepon = data.no_telepon;
            this.karyawan.email = data.email;
            this.karyawan.tgl_masuk = data.tgl_masuk;
            this.karyawan.status_perkawinan = data.status_perkawinan;
            this.karyawan.jabatan_id = data.jabatan_id;
            this.karyawan.jabatan_nama = data.jabatan_nama;
            this.karyawan.unit_id = data.unit_id;
            this.karyawan.unit_nama = data.unit_nama;
            this.karyawan.url_foto = data.url_foto;
            this.karyawan.keahlian = data.keahlian;
            this.karyawan.status = data.status;
            this.karyawan.rekening_gaji = data.rekening_gaji;
            this.karyawan.is_aktif = data.is_aktif;
        },
    }
}
</script>