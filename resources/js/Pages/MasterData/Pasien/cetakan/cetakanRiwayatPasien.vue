<template>
    <div>
        <vue3-html2pdf :show-layout="false" :float-layout="true" :enable-download="false" :preview-modal="true"
            :paginate-elements-by-height="1400" filename="hee hee" :pdf-quality="2" :manual-pagination="false"
            pdf-format="a4" pdf-orientation="portrait" pdf-content-width="800px"
            @hasStartedGeneration="hasStartedGeneration()" @hasGenerated="hasGenerated($event)" ref="html2Pdf">

            <template v-slot:pdf-content>
                <div class="uk-grid-small uk-align-center" uk-grid>
                    <div>
                        <div class="uk-width-1-1 uk-margin-small-top">
                            <h3 class="uk-text-emphasis uk-text-center" style="text-align:center;color:black;margin-left:28px;margin-top:28px;font-size:20px;font-weight: 500;">RINGKASAN RIWAYAT KLINIK</h3>
                        </div>
                    </div>
                </div>
                <div class="uk-margin-auto uk-margin-auto-vertical" uk-grid>
                    <div>
                        <div>
                            <h3 style="font-size:14px;">Tanggal</h3>                       
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-top:-10px;">Nama Pasien</h3>
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-top:-10px;">Tanggal Lahir</h3>
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-top:-10px;">Pendidikan</h3>
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-top:-10px;">Pekerjaan</h3>
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-top:-10px;">Alamat</h3>
                        </div>
                    </div>
                    <div>
                        <div>
                            <h3 style="font-size:14px;">{{ ': ' + new Date().toISOString().slice(0,10) }}</h3>                       
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-top:-10px;">{{ ': ' + this.reportData.pasien }}</h3>
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-top:-10px;">{{ ': ' + this.reportData.tgl_lahir }}</h3>
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-top:-10px;">Pendidikan</h3>
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-top:-10px;">Pekerjaan</h3>
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-top:-10px;width:150px;">{{ ': ' + this.reportData.alamat }}</h3>
                        </div>
                    </div>
                    <div>
                        <div>
                            <h3 style="font-size:14px;margin-left: 20px;">No. RM</h3>                       
                        </div>
                        <div>
                            <h3 style="font-size:20px;margin-top:-15px;margin-left: 20px;font-weight: bold;">{{ this.reportData.no_rm }}</h3>
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-left:20px;margin-top:-10px;">Jenis Kelamin</h3>                       
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-left:20px;margin-top:-10px;">Penjamin</h3>                       
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-left:20px;margin-top:-10px;">No. Klien</h3>                       
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-left:20px;margin-top:-10px;">No. Telp</h3>                       
                        </div>
                    </div>
                    <div>
                        <div>
                            <h3 style="font-size:14px;margin-left:20px;margin-top:62px;">{{ ': ' + this.reportData.jk }}</h3>                       
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-left:20px;margin-top:-10px;">{{ ': ' + this.reportData.penjamin }}</h3>                       
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-left:20px;margin-top:-10px;">{{ ': ' + this.reportData.no_klien }}</h3>                       
                        </div>
                        <div>
                            <h3 style="font-size:14px;margin-left:20px;margin-top:-10px;">{{ ': ' + this.reportData.no_telepon }}</h3>                       
                        </div>
                    </div>
                </div>
                <div class="uk-margin-auto uk-margin-auto-vertical" uk-grid>
                    <div>
                        <div>
                            <table align="center" class="css-serial" style="font-size:10px;font-weight:100;border:0.1px solid #000000;font-family: arial,sans-serif;border-collapse:collapse;width:100%;">
                                <tr>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 75px;">Tanggal</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 75px;">Poliklinik</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 75px;">Dokter</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 75px;">Diagnosa</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 100px;">Kode</th>
                                    <th style="color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 150px;">Keterangan</th>
                                </tr>
                                <tr v-for="dt in reportData.item_name">
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 5px;width: 75px;">{{dt['tgl_transaksi']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 5px;width: 75px;">{{dt['unit']}}</td>
                                    <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 5px;width: 75px;">{{dt['dokter']}}</td>
                                    <!-- <td style="color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 5px;width: 75px;">{{dt['tgl_periksa']}}</td> -->
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
        </vue3-html2pdf>
    </div>
</template>

<script>
import Vue3Html2pdf from 'vue3-html2pdf';

export default {
    name: 'cetakan-riwayat-pasien',
    components: {
        Vue3Html2pdf
    },
    data() {
        return {
            reportData: {
                pasien: '',
                no_rm: '',
                tgl_lahir: '',
                pendidikan: '',
                pekerjaan: '',
                alamat: '',
                kelurahan: '',
                kecamatan: '',
                kota: '',
                sex: '',
                penjamin: '',
                no_klien: '',
                no_telepon: '',
                item_name: []
            },
        }
    },
    computed: {
        htmlToPdfOptions() {
            return {
                margin: 0,
                filename: "hee hee.pdf",
                image: {
                    type: "jpeg",
                    quality: 2,
                },
                enableLinks: true,
                html2canvas: {
                    scale: 1,
                    useCORS: true,
                },
                jsPDF: {
                    unit: "mm",
                    format: "a4",
                    orientation: 'portrait',
                },
            };
        },
    },
    methods: {

        generateReport(data) {
            this.reportData.detail = [];
            this.reportData.pasien = data[0].nama_lengkap;
            this.reportData.no_rm = data[0].no_rm;
            this.reportData.tgl_lahir = data[0].tgl_lahir;
            this.reportData.alamat = data[0].alamat;
            this.reportData.kelurahan = data[0].kelurahan;
            this.reportData.kecamatan = data[0].kecamatan;
            this.reportData.kota = data[0].kota;
            this.reportData.jk = data[0].jns_kelamin;
            this.reportData.penjamin = data[0].penjamin_nama;
            if (this.reportData.penjamin == 'PRIBADI')
            {
                this.reportData.no_klien = '-';
            }else{
                this.reportData.no_klien = data[0].no_kepesertaan;
            }
            this.reportData.no_telepon = data[0].no_telepon;
            for(let i=0;i<=data.length-1;i++) {
                this.reportData.item_name.push({
                    'tgl': data[i].tgl_transaksi,
                    'dokter': data[i].dokter_nama,
                    'unit': data[i].unit_nama
                });
            }
            this.$refs.html2Pdf.generatePdf();
        }
    },
}
</script>

<style type="text/css">
/* Penomoran otomatis pada baris */
.css-serial {
  counter-reset: serial-number;  /* Atur penomoran ke 0 */
}
.css-serial td:first-child:before {
  counter-increment: serial-number;  /* Kenaikan penomoran */
  content: counter(serial-number);  /* Tampilan counter */
}
</style>