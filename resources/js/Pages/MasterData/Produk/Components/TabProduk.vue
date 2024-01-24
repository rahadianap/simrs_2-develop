<template>
    <div>
        <ul uk-tab class="item-tab" style="padding:0;">
            <li :class=" masterName.name == 'listItemMedis' ? 'uk-active' : ''">
                <router-link :to = "{ name:'listItemMedis' }" style="font-size:14px;display:block;">
                    <h5 style="padding:0;margin:0;font-weight:500;">ITEM MEDIS</h5>
                    <p style="padding:0;margin:0;text-transform: lowercase;font-size:12px;font-weight:200;font-style:italic;">item medis dan alkes</p>
                </router-link> 
            </li>
            <li :class=" masterName.name == 'listItemUmum' ? 'uk-active' : ''">
                <router-link :to = "{ name:'listItemUmum' }" style="font-size:14px;display:block;">
                    <h5 style="padding:0;margin:0;font-weight:500;">ITEM UMUM</h5>
                    <p style="padding:0;margin:0;text-transform: lowercase;font-size:12px;font-weight:200;font-style:italic;">item non medis dan jasa</p>
                </router-link>    
            </li>
            <li :class=" masterName.name == 'listItemDapur' ? 'uk-active' : ''">
                <router-link :to = "{ name:'listItemDapur' }" style="font-size:14px;display:block;">
                    <h5 style="padding:0;margin:0;font-weight:500;">ITEM DAPUR</h5>
                    <p style="padding:0;margin:0;text-transform: lowercase;font-size:12px;font-weight:200;font-style:italic;">bahan dan alat dapur</p>
                </router-link>    
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    name : 'tab-produk',
    data() {
        return {
            masterName : '',
        }
    },
    created() {
        this.masterName = this.$router.currentRoute;
    },
    watch:{
        'masterName': function(newVal, oldVal) {
            this.masterName = newVal;
        },
    },
}
</script>