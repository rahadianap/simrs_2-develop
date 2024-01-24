<template>
    <div class="uk-modal" uk-modal="bg-close:false;" :id="modalId">
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-body">
                <data-pasien ref="dataPasienModal" :pasien="pasien"></data-pasien>
            </div>
            <div class="uk-modal-footer uk-text-center">
                <p style="font-size:11px; font-weight: 400;margin:0;padding:0;font-style:italic;">untuk mengurangi penggunaan kertas dalam mendukung program pelestarian alam, anda dapat mem-foto bukti pendaftaran diatas, atau klik CETAK untuk mencetak.</p>
                <p style="font-size:11px; font-weight: 400;margin:1em 0 1em 0;padding:0.5em;font-style:italic;">Terima Kasih.</p>
                
                <div class="uk-grid-small" uk-grid>
                    <button class="uk-button-large uk-button uk-width-1-2" @click.prevent="pasienSelected" type="button" style="background-color:#fff;color:#000;">
                        TUTUP
                    </button>
                    <button class="uk-button-large uk-button uk-width-1-2" style="background-color:#cc0202;color:#fff;">
                        CETAK
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import DataPasien from '@/Pages/Kiosk/Antrian/components/DataPasien.vue';
export default {
    name : 'modal-pasien-baru',
    components : {
        DataPasien,
    },
    data() {
        return {
            modalId : 'modalPasienBaru', 
            pasien : {
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                salut : null,
                nama_lengkap : null,
                nik : null,
                no_kk : null,
                no_telepon : null,
                jns_kelamin : null,
                tgl_lahir : null,
                tempat_lahir : null,
                alamat_tinggal : null,
                alamat_ktp : null,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kiosk',['kioskData','poliLists']),
    },
    mounted() {
        this.init();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('kiosk',['SET_PASIEN','CLR_DATA','SET_JADWAL']),
        ...mapActions('kiosk',['jknAntrianData','pasienData','listUnitPoli']),
        
        init(){
            this.idModal = `modalPasienBaru-${Date.now()}`;
        },

        pasienSelected(){
            this.$emit('modalPasienBaruClosed',true);
            UIkit.modal(`#${this.modalId}`).hide();
        },

        showModal(data) {
            this.pasien.pasien_id = data.pasien_id;
            this.pasien.no_rm = data.no_rm;
            this.pasien.nama_pasien = data.nama_pasien;
            this.pasien.salut = data.salut;
            this.pasien.nama_lengkap = data.nama_lengkap;
            this.pasien.nik = data.nik;
            this.pasien.no_kk = data.no_kk;
            this.pasien.jns_kelamin = data.jns_kelamin;
            this.pasien.no_telepon = data.no_telepon;
            this.pasien.tgl_lahir = data.tgl_lahir;
            this.pasien.tempat_lahir = data.tempat_lahir; 
            this.pasien.alamat_tinggal = data.alamat_tinggal;
            this.pasien.alamat_ktp = data.alamat_ktp;
            
            UIkit.modal(`#${this.modalId}`).show();
        },

        closeModal(){
            UIkit.modal(`#${this.modalId}`).hide();
        }    
    }
}
</script>
<style>
</style>