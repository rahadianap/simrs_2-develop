
import { saturate } from 'tailwindcss/defaultTheme';
import axios from './httpReq';

export default {
    namespaced: false,

    state: () => ({
        token: localStorage.getItem('token'),
        user: localStorage.getItem('user'),
    }),

    mutations : {
        //save user token
        SET_TOKEN(state, payload) { 
            localStorage.setItem('token', payload); 
            state.token = payload; 
        },   
        CLR_TOKEN(state, payload) { 
            localStorage.setItem('token', null);
            state.token = null;
        },   
             
        //set and clear auth user
        SET_USER(state, payload) { 
            localStorage.setItem('user', payload); 
            state.user = payload;
        },
        CLR_USER(state) { 
            localStorage.removeItem('user');
            state.user = null;
        },

         
    },

    getters : {
        token: state => {
            return state.token;
        },
        //return flag user is auth or not        
        isAuth: state => {
            return state.token !== "null" && state.token !== null
        },
        //return auth user data
        user : state => { 
            if(state.user == null) { return null; }
            else { return JSON.parse(state.user); }
        },        
    },

    actions : {
        signup({ commit }, payload ) {
            localStorage.setItem('token', null) 
            commit('SET_TOKEN', null, { root: false });

            return new Promise((resolve, reject) => {
                axios.post('/signup', payload)
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

        signupVerification({ commit }, payload ) {
            localStorage.setItem('token', null) 
            commit('SET_TOKEN', null, { root: false });

            return new Promise((resolve, reject) => {
                axios.post('/signup/verification', payload)
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

        login({ commit }, payload) {
            localStorage.setItem('token', null) 
            commit('SET_TOKEN', null)
            
            return new Promise((resolve, reject) => {
                axios.post('/login', payload)
                .then((response) => {
                    if (response.data.success == true) {                        
                        commit('SET_TOKEN', response.data.access_token)
                        commit('SET_USER',JSON.stringify(response.data.auth))
                    } 
                    else {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true })    
                    }
                    resolve(response.data)
                })
                
                .catch((error) => {
                    commit('SET_ERRORS', { invalid : error.response.data.error }, { root: true })
                    reject(error);
                })
            })
        },

        resetPassword({ commit }, payload ) {
            localStorage.setItem('token', null) 
            commit('SET_TOKEN', null, { root: false });

            return new Promise((resolve, reject) => {
                axios.post('/reset/password', payload)
                .then((response) => {
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

        resetPasswordData({ commit }, payload ) {
            localStorage.setItem('token', null) 
            commit('SET_TOKEN', null, { root: false });

            return new Promise((resolve, reject) => {
                axios.get('/reset/password',{params:payload})
                .then((response) => {
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

        resetVerification({ commit }, payload ) {
            localStorage.setItem('token', null) 
            commit('SET_TOKEN', null, { root: false });

            return new Promise((resolve, reject) => {
                axios.post('/reset/verification', payload)
                .then((response) => {
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

        logout({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post('/logout', payload)
                .then((response) => {
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

        changePassword({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post('/change/password', payload)
                .then((response) => {
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

    }
}