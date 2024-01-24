<template>
    <div class="uk-container" style="min-height:50vh; background-color:#fff;">
        <form action="" @submit.prevent="submitPasien" >
            <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                    <h5 class="uk-text-uppercase">DATA PASIEN</h5>
                </div>                                
                <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                    <button type="button" @click.prevent="closeFormPasien" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                            data utama rekam medis pasien.
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                        <div v-if="!isUpdate" class="uk-width-1-1@s uk-grid-small" uk-grid style="margin-bottom:1em;">
                            <div class="uk-width-1-2@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;font-weight: 500;">
                                        <input class="uk-radio" type="radio" value=false v-model="pasien.is_pasien_luar"> Pasien Internal
                                    </label>
                                </div>
                                <p style="font-size:11px; color:black; padding:0.2em 0.2em 0.2em 2em;margin:0;">Dicatat sebagai pasien rumah sakit dilengkapi catatan rekam medis.</p>
                            </div>

                            <div class="uk-width-1-2@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;font-weight: 500;">
                                        <input class="uk-radio" type="radio" value=true v-model="pasien.is_pasien_luar"> Pasien Luar
                                    </label>
                                </div>
                                <p style="font-size:11px; color:black; padding:0.2em 0.2em 0.2em 2em;margin:0;">Pasien rujukan dan tidak ada pencatatan rekam medis.</p>
                            </div>
                        </div>
                        <div v-else class="uk-width-1-1">
                            <p v-if="pasien.is_pasien_luar" style="color:black;font-weight:500;">PASIEN LUAR</p>
                            <p v-else style="color:black;font-weight:500;">PASIEN INTERNAL</p>                            
                        </div>
                        <div class="uk-width-1-3@s" >
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-medium" v-model="pasien.no_rm"  autocomplete="off" placeholder="" :disabled="isUpdate" :required="isUpdate">
                            </div>
                        </div>
                        <div class="uk-width-2-3@s">
                            <p style="font-style:italic;font-size:12px;margin:0;padding:0;">no rekam medis setelah disimpan tidak akan bisa lagi di ubah.</p>
                            <p style="font-style:italic;font-size:12px;margin:0;padding:0;">jika dikosongkan akan diisi otomatis oleh aplikasi.</p>
                        </div>                            
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.nama_pasien" type="text" placeholder="nama pasien" required>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Salut*</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.salut" class="uk-select uk-form-small" required>
                                    <option v-if="isRefExist" v-for="dt in salutRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin*</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.jns_kelamin" class="uk-select uk-form-small" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                    <option value="-">-</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="pasien.tempat_lahir" type="text" placeholder="tempat lahir" required>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.tgl_lahir" type="date" placeholder="tanggal lahir" required>
                            </div>
                        </div>

                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No.KTP / No. NIK*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.nik" type="text" placeholder="no kependudukan" required>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Kartu Keluarga</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.no_kk" type="text" placeholder="no kartu keluarga">
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Penjamin*</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.jns_penjamin" class="uk-select uk-form-small" required @change="ambilPenjaminList">
                                    <option v-if="isRefExist" v-for="dt in jenisPenjaminRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <search-select
                                :config = configSelect
                                :searchUrl = urlSelect.guarantor
                                label = "Penjamin / Asuransi"
                                placeholder = "pilih penjamin (asuransi)"
                                captionField = "penjamin_nama"
                                valueField = "penjamin_id"
                                :itemListData = penjaminDesc
                                :value = "pasien.penjamin_id"
                                v-on:item-select = "penjaminSelected"
                            ></search-select>
                        </div>

                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Kepesertaan</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.no_kepesertaan" type="text" placeholder="no kepesertaan asuransi">
                            </div>
                        </div>
                        <div v-if="isUpdate" class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:14px;color:black;"><input class="uk-checkbox" type="checkbox" v-model="pasien.is_aktif" style="margin-left:5px;"> Data pasien aktif</label>
                        </div>
                    </div>
                </div>

                <div class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA PERSONAL</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            data personal pasien dan kontak darurat
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Telepon</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.detail.no_telepon" type="text" placeholder="no telepon">
                            </div>
                        </div>
                        
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Golongan Darah</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.detail.gol_darah" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in golDarahRefs.json_data" :value="dt">{{dt}}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Rhesus</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.detail.rhesus" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in rhesusRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Email</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.detail.alamat_email" type="email" placeholder="alamat email">
                            </div>
                        </div>

                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kewarganegaraan</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.detail.kebangsaan" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in kebangsaanRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Suku/Etnis</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.detail.suku_bangsa" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in sukuBangsaRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Agama</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.detail.agama" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in agamaRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pendidikan</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.detail.pendidikan" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in pendidikanRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pekerjaan</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.detail.pekerjaan" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in pekerjaanRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No.Kontak Darurat</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.detail.no_kontak_darurat" type="text" placeholder="kontak kondisi darurat">
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Kontak Darurat</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="pasien.detail.nama_kontak_darurat" type="text" placeholder="nama kontak darurat">
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Hub. Kontak Darurat</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="pasien.detail.hub_kontak_darurat" type="text" placeholder="hubungan dengan pasien">
                            </div>
                        </div>
                    </div>
                </div>  

                <div v-if="!pasien.is_pasien_luar" class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">KELUARGA PASIEN</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            keluarga dan kontak darurat pasien
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                        
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status Perkawinan</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.keluarga.status_perkawinan" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in statusKawinRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasangan</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.keluarga.nama_pasangan" type="text" placeholder="nama suami/istri">
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pekerjaan Pasangan</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.keluarga.pekerjaan_pasangan" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in pekerjaanRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Ayah</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.keluarga.nama_ayah" type="text" placeholder="nama ayah">
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pekerjaan Ayah</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.keluarga.pekerjaan_ayah" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in pekerjaanRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Ibu</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="pasien.keluarga.nama_ibu" type="text" placeholder="nama ibu">
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pekerjaan Ibu</label>
                            <div class="uk-form-controls">
                                <select v-model="pasien.keluarga.pekerjaan_ibu" class="uk-select uk-form-small">
                                    <option v-if="isRefExist" v-for="dt in pekerjaanRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                </div>    
                <div v-if="!pasien.is_pasien_luar" class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">ALAMAT IDENTITAS (KTP)</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            alamat sesuai identitas pasien
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>   
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alamat</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="pasien.alamat.alamat" type="text" placeholder="alamat sesuai identitas"></textarea>
                            </div>
                        </div>   
                        <div class="uk-width-1-3@m uk-grid uk-grid-small" style="padding:15px 0 0 0;margin:0;">
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">RT</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.alamat.rt" type="text" placeholder="rt">
                                </div>
                            </div>        
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">RW</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.alamat.rw" type="text" placeholder="rw">
                                </div>
                            </div>
                        </div>
                        
                        <div class="uk-width-1-3@m">
                            <search-list
                                ref = "propSelect"
                                :config = configSelect
                                :dataLists = propinsi
                                label = "Propinsi"
                                placeholder = "propinsi sesuai identitas"
                                captionField = "propinsi"
                                valueField = "propinsi_id"
                                :detailItems = propDesc
                                :value = "pasien.alamat.propinsi_id"
                                v-on:item-select = "propSelected"
                            ></search-list>
                            
                            <!-- <search-select
                                ref = "kotaSelect"
                                :config = configSelect
                                :searchUrl = urlSelect.propinsi
                                label = "Propinsi"
                                placeholder = "propinsi sesuai identitas"
                                captionField = "propinsi"
                                valueField = "propinsi_id"
                                :itemListData = propDesc
                                :value = "pasien.alamat.propinsi_id"
                                v-on:item-select = "propSelected"
                            ></search-select> -->
                        </div>
                        <div class="uk-width-1-3@m">
                            <search-list
                                ref = "kotaSelect" 
                                :config = configSelect
                                :dataLists = kota
                                label = "Kabupaten/Kota"
                                placeholder = "kota sesuai identitas"
                                captionField = "kota"
                                valueField = "kota_id"
                                :detailItems = kabDesc
                                :value = "pasien.alamat.kota_id"
                                v-on:item-select = "kabSelected"
                            ></search-list>
                            <!-- <search-select
                                ref = "kotaSelect"
                                :config = configSelect
                                :searchUrl = urlSelect.kota
                                label = "Kabupaten/Kota"
                                placeholder = "kota sesuai identitas"
                                captionField = "kota"
                                valueField = "kota_id"
                                :itemListData = kabDesc
                                :value = "pasien.alamat.kota_id"
                                v-on:item-select = "kabSelected"
                            ></search-select> -->
                        </div>
                        <div class="uk-width-1-3@m">
                            <search-list
                                ref = "kecSelect" 
                                :config = configSelect
                                :dataLists = kecamatan
                                label = "Kecamatan"
                                placeholder = "kecamatan sesuai identitas"
                                captionField = "kecamatan"
                                valueField = "kecamatan_id"
                                :detailItems = kecDesc
                                :value = "pasien.alamat.kecamatan_id"
                                v-on:item-select = "kecSelected"
                            ></search-list>

                            <!-- <search-select
                                ref = "kecSelect" 
                                :config = configSelect
                                :searchUrl = urlSelect.kecamatan
                                label = "Kecamatan"
                                placeholder = "kecamatan sesuai identitas"
                                captionField = "kecamatan"
                                valueField = "kecamatan_id"
                                :itemListData = kecDesc
                                :value = "pasien.alamat.kecamatan_id"
                                v-on:item-select = "kecSelected"
                            ></search-select> -->
                        </div>
                        <div class="uk-width-1-3@m">
                            <search-list
                                ref = "kelSelect" 
                                :config = configSelect
                                :dataLists = kelurahan
                                label = "Kelurahan/Desa"
                                placeholder = "kelurahan sesuai identitas"
                                captionField = "kelurahan"
                                valueField = "kelurahan_id"
                                :detailItems = kelDesc
                                :value = "pasien.alamat.kelurahan_id"
                                v-on:item-select = "kelSelected"
                            ></search-list>

                            <!-- <search-select
                                ref = "kelSelect" 
                                :config = configSelect
                                :searchUrl = urlSelect.kelurahan
                                label = "Kelurahan/Desa"
                                placeholder = "kelurahan sesuai identitas"
                                captionField = "kelurahan"
                                valueField = "kelurahan_id"
                                :itemListData = kelDesc
                                :value = "pasien.alamat.kelurahan_id"
                                v-on:item-select = "kelSelected"
                            ></search-select> -->
                        </div>                        
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kodepos</label>
                            <div class="uk-inline uk-width-1-1">
                                <!-- <a class="uk-form-icon uk-form-icon-flip" href="#" uk-icon="icon:search" style="background-color:#cc0202;color:white;" @click.prevent="showKodeposPicker"></a> -->
                                <input v-model="pasien.alamat.kodepos" class="uk-input uk-form-small" type="text">
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:1em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px; color:black;">
                                <input class="uk-checkbox" type="checkbox" v-model="pasien.alamat.is_ktp_sama_dgn_tinggal" style="margin-left:5px;" @change="isAlamatKtpTinggalSama"> Alamat tinggal sama dengan KTP
                            </label>
                        </div>       
                    </div>
                </div> 
                <div v-if="!pasien.is_pasien_luar && !pasien.alamat.is_ktp_sama_dgn_tinggal" class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">ALAMAT TINGGAL</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            alamat tinggal pasien saat ini.
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>   
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alamat</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="pasien.alamat.alamat_tinggal" type="text" placeholder="alamat tinggal"
                                :disabled = "pasien.alamat.is_ktp_sama_dgn_tinggal"></textarea>
                            </div>
                        </div>   
                        <div class="uk-width-1-3@m uk-grid uk-grid-small" style="padding:15px 0 0 0;margin:0;">
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">RT</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.alamat.rt_tinggal" type="text" placeholder="rt" :disabled = "pasien.alamat.is_ktp_sama_dgn_tinggal">
                                </div>
                            </div>        
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">RW</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.alamat.rw_tinggal" type="text" placeholder="rw" :disabled = "pasien.alamat.is_ktp_sama_dgn_tinggal">
                                </div>
                            </div>
                        </div>
                        
                        <div class="uk-width-1-3@m">
                            <search-list
                                ref = "propTinggalSelect"
                                :config = configTinggalSelect
                                :dataLists = propinsiTinggal
                                label = "Propinsi"
                                placeholder = "propinsi tempat tinggal"
                                captionField = "propinsi"
                                valueField = "propinsi_id"
                                :detailItems = propDesc
                                :value = "pasien.alamat.propinsi_tinggal_id"
                                v-on:item-select = "propTinggalSelected"
                            ></search-list>
                        </div>
                        <div class="uk-width-1-3@m">
                            <search-list
                                ref = "kotaTinggalSelect" 
                                :config = configTinggalSelect
                                :dataLists = kotaTinggal
                                label = "Kabupaten/Kota"
                                placeholder = "kota tempat tinggal"
                                captionField = "kota"
                                valueField = "kota_id"
                                :detailItems = kabDesc
                                :value = "pasien.alamat.kota_tinggal_id"
                                v-on:item-select = "kabTinggalSelected"
                            ></search-list>
                        </div>
                        <div class="uk-width-1-3@m">
                            <search-list
                                ref = "kecTinggalSelect" 
                                :config = configTinggalSelect
                                :dataLists = kecamatanTinggal
                                label = "Kecamatan"
                                placeholder = "kecamatan tempat tinggal"
                                captionField = "kecamatan"
                                valueField = "kecamatan_id"
                                :detailItems = kecDesc
                                :value = "pasien.alamat.kecamatan_tinggal_id"
                                v-on:item-select = "kecTinggalSelected"
                            ></search-list>
                        </div>
                        <div class="uk-width-1-3@m">
                            <search-list
                                ref = "kelTinggalSelect" 
                                :config = configTinggalSelect
                                :dataLists = kelurahanTinggal
                                label = "Kelurahan/Desa"
                                placeholder = "kelurahan tempat tinggal"
                                captionField = "kelurahan"
                                valueField = "kelurahan_id"
                                :detailItems = kelDesc
                                :value = "pasien.alamat.kelurahan_tinggal_id"
                                v-on:item-select = "kelTinggalSelected"
                            ></search-list>
                        </div>            
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kodepos</label>
                            <div class="uk-inline uk-width-1-1">
                                <!-- <a class="uk-form-icon uk-form-icon-flip" href="#" uk-icon="icon:search" style="background-color:#cc0202;color:white;"></a> -->
                                <input v-model="pasien.alamat.kodepos_tinggal" class="uk-input uk-form-small" type="text" :disabled = "pasien.alamat.is_ktp_sama_dgn_tinggal">
                            </div>
                        </div>
                    </div>
                </div>    
                <div v-if="!pasien.is_pasien_luar" class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">ALERGI PASIEN</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            alergi yang dimiliki oleh pasien.
                        </p>
                    </div>
                    <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                        <table class="uk-table uk-table-striped">
                            <thead>
                                <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">Jenis Alergi</th>
                                <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">Catatan</th>                            
                                <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">tgl Awal</th>
                                <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;text-align:center;">...</th>
                            </thead>
                            <tbody>    
                                <tr>                            
                                    <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:100px;">
                                        <div class="uk-form-controls">
                                            <select v-model="alergiData.jns_alergi" class="uk-select uk-form-small">
                                                <option v-if="isRefExist" v-for="dt in jenisAlergiRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                            </select>
                                        </div>                        
                                    </td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:11px;">
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="alergiData.catatan_alergi">
                                        </div>                        
                                    </td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:60px;">
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="date" v-model="alergiData.tgl_kejadian_awal">
                                        </div>                        
                                    </td>
                                    <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                        <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addDataAlergi" style="padding:0;margin:0;color:blue;display:inline-block;"></a>
                                    </td>
                                </tr>

                                <tr v-for="dt in pasien.alergi" v-bind:class="dt.is_aktif != true ? 'inaktif':'' ">
                                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:80px;font-weight:500;">{{dt.jns_alergi}}</td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.catatan_alergi}}</td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.tgl_kejadian_awal}}</td>
                                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;">
                                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif">
                                    </td>   
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>                         
            </div>
        </form>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Pages/MasterData/Pasien/components/SearchList.vue';

