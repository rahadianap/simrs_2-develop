import axios from './httpReq';

let baseUrl = '/cetakan/kartustock';

export default {
    namespaced: true,

    state: () => ({
        cStockLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_CSTOCK_LISTS(state,payload) { 
            state.cStockLists = payload; 
        },
        CLR_CSTOCK_LISTS(state) { 
            state.cStockLists = [];
        },
    },

    getters : {
        //return all user client data
        cStockLists : state => { 
            return state.cStockLists;
        },    
    },

    actions : {

        dataKartuStock({ commit }, payload ) {
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