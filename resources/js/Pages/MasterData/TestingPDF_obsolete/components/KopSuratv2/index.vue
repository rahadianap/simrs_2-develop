<template>
    <kop
    :kopSurat="kopSurat"
    >
    </kop>
</template>
<script>
import kop from "./kop.vue";
import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    name: 'kop-surat',
    components: {
        kop
    },
    props : {
        kopSurat : { type:Boolean, require:false}, 
    },
    computed: {

    },
    methods: {
 
    },
}


</script>