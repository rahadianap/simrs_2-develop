<template>
    <div class="monitoring-container uk-width-1-3@m" style="gap:45px;margin-bottom:55px;">
             <!-- box card monitoring antrian  -->
             <div class="monitoring-card-bg-1">
                <div style="padding: 1.25rem 1.2rem 0">
                    <!-- title & icon -->
                    <div class="header-card-1 uk-width-1-2@m">
                        <div class="card-header uk-flex-middle uk-flex uk-margin-small">
                            <div>
                                <div class="ellipse-1">
                                    <div class="uk-flex uk-flex-center uk-flex-middle" style="padding:9px">
                                        <font-awesome-icon :icon="['fas', 'user-clock']" style="color: #ffffff;" />                                
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <p class="card-title uk-margin-remove-bottom"> 
                                        {{ judulBoxMonitoring1 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- jumlah Antrian Pasien-->
                    <div style="padding:0.2rem;margin-top:12px">
                        <p class="card-paragraph uk-margin-remove-bottom">
                            {{ jumlahBoxMonitoring1 }}
                        </p>
                    </div>

                    <!-- subJudul Antrian Pasien-->
                    <div style="padding:0.2rem">
                        <p class="card-subtitle-1">
                            {{ subJudulBoxMonitoring1 }}
                        </p>
                    </div>

                    <!-- scrollbar -->
                    <div>

                    </div>
                </div>
            </div>
            <!-- END box card monitoring antrian -->

             <!-- box card monitoring serving -->
             <div class="monitoring-card-bg-2">
                <div style="padding: 1.25rem 1.2rem 0">
                     <!-- title & icon -->
                     <div class="header-card-2 uk-width-1-2@m">
                        <div class="card-header uk-flex-middle uk-flex uk-margin-small">
                            <div class="ellipse-2">
                                <div class="uk-flex uk-flex-center uk-flex-middle" style="padding:9px">
                                    <font-awesome-icon :icon="['fas', 'user-doctor']" style="color: #ffffff;" />                                
                                </div>
                            </div>
                            <div>
                                <div>
                                    <p class="card-title uk-margin-remove-bottom"> 
                                        {{ judulBoxMonitoring2 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- jumlah Pasien Dilayani-->
                    <div style="padding:0.2rem;margin-top:12px">
                        <p class="card-paragraph uk-margin-remove-bottom">
                            {{ jumlahBoxMonitoring2 }}
                        </p>
                    </div>

                    <!-- subJudul Pasien Dilayani-->
                    <div style="padding:0.2rem;">
                        <p class="card-subtitle-2">
                            {{ subJudulBoxMonitoring2 }}
                        </p>
                    </div>

                    <!-- scrollbar -->
                    <div>

                    </div>
                </div>
            </div>
            <!-- END box card monitoring serving -->

            <!-- box card monitoring done -->
            <div class="monitoring-card-bg-3">
                <div style="padding: 1.25rem 1.2rem 0">
                     <!-- title & icon -->
                     <div class="header-card-3 uk-width-1-2@m">
                        <div class="card-header uk-flex-middle uk-flex uk-margin-small">
                            <div class="ellipse-3">
                                    <div class="uk-flex uk-flex-center uk-flex-middle" style="padding:9px">
                                        <font-awesome-icon :icon="['fas', 'user-check']" style="color: #ffffff;" />                                
                                    </div>
                                </div>
                            <div>
                                <div>
                                    <p class="card-title uk-margin-remove-bottom"> 
                                        {{ judulBoxMonitoring3 }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                     <!-- jumlah Pasien Selesai Berobat-->
                     <div style="padding:0.2rem;margin-top:12px">
                        <p class="card-paragraph uk-margin-remove-bottom">
                            {{ jumlahBoxMonitoring3 }}
                        </p>
                    </div>

                    <!-- subJudul Pasien Selesai Berobat-->
                    <div style="padding:0.2rem;">
                        <p class="card-subtitle-3">
                            {{ subJudulBoxMonitoring3 }}
                        </p>
                    </div>

                    <!-- scrollbar -->
                    <div>

                    </div>
                </div>
            </div>
            <!-- END box card monitoring done -->

    </div>
    <div class="uk-card uk-card-default">
        <div class="uk-width-1-1@m uk-flex uk-padding-small">
            <div class="uk-width-1-2">
                <header-page 
                    title="Recent Patients" 
                    subTitle="This is your several latest patient list and transaction monitoring">
                </header-page>
            </div>
            <div class="uk-width-1-2">
                <div class="dropdown-container" @click="toggleDropdown">
                    <img src='@/Assets/circle-dropdown.svg' alt="Dropdown Icon" class="dropdown-icon" />
                    <div uk-dropdown="pos: bottom-right;animation: slide-top; animate-out: true; duration: 700;" style="border-radius:10px">
                        <ul class="uk-nav uk-dropdown-nav">
                            <li>
                                <a href="/praktek/dokter/" style="color:#808080">
                                    <span class="uk-margin-small-right"><font-awesome-icon :icon="['fas', 'address-card']" size="lg" style="color: #808080;"/></span>
                                    Lihat Summary Pasien
                                </a>
                            </li>
                            <li 
                                class="uk-nav-divider"                                            >
                            </li>
                            <li>
                                <a href="/praktek/dokter/layanan/pasien" style="color:#808080">
                                    <span class="uk-margin-small-right"><font-awesome-icon :icon="['fas', 'rectangle-list']" size="lg" style="color: #808080;"/></span>
                                    Master Data Pasien
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-width-1-1@m">  
            <!-- start table recentPatient -->
            <div class="uk-padding-small uk-container uk-container-large" style="padding:1em 2em;">
                <div style="margin-top:1em;">
                    <tab-switcher-table
                        class="uk-margin"
                        style="margin-top:1em;"
                        :tabList="tabList"
                        >
                        <template v-slot:tabPanel-1> 
                            <antrian-terkini/>
                        </template>

                        <template v-slot:tabPanel-2> 
                            <transaksi/>
                        </template>
                    </tab-switcher-table>
                </div>
            </div>
            <!-- END table recentPatient -->
        </div>
    </div>

</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import "../dashboardStyle.css";
    import headerPage from '@/Components/HeaderPage.vue';
    import TabSwitcherTable from '../../components/tabSwitcherTable.vue';
    import AntrianTerkini from './components/antrianTerkini.vue';
    import Transaksi from './components/transaksi.vue';

export default {
    name: "monitoring",
    components: {
        headerPage,
        TabSwitcherTable,
        AntrianTerkini,
        Transaksi,
    },
    props: {
        judulBoxMonitoring1: String,
        subJudulBoxMonitoring1: String,
        jumlahBoxMonitoring1: Number,
        judulBoxMonitoring2: String,
        subJudulBoxMonitoring2: String,
        jumlahBoxMonitoring2: Number,
        judulBoxMonitoring3: String,
        subJudulBoxMonitoring3: String,
        jumlahBoxMonitoring3: Number,
    },
    data() {
        return {
            tabList: ["Antrian Terkini", "Transaksi"],
            antrianList : [],
        }
    },
    computed: {
        ...mapGetters(['errors']),
        // ...mapGetters('praktekDokter',['doctorLists','antrianDashboardLists','filterDataAntrianDashboard']),
    },
    mounted() {
        // this.initialize();
    },
    methods : {
            // ...mapActions('praktekDokter',['listAntrianDashboard']),
            ...mapMutations(['CLR_ERRORS']),

            // initialize(){
            //     this.submitFilter();
            // },

            //* START PAGINATION *//
            // submitFilter() {
                // this.listAntrianDashboard(this.filterDataAntrianDashboard).then((response) => {
                // if (response.success == true) {
                //     console.log(response.data);
                //     this.antrianList = response.data;
                // }
                // this.isLoading = false;
                // });
            // },
        },

}
</script>

<style>
/* STYLE GUIDE:
    1. Chart Card BG
        a. Small - Size Card untuk Chart Ringkasan Pasien, Ringkasan Payment Method, dan Ringkasan Penjamin
        b. Medium - Size Card untuk Chart Ringkasan Pendapatan
        c. Large - Size Card untuk Chart Ringkasan Kas InOut
    2. Box Kunjungan
    3. Chart Income Periode
    4. Chart Line Kas
    5. Chart Pie Ringkasan Pasien
    6. Chart Pie Ringkasan Payment Method
    7. Chart Pie Ringkasan Penjamin
*/

    @media screen and (max-width: 768px) {
        .monitoring-container {
            display: flex;
            flex-wrap: wrap;
        }
    }

  /* ukuran ipad pro */
    @media screen and (max-width: 1024px) {
        .monitoring-card-bg-1{
            flex: 1 0 calc(40% - 31px);
        }
        .monitoring-card-bg-2{
            flex: 1 0 calc(40% - 31px);

        }
        .monitoring-card-bg-3{
            flex: 1 0 calc(40% - 31px);
        }
    }

    .monitoring-container {
        display: flex;
    }

/**  1. Monitoring Card BG **/
.monitoring-card-bg-1 {
    background-color: #FFF8E5;
    border-radius: 8px;
    box-shadow: 0px 12px 26px #101e7333;
    height: 203.104px;
    width: 430.104px;
    left: 5px;
    top: 0;
    flex: 1 0 calc(100% - 31px);
}

.monitoring-card-bg-2 {
    background-color: #E4F8EB;
    border-radius: 8px;
    box-shadow: 0px 12px 26px #101e7333;
    height: 203.104px;
    width: 430.104px;
    left: 5px;
    top: 0;
    flex: 1 0 calc(100% - 31px);
}

.monitoring-card-bg-3 {
    background-color: #EBE8FE;
    border-radius: 8px;
    box-shadow: 0px 12px 26px #101e7333;
    height: 203.104px;
    width: 430.104px;
    left: 5px;
    top: 0;
    flex: 1 0 calc(100% - 31px);
}


.card-pasien {
    width: 223.705px;
    height: 118.331px;
    flex-shrink: 0;
    border-radius: 18.019px;
    background-color: rgba(255, 255, 255, 0.55);;
    /* -webkit-filter: drop-shadow(0px 10px 25px rgba(0, 0, 0, 0.20)); */
    box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.20);
    z-index:1

}

.container-center-horizontal {
    display: flex;
    flex-direction: row;
    justify-content: center;
    pointer-events: none;
    width: 100%;
}
.container-center-horizontal > * {
    flex-shrink: 0;
    pointer-events: auto;
}
.align-text-middle {
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.dropdown-container {
    align-items: flex-end;
    display: flex;
    flex-direction:column;
}
/** END box Kunjungan **/



/** 3. Card Style **/
.header-card-1 {
    border-bottom: 1.5px solid rgba(255, 94, 28, 0.37);
    width: auto;
}
.header-card-2 {
    border-bottom: 1.5px solid rgba(40, 181, 95, 0.37);
    width: auto;
}
.header-card-3 {
    border-bottom: 1.5px solid rgba(107, 86, 251, 0.37);
    width: auto;
}
.card-header {
    /* align-items: flex-start; */
    display: flex;
    gap: 14px;
}
.card-title {
    color:#192434;
    text-transform: uppercase;
    font-family: var(--lato);
    font-size: var(--font-md-14);
    font-weight: var(--bold);
    letter-spacing: 0.14px;
    line-height: 26px;
}
.card-paragraph {
    color:#303946;
    font-family: var(--lato);
    font-size: var(--font-2xl);
    font-weight: var(--bold);
    letter-spacing: 0.14px;
}
.card-subtitle-1 {
    color:#FF8831;
    font-family: var(--lato);
    font-size: var(--font-md-14);
    font-weight: var(--bold);
    letter-spacing: 0.14px;
}
.card-subtitle-2 {
    color:#00BC39;
    font-family: var(--lato);
    font-size: var(--font-md-14);
    font-weight: var(--bold);
    letter-spacing: 0.14px;
}
.card-subtitle-3 {
    color:#9687FF;
    font-family: var(--lato);
    font-size: var(--font-md-14);
    font-weight: var(--bold);
    letter-spacing: 0.14px;
}

.ellipse-1 {
    background: linear-gradient(180deg, #FF3408 -50%, #FFEA5F 182.35%);
    border-radius:100%;
    width: 36.266px;
    height: 36.266px;
    flex-shrink: 0;
}
.ellipse-2 {
    background: linear-gradient(180deg, #048A42 -50%, #50E680 182.35%);
    border-radius:100%;
    width: 36.266px;
    height: 36.266px;
    flex-shrink: 0;
}
.ellipse-3 {
    background: linear-gradient(180deg, #3F24F7 -50%, #8E7FFE 182.35%);
    border-radius:100%;
    width: 36.266px;
    height: 36.266px;
    flex-shrink: 0;
}

.scrollbar-horizontal-bg {
    align-items: flex-start;
    border-radius: 5px;
    background: rgba(217, 217, 217, 0.55);
    display: flex;
    height: 11px;
    justify-content: flex-end;
    left: 104px;
    min-width: 341px;
    padding: 0 0;
    position: absolute;
    top: 330px;
}
.scrollbar-horizontal {
    background-color:CF6065;
    border-radius: 5px;
    height: 11px;
    width: 93px;
}
/** END card style  **/
</style>