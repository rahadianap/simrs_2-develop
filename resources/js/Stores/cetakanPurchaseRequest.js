import axios from './httpReq';

let baseUrl = '/cetakan/prdata';

export default {
    namespaced: true,

    state: () => ({
        cPRLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_CDOCTOR_LISTS(state,payload) { 
            state.cPRLists = payload; 
        },
        CLR_CDOCTOR_LISTS(state) { 
            state.cPRLists = [];
        },
    },

    getters : {
        //return all user client data
        cPRLists : state => { 
            return state.cPRLists;
        },    
    },

    actions : {

        dataPR({ commit }, payload ) {
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