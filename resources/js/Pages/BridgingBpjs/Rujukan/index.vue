<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="DATA RUJUKAN" subTitle="pencarian data rujukan"></header-page>
        <div style="margin-top:0;padding:0;" class="uk-card-default" uk-card>            
            <div style="margin-top:1em;">
                <table class="uk-table uk-table-striped child-table tb-hasil-lab" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-sps" style="width:40px;text-align:left;color:white;  background-color: #cc0202;">...</th>        
                        <th class="tb-header-sps" style="text-align:left;color:white;  background-color: #cc0202;">Menu</th>        
                    </thead>
                    <tbody>    
                        <tr>
                            <td colspan="2">PENCARIAN</td>
                        </tr>              
                        <search-no-rujukan></search-no-rujukan>
                        <search-no-kartu></search-no-kartu>
                        <search-list-kartu></search-list-kartu>                        
                    </tbody>
                </table>
            </div>
        </div>        
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import SearchNoRujukan from '@/Pages/BridgingBpjs/Rujukan/components/SearchNoRujukan.vue';
import SearchNoKartu from '@/Pages/BridgingBpjs/Rujukan/components/SearchNoKartu.vue';
import SearchListKartu from '@/Pages/BridgingBpjs/Rujukan/components/SearchListKartu.vue';

export default {
    name : 'pencarian-rujukan',
    components : {
        headerPage,
        SearchNoRujukan,
        SearchNoKartu,
        SearchListKartu,
        
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isLoading : false,
            dataLists : null,
            params : {
                jnsPelayanan : 1,
                tglKunjungan : null,
            },
        }
    },
    mounted() {
        //this.retrieveData();
        this.init();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('bpjsReferensi',['refBpjsMonitoringKunjungan']),

        init(){
            this.params.tglKunjungan = this.getTodayDate();
        },

        retrieveData(){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.refBpjsMonitoringKunjungan(this.params).then((response) => {
                if (response.success == true) {
                    let dt = response.data;
                    this.dataLists = dt.sort(function(a, b){return a.kode - b.kode});
                    this.isLoading = false;
                }
                else { 
                    this.isLoading = false;
                    alert(response.message); 
                }
            })
        },

        /**
         * get date today for default value input date 
         */
         getTodayDate(){
            let dt = new Date();
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate();
            if(date < 10) {date = `0${date}`}
            let str_dt = `${year}-${month}-${date}`
            return str_dt;
        },
    }
}
</script>