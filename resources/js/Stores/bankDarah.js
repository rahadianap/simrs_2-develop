import axios from './httpReq';

let bloodUrl = '/bloods/stocks';
let receiveBaseUrl = '/bloods/receive';
let requestBaseUrl = '/bloods/requests';
let distributionBaseUrl = '/bloods/distributions';
let exterminationBaseUrl = '/bloods/exterminations';

export default {
    namespaced: true,

    state: () => ({
        bloodReceiveLists : null,
        bloodDistributionLists : null,
        groupDarahLists : null,
        asalDarahLists : null,
        bloodExterminationLists : null,
        bloodRequestLists : null,
    }),

    mutations : {
        SET_BLOOD_REFS(state,payload) {  
            if(payload) {
                payload.forEach(el => {
                    if(el.ref_id == 'GROUP_DARAH') { state.groupDarahLists = el; }
                    else if(el.ref_id == 'ASAL_DARAH') { state.asalDarahLists = el; }
                })
            }
        },

        SET_BLOOD_RECEIVE_LISTS(state,payload) {  state.bloodReceiveLists = payload; },
        SET_BLOOD_DISTRIBUTION_LISTS(state,payload) {  state.bloodDistributionLists = payload; },
        SET_BLOOD_EXTERMINATION_LISTS(state,payload) {  state.bloodExterminationLists = payload; },
        SET_BLOOD_REQUEST_LISTS(state,payload) {  state.bloodRequestLists = payload; },
    },

    getters : {
        bloodReceiveLists : state => { 
            return state.bloodReceiveLists;
        },  
        bloodRequestLists : state => { 
            return state.bloodRequestLists;
        },
        bloodDistributionLists : state => { 
            return state.bloodDistributionLists;
        },
        bloodExtermintationLists : state => { 
            return state.bloodExterminationLists;
        },
        groupDarahLists : state => { 
            return state.groupDarahLists;
        },   
        asalDarahLists : state => { 
            return state.asalDarahLists;
        },   
    },

    actions : {
        getDarahRefsLists({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get('/bloods/references')
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_BLOOD_REFS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        /**
        * PENERIMAAN DARAH 
         */
        createPenerimaanDarah({ commit }, payload ) {
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

        updatePenerimaanDarah({ commit }, payload ) {
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

        deletePenerimaanDarah({ commit }, payload ) {
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

        dataPenerimaanDarah({ commit }, payload ) {
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

        listPenerimaanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(receiveBaseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_BLOOD_RECEIVE_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        /** PERMINTAAN DARAH */
        createPermintaanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(requestBaseUrl, payload)
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

        updatePermintaanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(requestBaseUrl, payload)
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

        deletePermintaanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${requestBaseUrl}/${payload}`)
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

        dataPermintaanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${requestBaseUrl}/${payload}`)
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

        listPermintaanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(requestBaseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_BLOOD_REQUEST_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },


        /**
        * DISTRIBUSI/PENGIRIMAN DARAH 
         */
         createPengirimanDarah({ commit }, payload ) {
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

        updatePengirimanDarah({ commit }, payload ) {
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

        deletePengirimanDarah({ commit }, payload ) {
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

        dataPengirimanDarah({ commit }, payload ) {
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

        listPengirimanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(distributionBaseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_BLOOD_DISTRIBUTION_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        /**
        * PEMUSNAHAN DARAH 
         */
        getStockDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(bloodUrl, {params:payload})
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

         createPemusnahanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(exterminationBaseUrl, payload)
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

        deletePemusnahanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${exterminationBaseUrl}/${payload}`)
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

        dataPemusnahanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${exterminationBaseUrl}/${payload}`)
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

        listPemusnahanDarah({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(exterminationBaseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_BLOOD_EXTERMINATION_LISTS',response.data.data); }
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