export default {
    name : 'form-pasien', 
    props : {
        urlBack : { type:String, required:false, default:null }
    },
    components : {
        SearchSelect,
        SearchList,
    },
    data() {
        return {            
            //urlBack : null,
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : true,
            },

            configTinggalSelect : {
                disabled : false, 
                required : false,
                retrieveAll : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            propDesc : [
                { colName : 'propinsi', labelData : '', isTitle : true },
                { colName : 'propinsi_id', labelData : '', isTitle : false },
            ],
            
            kabDesc : [
                { colName : 'kota', labelData : '', isTitle : true },
                { colName : 'propinsi', labelData : 'prop. ', isTitle : false },
            ],
            kecDesc : [
                { colName : 'kecamatan', labelData : '', isTitle : true },
                { colName : 'kota', labelData : 'kab/kota. ', isTitle : false },
                { colName : 'propinsi', labelData : 'prop. ', isTitle : false },
            ],
            kelDesc : [
                { colName : 'kelurahan', labelData : '', isTitle : true },
                { colName : 'kodepos', labelData : 'kodepos. ', isTitle : false },
                { colName : 'kecamatan', labelData : 'kec. ', isTitle : false },
            ],

            penjaminDesc : [
                { colName : 'penjamin_nama', labelData : '', isTitle : true },
                { colName : 'penjamin_id', labelData : '', isTitle : false },
            ],
            isUpdate : true,

            pasien : {
                client_id:null,
                pasien_id:null,
                no_rm : null,
                is_pasien_luar : false,
                nama_pasien : null,
                salut : null,
                nama_lengkap : null,
                nik : null,
                no_kk : null,
                jns_kelamin : null,
                tgl_lahir : null,
                tempat_lahir : null,
                jns_penjamin : null,
                penjamin_id : null,
                no_kepesertaan : null,
                tgl_terakhir_periksa : null,
                is_hamil : false,
                is_meninggal : false,
                tgl_meninggal : null,
                penyebab_kematian : null,
                tgl_daftar : null,
                url_foto : null,
                is_aktif : true,
                detail : {
                    gol_darah : null,
                    rhesus : null,
                    pendidikan : null,
                    pekerjaan : null,
                    agama : null,
                    kebangsaan : null,
                    suku_bangsa : null,
                    no_telepon : null,
                    alamat_email : null,
                    no_kontak_darurat : null,
                    nama_kontak_darurat : null,
                    hub_kontak_darurat : null,
                    is_aktif : true,
                },
                alamat : {
                    alamat : null,
                    propinsi_id : null,
                    propinsi : null,
                    kota_id : null,
                    kota : null,
                    kecamatan_id : null,
                    kecamatan : null,
                    kelurahan_id : null,
                    kelurahan : null,
                    kodepos : null,
                    rt : null,
                    rw : null,
                    is_ktp_sama_dgn_tinggal : false,
                    alamat_tinggal : null,
                    propinsi_tinggal_id : null,
                    propinsi_tinggal : null,
                    kota_tinggal_id : null,
                    kota_tinggal : null,
                    kecamatan_tinggal_id : null,
                    kecamatan_tinggal : null,
                    kelurahan_tinggal_id : null,
                    kelurahan_tinggal : null,
                    kodepos_tinggal : null,
                    rt_tinggal : null,
                    rw_tinggal : null,
                    is_aktif : true,
                },
                keluarga : {
                    status_perkawinan : null,
                    nama_pasangan : null,
                    nama_ayah : null,
                    nama_ibu : null,
                    pekerjaan_pasangan : null,
                    pekerjaan_ayah : null,
                    pekerjaan_ibu : null,
                    is_aktif : true,
                },
                alergi : [],
            },

            propinsi : [],
            kota :[],
            kecamatan: [],
            kelurahan: [],

            propinsiTinggal : null,
            kotaTinggal : [],
            kecamatanTinggal : [],
            kelurahanTinggal : [],

            alergiData : {
                pasien_alergi_id : null,
                pasien_id : null,
                jns_alergi : null,
                tgl_kejadian_awal : null,
                akibat : null,
                catatan_alergi : null,
                is_aktif : true,
            },

            urlSelect : {
                guarantor : '/guarantors',
                // propinsi : '/provinces',
                // kota : '/cities',
                // kecamatan : '/counties',
                // kelurahan : '/districts',
                // propinsiTinggal : '/provinces',
                // kotaTinggal : '/cities',
                // kecamatanTinggal : '/counties',
                // kelurahanTinggal : '/districts',
            
                propinsi : '/provinces',
                kota : '',
                kecamatan : '',
                kelurahan : '',
                propinsiTinggal : '/provinces',
                kotaTinggal : '',
                kecamatanTinggal : '',
                kelurahanTinggal : '',
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('propinsi',['propinsiLists']),
        ...mapGetters('asuransi',['asuransiLists']),
        
        ...mapGetters('referensi',[
            'salutRefs',
            'rhesusRefs',
            'golDarahRefs',            
            'agamaRefs',
            'pekerjaanRefs',
            'pendidikanRefs',
            'jenisPenjaminRefs',
            'hubKeluargaRefs',
            'statusKawinRefs',
            'sukuBangsaRefs',
            'kebangsaanRefs',
            'caraPulangRefs',
            'statusPulangRefs',
            'asalPasienRefs',
            'jenisAlergiRefs',
            'statusBedInapRefs',
            'kasusIgdRefs',
            'jenisPelayananRefs',
            'isRefExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('pasien',['createPasien','updatePasien','dataPasien','uploadDokterAvatar']),
        ...mapActions('asuransi',['listAsuransi']),
        ...mapActions('propinsi',['listPropinsi']),
        ...mapActions('kabupaten',['listKabupaten']),
        ...mapActions('kecamatan',['listKecamatan']),
        ...mapActions('kelurahan',['listKelurahan']),
        
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let param = {per_page:'ALL'};
            this.listPropinsi(param).then((response) => {
                if (response.success == true) {
                    this.propinsiTinggal = JSON.parse(JSON.stringify(response.data.data)); 
                    this.propinsi = JSON.parse(JSON.stringify(response.data.data)); 
                    
                }
            });
            this.listAsuransi({per_page:20});

            if(!this.isRefExist) { 
                this.listReferensi(); 
            };
        },

        penjaminSelected(data) {
            this.pasien.penjamin_id = null;
            if(data) {
                this.pasien.penjamin_id = data.penjamin_id;
            }
        },

        ambilPenjaminList(){
            this.pasien.penjamin_id = null;
            this.listAsuransi({per_page:'ALL',jns_penjamin:this.pasien.jns_penjamin, is_aktif:true });
        },

        closeFormPasien(){
            let lastPasienData = JSON.parse(JSON.stringify(this.pasien));
            if(this.urlBack) {
                this.clearPasien();
                this.$router.push(this.urlBack);
            }
            else {
                this.clearPasien();
                this.$emit('formPatientClosed',lastPasienData);
            }
        },

        isAlamatKtpTinggalSama(){
            this.configTinggalSelect.disabled = this.pasien.alamat.is_ktp_sama_dgn_tinggal;
            if(this.pasien.alamat.is_ktp_sama_dgn_tinggal == true) {
                this.pasien.alamat.rt_tinggal = null;
                this.pasien.alamat.rw_tinggal = null;
                this.pasien.alamat.alamat_tinggal = null;
                this.pasien.alamat.propinsi_tinggal_id = null;
                this.pasien.alamat.kota_tinggal_id = null;
                this.pasien.alamat.kecamatan_tinggal_id = null;
                this.pasien.alamat.kelurahan_tinggal_id = null;
                this.pasien.alamat.kodepos_tinggal = null;                
            }
        },

        propSelected(data) {
            if(!this.isLoading){
                this.pasien.alamat.kota_id = null;
                this.pasien.alamat.kecamatan_id = null;
                this.pasien.alamat.kelurahan_id = null;
                this.pasien.alamat.kodepos = null;
                this.kecamatan = [];
                this.kelurahan = [];
                this.kota = [];  
                if(data){
                    this.pasien.alamat.propinsi_id = data.propinsi_id;
                    this.retrieveKabKtp(data.propinsi_id);
                }
            } 
        },

        retrieveKabKtp(propId) {
            let prm = {per_page:'ALL',propinsi: propId }; 
            this.listKabupaten(prm).then((response) => {
                if (response.success == true) {
                    this.kota = JSON.parse(JSON.stringify(response.data.data)); 
                }
            })
        },


        kabSelected(data) {            
            if(!this.isLoading){
                this.pasien.alamat.kecamatan_id = null;
                this.pasien.alamat.kelurahan_id = null;
                this.pasien.alamat.kodepos = null;
                this.kecamatan = [];
                this.kelurahan = [];

                if(data) {                 
                    this.pasien.alamat.kota_id = data.kota_id; 
                    this.retrieveKecKtp(data.kota_id);
                }
            }
        },

        retrieveKecKtp(kabId) {
            let prm = {per_page:'ALL',kota: kabId };
            this.listKecamatan(prm).then((response) => {
                if (response.success == true) {
                    this.kecamatan = JSON.parse(JSON.stringify(response.data.data)); 
                }
            })
        },

        kecSelected(data) {
            if(!this.isLoading){
                this.pasien.alamat.kelurahan_id = null;
                this.pasien.alamat.kodepos = null;
                this.kelurahan = [];
                    
                if(data) {                 
                    this.pasien.alamat.kecamatan_id = data.kecamatan_id;   
                    this.retrieveKelKtp(data.kecamatan_id);       
                }
            }
        },

        retrieveKelKtp(kecId) {
            let prm = {per_page:'ALL',kecamatan: kecId };
            this.listKelurahan(prm).then((response) => {
                if (response.success == true) {
                    this.kelurahan = JSON.parse(JSON.stringify(response.data.data)); 
                }
            })
        },

        kelSelected(data) {
            if(!this.isLoading){
                this.pasien.alamat.kelurahan_id = null;
                this.pasien.alamat.kodepos = null;
                if(data){ 
                    this.pasien.alamat.kelurahan_id = data.kelurahan_id; 
                    this.pasien.alamat.kelurahan = data.kelurahan; 
                    this.pasien.alamat.kodepos = data.kodepos;
                    if(this.pasien.alamat.is_ktp_sama_dgn_tinggal) {
                        this.pasien.alamat.kelurahan_tinggal_id = data.kelurahan_id;
                        this.pasien.alamat.kelurahan_tinggal = data.kelurahan;
                        this.pasien.alamat.kodepos_tinggal = data.kodepos;
                    }
                }
            }
        },

        propTinggalSelected(data) {  
            this.pasien.alamat.kota_tinggal_id = null;
            this.pasien.alamat.kecamatan_tinggal_id = null;
            this.pasien.alamat.kelurahan_tinggal_id = null;
            this.pasien.alamat.kodepos_tinggal = null;
            this.kecamatanTinggal = [];
            this.kelurahanTinggal = [];
            this.kotaTinggal = [];  
            if(data){
                this.pasien.alamat.propinsi_tinggal_id = data.propinsi_id;
                let prm = {per_page:'ALL',propinsi: data.propinsi_id };                            
                this.listKabupaten(prm).then((response) => {
                    if (response.success == true) {
                        // console.log(response.data);
                        this.kotaTinggal = JSON.parse(JSON.stringify(response.data.data)); 
                    }
                })
            }  
        },
        

        kabTinggalSelected(data) {
            this.pasien.alamat.kecamatan_tinggal_id = null;
            this.pasien.alamat.kelurahan_tinggal_id = null;
            this.pasien.alamat.kodepos_tinggal = null;
            this.kecamatanTinggal = [];
            this.kelurahanTinggal = [];

            if(data) {                 
                this.pasien.alamat.kota_tinggal_id = data.kota_id; 
                //this.urlSelect.kecamatanTinggal = `/counties?kota=${data.kota_id}`;
                let prm = {per_page:'ALL',kota: data.kota_id };
                this.listKecamatan(prm).then((response) => {
                    if (response.success == true) {
                        //console.log(response.data);
                        this.kecamatanTinggal = JSON.parse(JSON.stringify(response.data.data)); 
                    }
                })
            }
        },

        kecTinggalSelected(data) {
            this.pasien.alamat.kelurahan_tinggal_id = null;
            this.pasien.alamat.kodepos_tinggal = null;
            this.kelurahanTinggal = [];   
            if(data) {                 
                this.pasien.alamat.kecamatan_tinggal_id = data.kecamatan_id;   
                //this.urlSelect.kelurahanTinggal = `/districts?kecamatan=${data.kecamatan_id}`;
                let prm = {per_page:'ALL',kecamatan: data.kecamatan_id };
                this.listKelurahan(prm).then((response) => {
                    if (response.success == true) {
                        // console.log(response.data);
                        this.kelurahanTinggal = JSON.parse(JSON.stringify(response.data.data)); 
                    }
                })       
            }
        },

        kelTinggalSelected(data) {
            this.pasien.alamat.kelurahan_tinggal_id = null;
            this.pasien.alamat.kodepos_tinggal = null;
            if(data){ 
                this.pasien.alamat.kelurahan_tinggal_id = data.kelurahan_id; 
                this.pasien.alamat.kodepos_tinggal = data.kodepos;
            }
        },

        submitPasien(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createPasien(this.pasien).then((response) => {
                    if (response.success == true) {
                        alert("Data pasien baru berhasil dibuat.");
                        this.fillPasien(response.data);
                        this.$emit('saveSucceed',response.data);
                        this.isUpdate = true;
                        this.closeFormPasien();
                    }
                })
            }
            else {
                this.updatePasien(this.pasien).then((response) => {
                    if (response.success == true) {
                        this.fillPasien(response.data);
                        alert("Data pasien berhasil diubah.");
                        this.$emit('saveSucceed',response.data);
                        this.isUpdate = true;
                    }
                })
            }
        },

        redirectEditPasien(pasienId){
            this.$router.push(`/master/pasien/list/${pasienId}`);
        },

        setUrlSelect(){
            this.urlSelect.guarantor = '/guarantors';
            this.urlSelect.propinsi = '/provinces';
            this.urlSelect.kota = "/cities";
            this.urlSelect.kecamatan = '/counties';
            this.urlSelect.kelurahan = '/districts';
        },

        newPasien(){
            //this.setUrlSelect();
            this.clearPasien();
            this.isUpdate = false;
            this.isLoading = false;
        },

        editPasien(pasienId){
            //this.setUrlSelect();
            this.isLoading = true;
            this.dataPasien(pasienId).then((response)=>{
                if (response.success == true) {
                    this.fillPasien(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                    //this.$router.push('/master/pasien');
                }
                this.isLoading = false;
            })
        },   

        clearPasien(){
            this.pasien.client_id = null;
            this.pasien.pasien_id = null;
            this.pasien.no_rm = null;
            this.pasien.is_pasien_luar = false;
            this.pasien.nama_pasien = null;
            this.pasien.salut = null;
            this.pasien.nama_lengkap = null;
            this.pasien.nik = null;
            this.pasien.no_kk = null;
            this.pasien.jns_kelamin = null;
            this.pasien.tgl_lahir = null;
            this.pasien.tempat_lahir = null;
            this.pasien.jns_penjamin = null;
            this.pasien.penjamin_id = null;
            this.pasien.no_kepesertaan = null;
            this.pasien.tgl_terakhir_periksa = null;
            this.pasien.is_hamil = false;
            this.pasien.is_meninggal = false;
            this.pasien.tgl_meninggal = null;
            this.pasien.penyebab_kematian = null;
            this.pasien.tgl_daftar = null;
            this.pasien.url_foto = null;
            this.pasien.is_aktif = true;

            this.pasien.detail.gol_darah = null;
            this.pasien.detail.rhesus = null;
            this.pasien.detail.pendidikan = null;
            this.pasien.detail.pekerjaan = null;
            this.pasien.detail.agama = null;
            this.pasien.detail.kebangsaan = null;
            this.pasien.detail.suku_bangsa = null;
            this.pasien.detail.no_telepon = null;
            this.pasien.detail.alamat_email = null;
            this.pasien.detail.no_kontak_darurat = null;
            this.pasien.detail.nama_kontak_darurat = null;
            this.pasien.detail.hub_kontak_darurat = null;
            
            this.pasien.alamat.alamat = null;
            this.pasien.alamat.propinsi_id = null;
            this.pasien.alamat.kota_id = null;
            this.pasien.alamat.kecamatan_id = null;
            this.pasien.alamat.kelurahan_id = null;
            this.pasien.alamat.propinsi = null;
            this.pasien.alamat.kota = null;
            this.pasien.alamat.kecamatan = null;
            this.pasien.alamat.kelurahan = null;
            this.pasien.alamat.kodepos = null;
            this.pasien.alamat.rt = null;
            this.pasien.alamat.rw = null;
            
            this.pasien.alamat.is_ktp_sama_dgn_tinggal = false;
            this.pasien.alamat.alamat_tinggal = null;
            this.pasien.alamat.propinsi_tinggal_id = null;
            this.pasien.alamat.kota_tinggal_id = null;
            this.pasien.alamat.kecamatan_tinggal_id = null;
            this.pasien.alamat.kelurahan_tinggal_id = null;

            this.pasien.alamat.propinsi_tinggal = null;
            this.pasien.alamat.kota_tinggal = null;
            this.pasien.alamat.kecamatan_tinggal = null;
            this.pasien.alamat.kelurahan_tinggal = null;

            this.pasien.alamat.kodepos_tinggal = null;
            this.pasien.alamat.rt_tinggal = null;
            this.pasien.alamat.rw_tinggal = null;
            this.pasien.alamat.is_aktif = true;
            
            this.pasien.keluarga.status_perkawinan = null;
            this.pasien.keluarga.nama_pasangan = null;
            this.pasien.keluarga.nama_ayah = null;
            this.pasien.keluarga.nama_ibu = null;
            this.pasien.keluarga.pekerjaan_pasangan = null;
            this.pasien.keluarga.pekerjaan_ayah = null;
            this.pasien.keluarga.pekerjaan_ibu = null;
            
            this.pasien.alergi = [];
        },

        fillPasien(data) {
            this.pasien.client_id = null;
            this.pasien.pasien_id = data.pasien_id;
            this.pasien.no_rm = data.no_rm;
            this.pasien.is_pasien_luar = data.is_pasien_luar;
            this.pasien.nama_pasien = data.nama_pasien;
            this.pasien.salut = data.salut;
            this.pasien.nama_lengkap = data.nama_lengkap;
            this.pasien.nik = data.nik;
            this.pasien.no_kk = data.no_kk;
            this.pasien.jns_kelamin = data.jns_kelamin;
            this.pasien.tgl_lahir = data.tgl_lahir;
            this.pasien.tempat_lahir = data.tempat_lahir;
            this.pasien.jns_penjamin = data.jns_penjamin;
            this.pasien.penjamin_id = data.penjamin_id;
            this.pasien.no_kepesertaan = data.no_kepesertaan;
            this.pasien.tgl_terakhir_periksa = data.tgl_terakhir_periksa;
            this.pasien.is_hamil = data.is_hamil;
            this.pasien.is_meninggal = data.is_meninggal;
            this.pasien.tgl_meninggal = data.tgl_meninggal;
            this.pasien.penyebab_kematian = data.penyebab_kematian;
            this.pasien.tgl_daftar = data.tgl_daftar;
            this.pasien.url_foto = data.url_foto;
            this.pasien.is_aktif = data.is_aktif;
            this.pasien.alergi = data.alergi;

            if(data.detail) {
                this.pasien.detail.gol_darah = data.detail.gol_darah;
                this.pasien.detail.rhesus = data.detail.rhesus;
                this.pasien.detail.pendidikan = data.detail.pendidikan;
                this.pasien.detail.pekerjaan = data.detail.pekerjaan;
                this.pasien.detail.agama = data.detail.agama;
                this.pasien.detail.kebangsaan = data.detail.kebangsaan;
                this.pasien.detail.suku_bangsa = data.detail.suku_bangsa;
                this.pasien.detail.no_telepon = data.detail.no_telepon;
                this.pasien.detail.alamat_email = data.detail.alamat_email;
                this.pasien.detail.no_kontak_darurat = data.detail.no_kontak_darurat;
                this.pasien.detail.nama_kontak_darurat = data.detail.nama_kontak_darurat;
                this.pasien.detail.hub_kontak_darurat = data.detail.hub_kontak_darurat;
            }
            
            if(data.keluarga) {
                this.pasien.keluarga.status_perkawinan = data.keluarga.status_perkawinan;
                this.pasien.keluarga.nama_pasangan = data.keluarga.nama_pasangan;
                this.pasien.keluarga.nama_ayah = data.keluarga.nama_ayah;
                this.pasien.keluarga.nama_ibu = data.keluarga.nama_ibu;
                this.pasien.keluarga.pekerjaan_pasangan = data.keluarga.pekerjaan_pasangan;
                this.pasien.keluarga.pekerjaan_ayah = data.keluarga.pekerjaan_ayah;
                this.pasien.keluarga.pekerjaan_ibu = data.keluarga.pekerjaan_ibu;
            }
                        
            if(data.alamat){
                
                this.pasien.alamat.alamat = data.alamat.alamat;
                this.pasien.alamat.propinsi = data.alamat.propinsi;
                this.pasien.alamat.kota = data.alamat.kota;
                this.pasien.alamat.kecamatan = data.alamat.kecamatan;
                this.pasien.alamat.kelurahan = data.alamat.kelurahan;                
                this.pasien.alamat.kodepos = data.alamat.kodepos;
                this.pasien.alamat.rt = data.alamat.rt;
                this.pasien.alamat.rw = data.alamat.rw;

                this.pasien.alamat.alamat_tinggal = data.alamat.alamat_tinggal;
                this.pasien.alamat.kota_tinggal = data.alamat.kota_tinggal;
                this.pasien.alamat.kecamatan_tinggal = data.alamat.kecamatan_tinggal;
                this.pasien.alamat.kelurahan_tinggal = data.alamat.kelurahan_tinggal;
                this.pasien.alamat.kodepos_tinggal = data.alamat.kodepos_tinggal;
                this.pasien.alamat.rt_tinggal = data.alamat.rt_tinggal;
                this.pasien.alamat.rw_tinggal = data.alamat.rw_tinggal;
                
                
                this.pasien.alamat.propinsi_id = data.alamat.propinsi_id;
                this.pasien.alamat.propinsi_tinggal_id = data.alamat.propinsi_tinggal_id;
                
                if(data.alamat.propinsi_id) {
                    this.listKabupaten({ per_page:'ALL',propinsi:data.alamat.propinsi_id }).then((response) => {
                        if (response.success == true) {

                            this.kota = JSON.parse(JSON.stringify(response.data.data));
                            this.pasien.alamat.kota_id = data.alamat.kota_id;

                            if(data.alamat.is_ktp_sama_dgn_tinggal){
                                this.kotaTinggal = JSON.parse(JSON.stringify(response.data.data));
                                this.pasien.alamat.kota_tinggal_id = data.alamat.kota_tinggal_id;
                            }

                            this.listKecamatan({per_page:'ALL', kota: data.alamat.kota_id}).then((response) => {
                                this.kecamatan = JSON.parse(JSON.stringify(response.data.data));
                                this.pasien.alamat.kecamatan_id = data.alamat.kecamatan_id;

                                if(data.alamat.is_ktp_sama_dgn_tinggal){
                                    this.kecamatanTinggal = JSON.parse(JSON.stringify(response.data.data));
                                    this.pasien.alamat.kecamatan_tinggal_id = data.alamat.kecamatan_tinggal_id;
                                }

                                this.listKelurahan({per_page:'ALL', kecamatan: data.alamat.kecamatan_id}).then((response) => {
                                    this.kelurahan = JSON.parse(JSON.stringify(response.data.data));
                                    this.pasien.alamat.kelurahan_id = data.alamat.kelurahan_id;
                                    this.pasien.alamat.kodepos = data.alamat.kodepos;
                                    
                                    if(data.alamat.is_ktp_sama_dgn_tinggal){
                                        this.kelurahanTinggal = JSON.parse(JSON.stringify(response.data.data));
                                        this.pasien.alamat.kelurahan_tinggal_id = data.alamat.kelurahan_tinggal_id;
                                        this.pasien.alamat.kodepos_tinggal = data.alamat.kodepos_tinggal;
                                    }    
                                })
                            })
                        }
                    })
                }
                        
                
                this.pasien.alamat.is_ktp_sama_dgn_tinggal = data.alamat.is_ktp_sama_dgn_tinggal;
                if(!data.alamat.is_ktp_sama_dgn_tinggal) {
                    // this.pasien.alamat.kota_tinggal_id = data.alamat.kota_tinggal_id;
                    // this.pasien.alamat.kecamatan_tinggal_id = data.alamat.kecamatan_tinggal_id;
                    // this.pasien.alamat.kelurahan_tinggal_id = data.alamat.kelurahan_tinggal_id;
                    
                    // this.pasien.alamat.alamat_tinggal = data.alamat.alamat_tinggal;
                    // this.pasien.alamat.kota_tinggal = data.alamat.kota_tinggal;
                    // this.pasien.alamat.kecamatan_tinggal = data.alamat.kecamatan_tinggal;
                    // this.pasien.alamat.kelurahan_tinggal = data.alamat.kelurahan_tinggal;
                    // this.pasien.alamat.kodepos_tinggal = data.alamat.kodepos_tinggal;
                    // this.pasien.alamat.rt_tinggal = data.alamat.rt_tinggal;
                    // this.pasien.alamat.rw_tinggal = data.alamat.rw_tinggal;
                    
                    this.pasien.alamat.propinsi_tinggal_id = data.alamat.propinsi_tinggal_id;
                    this.pasien.alamat.propinsi_tinggal = data.alamat.propinsi_tinggal;
                    
                    if(data.alamat.propinsi_tinggal_id) {
                        this.listKabupaten({ per_page:'ALL',propinsi:data.alamat.propinsi_tinggal_id }).then((response) => {
                            if (response.success == true) {
                                this.kotaTinggal = JSON.parse(JSON.stringify(response.data.data));
                                this.pasien.alamat.kota_tinggal_id = data.alamat.kota_tinggal_id;

                                this.listKecamatan({per_page:'ALL', kota: data.alamat.kota_id}).then((response) => {
                                    this.kecamatanTinggal = JSON.parse(JSON.stringify(response.data.data));
                                    this.pasien.alamat.kecamatan_tinggal_id = data.alamat.kecamatan_tinggal_id;

                                    this.listKelurahan({per_page:'ALL', kecamatan: data.alamat.kecamatan_id}).then((response) => {
                                        this.kelurahanTinggal = JSON.parse(JSON.stringify(response.data.data));
                                        this.pasien.alamat.kelurahan_tinggal_id = data.alamat.kelurahan_tinggal_id;
                                        this.pasien.alamat.kodepos_tinggal = data.alamat.kodepos_tinggal;
                                    })
                                })
                            }
                        })
                    }
                }

                this.configTinggalSelect.disabled = data.alamat.is_ktp_sama_dgn_tinggal; 
            } 
        },



        addDataAlergi(){
            if(this.alergiData.jns_alergi == '' || this.alergiData.jns_alergi == null) {
                alert('jenis alergi tidak boleh kosong'); return false;
            }

            if(this.alergiData.catatan_alergi == '' || this.alergiData.catatan_alergi == null) {
                alert('catatan alergi tidak boleh kosong');  return false;
            }

            if(!this.pasien.alergi) { this.pasien.alergi = []; }            
            this.pasien.alergi.push(JSON.parse(JSON.stringify(this.alergiData)));

            this.alergiData.pasien_alergi_id = null;
            this.alergiData.jns_alergi = null;
            this.alergiData.pasien_id = null;
            this.alergiData.catatan_alergi = null;
            this.alergiData.tgl_kejadian_awal = null;
            this.alergiData.is_aktif = true;
            this.alergiData.akibat = null;
            
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

/* tr.inaktif td {
    text-decoration: line-through;
    font-style: italic;
} */

</style>