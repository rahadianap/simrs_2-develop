<template>
    <div class="uk-container uk-container-large" style="padding:0;"> 
        <ul id="switcherDepo" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List</a></li>
            <li><a href="#">Form</a></li>
        </ul>

        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-depo ref="listDepo" :depoMapFunction="updateItemDepo" :addFunction="addDepo" :updateFunction="updateDepo"></list-depo>
            </li>
            <li>
                <form-depo-persediaan ref="formDepoPersediaan" v-on:closedFormDepo="showListDepo"></form-depo-persediaan>
            </li>
        </ul>
    </div>
</template>

<script>
    
import { mapGetters, mapMutations, mapActions } from 'vuex';
import ListDepo from '@/Pages/MasterData/Depo/Components/ListDepo.vue';
import FormDepoPersediaan from '@/Pages/MasterData/Depo/Components/FormDepoPersediaan.vue';

export default {
    components : {
        ListDepo,
        FormDepoPersediaan,
    },

    computed : {},
    data() {
        return {
            produkId : 'baru'
        }
    },
    mounted(){
        this.initialize();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        initialize() {
            this.CLR_ERRORS();
        },   
        
        showListDepo(){
            this.$refs.listDepo.refreshTable();
            UIkit.switcher('#switcherDepo').show(0);
        },

        addDepo(){
            this.CLR_ERRORS();
            this.$refs.formDepoPersediaan.newDepo();
            UIkit.switcher('#switcherDepo').show(1); 
        },

        updateDepo(data){
            this.CLR_ERRORS();
            this.$refs.formDepoPersediaan.editDepo(data);
            UIkit.switcher('#switcherDepo').show(1); 
        },
        
        addData(){        
            this.CLR_ERRORS();
            this.$refs.itemDepo.newProduk();
            UIkit.switcher('#switcherDepo').show(1); 
        },

        updateItemDepo(data){     
            this.CLR_ERRORS();
            // this.produkId = data.produk_id; 
            this.$refs.itemDepo.depoData(data.depo_id);
            UIkit.switcher('#switcherDepo').show(1);
        },

        formClosed(){
            this.produkId = 'baru'; 
            this.$refs.listDepo.refreshTable();
            UIkit.switcher('#switcherDepo').show(0);
        }
    }
}
</script>