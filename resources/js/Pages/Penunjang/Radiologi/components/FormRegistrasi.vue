<template>
    <div class="uk-container hims-form-container" style="min-height:50vh;" id="formRegistrasi">
        <div>
            <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                <li>
                    <a href="#" @click.prevent="closeTab" class="uk-text-uppercase">                        
                        <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">
                            <span uk-icon="icon:arrow-left;ratio:1.5"></span> RADIOLOGI
                        </h4>
                    </a>                    
                </li>
                <li><span style="font-weight:400;color:#333;font-size:16px;" class="uk-text-uppercase">Daftar Pasien</span></li>           
            </ul>
        </div>
        <div class="uk-container uk hims-form-header" style="background-color:#fff;padding:1em;margin-top:1em;">
            <div class="uk-grid-small hims-form-subpage" uk-grid >
                <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                    
                </div>
            </div>  
            <ul uk-tab>
                <li><a href="#" style="font-size:14px;font-weight:500;">DETAIL INFO</a></li>
                <li><a href="#" style="font-size:14px;font-weight:500;">HASIL PEMERIKSAAN (bukan multi)</a></li>
                <li><a href="#" style="font-size:14px;font-weight:500;">HASIL PEMERIKSAAN (multi hasil)</a></li>
            </ul>
            <ul class="uk-switcher">
                <!-- Input Pemeriksaan Radiologi-->
                <li>
                    <form action="" @submit.prevent="submitRadiology">
                        <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                            <div class="uk-width-expand" style="padding:0.5em;margin:0;">
                                <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0;">
                                    <div class="uk-width-1-4@m" style="padding-top:0.2em; padding-right : 0.2em; max-width:200px;">
                                        <search-list ref="asalDataPasien"
                                            :config = configSelect
                                            :dataLists = asalDataLists
                                            placeholder = "asal data pasien"
                                            captionField = "caption"
                                            valueField = "value"
                                            :detailItems = asalDataPasienDesc
                                            :value = "radiology.asal_data_pasien"
                                            v-on:item-select = "asalDataPasienSelected"
                                        ></search-list>
                                    </div>
                                    <div style="padding:0;margin:0;">
                                        <div class="uk-button-group uk-box-shadow-medium" style="padding:0;margin:0;border-radius:5px;border:1px solid #efefef;">
                                            <button :disabled="radiology.pasien.is_pasien_luar" type="button" href="#" @click.prevent="searchPasien" class="hims-button-rm" style="border-radius: 5px; ">
                                                <span uk-icon="icon:search; ratio:0.8"></span> Cari
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>                                
                            <div class="uk-width-auto" style="text-align:right;padding:0.5em;">                        
                                <div class="uk-width-auto@m">
                                    <div class="uk-button-group uk-box-shadow-medium" style="padding:0;margin:0;border-radius:5px;border:1px solid #efefef;">
                                        <button type="button" href="#" @click.prevent="closeModal" class="hims-button-rm">
                                            <span uk-icon="icon:close; ratio:0.8"></span> Tutup
                                        </button>
                                        <button type="submit" href="#" class="hims-button-rm" style="border-top-right-radius: 5px; border-bottom-right-radius: 5px;">
                                            <span uk-icon="icon:file-text; ratio:0.8"></span> Simpan
                                        </button>
                                    </div>
                                </div>        
                            </div>
                        </div>
                        <div style="margin-top:15px;"> 
                            <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                                <div uk-spinner="ratio : 2"></div>
                                <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                            </div>                         
                            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                                <p>{{ errors.invalid }}</p>
                            </div>        
                            <div class="uk-grid-small hims-form-subpage" uk-grid >
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                        no rekam medis dan data pasien.
                                    </p>
                                </div>
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No RM</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="radiology.pasien.no_rm" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>                            
                                    
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small uk-width-expand" v-model="radiology.pasien.nama_pasien" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Salut</label>
                                        <div class="uk-form-controls">
                                            <select v-model="radiology.pasien.salut" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option v-if="isRefExist" v-for="dt in salutRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                        <div class="uk-form-controls">
                                            <select v-model="radiology.pasien.jns_kelamin" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option value="L">LAKI-LAKI</option>
                                                <option value="P">PEREMPUAN</option>
                                                <option value="-">-</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small uk-text-uppercase" v-model="radiology.pasien.tempat_lahir" type="text" style="color:black;">
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="radiology.pasien.tgl_lahir" type="date" style="color:black;">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="radiology.pasien.nik" type="text" style="color:black;">
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <div class="uk-grid-small hims-form-subpage" uk-grid>
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 0.2em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">DATA RADIOLOGI</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">pilih pelayanan dan spesialis.</p>
                                </div>                    
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">         
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Permintaan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="radiology.tgl_permintaan" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Periksa*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="radiology.tgl_periksa" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diagnosa</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.diagnosa">
                                        </div>
                                    </div>    
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Keterangan Klinis</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.ket_klinis">
                                        </div>
                                    </div>    
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Cito*</label>
                                        <div class="uk-form-controls">
                                            <search-list ref="jenisCito"
                                                :config = configSelect
                                                :dataLists = jenisCitoDataLists
                                                placeholder = "jenis cito"
                                                captionField = "caption"
                                                valueField = "value"
                                                :detailItems = jenisCitoDesc
                                                :value = "radiology.jenis_cito"
                                                v-on:item-select = "citoSelected"
                                            ></search-list>
                                        </div>
                                    </div> 
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Multi Hasil</label>
                                        <div class="uk-form-controls">
                                            <select v-model="radiology.is_multi_hasil" class="uk-select uk-form-small" style="border:1px solid dark;">
                                                <option value="false">Tidak</option>
                                                <option value="true">Ya</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <div class="uk-form-controls">
                                            <search-select
                                                :config = configSelect
                                                :searchUrl = dokterUrl
                                                label = "Dokter / Spesialis Pemeriksa"
                                                placeholder = "pilih dokter spesialis"
                                                captionField = "dokter_nama"
                                                valueField = "dokter_id"
                                                :itemListData = dokterDesc
                                                :value = "radiology.dokter_id"
                                                v-on:item-select = "dokterSelected"
                                            ></search-select>
                                        </div>
                                    </div>   
                                </div>
                            </div>                          
                        </div>
                        <div class="uk-grid-small hims-form-subpage" uk-grid>
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">PEMERIKSAAN DETAIL INFO
                                    <span style="font-size:12px;font-style:italic;padding:0;margin:0 0 0 0.5em;">item pemeriksaan pasien.</span>
                                </h5>                    
                            </div>         
                            <!-- PEMERIKSAAN -->
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;margin:0;">
                                <table class="uk-table hims-table">
                                    <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                                        <tr>
                                            <th style="text-align:center;width:140px;">ID</th>
                                            <th>Tindakan/Pemeriksaan</th>
                                            <th style="text-align:center;width:70px;">Jml</th>
                                            <th style="text-align:center;width:70px;">Satuan</th>
                                            <th style="text-align:center;">...</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="padding:0.2em;margin:0;">    
                                                <search-select
                                                    :config = configSelect
                                                    :searchUrl = tindakanUrl
                                                    placeholder = "tindakan pemeriksaan"
                                                    captionField = "tindakan_nama"
                                                    valueField = "tindakan_nama"
                                                    :itemListData = tindakanDesc
                                                    :value = "addedAct.tindakan_nama"
                                                    v-on:item-select = "itemActSelected"
                                                ></search-select>
                                            </td>
                                            <td style="text-align:center;width:70px;padding:0.2em;margin:0;">
                                                <input class="uk-input uk-form-small" type="number" v-model="addedAct.jumlah" max="100" min="0" style="text-align: center;">
                                            </td>
                                            <td style="text-align:center;width:70px;padding:0.2em;margin:0;">
                                                <input class="uk-input uk-form-small" type="text" v-model="addedAct.satuan" style="text-align: center;" disabled>
                                            </td>
                                            <td style="padding:0.5em; font-size:12px;text-align:center;">
                                                <a href="#" uk-icon="icon:plus-circle;ratio:1" @click.prevent="addActItem"></a>
                                            </td>                               
                                        </tr>
                                        <tr v-if="radiology.detail.length > 0" v-for="dt in radiology.detail">
                                            <td style="font-weight:500;">{{dt.tindakan_id}}</td>
                                            <td style="font-weight:500;">{{dt.tindakan_nama}}</td>
                                            <td style="text-align:center;">{{dt.jumlah}}</td>
                                            <td style="text-align:center;">{{dt.satuan}}</td>
                                            <td style="padding:1em 0.5em 1em 0.5em; text-align:center;">
                                                <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" style="text-align: center;" @change="addedItemAktifasi(dt)">
                                            </td>
                                        </tr>
                                        <tr v-else>
                                            <td colspan="5">
                                                <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>  
                </li>
                <!-- Hasil Radiologi Single -->
                <li>
                    <form action="" @submit.prevent="submitHasilRadiology">
                        <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                            <div class="uk-width-expand" style="padding:0.5em;margin:0;">
                                <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0;">
                                </div>
                            </div>      
                            <div class="uk-width-auto" style="text-align:right;padding:0.5em;">                        
                                <div class="uk-width-auto@m">
                                    <div class="uk-button-group uk-box-shadow-medium" style="padding:0;margin:0;border-radius:5px;border:1px solid #efefef;">
                                        <button type="button" href="#" @click.prevent="closeModal" class="hims-button-rm">
                                            <span uk-icon="icon:close; ratio:0.8"></span> Tutup
                                        </button>
                                        <button type="submit" href="#" class="hims-button-rm" style="border-top-right-radius: 5px; border-bottom-right-radius: 5px;">
                                            <span uk-icon="icon:file-text; ratio:0.8"></span> Simpan
                                        </button>
                                    </div>
                                </div>        
                            </div>
                        </div>
                        <div style="margin-top:15px;"> 
                            <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                                <div uk-spinner="ratio : 2"></div>
                                <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                            </div>                         
                            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                                <p>{{ errors.invalid }}</p>
                            </div>        
                            <div class="uk-grid-small hims-form-subpage" uk-grid >
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                        no rekam medis dan data pasien.
                                    </p>
                                </div>
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No RM</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="radiology.pasien.no_rm" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>                            
                                    
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small uk-width-expand" v-model="radiology.pasien.nama_pasien" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Salut</label>
                                        <div class="uk-form-controls">
                                            <select v-model="radiology.pasien.salut" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option v-if="isRefExist" v-for="dt in salutRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                        <div class="uk-form-controls">
                                            <select v-model="radiology.pasien.jns_kelamin" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option value="L">LAKI-LAKI</option>
                                                <option value="P">PEREMPUAN</option>
                                                <option value="-">-</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small uk-text-uppercase" v-model="radiology.pasien.tempat_lahir" type="text" style="color:black;">
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="radiology.pasien.tgl_lahir" type="date" style="color:black;">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="radiology.pasien.nik" type="text" style="color:black;">
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <div class="uk-grid-small hims-form-subpage" uk-grid>
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 0.2em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">HASIL RADIOLOGI</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">pilih pelayanan dan spesialis.</p>
                                </div>                    
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">         
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Hasil*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="radiology.tgl_hasil" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Diserahkan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="radiology.tgl_periksa" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diserahkan Oleh</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.diserahkan_oleh">
                                        </div>
                                    </div>   
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Media Hasil</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.media_hasil">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penerima Hasil</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.penerima_hasil">
                                        </div>
                                    </div>  
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Hubungan Penerima</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.hub_penerima">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Foto</label>
                                        <div class="uk-form-controls">
                                            <select v-model="radiology.jenis_foto" class="uk-select uk-form-small" style="color:black;">
                                                <option v-if="isRefExist" v-for="dt in jenisFotoRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            </select>
                                        </div>
                                    </div>   
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Foto</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.no_foto">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <div class="uk-form-controls">
                                            <search-select
                                                :config = configSelect
                                                :searchUrl = dokterUrl
                                                label = "Dokter / Spesialis Pemeriksa"
                                                placeholder = "pilih dokter spesialis"
                                                captionField = "dokter_nama"
                                                valueField = "dokter_id"
                                                :itemListData = dokterDesc
                                                :value = "radiology.dokter_id"
                                                v-on:item-select = "dokterSelected"
                                            ></search-select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-1@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Uraian Hasil</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="radiology.uraian_hasil" type="text" placeholder="uraian hasil"></textarea>
                                        </div>
                                    </div> 
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kesan</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="radiology.kesan" type="text" placeholder="kesan"></textarea>
                                        </div>
                                    </div> 
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="radiology.catatan" type="text" placeholder="catatan"></textarea>
                                        </div>
                                    </div> 
                                </div>
                            </div>                          
                        </div>
                        <div class="uk-grid-small hims-form-subpage" uk-grid>
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">PEMERIKSAAN DETAIL INFO
                                    <span style="font-size:12px;font-style:italic;padding:0;margin:0 0 0 0.5em;">item pemeriksaan pasien.</span>
                                </h5>                    
                            </div>         
                            <!-- PEMERIKSAAN -->
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;margin:0;">
                                <div style="margin-top:1em;" class="uk-responsive">
                                    <base-table ref="tableRadiologi"
                                        :columns = "tbColumns" 
                                        :config = "configTable" 
                                        :urlSearch="hasilTindakanUrl">
                                    </base-table>
                                </div>
                            </div>
                        </div>
                    </form>  
                </li>
                <!-- Hasil Radiologi Multiple -->
                <li>
                    <form action="" @submit.prevent="submitHasilRadiology">
                        <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                            <div class="uk-width-expand" style="padding:0.5em;margin:0;">
                                <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0;">
                                </div>
                            </div>      
                            <div class="uk-width-auto" style="text-align:right;padding:0.5em;">                        
                                <div class="uk-width-auto@m">
                                    <div class="uk-button-group uk-box-shadow-medium" style="padding:0;margin:0;border-radius:5px;border:1px solid #efefef;">
                                        <button type="button" href="#" @click.prevent="closeModal" class="hims-button-rm">
                                            <span uk-icon="icon:close; ratio:0.8"></span> Tutup
                                        </button>
                                        <button type="submit" href="#" class="hims-button-rm" style="border-top-right-radius: 5px; border-bottom-right-radius: 5px;">
                                            <span uk-icon="icon:file-text; ratio:0.8"></span> Simpan
                                        </button>
                                    </div>
                                </div>        
                            </div>
                        </div>
                        <div style="margin-top:15px;"> 
                            <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                                <div uk-spinner="ratio : 2"></div>
                                <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                            </div>                         
                            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                                <p>{{ errors.invalid }}</p>
                            </div>        
                            <div class="uk-grid-small hims-form-subpage" uk-grid >
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                        no rekam medis dan data pasien.
                                    </p>
                                </div>
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No RM</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="radiology.pasien.no_rm" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>                            
                                    
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small uk-width-expand" v-model="radiology.pasien.nama_pasien" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Salut</label>
                                        <div class="uk-form-controls">
                                            <select v-model="radiology.pasien.salut" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option v-if="isRefExist" v-for="dt in salutRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                        <div class="uk-form-controls">
                                            <select v-model="radiology.pasien.jns_kelamin" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option value="L">LAKI-LAKI</option>
                                                <option value="P">PEREMPUAN</option>
                                                <option value="-">-</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small uk-text-uppercase" v-model="radiology.pasien.tempat_lahir" type="text" style="color:black;">
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="radiology.pasien.tgl_lahir" type="date" style="color:black;">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="radiology.pasien.nik" type="text" style="color:black;">
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <div class="uk-grid-small hims-form-subpage" uk-grid>
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 0.2em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">HASIL RADIOLOGI</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">informasi umum hasil radiologi.</p>
                                </div>                    
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">         
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Hasil*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="radiology.tgl_hasil" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Diserahkan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="radiology.tgl_periksa" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diserahkan Oleh</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.diserahkan_oleh">
                                        </div>
                                    </div>   
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Media Hasil</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.media_hasil">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penerima Hasil</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.penerima_hasil">
                                        </div>
                                    </div>  
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Hubungan Penerima</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.hub_penerima">
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <div class="uk-grid-small hims-form-subpage" uk-grid>
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 0.2em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">TINDAKAN RADIOLOGI</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">catatan hasil radiologi per tindakan.</p>
                                </div>                    
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">         
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tindakan</label>
                                        <div class="uk-form-controls">
                                            <h3>Nama Tindakan</h3>
                                        </div>
                                    </div>   
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Foto</label>
                                        <div class="uk-form-controls">
                                            <select v-model="radiology.jenis_foto" class="uk-select uk-form-small" style="color:black;">
                                                <option v-if="isRefExist" v-for="dt in jenisFotoRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            </select>
                                        </div>
                                    </div>   
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Foto</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="radiology.no_foto">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <div class="uk-form-controls">
                                            <search-select
                                                :config = configSelect
                                                :searchUrl = dokterUrl
                                                label = "Dokter / Spesialis Pemeriksa"
                                                placeholder = "pilih dokter spesialis"
                                                captionField = "dokter_nama"
                                                valueField = "dokter_id"
                                                :itemListData = dokterDesc
                                                :value = "radiology.dokter_id"
                                                v-on:item-select = "dokterSelected"
                                            ></search-select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-1@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Uraian Hasil</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="radiology.uraian_hasil" type="text" placeholder="uraian hasil"></textarea>
                                        </div>
                                    </div> 
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kesan</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="radiology.kesan" type="text" placeholder="kesan"></textarea>
                                        </div>
                                    </div> 
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="radiology.catatan" type="text" placeholder="catatan"></textarea>
                                        </div>
                                    </div> 
                                </div>
                            </div>                        
                        </div>
                        <div class="uk-grid-small hims-form-subpage" uk-grid>
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">PEMERIKSAAN DETAIL INFO
                                    <span style="font-size:12px;font-style:italic;padding:0;margin:0 0 0 0.5em;">item pemeriksaan pasien.</span>
                                </h5>                    
                            </div>         
                            <!-- PEMERIKSAAN -->
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;margin:0;">
                                <div style="margin-top:1em;" class="uk-responsive">
                                    <base-table ref="tableRadiologi"
                                        :columns = "tbColumns" 
                                        :config = "configTable" 
                                        :urlSearch="hasilTindakanUrl">
                                    </base-table>
                                </div>
                            </div>
                        </div>
                    </form>  
                </li>
            </ul>
           
        </div>
        <modal-list-pasien ref="modalListPasien" v-on:listPasienSelected = "pasienSelected"></modal-list-pasien>
        <modal-form-pasien ref="modalFormPasien" v-on:modalPasienClosed="modalPasienClosed"></modal-form-pasien>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';
