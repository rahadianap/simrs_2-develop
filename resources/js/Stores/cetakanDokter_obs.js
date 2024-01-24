import axios from './httpReq';

let baseUrl = '/cetakan/doctorsdata';

export default {
    namespaced: true,

    state: () => ({
        cDoctorsLists : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_CDOCTOR_LISTS(state,payload) { 
            state.cDoctorsLists = payload; 
        },
        CLR_CDOCTOR_LISTS(state) { 
            state.cDoctorsLists = [];
        },
    },

    getters : {
        //return all user client data
        cDoctorsLists : state => { 
            return state.cDoctorsLists;
        },    
    },

    actions : {

        dataDokter({ commit }, payload ) {
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