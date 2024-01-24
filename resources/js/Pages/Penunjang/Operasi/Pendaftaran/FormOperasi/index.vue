<template>
    <div class="uk-container">
        <div class="uk-grid-small" uk-grid style="margin:0;padding:1em;">
            <div class="uk-width-auto" style="padding:0.2em 0 0 0;">
                <a href="#" @click.prevent="formClosed" style="background-color:white;padding:0.5em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:1"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <header-page title="REGISTRASI OPERASI" subTitle="pendaftaran jadwal tindakan operasi."></header-page>
            </div>
        </div>
        <div style="padding:0.5em 1em 1em 1em; margin:0;background-color: #fff;">
            <ul id="switcherRegistrasiOps" class="uk-tab hims-operasi-tab" uk-switcher style="padding:0;margin:0;">
                <li v-if="!isUpdate"><div><h5><a href="#">Pilih Pasien</a></h5></div></li>
                <li><div><h5><a href="#">Form Registrasi</a></h5></div></li>
            </ul>
            <ul class="uk-switcher" style="padding:0;margin:0;">
                <li v-if="!isUpdate">
                    <list-pasien ref="listAsalPasien"
                        v-on:pasienInapSelected="refreshPasienInap" 
                        v-on:pasienSelected="refreshPasien" 
                        v-on:pasienPoliSelected="refreshPasienPoli">
                    </list-pasien>
                </li>
                <li>
                    <form action="" @submit.prevent="submisiRegOperasi" style="margin:0;padding:0;">
                        <div class="hims-form-header" style="padding:0.5em 0.5em 0.5em 0.5em;">
                            <div style="padding:0;margin:0;">  
                                <div class="uk-grid-small uk-box-shadow-large" uk-grid style="padding:0;margin:0;background-color:#cc0202;">
                                    <button type="button" class="uk-button-large hims-form-reg-btn uk-width-auto" @click.prevent="formClosed" style="padding:0.5em;"><span uk-icon="arrow-left"></span> Tutup</button>
                                    <button type="button" class="uk-button-large hims-form-reg-btn uk-width-auto" @click="hapusRegistrasi" v-if="isUpdate" style="padding:0.5em;"><span uk-icon="trash"></span> Hapus</button>
                                    <span class="uk-width-expand@m" style="background-color:transparent !!important;"></span>
                                    <button :disabled="isLoading" type="submit" class="uk-button-large hims-form-reg-btn uk-width-auto" style="padding:0.5em;"><span uk-icon="file-text"></span> Simpan</button>
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
                                                <input class="uk-input uk-form-small" v-model="registrasi.nama_pasien" 
                                                    type="text" aria-label="pilih pasien" 
                                                    style="color:black;" 
                                                    disabled>
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
                                                <input class="uk-input uk-form-small" type="text" v-model="registrasi.pasien_id" style="color:black;" disabled>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="registrasi.jns_kelamin" class="uk-select uk-form-small" required>
                                                    <option value="L">Laki-laki</option>
                                                    <option value="P">Perempuan</option>
                                                    <option value="-">-</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir*</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" v-model="registrasi.tempat_lahir" style="color:black;">
                                            </div>
                                        </div>
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Lahir*</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_lahir" style="color:black;" :max="minDate">
                                            </div>
                                        </div>
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Penjamin*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="registrasi.jns_penjamin" class="uk-select uk-form-small" required @change="ambilPenjaminList">
                                                    <option v-if="isRefExist" v-for="dt in jenisPenjaminRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <search-list
                                                :config = configSelect
                                                :dataLists = asuransiLists.data
                                                label = "Penjamin / Asuransi*"
                                                placeholder = "pilih penjamin (asuransi)"
                                                captionField = "penjamin_nama"
                                                valueField = "penjamin_nama"
                                                :detailItems = penjaminDesc
                                                :value = "registrasi.penjamin_nama"
                                                v-on:item-select = "penjaminSelected"
                                            ></search-list>
                                        </div>
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Kepesertaan*</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="registrasi.no_kepesertaan" type="text" style="color:black;" required>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK (KTP / ID Card)*</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="registrasi.nik" type="text" style="color:black;" required>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-1" style="padding:0;margin:0;text-align: center;">
                                            <p style="color:black; font-size: 10px; font-style: italic;padding:0;margin-bottom:0;">
                                                {{registrasi.buku_harga_id}} {{registrasi.buku_harga}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                        
                                <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                                    <div class="uk-width-1-4@m" style="padding:0 0.5em 0.5em 0.5em;">
                                        <h5 style="color:indigo;padding:0;margin:0;">KELAS DAN RUANG OPERASI</h5>
                                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                            detail data ruang dan kelas operasi.
                                        </p>
                                    </div>
                                    <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                        
                                        <div class="uk-width-1-2@m">
                                            <search-select
                                                :config = configItemSelect
                                                :searchUrl = dokterUrl
                                                label = "Dokter Pengirim"
                                                placeholder = "dokter pengirim"
                                                captionField = "dokter_nama"
                                                valueField = "dokter_nama"
                                                :itemListData = dokterDesc
                                                :value = "registrasi.dokter_pengirim"
                                                v-on:item-select = "dokterPengirimSelected"
                                            ></search-select>
                                        </div>

                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Layanan*</label>
                                            <div class="uk-form-controls">
                                                <select v-model = "registrasi.kelas_harga_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;" required :disabled="isLocked" @change.prevent="updateKelasHarga">
                                                    <option v-for="option in kelasHargaLists" v-bind:value="option.kelas_id">{{option.kelas_nama}}</option>
                                                </select>
                                            </div>
                                        </div>                                        
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Asal Pasien</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" v-model="registrasi.asal_pasien" style="color:black;"  disabled>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="registrasi.unit_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="unitSelected" required> 
                                                    <option v-for="option in unitLists" v-bind:value="option.unit_id" v-bind:key="option.unit_id">{{option.unit_nama}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Ruang*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="registrasi.ruang_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="ruangSelected" required> 
                                                    <option v-for="option in roomLists" v-bind:value="option.ruang_id" v-bind:key="option.ruang_id">{{option.ruang_nama}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Bed*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="registrasi.bed_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="bedSelected" required> 
                                                    <option v-for="option in bedLists" v-bind:value="option.bed_id" v-bind:key="option.bed_id">{{option.no_bed}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Tindakan*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="registrasi.jenis_operasi" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" required> 
                                                    <option v-for="dt in jenisOperasiRefs.json_data" v-bind:value="dt.value" v-bind:key="dt.value">{{dt.text}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Skala Tindakan*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="registrasi.skala_operasi" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" required> 
                                                    <option v-for="dt in skalaOperasiRefs.json_data" v-bind:value="dt.value" v-bind:key="dt.value">{{dt.text}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <search-select
                                                :config = configSelect
                                                :searchUrl = actUrl
                                                label = "Paket Operasi*"
                                                placeholder = "pilih paket operasi"
                                                captionField = "tindakan_nama"
                                                valueField = "tindakan_nama"
                                                :itemListData = actDesc
                                                :value = "registrasi.tindakan_operasi"
                                                v-on:item-select = "actSelected"
                                            ></search-select>
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
                                                <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_operasi" style="color:black;" :min="minDate" required>
                                            </div>
                                        </div>

                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Operasi*</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="time" v-model="registrasi.jam_operasi" style="color:black;" required>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Operator*</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" v-model="registrasi.dokter_nama" style="color:black;" disabled required>
                                            </div>
                                        </div>
                                        
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diag. Pra Operasi</label>
                                            <div class="uk-form-controls">
                                                <textarea class="uk-textarea uk-form-small" type="text" v-model="registrasi.diagnosa_pra" style="color:black;"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diag. Pasca Operasi</label>
                                            <div class="uk-form-controls">
                                                <textarea class="uk-textarea uk-form-small" type="text" v-model="registrasi.diagnosa_pasca" style="color:black;"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Selesai</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_selesai" style="color:black;" :min="minDate">
                                            </div>
                                        </div>

                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Selesai</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="time" v-model="registrasi.jam_selesai" style="color:black;">
                                            </div>
                                        </div>

                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Anestesi</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_anestesi" style="color:black;" :min="minDate">
                                            </div>
                                        </div>

                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Anestesi</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="time" v-model="registrasi.jam_anestesi" style="color:black;">
                                            </div>
                                        </div>
                                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin-top:1em;">
                                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="registrasi.is_persalinan" style="margin-left:5px;"> Operasi Persalinan</label>
                                        </div>
                                        <div class="uk-width-1-1" style="padding:0;margin:0;text-align: center;">
                                            <p style="color:black; font-size: 10px; font-style: italic;padding:0;margin-bottom:0;">
                                                {{registrasi.reff_trx_id}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-grid-small hims-form-subpage" uk-grid style="padding-bottom: 0;margin-bottom: 0;">                        
                                    <div class="uk-width-1-4@m" style="padding:0 0.5em 0 0.5em;">
                                        <h5 style="color:indigo;padding:0;margin:0;">TIM OPERASI</h5>
                                    </div>
                                    <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">  
                                        <div class="uk-width-1-2@m">
                                            <search-list
                                                :config = configItemSelect
                                                :dataLists = dokterLists
                                                placeholder = "pilih dokter unit..."
                                                captionField = "dokter_nama"
                                                valueField = "dokter_nama"
                                                :detailItems = dokterDesc
                                                :value = "addedTim.dokter_nama"
                                                v-on:item-select = "addedDokterSelected"
                                            ></search-list>
                                        </div>
                                        <div class="uk-width-auto@m">
                                            <button @click.prevent="addTimOperasi" :disabled="isLoading">Tambah Tim</button>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="uk-grid-small hims-form-subpage" uk-grid style="border:none;margin-top:0;padding-top:0.2em;">
                                    <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                        <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                            <thead>
                                                <th class="tb-header-reg" style="width:100px;text-align:left;color:white;">ID</th>
                                                <th class="tb-header-reg" style="text-align:left;color:white;">Dokter</th>
                                                <th class="tb-header-reg" style="text-align:center;color:white;">Posisi</th>  
                                                <th class="tb-header-reg" style="text-align:center;color:white;">Opr</th>                                              
                                                <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                                            </thead>
                                            <tbody>                                               
                                                <tr v-for="row in registrasi.tim_operasi" v-bind:class="row.is_aktif != true ? 'inaktif':'' ">
                                                    <td style="width:150px;font-size:12px;color:black; padding:0.5em;margin:0;font-weight: 500;">{{row.dokter_id}}</td>
                                                    <td style="font-size:12px;color:black; padding:0.5em;margin:0;">{{row.dokter_nama}}</td>
                                                    <td style="width:200px;font-size:12px;color:black;padding:0.2em;margin:0;">
                                                        <select v-model="row.posisi" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="posisiChange(row)"> 
                                                            <option v-for="pos in timOperasiRefs.json_data" v-bind:value="pos.value" v-bind:key="pos.value">{{pos.text}}</option>
                                                            <option value=""></option>
                                                        </select>
                                                    </td>
                                                    <td style="width:50px;text-align: center;padding:0.3em 0.2em 0.3em 0.2em;margin:0;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="row.is_operator" disabled style="border:1px solid black;">
                                                    </td>
                                                    <td style="width:50px;text-align: center;padding:0.3em 0.2em 0.3em 0.2em;margin:0;">
                                                        <input class="uk-checkbox" type="checkbox" v-model="row.is_aktif" style="border:1px solid black;" @change="timRowAktifChange(row)">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </li>
            </ul>                
        </div>
    </div>
</template>
<script>

import { mapGetters, mapActions, mapMutations } from 'vuex';
import HeaderPage from '@/Components/HeaderPage.vue';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

import ListPasien from '@/Pages/Penunjang/Operasi/components/ListPasien';

export default {
    name : 'form-operasi',
    components: {
        HeaderPage,
        SearchSelect,
        SearchList,
        ListPasien,
    },
    data() {
        return {
            isUpdate : false,
            isLoading : false,
            dataId : 'baru',
            tipeData : 'form',
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
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
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
                reff_trx_id : null,
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
                is_pasien_baru : false,
                is_pasien_luar : false,                
                jns_penjamin : null,
                penjamin_id : null,                
                penjamin_nama : null,
                kelas_harga_id : null,
                kelas_harga : null,
                buku_harga_id : null,
                buku_harga : null,
                total : 0,
                is_bpjs : false,
                status : null,
                is_aktif : true,
                client_id : null,
                is_realisasi : false,                
                tindakan_operasi_id : null,
                tindakan_operasi : null,
                diagnosa_pra : null,
                diagnosa_pasca : null,
                jenis_operasi : null,
                skala_operasi : null,
                is_persalinan : false,
                detail : [],
                tim_operasi : [],
            },
            addedTim : {
                dokter_id : null,
                dokter_nama : null,
                posisi : null,
                is_operator : false,
            },

            actUrl : '',
            unitLists : [],
            dokterLists : [],
            roomLists : [],
            bedLists : [],
            kelasHargaLists : [],
            minDate : this.getTodayDate(),
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelas',['classLists']),
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
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
            'jenisOperasiRefs',
            'skalaOperasiRefs',
            'timOperasiRefs',
        ]),    
    },
    mounted() {     
        this.setParamForm(this.$route.params);
    },
    methods : {
        ...mapActions('rawatInap',['dataAdmisiInap']),
        ...mapActions('pendaftaran',['dataRegistrasi']),
        ...mapActions('pasien',['dataPasien']),
        ...mapActions('operasi',['dataOperasi','createOperasi','deleteOperasi','updateOperasi']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('asuransi',['listAsuransi','dataAsuransi']),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('kelas',['listKelas']),
        ...mapMutations(['CLR_ERRORS']),


        setParamForm(data){
            this.initialize();
            this.dataId = data.dataId.toUpperCase();
            this.tipeData = data.tipe.toUpperCase();

            if(this.tipeData == 'FORM') {
                if(data.dataId.toUpperCase() == 'BARU') { 
                    this.$refs.listAsalPasien.showMasterPasien(); 
                    this.isUpdate = false; 
                }
                else { 
                    this.isUpdate = true; 
                    this.editData({'trx_id':this.dataId}); 
                }
            }
            else { this.getDataPasien(); }
        },

        initialize(){
            this.CLR_ERRORS();   
            if(!this.isRefExist) { this.listReferensi({per_page:'ALL'}); }     

            this.listUnitPelayanan({'jenis_reg':this.registrasi.jns_registrasi, 'per_page':'ALL', 'jadwal_operasi':true }).then((response) => {
                if (response.success == true) { this.unitLists = response.data.data; }
            })      

            this.listKelas({per_page:'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.kelasHargaLists = response.data.data.filter(function (el) { return el.is_kelas_harga == true; });
                } 
                else { alert(response.message); }
            })  
        },

        getDataPasien(){
            this.isLoading = true;
            this.dataPasien(this.bookingId).then((response)=>{
                if (response.success == true) {
                    this.fillDataFromMasterPasien(response.data);
                    this.isLoading = false;
                    this.isUpdate = false;
                } 
                else {
                    this.isLoading = false;
                    alert(response.message);
                }
            })
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
        formClosed(){
            this.clearRegistrasi();
            this.isUpdate = false;
            this.$router.push({ name: 'listPendaftaranOperasi' });
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

        addedDokterSelected(data) {
            if(data) {
                this.addedTim.dokter_id = data.dokter_id;
                this.addedTim.dokter_nama = data.dokter_nama;                   
            }
        },

        searchPasien(){
            if(!this.isUpdate){
                this.$refs.listAsalPasien.showMasterPasien();
                UIkit.switcher(`#switcherRegistrasiOps`).show(0);
            }
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
                        this.isUpdate = true;
                        this.isLoading = false;
                    }
                    else { 
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
        },

        refreshPasien(data){
            this.CLR_ERRORS();
            if(data) {
                this.isLoading = true;
                UIkit.switcher(`#switcherRegistrasiOps`).show(1);
                        
                this.dataPasien(data.pasien_id).then((response) => {
                    if (response.success == true) {
                        this.fillDataFromMasterPasien(response.data);
                        this.isUpdate = false;
                        this.isLoading = false;
                    }
                    else { alert('data pasien tidak ditemukan'); this.isLoading = false; }
                })                
            }
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
                UIkit.switcher(`#switcherRegistrasiOps`).show(1);
                this.dataAdmisiInap(data.trx_id).then((response) => {
                    if (response.success == true) {
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
            this.registrasi.bed_asal_id = data.bed_id;
            this.registrasi.no_asal_bed = data.no_bed;
            
            this.registrasi.kelas_harga_id = data.kelas_harga_id;
            this.registrasi.kelas_harga = data.kelas_harga;
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
            if(data) {
                this.isLoading = true;
                UIkit.switcher(`#switcherRegistrasiOps`).show(1);
                this.dataRegistrasi(data.trx_id).then((response) => {
                    if (response.success == true) {
                        this.fillDataFromPasienPoli(response.data);
                        this.isUpdate = false;
                        this.isLoading = false;
                    }
                    else { alert('data pasien poli tidak ditemukan'); this.isLoading = false; }
                })  
                
            }
        },

        fillDataFromPasienPoli(data){
            console.log(data);
            this.registrasi.reg_id = data.reg_id;
            this.registrasi.reff_trx_id = data.trx_id;
            
            this.registrasi.trx_id = null;
            this.registrasi.sub_trx_id = null;            
            
            /*isi unit dari rawat inap*/
            this.registrasi.unit_asal_id = data.unit_id;
            this.registrasi.unit_asal = data.unit_nama;
            this.registrasi.ruang_asal_id = data.ruang_id;
            this.registrasi.ruang_asal = data.ruang_nama;
            this.registrasi.bed_asal_id = data.bed_id;
            this.registrasi.no_asal_bed = data.no_bed;
            
            this.registrasi.kelas_harga_id = data.kelas_harga_id;
            this.registrasi.kelas_harga = data.kelas_harga;
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
            this.registrasi.asal_pasien = 'RAWAT_JALAN';
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

            this.registrasi.is_pasien_baru = false;
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

            this.registrasi.tindakan_operasi_id = null;
            this.registrasi.tindakan_operasi = null;
            this.registrasi.diagnosa_pra = null;
            this.registrasi.diagnosa_pasca = null;
            this.registrasi.jenis_operasi = null;
            this.registrasi.skala_operasi = null;
                
            this.registrasi.detail = [];
            this.registrasi.tim_operasi = [];
            
            this.minDate = this.getTodayDate();   

        },

        fillRegistrasi(data){
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
            this.registrasi.reff_trx_id = data.reff_trx_id;
            
            //this.registrasi.sub_trx_id = data.sub_trx_id;
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
            
            this.registrasi.unit_asal_id = data.unit_asal_id;
            this.registrasi.unit_asal = data.unit_asal;
            this.registrasi.ruang_asal_id = data.ruang_asal_id;
            this.registrasi.ruang_asal = data.ruang_asal;
            this.registrasi.bed_asal_id = data.bed_asal_id;
            this.registrasi.no_asal_bed = data.no_asal_bed;
            
            
            this.registrasi.kelas_harga = data.kelas_harga;
            this.registrasi.kelas_harga_id = data.kelas_harga_id;
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
        },

        ambilPenjaminList(){
            this.registrasi.penjamin_id = null;
            this.registrasi.penjamin_nama = null;
            let dt = this.jenisPenjaminRefs.json_data.find(item => item.value === this.registrasi.jns_penjamin);
            if(dt) { this.registrasi.is_bpjs = dt.is_bpjs; }            
            this.listAsuransi({per_page:'ALL',jns_penjamin:this.registrasi.jns_penjamin, is_aktif:true }).then((response) => {
                // console.log(response.data);
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

                if(this.isUpdate) { this.submisiRegOperasi(); }
            }
        },

        actSelected(data) {
            this.registrasi.tindakan_operasi_id = null;
            this.registrasi.tindakan_operasi = null;
            if(data) {
                this.registrasi.tindakan_operasi_id = data.tindakan_id;
                this.registrasi.tindakan_operasi = data.tindakan_nama;
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

        bedSelected(){
            let bedSelected = this.bedLists.find(item => item.bed_id === this.registrasi.bed_id);
            if(bedSelected) {
                this.registrasi.no_bed = bedSelected.no_bed;  
            }
        },

        searchTindakan(data){
            this.urlPicker = `/doctors`;
            this.$refs.docModalPicker.showModalPicker(this.urlPicker);
        },

        updateKelasHarga(){
            let val = this.kelasHargaLists.find(item => item.kelas_id === this.registrasi.kelas_harga_id);
            if(val) {  this.registrasi.kelas_harga  = val.kelas_nama; }
            if(this.isUpdate){
                this.submisiRegOperasi();
            }
        },

        asalPasienChange(){
            let val = this.asalPasienList.find(item => item.value == this.registrasi.asal_pasien );
            if(val) {
                this.registrasi.is_rujukan_int = val.isInternal;
                this.registrasi.is_pasien_luar = val.isPasienLuar;
            }
        },

        submisiRegOperasi(){
            this.CLR_ERRORS();
            if(this.isUpdate) {
                this.isLoading = true;
                this.updateOperasi(this.registrasi).then((response) => {
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
                this.createOperasi(this.registrasi).then((response) => {
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
            if(confirm(`Proses ini akan menghapus data registrasi ${this.registrasi.trx_id} - ${this.registrasi.nama_pasien}. Lanjutkan ?`)){
                this.isLoading = true;
                this.deleteOperasi(this.registrasi.trx_id).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; this.isUpdate = false;
                        alert('jadwal operasi BERHASIL dihapus.');
                        this.formClosed();
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
                        this.formClosed();
                    }
                    else { 
                        alert('jadwal operasi tidak berhasil dikonfirmasi.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        posisiChange(data) {
            let val = this.timOperasiRefs.json_data.find(item=> item.value == data.posisi);
            if(val) {
                data.is_operator = val.is_operator;
                if(val.is_operator) {
                    this.registrasi.dokter_id = data.dokter_id;
                    this.registrasi.dokter_nama = data.dokter_nama;
                }
            }
        },

        addTimOperasi(){
            this.CLR_ERRORS();
            let exist = this.registrasi.tim_operasi.find(item => item.dokter_id == this.addedTim.dokter_id);
            if(!exist) {
                this.registrasi.tim_operasi.push({
                    tim_operasi_id : null,
                    reg_id : null,
                    trx_id : null,
                    sub_trx_id : null,
                    dokter_id : this.addedTim.dokter_id,
                    dokter_nama : this.addedTim.dokter_nama,
                    posisi : null,
                    is_operator : false,
                    is_aktif : true,
                });
                this.addedTim.dokter_id = null;
                this.addedTim.dokter_nama = null;
            }
            else { 
                exist.is_aktif = true;
                alert('dokter sudah didalam list tim operasi.');
                this.addedTim.dokter_id = null;
                this.addedTim.dokter_nama = null;
            }

            // if(dataVal.length < 1) { return false; }
            // else {
            //     let addedTim = JSON.parse(JSON.stringify(dataVal));
            //     addedTim.forEach(data => {
            //         let exist = this.registrasi.tim_operasi.find(item => item.dokter_id == data.dokter_id);
            //         if(!exist) {
            //             this.registrasi.tim_operasi.push({
            //                 tim_operasi_id : null,
            //                 reg_id : null,
            //                 trx_id : null,
            //                 sub_trx_id : null,
            //                 dokter_id : data.dokter_id,
            //                 dokter_nama : data.dokter_nama,
            //                 posisi : null,
            //                 is_operator : false,
            //                 is_aktif : true,
            //             });
            //         }
            //         else { exist.is_aktif = true; }
            //     });
            // }
        },

        konfirmasiReg(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan Mengubah status data registrasi ${this.registrasi.trx_id} - ${this.registrasi.nama_pasien}. Lanjutkan ?`)){
                this.isLoading = true;
                this.confirmOperasi(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('jadwal operasi BERHASIL dikonfirmasi.');
                        this.formClosed();
                    }
                    else { 
                        alert('jadwal operasi tidak berhasil dikonfirmasi.'); 
                        this.isLoading = false;
                    }
                })
            }
        },

        changeActivationActItem(){
            this.registrasi.detail = this.registrasi.detail.filter(
                item => { return item.detail_id !== null || item.is_aktif == true }
            );
        },

        calculateTotalLab(){  
            let total = 0;          
            this.registrasi.detail.forEach(data => {
                total = (total*1) + (data.subtotal*1);
                this.registrasi.total = total; 
            })
        },

        timRowAktifChange(data){
            this.registrasi.tim_operasi = this.registrasi.tim_operasi.filter(
                item => { return item.detail_id !== null || item.is_aktif == true }
            );
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

.hims-form-reg-btn {
    background-color: #cc0202;
    color:#eee;
    border:none;
    margin:0;
}
.hims-form-reg-btn:hover {
    background-color: #fff;
    color:#000;
    cursor: pointer;
}


ul.hims-operasi-tab li {
    margin:0;
    padding:0;
}

ul.hims-operasi-tab li h5{
    padding:0 1em 0 1em;
    margin:0;
}

ul.hims-operasi-tab li div a {
    padding:0;
    margin:0;
    color:#000; 
    display:block; 
    padding:0.5em;
    text-decoration: none;
    font-weight: 500;
}

ul.hims-operasi-tab li div {
    margin:0; 
    background-color: #eee; 
    color:#000;
    padding:0;
}

ul.hims-operasi-tab li.uk-active div {
    margin:0; 
    background-color: #fff; 
    color:#000;
    padding:0;
}

ul.hims-operasi-tab li div a h5 {
    color:#666;
    font-weight: 500;
}

ul.hims-operasi-tab li.uk-active div a h5 {
    color:#000;
    font-weight: 500;
}
</style>