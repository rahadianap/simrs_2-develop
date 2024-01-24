<template>
    <div class="uk-container uk-container-large" style="padding:0;">
        <!-- <header-page title="OBAT PRB" subTitle="referensi obat PRB"></header-page> -->
        <div style="margin-top:0;padding:1em;" class="uk-card-default" uk-card>
            <div class="uk-width-1-1" style="padding:0 0 0 1em;">
                <form action="" @submit.prevent="retrieveData" class="uk-grid-small" uk-grid>
                    <select class="uk-select uk-form-small uk-width-1-4@m" v-model="params.jnsPelayanan" required style="margin-right:5px;max-width:140px;">
                        <option value="1">Rawat Inap</option>
                        <option value="2">Rawat Jalan</option>
                    </select>
                    <input class="uk-input uk-form-small uk-width-1-4@m" v-model="params.tglKunjungan" type="date" placeholder="tanggal SEP" required style="margin-right:5px;max-width:120px;">
                    <select class="uk-select uk-form-small uk-width-1-4@m" v-model="params.status" required style="margin-right:5px;max-width:180px;">
                        <option value="1">Proses Verifikasi</option>
                        <option value="2">Pending Verifikasi</option>
                        <option value="3">Klaim</option>
                    </select>
                    <button type="submit">Refresh</button>
                </form>
            </div>
            <div style="margin-top:1em;">
                <table class="uk-table uk-table-striped child-table tb-hasil-lab" style="padding:0;margin:0;">
                    <thead>
                        <th class="tb-header-sps" style="width:70px;text-align:left;color:white;  background-color: #cc0202;">TGL SEP</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;  background-color: #cc0202;">TGL PULANG</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">PESERTA</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;  background-color: #cc0202;">INACBG</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;  background-color: #cc0202;">SEP</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">PELAYANAN</th>
                        <th class="tb-header-sps" style="text-align:left;color:white;background-color: #cc0202;">STATUS</th>
                    </thead>
                    <tbody>
                        <tr v-if="isLoading">
                            <th style="text-align: center; background-color: #fff;padding:1em;" colspan="7">
                                <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                                    <span uk-spinner></span>
                                    sedang mengambil data
                                </p>
                            </th>
                        </tr>
                        <tr v-if="dataLists" v-for="sl in dataLists">
                            <td style="font-size:12px;color:black;font-weight: 500;">
                                <p style="margin:0;padding:0;">{{ sl.tglSep }}</p>
                            </td>
                            <td style="font-size:12px;color:black;font-weight: 500;">
                                <p style="margin:0;padding:0;">{{ sl.tglPulang }}</p>
                            </td>
                            <td style="font-size:12px;color:black;font-weight: 500;">
                                <p style="margin:0;padding:0;">{{ sl.peserta.nama }}</p>
                                <p style="margin:0;padding:0;">{{ sl.peserta.noKartu }}</p>
                                <p style="margin:0;padding:0;">{{ sl.peserta.noMR }}</p>
                            </td>
                            <td style="font-size:12px;color:black;font-weight: 500;">
                                <p style="margin:0;padding:0;">{{ sl.Inacbg.kode }}</p>
                                <p style="margin:0;padding:0;">{{ sl.Inacbg.nama }}</p>
                            </td>
                            <td style="font-size:12px;color:black;font-weight: 500;">
                                <p style="margin:0;padding:0;">{{ sl.noSEP }}</p>
                            </td>
                            <td style="font-size:12px;color:black;font-weight: 500;">
                                <p style="margin:0;padding:0;">{{ sl.poli }}</p>
                                <p style="margin:0;padding:0;">{{ sl.kelasRawat }}</p>
                            </td>
                            <td style="font-size:12px;color:black;font-weight: 500;">
                                <p style="margin:0;padding:0;">{{ sl.status }}</p>
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
    name : 'monitoring-klaim',
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
                jnsPelayanan : 1,
                tglKunjungan : null,
                status : 1,
            },
        }
    },
    mounted() {
        this.init();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('bpjsReferensi',['refBpjsMonitoringKlaim']),

        init(){
            this.params.tglKunjungan = this.getTodayDate();
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
            this.refBpjsMonitoringKlaim(this.params).then((response) => {
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