<template>
    <div class="uk-modal-container" id="modalViewRegistrasiRad" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div style="margin:0;padding:0;">
                <div style="margin:0;padding:0.5em;text-align: center;">
                    <h4 style="font-weight:500;padding:0;margin:0;">{{registrasi.trx_id}}</h4>
                    <p style="font-size:12px;color:#333;padding:0;margin:0;">{{registrasi.reg_id}}</p>
                </div>
                <div class="uk-container uk-container-large">                
                    <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                        <div uk-spinner="ratio : 2"></div>
                        <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                    </div>
                    
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                    
                    <div class="hims-form-container view-lab-container" style="padding:0.5em 1.5em 2em 1.5em; margin:0;">     
                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                            <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    detail data pasien.
                                </p>
                            </div>
                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0 0.5em;"> 
                                <div class="uk-width-1-1" style="margin:0;padding:0;"></div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="registrasi.nama_pasien" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No RM</label>
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
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="registrasi.jns_kelamin" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="registrasi.tempat_lahir" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Lahir*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_lahir" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Penjamin*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="registrasi.jns_penjamin" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penjamin / Asuransi</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="registrasi.penjamin_nama" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Kepesertaan</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="registrasi.no_kepesertaan" type="text" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK (KTP / ID Card)*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="registrasi.nik" type="text" style="color:black;" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                            <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DIAGNOSA</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;"> detail data diagnosa.</p>
                            </div>
                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diagnosa</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="registrasi.diagnosa" type="text" style="color:black;" disabled></textarea>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Keterangan Klinis</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="registrasi.ket_klinis" type="text" style="color:black;" disabled></textarea>
                                    </div>
                                </div>
                                <div class="uk-width-1-1@m">
                                    <p style="padding:0;margin:0; font-size:12px; color:black; font-weight: 500;"> Jenis Hasil : 
                                        <span v-if="registrasi.is_multi_hasil" style="font-size: 12px; font-weight: 500;">Multi hasil (per pemeriksaan)</span>
                                        <span v-else style="font-size: 12px; font-weight: 400;">Hasil tunggal (satu hasil untuk seluruh item pemeriksaan)</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                                
                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                            <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA REGISTRASI</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    detail data pendaftaran radiologi.
                                </p>
                            </div>
                            <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Periksa*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="registrasi.tgl_periksa" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Periksa*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="time" v-model="registrasi.jam_periksa" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="registrasi.unit_nama" type="text" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Ruang</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="registrasi.ruang_nama" type="text" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Pemeriksa</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="registrasi.dokter_nama" type="text" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter Pengirim</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="registrasi.dokter_pengirim" type="text" style="color:black;" disabled>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Layanan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="registrasi.kelas_harga_id" type="text" style="color:black;" disabled>
                                    </div>
                                </div>                                        
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Asal Pasien</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="registrasi.asal_pasien" style="color:black;"  disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="uk-grid-small hims-form-subpage" uk-grid>                        
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0 0.5em 0 0.5em;">
                                <div class="uk-width-expand@m">
                                    <h5 style="color:indigo;padding:0;margin:0;">PEMERIKSAAN RADIOLOGI
                                        <span style="font-size:12px;font-style:italic;padding:0;margin:0 0 0 0.2em;">detail pemeriksaan radiologi.</span>
                                    </h5>
                                </div>                                   
                            </div>
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                                <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                    <thead>
                                        <th class="tb-header-reg" style="width:100px;text-align:left;color:white;">ID</th>
                                        <th class="tb-header-reg" style="text-align:left;color:white;">Tindakan</th>
                                        <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Jumlah</th>
                                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Harga</th>
                                        <th class="tb-header-reg" style="width:60px;text-align:right;color:white;">Diskon(Rp)</th>
                                        <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>
                                    </thead>
                                    <tbody> 
                                        <tr v-for="dt in registrasi.detail">
                                            <td style="width:140px; padding:1em; font-size: 12px; text-align:left; color:black; font-weight:500;background-color:#fff;">{{dt.item_id}}</td>
                                            <td style="padding:1em; font-size: 12px; text-align:left; color:black;background-color:#fff;">{{dt.item_nama}}</td>
                                            <td style="width:60px; padding:1em; font-size: 12px; text-align:center; color:black;background-color:#fff;">{{dt.jumlah}} {{dt.satuan}}</td>
                                            <td style="width:80px; padding:1em; font-size: 12px; text-align:right; color:black;background-color:#fff;">{{formatCurrency(dt.nilai)}}</td>
                                            <td style="width:80px; padding:1em; font-size: 12px; text-align:right; color:black;background-color:#fff;">{{formatCurrency(dt.diskon)}}</td>
                                            <td style="width:80px; padding:1em; font-size: 12px; text-align:right; color:black;background-color:#fff;">{{formatCurrency(dt.subtotal)}}</td>
                                        </tr>   
                                        <tr>
                                            <td colspan="5" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL</td>
                                            <td style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(registrasi.total)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'view-registrasi-rad',
    data() {
        return {
            isLoading : false,
            registrasi : {
                reg_id : null,
                trx_id : null,
                reff_trx_id : null,
                client_id : null,
                is_rujukan_int : false,
                jns_registrasi : 'RADIOLOGI',
                tgl_transaksi : null,
                tgl_periksa : null,
                jam_periksa : null,
                
                dokter_unit_id : null,
                dokter_id : null,
                dokter_nama : null,
                
                dokter_pengirim_id: null,
                dokter_pengirim: null,
                
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                
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

                diagnosa : null,
                ket_klinis : null,
                is_multi_hasil : true,               
                jenis_cito : null, 
                is_cito : null,
                media_hasil : null,
                expertise_id : null,
                expertise_nama : null,  

                detail : [],
            },
            // unitLists : [],
            // dokterLists : [],
            // roomLists : [],
            // kelasHargaLists : [],
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    methods : {
        ...mapActions('radiologi',['dataRadiologi']),
        ...mapMutations(['CLR_ERRORS']),

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        /** function called before modal closed */
        modalClosed(){
            this.clearRegistrasi();
            this.$emit('viewRegistrasiRadClosed',true);
            UIkit.modal('#modalViewRegistrasiRad').hide();
        },

        viewData(refData) {
            if(refData) {
                this.CLR_ERRORS();
                this.clearRegistrasi();
                this.isLoading = true;
                if(refData) {
                    this.dataRadiologi(refData.trx_id).then((response) => {
                        if (response.success == true) {
                            this.fillRegistrasi(response.data);
                            this.isLoading = false;
                            UIkit.modal('#modalViewRegistrasiRad').show();
                        }
                        else { alert('data registrasi radiologi tidak ditemukan'); this.isLoading = false; }
                    })
                }
            }
        },

        clearRegistrasi() {
            this.registrasi.reg_id = null;
            this.registrasi.trx_id = null;
            this.registrasi.reff_trx_id = null;
            this.registrasi.client_id = null;
            this.registrasi.is_rujukan_int = false;
            this.registrasi.jns_registrasi = 'RADIOLOGI';
            this.registrasi.tgl_transaksi = null;
            this.registrasi.tgl_periksa = null;
            this.registrasi.jam_periksa = null;
                
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
            
            this.registrasi.diagnosa = null;
            this.registrasi.ket_klinis = null;
            this.registrasi.is_multi_hasil = true;
            this.registrasi.jenis_cito = null; 
            this.registrasi.is_cito = null;
            this.registrasi.media_hasil = null;
            this.registrasi.expertise_id = null;
            this.registrasi.expertise_nama = null;

            this.registrasi.detail = [];

        },

        fillRegistrasi(data){
            this.registrasi.reg_id = data.reg_id;
            this.registrasi.trx_id = data.trx_id;
            this.registrasi.reff_trx_id = data.reff_trx_id;
            this.registrasi.is_rujukan_int = data.is_rujukan_int;
            
            this.registrasi.client_id = data.client_id;
            this.registrasi.jns_registrasi = data.jns_registrasi;
            this.registrasi.cara_registrasi = data.cara_registrasi;
            this.registrasi.tgl_transaksi = data.tgl_transaksi;
                
            this.registrasi.tgl_periksa = data.tgl_periksa;
            this.registrasi.jam_periksa = data.jam_periksa;
                
            this.registrasi.dokter_unit_id = data.dokter_unit_id;
            this.registrasi.dokter_id = data.dokter_id;
            this.registrasi.dokter_nama = data.dokter_nama;
            this.registrasi.dokter_pengirim_id = data.dokter_pengirim_id;
            this.registrasi.dokter_pengirim = data.dokter_pengirim;
                
            this.registrasi.unit_id = data.unit_id;
            this.registrasi.unit_nama = data.unit_nama;
            this.registrasi.ruang_id = data.ruang_id;
            this.registrasi.ruang_nama = data.ruang_nama;
            
            this.registrasi.unit_asal_id = data.unit_asal_id;
            this.registrasi.unit_asal = data.unit_asal;
            this.registrasi.ruang_asal_id = data.ruang_asal_id;
            this.registrasi.ruang_asal = data.ruang_asal;
            this.registrasi.bed_asal_id = data.bed_asal_id;
            this.registrasi.no_asal_bed = data.no_asal_bed;
            
            this.registrasi.kelas_harga_id = data.kelas_harga_id;
            this.registrasi.kelas_harga = data.kelas_harga;
            this.registrasi.total = data.transaksi.total;
            
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
            this.registrasi.detail = data.detail;

            this.registrasi.diagnosa = data.diagnosa;
            this.registrasi.ket_klinis = data.ket_klinis;
            this.registrasi.is_multi_hasil = data.is_multi_hasil;
            this.registrasi.jenis_cito = data.jenis_cito; 
            this.registrasi.is_cito = data.is_cito;
            this.registrasi.media_hasil = data.media_hasil;
            this.registrasi.expertise_id = data.expertise_id;
            this.registrasi.expertise_nama = data.expertise_nama;

            if(this.registrasi.jns_kelamin == "L") { this.registrasi.jns_kelamin = "LAKI-LAKI"; }
            else if(this.registrasi.jns_kelamin == "P") { this.registrasi.jns_kelamin = "PEREMPUAN"; }
            else { this.registrasi.jns_kelamin = "TIDAK TAHU"; }
        },

    }
}
</script>
<style>
th.tb-header-reg {
    padding:1em; 
    background-color:#cc0202; 
    color:white;
}

.view-lab-container div {
    padding:0 2px 0 2px;margin:0;
}

div.view-lab-container label {
    font-weight:500;
}
</style>