import axios from './httpReq';

let baseUrl = '/cetakan';

export default {
    namespaced: true,

    state: () => ({
        resepLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_RESEP_LISTS(state,payload) { 
            state.resepLists = payload; 
        },
        CLR_RESEP_LISTS(state) { 
            state.resepLists = [];
        },
    },

    getters : {
        //return all user client data
        resepLists : state => { 
            return state.resepLists;
        },    
    },

    actions : {

        dataResep({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/etiket/${payload}`)
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