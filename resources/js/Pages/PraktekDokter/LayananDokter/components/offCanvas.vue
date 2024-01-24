<template>
    <!-- <div class="uk-flex uk-width-1-1@m" style="gap:31px;margin-bottom:55px"> -->
    <div ref="offCanvasMedrec" class="uk-width-1-2 uk-width-1-2@s" style="height:100vh;z-index:999; background-color:white; position:fixed; right:0; top:0; bottom:0; max-width:780px;">        
        <!-- Offcanvas berisi Form -->
        <div id="offcanvas-medrec-flip" class="uk-flex uk-flex-column">
            <div class="uk-offcanvas-bar offcanvas-bg uk-padding-small uk-width-1-1" style="left:0">
                <div style="background:white;position:sticky;top:0;width:auto;right:0;height:100vh;" class="uk-width-1-1" >    
                    <div style="top: 0;">
                        <div style="border-bottom: solid #D4D4D4 1px;width:auto;">
                            <h3 style="color: black;" class="offcanvas-title uk-margin-remove-bottom uk-width-expand"> {{ isCanvasTitle }}</h3>
                            <div class="uk-offcanvas-close uk-width-auto" uk-tooltip="Tutup">
                                <font-awesome-icon :icon="['fas', 'xmark']" size="xl" style="color: #d4d4d4;" @click="closeCanvas"/>
                            </div>
                        </div>
                        <div class="offcanvas-content-header" style="padding:0.5em 1em 0.5em 1em;margin-top:0.5em;">
                            <div class="uk-width-1-1 uk-grid-small" uk-grid style="color:black;">
                                <div class="uk-width-1-2@m uk-padding-small">
                                    <div style="margin-bottom: 20px;">
                                        <p class="offcanvas-content-title uk-margin-remove-bottom">No. RM : </p>
                                        <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ regData.no_rm }}</p>
                                    </div>

                                    <div style="margin-bottom: 20px;">
                                        <p class="offcanvas-content-title uk-margin-remove-bottom">Nama Pasien : </p>
                                        <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ regData.nama_pasien }} ({{ regData.jns_kelamin }})</p>
                                    </div>

                                    <div style="margin-bottom: 20px;">
                                        <p class="offcanvas-content-title uk-margin-remove-bottom">Tgl Lahir : </p>
                                        <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ dateFormatting(regData.tgl_lahir) }}</p>
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m uk-padding-small">
                                    <div style="margin-bottom: 20px;">
                                        <p class="offcanvas-content-title uk-margin-remove-bottom">No.Registrasi : </p>
                                        <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ regData.reg_id }}</p>
                                    </div>

                                    <div style="margin-bottom: 20px;">
                                        <p class="offcanvas-content-title uk-margin-remove-bottom">Tgl Periksa : </p>
                                        <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ dateFormatting(regData.tgl_periksa) }}</p>
                                    </div>

                                    <div style="margin-bottom: 20px;">
                                        <p class="offcanvas-content-title uk-margin-remove-bottom">Nama Dokter : </p>
                                        <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ regData.dokter_nama }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="uk-width-1-1" style="margin-top:10px;">
                            <div class="margin-top:10px">
                                <!-- <button v-for="letter in letterOfButtons" :key="letter"
                                    @click="scrollToCard(letter)"
                                    :class="{'offcanvas-button-active': activeLetter === letter, 'offcanvas-button': activeLetter !== letter }"
                                >
                                    {{ letter }}
                                </button> -->
                                <button type="button" @click.prevent="scrollToCard('formSoapTandaVital')" :class="{'offcanvas-button-active': activeLetter === 'formSoapTandaVital', 'offcanvas-button': activeLetter !== 'formSoapTandaVital' }">Tanda Vital</button>
                                <button type="button" @click.prevent="scrollToCard('formSoapDiv')" :class="{'offcanvas-button-active': activeLetter === 'formSoapDiv', 'offcanvas-button': activeLetter !== 'formSoapDiv' }" style="width:60px!important">SOAP</button>
                                <button type="button" @click.prevent="scrollToCard('formSoapDiagnosa')" :class="{'offcanvas-button-active': activeLetter === 'formSoapDiagnosa', 'offcanvas-button': activeLetter !== 'formSoapDiagnosa' }">Diagnosa</button>
                                <button type="button" @click.prevent="scrollToCard('formSoapTindakan')" :class="{'offcanvas-button-active': activeLetter === 'formSoapTindakan', 'offcanvas-button': activeLetter !== 'formSoapTindakan' }">Tindakan</button>
                                <button type="button" @click.prevent="scrollToCard('formSoapResep')" :class="{'offcanvas-button-active': activeLetter === 'formSoapResep', 'offcanvas-button': activeLetter !== 'formSoapResep' }" style="width:60px!important">Resep</button>
                                
                                <button class="offcanvas-button" @click.prevent="getRiwayatPasien" type="button" style="background: #05D2FF;color:#040608;cursor:pointer" uk-toggle="target: #offcanvasFlip2" uk-tooltip="Buka Riwayat ERM">Riwayat</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="uk-width-1-1"  style="color:black;height:calc(100vh - 430px); overflow:auto;margin-top:10px; padding:1em 0.5em 2em 0.5em;" > -->
            <div style="margin-top:375px;left:0" class="uk-offcanvas-bar uk-offcanvas-bar-animation uk-offcanvas-slide uk-width-1-1 offcanvas-bg uk-padding-small">
                <div id="formSoapTandaVital" class="uk-card uk-card-default uk-card-body offcanvas-content-subject offcanvas-content-body" style="padding:10px 5px;height:auto;">
                    <div class="uk-flex uk-width-1-1 offcanvas-border-bottom">
                        <div><hr class="offcanvas-content-divider" /></div>
                        <div>Form Tanda Vital</div>
                    </div>
                    <div style="padding:5px 15px 5px 5px;">
                        <form-tanda-vital ref="formTandaVital" v-on:dataChange="submitSoap"></form-tanda-vital>
                    </div>
                </div>
                <div id="formSoapDiv" class="uk-card uk-card-default uk-card-body offcanvas-content-subject offcanvas-content-body" style="padding:10px 5px;height:auto;">
                    <div class="uk-flex uk-width-1-1 offcanvas-border-bottom">
                        <div><hr class="offcanvas-content-divider" /></div>
                        <div>Form SOAP</div>
                    </div>
                    <div style="padding:5px 10px 5px 5px;">
                        <form-soap ref="formSoap" v-on:dataChange="submitSoap"></form-soap>
                    </div>
                </div>
                <div id="formSoapDiagnosa" class="uk-card uk-card-default uk-card-body offcanvas-content-subject offcanvas-content-body" style="padding:10px 5px;height:auto">
                    <div class="uk-flex uk-width-1-1 offcanvas-border-bottom">
                        <div><hr class="offcanvas-content-divider" /></div>
                        <div>Form Diagnosa</div>
                    </div>
                    <div style="padding:5px 10px 5px 5px;">
                        <form-diagnosa ref="formDiagnosa" v-on:dataChange="submitSoap"></form-diagnosa>
                    </div>
                </div>
                <div id="formSoapTindakan" class="uk-card uk-card-default uk-card-body offcanvas-content-subject offcanvas-content-body" style="padding:10px 5px;height:auto">
                    <div class="uk-flex uk-width-1-1 offcanvas-border-bottom">
                        <div><hr class="offcanvas-content-divider" /></div>
                        <div>Form Tindakan</div>
                    </div>
                    <div style="padding:5px 10px 5px 5px;" >
                        <form-tindakan ref="formTindakan" v-on:dataChange="submitSoap"></form-tindakan>
                    </div>
                    <!-- <div v-else style="padding:5px 10px 5px 5px;">
                        <form-tindakan ref="formTindakan" v-on:dataChange="submitSoap"></form-tindakan>
                    </div> -->
                </div>
                <div id="formSoapResep" class="uk-card uk-card-default uk-card-body offcanvas-content-subject offcanvas-content-body" style="padding:10px 5px;height:auto">
                    <div class="uk-flex uk-width-1-1 offcanvas-border-bottom">
                        <div><hr class="offcanvas-content-divider" /></div>
                        <div>Form Resep</div>
                    </div>
                    <div style="padding:5px 10px 5px 5px;">
                        <form-resep ref="formResep" v-on:dataChange="submitSoap"></form-resep>
                    </div>
                </div>
            </div>
        </div>
            
            
        

        <!-- Offcanvas berisi Accordion (history EMR) -->
        <div id="offcanvasFlip2" ref="offcanvas-flip2" class="uk-flex uk-flex-column" uk-offcanvas="flip: true; overlay: true;bg-close:false;esc-close:false;mode: slide;z-index:999">
            <div class="uk-offcanvas-bar offcanvas-bg uk-padding-small uk-width-1-2">
                <div style="z-index:100;position:sticky;top:0;width:auto;right:0" class="uk-width-1-1" >    
                    <div style="top: 0;">
                        <div style="border-bottom: solid #D4D4D4 1px;width:auto;">
                            <div class="offcanvas-back-button" uk-toggle="target: #offcanvasFlip2" @click="backCanvas" uk-tooltip="Kembali">
                                <font-awesome-icon :icon="['fas', 'backward-step']" size="xl" style="color: #d4d4d4;" />
                            </div>
                            <h3 style="color: black;" class="offcanvas-title uk-margin-remove-bottom uk-margin-remove-top"> {{ canvasTitle }}</h3>
                        </div>

                        <div class="offcanvas-content-header uk-margin-small uk-flex" style="color: black;">
                            <div class="uk-width-1-1 uk-padding-small uk-align-left"> 
                                
                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">No. RM : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ regData.no_rm }}</p>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">Nama Pasien : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ regData.nama_pasien }} ({{ regData.jns_kelamin }})</p>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">Tgl Lahir : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ dateFormatting(regData.tgl_lahir) }}</p>
                                </div>
                                    
                            </div>
                            <div class="uk-width-1-1 uk-padding-small uk-align-right"> 
                                
                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">No.Registrasi : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ regData.reg_id }}</p>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">Tgl Periksa : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ dateFormatting(regData.tgl_periksa) }}</p>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">Nama Dokter : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ regData.dokter_nama }}</p>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="uk-grid uk-padding-remove-right uk-grid-small uk-child-width-expand@s uk-padding-remove-bottom">
                        <div class="uk-width-3-5 uk-align-left uk-flex-start uk-margin-remove-right uk-margin-remove-left uk-margin-remove-bottom">
                            <label class="lato-md-14-semibold" style="padding:0;margin:0;color:#040608;">Filter Dokter</label>
                            <div>  
                                <div class="uk-margin">
                                    <select class="uk-select" style="color:black;border-radius:7px;border:1px solid black;" v-model="filterData.dokter" @change="getRiwayatPasien">   
                                        <option value="">pilih dokter</option>             
                                        <option v-if="doctorLists" v-for="itm in doctorLists.data" :value="itm.dokter_id">{{ itm.dokter_nama }}</option>
                                    </select> 
                                </div>
                            </div>
                        </div>

                        <div class="uk-width-1-2 uk-margin uk-margin-remove-right uk-margin-remove-left uk-padding-remove-horizontal">
                            <p class="lato-md-14-bold uk-align-right uk-margin-remove-right uk-margin-remove-left uk-padding-remove-horizontal" style="color:black">Jumlah Riwayat: {{ numberOfCards }}</p>
                        </div>

                    </div>   

                </div>
            </div>      
            <div style="margin-top:375px" class="uk-offcanvas-bar uk-offcanvas-bar-animation uk-offcanvas-slide uk-width-1-2 offcanvas-bg uk-padding-small">
                            

                <div v-if="daftarMedrec" v-for="(dt, index) in daftarMedrec.data">
                    <!-- <Accordion 
                        v-for="(item, index) in items"
                        :key="index"
                        :title="item.items"
                        :expanded=true
                    > -->
                    <Accordion 
                        :expanded="expandedCards" 
                        :numberOfCards="numberOfCards" 
                        :key="index"
                        :data="dt"
                    >
                    </Accordion>
                        <!-- <slot></slot> -->
                    
                </div>

            </div>
            
        </div>
    </div>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';

    import "../../Dashboard/tabs/dashboardStyle.css";
    import FormTandaVital from '../components/tandaVital.vue';
    import FormSoap from '../components/FormSoap.vue';
    import FormTindakan from '../components/tindakan.vue';
    import FormDiagnosa from '../components/diagnosa.vue';
    import FormResep from '../components/resep.vue';
    import Accordion from '../components/accordion.vue';

    import { far } from '@fortawesome/free-regular-svg-icons';
    import { fas } from '@fortawesome/free-solid-svg-icons';
    import { library } from '@fortawesome/fontawesome-svg-core'
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
    library.add(far, fas);


