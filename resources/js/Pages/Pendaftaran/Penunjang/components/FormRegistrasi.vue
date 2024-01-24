<template>
    <div class="uk-container hims-form-container" style="min-height:50vh;">
        <div>
            <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                <li>
                    <a href="#" @click.prevent="closeTab" class="uk-text-uppercase">                        
                        <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">
                            <span uk-icon="icon:arrow-left;ratio:1.5"></span> REGISTRASI
                        </h4>
                    </a>                    
                </li>
                <li><span style="font-weight:400;color:#333;font-size:16px;" class="uk-text-uppercase">Pendaftaran pelayanan</span></li>           
            </ul>
        </div>
        <div class="uk-container" style="background-color:#fff;padding:1em;margin-top:1em;">
            <form action="" @submit.prevent="submitRegistrasi">
                <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                    <div class="uk-width-expand" style="padding:0.5em;margin:0;">
                        <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0;">
                            <div class="uk-width-expand@m" style="padding-top:0.2em; padding-right : 0.2em; max-width:200px;">
                                <search-list ref="asalDataPasien"
                                    :config = configAsalSelect
                                    :dataLists = asalDataLists
                                    placeholder = "asal data pasien"
                                    captionField = "caption"
                                    valueField = "value"
                                    :detailItems = asalDataPasienDesc
                                    :value = "registrasi.asal_data_pasien"
                                    v-on:item-select = "asalDataPasienSelected"
                                ></search-list>
                            </div>
                            <div class="uk-width-auto@m" style="padding:0;margin:0;">
                                <div class="uk-button-group uk-box-shadow-medium" style="padding:0;margin:0;border-radius:5px;border:1px solid #efefef;">
                                    <button :disabled="registrasi.pasien.is_pasien_luar" type="button" href="#" @click.prevent="searchPasien" class="hims-button-rm" style="border-top-left-radius: 5px; border-bottom-left-radius: 5px;">
                                        <span uk-icon="icon:search; ratio:0.8"></span> Cari Pasien
                                    </button>
                                    <button :disabled="registrasi.pasien.is_pasien_luar" type="button" href="#" @click.prevent="ubahPasien" class="hims-button-rm">
                                        <span uk-icon="icon:pencil; ratio:0.8"></span> Ubah Pasien
                                    </button>
                                    <button :disabled="!allow_pasien_baru" type="button" href="#" @click.prevent="newPasien" class="hims-button-rm" style="border-top-right-radius: 5px; border-bottom-right-radius: 5px;">
                                        <span uk-icon="icon:plus-circle; ratio:0.8"></span> Pasien Baru
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>                                
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">                        
                        <div class="uk-width-auto@m">
                            <div class="uk-button-group uk-box-shadow-medium" style="padding:0;margin:0;border-radius:5px;border:1px solid #efefef;">
                                <button type="button" href="#" @click.prevent="searchPasien" class="hims-button-rm" style="border-top-left-radius: 5px; border-bottom-left-radius: 5px;">
                                    <span uk-icon="icon:refresh; ratio:0.8"></span> Data Baru
                                </button>
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
                <div> 
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
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No RM*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="registrasi.pasien.no_rm" type="text" style="color:black;" disabled>
                                </div>
                            </div>                            
                            
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien*</label>
                                <div class="uk-form-controls">
                                    <input :disabled="!registrasi.pasien.is_pasien_luar" class="uk-input uk-form-small uk-width-expand" v-model="registrasi.pasien.nama_pasien" type="text" style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Salut*</label>
                                <div class="uk-form-controls">
                                    <select :disabled="!registrasi.pasien.is_pasien_luar" v-model="registrasi.pasien.salut" class="uk-select uk-form-small" required style="color:black;">
                                        <option v-if="isRefExist" v-for="dt in salutRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin*</label>
                                <div class="uk-form-controls">
                                    <select :disabled="!registrasi.pasien.is_pasien_luar" v-model="registrasi.pasien.jns_kelamin" class="uk-select uk-form-small" required style="color:black;">
                                        <option value="L">LAKI-LAKI</option>
                                        <option value="P">PEREMPUAN</option>
                                        <option value="-">-</option>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir*</label>
                                <div class="uk-form-controls">
                                    <input :disabled="!registrasi.pasien.is_pasien_luar" class="uk-input uk-form-small uk-text-uppercase" v-model="registrasi.pasien.tempat_lahir" type="text" style="color:black;" required>
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir*</label>
                                <div class="uk-form-controls">
                                    <input :disabled="!registrasi.pasien.is_pasien_luar" class="uk-input uk-form-small" v-model="registrasi.pasien.tgl_lahir" type="date" style="color:black;" required>
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK*</label>
                                <div class="uk-form-controls">
                                    <input :disabled="!registrasi.pasien.is_pasien_luar" class="uk-input uk-form-small" v-model="registrasi.pasien.nik" type="text" style="color:black;" required>
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
                                    <input class="uk-input uk-form-small" v-model="registrasi.pasien.no_kepesertaan" type="text" style="color:black;" required>
                                </div>
                            </div>
                            <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;">
                                        <input class="uk-checkbox" type="checkbox" value="1" v-model="registrasi.is_bpjs" > Asuransi BPJS
                                        <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.2em; margin:0;">asuransi pasien adalah BPJS atau turunannya.</span>
                                    </label>
                                </div>
                            </div>
                       </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid>
                        <div class="uk-width-1-4@m" style="padding:0 0.5em 0.2em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA REGISTRASI</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">pilih pelayanan dan spesialis.</p>
                        </div>                    
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">         
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Referensi.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="text" disabled>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit Asal</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="text" disabled>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Perujuk</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="text" disabled>
                                </div>
                            </div>                   
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Periksa*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="registrasi.tgl_periksa" type="date">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Registrasi*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.jns_registrasi" class="uk-select uk-form-small" style="border:1px solid #cc0202;"  @change="jnsRegistrasiChange">
                                        <option v-for="option in jenisRegistrasiRefs.json_data" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                    </select>
                                </div>
                            </div>      
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Cara Registrasi*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.cara_registrasi" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-for="option in caraRegistrasiRefs.json_data" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="uk-width-1-2@m">
                                <search-list
                                    ref = "selectUnit"
                                    :config = configSelect
                                    :dataLists = unitLists
                                    label = "Unit Pelayanan*"
                                    placeholder = "unit pelayanan"
                                    captionField = "unit_nama"
                                    valueField = "unit_nama"
                                    :detailItems = unitDesc
                                    :value = "registrasi.unit_nama"
                                    v-on:item-select = "unitSelected"
                                ></search-list>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Ruang Pelayanan*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.ruang_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;"  @change="ruangSelected">
                                        <option v-for="option in roomLists" v-bind:value="option.ruang_id" v-bind:key="option.ruang_id">{{option.ruang_nama}}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="uk-width-1-2@m">
                                <search-list
                                    :config = configSelect
                                    :dataLists = dokterLists
                                    label = "Dokter / Spesialis*"
                                    placeholder = "pilih dokter spesialis"
                                    captionField = "dokter_nama"
                                    valueField = "dokter_id"
                                    :detailItems = dokterDesc
                                    :value = "registrasi.dokter_id"
                                    v-on:item-select = "dokterSelected"
                                ></search-list>
                            </div>            
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jadwal*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.jadwal_id" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-for="option in jadwalLists" v-bind:value="option.jadwal_id" v-bind:key="option.jadwal_id">
                                            {{option.nama_hari}} : {{option.mulai}} - {{option.selesai}}  
                                        </option>
                                    </select>
                                </div>
                            </div>            
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Asal Rujukan*</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.asal_pasien" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-for="option in asalPasienRefs.json_data" v-bind:value="option.value" v-bind:key="option.value">{{option.value}}</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="uk-width-3-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan Rujukan Pasien</label>
                                <div class="uk-form-controls">
                                    <input v-model="registrasi.ket_asal_pasien" class="uk-input uk-form-small" type="text" style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penanggung</label>
                                <div class="uk-form-controls">
                                    <select v-model="registrasi.hub_penanggung" class="uk-select uk-form-small">
                                        <option v-if="isRefExist" v-for="dt in hubKeluargaRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="uk-width-3-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Penanggung</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="registrasi.nama_penanggung" type="text" style="color:black;">
                                </div>
                            </div>
                        </div>
                    </div>                          
                </div>
            </form>            
            <div class="uk-grid-small hims-form-subpage" uk-grid>
                <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;">
                    <h5 style="color:indigo;padding:0;margin:0;">PEMERIKSAAN
                        <span style="font-size:12px;font-style:italic;padding:0;margin:0 0 0 0.5em;">item pemeriksaan pasien.</span>
                    </h5>                    
                </div>                    
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
                            <tr v-if="registrasi.tindakan.length > 0" v-for="dt in registrasi.tindakan">
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
        </div>
        <modal-list-pasien ref="modalListPasien" v-on:listPasienSelected = "pasienSelected"></modal-list-pasien>
        <modal-form-pasien ref="modalFormPasien" v-on:modalPasienClosed="modalPasienClosed"></modal-form-pasien>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';
