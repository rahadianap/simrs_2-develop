<template>
    <!-- <div class="uk-flex uk-width-2-3@m" style="gap:31px;margin-bottom:55px"> -->
    <!-- <div class="uk-grid" style="gap:31px;margin-bottom:55px"> -->
    <div class="card-container" style="margin-bottom: 55px;gap:31px">
            <!-- box card total kunjungan pasien -->
            <div class="layer-card-kunjungan">
                <div class="card-kunjungan">
                    <div class="uk-flex">
                        <div class="uk-width-auto">
                            <div class="div-total-kunjungan uk-child-width-1-2@m">
                                <p class="title-kunjungan">{{ judulKunjungan }}</p>
                                <p class="number-kunjungan">{{ jumlahKunjungan }}</p>
                            </div>

                            <div class="uk-width-1-1 uk-width-1-2@m span-jenis-pasien">
                                <div class="card-pasien uk-width-1-2@m">
                                    <p class="title-jenis-pasien">{{ judulPasienBaru }}</p>
                                    <p class="number-jenis-pasien">{{ jumlahPasienBaru }}</p>
                                </div>

                                <div class="card-pasien uk-width-1-2@m">
                                    <p  class="title-jenis-pasien">{{ judulPasienLama }}</p>
                                    <p class="number-jenis-pasien">{{ jumlahPasienLama }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="uk-width-expand@m" style="display: contents;">
                            <img class="img-doktor" src='@/Assets/stock-doctor-image.png' alt="imgDoctor">
                        </div>

                    </div>

                
                </div>
            </div>
            <!-- END box card total kunjungan pasien -->


            <!-- box card income periode -->
            <div class="medium-chart-card-bg">
                <div style="padding: 1.25rem 1.2rem 0">
                    <!-- title & dropdown -->
                    <div class="uk-child-width-1-1@m uk-flex header-card">
                        <div>
                            <div class="header-chart-title">
                                <p class="chart-card-title"> 
                                    {{ judulPendapatan }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="dropdown-container" @click="toggleDropdown">
                                <img src='@/Assets/circle-dropdown.svg' alt="Dropdown Icon" class="dropdown-icon" />
                                <div uk-dropdown="pos: bottom-right;animation: slide-top; animate-out: true; duration: 700;" style="border-radius:10px">
                                    <ul class="uk-nav uk-dropdown-nav">
                                        <li>
                                            <a href="/praktek/dokter/kas" style="color:#808080">
                                                <span class="uk-margin-small-right"><font-awesome-icon :icon="['fas', 'money-bill-transfer']" size="lg" style="color: #808080;"/></span>
                                                Lihat Pencatatan Kas
                                            </a>
                                        </li>
                                        <li 
                                            class="uk-nav-divider"                                            >
                                        </li>
                                        <li>
                                            <a href="/praktek/dokter/laporan/pendapatan" style="color:#808080">
                                                <span class="uk-margin-small-right"><font-awesome-icon :icon="['fas', 'money-bill-trend-up']" size="lg" style="color: #808080;"/></span>
                                                Lihat Laporan Pendapatan
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <!-- chartjs Pendapatan Perbulan-->
                    <div style="padding:0.2rem;margin-top:12px">
                        <canvas style="height:255px;width:auto" ref="chartIncomeMonth"></canvas>
                    </div>

                    <!-- scrollbar -->
                    <div>

                    </div>
                </div>
            </div>
            <!-- END box card income periode -->
    </div>

    <div class="card-container" style="margin-bottom:55px">
        <!-- box card kas -->
        <div class="large-chart-card-bg uk-width-1-2@m">
            <div style="padding: 1.25rem 1.2rem 0">
                <!-- title & dropdown -->
                <div class="uk-child-width-1-1@m uk-flex header-card">
                    <div class="kas-header-chart-title">
                        <p class="chart-card-title"> 
                            {{ judulKas }}
                        </p>
                    </div>
                    <div class="dropdown-container" @click="toggleDropdown">
                            <img src='@/Assets/circle-dropdown.svg' alt="Dropdown Icon" class="dropdown-icon" />
                            <div uk-dropdown="pos: bottom-right;animation: slide-top; animate-out: true; duration: 700;" style="border-radius:10px">
                                <ul class="uk-nav uk-dropdown-nav">
                                    <li>
                                        <a href="/praktek/dokter/kas" style="color:#808080" uk-toggle1="target: #offcanvas-flip">
                                            <span class="uk-margin-small-right"><font-awesome-icon :icon="['fas', 'money-bill-transfer']" size="lg" style="color: #808080;"/></span>
                                            Lihat Pencatatan Kas
                                        </a>
                                    </li>
                                    <li 
                                        class="uk-nav-divider"                                            >
                                    </li>
                                    <li>
                                        <a href="/praktek/dokter/laporan/kas" style="color:#808080" @click.prevent="ubahPasien(item)">
                                            <span class="uk-margin-small-right"><font-awesome-icon :icon="['fas', 'money-bill-wave']" size="lg" style="color: #808080;"/></span>
                                            Lihat Laporan Kas
                                        </a>
                                    </li>
                                   
                                </ul>
                            </div>
                        </div>

                </div>
                
                <!-- chartjs Kas InOut Perbulan-->
                <div style="padding:0.2rem;margin-top:12px">
                    <canvas style="height:275px;width:auto" ref="chartInOutMonth"></canvas>
                </div>

                <!-- scrollbar -->
                <div>

                </div>
            </div>
        </div>
        <!-- END box card Kas -->
    </div>

    <div class="card-container" style="gap:31px">
        <!-- Box card Gender Pasien periode per month -->
        <div class="small-chart-card-bg">
            <div style="padding: 1.25rem 1.2rem 0">
                <!-- title & dropdown -->
                <div class="uk-child-width-1-1@m uk-flex header-card">
                    <div>
                        <div class="header-chart-title">
                            <p class="chart-card-title"> 
                                {{ judulGenderPasien }}
                            </p>
                        </div>
                    </div>
                   

                </div>
                
                <!-- chartjs Gender Pasien Perbulan-->
                <div style="padding:0.2rem;margin-top:12px">
                    <canvas style="height:255px;width:auto" ref="chartPatientMonth"></canvas>
                </div>

                <!-- scrollbar -->
                <div>

                </div>
            </div>
        </div>
        <!-- END box card Gender Pasien periode per month -->

        <!-- Box card Payment Method periode per month -->
        <div class="small-chart-card-bg">
            <div style="padding: 1.25rem 1.2rem 0">
                <!-- title & dropdown -->
                <div class="uk-child-width-1-1@m uk-flex header-card">
                    <div>
                        <div class="header-chart-title">
                            <p class="chart-card-title"> 
                                {{ judulPaymentMethod }}
                            </p>
                        </div>
                    </div>
                   

                </div>
                
                <!-- chartjs pie Payment Method Perbulan-->
                <div style="padding:0.2rem;margin-top:12px">
                    <canvas style="height:255px;width:auto" ref="chartPaymentMethodMonth"></canvas>
                </div>

                <!-- scrollbar -->
                <div>

                </div>
            </div>
        </div>
        <!-- END box card Payment Method periode per month -->

        
        <!-- Box card Penjamin periode per month-->
        <div class="small-chart-card-bg">
            <div style="padding: 1.25rem 1.2rem 0">
                <!-- title & dropdown -->
                <div class="uk-child-width-1-1@m uk-flex header-card">
                    <div>
                        <div class="header-chart-title">
                            <p class="chart-card-title"> 
                                {{ judulPenjamin }}
                            </p>
                        </div>
                    </div>
                   

                </div>
                
                <!-- chartjs pie Penjamin Perbulan-->
                <div style="padding:0.2rem;margin-top:12px">
                    <canvas style="height:255px;width:auto" ref="chartPenjaminMonth"></canvas>
                </div>

                <!-- scrollbar -->
                <div>

                </div>
            </div>
        </div>
        <!-- END box card Penjamin periode per month -->
    </div>

</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import "../dashboardStyle.css";
    import Chart from 'chart.js/auto';
    import 'chartjs-plugin-labels';

    export default {
        name: "summary",
        props: {
            judulKunjungan: String,
            jumlahKunjungan: Number,
            judulPasienBaru: String,
            judulPasienLama: String,
            jumlahPasienBaru: Number,
            jumlahPasienLama: Number,
            judulPendapatan: String,
            judulKas: String,
            judulGenderPasien: String,
            judulPaymentMethod: String,
            judulPenjamin: String,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
            isOpen : false,
            }
        },
        mounted() {
            this.initialize();
        },
        methods: {
            ...mapActions('praktekDokter',['dashboardSummary']),
            
            initialize(){            
                this.dashboardSummary().then((response) => {
                    if (response.success == true) {
                        this.chartIncome(response.data.pendapatan);
                        this.chartInOut(response.data.kas);
                        this.chartPatient(response.data.pasien);
                        this.chartPaymentMethod(response.data.cara_bayar);
                        this.chartPenjamin(response.data.penjamin);
                    } else { alert (response.message) }
                });
            },

            // chart Pendapatan Per Bulan
            chartIncome(ttlPendapatan) {
            console.log(ttlPendapatan);
                const ctx = this.$refs.chartIncomeMonth.getContext("2d");
                const keys = Object.keys(ttlPendapatan);
                const values = Object.values(ttlPendapatan);
                new Chart(ctx, {
                    type: "line",
                    data: {
                    labels:keys,
                    datasets: [
                        {
                        label: "Nominal (Rp.)",
                        data: values,
                        fill: false,
                        borderColor: "#FAC032",
                        backgroundColor: "#FAC032",
                        borderWidth: 2,
                        tension: 0.5
                        },
                    ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    font: {
                                        size: 15
                                    }
                                }
                            }
                        },
                    },
                });
            },

            // chart Kas InOut Per Bulan
            chartInOut(ttlKas) {
                const ctx = this.$refs.chartInOutMonth.getContext("2d");
                const keys = Object.keys(ttlKas);
                const firstElements = [];
                const secondElements = [];
                for (const key in ttlKas) {
                    if (ttlKas.hasOwnProperty(key)) {
                        firstElements.push(ttlKas[key]['total_jml_terima']);
                        secondElements.push(ttlKas[key]['total_jml_bayar']);
                    }
                }
                new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: keys,
                        datasets: [
                            {
                                label: "Kas Masuk (Rp.)",
                                data: firstElements,
                                fill: false,
                                borderColor: "#18A0FB",
                                backgroundColor: "#18A0FB",
                                borderWidth: 2,
                                tension: 0.5
                            },
                            {
                                label: "Kas Keluar (Rp.)",
                                data: secondElements,
                                fill: false,
                                borderColor: "#FAC032",
                                backgroundColor: "#FAC032",
                                borderWidth: 2,
                                tension: 0.5
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    font: {
                                        size: 15
                                    }
                                }
                            }
                        },
                    },
                });
            },

            // chart Pie - Pasien Per Bulan
            chartPatient(ttlPasien) {
                const ctx = this.$refs.chartPatientMonth.getContext("2d");
                const keys = Object.keys(ttlPasien);
                const values = Object.values(ttlPasien);

                // Sebagai Highlight saat Hover di suatu dataset chart Pie
                function handleHover(evt, item, legend) {
                    legend.chart.data.datasets[0].backgroundColor.forEach(
                    (color, index, colors) => 
                        {
                        colors[index] = index === item.index || color.length === 9 ? color : color + '4D';
                        }
                    );
                legend.chart.update();
                };

                function handleLeave(evt, item, legend) {
                    legend.chart.data.datasets[0].backgroundColor.forEach(
                    (color, index, colors) => 
                        {
                        colors[index] = color.length === 9 ? color.slice(0, -2) : color;
                        }
                    );
                legend.chart.update();
                };
                // End Highlight

                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: keys,
                        datasets: [
                            {
                                labels: "Jumlah:",
                                data: values,
                                fill: false,
                                borderColor: "#ffffff",
                                backgroundColor: [
                                    "#E0C744",
                                    "#966AA1"
                                ],
                                hoverOffset: 4,
                            },
                        ],
                    },
                
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position:"bottom",
                                onHover: handleHover,
                                onLeave: handleLeave,
                                labels: {
                                    font: {
                                        size: 15
                                    }
                                }
                            },
                            labels: 
                            {
                                mode: 'percentage',
                                precision: 1
                            },
                        },
                        layout: {
                            // padding: {
                            //     bottom: 50
                            // }
                        }
                        
                    },
                });
            },

            // chart Pie - Payment Method Per Bulan
            chartPaymentMethod(ttlCaraBayar) {
                const ctx = this.$refs.chartPaymentMethodMonth.getContext("2d");
                const keys = Object.keys(ttlCaraBayar);
                const values = Object.values(ttlCaraBayar);

                // mengganti huruf capital semua menjadi capital di awal huruf saja
                const capitalizedLabels = keys.map(label => label.charAt(0).toUpperCase() + label.slice(1).toLowerCase());

                // Sebagai Highlight saat Hover di suatu dataset chart Pie
                function handleHover(evt, item, legend) {
                    legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
                        colors[index] = index === item.index || color.length === 9 ? color : color + '4D';
                    });
                legend.chart.update();
                };

                function handleLeave(evt, item, legend) {
                    legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
                        colors[index] = color.length === 9 ? color.slice(0, -2) : color;
                    });
                legend.chart.update();
                };
                // End Highlight

                new Chart(ctx, {
                    type: "pie",
                    data: {
                        // labels: keys,
                        labels: capitalizedLabels,
                        datasets: [
                            {
                                label: "Jumlah",
                                data: values,
                                fill: false,
                                borderColor: "#ffffff",
                                backgroundColor: [
                                    "#2BD09F",
                                    "#33C3E2",
                                    "#ED212A",
                                    "#C144E0"
                                ],
                                hoverOffset: 4,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position:"bottom",
                                onHover: handleHover,
                                onLeave: handleLeave,
                                labels: {
                                    font: {
                                        size: 15,
                                    },
                                },
                            },
                        }
                    },
                });
            },

            // chart Pie - Penjamin Per Bulan
            chartPenjamin(ttlPenjamin) {
                const ctx = this.$refs.chartPenjaminMonth.getContext("2d");
                const keys = Object.keys(ttlPenjamin);
                const values = Object.values(ttlPenjamin);
                
                // mengganti huruf capital semua menjadi capital di awal huruf saja
                const capitalizedLabels = keys.map(label => label.charAt(0).toUpperCase() + label.slice(1).toLowerCase());

                // Sebagai Highlight saat Hover di suatu dataset chart Pie
                function handleHover(evt, item, legend) {
                    legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
                        colors[index] = index === item.index || color.length === 9 ? color : color + '4D';
                    });
                legend.chart.update();
                };

                function handleLeave(evt, item, legend) {
                    legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
                        colors[index] = color.length === 9 ? color.slice(0, -2) : color;
                    });
                legend.chart.update();
                };
                // End Highlight

                new Chart(ctx, {
                    type: "pie",
                    data: {
                        labels: capitalizedLabels,
                        datasets: [
                            {
                                label: "Jumlah",
                                data: values,
                                fill: false,
                                borderColor: "#ffffff",
                                backgroundColor: [
                                    "#D4AF37",
                                    "#35659D",
                                    "#359254",
                                ],
                                hoverOffset: 4,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position:"bottom",
                                onHover: handleHover,
                                onLeave: handleLeave,
                                labels: {
                                    font: {
                                        size: 15
                                    }
                                }
                            }
                        }
                    },
                });
            },

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


