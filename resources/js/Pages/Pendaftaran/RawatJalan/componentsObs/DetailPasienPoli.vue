<template>
    <div style="padding:0.5em;margin:0;">
        <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid style="padding:0;margin:0;"> 
            <div class="uk-width-1-3@m">
                <dl class="uk-description-list pasien-description-list">
                    <dt>No.Antrian</dt>
                    <dd>{{detailData.no_antrian}}</dd>
                    <dt>Waktu Periksa</dt>
                    <dd>{{detailData.tgl_periksa}} {{detailData.jam_periksa}}</dd>
                    <dt>Dokter Pemeriksa</dt>
                    <dd>{{detailData.dokter_nama}}</dd>
                    <dt>Unit (Poli)</dt>
                    <dd>{{detailData.unit_nama}}</dd>
                </dl>
            </div>
            <div class="uk-width-1-3@m">
                <dl class="uk-description-list pasien-description-list">
                    <dt>No.RM | ID</dt>
                    <dd>{{detailData.pasien.no_rm}} | {{detailData.pasien.pasien_id}}</dd>
                    <dt>Nama</dt>
                    <dd>{{detailData.pasien.nama_lengkap}}</dd>
                    <dt>Tempat Tanggal Lahir</dt>
                    <dd>{{detailData.pasien.tempat_lahir}}, {{dateFormatting(detailData.pasien.tgl_lahir)}}</dd>
                    <dt>Jenis Kelamin</dt>
                    <dd>
                        <span v-if="detailData.pasien.jns_kelamin == 'L'">Laki-laki</span>
                        <span v-else-if="detailData.pasien.jns_kelamin == 'P'">Perempuan</span>
                        <span v-else>Tidak Tahu</span>                    
                    </dd>
                </dl>
            </div>
            <div class="uk-width-1-3@m">
                <dl class="uk-description-list pasien-description-list">
                    <dt>Penjamin</dt>
                    <dd>{{detailData.pasien.jns_penjamin}} - {{detailData.pasien.penjamin_nama}}</dd>
                    <dt>No. Kepesertaan</dt>
                    <dd>{{detailData.pasien.no_kepesertaan}}</dd>
                    <dt>ID Kependudukan</dt>
                    <dd>{{detailData.pasien.nik}}</dd>
                    <dt>No. Kartu Keluarga</dt>
                    <dd>{{detailData.pasien.no_kk}}</dd>
                </dl>
            </div>
            <!-- <div class="uk-width-1-3@m">
                <dl class="uk-description-list pasien-description-list">
                    <dt>Telepon</dt>
                    <dd>{{detailData.detail.no_telepon}}</dd>
                    <dt>Email</dt>
                    <dd>{{detailData.detail.alamat_email}}</dd>
                    <dt>Kontak Darurat</dt>
                    <dd>{{detailData.detail.nama_kontak_darurat}} - {{detailData.detail.no_kontak_darurat}}</dd>
                    <dt>Kebangsaan</dt>
                    <dd>{{detailData.detail.kebangsaan}} ({{detailData.detail.suku_bangsa}})</dd>
                </dl>
            </div> -->
        </div>
    </div>
</template>
<script>
export default {
    props : {
        detailData : { type:Object, required:false, default:null },
        detailFunctions : { type:Object, required:false, default:null }
    },
    name : 'detail-pasien-poli',
    data() {
        return {
            isExpanded : false,
            buffer : [],
        }
    },
    methods : {
        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString(); 
            return val;
        }
    }
}
</script>
<style>
dl.pasien-description-list dt {
    margin:0;
    padding:0;
    font-size:11px;
    font-weight: bold;
    color:#333;
}

dl.pasien-description-list dd {
    margin:0;
    padding:0;
    font-size:12px;
    font-weight: 400;
    color:#000;
}
</style>