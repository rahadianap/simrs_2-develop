import axios from './httpReq';

let baseUrl = '/cetakan/distribusi';

export default {
    namespaced: true,

    state: () => ({
        cDistLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_CDIST_LISTS(state,payload) { 
            state.cDistLists = payload; 
        },
        CLR_CDIST_LISTS(state) { 
            state.cDistLists = [];
        },
    },

    getters : {
        //return all user client data
        cDistLists : state => { 
            return state.cDistLists;
        },    
    },

    actions : {

        dataDist({ commit }, payload ) {
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