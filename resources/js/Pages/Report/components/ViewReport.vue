<template>
    <div class="uk-modal-full" uk-modal="bg-close:false;" :id="modalId" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
            <div class="hims-form-container" style="padding:0; margin:0; height:calc(100vh - 100px);">
                <div v-html="dataView"></div>            
            </div>
        </div>
    </div> 
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'view-report',
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            modalId : 'viewReport',
            dataView : '<html></html>',
        }
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        showReport(data) {
            this.dataView = data;
            UIkit.modal(`#${this.modalId}`).show();
        }
    }

}
</script>