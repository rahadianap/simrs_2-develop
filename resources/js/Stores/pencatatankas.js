import axios from './httpReq';
let baseUrl = '/praktek/dokter/kas';

export default {
    namespaced: true,

    state: () => ({
        lists : [],
        totalBayar : 0,
        totalTerima : 0,
        
        jnsTransaksi : [
            { value: 'Pengeluaran', text:'PENGELUARAN', iconClass :  'fas fa-arrow-down', isExpense: true },
            { value: 'Pemasukan', text:'PEMASUKAN', iconClass :  'fas fa-arrow-up', isExpense: false },
        ],

        metodeBayar :[
            { value :'TUNAI', text : 'TUNAI', iconClass : 'fas fa-money-bill' },
            { value :'VIRTUAL ACCOUNT', text : 'VIRTUAL ACCOUNT', iconClass : 'fa-university' },
            { value :'QRIS', text : 'QRIS', iconClass : 'fas fa-qrcode' },
            { value :'E-WALLET', text : 'E-WALLET', iconClass : 'fas fa-mobile-alt' },
            { value :'DEBIT CARD', text : 'DEBIT CARD', iconClass : 'fas fa-credit-card' },
            { value :'CREDIT CARD', text : 'CREDIT CARD', iconClass : 'fas fa-credit-card' },
        ],

        filterTable : { 
            per_page : 10,
            page : 1,
            is_aktif : true, 
            keyword : null,
            date_start : null,
            date_end : null,
            periode : "",
            jns_transaksi : "",
            metode_pembayaran : "",
        },

    }),

    mutations : {
        SET_KAS_LISTS(state,payload) { 
            state.lists = payload; 
        },
    },

    getters : {
        kasLists : state => {
            return state.lists.detail;
        },
        totalTerima : state => {
            return state.totalTerima;
        },
        totalBayar : state => {
            return state.totalBayar;
        },
        filterDataKas : state => {
            return state.filterTable;
        },

        jnsTransaksiKas:state => {
            return state.jnsTransaksi;
        },
        metodeBayarKas:state => {
            return state.metodeBayar;
        }
    },

    actions : {        
        listKas({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { 
                        commit('SET_KAS_LISTS',response.data.data); 
                    }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        dataKas({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${payload}`)
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

        updateKas({ commit }, payload ) {
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

        createKas({ commit }, payload ) {
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

        deleteKas({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrl}/delete`,payload)
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

        uploadBukti({commit}, payload){
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/bukti`, payload)
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

        deleteBukti({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrl}/bukti/${payload}`)
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