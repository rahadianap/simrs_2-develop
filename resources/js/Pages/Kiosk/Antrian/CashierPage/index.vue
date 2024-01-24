<template>
    <div style="padding:0.5em 1em 1em 1em;">
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-auto" style="padding:0 0 0.5em 0;">
                <button class="uk-button button-jkn" type="button" @click.prevent="returnMain" style="width:50px;padding:0.5em;border:none;">
                    <span  uk-icon="icon: arrow-left; ratio: 1.4"></span>
                </button>
            </div>
            <div class="uk-width-expand" style="padding:0.3em 0 0.5em 1em;">
                <h2 style="padding:0;margin:0;">ANTRIAN PEMBAYARAN</h2>
            </div>
        </div>
        <div v-if="isLoading" style="text-align:center;padding:1em;">
            <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                <span uk-spinner></span> sedang mengambil data
            </p>
        </div>
        <div style="padding:0 0 1em 0;margin: 0.5em 0 0 0; width:400px;">
            <div class="uk-grid-small" uk-grid style="margin-top:1em;padding-left:1em;"> 
                <div class="uk-width-1-1" style="padding:1em;">
                    <h4 class="modal-subtitle">Nomor Antrian</h4>
                    <h2 class="modal-title">Pembayaran</h2>
                    <p style="margin:0.5em 0 0 0;padding:0;color:black; font-size:14px;">tanggal cetak : {{ tgl_ambil }}</p>
                </div>   
                <div class="uk-width-1-1" style="padding:0.5em 1em 0.5em 1em;">
                    <h1 style="font-size:52px;color:black;font-weight:500;margin:0;padding:0;">{{ no_antrian }}</h1>
                </div>
                <div class="uk-width-1-1" style="padding:1em;">
                    <p style="font-size:11px; font-weight: 400;margin:0;padding:0;font-style:italic;">Mohon menunggu hingga bagian pembayaran memanggil no antrian anda.</p>
                    <p style="font-size:11px; font-weight: 400;margin:0;padding:0;font-style:italic;">Terima Kasih.</p>
                </div>       
            </div>
            <div style="margin-top:1em;padding:0 0 0 0;margin:0;">
                <div style="padding:1em;background-color: whitesmoke;">
                    <p style="font-size:11px; font-weight: 400;margin:0;padding:0;font-style:italic;">untuk mengurangi penggunaan kertas dalam mendukung program pelestarian alam, anda dapat mem-foto bukti pendaftaran diatas dan klik selesai, atau klik CETAK untuk mencetak.</p>
                    <p style="font-size:11px; font-weight: 400;margin:1em 0 1em 0;padding:0.5em;font-style:italic;">Terima Kasih.</p>
                </div>
                <div class="uk-grid-small" uk-grid style="padding:1em 0 0 1em;">
                    <button class="uk-button modal-jadwal-button uk-width-1-2" type="button" style="border:2px solid #999;" @click.prevent="returnMain">SELESAI</button>
                    <button class="uk-button modal-jadwal-button-primary uk-width-1-2" type="button">CETAK</button>
                </div>
            </div>
        </div>       
    </div>
</template>
<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';

export default {
    name : 'cashier-page',
    data() {
        return {
            isLoading : false,  
            no_antrian : '---',
            tgl_ambil : '1970-01-01',
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('kiosk',['noAntrianKasir']),

        returnMain() {
            this.$emit('returnMainPage',true);
        },

        ambilNoAntrian(){
            this.CLR_ERRORS();
            this.no_antrian = '---';
            this.tgl_ambil = null;
            this.isLoading = true;
            this.noAntrianKasir().then((response)=>{
                if (response.success == true) {
                    this.isLoading = false;
                    this.fillData(response.data);                  
                }
                else {
                    this.isLoading = false;
                    alert(response.message);
                }
            })
        },

        fillData(data){
            this.no_antrian = data.no_antrian;
            this.tgl_ambil = data.tgl_ambil;  
        }
    }
}
</script>
