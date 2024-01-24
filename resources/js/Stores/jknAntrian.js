import axios from './httpReq';

let baseUrl = '';

export default {
    namespaced: true,

    state: () => ({
        dokterRefs : [],
        caraKeluarRef : [],
        pascaPulangRef : [],
        
    }),

    mutations : {
        SET_JKN_DOKTER_REFS(state,payload) { 
            state.dokterRefs = payload; 
        },
    },

    getters : {
        dokterRefs : state => { 
            return state.dokterRefs;
        },
    },

    actions : {             
        jknRefPoli({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/jkn/referensi/poli`)
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

        jknRefDokter({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/jkn/referensi/dokter`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_JKN_DOKTER_REFS',response.data.data); }
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