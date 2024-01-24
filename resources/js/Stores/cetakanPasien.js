import axios from './httpReq';

let baseUrl = '/cetakan';

export default {
    namespaced: true,

    state: () => ({
        PatientLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_REG_LISTS(state,payload) { 
            state.PatientLists = payload; 
        },
        CLR_REG_LISTS(state) { 
            state.PatientLists = [];
        },
    },

    getters : {
        //return all user client data
        PatientLists : state => { 
            return state.PatientLists;
        },    
    },

    actions : {

        dataRiwayat({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/history/${payload}`)
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