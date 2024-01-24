<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalJadwalDokter">
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <!-- <div class="uk-modal-header">
                <h2 class="uk-modal-title">{{ jadwalDokter.dokter_nama }}</h2>
                <h4 class="modal-subtitle">{{ jadwalDokter.unit_nama }}</h4>
            </div> -->
            <div class="uk-modal-body">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-auto">
                        <img class="uk-border-circle1" width="120" height="120" :src="jadwalDokter.url_avatar" alt="Avatar">
                    </div>
                    <div class="uk-width-expand" style="padding-top:1em;">
                        <h2 class="modal-title">{{ jadwalDokter.dokter_nama }}</h2>
                        <h4 class="modal-subtitle">{{ jadwalDokter.unit_nama }}</h4>
                    </div>
                </div>
                <div style="margin-top:1em;">
                    <div class="info-container">
                        <p class="title-info">Pendidikan : </p>
                        <p class="content-info">{{ jadwalDokter.pend_akhir }}</p>
                    </div>
                    <div class="info-container">
                        <p class="title-info">Tempat / Tanggal Lahir : </p>
                        <p class="content-info">{{ jadwalDokter.tempat_lahir }}, {{ jadwalDokter.tgl_lahir }}</p>
                    </div>
                    <div class="info-container">
                        <p class="title-info">Biografi Singkat : </p>
                        <p class="content-info">{{ jadwalDokter.biografi }}</p>
                    </div>
                    <div class="info-container">
                        <p class="title-info">Jadwal : </p>
                        <p class="content-info">{{ jadwalDokter.nama_hari }} {{ jadwalDokter.mulai }} - {{ jadwalDokter.selesai }}</p>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button modal-jadwal-button uk-modal-close" type="button" style="border:none;">TUTUP</button>
                <button class="uk-button modal-jadwal-button-primary" type="button" @click.prevent="fnSelect(jadwalDokter)">PILIH</button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'modal-dokter',
    props : {
        fnSelect : { type:Function, required:false }
    } ,
    data() {
        return {
            jadwalDokter : {
                jadwal_id : null,
                unit_id : null,
                unit_nama : null,
                dokter_id : null,
                dokter_nama : null,
                hari : null,
                nama_hari : null,
                mulai : null,
                selesai : null,
                tanggal_registrasi : null,        
                biografi : null,
                url_avatar : null,
                tempat_lahir : null,
                tgl_lahir : null,
                pend_akhir : null,   
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kiosk',['kioskData','poliLists']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('kiosk',['SET_PASIEN','CLR_DATA','SET_JADWAL']),
        ...mapActions('kiosk',['jknAntrianData','pasienData','listUnitPoli']),
    
        showModal(data) {
            console.log(data);
            this.jadwalDokter.jadwal_id = data.jadwal_id;
            this.jadwalDokter.unit_id = data.unit_id;
            this.jadwalDokter.unit_nama = data.unit_nama;
            this.jadwalDokter.dokter_id = data.dokter_id;
            this.jadwalDokter.dokter_nama = data.dokter_nama;
            this.jadwalDokter.hari = data.hari;
            this.jadwalDokter.nama_hari = data.nama_hari;
            this.jadwalDokter.mulai = data.mulai;
            this.jadwalDokter.selesai = data.selesai;
            this.jadwalDokter.biografi = data.biografi;
            this.jadwalDokter.url_avatar = data.url_avatar;
            this.jadwalDokter.tempat_lahir = data.tempat_lahir;
            this.jadwalDokter.tgl_lahir = data.tgl_lahir;
            this.jadwalDokter.pend_akhir = data.pend_akhir;
            
            UIkit.modal('#modalJadwalDokter').show();
        },

        closeModal(){
            UIkit.modal('#modalJadwalDokter').hide();
        }    
    }
}
</script>
<style>
.modal-title {
    padding:0;
    margin:0;
    font-weight: 500;
}
.modal-subtitle {
    padding:0;
    margin:0;
    font-size: 14px;
    font-weight: 500;
}

div.info-container {
    padding:0.2em 0.2em 1em 0.2em;
}

p.title-info {
    font-size: 14px;
    font-weight: 500;
    padding:0;
    margin:0;
    color: #000;
}

p.content-info {
    font-size: 14px;
    padding:0;
    margin:0;
    color: #000;
}

.modal-jadwal-button {
    border:none;
    font-weight: 500;
    background-color: #fff;    
}

.modal-jadwal-button-primary {
    border:none;
    font-weight: 500;
    color:white;
    background-color: #cc0202;    
}

.modal-jadwal-button:hover {
    color: #000;
    border:2px solid #000;
}

</style>