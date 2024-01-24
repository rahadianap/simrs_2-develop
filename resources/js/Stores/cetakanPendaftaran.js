import axios from './httpReq';

let baseUrl = '/cetakan';

export default {
    namespaced: true,

    state: () => ({
        RegLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_REG_LISTS(state,payload) { 
            state.RegLists = payload; 
        },
        CLR_REG_LISTS(state) { 
            state.RegLists = [];
        },
    },

    getters : {
        //return all user client data
        RegLists : state => { 
            return state.RegLists;
        },    
    },

    actions : {

        dataCetakanPendaftaran({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/register/${payload}`)
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

        dataCetakanTracer({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/tracer/${payload}`)
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

        dataGelangDewasa({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/gelangDewasa', {params:payload[0]})
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

        dataFormInformasiPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/form', {params:payload[0]})
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

        dataRegistrasiLabel({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/registrasi', {params:payload[0]})
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