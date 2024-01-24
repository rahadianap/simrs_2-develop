<template>
    <div>
        <div class="uk-grid-small" uk-grid>
            <!-- <div class="uk-width-auto">
                <img class="uk-border-circle1" width="120" height="120" :src="jadwalDokter.url_avatar" alt="Avatar">
            </div> -->
            <div class="uk-width-expand" style="padding-top:1em;">
                <h2 class="modal-title">{{ rujukan.booking_id }}</h2>
                <!-- <h4 class="modal-subtitle">{{ rujukan.pasien_id }}</h4> -->
            </div>
        </div>
        <div style="margin-top:1em;">
            <div class="uk-grid-small" uk-grid style="padding:1em;">
                <div class="info-container uk-width-1-2">
                    <p class="title-info">Dokter : </p>
                    <p class="content-info">{{ rujukan.dokter_nama }}</p>
                </div>
                <div class="info-container uk-width-1-2">
                    <p class="title-info">Unit Poli : </p>
                    <p class="content-info">{{ rujukan.unit_nama }}</p>
                </div>

                <div class="info-container uk-width-1-2">
                    <p class="title-info">Tanggal Periksa : </p>
                    <p class="content-info">{{ rujukan.tgl_periksa }} {{ rujukan.jam_periksa }}</p>
                </div>

                <div class="info-container uk-width-1-2">
                    <p class="title-info">Tanggal Registrasi : </p>
                    <p class="content-info">{{ rujukan.tgl_registrasi }}</p>
                </div>
                
                <div class="info-container uk-width-1-2">
                    <p class="title-info">No. RM : </p>
                    <p class="content-info">{{ rujukan.no_rm }}</p>
                </div>

                <div class="info-container uk-width-1-2">
                    <p class="title-info">NIK : </p>
                    <p class="content-info">{{ rujukan.nik }}</p>
                </div>

                <div class="info-container uk-width-1-2">
                    <p class="title-info">No. BPJS : </p>
                    <p class="content-info">{{ rujukan.no_kartu }}</p>
                </div>

                <div class="info-container uk-width-1-2">
                    <p class="title-info">No. Rujukan : </p>
                    <p class="content-info">{{ rujukan.no_rujukan }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name : 'data-rujukan',
    props : {
        rujukan : {type: Object, required:true },
        isSimple : {type:Boolean, required:false, default:false }
    },
    methods : {

    }
}
</script>