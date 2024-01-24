import axios from './httpReq';

let linenProductUrl = '/linens';
let receiveBaseUrl = '/linens/receive';
let distributionBaseUrl = '/linens/distributions';

export default {
    namespaced: true,

    state: () => ({
        linenLists : null,
        receiveLists : null,
        distributionLists : null,
        penerimaan : {
            linen_terima_id : null,
            unit_pengirim_id : null,
            unit_pengirim : null,
            unit_penerima_id : null,
            unit_penerima : null,
            pengirim : null,
            penerima : null,
            tgl_penerimaan : null,
            tgl_selesai : null,
            berat : 0,
            satuan : null,
            is_infeksius : false,
            status : 'TERIMA',
            is_aktif : true,
            client_id : null,
            items : [],
        },
        distribusiData : [],

    }),

    mutations : {
        SET_LINEN_LISTS(state,payload) { state.linenLists = payload; },
        SET_RECEIVE_LISTS(state,payload) {  state.receiveLists = payload; },
        SET_DISTRIBUTION_LISTS(state,payload) {  state.distributionLists = payload; },
    },

    getters : {
        isLinenListsExist :state => {
            return state.linenLists !== "null" && state.linenLists !== null
        },
        linenLists : state => { 
            return state.linenLists;
        },    
    },

    actions : {
        getLinenLists({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(linenProductUrl, payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    }                     
                    else { commit('SET_LINEN_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        /**
        * PENERIMAAN LINEN 
         */
        createPenerimaanLinen({ commit }, payload ) {
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

        updatePenerimaanLinen({ commit }, payload ) {
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

        deletePenerimaanLinen({ commit }, payload ) {
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

        dataPenerimaanLinen({ commit }, payload ) {
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

        listPenerimaanLinen({ commit }, payload ) {
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
        * DISTRIBUSI/PENGIRIMAN LINEN 
         */
         createPengirimanLinen({ commit }, payload ) {
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

        updatePengirimanLinen({ commit }, payload ) {
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

        deletePengirimanLinen({ commit }, payload ) {
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

        dataPengirimanLinen({ commit }, payload ) {
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

        listPengirimanLinen({ commit }, payload ) {
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