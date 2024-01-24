<template>
    <div class="uk-container uk-container-xlarge" style="padding:0.5em;margin:0;">      
        <div style="padding-left:0.5em;">
            <ul id="switcherLinen" class="uk-tab hims-tab" uk-switcher="connect:#contentCssdSwitcher" style="padding:0;margin:0;border:none;">
                <li><a href="#" @click.prevent="tabClicked(0)">PERSEDIAAN DARAH</a></li>
                <li><a href="#" @click.prevent="tabClicked(1)">PENERIMAAN DARAH</a></li>                
                <li><a href="#" @click.prevent="tabClicked(2)">DISTRIBUSI DARAH</a></li>
                <li><a href="#" @click.prevent="tabClicked(3)">PEMUSNAHAN DARAH</a></li>
            </ul>
        </div>  
        
        <div>
            <ul id="contentCssdSwitcher" class="uk-switcher" style="padding:0.5em;margin:0;">
                <li>
                    <persediaan ref="listPersediaan"></persediaan>
                </li>
                <li style="padding:0;margin:0;">
                    <penerimaan ref="listPenerimaan"></penerimaan>
                </li>
                <li style="padding:0;margin:0;">
                    <pengiriman ref="listPengiriman"></pengiriman>
                </li>
                <li>
                    <pemusnahan ref="listPemusnahan"></pemusnahan>
                </li>
                
            </ul>
        </div>  
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import Pengiriman from '@/Pages/Penunjang/BankDarah/Pengiriman';
import Persediaan from '@/Pages/Penunjang/BankDarah/Persediaan';
import Penerimaan from '@/Pages/Penunjang/BankDarah/Penerimaan';
import BaseTable from '@/Components/BaseTable';
import Pemusnahan from './Pemusnahan/index.vue';

export default {
    components : {
    Pengiriman,
    Penerimaan,
    Persediaan,
    BaseTable,
    Pemusnahan
},
    data(){
        return {
            configTable : { isExpandable : false, filterType : 'SIMPLE', },
            tbColumns : [
                { name : 'gol_darah', title : 'ID Produk', colType : 'text', width:'150px',},
                { name : 'rhesus', title : 'Nama Produk', colType : 'text',},
                { name : 'jumlah', title : 'Jumlah', colType : 'text',  textAlign:'center'},                
            ], 
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        tabClicked(index) {
            if(index == 1) { this.$refs.listPenerimaan.preparedData(); }
            else if(index == 2) { this.$refs.listPengiriman.preparedData(); }
            else if(index == 3) { this.$refs.listPemusnahan.preparedData(); }
        },
    }

}
</script>
<style>

ul.hims-tab::before {
    text-decoration: none;
    border: none;
    padding: 0;
    margin: 0;
}

ul.hims-tab li::before {
    border: none;
    font-weight: 400;
    margin: 0;
    padding: 0;
}

ul.hims-tab li {
    padding:0;margin:0;
}

ul.hims-tab li a {
    font-weight : 400;
    display : block;
    padding : 0.7em 1em 0.7em 1em;
}

ul.hims-tab li.uk-active {
    border:none;
    background-color:#ccc;
    border-radius: 5px;
}

ul.hims-tab li.uk-active a {
    border: none;
    background-color: #fff;
    border-radius: 5px;
    font-weight : 500;
    color: #000;
}

</style>