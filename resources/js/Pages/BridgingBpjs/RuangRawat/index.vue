<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="REFERENSI RUANG DAN KELAS" subTitle="pemetaan ruang dan kelas BPJS"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="padding:1em 0 0 0;">
            <ul class="uk-tab hims-penunjang-tab uk-hidden1" uk-switcher id="switcherBpjsRuang" style="padding:0;margin:0;">
                <li><div><a href="#"><h5>Kelas Rawat</h5></a></div></li>
                <li style="padding:0;"><div><a href="#"><h5>Ruang Rawat</h5></a></div></li> 
            </ul>
            <ul class="uk-switcher" style="">
                <li><kelas-rawat-map></kelas-rawat-map></li>
                <li><ruang-rawat-map></ruang-rawat-map></li>
            </ul>        
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import KelasRawatMap from '@/Pages/BridgingBpjs/RuangRawat/KelasRawatMap.vue';
import RuangRawatMap from '@/Pages/BridgingBpjs/RuangRawat/RuangRawatMap.vue';

export default {
    components : {
        headerPage,
        KelasRawatMap,
        RuangRawatMap,
    },
    computed : {
        ...mapGetters(['errors']),
    },
}
</script>