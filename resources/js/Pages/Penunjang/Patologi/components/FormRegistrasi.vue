<template>
    <div class="uk-container hims-form-container" style="min-height:50vh;" id="formRegistrasi">
        <div>
            <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                <li>
                    <a href="#" @click.prevent="closeTab" class="uk-text-uppercase">                        
                        <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">
                            <span uk-icon="icon:arrow-left;ratio:1.5"></span> PATHOLOGI ANATOMI
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
                <!-- Input Pemeriksaan Patologi-->
                <li>
                    <form action="" @submit.prevent="submitPatologi">
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
                                            :value = "patologi.asal_data_pasien"
                                            v-on:item-select = "asalDataPasienSelected"
                                        ></search-list>
                                    </div>
                                    <div style="padding:0;margin:0;">
                                        <div class="uk-button-group uk-box-shadow-medium" style="padding:0;margin:0;border-radius:5px;border:1px solid #efefef;">
                                            <button :disabled="patologi.pasien.is_pasien_luar" type="button" href="#" @click.prevent="searchPasien" class="hims-button-rm" style="border-radius: 5px; ">
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
                                            <input class="uk-input uk-form-small" v-model="patologi.pasien.no_rm" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>                            
                                    
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small uk-width-expand" v-model="patologi.pasien.nama_pasien" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Salut</label>
                                        <div class="uk-form-controls">
                                            <select v-model="patologi.pasien.salut" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option v-if="isRefExist" v-for="dt in salutRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                        <div class="uk-form-controls">
                                            <select v-model="patologi.pasien.jns_kelamin" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option value="L">LAKI-LAKI</option>
                                                <option value="P">PEREMPUAN</option>
                                                <option value="-">-</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small uk-text-uppercase" v-model="patologi.pasien.tempat_lahir" type="text" style="color:black;">
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="patologi.pasien.tgl_lahir" type="date" style="color:black;">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="patologi.pasien.nik" type="text" style="color:black;">
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <div class="uk-grid-small hims-form-subpage" uk-grid>
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 0.2em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">DATA PATOLOGI</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">pilih pelayanan dan spesialis.</p>
                                </div>                    
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">         
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Permintaan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="patologi.tgl_permintaan" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Periksa*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="patologi.tgl_periksa" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diagnosa</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.diagnosa">
                                        </div>
                                    </div>    
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Keterangan Klinis</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.ket_klinis">
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
                                                :value = "patologi.jenis_cito"
                                                v-on:item-select = "citoSelected"
                                            ></search-list>
                                        </div>
                                    </div> 
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Multi Hasil</label>
                                        <div class="uk-form-controls">
                                            <select v-model="patologi.is_multi_hasil" class="uk-select uk-form-small" style="border:1px solid dark;">
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
                                                :value = "patologi.dokter_id"
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
                                        <tr v-if="patologi.detail.length > 0" v-for="dt in patologi.detail">
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
                <!-- Hasil PATOLOGI Single -->
                <li>
                    <form action="" @submit.prevent="submitHasilPatologi">
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
                                            <input class="uk-input uk-form-small" v-model="patologi.pasien.no_rm" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>                            
                                    
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small uk-width-expand" v-model="patologi.pasien.nama_pasien" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Salut</label>
                                        <div class="uk-form-controls">
                                            <select v-model="patologi.pasien.salut" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option v-if="isRefExist" v-for="dt in salutRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                        <div class="uk-form-controls">
                                            <select v-model="patologi.pasien.jns_kelamin" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option value="L">LAKI-LAKI</option>
                                                <option value="P">PEREMPUAN</option>
                                                <option value="-">-</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small uk-text-uppercase" v-model="patologi.pasien.tempat_lahir" type="text" style="color:black;">
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="patologi.pasien.tgl_lahir" type="date" style="color:black;">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="patologi.pasien.nik" type="text" style="color:black;">
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <div class="uk-grid-small hims-form-subpage" uk-grid>
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 0.2em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">HASIL PATOLOGI</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">pilih pelayanan dan spesialis.</p>
                                </div>                    
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">         
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Hasil*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="patologi.tgl_hasil" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Diserahkan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="patologi.tgl_periksa" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diserahkan Oleh</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.diserahkan_oleh">
                                        </div>
                                    </div>   
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Media Hasil</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.media_hasil">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penerima Hasil</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.penerima_hasil">
                                        </div>
                                    </div>  
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Hubungan Penerima</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.hub_penerima">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Foto</label>
                                        <div class="uk-form-controls">
                                            <select v-model="patologi.jenis_foto" class="uk-select uk-form-small" style="color:black;">
                                                <option v-if="isRefExist" v-for="dt in jenisFotoRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            </select>
                                        </div>
                                    </div>   
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Foto</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.no_foto">
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
                                                :value = "patologi.dokter_id"
                                                v-on:item-select = "dokterSelected"
                                            ></search-select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-1@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Uraian Hasil</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="patologi.uraian_hasil" type="text" placeholder="uraian hasil"></textarea>
                                        </div>
                                    </div> 
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kesan</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="patologi.kesan" type="text" placeholder="kesan"></textarea>
                                        </div>
                                    </div> 
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="patologi.catatan" type="text" placeholder="catatan"></textarea>
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
                                    <base-table ref="tablePatologi"
                                        :columns = "tbColumns" 
                                        :config = "configTable" 
                                        :urlSearch="hasilTindakanUrl">
                                    </base-table>
                                </div>
                            </div>
                        </div>
                    </form>  
                </li>
                <!-- Hasil Patologi Multiple -->
                <li>
                    <form action="" @submit.prevent="submitHasilPatologi">
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
                                            <input class="uk-input uk-form-small" v-model="patologi.pasien.no_rm" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>                            
                                    
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small uk-width-expand" v-model="patologi.pasien.nama_pasien" type="text" style="color:black;" disabled>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Salut</label>
                                        <div class="uk-form-controls">
                                            <select v-model="patologi.pasien.salut" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option v-if="isRefExist" v-for="dt in salutRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                        <div class="uk-form-controls">
                                            <select v-model="patologi.pasien.jns_kelamin" class="uk-select uk-form-small" style="color:black;" disabled>
                                                <option value="L">LAKI-LAKI</option>
                                                <option value="P">PEREMPUAN</option>
                                                <option value="-">-</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small uk-text-uppercase" v-model="patologi.pasien.tempat_lahir" type="text" style="color:black;">
                                        </div>
                                    </div>

                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="patologi.pasien.tgl_lahir" type="date" style="color:black;">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK</label>
                                        <div class="uk-form-controls">
                                            <input disabled class="uk-input uk-form-small" v-model="patologi.pasien.nik" type="text" style="color:black;">
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <div class="uk-grid-small hims-form-subpage" uk-grid>
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 0.2em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">HASIL PATOLOGI</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">informasi umum hasil patologi.</p>
                                </div>                    
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">         
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Hasil*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="patologi.tgl_hasil" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Diserahkan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="patologi.tgl_periksa" type="date">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diserahkan Oleh</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.diserahkan_oleh">
                                        </div>
                                    </div>   
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Media Hasil</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.media_hasil">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penerima Hasil</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.penerima_hasil">
                                        </div>
                                    </div>  
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Hubungan Penerima</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.hub_penerima">
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <div class="uk-grid-small hims-form-subpage" uk-grid>
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 0.2em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">TINDAKAN PATOLOGI</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">catatan hasil patologi per tindakan.</p>
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
                                            <select v-model="patologi.jenis_foto" class="uk-select uk-form-small" style="color:black;">
                                                <option v-if="isRefExist" v-for="dt in jenisFotoRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            </select>
                                        </div>
                                    </div>   
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Foto</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="patologi.no_foto">
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
                                                :value = "patologi.dokter_id"
                                                v-on:item-select = "dokterSelected"
                                            ></search-select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-1@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Uraian Hasil</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="patologi.uraian_hasil" type="text" placeholder="uraian hasil"></textarea>
                                        </div>
                                    </div> 
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kesan</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="patologi.kesan" type="text" placeholder="kesan"></textarea>
                                        </div>
                                    </div> 
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                        <div class="uk-form-controls">
                                            <textarea class="uk-textarea uk-form-small" v-model="patologi.catatan" type="text" placeholder="catatan"></textarea>
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
                                    <base-table ref="tablePatologi"
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
import ModalListPasien from '@/Pages/Penunjang/Patologi/components/ModalListPasien.vue';
import ModalFormPasien from '@/Pages/Penunjang/Patologi/components/ModalFormPasien.vue';
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
                { value : 'PA', caption:'PASIEN PATOLOGI', desc : 'pasien yang sudah daftar ke patologi', is_pasien_luar : false, allow_pasien_baru : false },
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

            patologi : {
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
        ...mapActions('patologi',['createPatologi','updatePatologi','updateHasilPatologi','dataPatologi','listsRegPa','dataRegPa']),
        
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
            this.$refs.modalListPasien.showPasienList(this.patologi.asal_data_pasien);
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

        ubahPasien(){ this.CLR_ERRORS(); this.$refs.modalFormPasien.editDataPasien(this.patologi.pasien_id); },
        newPasien(){ this.CLR_ERRORS(); this.$refs.modalFormPasien.createNewPasien(); },
        closeTab() { this.$emit('redirectMainTab',true); },
        pasienTab(){ this.$emit('redirectPatientTab',true); },

        asalDataPasienSelected(data){
            this.clearRegPasien();
            this.patologi.asal_data_pasien = null;
            this.allow_pasien_baru = false;
            if(data) {
                this.patologi.asal_data_pasien = data.value;
                this.patologi.pasien.is_pasien_luar = data.is_pasien_luar;
                this.patologi.is_pasien_luar = data.is_pasien_luar;
                this.allow_pasien_baru = data.allow_pasien_baru;
            }
        },

        dokterSelected(data) {       
            this.patologi.dokter_id = null;
            this.patologi.dokter_nama = null;
            this.patologi.dokter_unit_id = null;
            if(data) {
                this.patologi.dokter_id = data.dokter_id;
                this.patologi.dokter_nama = data.dokter_nama;
                this.patologi.dokter_unit_id = data.dokter_unit_id;
            }

        },

        citoSelected(data) {
            this.patologi.is_cito = null;
            this.patologi.jenis_cito = null;
            if(data) {
                this.patologi.is_cito = '1';
                this.patologi.jenis_cito = data.value;
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

        submitPatologi(){            
            this.CLR_ERRORS();
            if(this.isUpdate == false) {
                this.createPatologi(this.patologi).then((response) => {
                    if (response.success == true) {
                        this.fillRegistrasi(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        alert(response.message);
                    }
                })
            }
            else {
                this.updatePatologi(this.patologi).then((response) => {
                    if (response.success == true) {
                        this.fillRegistrasi(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        alert(response.message);
                    }
                })
            }            
        },

        submitHasilPatologi(){
            this.CLR_ERRORS();
            this.updateHasilPatologi(this.patologi).then((response) => {
                if (response.success == true) {
                    this.fillRegistrasi(response.data);
                    this.$emit('saveSucceed',true);
                    this.isUpdate = true;
                    alert(response.message);
                }
            })
        },

        editPatologi(data){
            this.clearRegistrasi();
            this.dataPatologi(data.reg_id).then((response)=>{
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
            this.patologi.detail.push(addedVal);
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
            this.patologi.detail = this.patologi.detail.filter(item => { return item['is_aktif'] == true || item['reg_detail_id'] !== null });
        },

        refreshPasienReg(regId){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.clearRegPasien();
            this.dataRegPa(regId).then((response)=>{
                if (response.success == true) {
                    this.fillPasienReg(response.data);
                } else {
                    alert(response.message);
                }
                this.isLoading = false;
            })
        },

        clearRegPasien(){
            this.patologi.pasien.reg_id = null;
            this.patologi.pasien.pasien_id = null;
            this.patologi.pasien.nik = null;
            this.patologi.pasien.no_kk = null;
            this.patologi.pasien.no_rm = null;
            this.patologi.pasien.salut = null;
            this.patologi.pasien.nama_pasien = null;
            this.patologi.pasien.nama_lengkap = null;
            this.patologi.pasien.tempat_lahir = null;
            this.patologi.pasien.tgl_lahir = this.getTodayDate();
            this.patologi.pasien.usia = null;
            this.patologi.pasien.jns_kelamin = null;
            this.patologi.pasien.propinsi_id = null;
            this.patologi.pasien.kota_id= null;
            this.patologi.pasien.kecamatan_id = null;
            this.patologi.pasien.kelurahan_id = null;
            this.patologi.pasien.kodepos= null;
            this.patologi.pasien.is_hamil = null;
            this.patologi.pasien.is_pasien_baru = null;
            this.patologi.pasien.is_bpjs = false;
            this.patologi.pasien.is_aktif = null;
            this.patologi.pasien.client_id = null;

            this.patologi.pasien_id = null;
        },

        fillPasienReg(data) {    
            this.tindakanUrl = `/units/${data.transaksi.unit_id}/acts`;
            this.dokterUrl = `/doctors/unit/${data.transaksi.unit_id}`;
            
            this.patologi.unit_id = data.transaksi.unit_id;
            this.patologi.reg_id = data.transaksi.reg_id;
            this.patologi.trx_id = data.transaksi.trx_id;
            this.patologi.tgl_permintaan = this.formatedDate(data.transaksi.tgl_transaksi);
            this.patologi.dokter_id = data.transaksi.dokter_id;
            this.patologi.pasien_id = data.pasien_id;
            this.patologi.pasien.no_rm = data.no_rm;
            this.patologi.pasien.nama_pasien = data.nama_pasien;
            this.patologi.pasien.salut = data.salut;
            this.patologi.pasien.jns_kelamin = data.jns_kelamin;
            this.patologi.pasien.tempat_lahir = data.tempat_lahir;
            this.patologi.pasien.tgl_lahir = data.tgl_lahir;
            this.patologi.pasien.nik = data.nik;
            this.patologi.pasien.no_kk = data.no_kk;
            this.patologi.jns_penjamin = data.jns_penjamin;
            this.patologi.penjamin_id = data.penjamin_id;
            this.patologi.penjamin_nama = data.penjamin_nama;
            this.patologi.is_pasien_luar = false;
            this.patologi.detail = data.tindakan;
        },

        clearRegistrasi(){
            this.clearAddedItem();
            this.patologi.reg_id = null;
            this.patologi.trx_id = null;
            this.patologi.sub_trx_id = null;
            this.patologi.client_id = null;
            this.patologi.tgl_permintaan = this.getTodayDate();
            this.patologi.tgl_hasil = this.getTodayDate();
            this.patologi.tgl_periksa = this.getTodayDate();
            this.patologi.tgl_diserahkan = this.getTodayDate();
            this.patologi.media_hasil = null;
            this.patologi.diagnosa = null;
            this.patologi.ket_klinis = null;
            this.patologi.is_cito = null;
            this.patologi.jenis_cito = null;
            this.patologi.is_multi_hasil = null;
            this.patologi.diserahkan_oleh = null;
            this.patologi.penerima_hasil = null;
            this.patologi.hub_penerima = null;
            this.patologi.status = null;
            this.patologi.is_aktif = null;
            
            this.patologi.pasien.reg_id = null;
            this.patologi.pasien.is_pasien_luar = null;
            this.patologi.pasien.pasien_id = null;
            this.patologi.pasien.nik = null;
            this.patologi.pasien.no_kk = null;
            this.patologi.pasien.no_rm = null;
            this.patologi.pasien.salut = null;
            this.patologi.pasien.nama_pasien = null;
            this.patologi.pasien.nama_lengkap = null;
            this.patologi.pasien.tempat_lahir = null;
            this.patologi.pasien.tgl_lahir = null;
            this.patologi.pasien.usia = null;
            this.patologi.pasien.jns_kelamin = null;
            this.patologi.pasien.propinsi_id = null;
            this.patologi.pasien.kota_id= null;
            this.patologi.pasien.kecamatan_id = null;
            this.patologi.pasien.kelurahan_id = null;
            this.patologi.pasien.kodepos= null;
            this.patologi.pasien.is_hamil = null;
            this.patologi.pasien.is_pasien_baru = null;
            this.patologi.pasien.is_aktif = null;
            this.patologi.pasien.client_id = null;

            this.patologi.detail = [];

            this.patologi.hasil = [];
        },

        fillRegistrasi(data){
            this.tindakanUrl = `/units/${data.transaksi.unit_id}/acts`;
            this.dokterUrl = `/doctors/unit/${data.transaksi.unit_id}`;
            this.hasilTindakanUrl = `/patologi/tindakan/${data.reg_id}`;

            this.patologi.reg_id = data.reg_id;
            this.patologi.trx_id = data.trx_id;
            this.patologi.sub_trx_id = data.sub_trx_id;
            this.patologi.client_id = null;
            this.patologi.tgl_permintaan = this.formatedDate(data.tgl_permintaan);
            this.patologi.tgl_hasil = this.formatedDate(data.tgl_hasil);
            this.patologi.tgl_periksa = this.formatedDate(data.tgl_periksa);
            this.patologi.tgl_diserahkan = this.formatedDate(data.tgl_diserahkan);
            this.patologi.media_hasil = data.media_hasil;
            this.patologi.diagnosa = data.diagnosa;
            this.patologi.ket_klinis = data.ket_klinis;
            this.patologi.is_cito = data.is_cito;
            this.patologi.jenis_cito = data.jenis_cito;
            this.patologi.is_multi_hasil = data.is_multi_hasil;
            this.patologi.diserahkan_oleh = data.diserahkan_oleh;
            this.patologi.penerima_hasil = data.penerima_hasil;
            this.patologi.hub_penerima = data.hub_penerima;
            this.patologi.status = data.status;
            this.patologi.is_aktif = data.is_aktif;
            
            if(data.hasil != null){
                this.patologi.jenis_foto = data.hasil.jenis_foto;
                this.patologi.no_foto = data.hasil.no_foto;
                this.patologi.uraian_hasil = data.hasil.uraian_hasil;
                this.patologi.kesan = data.hasil.kesan;
                this.patologi.catatan = data.hasil.catatan;
                this.patologi.dokter_pemeriksa = data.hasil.dokter_pemeriksa;
            }

            this.patologi.dokter_id = data.transaksi.dokter_id;
            this.patologi.unit_id = data.transaksi.unit_id;
            this.patologi.penjamin_id = data.transaksi.penjamin_id;
            this.patologi.penjamin_nama = data.transaksi.penjamin_nama;
            
            this.patologi.pasien.is_pasien_luar = data.pasien.is_pasien_luar;
            this.patologi.pasien.pasien_id = data.pasien.pasien_id;
            this.patologi.pasien.nik = data.pasien.nik;
            this.patologi.pasien.no_kk = data.pasien.no_kk;
            this.patologi.pasien.no_rm = data.pasien.no_rm;
            this.patologi.pasien.salut = data.pasien.salut;
            this.patologi.pasien.nama_pasien = data.pasien.nama_pasien;
            this.patologi.pasien.nama_lengkap = data.pasien.nama_pasien;
            this.patologi.pasien.tempat_lahir = data.pasien.tempat_lahir;
            this.patologi.pasien.tgl_lahir = data.pasien.tgl_lahir;
            this.patologi.pasien.usia = data.pasien.usia;
            this.patologi.pasien.jns_kelamin = data.pasien.jns_kelamin;
            this.patologi.pasien.propinsi_id = data.pasien.propinsi_id;
            this.patologi.pasien.kota_id= data.pasien.kota_id;
            this.patologi.pasien.kecamatan_id = data.pasien.kecamatan_id;
            this.patologi.pasien.kelurahan_id = data.pasien.kelurahan_id;
            this.patologi.pasien.kodepos= data.pasien.kodepos;
            this.patologi.pasien.is_hamil = data.pasien.is_hamil;
            this.patologi.pasien.is_pasien_baru = data.pasien.is_pasien_baru;
            this.patologi.pasien.is_aktif = data.pasien.is_aktif;
            this.patologi.pasien.client_id = data.pasien.client_id;

            this.patologi.detail = data.tindakan;

            // this.patologi.hasil = data.hasil;
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