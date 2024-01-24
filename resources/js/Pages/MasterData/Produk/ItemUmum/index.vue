<template>
    <div class="uk-container uk-container-large" style="padding:1em;"> 
        <ul id="switcherItemUmum" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">Item1</a></li>
            <li><a href="#">Item2</a></li>
        </ul>

        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-item-umum ref="listItemUmum" :addFunction="addData" :editFunction="editData"></list-item-umum>
            </li>
            <li style="padding:0;margin:0;">
                <form-item-umum ref="formItemUmum" :produkId = produkId v-on:close="formClosed" v-on:succeed="formClosed"></form-item-umum>
            </li>
        </ul>
    </div>
</template>

<script>
    
import { mapGetters, mapMutations, mapActions } from 'vuex';
import FormItemUmum from '@/Pages/MasterData/Produk/ItemUmum/FormItemUmum.vue';
import ListItemUmum from '@/Pages/MasterData/Produk/ItemUmum/ListItemUmum.vue';

export default {
    components : {
        FormItemUmum,
        ListItemUmum,
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
        addData(){        
            this.CLR_ERRORS();
            this.$refs.formItemUmum.newProduk();
            UIkit.switcher('#switcherItemUmum').show(1); 
        },

        editData(data){        
            this.CLR_ERRORS();
            this.produkId = data.produk_id; 
            this.$refs.formItemUmum.editProduk(data.produk_id);
            UIkit.switcher('#switcherItemUmum').show(1);
        },

        formClosed(){
            this.produkId = 'baru'; 
            this.$refs.listItemUmum.refreshTable();
            UIkit.switcher('#switcherItemUmum').show(0);
        }
    }
}
</script>