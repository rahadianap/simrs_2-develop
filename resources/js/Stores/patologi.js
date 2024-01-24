import axios from './httpReq';

let baseUrlReg = '/patologi/registrations';
let baseUrlResult = '/patologi/results';

export default {
    namespaced: true,

    state: () => ({
        radiologiLists : [],
        radTansaction : null,
        radTableFilter : { 
            keyword : '', 
            is_aktif : true, 
            per_page:10 
        },
    }),

    mutations : {
        SET_REGISTRATION_LISTS(state,payload) { 
            state.radiologiLists = payload; 
        },
        CLR_REGISTRATION_LISTS(state) { 
            state.radiologiLists = [];
        },
        SET_RAD_TRANSACTION(state,payload) { 
            state.radTansaction = payload; 
        },
        CLR_RAD_TRANSACTION(state,payload) { 
            state.radTansaction = null; 
        },
    },

    getters : {
        //return all user client data
        radiologiLists : state => { 
            return state.radiologiLists;
        },
        radTransaction : state => {
            return state.radTansaction;
        }
    },

    actions : {
        createPatologi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrlReg, payload)
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

        updatePatologi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrlReg, payload)
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

        deletePatologi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrlReg}/${payload}`)
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

        dataPatologi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/${payload}`)
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

        confirmPatologi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrlReg}/confirm`,payload)
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
              
        unConfirmPatologi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrlReg}/unconfirm`,payload)
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

        updateHasilPatologi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrlResult}`,payload)
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

        dataHasilPatologi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                //axios.get(`radiology/hasil/${payload['reg_id']}/${payload['is_multi_hasil']}`,payload)
                axios.get(`${baseUrlResult}/${payload}`)
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
        
        // listsRegRad({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(`registrations/RAD/patients`, {params:payload})
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             else { commit('SET_REGISTRATION_LISTS',response.data.data); }
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // dataRegRad({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(`registrations/${payload}`)
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             else { commit('SET_REGISTRATION_LISTS',response.data.data); }
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

    }
}