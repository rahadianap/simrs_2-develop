import axios from './httpReq';

let baseUrl = '/report';

export default {
    namespaced: true,

    state: () => ({
        rLabLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_RLAB_LISTS(state,payload) { 
            state.rLabLists = payload; 
        },
        CLR_RLAB_LISTS(state) { 
            state.rLabLists = [];
        },
    },

    getters : {
        //return all user client data
        rLabLists : state => { 
            return state.rLabLists;
        },    
    },

    actions : {

        // Start Create, Update, and Delete Report //
        listData({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_RLAB_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        createData({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrl, payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },
        // End Create, Update, and Delete Report //

        // LABORATORIUM
        pemeriksaanLab({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/lab/pemeriksaan', {params:payload[0]})
                .then((response) => {
                    // if (response.data.success == false) {
                    //     commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    // } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        kunjunganLab({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/lab/kunjungan', {params:payload[0]})
                .then((response) => {
                    // if (response.data.success == false) {
                    //     commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    // } 
                    //resolve(response.data);
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        rujukanLab({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/lab/rujukan', {params:payload[0]})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        skriningPenyakit({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/lab/skrining')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        rujukanDetailLab({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/lab/rujukanDetail', {params:payload[0]})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // PENERIMAAN
        penerimaanMedis({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/penerimaan/medis', {params:payload[0]})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        penerimaanUmum({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/penerimaan/umum', {params:payload[0]})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // CETAKAN
        // formInformasiPasien({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(baseUrl+'/cetakan/formInformasiPasien', {params:payload[0]})
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        formPenempatanKelasRi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/cetakan/formPenempatanKelasRi')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // gelangDewasa({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(baseUrl+'/cetakan/gelangDewasa', {params:payload[0]})
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // registrasiLabel({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(baseUrl+'/cetakan/registrasiLabel')
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        ringkasanMasukKeluar({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/cetakan/ringkasanMasukKeluar')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        formPenempatanKelasRi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/cetakan/formPenempatanKelasRi')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        kwitansi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/cetakan/kwitansi')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // uangMukaPasien({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(baseUrl+'/cetakan/uangMukaPasien', {params:payload[0]})
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        pendaftaranRajal({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/cetakan/pendaftaranRajal')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        tracer({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/cetakan/tracer')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // AKUNTING
        pendapatanRanap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/akunting/pendapatanRanap',{params:payload[0]})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        pendapatanRajal({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/akunting/pendapatanRajal',{params:payload[0]})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        pendapatanFarmasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/akunting/pendapatanFarmasi')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        harianKasirRanap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/akunting/harianKasirRanap',{params:payload[0]})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        harianKasirRajal({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/akunting/harianKasirRajal',{params:payload[0]})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        pendapatanHarianKasir({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/akunting/pendapatanHarianKasir')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        marektingPerPayer({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/akunting/marektingPerPayer')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // FARMASI
        pemakaianObat({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/farmasi/pemakaianObat')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },
        
        jumlahResep({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/farmasi/jumlahResep')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        waktuTunggu({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/farmasi/waktuTunggu')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },
    }
}