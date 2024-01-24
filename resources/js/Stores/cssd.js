import axios from './httpReq';

let linenProductUrl = '/sterilizations';
let receiveBaseUrl = '/sterilizations/receive';
let distributionBaseUrl = '/sterilizations/distributions';

export default {
    namespaced: true,

    state: () => ({
        cssdLists : null,
        receiveLists : null,
        distributionLists : null,
        distribusiData : [],

    }),

    mutations : {
        SET_CSSD_LISTS(state,payload) { state.cssdLists = payload; },
        SET_RECEIVE_LISTS(state,payload) {  state.receiveLists = payload; },
        SET_DISTRIBUTION_LISTS(state,payload) {  state.distributionLists = payload; },
    },

    getters : {
        isCssdListsExist :state => {
            return state.cssdLists !== "null" && state.cssdLists !== null
        },
        cssdLists : state => { 
            return state.cssdLists;
        },    
    },

    actions : {
        getCssdLists({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(cssdProductUrl, payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    }                     
                    else { commit('SET_CSSD_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        /**
        * PENERIMAAN CSSD 
         */
        createPenerimaanCssd({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(receiveBaseUrl, payload)
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

        updatePenerimaanCssd({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(receiveBaseUrl, payload)
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

        deletePenerimaanCssd({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${receiveBaseUrl}/${payload}`)
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

        dataPenerimaanCssd({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${receiveBaseUrl}/${payload}`)
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

        listPenerimaanCssd({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(receiveBaseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_RECEIVE_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        /**
        * DISTRIBUSI/PENGIRIMAN CSSD 
         */
         createPengirimanCssd({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(distributionBaseUrl, payload)
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

        updatePengirimanCssd({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(distributionBaseUrl, payload)
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

        deletePengirimanCssd({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${distributionBaseUrl}/${payload}`)
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

        dataPengirimanCssd({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${distributionBaseUrl}/${payload}`)
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

        listPengirimanCssd({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(distributionBaseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_DISTRIBUTION_LISTS',response.data.data); }
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