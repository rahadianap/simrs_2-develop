<template>
    <div class="uk-container uk-container-large" style="padding:1em;">        
        <ul id="switcherAdjustment" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List</a></li>
            <li><a href="#">Form</a></li>
            <!-- <li><a href="#">Approve</a></li> -->
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-adjustment ref="listAdjustment" 
                    :addFunction="addAdjustment" 
                    :editFunction="updateAdjustment"
                    :approveFunction="approveAdjustment"
                ></list-adjustment>
            </li>
            <li style="padding:0;margin:0;">
                <form-adjustment ref="formAdjustment" v-on:closed="formClosed" v-on:succeed="formClosed"></form-adjustment>
            </li>
        </ul>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import ListAdjustment from '@/Pages/Persediaan/Adjustment/components/ListAdjustment.vue';
import FormAdjustment from '@/Pages/Persediaan/Adjustment/components/FormAdjustment.vue';

export default {
    components : {
        headerPage,
        ListAdjustment,
        FormAdjustment,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        addAdjustment(){
            this.CLR_ERRORS();
            this.$refs.formAdjustment.newAdjustment(); 
            UIkit.switcher('#switcherAdjustment').show(1);       
        },

        updateAdjustment(data){
            if(data.status == 'RENCANA') {
                this.CLR_ERRORS();
                this.$refs.formAdjustment.editAdjustment(data); 
                UIkit.switcher('#switcherAdjustment').show(1);       
            }
            else {
                alert('Data dengan status bukan RENCANA sudah tidak dapat diubah.');
            }
        },

        formClosed(){
            this.$refs.listAdjustment.refreshTable();
            UIkit.switcher('#switcherAdjustment').show(0);
        },

        approveAdjustment(data) {
            this.$refs.approveAdjustment.refreshData(data);
            UIkit.switcher('#switcherAdjustment').show(2);
        }
    }
}
</script>