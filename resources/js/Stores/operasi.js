import axios from './httpReq';

// let baseUrlAdmisi = '/operation/registrations';
let baseUrlReg = 'surgery';

export default {
    namespaced: true,

    state: () => ({
        filterTable : { 
            is_aktif : true, 
            keyword : '',
            per_page : 10,
            page : 1,
            tgl_operasi_start : null,
            tgl_operasi_end : null,
            unit : null,
            dokter : null,
            status : null,
        },

        operasiLists : [],
        dataOperasi : {
            trx_id : null,
            reg_id : null,
            detail : null,            
            detail : [],
            tim_operasi : [],
            transaksi : null,
            persalinan : {
                persalinan_id : null,
                trx_id : null,
                reg_id : null,
                detail : [],
            },
        },

    }),

    mutations : {
        SET_OPERASI_LISTS(state,payload) { 
            state.operasiLists = payload; 
        },
        CLR_OPERASI_LISTS(state) { 
            state.operasiLists = []; 
        },
        SET_OPERASI(state, payload) {
            state.dataOperasi = payload;
        },
        CLR_OPERASI(state) {
            state.dataOperasi = {
                trx_id : null,
                reg_id : null,
                detail : null,            
                detail : [],
                tim_operasi : [],
                transaksi : null,
                persalinan : {
                    persalinan_id : null,
                    trx_id : null,
                    reg_id : null,
                    detail : [],
                },
            }
        }
    },

    getters : {
        operasiLists : state => { 
            if(state.operasiLists.data) { return state.operasiLists.data; }
            else { return state.operasiLists; }
        },

        operasiFilterTable : state => { 
            return state.filterTable; 
        },

        operasiData : state => {
            return state.dataOperasi;
        }
    },

    actions : {
        listOperasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/lists`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) { commit('SET_ERRORS', { invalid: response.data.message }, { root: true }); } 
                    else { commit('SET_OPERASI_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true }); reject(error);
                })
            })
        },

        createOperasi({ commit }, payload ) {
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

        updateOperasi({ commit }, payload ) {
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

        deleteOperasi({ commit }, payload ) {
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

        dataOperasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else {
                        commit('SET_OPERASI',response.data.data); 
                    }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        confirmOperasi({ commit }, payload ) {
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
              
        unConfirmOperasi({ commit }, payload ) {
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
    }
}