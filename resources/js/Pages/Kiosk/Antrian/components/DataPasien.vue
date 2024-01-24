<template>
    <div>
        <div class="uk-grid-small" uk-grid>
            <!-- <div class="uk-width-auto">
                <img class="uk-border-circle1" width="120" height="120" :src="jadwalDokter.url_avatar" alt="Avatar">
            </div> -->
            <div class="uk-width-expand" style="padding-top:1em;">
                <h2 class="modal-title">{{ pasien.no_rm }} - {{ pasien.nama_lengkap }}</h2>
                <h4 class="modal-subtitle">{{ pasien.pasien_id }}</h4>
            </div>
        </div>
        <div style="margin-top:1em;">
            <div class="uk-grid-small" uk-grid style="padding:1em;">
                <div class="info-container uk-width-1-2">
                    <p class="title-info">Jenis Kelamin : </p>
                    <p v-if="pasien.jns_kelamin == 'L'" class="content-info">LAKI-LAKI</p>
                    <p v-else-if="pasien.jns_kelamin == 'P'" class="content-info">PEREMPUAN</p>
                    <p v-else class="content-info">TIDAK TAHU</p>                    
                </div>
                <div class="info-container uk-width-1-2">
                    <p class="title-info">Tempat Tanggal Lahir : </p>
                    <p class="content-info">{{ pasien.tempat_lahir }} {{ pasien.tgl_lahir }}</p>
                </div>
                <div class="info-container uk-width-1-2">
                    <p class="title-info">No.Telepon : </p>
                    <p class="content-info">{{ pasien.no_telepon }}</p>
                </div>
                <div class="info-container uk-width-1-2">
                    <p class="title-info">NIK | No.KK : </p>
                    <p class="content-info">{{ pasien.nik }} | {{ pasien.no_kk }}</p>
                </div>
                <div v-if="!isSimple" class="info-container uk-width-1-2">
                    <p class="title-info">Alamat KTP : </p>
                    <p class="content-info">{{ pasien.alamat_ktp }}</p>
                </div>
                <div v-if="!isSimple" class="info-container uk-width-1-2">
                    <p class="title-info">Alamat Tinggal : </p>
                    <p class="content-info">{{ pasien.alamat_tinggal }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name : 'data-pasien',
    props : {
        pasien : {type: Object, required:true },
        isSimple : {type:Boolean, required:false, default:false }
    },
    methods : {

    }
}
</script>