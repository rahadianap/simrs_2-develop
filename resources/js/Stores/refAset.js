import axios from './httpReq';

let baseUrl = '/assets/references';

export default {
    namespaced: true,

    state: () => ({
        refAsetStatus : [],
        refAsetGroup : [],
        isRefAsetStatusExist : false,
        isRefAsetGroupExist : false,
    }),

    mutations : {
        SET_REF_GROUP_ASET(state,payload){
            state.refAsetGroup = payload;
            state.isRefAsetGroupExist = true; 
        },
        SET_REF_STATUS_ASET(state,payload){
            state.refAsetStatus = payload;
            state.isRefAsetStatusExist = true; 
        },

        CLR_REF_GROUP_ASET(state){
            state.refAsetGroup = [];
            state.isRefAsetGroupExist = false; 
        },
        CLR_REF_STATUS_ASET(state){
            state.refAsetStatus = [];
            state.isRefAsetStatusExist = false; 
        },
        
    },

    getters : {
        //return all referensi
        refAsetStatus : state => { return state.refAsetStatus; },    
        refAsetGroup : state => { return state.refAsetGroup; },
        isRefAsetStatusExist : state => { return state.isRefAsetStatusExist; },
        isRefAsetGroupExist : state => { return state.isRefAsetGroupExist; },
    },

    actions : {
        updateRefAset({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrl}/${payload.ref_aset_id}`, payload)
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

        listRefAsetGroup({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/group`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_REF_GROUP_ASET',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        listRefAsetStatus({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/status`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_REF_STATUS_ASET',response.data.data); }
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