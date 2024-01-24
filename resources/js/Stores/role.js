import axios from './httpReq';

export default {
    namespaced: true,

    state: () => ({
        appmenus : [],
        role : [],
        roleLists : [],
    }),

    mutations : {
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
        }
    },

    actions : {
        createRole({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post('/roles', payload)
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

        updateRole({ commit }, payload ) {

            return new Promise((resolve, reject) => {
                axios.put('/roles', payload)
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

        deleteRole({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`/roles/${payload}`)
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

        dataRole({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/roles/${payload}`)
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

        listRole({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get('/roles', {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else {
                        commit('SET_ROLE_LISTS',response.data.data);
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