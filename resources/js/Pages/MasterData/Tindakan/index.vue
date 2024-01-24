<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <ul id="switcherTindakan" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">Item1</a></li>
            <li><a href="#">Item2</a></li>
        </ul>

        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-tindakan ref="listTindakan" :addFunction="addTindakan" :editFunction="updateTindakan"></list-tindakan>
            </li>
            <li style="padding:0;margin:0;">
                <form-tindakan ref="formTindakan" v-on:closed="formClosed" v-on:succeed="formClosed"></form-tindakan>
            </li>
        </ul>
        <modal-new-tindakan
            ref="modalNewTindakan" 
            v-on:addTindakanSuccess="updateTindakan">
        </modal-new-tindakan>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import FormTindakan from '@/Pages/MasterData/Tindakan/components/FormTindakan.vue';
import ListTindakan from '@/Pages/MasterData/Tindakan/components/ListTindakan.vue';
import ModalNewTindakan from '@/Pages/MasterData/Tindakan/components/ModalNewTindakan.vue';

export default {
    components : {
        FormTindakan,
        ListTindakan,
        ModalNewTindakan
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        addTindakan(){        
            this.CLR_ERRORS();
            this.$refs.modalNewTindakan.showModal();
            //this.$refs.formTindakan.newTindakan();   
            //UIkit.switcher('#switcherTindakan').show(1);     
        },
        updateTindakan(data){        
            this.CLR_ERRORS();
            this.$refs.formTindakan.editTindakan(data.tindakan_id); 
            UIkit.switcher('#switcherTindakan').show(1);       
        },
        formClosed(){
            this.$refs.listTindakan.refreshTable();
            UIkit.switcher('#switcherTindakan').show(0);
        }
    }
}
</script>