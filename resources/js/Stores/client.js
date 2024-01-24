import axios from './httpReq';

export default {
    namespaced: false,

    state: () => ({
        clients : [],
        selClient : localStorage.getItem('selclient'),
        menus : [],
        selMenu : localStorage.getItem('selmenu'),
        allMenus : [],
        clientMembers : [],
    }),

    mutations : {
        //set and clear selected board client
        SET_CLIENTS(state,payload) { 
            state.clients = payload; 
        },
        CLR_CLIENTS(state) { 
            state.clients = [];
        },

        //set and clear selected board client
        SET_SELECTED_CLIENT(state,payload) { 
            localStorage.setItem('selclient',payload); 
            state.selClient = payload; 
        },
        CLR_SELECTED_CLIENT(state) { 
            localStorage.removeItem('selClient');
            state.selClient = null;
        },

        //set and clear menu list based on user role on client board
        SET_MENUS(state, payload) { 
            state.menus = payload;
        },
        CLR_MENUS(state) { 
            state.menus = [];
        },

        //set and clear client member
        SET_MEMBERS(state, payload) { 
            state.clientMembers = payload;
        },
        CLR_MEMBERS(state) { 
            state.state.clientMembers = [];
        },

        //set and clear selected menu
        SET_SELECTED_MENU(state, payload) { 
            localStorage.setItem('selMenu',payload); 
            state.selMenu = payload; 
        },
        CLR_SELECTED_MENU(state, payload) { 
            localStorage.removeItem('selMenu');
            state.selMenu = null; 
        },

        //set and clear all client menus
        SET_ALL_MENUS(state, payload) { 
            state.allMenus = payload; 
        },
        CLR_ALL_MENUS(state, payload) { 
            state.allMenus = [];  
        },
    },

    getters : {
        //return all user client data
        clients : state => { 
            return state.clients;
        },
        //return selected client data
        client : state => { 
            return state.selClient; 
        },
        //return client id
        clientId : state => {
            if(state.selClient == null || state.selClient == '') { return null; } 
            else { 
                return JSON.parse(state.selClient).client_id; 
            }
        },
        //return list menu based on role on selected client
        userMenus : state => { 
            return state.menus;
        },
        selectedMenu : state => { 
            return state.selectedMenu; 
        },
        allClientMenus : state => { 
            return state.allMenus;
        },
        allClientMembers : state => { 
            return state.clientMembers;
        },
    },

    actions : {
        createClient({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post('/workplaces', payload)
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

        updateClient({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put('/workplaces', payload)
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

        deleteClient({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`/workplaces/${payload}`)
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

        dataClient({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/workplaces/${payload}`)
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

        listClient({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get('/workplaces', {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else {
                        commit('SET_CLIENTS',response.data.data);
                    }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        uploadLogo({commit}, payload){
            return new Promise((resolve, reject) => {
                axios.post('/workplaces/logo', payload)
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

        
        clientMenus({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get('/workplaces/menus')
                .then((response) => {
                    if(response.data.success == true) {
                        commit('SET_MENUS',response.data.data);
                    }
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data)
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        clientMembers({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get('/workplaces/members')
                .then((response) => {
                    if(response.data.success == true) {
                        commit('SET_MEMBERS',response.data.data);
                    }
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data)
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        getAllClientMenus({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get('/workplaces/allmenus')
                .then((response) => {
                    if(response.data.success == true) {
                        commit('SET_ALL_MENUS',response.data.data);
                    }
                    else if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
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