export default {
    name: "off-canvas",
    components: {
        FormTandaVital,
        FormSoap,
        FormDiagnosa,
        FormTindakan,
        FormResep,
        Accordion,
        FontAwesomeIcon
    },
    props: {
        canvasTitle: String, //isForm non aktif maka gunakan ini
        isCanvasTitle: String, //Jika isForm Aktif maka memakai isCanvasTitle
        // isForm: Boolean, //digunakan utk menampilkan form pada offcanvas, jika tidak digunakan maka menampilkan dropdown-custom pada offcanvas
        // isDropdown: Boolean
    },
    data() {
        return {
            letterOfButtons: ['Tanda Vital', 'SOAP', 'Diagnosa', 'Tindakan', 'Resep'],
            activeLetter: null,
            isLoading : false,
            isUpdate : false,
            regIdSelected : null, 
            daftarMedrec : [],
            regData : {
                reg_id : null,
                tgl_periksa : null,
                tgl_registrasi : null,
                nama_pasien : null,
                no_rm : null,
                pasien_id : null,
                dokter_nama : null,
                dokter_id : null,
                jns_kelamin : null,
                tgl_lahir : null,
                tempat_lahir : null,
                pasien : null,
                soap: null,
                diagnosa : [],
            },

            pasienData : {
                nama_pasien : null,
                no_rm : null,
                pasien_id : null,
                tgl_lahir : null,
                tempat_lahir : null,
            },
            expandedCards: {}, // expanded accordion riwayat medis
            numberOfCards: 0,  //looping card accordion riwayat medis    
            items: ['Tanda Vital', 'SOAP', 'Diagnosa', 'Tindakan', 'Resep'], // item list di dalam accordion riwayat medis
            diagData : {
                reg_id:null,
                soap_id : null,
                reff_trx_id : null,
                soap_diag_id : null,
                tgl_diagnosa : null,
                tipe : null,
                kode_icd : null,
                nama_icd : null,
                diagnosa : null,
                kasus_lama : null,
                pasien_id : null,
                dokter_id : null,
                dokter_nama : null,
            },

            filterData : {
                dokter : '',
                pasien : null,
            }
        };
    },
    mounted() {
        this.initialize();
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['soapData','medrecPasienList','doctorLists']),
    //   ...mapGetters('pencatatankas',['jnsTransaksiKas','metodeBayarKas','filterDataKas']),      
    },
    
    methods: {
        ...mapActions('praktekDokter',['dataMedrec','updateMedrec','listMedrec','listDokter']),
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('praktekDokter',['SET_SOAP_DATA']),

        initialize(){
            this.$refs.offCanvasMedrec.style.display = "none";
            let params = { is_aktif : true, per_page : 'ALL', };
            this.listDokter(params);
        },

            scrollToCard(letter) {
                const targetElement = document.getElementById(letter);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }

                this.activeLetter = letter;

            },

            dateFormatting(data) {
                let d = new Date(data);
                let val = d.toLocaleDateString();
                return val;
            },

            closeCanvas(){
                //UIkit.offcanvas('#offcanvas-medrec-flip').hide();
                this.$refs.offCanvasMedrec.style.display = "none";
            },

            backCanvas(){
                this.$refs.offcanvasFlip2.style.display = "none";
            },

            showCanvas(data) {
                if(data){
                    // console.log(data);
                    this.regIdSelected = data.reg_id;
                    this.retrieveData();
                    this.$refs.formTindakan.refreshData(this.regIdSelected);
                    this.$refs.formResep.refreshData(this.regIdSelected);
                }
            },

            retrieveData() {
                this.dataMedrec(this.regIdSelected).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        this.isUpdate = true;
                        this.fillSoapData(response.data);
                        //UIkit.offcanvas('#offcanvas-medrec-flip').show();
                        this.$refs.offCanvasMedrec.style.display = "block";
                        this.$refs.offcanvasFlip2.style.display = "block";
                        this.$refs.formDiagnosa.initialize();
                    }
                    else {
                        alert(response.message);
                    }
                })
            },

            fillSoapData(data) {
                this.regData.reg_id = data.reg_id;
                this.regData.tgl_periksa = data.tgl_periksa;
                this.regData.tgl_registrasi = data.tgl_registrasi;
                this.regData.tgl_soap = data.tgl_soap;
                this.regData.nama_pasien = data.nama_pasien;
                this.regData.no_rm = data.no_rm;
                this.regData.pasien_id = data.pasien_id;
                this.regData.dokter_nama = data.dokter_nama;
                this.regData.dokter_id = data.dokter_id;
                this.regData.jns_kelamin = data.pasien.jns_kelamin;
                this.regData.tgl_lahir = data.pasien.tgl_lahir;
                this.regData.tempat_lahir = data.pasien.tempat_lahir;
                this.regData.pasien = data.pasien;
            },

            submitSoap(){
                this.updateMedrec(this.soapData).then((response) => {
                    if (response.success == true) {
                        this.isUpdate = true;
                        this.retrieveData();
                    }
                    else {
                        alert(response.message);
                    }
                })

            },

            //untuk return, sesuaiin dgn nama components form yg di import.
            //untuk letter, diambil berdasarkan letterOfButtons yg udeh di declare sebelumnya.
            getFormComponent(letter) {
                if (letter === "Tanda Vital") {
                    return "FormTandaVital";
                } else if (letter === "SOAP") {
                    return "FormSoap";
                } else if (letter === "Diagnosa") {
                    return "FormDiagnosa";
                } else if (letter === "Tindakan") {
                    return "FormTindakan";
                } else if (letter === 'Resep') {
                    return "FormResep";
                }
            },

            getRiwayatPasien(){ 
                this.filterData.pasien = this.regData.pasien_id;
                this.daftarMedrec = [];
                this.listMedrec(this.filterData).then((response) => {
                    if (response.success == true) {
                        this.daftarMedrec = response.data;
                        this.numberOfCards = this.daftarMedrec.data.length;
                    }
                    else {
                        alert(response.message);
                    }
                })
            }
        }
    }
