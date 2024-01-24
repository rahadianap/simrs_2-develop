import axios from './httpReq';

let baseUrl = '/cetakan/lab';

export default {
    namespaced: true,

    state: () => ({
        cLabLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_CLAB_LISTS(state,payload) { 
            state.cLabLists = payload; 
        },
        CLR_CLAB_LISTS(state) { 
            state.cLabLists = [];
        },
    },

    getters : {
        //return all user client data
        cLabLists : state => { 
            return state.cLabLists;
        },    
    },

    actions : {

        dataCetakanLab({ commit }, payload ) {
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
        
        dataCetakanHasilLab({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/result/${payload}`)
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