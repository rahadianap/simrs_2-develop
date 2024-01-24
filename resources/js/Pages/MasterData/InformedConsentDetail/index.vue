<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER PERTANYAAN" subTitle="pertanyaan untuk informed consent"></header-page>
        <div style="margin-top:1em;">
            <div class="uk-card uk-card-default1" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                <informed-detail></informed-detail>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import informedDetail from '@/Pages/MasterData/InformedConsentDetail/InformedConsentDetail.vue';

export default {
    components : {
        headerPage,
        informedDetail,        
}
}
</script>