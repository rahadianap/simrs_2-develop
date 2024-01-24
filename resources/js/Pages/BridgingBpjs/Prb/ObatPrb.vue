<template>
    <div class="uk-container uk-container" style="padding:0;">
        <!-- <header-page title="OBAT PRB" subTitle="referensi obat PRB"></header-page> -->
        <div style="margin-top:0;padding:1em;" class="uk-card-default" uk-card>
            <div class="uk-width-1-1">
                <form action="" @submit.prevent="retrieveData">
                    <input v-model="keyword" type="text" placeholder="kata pencarian" required style="margin-right:5px;">
                    <button type="submit">Refresh</button>
                </form>
            </div>
            <div style="margin-top:1em;">
                <table class="uk-table uk-table-striped child-table tb-hasil-lab" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-sps" style="width:40px;text-align:left;color:white;  background-color: #cc0202;">Kode</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">Nama</th>
                    </thead>
                    <tbody>
                        <tr v-if="isLoading">
                            <th style="text-align: center; background-color: #fff;padding:1em;" colspan="2">
                                <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                                    <span uk-spinner></span>
                                    sedang mengambil data
                                </p>
                            </th>
                        </tr>
                        <tr v-if="dataLists" v-for="sl in dataLists">
                            <td style="width:40px;font-size:12px;color:black;font-weight: 500;">{{ sl.kode }}</td>
                            <td style="font-size:12px;color:black;">{{ sl.nama }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>        
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
export default {
    name : 'obat-prb',
    components : {
        headerPage,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isLoading : false,
            dataLists : null,
            keyword : '',
        }
    },
    mounted() {
        //this.retrieveData();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('bpjsReferensi',['refBpjsDiagnosaPrb','refBpjsObatPrb']),

        retrieveData(){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.refBpjsObatPrb(this.keyword).then((response) => {
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
        }
    }
}
</script>