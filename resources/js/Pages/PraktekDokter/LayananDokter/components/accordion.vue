<template>
  <div v-if="data">
    <div class="uk-width-1-1 uk-margin uk-card uk-card-default">
      <button class="uk-button uk-button-primary" title="Cetak" style="border: none;background-color:#fdf5c8;width: 100%;" @click.prevent="printHisMedis(data)">
          <font-awesome-icon :icon="['fas', 'print']" style="color: #000000;margin-right:3px;" /> Cetak Catatan Medis
      </button>
      <div :class="{ 'uk-card-small': !isCardExpanded }">
        <div class="uk-card-header uk-card-default" @click="toggleCardState">
          <div class="uk-card-title uk-width-3-5">
            <div class="uk-width-1-1@m">
                <div style="margin-bottom: 20px;">
                    <p class="offcanvas-content-title uk-margin-remove-bottom">Tanggal Daftar : </p>
                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ data.tgl_registrasi }}</p>
                </div>
                <div style="margin-bottom: 20px;">
                    <p class="offcanvas-content-title uk-margin-remove-bottom">Tanggal Periksa : </p>
                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ data.tgl_periksa }}</p>
                </div>

                <div style="margin-bottom: 20px;">
                    <p class="offcanvas-content-title uk-margin-remove-bottom">No. Registrasi : </p>
                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ data.reg_id }}</p>
                </div>

                <div style="margin-bottom: 20px;">
                    <p class="offcanvas-content-title uk-margin-remove-bottom">Dibuat oleh : </p>
                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ data.dokter_nama }}</p>
                </div>
            </div> 
          </div>
          
          <font-awesome-icon 
            class="uk-padding-small" :icon="[isCardExpanded ? 'fas' : 'fas', isCardExpanded ? 'angle-up' : 'angle-down']" size="xl"
            style="position: static; top: 50%; transform: translateY(-50%); right: 50px;"/>
        </div>
        <div class="uk-card-body">
          <slot>
            <div class="offcanvas-content-subject" style="padding:10px 5px;height:auto;">
              <!--Tanda Vital-->
              <div>
                <div class="uk-flex uk-width-1-1 offcanvas-border-bottom">
                  <hr class="offcanvas-content-divider" />
                  <div>{{ itemsEMR(0) }}</div>
                </div>
                
                <div v-if="data.tanda_vital" class="uk-margin-small-top uk-margin-small-left uk-margin-bottom">
                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p> {{ titleDataVital(0) }} </p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.tanda_vital.tinggi_badan }} cm
                      </p>
                    </div>
                  </div>

                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p> {{ titleDataVital(1) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.tanda_vital.berat_badan }} Kg
                      </p>
                    </div>
                  </div>

                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataVital(2) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.tanda_vital.diastole }} mmHg
                      </p>
                    </div>
                  </div>

                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p> {{ titleDataVital(3) }} </p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.tanda_vital.sistole }} mmHg
                      </p>
                    </div>
                  </div>

                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataVital(4) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.tanda_vital.suhu }} °C
                      </p>
                    </div>
                  </div>

                  
                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataVital(5) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.tanda_vital.denyut_nadi }} bpm
                      </p>
                    </div>
                  </div>

                  
                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataVital(6) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.tanda_vital.pernapasan }} bpm
                      </p>
                    </div>
                  </div>

                 
                </div>
              </div>

              <!--SOAP-->
              <div>
                <div class="uk-flex uk-width-1-1 offcanvas-border-bottom">
                  <hr class="offcanvas-content-divider" />
                  <div>{{itemsEMR(1)}}</div>
                </div>
                
                <div v-if="data.soap" class="uk-margin-small-top uk-margin-small-left uk-margin-bottom">

                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataSOAP(0) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.soap.subjective }}
                      </p>
                    </div>
                  </div>

                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataSOAP(1) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.soap.objective }}
                      </p>
                    </div>
                  </div>

                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataSOAP(2) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.soap.assesment }}
                      </p>
                    </div>
                  </div>

                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataSOAP(3) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.soap.plan }}
                      </p>
                    </div>
                  </div>

                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataSOAP(4) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.soap.evaluation }}
                      </p>
                    </div>
                  </div>

                  <div class="uk-grid uk-grid-small">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataSOAP(5) }}</p>
                    </div>
                    <div class="uk-width-1-2">
                      <p style="font-weight:400">
                        : {{ data.soap.intervention }}
                      </p>
                    </div>
                  </div>

                  <div class="uk-grid uk-grid-small uk-margin-top">
                    <div class="uk-width-1-4">
                      <p>{{ titleDataSOAP(6) }}  :</p>
                    </div>
                    <div class="uk-width-1-1">
                      <p style="font-weight:400">
                        {{ data.soap.catatan }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!--Diagnosa-->
              <div>
                <div class="uk-flex uk-width-1-1 offcanvas-border-bottom">
                  <hr class="offcanvas-content-divider" />
                  <div>{{itemsEMR(2)}}</div>
                </div>
                
                <div v-for="itm in data.diagnosa" :key="itm in data.diagnosa" class="uk-margin-small-top uk-margin-small-left uk-margin-bottom">
                  <p style="margin:0;padding:0;">{{ itm.kode_icd }} - {{ itm.nama_icd }}</p>
                  <p style="margin:0;padding:0;">
                    <span class="uk-label">kasus:{{ itm.tipe }}</span>
                    <span style="margin-left:5px;" class="uk-label" v-if="itm.kasus_lama">Kasus Lama</span>
                    <span style="margin-left:5px;" class="uk-label" v-else>Kasus Baru</span>
                  </p>
                </div>
              </div>
              
              <!--Tindakan-->
              <div>
                <div class="uk-flex uk-width-1-1 offcanvas-border-bottom">
                  <hr class="offcanvas-content-divider" />
                  <div>{{itemsEMR(3)}}</div>
                </div>
                
                <div v-if="data.tindakan" class="uk-margin-small-top uk-margin-small-left uk-margin-bottom">
                  <div v-for="itm in data.tindakan" :key="itm in data.tindakan" class="uk-grid uk-grid-small">
                    <div class="uk-width-3-5">
                      <p> {{ itm.tindakan_nama }} </p>
                    </div>
                    <div class="uk-width-1-5">
                      <p> {{ itm.jumlah }} {{itm.satuan}} </p>
                    </div>
                    <div class="uk-width-1-5 uk-text-right">
                      <p> {{ itm.subtotal }} </p>
                    </div>
                  </div>
                  <!-- <p   style="margin:0;padding:0;">{{ itm.tindakan_nama }} ({{ itm.jumlah }} {{ itm.satuan }})</p> -->
                </div>
              </div>
              
              <!--Resep-->
              <div>
                <div class="uk-flex uk-width-1-1 offcanvas-border-bottom">
                  <hr class="offcanvas-content-divider" />
                  <div>{{itemsEMR(4)}}</div>
                </div>
                
                <div v-if="data.resep"  class="uk-margin-small-top uk-margin-small-left uk-margin-bottom">
                  <div v-for="itm in data.resep" :key="itm in data.resep" class="uk-grid uk-grid-small">
                    <div class="uk-width-medium">
                      <p> {{ itm.item_nama }} </p>
                    </div>
                    <div class="uk-width-1-5@m">
                      <p> {{ itm.jumlah }} {{itm.satuan}} </p>
                    </div>
                    <div class="uk-width-auto@m">
                      <p> {{ itm.signa_deskripsi }} </p>
                    </div>
                  </div>
                  <!-- <p style="margin:0;padding:0;">{{ itm.item_nama }} ({{ itm.jumlah }} {{ itm.satuan }})</p>
                  <p style="margin:0;padding:0;font-weight:400; font-style: italic;">{{ itm.signa_deskripsi }})</p> -->
                </div>
              </div>

            </div>
          </slot>
        </div>
      </div>
    </div>
  </div>
  <!-- <button class="uk-button uk-button-default uk-button-small uk-align-right" style="background:cornflowerblue;:black;border-radius:5px;position:sticky;z-index: 99;bottom: 0;height:46px" @click="closeAllCards">Tutup Semua</button> -->
  </template>
  
  
  <script>
