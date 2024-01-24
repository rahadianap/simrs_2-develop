<template>
    <div id="formJadwalEdit" class="uk-modal" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container uk-container-medium">
                <form action="" @submit.prevent="submitJadwal" class="uk-width-1-1 uk-grid-small" uk-grid style="padding:0;margin:0;">                    
                    <div class="uk-width-1-1@m" style="padding:0.5em 0.2em 0.5em 0.2em;margin:0;">
                        <h4 style="color:indigo;font-weight:500;">{{jadwal.dokter_nama}}</h4>
                    </div>
                    <div class="uk-width-1-1@m" style="padding:0.2em;margin:0;">
                        <label class="uk-form-label" for="lokasi" style="font-size:12px;color:black;">Unit Pelayanan*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" disabled type="text" v-model="jadwal.unit_nama" style="color:black;">
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
                        <label style="padding:0;margin:0;font-size:14px;"><input class="uk-checkbox" type="checkbox" v-model="jadwal.is_ext_app" style="margin-left:0px;"> Munculkan jadwal di aplikasi external</label>
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
    name : 'form-jadwal-edit', 
    data() {
        return {
            isUpdate : true,
            kelompok : {
                client_id:null,
                kelompok_makanan_id:null,
                kelompok_makanan : null,
                catatan:null,
                is_aktif : true,
            },
            jadwal : {
                dokter_id : null,
                dokter_nama : null,
                dokter_unit_id : null,
                unit_id : null,
                unit_nama : null,
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
        ...mapActions('kelompokMakanan',['createKelompokMakanan','dataKelompokMakanan','updateKelompokMakanan']),
        ...mapActions('jadwalDokter',['dataJadwalDokter','createJadwalDokter','updateJadwalDokter']),
        ...mapActions('dokter',['dataDokter']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            if(!this.isRefExist) { 
                this.listReferensi(); 
            };
        },

        hariSelected() {
            let hari = this.namaHariRefs.json_data.filter(item => { return item.value === this.jadwal.hari });
            if(hari) { this.jadwal.nama_hari = hari[0].text; }
        },

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formJadwalEdit').hide();
            return false;
        },

        submitJadwal(){
            this.CLR_ERRORS();
            this.updateJadwalDokter(this.jadwal).then((response) => {
                if (response.success == true) {
                    alert("Jadwal dokter berhasil diubah.");
                    this.clearJadwal();
                    this.$emit('saveSucceed',true);
                    this.isUpdate = false;
                    this.closeModal();
                }
            })  
        },

        editJadwal(id){
            this.clearJadwal();            
            this.dataJadwalDokter(id).then((response)=>{
                if (response.success == true) {
                    this.fillJadwal(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formJadwalEdit').show();
                } else {
                    alert(response.message);
                }
            })
        }, 
        
        clearJadwal(){
            this.jadwal.dokter_id = null;
            this.jadwal.dokter_nama = null;
            this.jadwal.jadwal_id = null;
            this.jadwal.unit_id = null;
            this.jadwal.unit_nama = null;
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

        fillJadwal(data){
            this.jadwal.dokter_id = data.dokter_id;
            this.jadwal.dokter_nama = data.dokter_nama;
            this.jadwal.jadwal_id = data.jadwal_id;
            this.jadwal.unit_id = data.unit_id;
            this.jadwal.unit_nama = data.unit_nama;
            this.jadwal.hari = data.hari;
            this.jadwal.nama_hari = data.nama_hari;
            this.jadwal.dokter_unit_id = data.dokter_unit_id;
            this.jadwal.interval_periksa = data.interval_periksa;
            this.jadwal.kuota = data.kuota;
            this.jadwal.kuota_online = data.kuota_online;
            this.jadwal.mulai = data.mulai;
            this.jadwal.selesai = data.selesai;
            this.jadwal.is_ext_app = data.is_ext_app;
            this.jadwal.aktif = data.is_aktif;
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