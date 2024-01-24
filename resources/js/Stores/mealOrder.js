import axios from './httpReq';

let baseUrl = '/mealorder';

export default {
    namespaced: true,

    state: () => ({
        MOLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_MO_LISTS(state,payload) { 
            state.MOLists = payload; 
        },
        CLR_MO_LISTS(state) { 
            state.MOLists = [];
        },
    },

    getters : {
        //return all user client data
        MOLists : state => { 
            return state.MOLists;
        },    
    },

    actions : {

        listMealOrder({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_MO_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        createDiet({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrl, payload)
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