<template>
    <div class="uk-container uk-container-xlarge" style="padding:0.5em;margin:0;">      
        <div style="padding-left:0.5em;">
            <ul id="switcherLinen" class="uk-tab hims-tab" uk-switcher="connect:#contentLinenSwitcher" style="padding:0;margin:0;border:none;">
                <li><a href="#" @click.prevent="tabClicked(0)">PERSEDIAAN LINEN</a></li>
                <li><a href="#" @click.prevent="tabClicked(1)">PENERIMAAN LINEN</a></li>                
                <li><a href="#" @click.prevent="tabClicked(2)">PENGIRIMAN LINEN</a></li>
            </ul>
        </div>  
        
        <div>
            <ul id="contentLinenSwitcher" class="uk-switcher" style="padding:0.5em;margin:0;">
                <li>
                    <div>
                        <p style="padding:0.5em 0 0 0.5em;margin:0;font-size:12px;color:#333; font-style: italic;">daftar persediaan linen di penyimpanan.</p>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div style="margin-top:0.5em;" class="uk-responsive">
                        <base-table ref="tablePenerimaan"
                            :columns = "tbColumns" 
                            :config = "configTable"
                            urlSearch="/linens/distributions/product">
                        </base-table>
                    </div>
                </li>
                <li style="padding:0;margin:0;"><penerimaan ref="listPenerimaan"></penerimaan></li>
                <li style="padding:0;margin:0;"><pengiriman ref="listPengiriman"></pengiriman></li>                
            </ul>
        </div>  
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import Pengiriman from '@/Pages/Penunjang/Laundry/Pengiriman';
import Penerimaan from '@/Pages/Penunjang/Laundry/Penerimaan';
import BaseTable from '@/Components/BaseTable';

export default {
    components : {
        Pengiriman, Penerimaan, BaseTable
    },
    data(){
        return {
            configTable : { isExpandable : false, filterType : 'SIMPLE', },
            tbColumns : [
                { name : 'produk_id', title : 'ID Produk', colType : 'text', width:'150px',},
                { name : 'produk_nama', title : 'Nama Produk', colType : 'text',},
                { name : 'jumlah', title : 'Jumlah', colType : 'text',  textAlign:'center'},                
                { name : 'satuan', title : 'Satuan', colType : 'text', textAlign:'center'},
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
        },
    }

}
</script>
<style>
/* #switcherLinen li.uk-active {
    border-bottom:2px solid #cc0202;
}

#switcherLinen li.uk-active h6 {
    font-weight: 500;
} */


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
    background-color: #ccc;
    border-radius: 5px;
    color: #000;
}

</style>