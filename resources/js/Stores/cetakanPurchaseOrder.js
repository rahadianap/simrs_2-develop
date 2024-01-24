import axios from './httpReq';

let baseUrl = '/cetakan/podata';

export default {
    namespaced: true,

    state: () => ({
        cPOLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_CPO_LISTS(state,payload) { 
            state.cPOLists = payload; 
        },
        CLR_CPO_LISTS(state) { 
            state.cPOLists = [];
        },
    },

    getters : {
        //return all user client data
        cPOLists : state => { 
            return state.cPOLists;
        },    
    },

    actions : {

        dataCetakanPO({ commit }, payload ) {
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