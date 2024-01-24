import axios from './httpReq';

let baseUrl = '/purchases/orders';

export default {
    namespaced: true,

    state: () => ({
        poLists : [],
        poData : {
            // pengadaan_id : null,
            // trx_id: null,
            // reff_trx_id: null,
            // tgl_transaksi: null,
            // tgl_dibutuhkan: null,
            // unit_id: null,
            // unit_nama: null,
            // termin : null,
            // jenis_bayar : null,
            // unit_nama: null,
            // depo_id: null,
            // depo_nama: null,
            // vendor_id: null,
            // vendor_nama: null,
            // catatan: null,
            // is_aktif: true,
            // status : null,
            // client_id: null,
            // detail_po: []
        },
    }),

    mutations : {
        //set and clear selected board client
        SET_PO_LISTS(state,payload) { 
            state.poLists = payload; 
        },
        CLR_PO_LISTS(state) { 
            state.poLists = [];
        },
        SET_PO_DATA(state,payload) {

        },
    },

    getters : {
        //return all user client data
        poLists : state => { 
            return state.poLists;
        },    
    },

    actions : {
        setPurchaseOrder(data){

        },
        
        createPOFromPR({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrl, payload)
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

        createPO({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}`, payload)
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

        updatePO({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrl, payload)
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

        updateApprovePO({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrl}/${payload}/approve`)
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

        updateCancelPO({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrl}/${payload}/cancel`)
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

        updateProsesPO({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrl}/${payload}/proses`)
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

        deletePO({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrl}/${payload}`)
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

        dataPO({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/data/${payload}`)
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

        listPO({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PO_LISTS',response.data.data); }
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