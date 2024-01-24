import axios from './httpReq';

let baseUrl = '/informed';

export default {
    namespaced: true,

    state: () => ({
        informedLists : [],
        filterInformedList : {
            keyword : null,
            is_aktif : true,
            per_page : 20,
        },
        informedListUnit : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_INFORMED_LISTS(state,payload) { 
            state.informedLists = payload; 
        },
        CLR_INFORMED_LISTS(state) { 
            state.informedLists = [];
        },

        SET_INFORMED_UNIT_LISTS(state,payload) { 
            state.informedListUnit = payload; 
        },
        CLR_INFORMED_UNIT_LISTS(state) { 
            state.informedListUnit = [];
        },

    },

    getters : {
        //return all user client data
        informedLists : state => { 
            return state.informedLists;
        },    
        filterInformedLists : state => {
            return state.filterInformedList;
        },

        //return all user client data
        informedListUnit : state => { 
            return state.informedListUnit;
        },
    },

    actions : {
        createInformed({ commit }, payload ) {
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

        updateInformed({ commit }, payload ) {
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

        deleteInformed({ commit }, payload ) {
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

        dataInformed({ commit }, payload ) {
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

        listInformed({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_INFORMED_LISTS',JSON.parse(JSON.stringify(response.data.data))); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        deleteInformedItem({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrl}/item/${payload}`)
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

        createMapInformUnit({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/unit`, payload)
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

        deleteMapInformUnit({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrl}/unit/${payload}`)
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

        /**
         * 
         * mengambil seluruh daftar inform consent yang ada di unit terkait
         * berdasarkan unit ID 
         */
        listMapInformUnit({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/unit/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else {
                        console.log(response.data.data);
                        commit('SET_INFORMED_UNIT_LISTS',JSON.parse(JSON.stringify(response.data.data))); 
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