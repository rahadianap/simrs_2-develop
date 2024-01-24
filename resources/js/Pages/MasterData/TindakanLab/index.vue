<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <ul id="switcherTindakanLab" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">Item1</a></li>
            <li><a href="#">Item2</a></li>
        </ul>

        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-tindakan-lab ref="listTindakanLab" :addFunction="addTindakan" :editFunction="updateTindakan"></list-tindakan-lab>
            </li>
            <li style="padding:0;margin:0;">
                <form-tindakan-lab ref="formTindakanLab" v-on:closed="formClosed" v-on:succeed="formClosed"></form-tindakan-lab>
            </li>
        </ul>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import FormTindakanLab from '@/Pages/MasterData/TindakanLab/components/FormTindakanLab.vue';
import ListTindakanLab from '@/Pages/MasterData/TindakanLab/components/ListTindakanLab.vue';
export default {
    components : {
        FormTindakanLab,
        ListTindakanLab
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        addTindakan(){        
            this.CLR_ERRORS();
            this.$refs.formTindakanLab.newTindakan();   
            UIkit.switcher('#switcherTindakanLab').show(1);     
        },
        updateTindakan(data){        
            this.CLR_ERRORS();
            this.$refs.formTindakanLab.editTindakan(data.tindakan_id); 
            UIkit.switcher('#switcherTindakanLab').show(1);       
        },
        formClosed(){
            this.$refs.listTindakanLab.refreshTable();
            UIkit.switcher('#switcherTindakanLab').show(0);
        }
    }
}
</script>