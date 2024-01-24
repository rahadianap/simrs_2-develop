import axios from './httpReq';

let baseUrlReg = '/radiology/registrations';
let baseUrlResult = '/radiology/results';

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
        createRadiologi({ commit }, payload ) {
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

        updateRadiologi({ commit }, payload ) {
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

        deleteRadiologi({ commit }, payload ) {
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

        dataRadiologi({ commit }, payload ) {
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

        confirmRadiologi({ commit }, payload ) {
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
              
        unConfirmRadiologi({ commit }, payload ) {
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

        updateHasilRadiologi({ commit }, payload ) {
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

        dataHasilRadiologi({ commit }, payload ) {
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