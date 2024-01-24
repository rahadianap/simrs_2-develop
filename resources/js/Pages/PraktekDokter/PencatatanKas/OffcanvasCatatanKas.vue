<template>
    <div class="uk-flex uk-width-1-1@m" style="gap:31px;">

        <div id="offcanvas-flip" class="uk-flex uk-flex-column" uk-offcanvas="flip: true; overlay: true;esc-close:true;bg-close:false;mode: slide">
            <div class="uk-offcanvas-bar offcanvas-bg uk-padding-small uk-width-1-2">
                <div style="background:white;z-index:100;position:sticky;top:0;width:auto;right:0" class="uk-width-1-1" >    
                    <div style="top: 0;">
                        <div style="border-bottom: solid #D4D4D4 1px;width:auto;">
                            <div class="uk-offcanvas-close">
                                <font-awesome-icon :icon="['fas', 'xmark']" size="xl" style="color: #d4d4d4;" />
                            </div>
                            <h3 style="color: black;" class="offcanvas-title uk-margin-remove-bottom"> {{ canvasTitle }}</h3>
                        </div>

                        <div class="offcanvas-content-header uk-margin-small uk-flex" style="color: black;">
                            <div class="uk-width-1-1 uk-padding-small uk-align-left"> 
                                
                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">Tanggal Transaksi : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ detailKas.tgl_transaksi }}</p>
                                </div>
                                    
                            </div>
                            <div class="uk-width-1-1 uk-padding-small uk-align-right"> 
                                
                                <div style="margin-bottom: 20px;">
                                    <p class="offcanvas-content-title uk-margin-remove-bottom">ID Transaksi : </p>
                                    <p class="offcanvas-content-subject uk-margin-remove-top uk-margin-remove-bottom">{{ detailKas.kas_id }}</p>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div style="margin-top:185px" class="uk-offcanvas-bar uk-offcanvas-bar-animation uk-offcanvas-slide uk-width-1-2 offcanvas-bg uk-padding-small">

                <!-- preview condition -->
                <div v-if="isEditing" class="offcanvas-content-subject offcanvas-content-body">
                    <!-- jenis transaksi -->
                    <div class="uk-flex"> 
                        <!-- <div class="uk-padding-small uk-width-2-3 uk-flex-middle uk-flex">  -->
                        <div class="uk-padding-small offcanvas-content-title-width uk-flex-middle uk-flex"> 
                            <p class="offcanvas-content-title uk-margin-remove-bottom">Jenis Transaksi : </p>
                        </div>
                        <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex"> 
                            <!-- <form> -->
                                <div class="uk-inline">
                                    <span class="offcanvas-content-subject">{{ detailKas.jenis_transaksi }}</span>
                                </div>
                            <!-- </form> -->
                        </div>
                        
                        <div style="display: contents;" class="uk-align-right">
                            <font-awesome-icon :icon="['fas', 'pen']" class="uk-padding-small canvas-pen" size="lg" @click="toggle" v-if-else="!isEditing" uk-tooltip="Edit Transaksi"/>
                        </div>
                    </div>

                    <!-- metode pembayaran -->
                    <div class="uk-flex"> 
                        <div class="uk-padding-small offcanvas-content-title-width uk-flex-middle uk-flex"> 
                            <p class="offcanvas-content-title uk-margin-remove-bottom">Metode Pembayaran : </p>
                        </div>
                        <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex"> 
                            <!-- <form> -->
                                <div class="uk-inline">
                                    <span class="offcanvas-content-subject">{{ detailKas.metode_pembayaran }}</span>
                                </div>
                            <!-- </form> -->
                        </div>
                    </div>

                    <!-- nominal -->
                    <div class="uk-flex"> 
                        <div class="uk-padding-small offcanvas-content-title-width uk-flex-middle uk-flex"> 
                            <p class="offcanvas-content-title uk-margin-remove-bottom">Nominal : </p>
                        </div>
                        <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex"> 
                            <!-- <form> -->
                                <div class="uk-inline">
                                    <span class="offcanvas-content-subject">{{ detailKas.nominal }}</span>
                                </div>
                            <!-- </form> -->
                        </div>
                    </div>

                    <!-- deskripsi -->
                    <div class="uk-flex"> 
                        <div class="uk-padding-small offcanvas-content-title-width uk-flex-middle uk-flex"> 
                            <p class="offcanvas-content-title uk-margin-remove-bottom">Deskripsi : </p>
                        </div>
                        <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex"> 
                            <!-- <form> -->
                                <div class="uk-inline">
                                    <span class="offcanvas-content-subject">{{ detailKas.deskripsi }}</span>
                                </div>
                            <!-- </form> -->
                        </div>
                    </div>

                    <!-- dicatat oleh -->
                    <div class="uk-flex"> 
                        <div class="uk-padding-small offcanvas-content-title-width uk-flex-middle uk-flex"> 
                            <p class="offcanvas-content-title uk-margin-remove-bottom">Dicatat oleh : </p>
                        </div>
                        <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex"> 
                            <!-- <form> -->
                                <div class="uk-inline">
                                    <span disabled class="offcanvas-content-subject" style="background:#f5f5f5f1">{{ detailKas.updated_by }}</span>
                                </div>
                            <!-- </form> -->
                        </div>
                    </div>
                    <div class="uk-flex uk-flex-column"> 
                        <div class="uk-padding-small offcanvas-content-title-width"> 
                            <p class="offcanvas-content-title uk-margin-remove-bottom">Bukti Pembayaran : </p>
                        </div>
                        <div class="uk-padding-large uk-padding-remove-vertical uk-width-1-1">
                            <div v-if="lampiran">
                                <img v-for="lamp in lampiran" :data-src="lamp.url_lampiran" alt="" uk-img>
                            </div>
                        </div>
                    </div>
                    
                    <!-- bukti pembayaran -->
                    <!-- <div class="uk-flex uk-flex-column"> 
                        <div class="uk-padding-small offcanvas-content-title-width"> 
                            <p class="offcanvas-content-title uk-margin-remove-bottom">Bukti Pembayaran : </p>
                        </div>
                        <div class="uk-padding-large uk-padding-remove-vertical uk-width-1-1"> 
                            <form>
                                <div class="uk-inline"> -->
                                    <!-- <div uk-form-custom="target: true" class="offcanvas-input-img ">
                                        <input type="file" ref="fileInput" @change="displaySelectedImage">
                                        <input class="uk-form-width-medium" type="image" v-model="selectedFileName" disabled>
                                        <div class="placeholder-icon">
                                            <font-awesome-icon :icon="['fas', 'upload']" />
                                            <span class="placeholder-text">Pilih Lampiran Gambar</span>
                                        </div>
                                    </div> -->
                                    <!-- <div class="offcanvas-input-img">
                                        <img src="../../../Assets/preview-bukti-pembayaran.png" style="width:auto;height:750px" alt="img bukti pembayaran" />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> -->
                </div>
                <!-- END PREVIEW CONDITION -->


                <!-- EDIT condition -->
                <div v-else class="offcanvas-content-subject offcanvas-content-body">
                    <!-- jenis transaksi -->
                    <form action="" @submit.prevent="updateDataKas"> 
                        <div class="uk-flex"> 
                            <div class="uk-padding-small uk-width-2-3 uk-flex-middle uk-flex" style="width:350px"> 
                                <p class="offcanvas-content-title uk-margin-remove-bottom">Jenis Transaksi : </p>
                            </div>
                            <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex"> 
                                <!-- <form> -->
                                <div class="uk-inline">
                                    <!-- <span contenteditable class="offcanvas-input offcanvas-content-subject">{{formData.jenis_transaksi}}</span> -->
                                    <input type="text" disabled class="uk-input offcanvas-input offcanvas-content-subject" v-model="formData.jenis_transaksi" style="border-radius:7px; color:black; border:1px solid #ccc;" placeholder="jenis transaksi">
                                </div>
                                <!-- </form> -->
                            </div>

                            <div style="display: contents;" class="uk-align-right">
                                <font-awesome-icon :icon="['fas', 'pen']" class="uk-padding-small canvas-pen" size="lg" v-if="isEditing"/>
                            </div>
                        </div>

                        <!-- metode pembayaran -->
                        <div class="uk-flex"> 
                            <div class="uk-padding-small offcanvas-content-title-width uk-flex-middle uk-flex"> 
                                <p class="offcanvas-content-title uk-margin-remove-bottom">Metode Pembayaran : </p>
                            </div>
                            <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex"> 
                                <!-- <form> -->
                                <div class="uk-inline">
                                    <!-- <span contenteditable class="offcanvas-input offcanvas-content-subject"> {{formData.metode_pembayaran}}</span> -->
                                    <select class="uk-select offcanvas-input offcanvas-content-subject" v-model="formData.metode_pembayaran"  style="border-radius:7px; color:black; border:1px solid #666;" placeholder="Pilih Metode Pembayaran">
                                        <option v-for="mtd in metodeBayarKas" v-bind:value="mtd.value"  v-bind:key="mtd.value">{{ mtd.text }}</option>
                                    </select>
                                </div>
                                <!-- </form> -->
                            </div>
                        </div>

                        <!-- nominal -->
                        <div class="uk-flex"> 
                            <div class="uk-padding-small offcanvas-content-title-width uk-flex-middle uk-flex"> 
                                <p class="offcanvas-content-title uk-margin-remove-bottom">Nominal : </p>
                            </div>
                            <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex"> 
                                <!-- <form> -->
                                <div v-if="isTerima" class="uk-inline">
                                    <!-- <span contenteditable class="offcanvas-input offcanvas-content-subject">{{formData.jml_terima}}</span> -->
                                    <input type="number" class="offcanvas-input offcanvas-content-subject" v-model="formData.jml_terima"  style="border-radius:7px; color:black; border:1px solid #666;" placeholder="jumlah penerimaan">                                            
                                </div>
                                <div v-else class="uk-inline">
                                    <!-- <span contenteditable class="offcanvas-input offcanvas-content-subject">{{formData.jml_bayar}}</span> -->
                                    <input type="number" class="offcanvas-input offcanvas-content-subject" v-model="formData.jml_bayar"  style="border-radius:7px; color:black; border:1px solid #666;" placeholder="jumlah pengeluaran">
                                </div>
                                <!-- </form> -->
                            </div>
                        </div>

                        <!-- deskripsi -->
                        <div class="uk-flex"> 
                            <div class="uk-padding-small offcanvas-content-title-width uk-flex-middle uk-flex"> 
                                <p class="offcanvas-content-title uk-margin-remove-bottom">Deskripsi : </p>
                            </div>
                            <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex"> 
                                <!-- <form> -->
                                <div class="uk-inline">
                                    <!-- <span contenteditable class="offcanvas-input offcanvas-content-subject"> {{formData.deskripsi}}</span> -->
                                    <textarea type="text" rows="4" class="uk-textarea offcanvas-input offcanvas-content-subject" v-model="formData.deskripsi"  style="border-radius:7px; color:black; border:1px solid #666;" placeholder="jumlah pengeluaran">{{formData.deskripsi}}</textarea>
                                </div>
                                <!-- </form> -->
                            </div>
                        </div>
                        <!-- SIMPAN -->
                        <div class="uk-flex" style="margin-top: 15px;"> 
                            <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex uk-flex-right"> 
                                <div class="uk-flex">
                                    <button class="offcanvas-button" type="button" @click.prevent="updateDataKas" style="width:auto">Simpan Perubahan</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- <div class="uk-flex"> 
                        <div class="uk-padding-small offcanvas-content-title-width uk-flex-middle uk-flex"> 
                            <p class="offcanvas-content-title uk-margin-remove-bottom">Dicatat oleh : </p>
                        </div>
                        <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex"> 
                            <div class="uk-inline">
                                <span disabled class="offcanvas-input offcanvas-content-subject" style="background:#f5f5f5f1">Adminis</span>
                            </div>
                        </div>
                    </div> -->

                    <div class="uk-width-1-1@m">
                        <div class="uk-margin">
                            <div class="uk-width-1-1@m" style="padding-top:0.5em;">
                                <div>
                                    <div v-if="formData.bukti_bayar == null || formData.bukti_bayar == ''" class="uk-width-1-1 uk-hidden"  style="overflow:hidden; background-color:whitesmoke; height:80px; border-radius:10px; border:1px solid #aaa; margin:0; padding:0;">
                                    </div>
                                    <div v-else class="uk-width-1-1 uk-hidden" :style=" {'background-image': `url(${formData.bukti_bayar})` }">
                                        <img id="fileWrapper" :data-src="formData.bukti_bayar" alt="" uk-img @click.prevent="browseFile">
                                    </div>
                                    <button type="button" @click.prevent="browseFile" class="uk-width-1-1@m" style="margin-top:0.5em; border-radius:5px;height:40px;">Tambah lampiran gambar</button>
                                    <div class="uk-width-1-1" style="margin:0;padding:0;">
                                        <div uk-form-custom="target: true">
                                            <input type="file" ref="file" @input="selectedFileKas" accept=".jpg,.jpeg,.png" >
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>
                        <div class="uk-flex uk-flex-between" style="margin-top:-2em">
                            <p class="uk-text-meta uk-margin-small-top">Format yang didukung: JPEG, PNG, GIF</p>
                            <p class="uk-text-meta uk-margin-small-top">Ukuran maksimal: 5 MB</p>
                        </div>
                    </div>
                    <!-- bukti pembayaran -->
                    <div class="uk-flex uk-flex-column"> 
                        <div class="uk-padding-small offcanvas-content-title-width"> 
                            <p class="offcanvas-content-title uk-margin-remove-bottom">Bukti Pembayaran : </p>
                        </div>
                        <div v-if="lampiran">
                            <div v-for="lamp in lampiran" style="border-bottom:1px solid black; margin-top:5px;">
                                <img :data-src="lamp.url_lampiran" alt="" uk-img>
                                <p style="text-align: center;">
                                    <a href="#" @click.prevent="deleteLampiran(lamp)" style="color:blue; font-size: 12px; font-style: italic;">hapus lampiran</a>
                                </p>
                            </div>
                        </div>

                        <div class="uk-padding-large uk-padding-remove-vertical uk-width-1-1"> 
                            <!-- <form>
                                <div class="uk-inline">
                                    <div uk-form-custom="target: true" class="offcanvas-input-img ">
                                        <input type="file" ref="fileInput" @change="displaySelectedImage">
                                        <input class="uk-form-width-medium" type="image" v-model="selectedFileName" disabled>
                                        <div class="placeholder-icon">
                                            <font-awesome-icon :icon="['fas', 'upload']" />
                                            <span class="placeholder-text">Pilih Lampiran Gambar</span>
                                        </div>
                                    </div>
                                </div>
                            </form> -->
                            
                        </div>
                    </div>

                    <!-- SIMPAN -->
                    <!-- <div class="uk-flex" style="margin-top: 15px;"> 
                        <div class="uk-padding-small uk-width-1-1 uk-flex-middle uk-flex uk-flex-right"> 
                            <div class="uk-flex">
                                <button class="offcanvas-button" type="button" style="width:auto" @click="toggle" v-if="!isEditing" >Simpan Perubahan</button>
                            </div>
                        </div>
                    </div> -->

                </div>
                <!-- END EDIT CONDITION -->
            
            </div>
        </div>
    </div>

