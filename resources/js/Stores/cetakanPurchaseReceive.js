import axios from './httpReq';

let baseUrl = '/cetakan/pordata';

export default {
    namespaced: true,

    state: () => ({
        cPORLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_CPOR_LISTS(state,payload) { 
            state.cPORLists = payload; 
        },
        CLR_CPOR_LISTS(state) { 
            state.cPORLists = [];
        },
    },

    getters : {
        //return all user client data
        cPORLists : state => { 
            return state.cPORLists;
        },    
    },

    actions : {

        dataCetakanPOR({ commit }, payload ) {
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
    }
}