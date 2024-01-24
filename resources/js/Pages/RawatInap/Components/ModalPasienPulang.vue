<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalPasienPulang" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close @click.prevent=""></button>
            <div class="hims-form-container" style="padding:0; ">
                <form action="" @submit.prevent="submitPasienPulang">                        
                    <h3 class="uk-text-uppercase" style="font-weight:bold;">Pasien Pulang</h3>
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-1">
                            <h5 style="font-weight: 500;padding:0;margin:0;">{{pasienPulang.no_rm}} - {{pasienPulang.nama_pasien}}</h5>
                            <p style="font-size: 11px;padding:0;margin:0;">{{pasienPulang.trx_id}}</p>
                            
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Unit </dt>
                                <dd>{{pasienPulang.unit_nama}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Ruang </dt>
                                <dd>{{pasienPulang.ruang_nama}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Bed</dt>
                                <dd> {{pasienPulang.no_bed}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-1" style="border-top:1px solid #ccc;margin-top: 1em;margin-bottom: 0.5em;"></div>
                        
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Pulang*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" type="date" v-model="pasienPulang.tgl_pulang" style="color:black;" :min="minDate" required>
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Pulang*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" type="time" v-model="pasienPulang.jam_pulang" style="color:black;" required>
                            </div>
                        </div>
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Cara Pulang*</label>
                            <div class="uk-form-controls">
                                <select v-model="pasienPulang.cara_pulang" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="caraPulangSelected"> 
                                    <option v-for="option in caraPulangLists" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status Pulang*</label>
                            <div class="uk-form-controls">
                                <select v-model="pasienPulang.status_pulang" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="statusPulangSelected"> 
                                    <option v-for="option in statusPulangLists" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" type="text" v-model="pasienPulang.catatan_pulang" style="color:black;" required>
                            </div>
                        </div>

                        
                        <div class="uk-width-1-1" style="text-align:right;margin-top:1.5em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>
                        </div>
                    </div>                    
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'modal-pasien-pulang',
    components : { SearchList, },
    data() {
        return {
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            pasienPulang : {
                trx_id : null,
                reg_id : null,
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                bed_id : null,
                no_bed :null,
                dokter_id : null,
                dokter_nama : null,  

                status : null,
                is_pulang : false,
                cara_pulang : null,
                status_pulang : null,
                is_meninggal : false,
                catatan_pulang : null,
                tgl_pulang : this.getTodayDate(),
                jam_pulang : null,             
            },
            
            caraPulangLists : [],
            statusPulangLists:[],
            kelasHargaLists : [],
            minDate : this.getTodayDate(),
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelas',['classLists']),
        ...mapGetters('referensi',['isRefExist','caraPulangRefs','statusPulangRefs']),    
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapActions('rawatInap',['dataAdmisiInap','pasienInapPulang']),
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('kelas',['listKelas']),
        ...mapMutations(['CLR_ERRORS']),


        /**
         * get date today for default value input date 
         */
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

        modalPasienPulangClosed(){
            this.clearPasienPulang();
            this.$emit('modalPasienPulangClosed',true);
            UIkit.modal('#modalPasienPulang').hide();
        },

        showModal() { 
            UIkit.modal('#modalPasienPulang').show(); 
        },

        initialize(){
            this.CLR_ERRORS();
            if(!this.isRefExist) { 
                this.listReferensi({per_page:'ALL'}).then((response) => {                    
                    if (response.success == true) { this.retrieveReferensi(); }
                })   
            }     

            this.listKelas({per_page:'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.kelasHargaLists = response.data.data.filter(function (el) { return el.is_kelas_harga == true; });
                } 
                else { alert(response.message); }
            })  
        },

        retrieveReferensi(){
            console.log(this.caraPulangRefs);
            this.caraPulangLists = JSON.parse(JSON.stringify(this.caraPulangRefs.json_data));
            this.statusPulangLists = JSON.parse(JSON.stringify(this.statusPulangRefs.json_data));            
        },

        showData(data){
            if(data){
                this.CLR_ERRORS();
                this.clearPasienPulang();
                this.dataAdmisiInap(data.trx_id).then((response) => {
                    if (response.success == true) {
                        this.fillPasienPulang(response.data);
                        this.caraPulangLists = JSON.parse(JSON.stringify(this.caraPulangRefs.json_data));
                        this.statusPulangLists = JSON.parse(JSON.stringify(this.statusPulangRefs.json_data));   
                        this.showModal();
                    }
                    else { alert (response.message) }
                });
            }
        },

        clearPasienPulang(){
            this.pasienPulang.trx_id = null;
            this.pasienPulang.reg_id = null;
            this.pasienPulang.pasien_id = null;
            this.pasienPulang.no_rm = null;
            this.pasienPulang.nama_pasien = null;
            this.pasienPulang.unit_id = null;
            this.pasienPulang.unit_nama = null;
            this.pasienPulang.ruang_id = null;
            this.pasienPulang.ruang_nama = null;
            this.pasienPulang.bed_id = null;
            this.pasienPulang.no_bed =null;
            this.pasienPulang.dokter_id = null;
            this.pasienPulang.dokter_nama = null;
            this.pasienPulang.status = null;
            this.pasienPulang.is_pulang = false;    

            this.pasienPulang.tgl_pulang = this.getTodayDate();
            this.pasienPulang.jam_pulang = null;
            this.pasienPulang.cara_pulang = null;
            this.pasienPulang.status_pulang = null;
            this.pasienPulang.catatan_pulang = null;      
        },

        fillPasienPulang(data){
            this.pasienPulang.trx_id = data.trx_id;
            this.pasienPulang.reg_id = data.reg_id;
            this.pasienPulang.pasien_id = data.pasien_id;            
            this.pasienPulang.no_rm = data.no_rm;

            this.pasienPulang.nama_pasien = data.nama_pasien;
            this.pasienPulang.unit_id = data.unit_id;
            this.pasienPulang.unit_nama = data.unit_nama;
            this.pasienPulang.ruang_id = data.ruang_id;
            this.pasienPulang.ruang_nama = data.ruang_nama;
            this.pasienPulang.bed_id = data.bed_id;
            this.pasienPulang.no_bed =data.no_bed;

            this.pasienPulang.dokter_id = data.dokter_id;
            this.pasienPulang.dokter_nama = data.dokter_nama;

            this.pasienPulang.status = data.status;
            this.pasienPulang.is_pulang = data.is_pulang;  

            this.pasienPulang.tgl_pulang = this.getTodayDate();
            this.pasienPulang.jam_pulang = null;
            this.pasienPulang.cara_pulang = null;
            this.pasienPulang.status_pulang = null;
            this.pasienPulang.catatan_pulang = null;
        },

        submitPasienPulang(){
            if(confirm(`Proses ini akan memulangkan pasien dan mengunci input data. Lanjutkan ?`)){
                this.pasienInapPulang(this.pasienPulang).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.modalPasienPulangClosed();
                    }
                    else { 
                        alert (response.message); 
                    }
                });
            };
        },

        statusPulangSelected(data) {
            //console.log(data);
        },
        caraPulangSelected(data) {
            //console.log(data);
        },

    }
}
</script>