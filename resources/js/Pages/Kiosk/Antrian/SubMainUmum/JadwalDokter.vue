<template>
    <div style="padding:1em;">
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-auto" style="padding:0 0 1em 0;">
                <button class="uk-button button-jkn" type="button" @click.prevent="returnMainPage" style="width:60px;padding:0.5em;border:none;">
                    <span  uk-icon="icon: arrow-left; ratio: 2"></span>
                </button>
            </div>
            <div class="uk-width-expand" style="padding:0 0 1em 1em;">
                <h2 style="padding:0;margin:0;">PILIH POLI DAN DOKTER</h2>
                <h5 style="padding:0;margin:0;">pilih terlebih dahulu jadwal dokter</h5>
            </div>
        </div>
        <div style="padding: 1em 0 0 1em;">
            <form action="" @submit.prevent="searchKodeBooking">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-2" style="margin:0;padding-right:0;padding-left:0;">
                        <select v-model="poliId" class="uk-select input-select-jkn" @change="poliChange">
                            <option v-for="poli in poliLists" v-bind:value="poli.unit_id" v-bind:key="poli.unit_id">{{poli.unit_nama}}</option>
                        </select>
                    </div>
                    <div class="uk-width-auto uk-grid-small" uk-grid>
                        <div class="uk-width-auto@s" style="margin:0;padding-right:0; padding-left:0.5em;">
                            <button class="uk-button button-jkn-primer" type="submit">FILTER</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div style="padding:1em 0 0 0;">
            <div class="uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2@s uk-grid-small" uk-grid style="padding:1em 0 0 0;" uk-height-match="target: > div > .card-button">
                <div v-for="jd in jadwalLists" class="card-button-container">
                    <dokter-button :data="jd" :fnCallback="dokterClicked"></dokter-button>
                </div>
            </div>
        </div>
        <!-- <modal-dokter ref="modalDokter" :fnSelect="dokterSelected"></modal-dokter> -->
    </div>
</template>
<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import DokterButton from '@/Pages/Kiosk/Antrian/SubMainUmum/DokterButton.vue'
import ModalDokter from '@/Pages/Kiosk/Antrian/SubMainUmum/ModalDokter.vue'

export default {
    name : 'jadwal-dokter',
    components : {
        DokterButton, ModalDokter
    },
    data() {
        return {
            isLoading : false,
            poliId : null,
            poliNama : null,
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

            jadwalLists : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kiosk',['kioskData','poliLists']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('kiosk',['SET_PASIEN','CLR_DATA','SET_JADWAL']),
        ...mapActions('kiosk',['jknAntrianData','pasienData','listUnitPoli']),
        
        // retrievePoli(){
        //     this.CLR_ERRORS();
        //     this.listUnitPoli().then((response)=>{
        //         if (response.success == true) {
        //             this.poliLists = response.data;
        //             this.isLoading = false;
        //         } else {
        //             this.isLoading = false;
        //             alert(response.message);
        //         }
        //     })

        // },
        poliChange(){
            let selPoli = this.poliLists.find(item => item.unit_id == this.poliId); 
            if(selPoli) {
                this.poliNama = selPoli.unit_nama;
                this.jadwalLists = selPoli.jadwal;
            }
        },

        dokterClicked(data) {
            this.jadwal.jadwal_id = data.jadwal_id;
            this.jadwal.unit_id = this.poliId;
            this.jadwal.unit_nama = this.poliNama;
            this.jadwal.dokter_id = data.dokter_id;
            this.jadwal.dokter_nama = data.dokter_nama;
            this.jadwal.hari = data.hari;
            this.jadwal.nama_hari = data.nama_hari;
            this.jadwal.mulai = data.mulai;
            this.jadwal.selesai = data.selesai;
            this.jadwal.biografi = data.biografi;
            this.jadwal.url_avatar = data.url_avatar;
            this.jadwal.tempat_lahir = data.tempat_lahir;
            this.jadwal.tgl_lahir = data.tgl_lahir;
            this.jadwal.pend_akhir = data.pend_akhir;

            this.$refs.modalDokter.showModal(this.jadwal);
            
            // this.SET_JADWAL(this.jadwal);
        },

        dokterSelected(data) {
            this.SET_JADWAL(data);
            this.$refs.modalDokter.closeModal();
        },

        returnMainPage(){
            this.$emit('returnMainPage',true);
        },
        
    }
}
</script>