/**  1. Chart Card BG **/
.card-container {
    display: flex;
    flex-wrap: wrap;
    /* gap: 31px; */
}
.small-chart-card-bg {
    background-color: azure;
    border-radius: 8px;
    box-shadow: 0px 12px 26px #101e7333;
    height: 366px;
    width: 333px;
    left: 5px;
    top: 0;
    flex: 1 0 calc(33.333% - 31px);

}
.medium-chart-card-bg {
    background-color: azure;
    border-radius: 8px;
    box-shadow: 0px 12px 26px #101e7333;
    height: 366px;
    width: 475px;
    left: 5px;
    top: 0;
    flex: 1 0 calc(30% - 31px);
}
.large-chart-card-bg {
    background-color: azure;
    border-radius: 8px;
    box-shadow: 0px 12px 26px #101e7333;
    height: 463px;
    width: 1362px;
    left: 5px;
    top: 0;
    flex: 1 0 calc(50% - 31px);
}

/**  2. Box Kunjungan **/
.layer-card-kunjungan {
    align-items: flex-start;
    flex: 1 0 calc(53% - 31px);
    display: flex;
    /* height: 400.83px; */
    /* padding: 25px 0px; */
    /* width: 885.74px; */
    width: 0;
}
.card-kunjungan {
    display: flex;
    flex-wrap: wrap;
    flex: 1 0 calc(60% - 31px);
    margin: 0;
    padding: 0;
    list-style: none;
    width:780px;
    height:365px;
    top: 74.48px;
    left:0.74px;
    border-radius: 16.4px;
    transform: rotate(0.12deg);
    box-shadow: 0px 12px 26px #101e7333;
    background: linear-gradient(66deg, #B2E6FD -5.25%, #9BD8F1 35.43%, rgba(204, 2, 2, 0.71) 98.77%);
}
.div-total-kunjungan {
    align-items: flex-start;
    display: flex;
    flex-direction: column;
    gap: 7px;
    left: 33px;
    min-height: 159px;
    padding:1.2em 2.2em 0;
    top: 97px;
    width: 328px;
}
.span-jenis-pasien {
    align-items: flex-start;
    display: flex;
    gap: 27px;
    left: 33px;
    min-height: 159px;
    padding:1.2em 1.2em 0;
    top: 97px;
    width: 328px;
}
.title-kunjungan {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-xl);
    font-weight: var(--semibold);
    line-height: 45px;
    white-space: nowrap;
}
.number-kunjungan {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-3xl);
    font-weight: var(--bold);
    letter-spacing: 0.26px;
    line-height: 60px;
    margin-left: 23px;
}