</script>

<style>
.offcanvas-border-bottom {
    border-bottom: dashed 2px rgba(0, 87, 255, 0.47);
    gap:10px;
    height:30px;
}
.offcanvas-content-divider {
    width: 4px;
    height: 19px;
    flex-shrink: 0;
    background: rgba(255, 0, 0, 1);
}
.offcanvas-back-button {
    top: 0;
    right:0;
    cursor: pointer;
    position:absolute;
}

.offcanvas-button-active {
    width: 75px;
    height: 35px;
    flex-shrink: 0;
    border-radius: 5px;
    background: #1E87F0;
    color:white;
    text-align: center;
    align-items: flex-start;
    cursor: default;
    margin: 0em;
    padding-block: 1px;
    padding-inline: 6px;
    border-style: none;
    margin:0 3px;
    /* padding: 12px 18px; */
    font-size:12px;

}
.offcanvas-button {
    /* width: 92px; */
    width: 75px;
    height: 35px;
    flex-shrink: 0;
    border-radius: 5px;
    background: white;
    color:#1E87F0;
    border: 0.8px solid #1E87F0;
    text-align: center;
    align-items: flex-start;
    cursor: default;
    margin: 0em;
    padding-block: 1px;
    padding-inline: 6px;
    margin:0 3px;
    /* padding: 12px 18px; */
    font-size: 12px;
}
.offcanvas-button:hover {
  background: #AFDFFF;
  color: #000000;
}

