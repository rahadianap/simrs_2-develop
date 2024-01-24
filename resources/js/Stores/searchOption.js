import axios from './httpReq';
// let baseUrl = '';

export default {
    namespaced: false,
    state: () => ({
        optionLists : [],
        baseUrl : '',
    }),

    mutations : {
        SET_OPTION_LISTS(state,payload) { 
            state.optionLists = payload; 
        },
        CLR_OPTION_LISTS(state) { 
            state.optionLists = [];
        },
        SET_URL_SEARCH_OPTIONS(state,payload) {
            state.baseUrl = payload;
        } 
    },

    getters : {
        optionLists : state => { 
            return state.optionLists;
        },
    },

    actions : {
        searchOptions({ commit,state }, payload ) {            
            return new Promise((resolve, reject) => {
                axios.get(state.baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_OPTION_LISTS',JSON.parse(JSON.stringify(response.data.data.data))); }
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