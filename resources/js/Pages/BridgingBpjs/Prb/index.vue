<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="REFERENSI RUJUK BALIK" subTitle="referensi pembuatan rujuk balik"></header-page>
        <!-- <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div> -->
        <div style="padding:1em 0 0 0;">
            <ul class="uk-tab hims-penunjang-tab uk-hidden1" uk-switcher id="switcherBpjsPrb" style="padding:0;margin:0;">
                <li><div><a href="#"><h5>Diagnosa PRB</h5></a></div></li>
                <li style="padding:0;"><div><a href="#"><h5>Obat PRB</h5></a></div></li> 
            </ul>
            <ul class="uk-switcher" style="">
                <li><diagnosa-prb></diagnosa-prb></li>
                <li><obat-prb></obat-prb></li>
            </ul>        
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import DiagnosaPrb from '@/Pages/BridgingBpjs/Prb/DiagnosaPrb.vue';
import ObatPrb from '@/Pages/BridgingBpjs/Prb/ObatPrb.vue';

export default {
    components : {
        headerPage,
        DiagnosaPrb,
        ObatPrb,
    },
    computed : {
        ...mapGetters(['errors']),
    },
}
</script>