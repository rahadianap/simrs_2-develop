import axios from './httpReq';

let baseUrl = '/farmasi';

export default {
    namespaced: true,

    state: () => ({
        lists : [],
        depoFarmasiLists : [],
        prescription : {
            trx_id : null,
            reff_trx_id : null,
            items : [],
        }
    }),

    mutations : {
        //set and clear selected board client
        SET_PATIENT_PRESCRIPTION_LISTS(state,payload) { 
            state.lists = payload; 
        },
        CLR_PATIENT_PRESCRIPTION_LISTS(state) { 
            state.lists = [];
        },

        SET_PHARMACY_DEPO_LISTS(state,payload) {
            state.depoFarmasiLists = payload;
        },

        SET_PRESCRIPTION(state,payload) {
            state.prescription = payload;
        },
        SET_PRESCRIPTION_ITEMS(state,payload) {
            state.prescription.items = payload;
        },
        
    },

    getters : {
        //return all user client data
        patientPrescriptionLists : state => { 
            return state.lists;
        }, 
        depoFarmasiList : state => {
            return state.depoFarmasiLists;
        },   

        selectedPrescription: state => {
            return state.prescription;
        },
        
        selectedPrescriptionItems: state => {
            return state.prescription.items;
        },
        
    },

    actions : {
        createFarmasi({ commit }, payload ) {
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

        updateFarmasi({ commit }, payload ) {
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

        deleteFarmasi({ commit }, payload ) {
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

        dataFarmasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PRESCRIPTION',response.data.data); }
                    resolve(response.data);
                })
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        listFarmasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_SPECIALIST_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        listDepoFarmasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/depos`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PHARMACY_DEPO_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        listPatientPrescription({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/transaksi/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PATIENT_PRESCRIPTION_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        realisasiFarmasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/${payload}/realisasi`,payload)
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

        cancelFarmasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/${payload}/cancel`,payload)
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