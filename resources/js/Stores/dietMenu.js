import axios from './httpReq';

let baseUrl = '/diet/menu';

export default {
    namespaced: true,

    state: () => ({
        mDietLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_DIET_LISTS(state,payload) { 
            state.mDietLists = payload; 
        },
        CLR_DIET_LISTS(state) { 
            state.mDietLists = [];
        },
    },

    getters : {
        //return all user client data
        mDietLists : state => { 
            return state.mDietLists;
        },    
    },

    actions : {

        listDiet({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_DIET_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        createDietMenu({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(`/diet/${payload.diet_id}/menu`, payload)
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