<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER DATA ICD" subTitle="master data ICD prosedur dan diagnosa"></header-page>
        <div style="margin-top:1em;">
            <div class="uk-card uk-card-default1" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                <ul uk-tab style="padding:0;">
                    <li><a href="#" style="font-size:14px;font-weight:500;">DTD Diagnosa</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">ICD10 Diagnosa</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">ICD9 Prosedur</a></li>
                </ul>
                <ul class="uk-switcher" style="padding:0;margin:0;">
                    <li>
                        <dtd-diagnosa></dtd-diagnosa>
                    </li>
                    <li>
                        <icd-diagnosa></icd-diagnosa>
                    </li>
                    <li>
                        <icd-procedure></icd-procedure>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
import headerPage from '@/Components/HeaderPage.vue';
import icdDiagnosa from '@/Pages/MasterData/ICD/IcdDiagnosa.vue';
import icdProcedure from '@/Pages/MasterData/ICD/IcdProcedure.vue';
import dtdDiagnosa from '@/Pages/MasterData/ICD/DtdDiagnosa.vue';

export default {
    components : {
        headerPage,
        icdDiagnosa,
        icdProcedure,
        dtdDiagnosa,
        
}
}
</script>