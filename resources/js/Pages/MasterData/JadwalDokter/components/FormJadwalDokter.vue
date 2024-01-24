<template>
    <div id="formJadwalDokter" class="uk-modal" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container uk-container-medium">
                <form action="" @submit.prevent="submitJadwalDokter" class="uk-width-1-1 uk-grid-small" uk-grid style="padding:0;margin:0;">                    
                    <div class="uk-width-1-1@m" style="padding:0.5em 0.2em 0.5em 0.2em;margin:0;">
                        <h4 style="color:indigo;font-weight:500;">{{dokter.dokter_nama}}</h4>
                    </div>
                    <div class="uk-width-1-1@m" style="padding:0.2em;margin:0;">
                        <label class="uk-form-label" for="lokasi" style="font-size:12px;color:black;">Unit Pelayanan*</label>
                        <div class="uk-form-controls">
                            <select class="uk-select uk-form-small" v-model="jadwal.dokter_unit_id" required @change="unitSelected">
                                <option v-for="(dt,index) in dokter.units" v-bind:value="dt.dokter_unit_id" v-bind:key="index">{{dt.unit_nama}}</option>
                            </select>   
                        </div>
                    </div>
                    <div class="uk-width-1-3@m" style="padding:0.2em;margin:0;">
                        <label class="uk-form-label" for="lokasi" style="font-size:12px;color:black;">Hari Praktek*</label>
                        <div class="uk-form-controls">
                            <select class="uk-select uk-form-small" v-model="jadwal.hari" required  @change="hariSelected">
                                <option  v-if="isRefExist" v-for="(day,index) in namaHariRefs.json_data" v-bind:value="day.value" v-bind:key="index">{{day.value}}-{{day.text}}</option>
                            </select>   
                        </div>
                    </div>
                    <div class="uk-width-1-3@s" style="padding:0.2em;margin:0;">
                        <label class="uk-form-label" for="lokasi" style="font-size:12px;color:black;">Jam Mulai*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" type="time" v-model="jadwal.mulai" placeholder="01:00"  required style="color:black;">
                        </div>
                    </div>
                    <div class="uk-width-1-3@s" style="padding:0.2em;margin:0;">
                        <label class="uk-form-label" for="lokasi" style="font-size:12px;color:black;">Jam Selesai*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" type="time" v-model="jadwal.selesai" placeholder="23:00" required style="color:black;">
                        </div>
                    </div>
                    <div class="uk-width-1-3@s" style="padding:0.2em;margin:0;">
                        <label class="uk-form-label" for="lokasi" style="font-size:12px;color:black;">Pelayanan / pasien (menit)*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" type="number" v-model="jadwal.interval_periksa" placeholder="interval" required style="color:black;">
                        </div>
                    </div>
                    <div class="uk-width-1-3@s" style="padding:0.2em;margin:0;">
                        <label class="uk-form-label" for="lokasi" style="font-size:12px;color:black;">Kuota*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" type="number" v-model="jadwal.kuota" placeholder="kuota" required style="color:black;">
                        </div>
                    </div>
                    <div class="uk-width-1-3@s" style="padding:0.2em;margin:0;">
                        <label class="uk-form-label" for="lokasi" style="font-size:12px;color:black;">Kuota Online*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" type="number" v-model="jadwal.kuota_online" placeholder="kuota daftar online" required style="color:black;">
                        </div>
                    </div>
                    <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.2em;margin:0;">
                        <label style="padding:0;margin:0;font-size:14px;"><input class="uk-checkbox" type="checkbox" v-model="jadwal.is_ext_app" style="margin-left:0;"> Munculkan jadwal di aplikasi external</label>
                    </div> 
                    <div class="uk-width-1-1@m" style="padding:1.5em 0 0 0;margin:0;text-align:right;">
                        <button type="button" @click.prevent="closeModal" class="uk-modal-close uk-button uk-button-default" style="border-radius:0; margin:0.2em;">TUTUP</button>
                        <button type="submit" class="uk-button uk-box-shadow-large"  style="border-radius:0; margin:0.2em;background-color:#cc0202;color:#fff;">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'form-jadwal-dokter', 
    data() {
        return {
            isUpdate : true,
            dokter : {
                dokter_id : null,
                dokter_nama : null,
                units : [],
            },
            jadwal : {
                dokter_id : null,
                dokter_unit_id : null,
                unit_id : null,
                hari : null,
                nama_hari : null,
                mulai : null,
                selesai : null,
                is_ext_app : true,
                interval_periksa : 0,
                kuota : 0,
                kuota_online : 0,
                aktif : true,
            }, 
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',['namaHariRefs','isRefExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('jadwalDokter',['createJadwalDokter']),
        ...mapActions('dokter',['dataDokter']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            if(!this.isRefExist) { 
                this.listReferensi(); 
            };
        },

        unitSelected(){
            let unit = this.dokter.units.filter(item => { return item['dokter_unit_id'] === this.jadwal.dokter_unit_id }); 
            if(unit) { this.jadwal.unit_id = unit[0].unit_id; }
        },

        hariSelected() {
            let hari = this.namaHariRefs.json_data.filter(item => { return item.value === this.jadwal.hari });
            if(hari) { this.jadwal.nama_hari = hari[0].text; }
        },

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formJadwalDokter').hide();
            return false;
        },

        submitJadwalDokter(){
            this.CLR_ERRORS();
            this.createJadwalDokter(this.jadwal).then((response) => {
                if (response.success == true) {
                    alert("Jadwal baru berhasil dibuat.");
                    this.clearJadwalDokter();
                    this.$emit('saveSucceed',true);
                    this.isUpdate = false;
                }
            })
        },

        newJadwalDokter(data){
            this.clearJadwalDokter(data);
            this.isUpdate = false;
            this.dataDokter(data.dokter_id).then((response)=>{
                if (response.success == true) {
                    this.fillDataDokter(response.data);
                    this.clearJadwalDokter();
                    this.isUpdate = false;
                    UIkit.modal('#formJadwalDokter').show();
                } else {
                    alert(response.message);
                }
            })
        }, 

        fillDataDokter(data){
            this.dokter.dokter_id = data.dokter_id;
            this.dokter.dokter_nama = data.dokter_nama;
            this.dokter.units = data.units;
        },
        
        clearJadwalDokter(){
            this.jadwal.dokter_id = this.dokter.dokter_id;
            this.jadwal.jadwal_id = null;
            this.jadwal.unit_id = null;
            this.jadwal.hari = null;
            this.jadwal.nama_hari = null;
            this.jadwal.dokter_unit_id = null;
            this.jadwal.interval_periksa = 0;
            this.jadwal.kuota = 0;
            this.jadwal.kuota_online = 0;
            this.jadwal.mulai = null;
            this.jadwal.selesai = null;
            this.jadwal.is_ext_app = true;
            this.jadwal.aktif = true;            
        },
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox, .uk-select {
    border: 1px solid #999;
}

.hims-form-container label {
    color:#333;
    font-size:12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border:2px solid #cc0202;
}

.uk-button {
    border-radius:50px;
    
    font-weight:400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */

</style>