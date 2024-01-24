<template>
    <div class="uk-container hims-form-container" style="min-height:50vh;">
        <div class="uk-container" style="background-color:#fff;padding:1em;margin-top:1em;">
            <form action="" @submit.prevent="submitMonitoring">
                <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                    <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                        <h5 class="uk-text-uppercase">DATA DIET PASIEN</h5>
                    </div>                                
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;border:none;">Tutup</button>                  
                    </div>
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="submit" class="uk-button hims-button-primary1 uk-button-default uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                    </div>
                </div>
                <div> 
                    <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                        <div uk-spinner="ratio : 2"></div>
                        <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                    </div>                         
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>              
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                informasi utama pasien.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Transaksi ID</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.trx_id" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-2-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.nama_pasien" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Umur</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.usia" type="text" disabled style="color:black;">
                                </div>
                            </div>                                
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.unit_nama" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.kelas_harga" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.dokter_nama" type="text" disabled style="color:black;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA DETAIL MENU DIET</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                informasi menu diet pasien.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diet ID</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.diet_id" type="text" style="color:black;" disabled>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Kontrol</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="date" v-model="pasien.tgl_kontrol" required style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Meal Set</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.meal_set" class="uk-select uk-form-small">
                                        <option value="Pagi">Pagi</option>
                                        <option value="Siang">Siang</option>
                                        <option value="Sore">Sore</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;"></h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;"></p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Karbohidrat</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.karbo" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-if="isRefPenunjangExist" v-for="dt in monitoringGiziRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Sayuran</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.sayur" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-if="isRefPenunjangExist" v-for="dt in monitoringGiziRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lauk</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.lauk" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-if="isRefPenunjangExist" v-for="dt in monitoringGiziRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Buah</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.buah" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-if="isRefPenunjangExist" v-for="dt in monitoringGiziRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Minuman</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.minuman" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-if="isRefPenunjangExist" v-for="dt in monitoringGiziRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;"></h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;"></p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alasan</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.alasan" class="uk-select uk-form-small">
                                        <option value="mual">Mual</option>
                                        <option value="sesak">Sesak</option>
                                        <option value="bosan">bosan</option>
                                        <option value="-">Tanpa Keterangan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea uk-form-small" v-model="pasien.catatan" type="text"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-monitoring-diet', 
    components : {
        SearchSelect, SearchList
    },
    data() {
        return {
            tags: [],
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            dietDesc : [
                { colName : 'nama_diet', labelData : '', isTitle : true },
                { colName : 'diet_id', labelData : '', isTitle : false },
            ],

            menuDesc : [
                { colName : 'nama_menu', labelData : '', isTitle : true },
                { colName : 'menu_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            dietUrl : '/diet',
            urlSearch : '',

            pasien : {
                trx_id : null,
                pasien_id : null,
                nama_pasien : null,
                tempat_lahir : null,
                tgl_lahir : null,
                usia : null,
                unit_id : false,
                unit_nama : null,
                kelas_harga_id : null,
                kelas_harga : null,
                dokter_id : null,
                dokter_nama : null,
                tgl_kontrol : new Date().toISOString().slice(0,10),
                jam_berlaku : this.getTimeNow(),
                karbo : null,
                sayur : null,
                lauk : null,
                buah : null,
                minuman : null,
                alasan : null,
                is_aktif: true,
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refPenunjang',['isRefPenunjangExist', 'monitoringGiziRefs']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('dietPasien',['createDietMonitoring', 'dataDietPasien']),
        ...mapActions('refPenunjang',['listRefPenunjang']),
        ...mapMutations(['CLR_ERRORS']),

        getTimeNow(){
            let currentDate = new Date();
            let hours = currentDate.getHours();
            if(hours < 10){ hours = `0${hours}` };
            let minutes = currentDate.getMinutes();
            if(minutes < 10){ minutes = `0${minutes}` };
            
            let time = hours + ":" + minutes;
            return time;
        },

        initialize() {
            let params = { is_aktif:true, per_page:'ALL' };
            this.listRefPenunjang(params);
        },

        closeModal(){
            this.clearPengguna();
            this.$emit('closed',true);
        },

        submitMonitoring(){            
            this.CLR_ERRORS();
            this.createDietMonitoring(this.pasien).then((response) => {
                if (response.success == true) {
                    alert("Data monitoring diet baru berhasil dibuat.");
                    this.fillDiet(response.data);
                    this.$emit('saveSucceed',true);
                    this.isUpdate = true;
                }
            })         
        },

        newMonitoring(){
            this.clearPengguna();
            UIkit.modal('#formMonitoringDiet').show();
        },

        editPengguna(data){
            this.clearPengguna();
            this.isLoading = true;
            this.dataDietPasien(data.pasien_diet_id).then((response)=>{
                if (response.success == true) {
                    this.fillDiet(response.data);
                    this.isUpdate = true;
                } 
                else { alert(response.message); }
                this.isLoading = false;
            })
        },   

        clearPengguna(){
            this.pasien.trx_id = null;
            this.pasien.pasien_id = null;
            this.pasien.nama_pasien = null;
            this.pasien.tempat_lahir = null;
            this.pasien.tgl_lahir = null;
            this.pasien.usia = null;
            this.pasien.unit_id = false;
            this.pasien.unit_nama = null;
            this.pasien.kelas_harga_id = null;
            this.pasien.kelas_harga = null;
            this.pasien.dokter_id = null;
            this.pasien.dokter_nama = null;
            this.pasien.is_aktif = null;    
        },

        fillDiet(data){
            this.pasien.trx_id = data.trx_id;
            this.pasien.diet_id = data.pasien_diet_id;
            this.pasien.pasien_id = data.pasien_id;
            this.pasien.nama_pasien = data.nama_pasien;
            this.pasien.tempat_lahir = data.tempat_lahir;
            this.pasien.tgl_lahir = data.tgl_lahir;
            this.pasien.usia = data.usia;
            this.pasien.unit_id = data.unit_id;
            this.pasien.unit_nama = data.unit_nama;
            this.pasien.kelas_harga_id = data.kelas_harga_id;
            this.pasien.kelas_harga = data.kelas_harga;
            this.pasien.dokter_id = data.dokter_id;
            this.pasien.dokter_nama = data.dokter_nama;
        },

        menuSelected(data) {
            if(data){
                this.addedDiet.menu_id = data.menu_id;
                this.addedDiet.nama_menu = data.nama_menu;
            }
        },
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400&display=swap');
  
  .container{
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    width:100%;
    height:100vh;
    background: linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(102,126,234,1) 50%, rgba(69,252,250,1) 100%);
  }
  
  a {
  position: absolute;
  right: 15px;
  bottom: 15px;
  font-weight: bold;
  text-decoration: none;
  color: #00003a;
  font-size: 20px;
}
  
  
/*tag input style*/
  
  .tag-input {
    width: 50%;
    border: 1px solid #D9DFE7;
    background: #fff;
    border-radius: 4px;
    font-size: 0.9em;
    min-height: 45px;
    box-sizing: border-box;
    padding: 0 10px;
    font-family: "Roboto";
    margin-bottom: 10px;
  }

  .tag-input__tag {
    height: 24px;
    color: white;
    float: left;
    font-size: 14px;
    margin-right: 10px;
    background-color: #667EEA;
    border-radius: 15px;
    margin-top: 10px;
    line-height: 24px;
    padding: 0 8px;
    font-family: "Roboto";
  }

  .tag-input__tag > span {
    cursor: pointer;
    opacity: 0.75;
    display: inline-block;
    margin-left: 8px;
  }

  .tag-input__text {
    border: none;
    outline: none;
    font-size: 1em;
  line-height: 40px;
  background: none;
  }

</style>