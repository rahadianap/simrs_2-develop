import axios from './httpReq';

let baseUrl = '/coa/accounts';

export default {
    namespaced: true,

    state: () => ({
        coaLists : [],
        coaHeaderLists : [],
    }),

    mutations : {
        //set and clear coa
        SET_COA_LISTS(state,payload) { 
            state.coaLists = payload; 
        },
        CLR_COA_LISTS(state) { 
            state.coaLists = [];
        },
        //set and clear header coa by level
        SET_COA_HEADER_LISTS(state,payload) { 
            state.coaHeaderLists = payload; 
        },
        CLR_COA_HEADER_LISTS(state) { 
            state.coaHeaderLists = [];
        },
    },

    getters : {
        coaLists : state => { 
            return state.coaLists;
        },   
        coaHeaderLists : state => { 
            return state.coaHeaderLists;
        }, 
        coaListExist : state => {
            if(state.coaLists) {
                if(state.coaLists.length > 0){ return true; }
                else { return false; }
            }
            else { return false; }
        } 
    },

    actions : {
        createCoa({ commit }, payload ) {
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

        updateCoa({ commit }, payload ) {
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

        deleteCoa({ commit }, payload ) {
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

        dataCoa({ commit }, payload ) {
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

        listCoa({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_COA_LISTS',response.data.data); }
                    resolve(response.data);
                })
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },
        listCoaHeader({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/coa/headers/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_COA_HEADER_LISTS',response.data.data); }
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