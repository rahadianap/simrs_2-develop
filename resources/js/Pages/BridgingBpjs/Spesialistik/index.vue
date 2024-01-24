<template>
    <div class="uk-container uk-container" style="padding:1em;">
        <header-page title="KODE SPESIALISTIK" subTitle="referensi data spesialistik BPJS"></header-page>
        <div style="margin-top:1em;padding:1em;" class="uk-card-default" uk-card>
            <div class="uk-width-1-1">
                <button @click.prevent="retrieveData">Refresh</button>
            </div>
            <div style="margin-top:1em;">
                <table class="uk-table uk-table-striped child-table tb-hasil-lab" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-sps" style="width:40px;text-align:left;color:white;  background-color: #cc0202;">Kode</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">Nama</th>
                    </thead>
                    <tbody>
                        <tr v-if="spesLists" v-for="sl in spesLists">
                            <td style="width:40px;font-size:12px;color:black;">{{ sl.kode }}</td>
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
    components : {
        headerPage,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            spesLists : null,
        }
    },
    mounted() {
        this.retrieveData();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('bpjsReferensi',['refBpjsSpesialistik']),

        retrieveData(){
            this.refBpjsSpesialistik().then((response) => {
                if (response.success == true) {
                    let dt = response.data;
                    this.spesLists = dt.sort(function(a, b){return a.kode - b.kode});
                }
                else { 
                    alert(response.message); 
                }
            })
        }
    }
}
</script>