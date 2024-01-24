import axios from './httpReq';
let baseUrl = '/medical/records';


export default {
    namespaced: true,

    state: () => ({
        appmenus : [],
        role : [],
        roleLists : [],
        pasienId : null,
        medrecData : null,
    }),

    mutations : {
        SET_PASIEN_ID(state,payload) {
            state.pasienId = payload;
        },
        //set and clear selected board client
        SET_APP_MENUS(state,payload) { 
            state.appmenus = payload; 
        },
        CLR_APP_MENUS(state) { 
            state.appmenus = [];
        },

        //set and clear selected board client
        SET_ROLE(state,payload) { 
            state.role = payload; 
        },
        CLR_ROLE(state) { 
            state.role = null;
        },
        //set and clear selected board client
        SET_ROLE_LISTS(state,payload) { 
            state.roleLists = payload; 
        },
        CLR_ROLE_LISTS(state) { 
            state.roleLists = null;
        },
        CLR_MEDREC_DATA(state){
            state.medrecData = null;
        },
        SET_MEDREC_DATA(state,payload) {
            state.medrecData = payload;
        }
    },

    getters : {
        //return all user client data
        appmenus : state => { 
            return state.appmenus;
        },
        //return selected client data
        role : state => { 
            return state.role; 
        },
        roleLists : state => {
            return state.roleLists;
        },
        dataMedrec : state => {
            return state.medrecData;
        }
    },

    actions : {
        listMedicalRecord({ commit,state }, payload ) {
            console.log(state.pasienId);
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${state.pasienId}`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    // else {
                    //     commit('SET_ROLE_LISTS',response.data.data);
                    // }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        dataMedicalRecord({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/data/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else {
                        commit('SET_MEDREC_DATA',response.data.data);
                    }
                    resolve(response.data);
                })
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // createRole({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.post('/roles', payload)
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // updateRole({ commit }, payload ) {

        //     return new Promise((resolve, reject) => {
        //         axios.put('/roles', payload)
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // deleteRole({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.delete(`/roles/${payload}`)
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // dataRole({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(`/roles/${payload}`)
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // listRole({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get('/roles', {params:payload})
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             else {
        //                 commit('SET_ROLE_LISTS',response.data.data);
        //             }
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        createInformConsent({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/inform`, payload)
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

        updateInformConsent({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrl}/inform`, payload)
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

        deleteInformConsent({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrl}/inform/${payload}`)
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

        dataInformConsent({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/inform/${payload}`)
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