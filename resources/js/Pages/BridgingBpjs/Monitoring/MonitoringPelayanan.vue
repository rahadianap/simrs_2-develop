<template>
    <div class="uk-container uk-container-large" style="padding:0;">
        <!-- <header-page title="OBAT PRB" subTitle="referensi obat PRB"></header-page> -->
        <div style="margin-top:0;padding:1em;" class="uk-card-default" uk-card>
            <div class="uk-width-1-1" style="padding:0 0 0 1em;">
                <form action="" @submit.prevent="retrieveData" class="uk-grid-small" uk-grid>
                    <input class="uk-input uk-form-small uk-width-1-4@m" v-model="params.noka" type="text" placeholder="no peserta" required style="margin-right:5px;max-width:180px;">
                    <input class="uk-input uk-form-small uk-width-1-4@m" v-model="params.tglMulai" type="date" placeholder="tanggal mulai" required style="margin-right:5px;max-width:120px;">
                    <input class="uk-input uk-form-small uk-width-1-4@m" v-model="params.tglAkhir" type="date" placeholder="tanggal akhir" required style="margin-right:5px;max-width:120px;">
                    <button type="submit">Refresh</button>
                </form>
            </div>
            <div style="margin-top:1em;">
                <table class="uk-table uk-table-striped child-table tb-hasil-lab" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-sps" style="width:70px;text-align:left;color:white;  background-color: #cc0202;">Tgl. SEP</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">Tgl.PULANG</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">PASIEN</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">SEP / RUJUKAN</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">PELAYANAN</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">POLI</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">DIAGNOSA</th>   
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">PPK</th>   
                    </thead>
                    <tbody>
                        <tr v-if="isLoading">
                            <th style="text-align: center; background-color: #fff;padding:1em;" colspan="8">
                                <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                                    <span uk-spinner></span>
                                    sedang mengambil data
                                </p>
                            </th>
                        </tr>
                        <tr v-if="dataLists" v-for="sl in dataLists">
                            <td style="width:40px;font-size:12px;color:black;font-weight: 500;">
                                <p style="margin:0;padding:0;">{{ sl.tglSep }}</p>
                            </td>
                            <td style="font-size:12px;color:black;">
                                <p style="margin:0;padding:0;">{{ sl.tglPlgSep }}</p>
                            </td>
                            <td style="font-size:12px;color:black;">
                                <p style="margin:0;padding:0;">{{ sl.namaPeserta }}</p>
                                <p style="margin:0;padding:0;">{{ sl.noKartu }}</p>
                            </td>
                            <td style="font-size:12px;color:black;">
                                <p style="margin:0;padding:0;">{{ sl.noSep }}</p>
                                <p style="margin:0;padding:0;">{{ sl.noRujukan }}</p>
                            </td>
                            <td style="font-size:12px;color:black;">
                                <p style="margin:0;padding:0;">{{ sl.jnsPelayanan }}</p>
                                <p style="margin:0;padding:0;">Kls. {{ sl.kelasRawat }}</p>
                            </td>
                            <td style="font-size:12px;color:black;">
                                <p style="margin:0;padding:0;">{{ sl.poli }}</p>
                            </td>
                            <td style="font-size:12px;color:black;">
                                <p style="margin:0;padding:0;">{{ sl.diagnosa }}</p>
                            </td>
                            <td style="font-size:12px;color:black;">
                                <p style="margin:0;padding:0;">{{ sl.ppkPelayanan }}</p>
                            </td>
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
    name : 'monitoring-pelayanan',
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
            params : {
                noka : null,
                tglMulai : null,
                tglAkhir : null,
            },
        }
    },
    mounted() {
        this.init();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('bpjsReferensi',['refBpjsMonitoringPelayanan']),

        init(){
            this.params.tglMulai = this.getTodayDate();
            this.params.tglAkhir = this.getTodayDate();
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

        retrieveData(){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.refBpjsMonitoringPelayanan(this.params).then((response) => {
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