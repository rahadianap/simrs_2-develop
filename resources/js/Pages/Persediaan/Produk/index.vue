<template>
    <div class="uk-container uk-container-large" style="padding:1em;"> 
        <ul id="switcherProduk" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">Item</a></li>
            <li><a href="#">Item</a></li>
        </ul>

        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-produk ref="listProduk" :addFunction="addData" :editFunction="editData"></list-produk>
            </li>
            <li style="padding:0;margin:0;">
                <form-produk ref="formProduk" :produkId = produkId v-on:close="formClosed" v-on:succeed="formClosed"></form-produk>
            </li>
        </ul>
    </div>
</template>

<script>
    
import { mapMutations } from 'vuex';
import FormProduk from '@/Pages/Persediaan/Produk/components/FormProduk.vue';
import ListProduk from '@/Pages/Persediaan/Produk/components/ListProduk.vue';

export default {
    components : {
        FormProduk,
        ListProduk,
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
            this.$refs.formProduk.newProduk();
            UIkit.switcher('#switcherProduk').show(1); 
        },

        editData(data){        
            this.CLR_ERRORS();
            this.produkId = data.produk_id; 
            this.$refs.formProduk.editProduk(data.produk_id);
            UIkit.switcher('#switcherProduk').show(1);
        },

        formClosed(){
            this.produkId = 'baru'; 
            this.$refs.listProduk.refreshTable();
            UIkit.switcher('#switcherProduk').show(0);
        }
    }
}
</script>