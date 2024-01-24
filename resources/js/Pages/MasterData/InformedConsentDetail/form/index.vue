<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MASTER PERTANYAAN" subTitle="pertanyaan untuk informed consent"></header-page>
        <div style="margin-top:1em;background-color:#fff;">
            <form-template ref="formTemplate"></form-template>
        </div>
    </div>
</template>
<script>
import FormTemplate from '@/Pages/MasterData/InformedConsentDetail/components/FormTemplate.vue';
import headerPage from '@/Components/HeaderPage.vue';
export default {
    props : {
        detailId : { type:String, required:true, default:'baru' },
    },
    components : {
        FormTemplate,headerPage
    },
    mounted(){
        this.initialize();
    },
    methods : {

        initialize() {
            if(this.detailId == '' || this.detailId == 'baru' || this.detailId == null) {
                this.$refs.formTemplate.newTemplate();
            }
            else {
                this.$refs.formTemplate.editTemplate(this.detailId);
            }
        }
    }
}
</script>