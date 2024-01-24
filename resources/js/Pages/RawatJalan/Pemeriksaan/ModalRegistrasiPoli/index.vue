<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" :id="modalId" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <form action="" @submit.prevent="submitConfirmAdmisiPoli" style="margin:0;padding:0;">                
                <div class="uk-container uk-container-large">                
                    <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                        <div uk-spinner="ratio : 2"></div>
                        <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                    </div>                    
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert><p>{{ errors.invalid }}</p></div>
                    
                    <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">     
                        <h2 class="uk-modal-title">Konfirmasi Pelayanan</h2>
                        <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">                        
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.5em 0.5em;">
                                <h5 style="font-weight:500;padding:0;margin:0;">{{registrasi.reg_id}}</h5>
                            </div>                            
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">       
                                <div class="uk-width-1-1">
                                    <p style="margin:0;padding:0;font-size: 12px;">{{registrasi.trx_id}}</p>
                                    <h5 style="font-weight: 500;padding:0;margin:0;">{{registrasi.no_rm}} - {{registrasi.nama_lengkap}} ({{registrasi.jns_kelamin}})</h5>
                                    <p style="margin:0;padding:0;font-size: 11px; font-style:italic;">*Data pasien akan mengikuti perubahan master data pasien.</p>
                                </div>         
                                <div class="uk-width-1-1"></div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>ID Pasien </dt>
                                        <dd>{{registrasi.pasien_id}}</dd>
                                    </dl>
                                </div>        
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Tempat | Tgl Lahir </dt>
                                        <dd>{{registrasi.tempat_lahir}} , {{registrasi.tgl_lahir}}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Jenis Kelamin</dt>
                                        <dd v-if="registrasi.jns_kelamin == 'L'">Laki-laki</dd>
                                        <dd v-else-if="registrasi.jns_kelamin == 'P'">Perempuan</dd>
                                        <dd v-else>(Tidak tahu)</dd>                                
                                    </dl>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Dokter</dt>
                                        <dd>{{ registrasi.dokter_nama }}</dd> 
                                        <dd>{{ registrasi.no_antrian }}</dd>                               
                                    </dl>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Unit</dt>
                                        <dd>{{ registrasi.unit_nama }}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Ruang</dt>
                                        <dd>{{ registrasi.ruang_nama }}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Jadwal Periksa</dt>
                                        <dd>{{ registrasi.tgl_periksa }} {{ registrasi.jam_periksa }}</dd>
                                    </dl>
                                </div>

                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Penjamin</dt>
                                        <dd>{{ registrasi.jns_penjamin }} - {{ registrasi.penjamin_nama }}</dd>
                                    </dl>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <dl class="uk-description-list hims-description-list">
                                        <dt>Kelas</dt>
                                        <dd>{{ registrasi.kelas_harga_id }} - {{ registrasi.kelas_harga }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">
                            <button type="button" class="uk-width-auto@m uk-button uk-modal-close" style="padding:0.5em;border:none;"><span uk-icon="close"></span> Tutup</button>
                            <button type="submit" class="uk-width-auto@m uk-button" style="padding:0.5em;background-color: #cc0202;color:#fff;border:none;"><span uk-icon="approve"></span> Konfirmasi</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</template>
<script>

import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';

export default {
    name : 'modal-registrasi-poli',
    props : {
        modalId : { type:String, required:true },
    },
    components : { headerPage },
    data() {
        return {
            isUpdate : false,
            isLoading : false,            
            registrasi : {
                reg_id : null,
                trx_id : null,
                no_rm : null,
                pasien_id : null,
                nama_pasien : null,
                jns_kelamin : null,
                tempat_lahir : null,
                tgl_lahir : null,
                dokter_id : null,
                dokter_nama : null,
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                no_antrian : null,
                tgl_periksa : null,
                jam_periksa : null,
                jns_penjamin : null,
                penjamin_nama : null,
                penjamin_id : null,
                kelas_harga_id : null,
                kelas_harga : null,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapActions('pendaftaran',['dataRegistrasi','confirmPelayanan']),
        ...mapMutations(['CLR_ERRORS']),

        /** function called before modal closed */
        modalClosed(){
            this.clearRegistrasi();
            this.$emit('registrasiPoliClosed',true);
            UIkit.modal(`#${this.modalId}`).hide();
        },
        
        confirmSucceed(){
            this.clearRegistrasi();
            this.$emit('confirmPoliSucceed',this.registrasi);
            UIkit.modal(`#${this.modalId}`).hide();
        },
        
        /**modal call from other component for update data entry */
        editData(refData) {
            this.CLR_ERRORS();
            this.clearRegistrasi();
            this.isUpdate = true;
            this.isLoading = true;
            if(refData) {
                this.dataRegistrasi(refData.trx_id).then((response) => {
                    if (response.success == true) {
                        this.fillRegistrasi(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal(`#${this.modalId}`).show();
                    }
                    else { alert('data registrasi inap tidak ditemukan'); this.isLoading = false; }
                })
            }
        },

        clearRegistrasi() {
            this.registrasi.reg_id = null;
            this.registrasi.trx_id = null;
            this.registrasi.no_rm = null;
            this.registrasi.pasien_id = null;
            this.registrasi.nama_pasien = null;
            this.registrasi.jns_kelamin = null;
            this.registrasi.tempat_lahir = null;
            this.registrasi.tgl_lahir = null;
            this.registrasi.dokter_id = null;
            this.registrasi.dokter_nama = null;
            this.registrasi.unit_id = null;
            this.registrasi.unit_nama = null;
            this.registrasi.ruang_id = null;
            this.registrasi.ruang_nama = null;
            this.registrasi.no_antrian = null;
            this.registrasi.tgl_periksa = null;
            this.registrasi.jam_periksa = null;
            this.registrasi.jns_penjamin = null;
            this.registrasi.penjamin_nama = null;
            this.registrasi.penjamin_id = null;
            this.registrasi.kelas_harga_id = null;
            this.registrasi.kelas_harga = null;
        },

        fillRegistrasi(data){      
            console.log(data);
            this.registrasi.reg_id = data.reg_id;
            this.registrasi.trx_id = data.trx_id;
            this.registrasi.no_rm = data.no_rm;
            this.registrasi.pasien_id = data.pasien_id;
            this.registrasi.nama_pasien = data.nama_pasien;
            this.registrasi.jns_kelamin = data.jns_kelamin;
            this.registrasi.tempat_lahir = data.pasien.tempat_lahir;
            this.registrasi.tgl_lahir = data.pasien.tgl_lahir;
            this.registrasi.dokter_id = data.dokter_id;
            this.registrasi.dokter_nama = data.dokter_nama;
            this.registrasi.unit_id = data.unit_id;
            this.registrasi.unit_nama = data.unit_nama;
            this.registrasi.ruang_id = data.ruang_id;
            this.registrasi.ruang_nama = data.ruang_nama;
            this.registrasi.no_antrian = data.no_antrian;
            this.registrasi.tgl_periksa = data.tgl_periksa;
            this.registrasi.jam_periksa = data.jam_periksa;
            this.registrasi.jns_penjamin = data.jns_penjamin;
            this.registrasi.penjamin_nama = data.penjamin_nama;
            this.registrasi.penjamin_id = data.penjamin_id;
            this.registrasi.kelas_harga_id = data.kelas_harga_id;
            this.registrasi.kelas_harga = data.kelas_harga;
        },

        submitConfirmAdmisiPoli(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan mengkonfirmasi kehadiran pasien dan mengubah status antrian menjadi PERIKSA. Lanjutkan ?`)){
                this.isLoading = true;
                this.confirmPelayanan(this.registrasi).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('data registrasi poli BERHASIL dikonfirmasi.');
                        this.confirmSucceed();
                    }
                    else { 
                        alert('data registrasi poli tidak berhasil dikonfirmasi.'); 
                        this.isLoading = false;
                    }
                })
            }
        },
    }
}
</script>