import { mapActions } from 'vuex';
import "../../Dashboard/tabs/dashboardStyle.css";
import { far } from '@fortawesome/free-regular-svg-icons';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
library.add(far, fas);

  export default {
    name: 'accordion',
    components: 'FontAwesomeIcon',
    props: {        
      data : { type : Object, required : true }  
    },
    data() {
      return {
        isCardExpanded : false,
        isLoading : false,
        isUpdate : false,
        no_rm : '0001',
        items: ['Tanda Vital', 'SOAP', 'Diagnosa', 'Tindakan', 'Resep'],
        tandaVital: ['Tinggi Badan', 'Berat Badan', 'Diastole', 'Sistole', 'Suhu Badan', 'Nadi', 'Pernapasan'],
        soap: ['Subject','Objective','Assestment','Plan', 'Evaluation', 'Intervention', 'Note'],
        // diagnosa: [''],
        // tindakan: [''],
        // resep: [''],
      };
    },

    computed: {

    },

    methods: {
      ...mapActions('praktekDokter',['cetakHistory']),

        itemsEMR(index) {
          return this.items[index];
        },

        titleDataVital(index) {
          return this.tandaVital[index];
        },

        titleDataSOAP(index) {
          return this.soap[index];
        },

        titleDataDiagnosa(index) {
          return this.diagnosa[index];
        },

        titleDataTindakan(index) {
          return this.tindakan[index];
        },

        titleDataResep(index) {
          return this.resep[index];
        },

        toggleCardState() {
          this.isCardExpanded = !this.isCardExpanded;
        },

        closeAllCards() {
          for (let index in this.isCardExpanded) {
            this.isCardExpanded[index] = false;
          }
          // this.isCardExpanded[index]=false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        },


        printHisMedis(data){
          this.cetakHistory(data).then((response) => {
              var newNode = document.createElement('p');
              newNode.appendChild(document.createTextNode('html string'));
              this.printDiv(response,"a4");
          });
        },
        
        getDateFormated(curDate){
              const today = new Date(curDate);
              const yyyy = today.getFullYear();
              let mm = today.getMonth() + 1; // Months start at 0!
              let dd = today.getDate();

              if (dd < 10) dd = '0' + dd;
              if (mm < 10) mm = '0' + mm;

              const formattedToday = dd + '/' + mm + '/' + yyyy;
              return formattedToday;
        },

        printDiv(pdf_body, paperSize) {
            const customPaperSize = paperSize;
            let frame1 = document.createElement('iframe');
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            let frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();

            // Set custom paper size using CSS @page rule
            const customStyles = `
                <style>
                    @page {
                        size: ${customPaperSize};
                    }
                </style>
            `;

            frameDoc.document.write(customStyles);

            // Use the entire content as one page
            const fullHtml = pdf_body;

            frameDoc.document.write(fullHtml);
            frameDoc.document.close();

            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 1000);

            return false;
        },


    },
  };
  </script>
<style scoped>
.card {
  margin-bottom: 10px;
  width: 50%;
}

.uk-card {
  border: 1px solid #ccc;
  border-radius: 5px;
}

.uk-card-default {
  background: #E7F1FF;
}

.uk-card-header {
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  /* background-color: #f5f5f5; */
  /* border-bottom: 1px solid #ccc; */
}

.uk-card-title {
  margin: 0;
}

.uk-icon-button {
  font-size: 24px;
  transition: transform 150ms ease-out;
}

.uk-card-body {
  padding: 10px;
}

/* Style when not expanded */
.uk-card-small .uk-icon-button {
  transform: rotate(0deg);
  color: #333;
}

.uk-card-small .uk-card-body {
  display: none;
}


</style>
  