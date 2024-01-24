<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <!-- <ul id="switcherOpname" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List</a></li>
            <li><a href="#">Form</a></li>
            <li><a href="#">Data</a></li>            
        </ul>

        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-opname ref="listOpname" :editFunction="updateOpname" :showFunction="dataOpname"></list-opname>
            </li>
            <li style="padding:0;margin:0;">
                <form-opname ref="formOpname" v-on:closed="formClosed" v-on:succeed="formClosed"></form-opname>
            </li>
            <li>
                <preview-opname ref="previewOpname" v-on:closed="formClosed"></preview-opname>
            </li>
        </ul> -->
        <router-view></router-view>
    </div>
</template>
<script>
// import { mapGetters, mapActions, mapMutations } from 'vuex';
// import FormOpname from '@/Pages/Persediaan/Opname/components/FormOpname.vue';
// import ListOpname from '@/Pages/Persediaan/Opname/components/ListOpname.vue';
// import PreviewOpname from '@/Pages/Persediaan/Opname/components/PreviewOpname.vue';

export default {
    components : {
        // FormOpname,
        // ListOpname,
        // PreviewOpname
    },
    computed : {
        // ...mapGetters(['errors']),
    },
    methods : {
        // ...mapMutations(['CLR_ERRORS']),

        // updateOpname(data){
        //     this.CLR_ERRORS();
        //     this.$refs.formOpname.editDataOpname(data.so_id); 
        //     UIkit.switcher('#switcherOpname').show(1);       
        // },

        // saveSucceed() {

        // },

        // formClosed(){
        //     this.$refs.listOpname.refreshTable();
        //     UIkit.switcher('#switcherOpname').show(0);
        // },

        // dataOpname(data){
        //     this.$refs.previewOpname.refreshData(data);
        //     UIkit.switcher('#switcherOpname').show(2);
        // }
    }
}
</script>