.title-jenis-pasien {
    color:var(--black);
    font-family: var(--poppins);
    font-size: var(--font-lg-26);
    font-weight: 600;
    letter-spacing: 0.25px;
    text-transform: capitalize;
    margin-left: 23px;
    margin-top: 10px;
}
.number-jenis-pasien {
    color:var(--black);
    font-family: var(--poppins);
    font-size: var(--font-xl);
    font-weight: 600;
    margin-left: 1.5em;
    margin-top: -12px;
}


.overlap {
    height: 441px;
    left: 0;
    position: absolute;
    top: 0;
    width: 856px;
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
.img-doktor {
    max-width: 100%;  
    height: 441px;
    /* right: 350px; */
    right: 165px; /* resolusi 1600 x 753,75
    /* right: 0; */
    margin-top: -75px;
    position: relative;
}
/* .screen a {
    display: contents;
    text-decoration: none;
} */
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



/** 3. Chart Line Income Periode **/
.header-card {
    border-bottom: 1.5px solid rgba(0, 0, 0, 0.35);
    width: auto;
}
.header-chart-title {
    align-items: flex-start;
    display: flex;
    flex-direction: column;
    gap: 14px;
    min-height: 42px;
    width: 410px;
}
.chart-card-title {
    color:#4F4F4F;
    font-family: var(--lato);
    font-size: var(--font-lg-26);
    font-weight: var(--bold);
    letter-spacing: 0.1px;
    line-height: 16px;
}

.divider-chart {
    height: 179px;
    left: 100px;
    position: absolute;
    top: 100px;
    width: 341px;
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
/** END Chart Line Income Periode  **/



/** 4. Chart Line Kas InOut **/
.kas-header-chart-title {
    align-items: flex-start;
    display: flex;
    flex-direction: column;
    gap: 14px;
    min-height: 42px;
    width: 1330px;
}

/** END Chart Line Kas InOut **/



/** 5. Chart Pie Ringkasan Pasien **/
.pasien-header-card {
    border-bottom: 1.5px solid rgba(0, 0, 0, 0.35);
    width: auto;
}
.pasien-header-chart-title {
    align-items: flex-start;
    display: flex;
    flex-direction: column;
    gap: 14px;
    min-height: 42px;
    width: 410px;
}
.pasien-chart-card-title {
    color:#4F4F4F;
    font-family: var(--lato);
    font-size: var(--font-lg-26);
    font-weight: var(--bold);
    letter-spacing: 0.1px;
    line-height: 16px;
}
.pasien-chart-card-bg {
    background-color: azure;
    border-radius: 8px;
    box-shadow: 0px 12px 26px #101e7333;
    height: 366px;
    width: 475px;
    left: 5px;
    top: 0;
    flex-shrink: 0;
}
/** END Chart Pie Ringkasan Pasien  **/



/** 6. Chart Pie Ringkasan Payment Methods **/

/** END Chart Pie Ringkasan Payment Methods  **/



/** 7. Chart Pie Ringkasan Penjamin **/

/** END Chart Pie Ringkasan Penjamin **/



/* Jika settingan display laptop Anda:
    - Scale: 200%
    - Resolution: 2560 x 1440
*/
/* @media (max-width: 753px) { */
@media only screen and (max-width: 670px) {
    /* .layer-card-kunjungan,
    .medium-chart-card-bg {
        flex: 1 0 calc(100% - 31px);
    } */
    /* .layer-card-kunjungan {
        flex: 1 0 calc(53% - 31px);
    } */
    .card-kunjungan {
        width: 0;
    }
    .img-doktor {
        right: 350px!important;
    }
}

/* ukuran ipad mini */
@media screen and (max-width: 768px) {
    .small-chart-card-bg {
        flex: 1 0 calc(100% - 31px);
    }
    .large-chart-card-bg {
        flex: 1 0 calc(100% - 31px);
    }
    .layer-card-kunjungan {
        flex: 1 0 calc(60% - 31px);
    }
    .medium-chart-card-bg {
        flex: 1 0 calc(40% - 31px);
    }
    /* .layer-card-kunjungan {
        width: 785.74px;
    } */
    /* .img-doktor {
        max-width: -webkit-fill-available!important;
        right: 230px!important;
    } */
    img.img-doktor {
        max-width: 100%;
        height: auto;
        right: 250px!important;
    }

}

/* ukuran ipad pro */
@media screen and (max-width: 1024px) {
    .title-kunjungan {
        font-size: var(--font-lg-26);
    }
    .number-kunjungan {
        font-size: var(--font-2xl);
    }

    .title-jenis-pasien {
        font-size: var(--font-lg-22);
        margin-top:22px;
    }
    .number-jenis-pasien {
        font-size: var(--font-lg-22);
        margin-left: 2.35em;
    }
    .img-doktor {
        max-width: 100%;
        right: 205px!important;
        height: auto;
    }
    .card-kunjungan {
        flex: 1 0 calc(60% - 31px);
        width: 700px;
    }
    .medium-chart-card-bg {
        flex: 1 0 calc(40% - 31px);
    }
    /* .layer-card-kunjungan {
        width: 575.74px;
    } */

}

/* ukuran ipad pro */
@media screen and (max-width: 1024px) {
    .uk-width-3-4\@s {
        flex: none;
        min-width: 1px;
    }
    .uk-width-1-4\@s {
        flex: none;
        min-width: 1px;
    }
    .chart-card-title {
        font-size: var(--font-lg-22);
    }
    .img-doktor {
        max-width: -webkit-fill-available!important;
        right: 275px!important;
    }
    .card-kunjungan {
        width: 0;
    }


}


</style>