</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import "../Dashboard/tabs/dashboardStyle.css";
import { fas } from '@fortawesome/free-solid-svg-icons';
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
library.add(fas);


export default {
    name: "detail-transaksi",
    components: {
        FontAwesomeIcon,
        library
    },
    props: {
        canvasTitle: String,
    },
    mounted() {
        //this.chartIncome();
        // contenteditable
        function onContentLoad() {

            function onClick(e) {
                var element = e.target.querySelector('[contenteditable]'), row;

                element && e.target != document.documentElement && e.target != document.body && element.focus();

                if (e.target.matchesSelector('.add')) {
                    document.querySelector('table.printpdf tbody').appendChild(generateTableRow());
                }
                else if (e.target.className == 'cut') {
                    row = e.target.ancestorQuerySelector('tr');

                    row.parentNode.removeChild(row);
                }
            }

            function onEnterCancel(e) {
                e.preventDefault();

                image.classList.add('hover');
            }

            function onLeaveCancel(e) {
                e.preventDefault();

                image.classList.remove('hover');
            }

            if (window.addEventListener) {
                document.addEventListener('click', onClick);

                document.addEventListener('mousewheel', updateNumber);
                document.addEventListener('keydown', updateNumber);

                document.addEventListener('keydown', updateInvoice);
                document.addEventListener('keyup', updateInvoice);

                input.addEventListener('focus', onEnterCancel);
                input.addEventListener('mouseover', onEnterCancel);
                input.addEventListener('dragover', onEnterCancel);
                input.addEventListener('dragenter', onEnterCancel);

                input.addEventListener('blur', onLeaveCancel);
                input.addEventListener('dragleave', onLeaveCancel);
                input.addEventListener('mouseout', onLeaveCancel);

                input.addEventListener('drop', onFileInput);
                input.addEventListener('change', onFileInput);
            }
        }        

    },

    data() {
        return {                
            selectedFile : null,
            selectedFileName: '',
            imageUrl: '', 
            isTerima : false,
            isEditing: true,
            subjectKas: ['Pemasukan', 'Tunai', 'Rp. 15.000.000,00', 'Kas'],
            detailKas : {
                client_id:null,
                kas_id:null,
                tgl_transaksi:null,
                jenis_transaksi : null,
                deskripsi : null,
                metode_pembayaran : null,
                jml_bayar : 0,
                jml_terima : 0,
                nominal : 0,
                bukti_bayar : null,
                created_by : null,
                updated_by : null,
                is_aktif : true, 
            },
            formData : {
                client_id:null,
                kas_id:null,
                tgl_transaksi:null,
                jenis_transaksi : null,
                deskripsi : null,
                metode_pembayaran : null,
                jml_bayar : 0,
                jml_terima : 0,
                nominal : 0,
                bukti_bayar : null,
                created_by : null,
                updated_by : null,
                is_aktif : true,
            },

            lampiran : [],
        };
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('pencatatankas',['jnsTransaksiKas','metodeBayarKas','filterDataKas']),
    },
    
    methods: {
        ...mapMutations(['CLR_ERRORS']),
        //...mapActions('pencatatankas',['dataKas','updateKas']),
        ...mapActions('pencatatankas',['createKas','updateKas','deleteKas','dataKas','uploadBukti','deleteBukti']),
        
        browseFile() {
            this.$refs.file.click();
        },

        selectedFileKas(){
            let input = this.$refs.file;
            let file = input.files;
            
            if(file && file[0]){
                this.selectedFile = file[0];
                console.log(this.selectedFile.name);
                if(this.selectedFile.name.lastIndexOf(".") > 0) {
                    let filename = this.selectedFile.name;
                    let fileExtension = filename.substring(filename.lastIndexOf(".") + 1, filename.length);
                    if(fileExtension.toUpperCase() !== 'JPEG' && fileExtension.toUpperCase() !== 'JPG' && fileExtension.toUpperCase() !== 'PNG' ) {
                        alert('format file hanya boleh JPEG, JPG, dan PNG');
                        this.removeTempFile();
                        return false;
                    }
                    else {
                        this.uploadFileLampiran();
                    }
                }
            }
        },

        uploadFileLampiran(){
            this.CLR_ERRORS();

            if(this.selectedFile.name.lastIndexOf(".") > 0) {
                let filename = this.selectedFile.name;
                let fileExtension = filename.substring(filename.lastIndexOf(".") + 1, filename.length);
                if(fileExtension.toUpperCase() !== 'JPEG' && fileExtension.toUpperCase() !== 'JPG' && fileExtension.toUpperCase() !== 'PNG' ) {
                    return false;
                }
                else {
                    let formData = new FormData();
                    formData.append("image", this.selectedFile);
                    formData.append("kas_id", this.detailKas.kas_id);

                    this.uploadBukti(formData).then((response)=>{
                        if (response.success == true) {
                            //this.formData.bukti_bayar = response.data.path_url;       
                            let dt = JSON.parse(JSON.stringify(this.detailKas));
                            this.showData(dt);             
                        } else {
                            alert(response.message);
                        }
                    })        
                }
            }            
        },


        deleteLampiran(data) {
            console.log(data);
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus bukti lampiran pencatatan kas. Lanjutkan ?`)){
                this.deleteBukti(data.kas_lampiran_id).then((response) => {
                    if (response.success == true) {
                        let dt = JSON.parse(JSON.stringify(this.detailKas));
                        this.showData(dt); 
                    }
                    else { alert (response.message) }
                });
            };
        },
            
        toggle: function () {
            this.isEditing = !this.isEditing;
            // console.log(this.isEditing);
            this.formData = JSON.parse(JSON.stringify(this.detailKas));
        },

        getTodayDate(){
            let dt = new Date();
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate();
            if(date < 10) {date = `0${date}`}
            let str_dt = `${year}-${month}-${date}`
            return str_dt;
        },

        showData(data) {
            if(data) {
                this.clearDataKas();
                this.CLR_ERRORS();
                this.dataKas(data.kas_id).then((response) => {
                    if (response.success == true) {
                        console.log(response.data);
                        this.fillDataKas(response.data);
                        this.lampiran = response.data.lampiran;
                        console.log(this.lampiran);
                    }
                })
            }
        },

        fillDataKas(data) {
            this.detailKas.client_id=data.client_id;
            this.detailKas.kas_id=data.kas_id;
            this.detailKas.tgl_transaksi=data.tgl_transaksi;
            this.detailKas.jenis_transaksi = data.jenis_transaksi;
            this.detailKas.deskripsi = data.deskripsi;
            this.detailKas.metode_pembayaran = data.metode_pembayaran;
            this.detailKas.jml_bayar = data.jml_bayar;
            this.detailKas.jml_terima = data.jml_terima;
            this.detailKas.nominal = data.nominal;
            this.detailKas.bukti_bayar = data.bukti_bayar;
            this.detailKas.is_aktif = data.is_aktif;
            this.detailKas.created_by = data.created_by;
            this.detailKas.updated_by = data.updated_by;
            
            let jenisSelected = this.jnsTransaksiKas.find(item => item.value === this.detailKas.jenis_transaksi);
            if(jenisSelected) {
                this.isTerima = !jenisSelected.isExpense;
            }

            this.formData = JSON.parse(JSON.stringify(this.detailKas));
        },

        clearDataKas(){
            this.detailKas.client_id = null;
            this.detailKas.kas_id = null;
            this.detailKas.is_aktif = null;
            this.detailKas.tgl_transaksi = this.getTodayDate();
            this.detailKas.jenis_transaksi = this.jnsTransaksiKas[0].value;
            this.detailKas.metode_pembayaran = this.metodeBayarKas[0].value;
            this.detailKas.jml_bayar = 0;
            this.detailKas.jml_terima = 0;
            this.detailKas.nominal = 0;
            this.detailKas.bukti_bayar = null;
            this.detailKas.deskripsi = null;
            this.detailKas.created_by = null;
            this.detailKas.updated_by = null;
            this.isUpdate = false;
            this.isTerima = !this.jnsTransaksiKas[0].isExpense;
            this.formData = JSON.parse(JSON.stringify(this.detailKas));
        },

        displaySelectedImage() {
            const fileInput = this.$refs.fileInput;
            const selectedFile = fileInput.files[0];
            const imgPlaceholder = this.$el.querySelector('.offcanvas-input-img');

            if (selectedFile) {
                this.selectedFileName = selectedFile.name; // Mengatur nama berkas
                this.imageUrl = URL.createObjectURL(selectedFile); // Mengambil URL gambar

                // Menampilkan gambar sebagai latar belakang dan mengatur properti CSS
                imgPlaceholder.style.backgroundImage = `url('${this.imageUrl}')`;
                imgPlaceholder.style.backgroundRepeat = 'no-repeat'; // Tidak mengulang gambar
                imgPlaceholder.style.backgroundSize = 'cover'; // Mengatur ukuran gambar
            } else {
                this.selectedFileName = ''; // Menghapus nama berkas
                this.imageUrl = ''; // Menghapus URL gambar
                imgPlaceholder.style.backgroundImage = 'none'; // Menghapus latar belakang
            }
        },

        scrollToCard(letter) {
             // Buat ID yang sesuai dengan card yang ingin di-scroll
            const targetID = 'card-' + letter;

            // Cari elemen card yang sesuai
            const targetElement = document.getElementById(targetID);

            // Gulir ke elemen card
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }

            this.activeLetter = letter;

        },

        updateDataKas() {
            this.CLR_ERRORS();
            this.updateKas(this.formData).then((response) => {
                if (response.success == true) {
                    this.fillDataKas(response.data);
                    this.formData = JSON.parse(JSON.stringify(this.detailKas));
                    this.$emit('saved',true);
                    alert('Data berhasil diubah');
                }
            })
        }
    },
}
</script>

<style>
.canvas-pen {
    color: rgba(217, 217, 217, 1);
    cursor: pointer;
}
.canvas-pen:hover {
    color: #000000;
}
.placeholder-icon {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

.placeholder-text {
  margin-left: 5px;
}
.offcanvas-input {
    border: 1px solid rgba(217, 217, 217, 1);
    border-radius:3px;
    padding:8px 10px;
    width: 285px;
}
.offcanvas-input-img {
    border: 1px solid rgba(217, 217, 217, 1);
    border-radius:3px;
    padding:8px 10px;
    width: 485px;
    height: 780px;
    background: rgba(111, 111, 111, 0.35);
}
.offcanvas-button-active {
    /* width: 112px; */
    width:92px;
    height: 45px;
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
    margin:0 6px;
    /* padding: 12px 18px; */
}
.offcanvas-button {
    /* width: 112px; */
    width:92px;
    height: 45px;
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
    margin:0 6px;
    /* padding: 12px 18px; */
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
    font-size: var(--font-lg-26);
    font-weight: var(--semibold);
    letter-spacing: 0.1px;
    line-height: 34px;
}
.offcanvas-content-header {
    width: auto;
    height: auto;
    flex-shrink: 0;
    background: #F5F5F5;
    /* border: 1px solid rgb(0, 0, 0); */
    border-radius: 3px;
}

.offcanvas-content-body {
    margin-bottom:30px;
    height:auto;
    padding: 25px 5px;
    background: #FFFFFF;
    border:1px solid #BFBDBD;
    border-radius:10px;
}

.offcanvas-content-title {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-md-14);
    font-weight: var(--medium);
    line-height: 20px;
}
.offcanvas-content-title-width {
    width:320px;
}
.offcanvas-content-subject {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-md-14);
    font-weight: var(--bold);
    line-height: 24px;
    letter-spacing: 0.1px;
}
.uk-width-1-2 {
    width: calc(100% * 1 / 2.589);
}

/* content editable */

*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

*[contenteditable] { cursor: pointer; }

*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 10em 0.5em #DEF; }

span[contenteditable] { display: inline-block; }

</style>