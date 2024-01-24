<template>
    <div style="padding: 3em;">
        <div class="uk-container paper-like" style="padding:0 1em 10em;height: auto;" >
            <div style="position: sticky;top:10px;z-index:100;">
                <div class="uk-width-1-1 uk-text-right">
                    <input class="submit" type="submit" value="Print" style="margin-right:-120px" @click="printDiv('cetakanBiayaPerawatan')">
                </div>
            </div>
            <ul style="padding:0;margin:0;" id="cetakanBiayaPerawatan">
                <div v-html="htmlContent" id="appHtmlContent"></div>
            </ul>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    name : 'cetakan-kwitansi', 
    components : {
    },
    props : {
    },
    data() {
        return {    
            htmlContent: "",
            no_reg : '',
            no_rm : '',
            nama_pasien : '',
            tgl_lahir : '',
            dokter_utama : '',
            tgl_masuk : '',
            alamat : '',
            alasan_pulang : '',
            penjamain : '',
            instansi : '',
            tgl_keluar : '',
            hak_kelas : '',
            lama_perawatan : '',
            ruang_perawatan : '',
            kelas : '',
            no_tempat_tidur : '',
            data_alkes : [],
            data_farmasi : [],
            data_jasa_visit : [],
            data_laboratorium : [],
            data_obat : [],
            data_radiologi : [],
            data_ruangan : [],
            data_tindakan_medis : [],
            data_tindakan_ranap : [],
            data_tindakan_operasi : [],
        }
    },
    mounted() {
        this.fetchBladeContent();
    },
    methods : {
        ...mapActions('pdf',['dataRawatInap']),
        fetchBladeContent() {
            this.dataRawatInap().then((response) => {
                var tag_id = document.getElementById('appHtmlContent');
                var newNode = document.createElement('p');
                newNode.appendChild(document.createTextNode('html string'));
                this.htmlContent = response;
            })
        },
        
        printDiv(pdf_body) {
            let contents = document.getElementById(pdf_body).innerHTML;
            let frame1 = document.createElement('iframe');
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            let frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();
            frameDoc.document.write('<html lang="en"><head><title>SIMRS</title>');
            frameDoc.document.write('</head><body>');
            frameDoc.document.write('<style>@page { zoom: 90%; } hr { margin:0px !important; } </style>');
            frameDoc.document.write('<div class="print-content">'+contents+'</div>');
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 1000);
           return false;
        },
       
    }
}
</script>
