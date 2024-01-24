<template>
    <div style="padding:0.5em 1em 1em 1em;">
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-auto" style="padding:0 0 0.5em 0;">
                <button class="uk-button button-jkn" type="button" @click.prevent="returnMain" style="width:50px;padding:0.5em;border:none;">
                    <span  uk-icon="icon: arrow-left; ratio: 1.4"></span>
                </button>
            </div>
            <div class="uk-width-expand" style="padding:0.3em 0 0.5em 1em;">
                <h2 style="padding:0;margin:0;">JANJI KONSULTASI SPESIALIS</h2>
            </div>
        </div>

        <div style="padding:0 0 1em 0;margin: 0.5em 0 0 0;">
            <ul id="switcherBooking" uk-tab >
                <li class="uk-width-1-2@s uk-width-1-3@m" style="margin:0;padding:0;">
                    <a href="#" class="tab-text-container" @click.prevent="tabBpjsClicked">
                        <p class="tab-text">BPJS / JKN</p>
                        <p class="uk-text-lowercase tab-subtext">Penjamin pelayanan dari BPJS / JKN.</p>
                    </a>
                </li>
                <li class="uk-width-1-2@s uk-width-1-3@m" style="margin:0;padding:0;">
                    <a href="#" class="tab-text-container" @click.prevent="tabNonBpjsClicked">
                        <p class="tab-text">NON BPJS</p>
                        <p class="uk-text-lowercase tab-subtext">Penjamin pelayanan dari asuransi/pribadi.</p>
                    </a>
                </li>
            </ul>
            <ul class="uk-switcher uk-margin">
                <li><booking-bpjs ref="bookingBpjs"></booking-bpjs></li>
                <li><booking-non-bpjs ref="bookingNonBpjs" v-on:closedForm="returnMain"></booking-non-bpjs></li>
            </ul>
        </div>
    </div>
</template>
<script>
import BookingBpjs from '@/Pages/Kiosk/Antrian/BookingPage/BookingBpjs.vue';
import BookingNonBpjs from '@/Pages/Kiosk/Antrian/BookingPage/BookingNonBpjs.vue';

export default {
    name : 'booking-page',
    components : { BookingBpjs,BookingNonBpjs, },
    data() {
        return {
            isLoading : false,
            is_pasien_exist : false,
            keyword : null,            
        }
    },
    mounted() {
    },
    methods : {
        returnMain() {
            this.$refs.bookingNonBpjs.clearData();
            this.$refs.bookingBpjs.clearData();            
            this.$emit('returnMainPage',true);
        },

        tabBpjsClicked() {
            this.$refs.bookingBpjs.clearData();
        },

        tabNonBpjsClicked() {
            this.$refs.bookingNonBpjs.clearData();
        },
    }

}
</script>
<style>
</style>