import ModalListPasien from '@/Pages/Pendaftaran/Penunjang/components/ModalListPasien.vue';
import ModalFormPasien from '@/Pages/Pendaftaran/Penunjang/components/ModalFormPasien.vue';

export default {
    name : 'form-registrasi', 
    components : {
        SearchSelect,
        SearchList,
        ModalListPasien,
        ModalFormPasien,
    },
    data() {
        return {
            isLoading : true,
            configAsalSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],
            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],
            penjaminDesc: [
                { colName : 'penjamin_nama', labelData : '', isTitle : true },
                { colName : 'penjamin_id', labelData : '', isTitle : false },
            ],
            bedDesc: [
                { colName : 'no_bed', labelData : '', isTitle : true },
                { colName : 'bed_id', labelData : '', isTitle : false },
                { colName : 'reg_id', labelData : '', isTitle : false },
            ],
            jadwalDesc : [
                { colName : 'value', labelData : '', isTitle : true },
                { colName : 'text', labelData : '', isTitle : false },
            ],
            tindakanDesc : [
                { colName : 'tindakan_nama', labelData : '', isTitle : true },
                { colName : 'tindakan_id', labelData : '', isTitle : false },
            ], 
            asalDataPasienDesc : [
                { colName : 'caption', labelData : '', isTitle : true },
                { colName : 'desc', labelData : '', isTitle : false },
            ], 

            asalDataPasienLists: [
                { value : 'MASTER_PASIEN', caption:'MASTER PASIEN', desc : 'pasien datang sendiri', is_pasien_luar : false, allow_pasien_baru : true },
                { value : 'RAWAT_JALAN', caption:'RAWAT JALAN', desc : 'pasien rawat jalan', is_pasien_luar : false, allow_pasien_baru : false },
                { value : 'IGD', caption:'GAWAT DARURAT', desc : 'pasien gawat darurat', is_pasien_luar : false, allow_pasien_baru : false },
                { value : 'RAWAT_INAP', caption:'RAWAT INAP', desc : 'pasien rawat inap', is_pasien_luar : false, allow_pasien_baru : false },
                { value : 'PASIEN_LUAR', caption:'PASIEN LUAR', desc : 'pasien rujukan dari luar', is_pasien_luar : true, allow_pasien_baru : false },
            ],

            isUpdate : true,
            produkUrl : '',
            depoUrl : '/depos',
            tindakanUrl : '',
            unitUrl : '',
            jadwalUrl : '',
            asalDataLists : [],

            unitLists : [],
            dokterLists : [],
            roomLists : [],
            bedLists : [],
            tindakanLists : [],
            jadwalLists : [],

            distribusi : {
                jenis_registrasi : null,
                asal_pasien : null,
                cara_registrasi : null,
                client_id : null,
                distribusi_id : null,
                depo_penerima_id : null,
                depo_pengirim_id : null,
                depo_penerima : null,
                depo_pengirim : null,
                tgl_dibuat : null,
                tgl_dibutuhkan : null,
                tgl_realisasi : null,
                
                status : null,
                catatan : null,
                is_aktif : true,
                items : [],
            },

            addedItem : {
                distribusi_detail_id : null,
                distribusi_id : null,
                unit_id : null,
                unit_nama  : null,
                depo_produk_id : null,
                produk_id : null,
                produk_nama : null,
                satuan : null,
                depo_pengirim_id : null,
                depo_penerima_id : null,
                depo_pengirim : null,
                depo_penerima : null,
                jml_diminta : null,
                jml_dikirim : null,
                status : null,
                is_aktif : true,
                pr_id : null,
                po_id : null,
            },

            allow_pasien_baru : false,

            registrasi : {
                reg_id : null,
                client_id : null,
                jns_registrasi : null,
                cara_registrasi : null,
                asal_data_pasien : null,
                tgl_periksa : this.getTodayDate(),
                kode_booking : null,
                no_antrian : null,
                jadwal_id : null,
                dokter_id : null,
                dokter_unit_id : null,
                dokter_nama : null,
                dokter_pengirim_id: null,
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                bed_id : '-',
                asal_pasien : null,
                ket_asal_pasien : null,
                pasien_id : null,
                jns_penjamin : null,
                penjamin_id : null,                
                penjamin_nama : null,
                nama_penanggung : null,
                hub_penanggung : null,
                is_bpjs : false,
                is_pasien_baru : false,
                is_pasien_luar : false,
                
                status_reg : null,
                status_pulang : null,
                cara_pulang : null,
                tgl_pulang: null,
                is_aktif : true,
                client_id : null,
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
                tindakan : [],
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
            'hubKeluargaRefs'
        ]),    
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('pendaftaran',['createRegistrasi','updateRegistrasi','dataRegistrasi','approveRegistrasi']),
        ...mapActions('pasien',['createPasien','updatePasien','dataPasien','uploadDokterAvatar']),
        ...mapActions('jadwalDokter',['listJadwalByDokter']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('asuransi',['listAsuransi']),
        ...mapActions('refProduk',['listRefProduk']),
        ...mapActions('referensi',['listReferensi']),
        
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            this.asalDataLists = JSON.parse(JSON.stringify(this.asalDataPasienLists));
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

        searchPasien(){
            this.$refs.modalListPasien.showPasienList(this.registrasi.asal_data_pasien);
        },

        pasienSelected(data) {
            if(data){
                if(data.pasien_id) {
                    this.refreshPasienReg(data.pasien_id);
                }
            }
        },

        modalPasienClosed(data){
            if(data){
                if(data.pasien_id !== null) {
                    this.refreshPasienReg(data.pasien_id);
                }
            }
        },

        ambilPenjaminList(){
            this.registrasi.penjamin_id = null;
            this.registrasi.penjamin_nama = null;
            let dt = this.jenisPenjaminRefs.json_data.find(item => item.value === this.registrasi.jns_penjamin);
            console.log(dt);
            if(dt) { this.registrasi.is_bpjs = dt.is_bpjs; }
            
            this.listAsuransi({per_page:'ALL',jns_penjamin:this.registrasi.jns_penjamin, is_aktif:true });
        },

        penjaminSelected(data){
            if(data) {
                console.log(data);
                this.registrasi.penjamin_id = data.penjamin_id;
                this.registrasi.penjamin_nama = data.penjamin_nama;
                this.registrasi.is_bpjs = data.is_bpjs;
            }
        },

        ubahPasien(){ this.CLR_ERRORS(); this.$refs.modalFormPasien.editDataPasien(this.registrasi.pasien_id); },
        newPasien(){ this.CLR_ERRORS(); this.$refs.modalFormPasien.createNewPasien(); },
        closeTab() { this.$emit('redirectMainTab',true); },
        pasienTab(){ this.$emit('redirectPatientTab',true); },

        jnsRegistrasiChange(){
            this.registrasi.unit_id = null;
            this.registrasi.unit_nama = '';
            this.dokterLists = [];
            this.roomLists = [];
            this.registrasi.dokter_id = null;
            this.registrasi.ruang_id = '';

            this.listUnitPelayanan({'jenis_reg':this.registrasi.jns_registrasi, 'per_page':'ALL'}).then((response) => {
                if (response.success == true) {
                    this.unitLists = response.data.data;
                    this.$refs.selectUnit.refreshList(this.unitLists);
                }
            })
        },

        ruangSelected() {
                let ruangSelected = this.roomLists.find(item => item.ruang_id === this.registrasi.ruang_id);
                if(ruangSelected) {
                    this.registrasi.ruang_nama = ruangSelected.ruang_nama;
                    this.bedLists = ruangSelected.beds;
                }
        },

        asalDataPasienSelected(data){
            this.clearRegPasien();
            this.registrasi.asal_data_pasien = null;
            this.allow_pasien_baru = false;
            if(data) {
                this.registrasi.asal_data_pasien = data.value;
                this.registrasi.pasien.is_pasien_luar = data.is_pasien_luar;
                this.registrasi.is_pasien_luar = data.is_pasien_luar;
                this.allow_pasien_baru = data.allow_pasien_baru;
            }
        },

        dokterSelected(data) {    
            if(data) {
                this.registrasi.dokter_id = data.dokter_id;
                this.registrasi.dokter_nama = data.dokter_nama;
                this.registrasi.dokter_unit_id = data.dokter_unit_id;
                
                this.jadwalUrl = `schedule/doctors?per_page=ALL&&keyword=${data.dokter_id}&&unit=${this.registrasi.unit_id}`;
                let param = {
                    'per_page' : 'ALL', 
                    'is_aktif': true, 
                    'keyword' : data.dokter_id, 
                    'unit' : this.registrasi.unit_id,
                    'tanggal' : this.registrasi.tgl_periksa
                }
                this.listJadwalByDokter(param).then((response) => {
                    if (response.success == true) {
                        this.jadwalLists = response.data.data[0].jadwal;
                        if(this.jadwalLists.length > 0) {
                            this.registrasi.jadwal_id = this.jadwalLists[0].jadwal_id;
                        }
                    }
                })
            }

        },

        unitSelected(data) {
            if(data) {
                this.registrasi.unit_id = data.unit_id;
                this.registrasi.unit_nama = data.unit_nama;                
                this.dokterLists = data.dokter;
                this.roomLists = data.ruang;
                if(this.roomLists.length > 0) {
                    this.registrasi.ruang_id = this.roomLists[0].ruang_id;
                    this.registrasi.ruang_nama = this.roomLists[0].ruang_nama;
                    this.bedLists = this.roomLists[0].beds;
                }
                this.tindakanUrl = `/units/${this.registrasi.unit_id}/acts`;
            }
        },

        bedSelected(){
        },

        closeModal(){
            this.clearRegistrasi();
            this.$emit('closed',true);
        },

        submitRegistrasi(){            
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createRegistrasi(this.registrasi).then((response) => {
                    if (response.success == true) {
                        alert("Data pendaftaran baru berhasil dibuat.");
                        this.fillRegistrasi(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }
            else {
                this.updateRegistrasi(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.fillRegistrasi(response.data);
                        alert("Data pendaftaran berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }            
        },

        newRegistrasi(){
            this.clearRegistrasi();
            this.isUpdate = false;
            this.isLoading = false;            
        },

        editRegistrasi(data){
            this.clearRegistrasi();
            this.isLoading = true;
            this.dataRegistrasi(data.reg_id).then((response)=>{
                if (response.success == true) {
                    this.fillRegistrasi(response.data);
                    this.isUpdate = true;
                } 
                else { alert(response.message); }
                this.isLoading = false;
            })
        },   

        // clearDistribusi(){
        //     this.distribusi.client_id = null;
        //     this.distribusi.distribusi_id = null;
        //     this.distribusi.depo_penerima_id = null;
        //     this.distribusi.depo_pengirim_id = null;
        //     this.distribusi.depo_penerima = null;
        //     this.distribusi.depo_pengirim = null;
                
        //     this.distribusi.tgl_dibuat = null;
        //     this.distribusi.tgl_dibutuhkan = null;
        //     this.distribusi.tgl_realisasi = null;
                
        //     this.distribusi.status = null;
        //     this.distribusi.catatan = null;
        //     this.distribusi.is_aktif = true;
        //     this.distribusi.items = [];
        // },

        // fillDistribusi(data){
        //     this.distribusi.client_id = null;
        //     this.distribusi.distribusi_id = data.distribusi_id;
        //     this.distribusi.depo_penerima_id = data.depo_penerima_id;
        //     this.distribusi.depo_pengirim_id = data.depo_pengirim_id;
        //     this.distribusi.depo_penerima = data.depo_penerima;
        //     this.distribusi.depo_pengirim = data.depo_pengirim;
                
        //     this.distribusi.tgl_dibuat = data.tgl_dibuat;
        //     this.distribusi.tgl_dibutuhkan = data.tgl_dibutuhkan;
        //     this.distribusi.tgl_realisasi = data.tgl_realisasi;
                
        //     this.distribusi.status = data.status;
        //     this.distribusi.catatan = data.catatan;
        //     this.distribusi.is_aktif = data.is_aktif;
        //     this.distribusi.items = data.items;

        //     this.produkUrl = `/depos/${data.depo_penerima_id}/products`;
        // },

        addActItem() {
            if(this.addedAct.tindakan_nama == "" || this.addedAct.tindakan_nama == null || this.addedAct.jumlah == null || this.addedAct.jumlah == 0) {
                alert("Data tidak dapat ditambahkan. ada data yang masih kurang.");
                return false;
            }
            let addedVal = JSON.parse(JSON.stringify(this.addedAct));
            this.registrasi.tindakan.push(addedVal);
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

        itemActSelected(data) {
            this.addedAct.tindakan_id = null;
            this.addedAct.tindakan_nama = null;
            this.addedAct.harga = null;
            this.addedAct.satuan = null;
            this.addedAct.jumlah = null;
            this.addedAct.subtotal = null;
            this.addedAct.is_aktif = true;
            if(data) {
                console.log(data);
                this.addedAct.tindakan_id = data.tindakan_id;
                this.addedAct.tindakan_nama = data.tindakan_nama;
                this.addedAct.harga = data.harga;
                this.addedAct.satuan = data.satuan;
                this.addedAct.jumlah = 1;
                this.addedAct.subtotal = data.harga;
                this.addedAct.is_aktif = true;
            }
        },

        addedItemAktifasi(data){
            this.registrasi.tindakan = this.registrasi.tindakan.filter(item => { return item['is_aktif'] == true || item['reg_detail_id'] !== null });
        },

        refreshPasienReg(pasienId){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.clearRegPasien();
            this.dataPasien(pasienId).then((response)=>{
                if (response.success == true) {
                    this.fillPasienReg(response.data);
                } else {
                    alert(response.message);
                }
                this.isLoading = false;
            })
        },

        clearRegPasien(){
            this.registrasi.pasien.reg_id = null;
            this.registrasi.pasien.pasien_id = null;
            this.registrasi.pasien.nik = null;
            this.registrasi.pasien.no_kk = null;
            this.registrasi.pasien.no_rm = null;
            this.registrasi.pasien.salut = null;
            this.registrasi.pasien.nama_pasien = null;
            this.registrasi.pasien.nama_lengkap = null;
            this.registrasi.pasien.tempat_lahir = null;
            this.registrasi.pasien.tgl_lahir = this.getTodayDate();
            this.registrasi.pasien.usia = null;
            this.registrasi.pasien.jns_kelamin = null;
            this.registrasi.pasien.propinsi_id = null;
            this.registrasi.pasien.kota_id= null;
            this.registrasi.pasien.kecamatan_id = null;
            this.registrasi.pasien.kelurahan_id = null;
            this.registrasi.pasien.kodepos= null;
            this.registrasi.pasien.is_hamil = false;
            this.registrasi.pasien.is_pasien_baru = false;
            this.registrasi.pasien.is_bpjs = false;
            this.registrasi.pasien.is_aktif = null;
            this.registrasi.pasien.client_id = null;

            this.registrasi.pasien_id = null;
        },

        fillPasienReg(data) {            
            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.pasien.no_rm = data.no_rm;
            this.registrasi.pasien.nama_pasien = data.nama_pasien;
            this.registrasi.pasien.salut = data.salut;
            this.registrasi.pasien.jns_kelamin = data.jns_kelamin;
            this.registrasi.pasien.tempat_lahir = data.tempat_lahir;
            this.registrasi.pasien.tgl_lahir = data.tgl_lahir;
            this.registrasi.pasien.nik = data.nik;
            this.registrasi.pasien.no_kk = data.no_kk;
            
            this.registrasi.jns_penjamin = data.jns_penjamin;
            this.registrasi.penjamin_id = data.penjamin_id;
            this.registrasi.penjamin_nama = data.penjamin_nama;
            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.is_pasien_luar = false;
            this.ambilPenjaminList();

        },

        clearRegistrasi(){
            this.registrasi.reg_id = null;
            this.registrasi.client_id = null;
            this.registrasi.jns_registrasi = null;
            this.registrasi.cara_registrasi = null;
            this.registrasi.asal_data_pasien = null;
            this.registrasi.tgl_periksa = this.getTodayDate();
            this.registrasi.kode_booking = null;
            this.registrasi.no_antrian = null;
            this.registrasi.jadwal_id = null;
            this.registrasi.dokter_id = null;
            this.registrasi.dokter_unit_id = null;
            this.registrasi.dokter_nama = null;
            this.registrasi.dokter_pengirim_id= null;
            this.registrasi.unit_id = null;
            this.registrasi.unit_nama = null;
            this.registrasi.ruang_id = null;
            this.registrasi.ruang_nama = null;
            this.registrasi.bed_id = '-';
            this.registrasi.asal_pasien = null;
            this.registrasi.ket_asal_pasien = null;
            this.registrasi.pasien_id = null;
            this.registrasi.jns_penjamin = null;
            this.registrasi.penjamin_id = null;                
            this.registrasi.penjamin_nama = null;
            this.registrasi.nama_penanggung = null;
            this.registrasi.hub_penanggung = null;
            this.registrasi.is_bpjs = false;
            this.registrasi.is_pasien_baru = false;
            this.registrasi.is_pasien_luar = false;
                
            this.registrasi.status_reg = null;
            this.registrasi.status_pulang = null;
            this.registrasi.cara_pulang = null;
            this.registrasi.tgl_pulang= null;
            this.registrasi.is_aktif = true;
            
            this.registrasi.pasien.reg_id = null;
            this.registrasi.pasien.is_pasien_luar = false;
            this.registrasi.pasien.pasien_id = null;
            this.registrasi.pasien.nik = null;
            this.registrasi.pasien.no_kk = null;
            this.registrasi.pasien.no_rm = null;
            this.registrasi.pasien.salut = null;
            this.registrasi.pasien.nama_pasien = null;
            this.registrasi.pasien.nama_lengkap = null;
            this.registrasi.pasien.tempat_lahir = null;
            this.registrasi.pasien.tgl_lahir = this.getTodayDate();
            this.registrasi.pasien.usia = null;
            this.registrasi.pasien.jns_kelamin = null;
            this.registrasi.pasien.propinsi_id = null;
            this.registrasi.pasien.kota_id= null;
            this.registrasi.pasien.kecamatan_id = null;
            this.registrasi.pasien.kelurahan_id = null;
            this.registrasi.pasien.kodepos= null;
            this.registrasi.pasien.is_hamil = false;
            this.registrasi.pasien.is_pasien_baru = false;
            this.registrasi.pasien.is_aktif = null;
            this.registrasi.pasien.client_id = null;
            this.registrasi.tindakan = [];
        },

        fillRegistrasi(data){
            this.registrasi.reg_id = data.reg_id;
            this.registrasi.client_id = null;
            this.registrasi.jns_registrasi = data.jns_registrasi;
            this.registrasi.cara_registrasi = data.cara_registrasi;
            this.registrasi.asal_data_pasien = data.asal_data_pasien;
            this.registrasi.tgl_periksa = data.tgl_periksa;
            this.registrasi.kode_booking = data.kode_booking;
            this.registrasi.no_antrian = data.no_antrian;
            this.registrasi.jadwal_id = data.jadwal_id;
            this.registrasi.dokter_id = data.dokter_id;
            this.registrasi.dokter_unit_id = data.dokter_unit_id;
            this.registrasi.dokter_nama = data.dokter_nama;
            this.registrasi.dokter_pengirim_id= data.dokter_pengirim_id;
            this.registrasi.unit_id = data.unit_id;
            this.registrasi.unit_nama = data.unit_nama;
            this.registrasi.ruang_id = data.ruang_id;
            this.registrasi.ruang_nama = data.ruang_nama;
            this.registrasi.bed_id = data.bed_id;
            this.registrasi.asal_pasien = data.asal_pasien;
            this.registrasi.ket_asal_pasien = data.ket_asal_pasien;
            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.jns_penjamin = data.jns_penjamin;
            this.registrasi.penjamin_id = data.penjamin_id;   
            this.registrasi.penjamin_nama = data.penjamin_nama;
            this.registrasi.nama_penanggung = data.nama_penanggung;
            this.registrasi.hub_penanggung = data.hub_penanggung;
            this.registrasi.is_bpjs = data.is_bpjs;
            this.registrasi.is_pasien_baru = data.is_pasien_baru;
            this.registrasi.is_pasien_luar = data.is_pasien_luar;
                
            this.registrasi.status_reg = data.status_reg;
            this.registrasi.status_pulang = data.status_pulang;
            this.registrasi.cara_pulang = data.cara_pulang;
            this.registrasi.tgl_pulang= data.tgl_pulang;
            this.registrasi.is_aktif = data.is_aktif;
            
            this.registrasi.pasien.reg_id = data.pasien.reg_id;
            this.registrasi.pasien.is_pasien_luar = data.pasien.is_pasien_luar;
            this.registrasi.pasien.pasien_id = data.pasien.pasien_id;
            this.registrasi.pasien.nik = data.pasien.nik;
            this.registrasi.pasien.no_kk = data.pasien.no_kk;
            this.registrasi.pasien.no_rm = data.pasien.no_rm;
            this.registrasi.pasien.salut = data.pasien.salut;
            this.registrasi.pasien.nama_pasien = data.pasien.nama_pasien;
            this.registrasi.pasien.nama_lengkap = data.pasien.nama_pasien;
            this.registrasi.pasien.tempat_lahir = data.pasien.tempat_lahir;
            this.registrasi.pasien.tgl_lahir = data.pasien.tgl_lahir;
            this.registrasi.pasien.usia = data.pasien.usia;
            this.registrasi.pasien.jns_kelamin = data.pasien.jns_kelamin;
            this.registrasi.pasien.propinsi_id = data.pasien.propinsi_id;
            this.registrasi.pasien.kota_id= data.pasien.kota_id;
            this.registrasi.pasien.kecamatan_id = data.pasien.kecamatan_id;
            this.registrasi.pasien.kelurahan_id = data.pasien.kelurahan_id;
            this.registrasi.pasien.kodepos= data.pasien.kodepos;
            this.registrasi.pasien.is_hamil = data.pasien.is_hamil;
            this.registrasi.pasien.is_pasien_baru = data.pasien.is_pasien_baru;
            this.registrasi.pasien.is_aktif = data.pasien.is_aktif;
            this.registrasi.pasien.client_id = data.pasien.client_id;

            this.registrasi.tindakan = data.tindakan;
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