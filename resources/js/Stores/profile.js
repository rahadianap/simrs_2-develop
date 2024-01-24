import axios from './httpReq';

export default {
    namespaced: true,
    state: () => ({}),
    mutations : {},
    getters : {},
    actions : {
        submitProfile({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post('/profile', payload)
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
        
        dataProfile({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/profile`)
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

        uploadAvatar({commit}, payload){
            return new Promise((resolve, reject) => {
                axios.post('/avatar', payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true })
                    }
                    resolve(response.data)
                })
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },
    }
}