import ModalListPasien from '@/Pages/Penunjang/Radiologi/components/ModalListPasien.vue';
import ModalFormPasien from '@/Pages/Penunjang/Radiologi/components/ModalFormPasien.vue';
import BaseTable from '@/Components/BaseTable';

export default {
    name : 'form-registrasi', 
    components : {
        SearchSelect,
        SearchList,
        ModalListPasien,
        ModalFormPasien,
        BaseTable,
    },
    data() {
        return {
            isLoading : true,
            configSelect : {
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            configTable : { isExpandable : false, filterType : 'REGULAR', },
            penjaminDesc: [
                { colName : 'penjamin_nama', labelData : '', isTitle : true },
                { colName : 'penjamin_id', labelData : '', isTitle : false },
            ],
            tindakanDesc : [
                { colName : 'tindakan_nama', labelData : '', isTitle : true },
                { colName : 'tindakan_id', labelData : '', isTitle : false },
            ], 
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],
            asalDataPasienDesc : [
                { colName : 'caption', labelData : '', isTitle : true },
                { colName : 'desc', labelData : '', isTitle : false },
            ], 
            jenisCitoDesc : [
                { colName : 'caption', labelData : '', isTitle : true },
                { colName : 'desc', labelData : '', isTitle : false },
            ], 

            asalDataPasienLists: [
                { value : 'MASTER_PASIEN', caption:'MASTER PASIEN', desc : 'pasien datang sendiri', is_pasien_luar : false, allow_pasien_baru : true },
                { value : 'RAD', caption:'PASIEN RADIOLOGI', desc : 'pasien yang sudah daftar ke radiologi', is_pasien_luar : false, allow_pasien_baru : false },
            ],

            jenisCitoLists: [
                { value : 'SEGERA', caption:'SEGERA', desc : 'CITO'},
                { value : 'PENTING', caption:'PENTING', desc : 'STATIM'},
                { value : 'SANGAT_PENTING', caption:'SANGAT PENTING', desc : 'URGENT'},
                { value : 'BERBAHAYA', caption:'P.I.M', desc : 'PERICULUM IN MORA'},
            ],
            tbColumns : [
                { name : 'tindakan_nama', title : 'NAMA TINDAKAN', colType : 'text'},
                { name : 'jumlah', title : 'JUMLAH', colType : 'text'},
                { name : 'satuan', title : 'SATUAN', colType : 'text'},
            ], 

            hasilTindakanUrl : '',
            isUpdate : false,
            tindakanUrl : '',
            dokterUrl : '',
            asalDataLists : [],
            jenisCitoDataLists : [],
            tindakanLists : [],
            dokterLists : [],

            radiology : {
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                client_id : null,
                tgl_permintaan : this.getTodayDate(),
                tgl_hasil : this.getTodayDate(),
                tgl_periksa : this.getTodayDate(),
                tgl_diserahkan : this.getTodayDate(),
                media_hasil : null,
                diagnosa : null,
                ket_klinis : null,
                is_cito : null,
                jenis_cito : null,
                is_multi_hasil : null,
                diserahkan_oleh: null,
                penerima_hasil : null,
                hub_penerima : null,
                dokter_id : null,
                dokter_nama : null,
                dokter_unit_id : null,
                jenis_foto : null,
                no_foto : null,
                uraian_hasil : null,
                kesan : null,
                catatan : null,
                status : null,
                is_aktif : null,
                pasien : {
                    reg_id : null,
                    is_pasien_luar : false,
                    pasien_id : null,
                    nik : null,
                    no_kk : null,
                    no_rm : null,
                    salut : null,
                    nama_pasien : null,
                    nama_lengkap : null,
                    tempat_lahir : null,
                    tgl_lahir : this.getTodayDate(),
                    usia : null,
                    jns_kelamin : null,
                    propinsi_id : null,
                    kota_id: null,
                    kecamatan_id : null,
                    kelurahan_id : null,
                    kodepos: null,
                    is_hamil : false,
                    is_pasien_baru : false,
                    is_aktif : false,
                    client_id : null,
                },
                detail : [],
                hasil : [],
                
            },

            addedAct : {
                reg_detail_id : null,
                registrasi_id : null,
                tindakan_nama : null,
                tindakan_id : null,
                satuan : null,
                jumlah : 1,
                harga : 0,
                diskon : 0,
                subtotal : 0,
                is_aktif : true,
            }

        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('asuransi',['asuransiLists']),
        ...mapGetters('referensi',[
            'isRefExist',
            'jenisPelayananRefs',
            'jenisRegistrasiRefs',
            'asalPasienRefs',
            'caraRegistrasiRefs',
            'salutRefs',
            'jenisPenjaminRefs',
            'hubKeluargaRefs',
            'jenisFotoRefs'
        ]),    
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('radiologi',['createRadiologi','updateRadiologi','updateHasilRadiologi','dataRadiologi','listsRegRad','dataRegRad']),
        
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            this.asalDataLists = JSON.parse(JSON.stringify(this.asalDataPasienLists));
            this.jenisCitoDataLists = JSON.parse(JSON.stringify(this.jenisCitoLists));
        },

        /**
         * get date today for default value input date 
         */
         getTodayDate(){
            let dt = new Date();
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate();
            if(date < 10) {date = `0${date}`}
            let str_dt = `${year}-${month}-${date}`
            return str_dt;
        },

        formatedDate(data){
            let dt = new Date(data);
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate();
            if(date < 10) {date = `0${date}`}
            let str_dt = `${year}-${month}-${date}`
            return str_dt;
        },

        searchPasien(){
            this.$refs.modalListPasien.showPasienList(this.radiology.asal_data_pasien);
        },

        pasienSelected(data) {
            if(data){
                if(data.pasien_id) {
                    this.refreshPasienReg(data.reg_id);
                }
            }
        },

        newRegistrasi(){
            this.clearRegistrasi();
            this.isUpdate = false;
            this.isLoading = false;            
        },

        modalPasienClosed(data){
            if(data){
                if(data.pasien_id !== null) {
                    this.refreshPasienReg(data.pasien_id);
                }
            }
        },

        ubahPasien(){ this.CLR_ERRORS(); this.$refs.modalFormPasien.editDataPasien(this.radiology.pasien_id); },
        newPasien(){ this.CLR_ERRORS(); this.$refs.modalFormPasien.createNewPasien(); },
        closeTab() { this.$emit('redirectMainTab',true); },
        pasienTab(){ this.$emit('redirectPatientTab',true); },

        asalDataPasienSelected(data){
            this.clearRegPasien();
            this.radiology.asal_data_pasien = null;
            this.allow_pasien_baru = false;
            if(data) {
                this.radiology.asal_data_pasien = data.value;
                this.radiology.pasien.is_pasien_luar = data.is_pasien_luar;
                this.radiology.is_pasien_luar = data.is_pasien_luar;
                this.allow_pasien_baru = data.allow_pasien_baru;
            }
        },

        dokterSelected(data) {       
            this.radiology.dokter_id = null;
            this.radiology.dokter_nama = null;
            this.radiology.dokter_unit_id = null;
            if(data) {
                this.radiology.dokter_id = data.dokter_id;
                this.radiology.dokter_nama = data.dokter_nama;
                this.radiology.dokter_unit_id = data.dokter_unit_id;
            }

        },

        citoSelected(data) {
            this.radiology.is_cito = null;
            this.radiology.jenis_cito = null;
            if(data) {
                this.radiology.is_cito = '1';
                this.radiology.jenis_cito = data.value;
            }
        },

        itemActSelected(data) {
            this.addedAct.tindakan_id = null;
            this.addedAct.tindakan_nama = null;
            this.addedAct.harga = null;
            this.addedAct.satuan = null;
            this.addedAct.jumlah = null;
            this.addedAct.subtotal = null;
            this.addedAct.is_aktif = true;
            if(data) {
                this.addedAct.tindakan_id = data.tindakan_id;
                this.addedAct.tindakan_nama = data.tindakan_nama;
                this.addedAct.harga = data.harga;
                this.addedAct.satuan = data.satuan;
                this.addedAct.jumlah = data.jumlah;
                this.addedAct.subtotal = data.harga;
                this.addedAct.is_aktif = true;
            }
        },

        closeModal(){
            this.clearRegistrasi();
            this.$emit('closed',true);
        },

        closeRegistrasiModal(){
            this.$emit('formRegistrasiClosed',true);
            UIkit.modal('#formRegistrasi').hide();
        },

        submitRadiology(){            
            this.CLR_ERRORS();
            if(this.isUpdate == false) {
                this.createRadiologi(this.radiology).then((response) => {
                    if (response.success == true) {
                        this.fillRegistrasi(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        alert(response.message);
                    }
                })
            }
            else {
                this.updateRadiologi(this.radiology).then((response) => {
                    if (response.success == true) {
                        this.fillRegistrasi(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        alert(response.message);
                    }
                })
            }            
        },

        submitHasilRadiology(){
            this.CLR_ERRORS();
            this.updateHasilRadiologi(this.radiology).then((response) => {
                if (response.success == true) {
                    this.fillRegistrasi(response.data);
                    this.$emit('saveSucceed',true);
                    this.isUpdate = true;
                    alert(response.message);
                }
            })
        },

        editRadiologi(data){
            this.clearRegistrasi();
            this.dataRadiologi(data.reg_id).then((response)=>{
                if (response.success == true) {
                    this.isLoading = false;
                    this.fillRegistrasi(response.data);
                    this.isUpdate = true;
                } 
                else { alert(response.message); }
                this.isLoading = false;
            })
        },   

        addActItem() {
            if(this.addedAct.tindakan_nama == "" || this.addedAct.tindakan_nama == null || this.addedAct.jumlah == null || this.addedAct.jumlah == 0) {
                alert("Data tidak dapat ditambahkan. ada data yang masih kurang.");
                return false;
            }
            let addedVal = JSON.parse(JSON.stringify(this.addedAct));
            this.radiology.detail.push(addedVal);
            this.clearAddedItem();
        },

        clearAddedItem(){
            this.addedAct.reg_detail_id = null;
            this.addedAct.reg_id = null;
            this.addedAct.tindakan_id = null;
            this.addedAct.tindakan_nama = null;
            this.addedAct.satuan = null;
            this.addedAct.jumlah = 0;
            this.addedAct.harga = 0;
            this.addedAct.diskon = 0;
            this.addedAct.subtotal = 0;
            this.addedAct.status = 'DRAFT';
            this.addedAct.is_aktif = true;
        },

        addedItemAktifasi(data){
            this.radiology.detail = this.radiology.detail.filter(item => { return item['is_aktif'] == true || item['reg_detail_id'] !== null });
        },

        refreshPasienReg(regId){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.clearRegPasien();
            this.dataRegRad(regId).then((response)=>{
                if (response.success == true) {
                    this.fillPasienReg(response.data);
                } else {
                    alert(response.message);
                }
                this.isLoading = false;
            })
        },

        clearRegPasien(){
            this.radiology.pasien.reg_id = null;
            this.radiology.pasien.pasien_id = null;
            this.radiology.pasien.nik = null;
            this.radiology.pasien.no_kk = null;
            this.radiology.pasien.no_rm = null;
            this.radiology.pasien.salut = null;
            this.radiology.pasien.nama_pasien = null;
            this.radiology.pasien.nama_lengkap = null;
            this.radiology.pasien.tempat_lahir = null;
            this.radiology.pasien.tgl_lahir = this.getTodayDate();
            this.radiology.pasien.usia = null;
            this.radiology.pasien.jns_kelamin = null;
            this.radiology.pasien.propinsi_id = null;
            this.radiology.pasien.kota_id= null;
            this.radiology.pasien.kecamatan_id = null;
            this.radiology.pasien.kelurahan_id = null;
            this.radiology.pasien.kodepos= null;
            this.radiology.pasien.is_hamil = null;
            this.radiology.pasien.is_pasien_baru = null;
            this.radiology.pasien.is_bpjs = false;
            this.radiology.pasien.is_aktif = null;
            this.radiology.pasien.client_id = null;

            this.radiology.pasien_id = null;
        },

        fillPasienReg(data) {    
            this.tindakanUrl = `/units/${data.transaksi.unit_id}/acts`;
            this.dokterUrl = `/doctors/unit/${data.transaksi.unit_id}`;
            
            this.radiology.unit_id = data.transaksi.unit_id;
            this.radiology.reg_id = data.transaksi.reg_id;
            this.radiology.trx_id = data.transaksi.trx_id;
            this.radiology.tgl_permintaan = this.formatedDate(data.transaksi.tgl_transaksi);
            this.radiology.dokter_id = data.transaksi.dokter_id;
            this.radiology.pasien_id = data.pasien_id;
            this.radiology.pasien.no_rm = data.no_rm;
            this.radiology.pasien.nama_pasien = data.nama_pasien;
            this.radiology.pasien.salut = data.salut;
            this.radiology.pasien.jns_kelamin = data.jns_kelamin;
            this.radiology.pasien.tempat_lahir = data.tempat_lahir;
            this.radiology.pasien.tgl_lahir = data.tgl_lahir;
            this.radiology.pasien.nik = data.nik;
            this.radiology.pasien.no_kk = data.no_kk;
            this.radiology.jns_penjamin = data.jns_penjamin;
            this.radiology.penjamin_id = data.penjamin_id;
            this.radiology.penjamin_nama = data.penjamin_nama;
            this.radiology.is_pasien_luar = false;
            this.radiology.detail = data.tindakan;
        },

        clearRegistrasi(){
            this.clearAddedItem();
            this.radiology.reg_id = null;
            this.radiology.trx_id = null;
            this.radiology.sub_trx_id = null;
            this.radiology.client_id = null;
            this.radiology.tgl_permintaan = this.getTodayDate();
            this.radiology.tgl_hasil = this.getTodayDate();
            this.radiology.tgl_periksa = this.getTodayDate();
            this.radiology.tgl_diserahkan = this.getTodayDate();
            this.radiology.media_hasil = null;
            this.radiology.diagnosa = null;
            this.radiology.ket_klinis = null;
            this.radiology.is_cito = null;
            this.radiology.jenis_cito = null;
            this.radiology.is_multi_hasil = null;
            this.radiology.diserahkan_oleh = null;
            this.radiology.penerima_hasil = null;
            this.radiology.hub_penerima = null;
            this.radiology.status = null;
            this.radiology.is_aktif = null;
            
            this.radiology.pasien.reg_id = null;
            this.radiology.pasien.is_pasien_luar = null;
            this.radiology.pasien.pasien_id = null;
            this.radiology.pasien.nik = null;
            this.radiology.pasien.no_kk = null;
            this.radiology.pasien.no_rm = null;
            this.radiology.pasien.salut = null;
            this.radiology.pasien.nama_pasien = null;
            this.radiology.pasien.nama_lengkap = null;
            this.radiology.pasien.tempat_lahir = null;
            this.radiology.pasien.tgl_lahir = null;
            this.radiology.pasien.usia = null;
            this.radiology.pasien.jns_kelamin = null;
            this.radiology.pasien.propinsi_id = null;
            this.radiology.pasien.kota_id= null;
            this.radiology.pasien.kecamatan_id = null;
            this.radiology.pasien.kelurahan_id = null;
            this.radiology.pasien.kodepos= null;
            this.radiology.pasien.is_hamil = null;
            this.radiology.pasien.is_pasien_baru = null;
            this.radiology.pasien.is_aktif = null;
            this.radiology.pasien.client_id = null;

            this.radiology.detail = [];

            this.radiology.hasil = [];
        },

        fillRegistrasi(data){
            this.tindakanUrl = `/units/${data.transaksi.unit_id}/acts`;
            this.dokterUrl = `/doctors/unit/${data.transaksi.unit_id}`;
            this.hasilTindakanUrl = `/radiology/tindakan/${data.reg_id}`;

            this.radiology.reg_id = data.reg_id;
            this.radiology.trx_id = data.trx_id;
            this.radiology.sub_trx_id = data.sub_trx_id;
            this.radiology.client_id = null;
            this.radiology.tgl_permintaan = this.formatedDate(data.tgl_permintaan);
            this.radiology.tgl_hasil = this.formatedDate(data.tgl_hasil);
            this.radiology.tgl_periksa = this.formatedDate(data.tgl_periksa);
            this.radiology.tgl_diserahkan = this.formatedDate(data.tgl_diserahkan);
            this.radiology.media_hasil = data.media_hasil;
            this.radiology.diagnosa = data.diagnosa;
            this.radiology.ket_klinis = data.ket_klinis;
            this.radiology.is_cito = data.is_cito;
            this.radiology.jenis_cito = data.jenis_cito;
            this.radiology.is_multi_hasil = data.is_multi_hasil;
            this.radiology.diserahkan_oleh = data.diserahkan_oleh;
            this.radiology.penerima_hasil = data.penerima_hasil;
            this.radiology.hub_penerima = data.hub_penerima;
            this.radiology.status = data.status;
            this.radiology.is_aktif = data.is_aktif;
            
            if(data.hasil != null){
                this.radiology.jenis_foto = data.hasil.jenis_foto;
                this.radiology.no_foto = data.hasil.no_foto;
                this.radiology.uraian_hasil = data.hasil.uraian_hasil;
                this.radiology.kesan = data.hasil.kesan;
                this.radiology.catatan = data.hasil.catatan;
                this.radiology.dokter_pemeriksa = data.hasil.dokter_pemeriksa;
            }

            this.radiology.dokter_id = data.transaksi.dokter_id;
            this.radiology.unit_id = data.transaksi.unit_id;
            this.radiology.penjamin_id = data.transaksi.penjamin_id;
            this.radiology.penjamin_nama = data.transaksi.penjamin_nama;
            
            this.radiology.pasien.is_pasien_luar = data.pasien.is_pasien_luar;
            this.radiology.pasien.pasien_id = data.pasien.pasien_id;
            this.radiology.pasien.nik = data.pasien.nik;
            this.radiology.pasien.no_kk = data.pasien.no_kk;
            this.radiology.pasien.no_rm = data.pasien.no_rm;
            this.radiology.pasien.salut = data.pasien.salut;
            this.radiology.pasien.nama_pasien = data.pasien.nama_pasien;
            this.radiology.pasien.nama_lengkap = data.pasien.nama_pasien;
            this.radiology.pasien.tempat_lahir = data.pasien.tempat_lahir;
            this.radiology.pasien.tgl_lahir = data.pasien.tgl_lahir;
            this.radiology.pasien.usia = data.pasien.usia;
            this.radiology.pasien.jns_kelamin = data.pasien.jns_kelamin;
            this.radiology.pasien.propinsi_id = data.pasien.propinsi_id;
            this.radiology.pasien.kota_id= data.pasien.kota_id;
            this.radiology.pasien.kecamatan_id = data.pasien.kecamatan_id;
            this.radiology.pasien.kelurahan_id = data.pasien.kelurahan_id;
            this.radiology.pasien.kodepos= data.pasien.kodepos;
            this.radiology.pasien.is_hamil = data.pasien.is_hamil;
            this.radiology.pasien.is_pasien_baru = data.pasien.is_pasien_baru;
            this.radiology.pasien.is_aktif = data.pasien.is_aktif;
            this.radiology.pasien.client_id = data.pasien.client_id;

            this.radiology.detail = data.tindakan;

            // this.radiology.hasil = data.hasil;
        },
    }
}
</script>

<style>
button disabled {
    background-color: #000;
}

.hims-button-rm {
    background-color: #fff;
    color:black;
    height:35px;
    width:auto;
    border: none; 
    display:inline-block; 
    padding:0.2em 1em 0.2em 1em; 
    font-size: 14px;
}

.hims-button-rm:hover {
    background-color: #cc0202;
    color:white;
}

button:disabled,
button[disabled]{
  background: #cccccc;
  color: #666666;
}

.uk-input, .uk-textarea, .uk-checkbox, .uk-select {
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

</style>