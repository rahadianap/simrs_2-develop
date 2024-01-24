import axios from './httpReq';
let baseUrl = '';

export default {
    namespaced: false,
    state: () => ({
        resultLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_RESULT_LISTS(state,payload) { 
            state.resultLists = payload; 
        },
        CLR_RESULT_LISTS(state) { 
            state.resultLists = [];
        },
        SET_URL_SEARCH(payload) {
            this.baseUrl = payload;
        }
    },

    getters : {
        //return all user client data
        resultlists : state => { 
            return state.resultLists;
        },
    },

    actions : {
        searchList({ commit }, payload ) {            
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_RESULT_LISTS',response.data.data); }
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