<template>
    <div class="uk-container" style="margin-top:15px">
        <form action="" @submit.prevent="submitData">
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-4@m uk-width-1-3@s">
                    <label style="padding:0;margin:0;font-size:12px;">Sistole (mmHg)</label>
                    <div>
                        <input type="number" class="offcanvas-content-input" placeholder="0 MmHg" v-model="soapData.sistole" @change="submitData">
                    </div>
                </div>
                <div class="uk-width-1-4@m uk-width-1-3@s">
                    <label style="padding:0;margin:0;font-size:12px;">Diastole (mmHg)</label>
                    <div>
                        <input type="number" class="offcanvas-content-input" style="color:black;border-radius:7px;" placeholder="0 MmHg" v-model="soapData.diastole" @change="submitData">
                    </div>
                </div>
                <div class="uk-width-1-4@m uk-width-1-3@s">
                    <label style="padding:0;margin:0;font-size:12px;">Suhu Tubuh (Celcius)</label>
                    <div>
                        <input type="number" class="offcanvas-content-input" placeholder="0 C" v-model="soapData.suhu" @change="submitData">
                    </div>
                </div>
                <div class="uk-width-1-4@m uk-width-1-3@s">
                    <label style="padding:0;margin:0;font-size:12px;">Denyut Nadi (bpm)</label>
                    <div>
                        <input type="number" class="offcanvas-content-input" placeholder="0 x/menit" v-model="soapData.denyut_nadi" @change="submitData">
                    </div>
                </div>   
                <div class="uk-width-1-4@m uk-width-1-3@s">
                    <label style="padding:0;margin:0;font-size:12px;">Pernapasan (bpm)</label>
                    <div>
                        <input type="number" class="offcanvas-content-input" placeholder="0 x/menit" v-model="soapData.pernapasan" @change="submitData">
                    </div>
                </div>
                <div class="uk-width-1-4@m uk-width-1-3@s">
                    <label style="padding:0;margin:0;font-size:12px;">Berat Badan (Kg)</label>
                    <div>
                        <input type="number" class="offcanvas-content-input" style="color:black;border-radius:7px;" placeholder="0 Kg" v-model="soapData.berat_badan" @change="submitData">
                    </div>
                </div>
                <div class="uk-width-1-4@m uk-width-1-3@s">
                    <label style="padding:0;margin:0;font-size:12px;">Tinggi Badan (cm)</label>
                    <div>
                        <input type="number" class="offcanvas-content-input" placeholder="0 cm" v-model="soapData.tinggi_badan" @change="submitData">
                    </div>
                </div>   
                <div class="uk-width-1-1">
                    <label style="padding:0;margin:0;font-size:12px;">Catatan</label>
                    <div>
                        <textarea class="offcanvas-content-input" style="width:590px;height:55px;" v-model="soapData.note_assesmen" @change="submitData" ></textarea>
                    </div>
                </div>
            </div>

            <!-- <div class="uk-width-1-1@m" style="text-align:right;margin-top:1em;">
                <button class="uk-button uk-button-primary" type="submit" style="border-radius: 7px;border:none; font-size: 14px; text-transform: none;background: rgba(30, 135, 240, 1);color:white">Simpan</button>                                        
            </div> -->
        </form>
    </div>

</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import "../../Dashboard/tabs/dashboardStyle.css";

export default {
    name : 'tanda-vital',
    components : {
    },
    data(){
        return {
            // soapData : {
            //     soap_id : null,
            //     reg_id : null,
            //     reff_trx_id : null,
            //     tgl_soap :null,
            //     unit_id : '',
            //     unit_nama : '',
            //     dokter_id : null,
            //     dokter_nama : null,
            //     pasien_id : null,
            //     nama_pasien : null,
            //     no_rm : null,
            //     saturasi_oksigen : null,
            //     sistole : null,
            //     diastole : null,
            //     suhu : null,
            //     denyut_nadi : null,
            //     pernapasan : null,
            //     tinggi_badan : null,
            //     berat_badan : null,
            //     subjective : null,
            //     objective : null,
            //     intervention : null,
            //     evaluation : null,
            //     assesment : null,
            //     plan : null,
            //     catatan : null,
            //     status : null,
            //     is_aktif : true,               
            // },
        }
    },
    computed : {        
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['soapData']),
    },

    mounted() {
        this.initialize();
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.isLoading = false;            
        },

        setData(data) {
            // this.soapData.soap_id = null;
            // this.soapData.reg_id = null;
            // this.soapData.reff_trx_id = null;
            // this.soapData.tgl_soap =null;
            // this.soapData.unit_id = '';
            // this.soapData.unit_nama = '';
            // this.soapData.dokter_id = null;
            // this.soapData.dokter_nama = null;
            // this.soapData.pasien_id = null;
            // this.soapData.nama_pasien = null;
            // this.soapData.no_rm = null;
            // this.soapData.saturasi_oksigen = null;
            // this.soapData.sistole = null;
            // this.soapData.diastole = null;
            // this.soapData.suhu = null;
            // this.soapData.denyut_nadi = null;
            // this.soapData.pernapasan = null;
            // this.soapData.subjective = null;
            // this.soapData.objective = null;
            // this.soapData.assesment = null;
            // this.soapData.plan = null;
            // this.soapData.catatan = null;
            // this.soapData.status = null;
            // this.soapData.is_aktif = true;
        } ,
        
        submitData(){
            this.$emit('dataChange',true);
        }

        
    }
}
</script>
<style>
</style>