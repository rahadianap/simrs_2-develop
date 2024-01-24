<template>
    <div class="uk-container"  style="padding:0;min-height:70vh;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="formHasilOpsClosed" style="background-color:white;padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:1"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">DATA OPERASI</h4>
            </div>
        </div>
        <div style="background-color:#fff;margin-top:1em;">
            <div style="margin:0;padding:0;">
                <form action="" @submit.prevent="submisiRegOps" style="margin:0;padding:0;">
                    <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;padding:0.5em;margin-top:1em; background-color:#cc0202;">
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt style="color:#fff;">No.Registrasi</dt>
                                <dd style="color:#fff;">{{registrasi.reg_id}}</dd>
                            </dl>
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt style="color:#fff;">No.Transaksi</dt>
                                <dd style="color:#fff;">{{registrasi.trx_id}}</dd>
                            </dl>
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt style="color:#fff;">ID Pasien</dt>
                                <dd style="color:#fff;">{{registrasi.pasien_id}}</dd>
                            </dl>
                        </div>                        
                    </div> 
                    <div style="padding:0 0.5em 0.5em 1em;">
                        <div class="uk-grid-small uk-card1 uk-card-default1 hims-form-header" uk-grid style="padding:0;margin:0;">                        
                            <div class="uk-width-1-1" style="margin:0 0 0.5em 0;padding:0.5em 0 0 0.5em;">
                                <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                                    <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="modalClosed" style="padding:0.5em;"><span uk-icon="arrow-left"></span> Tutup</button>
                                    <button type="button" class="hims-table-btn uk-width-auto" @click="hapusRegistrasi" :disabled="!isUpdate" style="padding:0.5em;"><span uk-icon="trash"></span> Hapus</button>
                                    <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                                    <button type="button" class="hims-table-btn uk-width-auto" @click="refreshRegistrasi" :disabled="!isUpdate" style="padding:0.5em;"><span uk-icon="refresh"></span> Refresh</button>
                                    <button type="submit" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-text"></span> Simpan</button>
                                    <button v-if="isUpdate" :disabled="isLoading" type="button" @click.prevent="konfirmasiReg" class="hims-table-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-edit"></span> Konfirmasi</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-container uk-container-large">                
                        <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                            <div uk-spinner="ratio : 2"></div>
                            <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                        </div>
                        
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>
                        
                        
                        <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">     
                            <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">                        
                                <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                    <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN</h5>
                                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                        detail data pasien.
                                    </p>
                                </div>
                                <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                                    <div class="uk-width-1-1" style="margin:0;padding:0;"></div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                                        <div class="uk-inline uk-width-1-1">
                                            <a v-if="!isUpdate" class="uk-form-icon uk-form-icon-flip" href="#" uk-icon="icon: search" @click.prevent="searchPasien" style="color:black; background-color:white;border:1px solid #aaa;"></a>
                                            <input class="uk-input uk-form-small" v-model="registrasi.nama_pasien" type="text" aria-label="pilih pasien" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No RM*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="registrasi.no_rm" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Pasien*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="operasiData.pasien_id" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin*</label>
                                        <div class="uk-form-controls">
                                            <select v-model="operasiData.jns_kelamin" class="uk-select uk-form-small" required disabled style="color:black;">
                                                <option value="L">Laki-laki</option>
                                                <option value="P">Perempuan</option>
                                                <option value="-">-</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="operasiData.tempat_lahir" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Lahir*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="date" v-model="operasiData.tgl_lahir" style="color:black;" :max="minDate" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Penjamin*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="operasiData.jns_penjamin" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penjamin/Asuransi*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" type="text" v-model="operasiData.penjamin_nama" style="color:black;" disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Kepesertaan*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="operasiData.no_kepesertaan" type="text" style="color:black;" required disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-4@m">
                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK (KTP / ID Card)*</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input uk-form-small" v-model="operasiData.nik" type="text" style="color:black;" required disabled>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-1 uk-hidden" style="padding:0;margin:0;text-align: center;">
                                        <p style="color:black; font-size: 10px; font-style: italic;padding:0;margin-bottom:0;">
                                            {{operasiData.buku_harga_id}} {{operasiData.buku_harga}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <ul id="switcherFormOperasi" class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0 0.5em 0 0.5em;margin:0;background-color: #fff;">
                                    <li><div><a href="#"><h6>DATA OPERASI</h6></a></div></li>
                                    <li style="padding:0;"><div><a href="#"><h6>TIM OPERASI</h6></a></div></li>
                                    <li style="padding:0;"><div><a href="#"><h6>TINDAKAN</h6></a></div></li>
                                    <li style="padding:0;"><div><a href="#"><h6>BHP DAN OBAT</h6></a></div></li>
                                    <li style="padding:0;" id="tab_persalinan" ref="tab_persalinan"><div><a href="#"><h6>DATA IBU DAN BAYI</h6></a></div></li>
                                </ul>
                                <ul class="uk-switcher" style="padding:0;margin:0;background-color: #fff;">
                                    <li>
                                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                                            <div class="uk-width-1-4@m" style="padding:0 0.5em 0.5em 0.5em;">
                                                <h5 style="color:indigo;padding:0;margin:0;">KELAS DAN RUANG OPERASI</h5>
                                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                                    detail kelas dan ruang operasi.
                                                </p>
                                            </div>
                                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">                                
                                                <div class="uk-width-1-2@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Pengirim</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" v-model="operasiData.dokter_pengirim" type="text" style="color:black;" required disabled>
                                                    </div>
                                                </div>

                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Layanan*</label>
                                                    <div class="uk-form-controls">
                                                        <select v-model = "operasiData.kelas_harga_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" required disabled @change.prevent="updateKelasHarga">
                                                            <option v-for="option in kelasHargaLists" v-bind:value="option.kelas_id">{{option.kelas_nama}}</option>
                                                        </select>
                                                    </div>
                                                </div>                                        
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Asal Pasien</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="text" v-model="operasiData.asal_pasien" style="color:black;"  disabled>
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-2@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit*</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="text" v-model="operasiData.unit_nama" style="color:black;"  disabled>
                                                    </div>
                                                </div>

                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Ruang*</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="text" v-model="operasiData.ruang_nama" style="color:black;"  disabled>
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Bed*</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="text" v-model="operasiData.no_bed" style="color:black;"  disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                                            <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                                <h5 style="color:indigo;padding:0;margin:0;">PELAKSANAAN OPERASI</h5>
                                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                                    detail pelaksanaan operasi.
                                                </p>
                                            </div>
                                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">                                  
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Operasi*</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="date" v-model="operasiData.tgl_operasi" style="color:black;" :min="minDate" required>
                                                    </div>
                                                </div>

                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Operasi*</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="time" v-model="operasiData.jam_operasi" style="color:black;" required>
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-2@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Operator*</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="text" v-model="operasiData.dokter_nama" style="color:black;" disabled required>
                                                    </div>
                                                </div>

                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Tindakan*</label>
                                                    <div class="uk-form-controls">
                                                        <select v-model="operasiData.jenis_operasi" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;"> 
                                                            <option v-for="dt in jenisOperasiRefs.json_data" v-bind:value="dt.value" v-bind:key="dt.value">{{dt.text}}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Skala Tindakan*</label>
                                                    <div class="uk-form-controls">
                                                        <select v-model="operasiData.skala_operasi" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;"> 
                                                            <option v-for="dt in skalaOperasiRefs.json_data" v-bind:value="dt.value" v-bind:key="dt.value">{{dt.text}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-2@m">
                                                    <div class="uk-form-controls">
                                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Paket Operasi*</label>
                                                        <select v-model="operasiData.tindakan_operasi_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;"> 
                                                            <option v-for="dt in listTindakanPaket" v-bind:value="dt.tindakan_id" v-bind:key="dt.tindakan_id">{{dt.tindakan_nama}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-2@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diag. Pra Operasi</label>
                                                    <div class="uk-form-controls">
                                                        <textarea class="uk-textarea uk-form-small" type="text" v-model="operasiData.diagnosa_pra" style="color:black;"></textarea>
                                                    </div>
                                                </div>

                                                
                                                <div class="uk-width-1-2@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diag. Pasca Operasi</label>
                                                    <div class="uk-form-controls">
                                                        <textarea class="uk-textarea uk-form-small" type="text" v-model="operasiData.diagnosa_pasca" style="color:black;"></textarea>
                                                    </div>
                                                </div>


                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Selesai*</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="date" v-model="operasiData.tgl_selesai" style="color:black;" :min="minDate" required>
                                                    </div>
                                                </div>

                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Selesai*</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="time" v-model="operasiData.jam_selesai" style="color:black;" required>
                                                    </div>
                                                </div>

                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Anestesi</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="date" v-model="operasiData.tgl_anestesi" style="color:black;" :min="minDate">
                                                    </div>
                                                </div>

                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Anestesi</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="time" v-model="operasiData.jam_anestesi" style="color:black;">
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin-top:1em;">
                                                <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="operasiData.is_persalinan" style="margin-left:5px;" @change="isPersalinanChange"> Operasi Persalinan</label>
                                            </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li>                                    
                                        <sub-form-tim ref="subFormTimOperasi" 
                                            :dokterLists = doctorLists 
                                            :dataOperasi = operasiData
                                            :isLoading = isLoading
                                            v-on:subDataChange = submisiRegOps>
                                        </sub-form-tim>
                                    </li>

                                    <li>
                                        <sub-tindakan-ops ref="subTindakanOps" 
                                            :dokterLists = doctorLists 
                                            :dataOperasi = operasiData
                                            :isLoading = isLoading
                                            v-on:subDataChange = submisiRegOps>
                                        </sub-tindakan-ops>
                                    </li>

                                    <li>
                                        <div class="uk-grid-small hims-form-subpage" uk-grid style="margin-top:0;padding-top:0.2em;">
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                                            <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                                <h5 style="color:indigo;padding:0;margin:0;">DATA PERSALINAN</h5>
                                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                                    detail data orangtua bayi.
                                                </p>
                                            </div>
                                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Ayah*</label>
                                                    <div class="uk-inline uk-width-1-1">
                                                        <input class="uk-input uk-form-small" v-model="operasiData.persalinan.nama_ayah_bayi" type="text" aria-label="pilih pasien" style="color:black;"> 
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK Ayah</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="text" v-model="operasiData.persalinan.nik_ayah_bayi" style="color:black;">
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Kelahiran</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="date" v-model="operasiData.persalinan.tgl_kelahiran" style="color:black;">
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Kelahiran</label>
                                                    <div class="uk-form-controls">
                                                        <input class="uk-input uk-form-small" type="time" v-model="operasiData.persalinan.jam_kelahiran" style="color:black;">
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Umur Kehamilan (bulan)</label>
                                                    <div class="uk-inline uk-width-1-1">
                                                        <input class="uk-input uk-form-small" v-model="operasiData.persalinan.umur_kehamilan" type="number" style="color:black;">
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Persalinan</label>
                                                    <div class="uk-form-controls">
                                                        <select v-model="operasiData.persalinan.jenis_persalinan" class="uk-select uk-form-small" style="color:black;">
                                                            <option value="normal">Normal</option>
                                                            <option value="operasi">Operasi</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelahiran Ke-</label>
                                                    <div class="uk-form-controls">
                                                        <select v-model="operasiData.persalinan.kelahiran_ke" class="uk-select uk-form-small" style="color:black;">
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="uk-width-1-4@m">
                                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kondisi Pasca Melahirkan</label>
                                                    <div class="uk-form-controls">
                                                        <select v-model="operasiData.persalinan.kondisi_ibu" class="uk-select uk-form-small" style="color:black;">
                                                            <option value="baik">Baik</option>
                                                            <option value="buruk">Buruk</option>
                                                            <option value="meninggal">Meninggal</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">                        
                                            <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                                <h5 style="color:indigo;padding:0;margin:0;">DATA BAYI</h5>
                                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                                    detail data bayi baru lahir.
                                                </p>
                                            </div>
                                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                                                <div class="uk-width-1-1">
                                                    <table class="uk-table hims-table1 uk-table-responsive1 uk-box-shadow-small">
                                                        <thead>
                                                            <tr style="border-bottom:1px solid #cc0202;color:white;padding:0.5em;font-weight:500;background-color:#cc0202;">
                                                                <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">NO.RM</th>
                                                                <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Nama Pasien</th>
                                                                <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Informasi</th>
                                                                <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Aktif</th>
                                                                <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">...</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-if="operasiData.persalinan.detail.length > 0">
                                                                <tr v-for="itm in operasiData.persalinan.detail" style="border-top:1px solid #eee;"  v-bind:class="itm.is_aktif != true ? 'inaktif':'' ">
                                                                    <td style="padding:1em;font-weight:500;font-size:11px;color:black;">{{itm.no_rm_bayi}}</td>
                                                                    <td style="padding:1em;font-weight:500;font-size:11px;color:black;">{{itm.nama_bayi}} ({{itm.jk_bayi}})</td>
                                                                    <td style="padding:1em;font-weight:400;font-size:11px;color:black;">
                                                                        <p style="padding:0;margin:0;"><span style="font-weight:500;">BB :</span> {{ itm.bb_bayi }} kg, <span style="font-weight:500;">TB :</span> {{ itm.tb_bayi }} cm</p>
                                                                        <p style="padding:0;margin:0;"><span style="font-weight:500;">Lingkar Kepala :</span> {{ itm.lingkar_kepala }} cm, <span style="font-weight:500;">Lingkar Dada :</span> {{ itm.lingkar_dada }} cm</p>
                                                                        <p style="padding:0;margin:0;"><span style="font-weight:500;">frek. Napas :</span> {{ itm.frekuensi_napas }} /menit, <span style="font-weight:500;">Detak Jantung :</span> {{ itm.detak_jantung }} /menit</p>
                                                                        <p style="padding:0;margin:0;"><span style="font-weight:500;">Kondisi Lahir :</span> {{ itm.kondisi_lahir }}</p>                                                        
                                                                    </td>
                                                                    <td style="padding:1em;font-weight:400;font-size:11px;color:black;">
                                                                        <input class="uk-checkbox" type="checkbox" v-model="itm.is_aktif" style="border:1px solid black;" @change="removeDataBayi(itm)">
                                                                    </td>
                                                                    <td style="padding:0.4em;margin:0;color:black;font-size:12px;text-align:center;">
                                                                        <button type="button" style="border:none;background-color: white; color:red;" @click.prevent="editDataBayi(itm)"><span uk-icon="file-edit"></span></button>
                                                                        <!-- <button type="button" style="border:none;background-color: white; color:red;" @click.prevent="removeDataBayi(itm)"><span uk-icon="minus-circle"></span></button> -->
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                            <template v-else>
                                                                <tr>
                                                                    <td colspan="4">
                                                                        <p style="font-style: italic;font-size:10px;text-align:center;">belum ada data bayi</p>
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="uk-width-1-1@m uk-grid-small" uk-grid > 
                                                    <div class="uk-width-1-1"  style="border-top:1px solid #ccc;">
                                                        <button style="margin-right:5px;" @click.prevent="cariPasien">Pilih RM Bayi</button>
                                                        <button  @click.prevent="newPasien">Buat RM Bayi</button>
                                                    </div>
                                                    
                                                    <div class="uk-width-1-4@m">
                                                        <dl class="uk-description-list hims-description-list">
                                                            <dt style="color:#000;">No.RM</dt>
                                                            <dd style="color:#000; font-size:14px;">{{dataBayi.no_rm_bayi}}</dd>
                                                        </dl>
                                                    </div>
                                                    <div class="uk-width-1-2@m">
                                                        <dl class="uk-description-list hims-description-list">
                                                            <dt style="color:#000;">Nama Pasien</dt>
                                                            <dd style="color:#000; font-size:14px;">{{dataBayi.nama_bayi}}</dd>
                                                        </dl>
                                                    </div>
                                                    <div class="uk-width-1-4@m">
                                                        <dl class="uk-description-list hims-description-list">
                                                            <dt style="color:#000;">Pasien ID</dt>
                                                            <dd style="color:#000; font-size:14px;">{{dataBayi.pasien_id_bayi}}</dd>
                                                        </dl>
                                                    </div>
                                                    <div class="uk-width-1-1" style="margin:0;padding:0;"></div>
                                                    <div class="uk-width-1-4@m">
                                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                                        <div class="uk-inline uk-width-1-1">
                                                            <select v-model="dataBayi.jk_bayi" class="uk-select uk-form-small" style="color:black;">
                                                                <option value="L">Laki-laki</option>
                                                                <option value="P">Perempuan</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-1-4@m">
                                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Berat Badan Bayi (Kg)</label>
                                                        <div class="uk-inline uk-width-1-1">
                                                            <input class="uk-input uk-form-small" v-model="dataBayi.bb_bayi" type="number" style="color:black;">
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-1-4@m">
                                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Panjang Badan Bayi (Cm)</label>
                                                        <div class="uk-inline uk-width-1-1">
                                                            <input class="uk-input uk-form-small" v-model="dataBayi.tb_bayi" type="number" style="color:black;">
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-1-4@m">
                                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lingkar Kepala (Cm)</label>
                                                        <div class="uk-inline uk-width-1-1">
                                                            <input class="uk-input uk-form-small" v-model="dataBayi.lingkar_kepala" type="number" style="color:black;">
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-1-4@m">
                                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lingkar Dada (Cm)</label>
                                                        <div class="uk-inline uk-width-1-1">
                                                            <input class="uk-input uk-form-small" v-model="dataBayi.lingkar_dada" type="number" style="color:black;">
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-1-4@m">
                                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Frek. Pernapasan (x / Menit)</label>
                                                        <div class="uk-inline uk-width-1-1">
                                                            <input class="uk-input uk-form-small" v-model="dataBayi.frekuensi_napas" type="number" style="color:black;">
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-1-4@m">
                                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Detak Jantung (x / Menit)</label>
                                                        <div class="uk-inline uk-width-1-1">
                                                            <input class="uk-input uk-form-small" v-model="dataBayi.detak_jantung" type="number" style="color:black;">
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-1-4@m">
                                                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kondisi Lahir</label>
                                                        <div class="uk-inline uk-width-1-1">
                                                            <select v-model="dataBayi.kondisi_lahir" class="uk-select uk-form-small" style="color:black;">
                                                                <option value="Sehat">Sehat</option>
                                                                <option value="Kritis">Kritis</option>
                                                                <option value="Meninggal">Meninggal</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="uk-width-1-1" style="text-align:right;">
                                                        <button @click.prevent="dataPersalinanCancelled" style="margin-right:5px;">Batal</button>
                                                        <button @click.prevent="dataPersalinanSaved">Simpan</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div> 
        </div>
            
        <modal-form-pasien ref="modalFormPasien" v-on:pasienSaved="modalPasienSaved"></modal-form-pasien>
        <modal-pasien ref="modalPasien" modalId="modalPasien" v-on:pasienSelected = "fillPasien"></modal-pasien>
        <modal-persalinan ref="modalPersalinan" v-on:dataCreate="dataPersalinanSaved" v-on:dataUpdate="dataPersalinanUpdated"></modal-persalinan>
        
    </div>    
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';
import SubFormTim from '@/Pages/Penunjang/Operasi/Tindakan/SubFormTim.vue';
import SubTindakanOps from '@/Pages/Penunjang/Operasi/Tindakan/SubTindakanOps.vue';
import ModalPasien from '@/Pages/Pendaftaran/components/ModalPasien';

//import ModalPickerRuang from '@/Pages/Pendaftaran/RawatInap/components/ModalPickerRuang';
import ListPasien from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/ListPasien';
import ModalPicker from '@/Components/ModalPicker';

import RowFormRegistrasiLab from '@/Pages/Penunjang/Laboratorium/Pendaftaran/components/RowFormRegistrasiLab.vue';

import ModalFormPasien from '@/Pages/Pendaftaran/Penunjang/components/ModalFormPasien.vue';
import ModalPersalinan from '@/Pages/Penunjang/Operasi/Tindakan/ModalPersalinan.vue';

export default {
    name : 'form-realisasi-ops',
    props : {
        trxId : { type : String, required:true }
    },
    components : { 
        SearchSelect,
        SearchList,
        SubFormTim,
        SubTindakanOps,
        //ModalPickerRuang, 
        ListPasien,
        ModalPicker,
        RowFormRegistrasiLab,
        ModalFormPasien,
        ModalPersalinan,
        ModalPasien
     },
    
    data() {
        return {
            isUpdate : false,
            isLoading : false,
            urlPicker : '/doctors',
            dokterUrl : '/doctors',
            isLocked : false,
            isExpandedReg : false,
            pickerColumns : [ 
                { name : 'dokter_id', title : 'ID', colType : 'text', isCaption : false,colWidth:'100px' },
                { name : 'dokter_nama', title : 'Dokter', colType : 'text', isCaption : true, },
                { name : 'smf.smf_nama',title : 'Smf', colType : 'text', isCaption : false, },         
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            penjaminDesc: [
                { colName : 'penjamin_nama', labelData : '', isTitle : true },
                { colName : 'penjamin_id', labelData : '', isTitle : false },
            ],
            actDesc : [
                { colName : 'tindakan_nama', labelData : '', isTitle : true },
                { colName : 'tindakan_id', labelData : '', isTitle : false },
            ],

            asalPasienList : [
                { value : 'DATANG SENDIRI', title : 'DATANG SENDIRI', isInternal : false, isPasienLuar:false, },
                { value : 'RAWAT JALAN', title : 'RAWAT JALAN', isInternal : true, isPasienLuar:false, },
                { value : 'RAWAT INAP', title : 'RAWAT INAP', isInternal : true, isPasienLuar:false, },
                { value : 'PASIEN LUAR', title : 'PASIEN LUAR', isInternal : false, isPasienLuar: true, },
            ],
            
            registrasi : {
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                client_id : null,
                is_rujukan_int : false,
                jns_registrasi : 'BEDAH_SENTRAL',
                tgl_transaksi : this.getTodayDate(),
                
                tgl_operasi : this.getTodayDate(),
                jam_operasi : null,

                tgl_selesai : this.getTodayDate(),
                jam_selesai : null,

                tgl_anestesi : null,
                jam_anestesi : null,
                
                dokter_unit_id : null,
                dokter_id : null,
                dokter_nama : null,
                
                dokter_pengirim_id: null,
                dokter_pengirim: null,
                
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                bed_id : null,
                no_bed : null,
                
                unit_asal_id : null,
                unit_asal : null,
                ruang_asal_id : null,
                ruang_asal : null,
                bed_asal_id : null,
                no_asal_bed : null,

                cara_registrasi : null,
                asal_pasien : null,
                ket_asal_pasien : null,
                
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                tempat_lahir : null,
                tgl_lahir : null,
                nik : null,
                jns_kelamin : null,
                is_pasien_baru : null,
                is_pasien_luar : false,                
                jns_penjamin : null,
                penjamin_id : null,                
                penjamin_nama : null,

                kelas_harga : null,
                buku_harga_id : null,
                buku_harga : null,
                total : 0,

                is_bpjs : false,
                status : null,
                is_aktif : true,
                client_id : null,
                reff_trx_id : null,
                is_realisasi : false,
                
                tindakan_id : null,
                tindakan_nama : null,
                diagnosa_pra : null,
                diagnosa_pasca : null,
                jenis_operasi : null,
                skala_operasi : null,

                nama_ibu : null,
                nik_ibu : null,
                rm_ibu : null,
                pasien_id : null,
                nama_ayah : null,
                nik_ayah : null,
                alamat : null,

                persalinan : [],

                detail : [],
                tim_operasi : [],
            },

            addedPersalinan : [],

            dataBayi : {
                persalinan_bayi_id : null,
                persalinan_id : null,
                pasien_id_bayi : null,
                nama_bayi : null,
                no_rm_bayi : null,
                jam_kelahiran : null,
                tgl_kelahiran : null,
                umur_kehamilan : null,
                jenis_persalinan : null,
                kelahiran_ke : null,
                kondisi_ibu : null,
                jk_bayi : null,
                bb_bayi : null,
                tb_bayi : null,
                lingkar_kepala : null,
                lingkar_dada : null,
                frekuensi_napas : null,
                detak_jantung : null,
                kondisi_lahir : null,
                is_aktif : true,
            },

            isBayiEdit : false,

            // addedTim : {
            //     dokter_id : null,
            //     dokter_nama : null,
            //     posisi : null,
            //     is_operator : false,
            // },

            addedPersalinanItem : {
                pasien_id : null,
                nama_pasien : null,
                no_rm : null,                
                jk_bayi : null,
                bb_bayi : null,
                tb_bayi : null,
                lingkar_kepala : null,
                lingkar_dada : null,
                frekuensi_napas : null,
                detak_jantung : null,
                kondisi_lahir : null,
                is_aktif : null,

            },

            actUrl : '',
            unitLists : [],
            dokterLists : [],
            roomLists : [],
            bedLists : [],
            kelasHargaLists : [],
            minDate : this.getTodayDate(),
            listTindakanPaket : [],
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelas',['classLists']),
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
        ...mapGetters('operasi',['operasiData']),
        ...mapGetters('asuransi',['asuransiLists']),
        ...mapGetters('dokter',['doctorLists']),
        ...mapGetters('referensi',[
            'isRefExist',
            'jenisPelayananRefs',
            'jenisRegistrasiRefs',
            'asalPasienRefs',
            'caraRegistrasiRefs',
            'salutRefs',
            'jenisPenjaminRefs',
            'hubKeluargaRefs',
            'jenisOperasiRefs',
            'skalaOperasiRefs',
            'timOperasiRefs',
        ]),    
    },

    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapActions('rawatInap',['dataAdmisiInap']),
        ...mapActions('pasien',['dataPasien']),
        ...mapActions('dokter',['listDokter']),
        
        //...mapActions('labRegistrasi',['dataRegistrasi','createRegistrasi','updateRegistrasi','deleteRegistrasi','confirmRegistrasi']),
        
        ...mapActions('operasi',['dataOperasi','createOperasi','deleteOperasi','updateOperasi','confirmOperasi']),
        ...mapActions('unitPelayanan',['listUnitPelayanan','listUnitTindakan']),
        ...mapActions('asuransi',['listAsuransi','dataAsuransi']),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('kelas',['listKelas']),
        ...mapMutations(['CLR_ERRORS']),

        newPasien(){ 
            this.CLR_ERRORS(); 
            this.$refs.modalFormPasien.createNewPasien(); 
        },

        cariPasien(){
            this.CLR_ERRORS();
            this.$refs.modalPasien.showModal();
        },

        newPersalinan(data){ 
            this.CLR_ERRORS(); 
            this.$refs.modalPersalinan.createNewPersalinan(); 
        },

        modalPasienSaved(data) {
            console.log(data);
            let addedVal = JSON.parse(JSON.stringify(data));
            this.addedPersalinan.push(addedVal);
            console.log(this.addedPersalinan);
        },

        modalPasienClosed(data){
            //this.$emit('modalPasienClosed',data);
            // UIkit.modal('#modalFormPasien').hide();
            // this.$refs.modalPersalinan.newData(data);
        },

        // modalPersalinanClosed(data) {
        //     //let addedVal = JSON.parse(JSON.stringify(data));
        //     //this.registrasi.persalinan.push(addedVal);
        // },

        addPersalinan() {
            this.CLR_ERRORS();
            this.$refs.modalPasien.showModal();
            //this.$refs.modalPersalinan.newData(this.addedPersalinanItem);
            
            // if(this.addedMakanan.nama_makanan == "" || this.addedMakanan.nama_makanan == null) {
            //     alert("Data tidak dapat ditambahkan. ada data yang masih kurang.");
            //     return false;
            // }

            // const findItem = this.menuMakanan.foods.find(item => item.nama_makanan == this.addedMakanan.nama_makanan)
            // if(findItem) {
            //     alert("Data sudah ada dalam list.");
            //     return false;
            // }
            // let addedVal = JSON.parse(JSON.stringify(this.addedPersalinan));
            // this.registrasi.persalinan.push(addedVal);
            // this.clearAddedDiet();
        },

        initialize(){
            this.CLR_ERRORS();     
            if(!this.isRefExist) { this.listReferensi({per_page:'ALL'}); }     

            this.listUnitPelayanan({'jenis_reg':this.registrasi.jns_registrasi, 'per_page':'ALL'}).then((response) => {
                if (response.success == true) { 
                    this.unitLists = response.data.data; 
                    console.log('unit list');
                    console.log(response.data);
                }
            })      



            this.listKelas({per_page:'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.kelasHargaLists = response.data.data.filter(function (el) { return el.is_kelas_harga == true; });
                } 
                else { alert(response.message); }
            }) 

            this.editDataOperasi();
        },

        getListTindakanPaket(){
            this.listUnitTindakan(this.operasiData).then((response) => {
                if (response.success == true) {        
                    console.log(response.data.data);            
                    this.listTindakanPaket = response.data.data;
                }
            });
        },

        editDataOperasi() {
            this.CLR_ERRORS();
            this.clearRegistrasi();
            this.isUpdate = true;
            this.isLoading = true;
            if(this.trxId) {
                this.dataOperasi(this.trxId).then((response) => {
                    if (response.success == true) {                    
                        this.fillRegistrasi(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        var x = document.getElementById("tab_persalinan");
                        if(response.data.is_persalinan == false){
                            x.style.display = 'none';
                        }
                        this.getListTindakanPaket();
                    }
                    else { 
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
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

        getTimeNow(){
            let currentDate = new Date();
            let hours = currentDate.getHours();
            if(hours < 10){ hours = `0${hours}` };
            let minutes = currentDate.getMinutes();
            if(minutes < 10){ minutes = `0${minutes}` };
            
            let time = hours + ":" + minutes;
            return time;
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        /** function called before modal closed */
        modalClosed(){
            this.clearRegistrasi();
            this.isUpdate = false;
            //this.$emit('registrasiLabClosed',true);
            UIkit.modal('#modalRealisasiOps').hide();
        },

        formHasilOpsClosed(){
            this.clearRegistrasi();
            this.$router.push({ name: 'listPasienOperasi' }); 
        },
        
        /**modal call from other component for new data entry */
        newData(){
            this.CLR_ERRORS();
            this.isUpdate = false;
            this.clearRegistrasi();
            if(this.$refs.listAsalPasien) {
                this.$refs.listAsalPasien.showMasterPasien();
            }
        },

        // addedDokterSelected(data) {
        //     if(data) {
        //         this.addedTim.dokter_id = data.dokter_id;
        //         this.addedTim.dokter_nama = data.dokter_nama;                   
        //     }
        // },

        searchPasien(){
            this.$emit('searchPasien',true);
        },
        
        /**modal call from other component for update data entry */
        editData(refData) {
            this.CLR_ERRORS();
            this.clearRegistrasi();
            this.isUpdate = true;
            this.isLoading = true;
            if(refData) {
                this.dataOperasi(refData.trx_id).then((response) => {
                    if (response.success == true) {                    
                        this.fillRegistrasi(response.data);
                        //this.actUrl = `units/${this.registrasi.unit_id}/acts`; 
                        this.isUpdate = true;
                        this.isLoading = false;
                        var x = document.getElementById("tab_persalinan");
                        if(response.data.is_persalinan == false){
                            x.style.display = 'none';
                        }
                        this.getListTindakanPaket();
                    }
                    else { 
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
        },

        refreshRegistrasi(){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.dataOperasi(this.trxId).then((response) => {
                if (response.success == true) {
                    this.fillRegistrasi(response.data);
                    this.isLoading = false;
                }
                else { 
                    alert(response.message); 
                    this.isLoading = false; 
                }
            })
        },

        refreshPasien(data){
            // this.CLR_ERRORS();
            // if(data) {
            //     this.isLoading = true;
            //     this.dataPasien(data.pasien_id).then((response) => {
            //         if (response.success == true) {
            //             //UIkit.modal('#modalRealisasiOps').show();
            //             this.fillDataFromMasterPasien(response.data);
            //             this.isUpdate = false;
            //             this.isLoading = false;
            //         }
            //         else { alert('data pasien tidak ditemukan'); this.isLoading = false; }
            //     })                
            // }
        },

        fillDataFromMasterPasien(data) {
            this.registrasi.nama_pasien = data.nama_lengkap;
            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.nik = data.nik;
            this.registrasi.tempat_lahir = data.tempat_lahir;
            this.registrasi.tgl_lahir = data.tgl_lahir;
            this.registrasi.no_rm = data.no_rm;
            this.registrasi.jns_kelamin = data.jns_kelamin;
            
            this.registrasi.penjamin_id = data.penjamin_id;
            this.registrasi.penjamin_nama = data.penjamin_nama;
            this.registrasi.jns_penjamin = data.jns_penjamin;
            this.registrasi.no_kepesertaan = data.no_kepesertaan;
            this.registrasi.asal_pasien = 'DATANG SENDIRI';
            this.registrasi.is_rujukan_int = false;

            this.dataAsuransi(data.penjamin_id).then((response) => {
                if (response.success == true) {
                    this.registrasi.buku_harga_id = response.data.buku_harga_id;
                    this.registrasi.buku_harga = response.data.buku_harga;
                }
                else { alert('data penjamin tidak ditemukan'); }
            })   

            if(this.unitLists.length == 1) {
                this.registrasi.unit_id = this.unitLists[0].unit_id;
                this.registrasi.unit_nama = this.unitLists[0].unit_nama;
                this.roomLists = this.unitLists[0].ruang;
                this.dokterLists = this.unitLists[0].dokter;
                if(this.roomLists.length == 1) {
                    this.registrasi.ruang_id = this.roomLists[0].ruang_id;
                    this.registrasi.ruang_nama = this.roomLists[0].ruang_nama;
                }
                if(this.dokterLists.length == 1) {
                    this.registrasi.dokter_id = this.dokterLists[0].dokter_id;
                    this.registrasi.dokter_nama = this.dokterLists[0].dokter_nama;
                }   
                this.actUrl = `units/${this.registrasi.unit_id}/acts`;              
            }
        },

        refreshPasienInap(data) {
            this.CLR_ERRORS();
            this.clearRegistrasi();
            if(data){                
                this.isLoading = true;
                this.dataAdmisiInap(data.trx_id).then((response) => {
                    if (response.success == true) {
                        UIkit.modal('#modalRealisasiOps').show();
                        this.fillDataFromPasienInap(response.data);
                        this.isUpdate = false;
                        this.isLoading = false;
                        //UIkit.switcher('#switcherFormRegLab').show(1);
                    }
                    else { alert('data pasien tidak ditemukan'); this.isLoading = false; }
                })       
            }
        },

        fillDataFromPasienInap(data){
            this.registrasi.reg_id = data.reg_id;
            this.registrasi.reff_trx_id = data.trx_id;
            
            this.registrasi.trx_id = null;
            this.registrasi.sub_trx_id = null;
            
            
            /*isi unit dari rawat inap*/
            this.registrasi.unit_asal_id = data.unit_id;
            this.registrasi.unit_asal = data.unit_nama;
            this.registrasi.ruang_asal_id = data.ruang_id;
            this.registrasi.ruang_asal = data.ruang_nama;
            this.registrasi.bed_asal_id = data.bed_asal_id;
            this.registrasi.no_asal_bed = data.no_bed;
            
            this.registrasi.kelas_harga = data.kelas_harga_id;
            this.registrasi.dokter_pengirim_id = data.dokter_id;
            this.registrasi.dokter_pengirim = data.dokter_nama;
            
            this.registrasi.nama_pasien = data.nama_pasien;
            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.nik = data.pasien.nik;
            this.registrasi.tempat_lahir = data.pasien.tempat_lahir;
            this.registrasi.tgl_lahir = data.pasien.tgl_lahir;
            this.registrasi.no_rm = data.pasien.no_rm;
            this.registrasi.jns_kelamin = data.pasien.jns_kelamin;
            
            this.registrasi.penjamin_id = data.penjamin_id;
            this.registrasi.penjamin_nama = data.penjamin_nama;
            this.registrasi.jns_penjamin = data.jns_penjamin;
            this.registrasi.no_kepesertaan = data.no_kepesertaan;
            this.registrasi.asal_pasien = 'RAWAT_INAP';
            this.registrasi.is_rujukan_int = true;

            this.dataAsuransi(data.penjamin_id).then((response) => {
                if (response.success == true) {
                    this.registrasi.buku_harga_id = response.data.buku_harga_id;
                    this.registrasi.buku_harga = response.data.buku_harga;
                }
                else { alert('data penjamin tidak ditemukan'); }
            })
            if(this.unitLists.length == 1) {
                this.registrasi.unit_id = this.unitLists[0].unit_id;
                this.registrasi.unit_nama = this.unitLists[0].unit_nama;
                this.roomLists = this.unitLists[0].ruang;
                this.dokterLists = this.unitLists[0].dokter;
                if(this.roomLists.length == 1) {
                    this.registrasi.ruang_id = this.roomLists[0].ruang_id;
                    this.registrasi.ruang_nama = this.roomLists[0].ruang_nama;
                }
                if(this.dokterLists.length == 1) {
                    this.registrasi.dokter_id = this.dokterLists[0].dokter_id;
                    this.registrasi.dokter_nama = this.dokterLists[0].dokter_nama;
                } 
                this.actUrl = `units/${this.registrasi.unit_id}/acts`;               
            }
        },

        refreshPasienPoli(data){
            this.CLR_ERRORS();
            this.clearRegistrasi();
        },

        clearRegistrasi() {
            this.registrasi.reg_id = null;
            this.registrasi.trx_id = null;
            this.registrasi.sub_trx_id = null;
            this.registrasi.client_id = null;
            this.registrasi.is_rujukan_int = false;
            this.registrasi.jns_registrasi = 'OPERASI';
            this.registrasi.tgl_transaksi = this.getTodayDate();
            this.registrasi.tgl_periksa = this.getTodayDate();
            this.registrasi.jam_periksa = this.getTimeNow();
                
            this.registrasi.dokter_unit_id = null;
            this.registrasi.dokter_id = null;
            this.registrasi.dokter_nama = null;
                
            this.registrasi.dokter_pengirim_id= null;
            this.registrasi.dokter_pengirim= null;
                
            this.registrasi.unit_id = null;
            this.registrasi.unit_nama = null;
            this.registrasi.ruang_id = null;
            this.registrasi.ruang_nama = null;
            this.registrasi.bed_id = null;
            this.registrasi.no_bed = null;
                
            this.registrasi.unit_asal_id = null;
            this.registrasi.unit_asal = null;
            this.registrasi.ruang_asal_id = null;
            this.registrasi.ruang_asal = null;
            this.registrasi.bed_asal_id = null;
            this.registrasi.no_asal_bed = null;

            this.registrasi.cara_registrasi = null;
            this.registrasi.asal_pasien = null;
            this.registrasi.ket_asal_pasien = null;
                
            this.registrasi.pasien_id = null;
            this.registrasi.no_rm = null;
            this.registrasi.nama_pasien = null;
            this.registrasi.tempat_lahir = null;
            this.registrasi.tgl_lahir = null;
            this.registrasi.nik = null;
            this.registrasi.jns_kelamin = null;

            this.registrasi.is_pasien_baru = null;
            this.registrasi.is_pasien_luar = false;
                
            this.registrasi.jns_penjamin = null;
            this.registrasi.penjamin_id = null;      
            this.registrasi.buku_harga_id = null;    
            this.registrasi.total = 0;      
                      
            this.registrasi.penjamin_nama = null;
            this.registrasi.is_bpjs = false;
            this.registrasi.status = null;
            this.registrasi.is_aktif = true;
            this.registrasi.client_id = null;
            this.registrasi.reff_trx_id = null;
            this.registrasi.is_realisasi = false;

            this.registrasi.tindakan_id = null;
            this.registrasi.tindakan_nama = null;
            this.registrasi.diagnosa_pra = null;
            this.registrasi.diagnosa_pasca = null;
            this.registrasi.jenis_operasi = null;
            this.registrasi.skala_operasi = null;
                
            this.registrasi.detail = [];
            this.registrasi.tim_operasi = [];
            
            this.minDate = this.getTodayDate();   

        },

        fillRegistrasi(data){
            console.log(data);
            let unit = this.unitLists.find( item => item.unit_id == data.unit_id );
            this.listAsuransi({per_page:'ALL',jns_penjamin:data.jns_penjamin, is_aktif:true }).then((response) => {});
            this.actUrl = `units/${data.unit_id}/acts`;
            if(unit) { 
                this.roomLists = unit.ruang; 
                this.dokterLists = unit.dokter; 
                if(data.ruang_id) {
                    let room = this.roomLists.find(item => item.ruang_id == data.ruang_id);
                    if(room) {
                        this.bedLists = room.beds;
                    }
                }
            }
            
            this.registrasi.reg_id = data.reg_id;
            this.registrasi.trx_id = data.trx_id;
            this.registrasi.sub_trx_id = data.sub_trx_id;
            this.registrasi.is_rujukan_int = data.is_rujukan_int;
            
            this.registrasi.client_id = data.client_id;
            this.registrasi.jns_registrasi = data.jns_registrasi;
            this.registrasi.cara_registrasi = data.cara_registrasi;
            this.registrasi.tgl_transaksi = data.tgl_transaksi;
                
            this.registrasi.tgl_operasi = data.tgl_operasi;
            this.registrasi.jam_operasi = data.jam_operasi;
                
            this.registrasi.dokter_unit_id = data.dokter_unit_id;
            this.registrasi.dokter_id = data.dokter_id;
            this.registrasi.dokter_nama = data.dokter_nama;
            this.registrasi.dokter_pengirim_id = data.dokter_pengirim_id;
            this.registrasi.dokter_pengirim = data.dokter_pengirim;
                
            this.registrasi.unit_id = data.unit_id;
            this.registrasi.unit_nama = data.unit_nama;
            this.registrasi.ruang_id = data.ruang_id;
            this.registrasi.ruang_nama = data.ruang_nama;
            this.registrasi.bed_id = data.bed_id;
            this.registrasi.no_bed = data.no_bed;
            
            this.registrasi.unit_asal_id = data.unit_asal_id;
            this.registrasi.unit_asal = data.unit_asal;
            this.registrasi.ruang_asal_id = data.ruang_asal_id;
            this.registrasi.ruang_asal = data.ruang_asal;
            this.registrasi.bed_asal_id = data.bed_asal_id;
            this.registrasi.no_asal_bed = data.no_asal_bed;
            
            this.registrasi.kelas_harga = data.kelas_harga;
            //this.registrasi.total = data.transaksi.total;
            
            this.registrasi.asal_pasien = data.asal_pasien;
            this.registrasi.ket_asal_pasien = data.ket_asal_pasien;

            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.nama_pasien = data.nama_pasien;
            this.registrasi.no_rm = data.no_rm;
            this.registrasi.jns_kelamin = data.jns_kelamin;
            this.registrasi.nik = data.nik;
            
            this.registrasi.tempat_lahir = data.tempat_lahir;
            this.registrasi.tgl_lahir = data.tgl_lahir;
            
            this.registrasi.is_pasien_baru = data.is_pasien_baru;
            this.registrasi.is_pasien_luar = data.is_pasien_luar;
                
            this.registrasi.jns_penjamin = data.jns_penjamin;
            this.registrasi.penjamin_id = data.penjamin_id;                
            this.registrasi.penjamin_nama = data.penjamin_nama;
            this.registrasi.no_kepesertaan = data.no_kepesertaan;
            this.registrasi.is_bpjs = data.is_bpjs;

            this.registrasi.buku_harga = data.buku_harga;
            this.registrasi.buku_harga_id = data.buku_harga_id;
                
            this.registrasi.status = data.status;
            this.registrasi.is_aktif = data.is_aktif;
            this.registrasi.client_id = data.client_id;
            this.registrasi.is_realisasi = data.is_realisasi;
            
            this.registrasi.tindakan_id = data.tindakan_id;
            this.registrasi.tindakan_nama = data.tindakan_nama;
            this.registrasi.diagnosa_pasca = data.diagnosa_pasca;
            this.registrasi.diagnosa_pra = data.diagnosa_pra;
            this.registrasi.jenis_operasi = data.jenis_operasi;
            this.registrasi.skala_operasi = data.skala_operasi;
            
            this.registrasi.tim_operasi = data.tim_operasi;

            this.registrasi.detail = data.detail;
            this.minDate = this.registrasi.tgl_operasi;
            this.isUpdate = true;

            this.registrasi.nama_ibu = data.nama_pasien;
            this.registrasi.nik_ibu = data.nik;
            this.registrasi.rm_ibu = data.no_rm;
            this.registrasi.alamat = data.alamat;
        },

        ambilPenjaminList(){
            this.registrasi.penjamin_id = null;
            this.registrasi.penjamin_nama = null;
            let dt = this.jenisPenjaminRefs.json_data.find(item => item.value === this.registrasi.jns_penjamin);
            if(dt) { this.registrasi.is_bpjs = dt.is_bpjs; }            
            this.listAsuransi({per_page:'ALL',jns_penjamin:this.registrasi.jns_penjamin, is_aktif:true }).then((response) => {
                console.log(response.data);
            });
        },

        penjaminSelected(data){
            if(data) {
                this.registrasi.penjamin_id = data.penjamin_id;
                this.registrasi.penjamin_nama = data.penjamin_nama;
                this.registrasi.buku_harga_id = data.buku_harga_id;
                this.registrasi.buku_harga = data.buku_harga;
                
                this.registrasi.is_bpjs = false;
                if(data.is_bpjs) {
                    this.registrasi.is_bpjs = data.is_bpjs;
                }

                if(this.isUpdate) { this.submisiRegOps(); }
            }
        },

        actSelected(data) {
            this.operasiData.tindakan_operasi_id = null;
            this.operasiData.tindakan_operasi = null;
            if(data) {
                this.operasiData.tindakan_operasi_id = data.tindakan_id;
                this.operasiData.tindakan_operasi = data.tindakan_nama;
            }
        },

        ruangSelected() {
            let ruangSelected = this.roomLists.find(item => item.ruang_id === this.registrasi.ruang_id);
            if(ruangSelected) {
                this.registrasi.ruang_nama = ruangSelected.ruang_nama;
                this.bedLists = ruangSelected.beds;                
            }
        },

        dokterSelected() {
            let data = this.dokterLists.find( item => item.dokter_id == this.registrasi.dokter_id );
            if(data) {
                this.registrasi.dokter_id = data.dokter_id;
                this.registrasi.dokter_nama = data.dokter_nama;
                this.registrasi.dokter_unit_id = data.dokter_unit_id;
            }
        },

        dokterPengirimSelected(data){
            this.registrasi.dokter_pengirim_id = null;
            this.registrasi.dokter_pengirim = null;

            if(data) {
                this.registrasi.dokter_pengirim_id = data.dokter_id;
                this.registrasi.dokter_pengirim = data.dokter_nama;
            }
        },

        unitSelected() {
            let data = this.unitLists.find( item => item.unit_id == this.registrasi.unit_id );
            if(data) {
                console.log(data);
                this.registrasi.unit_id = data.unit_id;
                this.registrasi.unit_nama = data.unit_nama;
                this.roomLists = data.ruang;
                this.dokterLists = data.dokter;
                this.registrasi.ruang_id = null;
                this.registrasi.ruang_nama = null;
                this.registrasi.bed_id = null;
                this.registrasi.no_bed = null;
                this.actUrl = `units/${this.registrasi.unit_id}/acts`;
            }
        },

        searchTindakan(data){
            this.urlPicker = `/doctors`;
            this.$refs.docModalPicker.showModalPicker(this.urlPicker);
        },

        updateKelasHarga(){
            if(this.isUpdate){
                this.submisiRegOps();
            }
        },

        asalPasienChange(){
            let val = this.asalPasienList.find(item => item.value == this.registrasi.asal_pasien );
            if(val) {
                this.registrasi.is_rujukan_int = val.isInternal;
                this.registrasi.is_pasien_luar = val.isPasienLuar;
            }
        },

        submisiRegOps(){
            alert('submisi');
            UIkit.switcher('#switcherFormOperasi').show(0);
            this.CLR_ERRORS();
            if(this.isUpdate) {
                this.isLoading = true;
                this.updateOperasi(this.operasiData).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        this.isUpdate = true;
                        this.fillRegistrasi(response.data);
                        alert('Data berhasil disimpan.');
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
            else {
                //data baru
                this.isLoading = true;
                this.createOperasi(this.operasiData).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        this.isUpdate = true;
                        this.fillRegistrasi(response.data);
                        alert('Data berhasil disimpan.');
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
        },

        hapusRegistrasi(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus data registrasi ${this.registrasi.sub_trx_id} - ${this.registrasi.nama_pasien}. Lanjutkan ?`)){
                this.isLoading = true;
                this.deleteOperasi(this.registrasi.trx_id).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; this.isUpdate = false;
                        alert('jadwal operasi BERHASIL dihapus.');
                        this.modalClosed();
                    }
                    else { 
                        alert('jadwal operasi tidak berhasil dihapus.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        lockData(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan MENGUNCI data registrasi ${this.registrasi.trx_id} - ${this.registrasi.pasien.nama_pasien} dan membuat data transaksi dari pemakaian bed. Lanjutkan ?`)){
                this.isLoading = true;
                this.confirmOperasi(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('jadwal operasi BERHASIL dikonfirmasi.');
                        this.modalClosed();
                    }
                    else { 
                        alert('jadwal operasi tidak berhasil dikonfirmasi.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        // posisiChange(data) {
        //     let val = this.timOperasiRefs.json_data.find(item=> item.value == data.posisi);
        //     if(val) {
        //         data.is_operator = val.is_operator;
        //         if(val.is_operator) {
        //             this.registrasi.dokter_id = data.dokter_id;
        //             this.registrasi.dokter_nama = data.dokter_nama;
        //         }
        //     }
        // },

        // addTimOperasi(){
        //     this.CLR_ERRORS();
        //     let exist = this.registrasi.tim_operasi.find(item => item.dokter_id == this.addedTim.dokter_id);
        //     if(!exist) {
        //         this.registrasi.tim_operasi.push({
        //             tim_operasi_id : null,
        //             reg_id : null,
        //             trx_id : null,
        //             sub_trx_id : null,
        //             dokter_id : this.addedTim.dokter_id,
        //             dokter_nama : this.addedTim.dokter_nama,
        //             posisi : null,
        //             is_operator : false,
        //             is_aktif : true,
        //         });
        //         this.addedTim.dokter_id = null;
        //         this.addedTim.dokter_nama = null;
        //     }
        //     else { 
        //         exist.is_aktif = true;
        //         alert('dokter sudah didalam list tim operasi.');
        //         this.addedTim.dokter_id = null;
        //         this.addedTim.dokter_nama = null;
        //     }

        //     // if(dataVal.length < 1) { return false; }
        //     // else {
        //     //     let addedTim = JSON.parse(JSON.stringify(dataVal));
        //     //     addedTim.forEach(data => {
        //     //         let exist = this.registrasi.tim_operasi.find(item => item.dokter_id == data.dokter_id);
        //     //         if(!exist) {
        //     //             this.registrasi.tim_operasi.push({
        //     //                 tim_operasi_id : null,
        //     //                 reg_id : null,
        //     //                 trx_id : null,
        //     //                 sub_trx_id : null,
        //     //                 dokter_id : data.dokter_id,
        //     //                 dokter_nama : data.dokter_nama,
        //     //                 posisi : null,
        //     //                 is_operator : false,
        //     //                 is_aktif : true,
        //     //             });
        //     //         }
        //     //         else { exist.is_aktif = true; }
        //     //     });
        //     // }
        // },

        konfirmasiReg(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan Mengubah status data registrasi ${this.operasiData.trx_id} - ${this.operasiData.nama_pasien}. Lanjutkan ?`)){
                this.isLoading = true;
                this.confirmOperasi(this.operasiData).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('jadwal operasi BERHASIL dikonfirmasi.');
                        //this.modalClosed();
                    }
                    else { 
                        alert('jadwal operasi tidak berhasil dikonfirmasi.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        // changeActivationActItem(){
        //     this.registrasi.detail = this.registrasi.detail.filter(
        //         item => { return item.detail_id !== null || item.is_aktif == true }
        //     );
        // },

        // calculateTotalLab(){  
        //     let total = 0;          
        //     this.registrasi.detail.forEach(data => {
        //         total = (total*1) + (data.subtotal*1);
        //         this.registrasi.total = total; 
        //     })
        // },

        // timRowAktifChange(data){
        //     this.registrasi.tim_operasi = this.registrasi.tim_operasi.filter(
        //         item => { return item.detail_id !== null || item.is_aktif == true }
        //     );
        // }

        // modalPasienClosed(){

        // },

        fillPasien(data){
            // if(data) {
                //alert('new data persalinan');
                // let buff 
                this.dataBayi.pasien_id_bayi = data.pasien_id;
                this.dataBayi.no_rm_bayi = data.no_rm;
                this.dataBayi.nama_bayi = data.nama_pasien;                
                //this.$refs.modalPersalinan.newData(JSON.parse(JSON.stringify(this.addedPersalinanItem)));
                // this.$refs.modalPasien.closeModal();
                // this.$refs.modalPersalinan.newData(buff);
                
            // }
        },

        dataPersalinanSaved() {
            if(this.isBayiEdit) {
                this.dataPersalinanUpdated();
            }
            else {
                this.addNewDataPersalinan();
            }
        },

        addNewDataPersalinan(data){
            this.operasiData.persalinan.detail.push(JSON.parse(JSON.stringify(this.dataBayi)));
            this.clrDataBayi();
        },
        
        dataPersalinanUpdated() {
            alert('data persalinan update');
            let selData = this.operasiData.persalinan.detail.filter(item =>{return item.pasien_id !== this.dataBayi.pasien_id });
            console.log(selData);
            selData.push(JSON.parse(JSON.stringify(this.dataBayi)));
            this.operasiData.persalinan.detail = JSON.parse(JSON.stringify(selData));
            this.clrDataBayi();
        },
        

        dataPersalinanCancelled() {
            this.isBayiEdit = false;
            this.clrDataBayi();
        },

        clrDataBayi(){
            this.isBayiEdit = false;
            this.dataBayi.persalinan_bayi_id = null;
            this.dataBayi.persalinan_id = null;
            
            this.dataBayi.pasien_id_bayi = null;
            this.dataBayi.nama_bayi = null;
            this.dataBayi.no_rm_bayi = null;
            this.dataBayi.jam_kelahiran = null;
            this.dataBayi.tgl_kelahiran = null;
            this.dataBayi.umur_kehamilan = null;
            this.dataBayi.jenis_persalinan = null;
            this.dataBayi.kelahiran_ke = null;
            this.dataBayi.kondisi_ibu = null;
            this.dataBayi.jk_bayi = null;
            this.dataBayi.bb_bayi = null;
            this.dataBayi.tb_bayi = null;
            this.dataBayi.lingkar_kepala = null;
            this.dataBayi.lingkar_dada = null;
            this.dataBayi.frekuensi_napas = null;
            this.dataBayi.detak_jantung = null;
            this.dataBayi.kondisi_lahir = null;
            this.dataBayi.is_aktif = true;
        },

        editDataBayi(data) {
            if(data) {
                this.clrDataBayi();
                this.dataBayi = JSON.parse(JSON.stringify(data));
                this.isBayiEdit = true;
            }
        },

        removeDataBayi(data) {
            if(data) {
                this.operasiData.persalinan.detail = this.operasiData.persalinan.detail.filter(item => { return item.is_aktif == false || ( item.persalinan_bayi_id !== null && item.persalinan_bayi_id !== '') });
            }
        },

        isPersalinanChange(){
            let comp = this.$refs.tab_persalinan;
            if(this.operasiData.is_persalinan) {
                comp.style.display = "";
            }
            else {
                comp.style.display = "none";
            }
        }
        
    }
}
</script>
<style>
th.tb-header-reg {
    padding:1em; 
    background-color:#cc0202; 
    color:white;
}
</style>