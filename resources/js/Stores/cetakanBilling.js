import axios from './httpReq';

let baseUrl = '/cetakan/billing';

export default {
    namespaced: true,

    state: () => ({
        cBillingLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_CBILLING_LISTS(state,payload) { 
            state.cBillingLists = payload; 
        },
        CLR_CBILLING_LISTS(state) { 
            state.cBillingLists = [];
        },
    },

    getters : {
        //return all user client data
        cBillingLists : state => { 
            return state.cBillingLists;
        },    
    },

    actions : {

        cetakPembayaran({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+`/${payload[0]['jnsCetakan']}/${payload[0]['ids']}`)
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


        cetakUangMukaPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/cetakan/uangMuka', {params:payload[0]})
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