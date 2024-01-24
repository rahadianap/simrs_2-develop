<template>
    <div>
        client dashboard        
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {

    computed : {
        ...mapGetters(['errors','clientId']),
    },
    mounted(){
        this.getClientMenus();
    },
    methods : {
        ...mapActions(['clientMenus']),
        ...mapMutations(['CLR_ERRORS']),

        getClientMenus(){
            this.CLR_ERRORS();
            this.clientMenus();
        }
    }
}
</script>