<template>
    <div>
        <div>
            <form action="" @submit.prevent="searchDataBpjs">
                <div class="uk-grid-small" uk-grid style = "padding:1em;">
                    <div class="uk-width-1-4" style="margin:0;padding-right:0;padding-left:0;">
                        <select class="uk-select input-select-jkn" v-model="tipeData" required @change="tipeIdCardChange">
                            <option value="RUJUKAN">NO.RUJUKAN</option>
                            <option value="KARTU">NO.KEPESERTAAN</option>
                        </select>
                    </div>
                    <div class="uk-width-expand" style="margin:0;padding-right:0;padding-left:0;">
                        <input class="uk-input input-text-jkn" type="text" v-model="keyword" :placeholder="[[placeholder]]" required style="color:black;">
                    </div>
                    <div class="uk-width-auto uk-grid-small" uk-grid>
                        <div class="uk-width-auto@s" style="margin:0;padding-right:0; padding-left:0.5em;">
                            <button class="uk-button button-jkn-primer" type="submit">CARI</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
    </div>
</template>

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';

export default {
    name : 'booking-bpjs',
    data() {
        return {
            isLoading : false,
            is_pasien_exist : false,
            keyword : null,
            placeholder : null,
            placeholderPasien : 'masukkan no peserta bpjs',
            minDate : null,
            tipeData : 'RUJUKAN',

            pasien : {
                is_valid : false,
                keyword : null,
                jenis_id : 'RM',
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                salut : null,
                nama_lengkap : null,
                nik : null,
                no_kk : null,
                no_telepon : null,
                jns_kelamin : null,
                tgl_lahir : null,
                tempat_lahir : null,        
                alamat_tinggal : null,
                alamat_ktp : null,
            },
            poli : {
                unit_id : null,
                unit_nama : null,
            },
            jadwalLists : null,
            booking : {
                is_bpjs : null,
                is_walkin : true,
                jenis_identitas : 'KARTU',
                tgl_periksa : null,  
            },

            jadwal : {
                jadwal_id : null,
                unit_id : null,
                unit_nama : null,
                dokter_id : null,
                dokter_nama : null,
                hari : null,
                nama_hari : null,
                mulai : null,
                selesai : null,
                tanggal_registrasi : null,        
                biografi : null,
                url_avatar : null,
                tempat_lahir : null,
                tgl_lahir : null,
                pend_akhir : null,   
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kiosk',['kioskData','poliLists']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('kiosk',['SET_PASIEN','CLR_DATA','SET_JADWAL']),
        ...mapActions('kiosk',['jknAntrianData','pasienData','listUnitPoli','dataRujukanBpjsByNomor','dataRujukanBpjsByKartu']),

        tipeIdCardChange(){
            if(this.tipeData == 'RUJUKAN') { this.placeholder = 'masukkan no rujukan'; }
            else if(this.tipeData == 'KARTU') { this.placeholder = 'masukkan no peserta bpjs'; }
            this.setJadwalLists();
        },

        clearData(){

        },

        searchDataBpjs(){
            if(this.tipeData == 'RUJUKAN') { this.retrieveDataByNomer(); }
            else if(this.tipeData == 'KARTU') { this.retrieveDataByKartu(); }
        },

        retrieveDataByNomer(){
            this.CLR_ERRORS();
            this.dataRujukanBpjsByNomor(this.keyword).then((response)=>{
                if (response.success == true) {
                    console.log(response.data);
                    this.isLoading = false;
                } else {
                    this.isLoading = false;
                    alert(response.message);
                }
            })
        },

        retrieveDataByKartu(){
            this.CLR_ERRORS();
            this.dataRujukanBpjsByKartu(this.keyword).then((response)=>{
                if (response.success == true) {
                    console.log(response.data);
                    this.isLoading = false;
                } else {
                    this.isLoading = false;
                    alert(response.message);
                }
            })
        }
    }
}
</script>