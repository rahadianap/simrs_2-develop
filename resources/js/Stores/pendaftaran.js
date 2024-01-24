import axios from './httpReq';

let baseUrl = '/registrations';

export default {
    namespaced: true,

    state: () => ({
        filterTable : { 
            is_aktif : true, 
            keyword : '',
            per_page : 10,
            page : 1,
            tgl_periksa_start : null,
            tgl_periksa_end : null,
            unit : null,
            nama_unit : null,
            dokter : null,
            nama_dokter : null,
            status : null,
            jns_penjamin : null,
        },
        registrationLists : [],
        //bookingLists : [],
        poliLists : null,
    }),

    mutations : {
        SET_REGISTRATION_LISTS(state,payload) {
            state.registrationLists = payload; 
        },
        CLR_REGISTRATION_LISTS(state) { 
            state.registrationLists = [];
        },

        // SET_BOOKING_LISTS(state,payload) {
        //     state.bookingLists = payload; 
        // },

        // SET_POLI_LISTS(state,payload) { 
        //     state.poliLists = payload; 
        // },
        // CLR_POLI_LISTS(state) { 
        //     state.poliLists = null;
        // },
    },

    getters : {
        //return all user client data
        registrationLists : state => { 
            if(state.registrationLists.data) { return state.registrationLists.data; }
            else { return state.registrationLists; }
        },
        regFilterTable : state => {
            return state.filterTable;
        }
    },

    actions : {        
        createRegistrasi({ commit }, payload ) {
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

        updateRegistrasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrl, payload)
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

        deleteRegistrasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrl}/${payload}`)
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

        dataRegistrasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${payload}`)
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

        listRegistrasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_REGISTRATION_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        listUnitToday({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/registrations/unit/praktek`, {params:payload})
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

        // listBooking({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(${baseUrlBooking}, {params:payload})
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             else { commit('SET_BOOKING_LISTS',response.data); }
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // dataBooking({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(`/outpatient/booking/${payload}`)
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

        // confirmBookingRegistrasi({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.put('/outpatient/booking/konfirm', payload)
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

        dataRegistrasiPoli({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${payload}`)
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
    
        confirmPelayanan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put('registrations/confirm', payload)
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