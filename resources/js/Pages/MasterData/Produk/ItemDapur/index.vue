<template>
    <div class="uk-container uk-container-large" style="padding:1em;"> 
        <ul id="switcherItemDapur" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">Item</a></li>
            <li><a href="#">Item</a></li>
        </ul>

        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-item-dapur ref="listItemDapur" :addFunction="addData" :editFunction="editData"></list-item-dapur>
            </li>
            <li style="padding:0;margin:0;">
                <form-item-dapur ref="formItemDapur" :produkId = produkId v-on:close="formClosed" v-on:succeed="formClosed"></form-item-dapur>
            </li>
        </ul>
    </div>
</template>

<script>    
import { mapMutations } from 'vuex';
import FormItemDapur from '@/Pages/MasterData/Produk/ItemDapur/FormItemDapur.vue';
import ListItemDapur from '@/Pages/MasterData/Produk/ItemDapur/ListItemDapur.vue';

export default {
    components : {
        FormItemDapur,
        ListItemDapur,
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
            this.$refs.formItemDapur.newProduk();
            UIkit.switcher('#switcherItemDapur').show(1); 
        },

        editData(data){        
            this.CLR_ERRORS();
            this.produkId = data.produk_id; 
            this.$refs.formItemDapur.editProduk(data.produk_id);
            UIkit.switcher('#switcherItemDapur').show(1);
        },

        formClosed(){
            this.produkId = 'baru'; 
            this.$refs.listItemDapur.refreshTable();
            UIkit.switcher('#switcherItemDapur').show(0);
        }    
    }
}
</script>