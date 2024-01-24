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
                            <h3 class="uk-text-emphasis uk-text-center" style="text-align:center;color:black;margin-left:28px;margin-top:28px;font-size:20px;font-weight: 500;">LAPORAN RUJUKAN INTERNAL LABORATORIUM</h3>
                        </div>
                    </div>
                </div>
                <div class="uk-margin-auto uk-margin-auto-vertical" uk-grid>
                    <div>
                        <div>
                            <table align="center" class="css-serial" style="font-size:12px;font-weight:500;border:0.1px solid #000000;font-family: arial,sans-serif;border-collapse:collapse;width:100%;">
                                <tr>
                                    <th style="word-break: break-word;color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 30px">No.</th>
                                    <th style="word-break: break-word;color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 350px">Nama Dokter</th>
                                    <th style="word-break: break-word;color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 50px">Poli</th>
                                    <th style="word-break: break-word;color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 50px">Ranap</th>
                                    <th style="word-break: break-word;color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 50px">IGD</th>
                                    <th style="word-break: break-word;color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 50px">Walk In</th>
                                    <th style="word-break: break-word;color:black;border: 0.1px solid #000000;background-color:#c9c9c9;border-collapse:collapse;text-align: center;padding: 5px;width: 50px">Total</th>
                                </tr>
                                <tr v-for="dt in reportData.detail">
                                    <td style="word-break: break-word;color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 5px;width: 30px"></td>
                                    <td style="word-break: break-word;color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: left;padding: 5px;width: 350px">{{dt['dokter_nama']}}</td>
                                    <td style="word-break: break-word;color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 5px;width: 50px">{{dt['poli']}}</td>
                                    <td style="word-break: break-word;color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 5px;width: 50px">{{dt['ranap']}}</td>
                                    <td style="word-break: break-word;color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 5px;width: 50px">{{dt['igd']}}</td>
                                    <td style="word-break: break-word;color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 5px;width: 50px">{{dt['walk_in']}}</td>
                                    <td style="word-break: break-word;color:black;border: 0.1px solid #000000;border-collapse:collapse;text-align: center;padding: 5px;width: 50px">{{dt['total']}}</td>
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
    name: 'report-rujukan-lab',
    components: {
        Vue3Html2pdf
    },
    data() {
        return {
            reportData: {
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
            for(let i=0;i<=data.length-1;i++) {
                this.reportData.detail.push({
                    'dokter_nama': data[i].dokter_nama,
                    'poli': data[i].poli,
                    'ranap': data[i].ranap,
                    'igd': data[i].igd,
                    'walk_in': data[i].walk_in,
                    'total': parseInt(data[i].poli) + parseInt(data[i].ranap) + parseInt(data[i].igd) + parseInt(data[i].walk_in)
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