<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MONITORING" subTitle="monitoring pelayanan dan klaim"></header-page>
        <!-- <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div> -->
        <div style="padding:1em 0 0 0;">
            <ul class="uk-tab hims-penunjang-tab uk-hidden1" uk-switcher id="switcherBpjsPrb" style="padding:0;margin:0;">
                <li><div><a href="#"><h5>Kunjungan</h5></a></div></li>
                <li style="padding:0;"><div><a href="#"><h5>Klaim</h5></a></div></li> 
                <li style="padding:0;"><div><a href="#"><h5>Pelayanan</h5></a></div></li>
                <li style="padding:0;"><div><a href="#"><h5>Klaim Jasa Raharja</h5></a></div></li> 
            </ul>
            <ul class="uk-switcher" style="">
                <li><monitoring-kunjungan></monitoring-kunjungan></li>
                <li><monitoring-klaim></monitoring-klaim></li>
                <li><monitoring-pelayanan></monitoring-pelayanan></li>
                <li><monitoring-jasa-raharja></monitoring-jasa-raharja></li>
            </ul>        
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import MonitoringKunjungan from '@/Pages/BridgingBpjs/Monitoring/MonitoringKunjungan.vue';
import MonitoringKlaim from '@/Pages/BridgingBpjs/Monitoring/MonitoringKlaim.vue';
import MonitoringPelayanan from '@/Pages/BridgingBpjs/Monitoring/MonitoringPelayanan.vue';
import MonitoringJasaRaharja from '@/Pages/BridgingBpjs/Monitoring/MonitoringJasaRaharja.vue';

export default {
    components : {
        headerPage,
        MonitoringKunjungan,
        MonitoringKlaim,
        MonitoringPelayanan,
        MonitoringJasaRaharja,
    },
    computed : {
        ...mapGetters(['errors']),
    },
}
</script>