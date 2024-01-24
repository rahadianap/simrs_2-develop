<template>
    <div class="uk-modal" uk-modal="bg-close:false;" :id="modalId">
        <div class="uk-modal-dialog">
            <div class="uk-modal-body">
                <div v-if="modeCetakFull" style="text-align:center;margin-bottom:1em;">
                    <p style="font-size:16px; font-weight: 400;margin:0;padding:0;">ANTRIAN PENDAFTARAN</p>
                    <h1 style="font-size:40px;font-weight:400;">{{ booking.no_pendaftaran }}</h1>
                    <p style="font-size:11px;font-style: italic;margin:0;padding:0;">
                        Siapkan data diri anda untuk melengkapi dan verifikasi 
                    </p>
                    <p style="font-size:11px;font-style: italic;margin:0;padding:0;">
                        data rekam medis anda di bagian pendaftaran. 
                    </p>
                </div>

                <div style="text-align:center;">
                    <p style="font-size:16px; font-weight: 400;margin:0;padding:0.5em;">ANTRIAN POLI</p>
                    <h2 class="modal-title">{{ booking.dokter_nama }}</h2>
                    <h4 class="modal-subtitle">{{ booking.unit_nama }}</h4>
                    <h1>{{ booking.no_antrian }}</h1>
                </div>
                
                <div style="text-align:center;">
                    <div class="info-container" >
                        <p class="title-info">Booking ID: </p>
                        <p class="content-info">{{ booking.antrian_id }}</p>
                    </div>
                    <div class="info-container">
                        <p class="title-info">Est. Jam Periksa: </p>
                        <p class="content-info">{{ booking.tgl_periksa }} {{ booking.jam_periksa }}</p>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-center">
                <p style="font-size:11px; font-weight: 400;margin:0;padding:0;font-style:italic;">untuk mengurangi penggunaan kertas dalam mendukung program pelestarian alam, anda dapat mem-foto bukti pendaftaran diatas, atau klik CETAK untuk mencetak.</p>
                <p style="font-size:11px; font-weight: 400;margin:1em 0 1em 0;padding:0.5em;font-style:italic;">Terima Kasih.</p>
                
                <button class="uk-button modal-jadwal-button uk-modal-close" type="button" style="border:none;" @click.prevent="closeModalVerif">SELESAI</button>
                <button class="uk-button modal-jadwal-button-primary" type="button">CETAK</button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name : 'modal-verif-confirm',
    props : {
        modeCetakFull : {type:Boolean, required:false, default:true }
    },
    data() {
        return {
            modalId : 'modalVerifConfirm', 
            booking : {
                nama_pasien : null,
                antrian_id : null, 
                no_antrian : null,
                angka_antrian : null,
                no_pendaftaran : null,
                angka_pendaftaran : null,
                dokter_nama : null,
                unit_nama : null,
                tgl_periksa : null,
                jam_periksa : null,                
            }
        }
    },
    mounted() {
        this.init();
    },
    methods : {
        init(){
            this.idModal = `modalVerifConfirm-${Date.now()}`;
        },

        showModal(data){
            this.booking.nama_pasien = data.nama_pasien;
            this.booking.antrian_id = data.kode_booking;
            this.booking.no_antrian = data.no_antrian;
            this.booking.angka_antrian = data.angka_antrian;
            this.booking.no_pendaftaran = data.no_pendaftaran;
            this.booking.dokter_nama = data.dokter_nama;
            this.booking.unit_nama = data.unit_nama;
            this.booking.tgl_periksa = data.tgl_periksa;
            this.booking.jam_periksa = data.jam_periksa;

            UIkit.modal(`#${this.modalId}`).show();
        },

        
        closeModalVerif(){
            this.$emit('closedModalVerifConfirm',true);
            UIkit.modal(`#${this.modalId}`).hide();
        }
    }
}
</script>
<style>

</style>