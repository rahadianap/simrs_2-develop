<template>
    <div style="padding:1em;">
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-auto" style="padding:0 0 0.5em 0;">
                <button class="uk-button button-jkn" type="button" @click.prevent="returnMain" style="width:50px;padding:0.5em;border:none;">
                    <span  uk-icon="icon: arrow-left; ratio: 1.4"></span>
                </button>
            </div>
            <div class="uk-width-expand" style="padding:0.3em 0 0.5em 1em;">
                <h2 style="padding:0;margin:0;">DAFTAR PASIEN BARU</h2>
            </div>
        </div>

        <div style="padding: 1em 0 1em 0;">
            <form-pasien-baru ref="formPasienBaru" v-on:formPasienClosed="returnMain"></form-pasien-baru>
        </div>
    </div>
</template>

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import FormPasienBaru from '@/Pages/Kiosk/Antrian/PasienPage/FormPasienBaru.vue';

export default {
    name : 'pasien-page',
    components : { FormPasienBaru },
    methods : {
        returnMain() {
            this.$emit('returnMainPage',true);
        },
    }
}
</script>