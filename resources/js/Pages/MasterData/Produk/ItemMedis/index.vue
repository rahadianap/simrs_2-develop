<template>
    <div class="uk-container uk-container-large" style="padding:1em;"> 
        <ul id="switcherItemMedis" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">Item</a></li>
            <li><a href="#">Item</a></li>
        </ul>

        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-item-medis ref="listItemMedis" :addFunction="addData" :editFunction="editData"></list-item-medis>
            </li>
            <li style="padding:0;margin:0;">
                <form-item-medis ref="formItemMedis" :produkId = produkId v-on:close="formClosed" v-on:succeed="formClosed"></form-item-medis>
            </li>
        </ul>
    </div>
</template>

<script>
    
import { mapMutations } from 'vuex';
import FormItemMedis from '@/Pages/MasterData/Produk/ItemMedis/FormItemMedis.vue';
import ListItemMedis from '@/Pages/MasterData/Produk/ItemMedis/ListItemMedis.vue';

export default {
    components : {
        FormItemMedis,
        ListItemMedis,
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
            this.$refs.formItemMedis.newProduk();
            UIkit.switcher('#switcherItemMedis').show(1); 
        },

        editData(data){        
            this.CLR_ERRORS();
            this.produkId = data.produk_id; 
            this.$refs.formItemMedis.editProduk(data.produk_id);
            UIkit.switcher('#switcherItemMedis').show(1);
        },

        formClosed(){
            this.produkId = 'baru'; 
            this.$refs.listItemMedis.refreshTable();
            UIkit.switcher('#switcherItemMedis').show(0);
        }
    }
}
</script>