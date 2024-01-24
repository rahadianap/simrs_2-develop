<template>
    <div class="uk-modal" uk-modal="bg-close:false;" :id="modalId">
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-body">
                <data-pasien ref="dataPasienModal" :pasien="pasien"></data-pasien>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <div class="uk-grid-small" uk-grid>
                    <button class="uk-button-large uk-button uk-modal-close" type="button" style="background-color:#fff;color:#000;">
                        <span  uk-icon="icon: close; ratio: 2"></span>
                        SALAH, ini bukan data saya
                    </button>
                    <button class="uk-button-large uk-button" @click.prevent="pasienSelected" style="background-color:#cc0202;color:#fff; margin-left:10px;">
                        <span  uk-icon="icon: check; ratio: 2"></span>
                        BENAR, ini data saya
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
    name : 'modal-pasien',
    props : {
        fnSelect : { type:Function, required:false }
    } ,
    components : {
        DataPasien,
    },
    data() {
        return {
            modalId : 'modalPasien', 
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
            this.idModal = `modalPasien-${Date.now()}`;
        },

        pasienSelected(){
            this.fnSelect(this.pasien);
            UIkit.modal(`#${this.modalId}`).hide();
        },
        showModal(data) {
            console.log(data);
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