.uk-offcanvas-close {
    top: 0;
    right:0;
    cursor: pointer;
}
.offcanvas-bg {
    background: white;
}
.offcanvas-title {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-md-18);
    font-weight: var(--semibold);
    letter-spacing: 0.1px;
    line-height: 34px;
}
.offcanvas-content-header {
    width: auto;
    height: 230px;
    flex-shrink: 0;
    background: #E7F9FD;
    border: 1px solid rgba(5, 210, 255, 1);
    border-radius: 3px;
}

.offcanvas-content-body {
    margin-bottom:30px;
    height:300px;
    border:1px solid #BFBDBD;
    border-radius:10px;
}

.offcanvas-content-title {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-md-14);
    font-weight: var(--regular);
    line-height: 20px;
}
.offcanvas-content-subject {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-md-14);
    font-weight: var(--bold);
    line-height: 18px;
}
.offcanvas-content-input {
    border: 1px solid rgba(204, 204, 204, 1);
    border-radius:7px;
    color: black;
    height: 32px;
    vertical-align: middle;
    display: inline-block;
    max-width: 100%;
    width: 120px;
    padding: 0 10px;
    background: #fff;
    transition: all .53s ease-in-out;
}
.offcanvas-content-input:focus {
  outline: none;
  border-color: #1E87F0;
}
.uk-width-1-2 {
    width: calc(100% * 1 / 2);
}
/* .blur-box {
  background-color: rgb(255 255 255 / 0.3);
  backdrop-filter: blur(10px);
  border-radius: 5px;
  text-align: center;
  max-width: 50%;
  max-height: 50%;
  padding: 20px 40px;
  cursor:not-allowed;
} */

</style>