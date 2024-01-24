<template>
    <div class="uk-container uk-container-large" style="padding:1em;">        
        <ul id="switcherIssue" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List</a></li>
            <li><a href="#">Form</a></li>
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-issue ref="listIssue" 
                    :addFunction="addIssue" 
                    :editFunction="updateIssue"
                ></list-issue>
            </li>
            <li style="padding:0;margin:0;">
                <form-issue ref="formIssue" v-on:closed="formClosed" v-on:succeed="formClosed"></form-issue>
            </li>
        </ul>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import ListIssue from '@/Pages/Persediaan/Pengeluaran/components/ListIssue.vue';
import FormIssue from '@/Pages/Persediaan/Pengeluaran/components/FormIssue.vue';

export default {
    components : {
        headerPage,
        ListIssue,
        FormIssue,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        addIssue(){
            this.CLR_ERRORS();
            this.$refs.formIssue.newInventoryIssue(); 
            UIkit.switcher('#switcherIssue').show(1);       
        },

        updateIssue(data){
            if(data.status == 'RENCANA') {
                this.CLR_ERRORS();
                this.$refs.formIssue.editInventoryIssue(data); 
                UIkit.switcher('#switcherIssue').show(1);       
            }
            else {
                alert('Data dengan status bukan RENCANA sudah tidak dapat diubah.');
            }
        },

        formClosed(){
            this.$refs.listIssue.refreshTable();
            UIkit.switcher('#switcherIssue').show(0);
        },
    }

}
</script>