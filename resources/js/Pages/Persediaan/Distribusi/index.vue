<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">        
        <ul id="switcherDistribusi" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List</a></li>
            <li><a href="#">Form</a></li>
            <li><a href="#">Approve</a></li>
        </ul>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <list-distribusi ref="listDistribusi" 
                    :addFunction="addDistribusi" 
                    :editFunction="updateDistribusi"
                    :approveFunction="approveDistribusi"
                ></list-distribusi>
            </li>
            <li style="padding:0;margin:0;">
                <form-distribusi ref="formDistribusi" v-on:closed="formClosed" v-on:succeed="formClosed"></form-distribusi>
            </li>
            <li style="padding:0;margin:0;">
                <approve-distribusi ref="approveDistribusi" v-on:closed="formClosed" v-on:succeed="formClosed"></approve-distribusi>
            </li>
        </ul>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import ListDistribusi from '@/Pages/Persediaan/Distribusi/components/ListDistribusi.vue';
import FormDistribusi from '@/Pages/Persediaan/Distribusi/components/FormDistribusi.vue';
import ApproveDistribusi from '@/Pages/Persediaan/Distribusi/components/ApproveDistribusi.vue';

export default {
    components : {
        headerPage,
        ListDistribusi,
        FormDistribusi,
        ApproveDistribusi,
        
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapActions('users',['isUserDepoAuthorized']),
        ...mapMutations(['CLR_ERRORS']),

        addDistribusi(){
            this.CLR_ERRORS();
            this.$refs.formDistribusi.newDistribusi(); 
            UIkit.switcher('#switcherDistribusi').show(1);       
        },

        updateDistribusi(data){
            console.log(data);
            this.CLR_ERRORS();
            this.isUserDepoAuthorized(data.depo_penerima_id).then((response)=> {
                if (response.success == true) {
                    if(data.status == 'PERMINTAAN') {
                        this.CLR_ERRORS();
                        this.$refs.formDistribusi.editDistribusi(data); 
                        UIkit.switcher('#switcherDistribusi').show(1);       
                    }
                    else {
                        alert('Data dengan status bukan PERMINTAAN sudah tidak dapat diubah.');
                    }
                }
                else { 
                    alert (response.message); 
                    this.$refs.listDistribusi.refreshTable();
                }
            });
        },

        formClosed(){
            this.$refs.listDistribusi.refreshTable();
            UIkit.switcher('#switcherDistribusi').show(0);
        },

        approveDistribusi(data) {
            console.log(data);
            this.CLR_ERRORS();
            this.isUserDepoAuthorized(data.depo_pengirim_id).then((response)=> {
                if (response.success == true) {
                    this.$refs.approveDistribusi.refreshData(data);
                    UIkit.switcher('#switcherDistribusi').show(2);
                }
                else { 
                    alert (response.message); 
                    this.$refs.listDistribusi.refreshTable();
                }
            });
        }
    }

}
</script>